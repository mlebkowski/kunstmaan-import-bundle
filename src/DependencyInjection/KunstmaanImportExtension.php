<?php

namespace Nassau\KunstmaanImportBundle\DependencyInjection;

use Nassau\KunstmaanImportBundle\Hydrator\EntityHydrator;
use Nassau\KunstmaanImportBundle\Hydrator\Factory\EntityFactory;
use Nassau\KunstmaanImportBundle\Import\GenericImportHandler;
use Nassau\KunstmaanImportBundle\Import\Spreadsheet\AttributeMatcher\IdentityAttributeMatcher;
use Nassau\KunstmaanImportBundle\Import\Spreadsheet\RowIterator\StrategyRowIteratorFactory;
use Nassau\KunstmaanImportBundle\Import\Spreadsheet\SpreadsheetImporter;
use Nassau\KunstmaanImportBundle\Import\Zip\ZipImporter;
use Nassau\KunstmaanImportBundle\Process\ConfigurableIterator;
use Nassau\KunstmaanImportBundle\Process\ImportProcessor;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class KunstmaanImportExtension extends Extension
{

    /**
     * Loads a specific configuration.
     *
     * @param array            $configs   An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configs = (new Processor)->processConfiguration(new Configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');


        foreach ($configs as $name => $settings) {
            $this->loadImportType($container, $name, $settings);
        }

    }

    private function loadImportType(ContainerBuilder $container, $name, array $settings)
    {
        $handlerId = 'nassau.kunstmaan_import.handler.' . $name;

        if ($settings['handler_id']) {
            $container->setAlias($handlerId, $settings['handler_id']);

            $container->getDefinition('nassau.kunstmaan_import.strategy_handler.collection')
                ->addMethodCall(['offsetSet', [$name, new Reference($handlerId)]]);

            return;
        }


        $container->setDefinition($handlerId, $this->createHandlerDefinition($container, $handlerId, $settings))
            ->setPublic(false)
            ->addTag('kunstmaan_import.handler', ['type' => $name]);
    }

    private function createHandlerDefinition(ContainerBuilder $containerBuilder, $handlerId, array $settings)
    {
        $fileHandlerId = $this->configureFileHandler($containerBuilder, $handlerId, $settings);
        $processorId = $this->configureProcessor($containerBuilder, $handlerId, $settings);

        return new Definition(GenericImportHandler::class, [
            new Reference($fileHandlerId),
            new Reference($processorId)
        ]);
    }

    /**
     * @param ContainerBuilder $containerBuilder
     * @param $handlerId
     * @param array $settings
     * @return string
     */
    private function configureFileHandler(ContainerBuilder $containerBuilder, $handlerId, array $settings)
    {
        $fileHandlerId = sprintf('%s.xsl', $handlerId);

        $rowIteratorId = sprintf('%s.row_iterator', $fileHandlerId);
        $containerBuilder->setDefinition($rowIteratorId, new Definition(
            StrategyRowIteratorFactory::class, [
                new Reference('nassau.kunstmaan_import.import.spreadsheet.row_iterator.collection'),
                $settings['excel']['format']
            ]
        ))->setPublic(false);

        $attributeMatcherId = sprintf('%s.attribute_matcher', $fileHandlerId);
        $defaultAttributes = $settings['default_attributes'];

        $containerBuilder->setDefinition($attributeMatcherId, new Definition(IdentityAttributeMatcher::class, [
            // [name => [name => label, …]] => [label => [name, …]
            array_combine(
                array_map(function ($attribute) {
                    return $attribute['label'];
                }, $settings['attributes']),
                array_map(function ($name, $attribute) use ($defaultAttributes) {
                    $attribute += $defaultAttributes;
                    return [
                        'name' => $name,
                        'type' => $attribute['type'],
                        'ignore_empty' => $attribute['ignore_empty'],
                    ];
                }, array_keys($settings['attributes']), $settings['attributes'])
            )
        ]))->setPublic(false);

        $containerBuilder->setDefinition($fileHandlerId, new Definition(SpreadsheetImporter::class, [
            new Reference('nassau.kunstmaan_import.import.excel_reader'),
            new Reference($attributeMatcherId),
            new Reference($rowIteratorId),
        ]))->setPublic(false);

        if ($settings['zip']['enabled']) {
            $zipFileHandlerId = sprintf('%s.zip', $handlerId);

            $containerBuilder->setDefinition($zipFileHandlerId, new Definition(ZipImporter::class, [
                new Reference($fileHandlerId),
                new Reference('nassau.kunstmaan_import.import.zip.media_uploader'),
                $settings['zip']['data_file_extension'],
                $settings['zip']['file_attributes']
            ]))->setPublic(false);

            return $zipFileHandlerId;
        }

        return $fileHandlerId;
    }

    private function configureProcessor(ContainerBuilder $containerBuilder, $handlerId, array $settings)
    {
        $processorId = sprintf('%s.processor', $handlerId);

        $entityHydratorId = $this->configureEntityHydrator($containerBuilder, $processorId, $settings);

        $postProcessorsId = $this->configurePostProcessors($containerBuilder, $processorId, $settings);

        $containerBuilder->setDefinition($processorId, new Definition(ImportProcessor::class, [
            new Reference($entityHydratorId),
            new Reference('validator'),
            new Reference($postProcessorsId), // configurable post processor
        ]))->setPublic(false);

        return $processorId;
    }

    /**
     * @param ContainerBuilder $containerBuilder
     * @param string $processorId
     * @param array $settings
     * @return string
     */
    private function configureEntityHydrator(ContainerBuilder $containerBuilder, $processorId, array $settings)
    {
        $entityHydratorId = sprintf('%s.entity_hydrator', $processorId);

        $entityFactoryId = sprintf('%s.entity_factory', $entityHydratorId);

        $containerBuilder->setDefinition($entityFactoryId, new Definition(EntityFactory::class, [
            $settings['entity'],
            new Reference('doctrine.orm.entity_manager'),
            new Reference('nassau.kunstmaan_import.hydrator.attributes_formatter'),
            new Reference('nassau.kunstmaan_import.hydrator.attributes_writer'),
        ]))->setPublic(false);

        $containerBuilder->setDefinition($entityHydratorId, new Definition(EntityHydrator::class, [
            new Reference($entityFactoryId),
            new Reference('nassau.kunstmaan_import.hydrator.entity_matcher'),
        ]))->setPublic(false);

        return $entityHydratorId;
    }

    /**
     * @param ContainerBuilder $containerBuilder
     * @param string $processorId
     * @param array $settings
     * @return string
     */
    private function configurePostProcessors(ContainerBuilder $containerBuilder, $processorId, array $settings)
    {
        $postProcessorsId = sprintf('%s.post_processors', $processorId);

        $containerBuilder->setDefinition($postProcessorsId, new Definition(ConfigurableIterator::class, [
            new Reference('nassau.kunstmaan_import.post_processor.collection'),
            $settings['post_processors']
        ]))->setPublic(false);

        return $postProcessorsId;
    }

}
