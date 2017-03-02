<?php

namespace Nassau\KunstmaanImportBundle\Hydrator\Attributes\Formatter;

class BooleanFormatter implements AttributeFormatter
{
    private $falsyValues;

    /**
     * @param array $falsyValues
     */
    public function __construct(array $falsyValues = ["", "false", "0", "no"])
    {
        $this->falsyValues = $falsyValues;
    }


    /**
     * @param FormattedAttribute $attribute
     * @return FormattedAttribute
     */
    public function format(FormattedAttribute $attribute)
    {
        $value = false === in_array(strtolower($attribute->getOriginalValue()), $this->falsyValues, true);

        return $attribute->withFormattedValue($value);
    }
}
