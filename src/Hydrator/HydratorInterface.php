<?php

namespace Nassau\KunstmaanImportBundle\Hydrator;

use Nassau\KunstmaanImportBundle\Entity\ImportedEntity;
use Nassau\KunstmaanImportBundle\Entity\ImportItem;

interface HydratorInterface
{
    /**
     * @param ImportItem $importItem
     * @return ImportedEntity
     */
    public function hydrate(ImportItem $importItem);
}
