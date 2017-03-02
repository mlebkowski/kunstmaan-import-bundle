<?php

namespace Nassau\KunstmaanImportBundle\Hydrator\Attributes\Writer;

use Nassau\KunstmaanImportBundle\Entity\ImportedEntity;
use Nassau\KunstmaanImportBundle\Hydrator\Attributes\Formatter\FormattedAttribute;

class ChainedWriter implements AttributeWriter
{

    /**
     * @var AttributeWriter[]
     */
    private $handlers;

    /**
     * @param AttributeWriter[]|\Traversable $handlers
     */
    public function __construct(\Traversable $handlers)
    {
        $this->handlers = $handlers;
    }


    public function write(ImportedEntity $entity, FormattedAttribute $attribute)
    {
        foreach ($this->handlers as $handler) {
            if ($handler->write($entity, $attribute)) {
                return true;
            }
        }

        return false;
    }

}
