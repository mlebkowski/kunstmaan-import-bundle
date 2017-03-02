<?php

namespace Nassau\KunstmaanImportBundle\Import\Spreadsheet\AttributeMatcher;

class AttributeSettings
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var bool
     */
    private $ignoreEmpty;

    /**
     * @param string $name
     * @param string $type
     * @param bool $ignoreEmpty
     */
    public function __construct($name, $type, $ignoreEmpty)
    {
        $this->name = $name;
        $this->type = $type;
        $this->ignoreEmpty = (bool)$ignoreEmpty;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    public function getType()
    {
        return $this->type;
    }

    /**
     * @return boolean
     */
    public function isIgnoreEmpty()
    {
        return $this->ignoreEmpty;
    }




}
