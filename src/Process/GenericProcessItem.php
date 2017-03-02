<?php

namespace Nassau\KunstmaanImportBundle\Process;

use Nassau\KunstmaanImportBundle\Entity\ImportedEntity;
use Nassau\KunstmaanImportBundle\Entity\ImportItem;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class GenericProcessItem implements ProcessItemInterface
{

    /**
     * @var ImportedEntity
     */
    private $entity;

    /**
     * @var ImportItem
     */
    private $importItem;

    /**
     * @var ConstraintViolationListInterface
     */
    private $errors;

    public function __construct(ImportedEntity $entity, ImportItem $importItem, ConstraintViolationListInterface $errors)
    {
        $this->errors = $errors;
        $this->entity = $entity;
        $this->importItem = $importItem;
    }

    /**
     * @return ImportedEntity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return ImportItem
     */
    public function getImportItem()
    {
        return $this->importItem;
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getErrors()
    {
        return $this->errors;
    }

}

