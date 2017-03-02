<?php

namespace Nassau\KunstmaanImportBundle\Hydrator\Attributes\Formatter;

class FormattedAttribute
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $originalValue;

    /**
     * @var mixed
     */
    private $formattedValue;

    /**
     * @var string
     */
    private $type;

    /**
     * @param string $name
     * @param string $originalValue
     * @param string $type
     */
    public function __construct($name, $originalValue, $type)
    {
        $this->name = $name;
        $this->originalValue = $originalValue;
        $this->type = $type;
        $this->formattedValue = $originalValue;
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
    public function getType()
    {
        return (string)$this->type;
    }

    /**
     * @return string
     */
    public function getOriginalValue()
    {
        return $this->originalValue;
    }

    /**
     * @param mixed $value
     * @param string $type
     * @return FormattedAttribute
     */
    public function withFormattedValue($value, $type = null)
    {
        $result = clone $this;
        $result->originalValue = $result->formattedValue;
        $result->formattedValue = $value;
        $result->type = $type;

        return $result;
    }

    /**
     * @return mixed
     */
    public function getFormattedValue()
    {
        return $this->formattedValue;
    }

}

