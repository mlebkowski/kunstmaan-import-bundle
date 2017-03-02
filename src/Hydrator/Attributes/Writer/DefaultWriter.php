<?php

namespace Nassau\KunstmaanImportBundle\Hydrator\Attributes\Writer;

use Nassau\KunstmaanImportBundle\Entity\ImportedEntity;
use Nassau\KunstmaanImportBundle\Hydrator\Attributes\Formatter\FormattedAttribute;
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class DefaultWriter implements AttributeWriter
{

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
    }

    public function write(ImportedEntity $entity, FormattedAttribute $attribute)
    {
        if (false === $this->propertyAccessor->isWritable($entity, $attribute->getName())) {
            return false;
        }

        try {
            $this->propertyAccessor->setValue($entity, $attribute->getName(), $attribute->getFormattedValue());
        } catch (InvalidArgumentException $e) {
            throw new WritingException($e->getMessage(), $e->getCode(), $e);
        }

        return true;
    }
}
