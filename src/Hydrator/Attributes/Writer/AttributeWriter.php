<?php

namespace Nassau\KunstmaanImportBundle\Hydrator\Attributes\Writer;

use Nassau\KunstmaanImportBundle\Entity\ImportedEntity;
use Nassau\KunstmaanImportBundle\Hydrator\Attributes\Formatter\FormattedAttribute;

interface AttributeWriter
{
    /**
     * @param ImportedEntity $entity
     * @param FormattedAttribute $attribute
     * @return true if attribute has been handled
     */
    public function write(ImportedEntity $entity, FormattedAttribute $attribute);
}
