<?php

namespace Nassau\KunstmaanImportBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\ConstraintViolationList;

class ImportItem
{
    private $id;

    /**
     * @var Import
     */
    private $import;

    private $entityId;

    /**
     * @var ImportedEntity
     */
    private $importedEntity;

    /**
     * @var ConstraintViolationList
     */
    private $errors;

    /**
     * @var Collection|ImportItemAttribute[]
     */
    private $attributes;

    public function __construct(Import $import)
    {
        $this->attributes = new ArrayCollection();
        $this->import = $import->addItem($this);
    }

    public function getErrors()
    {
        if (null === $this->errors) {
            $this->errors = new ConstraintViolationList();
        }

        return $this->errors;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return Import
     */
    public function getImport()
    {
        return $this->import;
    }

    /**
     * @return string|null
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * @param string|null $entityId
     *
     * @return $this
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId ?: null;

        return $this;
    }

    /**
     * @return ImportItemAttribute[]|Collection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }


    public function addAttribute($name, $value, $type)
    {
        $this->attributes->add(new ImportItemAttribute($this, $name, $value, $type));

        return $this;
    }

    public function removeAttribute(ImportItemAttribute $attribute)
    {
        $this->attributes->removeElement($attribute);

        return $this;
    }

    public function replaceAttributeValue(ImportItemAttribute $attribute, $value)
    {
        $this->removeAttribute($attribute);

        $this->addAttribute($attribute->getName(), $value, $attribute->getType());
    }

    /**
     * @return ImportedEntity|null
     */
    public function getImportedEntity()
    {
        return $this->importedEntity;
    }

    /**
     * @param ImportedEntity $importedEntity
     *
     * @return $this
     */
    public function setImportedEntity($importedEntity)
    {
        $this->importedEntity = $importedEntity;

        return $this;
    }


}
