<?php

namespace Nassau\KunstmaanImportBundle\Hydrator;

use Nassau\KunstmaanImportBundle\Entity\ImportedEntity;
use Nassau\KunstmaanImportBundle\Entity\ImportItem;
use Nassau\KunstmaanImportBundle\Hydrator\Factory\FactoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class EntityHydrator implements HydratorInterface
{

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var EntityMatcher
     */
    private $matcher;

    /**
     * @var PropertyAccessorInterface
     */
    private $accessor;

    public function __construct(FactoryInterface $factory, EntityMatcher $matcher, PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->factory = $factory;
        $this->matcher = $matcher;
        $this->accessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
    }

    /**
     * @param ImportItem $importItem
     * @return ImportedEntity
     */
    public function hydrate(ImportItem $importItem)
    {
        $result = $this->factory->createEntity($importItem);

        $existing = $this->matcher->findExistingEntity($result);

        if ($existing) {
            foreach ($importItem->getAttributes() as $attribute) {
                $property = $attribute->getName();
                $this->accessor->setValue($existing, $property, $this->accessor->getValue($result, $property));
            }

            $result = $existing;
        }

        return $result;
    }


}
