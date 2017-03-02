<?php

namespace Nassau\KunstmaanImportBundle\Hydrator\Factory;

use Nassau\KunstmaanImportBundle\Entity\ImportedEntity;
use Nassau\KunstmaanImportBundle\Entity\ImportItem;

interface FactoryInterface
{

    /**
     * @param ImportItem $item
     * @return ImportedEntity
     */
    public function createEntity(ImportItem $item);
}
