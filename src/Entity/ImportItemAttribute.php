<?php

namespace Nassau\KunstmaanImportBundle\Entity;

class ImportItemAttribute
{
    private $id;

    /**
     * @var ImportItem
     */
    private $item;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $type;

    public function __construct(ImportItem $item, $name, $value, $type)
    {
        $this->item = $item;
        $this->name = $name;
        $this->value = $value;
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ImportItem
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

}
