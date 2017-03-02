<?php

namespace Nassau\KunstmaanImportBundle\Hydrator\Attributes\Writer;

use Nassau\KunstmaanImportBundle\Entity\ImportedEntity;
use Nassau\KunstmaanImportBundle\Hydrator\Attributes\Formatter\FormattedAttribute;

class IgnoredWriter implements AttributeWriter
{
    private $ignored;

    public function __construct(array $ignored)
    {
        $this->ignored = $ignored;
    }

    public function write(ImportedEntity $entity, FormattedAttribute $attribute)
    {
        return in_array($attribute->getName(), $this->ignored);
    }
}
