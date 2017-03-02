<?php

namespace Nassau\KunstmaanImportBundle\Hydrator\Attributes\Formatter;

interface AttributeFormatter
{
    /**
     * @param FormattedAttribute $attribute
     * @return FormattedAttribute
     * @throws FormattingException
     */
    public function format(FormattedAttribute $attribute);
}
