<?php

namespace Nassau\KunstmaanImportBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Import
{
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var ImportItem[]|Collection
     */
    private $items;

    /**
     * @var ImportError[]|Collection
     */
    private $errors;

    /**
     * @var \DateTime
     */
    private $createdAt;

    public function __construct($type)
    {
        $this->type = $type;
        $this->items = new ArrayCollection();
        $this->errors = new ArrayCollection();
        $this->createdAt = new \DateTime;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return Collection|ImportItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    public function addItem(ImportItem $item)
    {
        $this->items->add($item);

        return $this;
    }

    /**
     * @return Collection|ImportItem[]
     */
    public function getPendingItems()
    {
        return $this->items->filter(function (ImportItem $item) {
            return null === $item->getEntityId();
        });
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function addError($error, array $parameters = [])
    {
        $this->errors->add(new ImportError($this, $error, $parameters));

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
