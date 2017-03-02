<?php

namespace Nassau\KunstmaanImportBundle\Hydrator\Attributes\Formatter;

class StrategyFormatter implements AttributeFormatter
{
    /**
     * @var AttributeFormatter[]
     */
    private $formatters;

    public function __construct(\Traversable $formatters)
    {
        $this->formatters = $formatters;
    }

    /**
     * @param FormattedAttribute $attribute
     * @return FormattedAttribute
     * @throws FormattingException
     */
    public function format(FormattedAttribute $attribute)
    {
        $type = $attribute->getType();

        if ("" === $attribute->getType()) {
            return $attribute;
        }

        if (false === isset($this->formatters[$type])) {
            throw new FormattingException(sprintf('There is no %s formatter configured', $type));
        }

        return $this->formatters[$type]->format($attribute);
    }
}
