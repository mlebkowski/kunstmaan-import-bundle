<?php

namespace Nassau\KunstmaanImportBundle\Process;

use Nassau\KunstmaanImportBundle\Entity\ImportedEntity;
use Nassau\KunstmaanImportBundle\Entity\ImportItem;
use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ProcessItemInterface
{
    /**
     * @return ImportedEntity
     */
    public function getEntity();

    /**
     * @return ImportItem
     */
    public function getImportItem();

    /**
     * @return ConstraintViolationListInterface
     */
    public function getErrors();
}
