<?php

namespace Nassau\KunstmaanImportBundle\Hydrator\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Nassau\KunstmaanImportBundle\Entity\ImportedEntity;
use Nassau\KunstmaanImportBundle\Entity\ImportItem;
use Nassau\KunstmaanImportBundle\Entity\ImportItemAttribute;
use Nassau\KunstmaanImportBundle\Hydrator\Attributes\Formatter\AttributeFormatter;
use Nassau\KunstmaanImportBundle\Hydrator\Attributes\Formatter\FormattedAttribute;
use Nassau\KunstmaanImportBundle\Hydrator\Attributes\Formatter\FormattingException;
use Nassau\KunstmaanImportBundle\Hydrator\Attributes\Writer\AttributeWriter;
use Nassau\KunstmaanImportBundle\Hydrator\Attributes\Writer\WritingException;
use Symfony\Component\Validator\ConstraintViolation;

class EntityFactory implements FactoryInterface
{

    /**
     * @var string
     */
    private $entityName;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var AttributeFormatter
     */
    private $formatter;

    /**
     * @var AttributeWriter
     */
    private $writer;

    public function __construct($entityName, EntityManagerInterface $em, AttributeFormatter $formatter, AttributeWriter $writer)
    {
        $this->entityName = $entityName;
        $this->em = $em;
        $this->formatter = $formatter;
        $this->writer = $writer;
    }

    /**
     * @param ImportItem $item
     * @return ImportedEntity
     */
    public function createEntity(ImportItem $item)
    {
        $result = $this->em->getClassMetadata($this->entityName)->newInstance();

        if (false === $result instanceof ImportedEntity) {
            throw new \RuntimeException(sprintf(
                'The imported entity needs to implement %s, instance of %s given',
                ImportedEntity::class,
                get_class($result)
            ));
        }

        $addError = function ($error, $attribute, $value, array $parameters) use ($item) {
            $item->getErrors()->add(new ConstraintViolation(
                $error,
                'nassau.import.error.' . $error,
                $parameters,
                $item, /* root */
                $attribute, /* property path */
                $value
            ));
        };

        return array_reduce($item->getAttributes()->toArray(), function (ImportedEntity $entity, ImportItemAttribute $attribute) use ($addError) {

            $attribute = new FormattedAttribute($attribute->getName(), $attribute->getValue(), $attribute->getType());

            try {
                $attribute = $this->formatter->format($attribute);

                $this->writer->write($entity, $attribute);
            } catch (FormattingException $e) {
                $addError($e::FORMATTING_ERROR, $attribute->getName(), $attribute->getOriginalValue(), [
                    '%id%' => $entity->getImportId(),
                    '%attribute%' => $attribute->getName(),
                    '%value%' => $attribute->getOriginalValue(),
                    '%error%' => $e->getMessage(),
                ]);
            } catch (WritingException $e) {
                $addError($e::WRITING_ERROR, $attribute->getName(), $attribute->getOriginalValue(), [
                    '%id%' => $entity->getImportId(),
                    '%attribute%' => $attribute->getName(),
                    '%value%' => $attribute->getOriginalValue(),
                    '%error%' => $e->getMessage(),
                ]);
            }

            return $entity;
        }, $result);

    }

}
