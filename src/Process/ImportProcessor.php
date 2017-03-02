<?php

namespace Nassau\KunstmaanImportBundle\Process;

use Nassau\KunstmaanImportBundle\Entity\Import;
use Nassau\KunstmaanImportBundle\Entity\ImportItem;
use Nassau\KunstmaanImportBundle\Hydrator\EntityHydrator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ImportProcessor
{
    /**
     * @var EntityHydrator
     */
    private $hydrator;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var PostProcessorInterface[]
     */
    private $postProcessors;

    public function __construct(EntityHydrator $hydrator, ValidatorInterface $validator, \Traversable $postProcessors)
    {
        $this->hydrator = $hydrator;
        $this->validator = $validator;
        $this->postProcessors = $postProcessors;
    }

    /**
     * @param ImportItem $item
     * @return ProcessItemInterface
     */
    public function processItem(ImportItem $item)
    {
        $entity = $this->hydrator->hydrate($item);

        $errors = $this->validator->validate($entity);
        $errors->addAll($item->getErrors());

        if (0 === $errors->count()) {
            $item->setEntityId($entity->getImportId());

            foreach ($this->postProcessors as $postProcessor) {
                $postProcessor->postProcess($entity);
            }
        }

        return new GenericProcessItem($entity, $item, $errors);
    }

    /**
     * @param Import $import
     * @return \Generator|ProcessItemInterface[]
     */
    public function getProcessIterator(Import $import)
    {
        foreach ($import->getPendingItems() as $item) {
            if (yield $this->processItem($item)) {
                break;
            }
        }
    }
}
