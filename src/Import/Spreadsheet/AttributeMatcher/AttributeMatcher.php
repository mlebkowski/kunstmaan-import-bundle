<?php

namespace Nassau\KunstmaanImportBundle\Import\Spreadsheet\AttributeMatcher;

interface AttributeMatcher
{
    /**
     * @param string $label column name
     * @return AttributeSettings
     * @throws UnknownAttributeException
     */
    public function getAttributeSettings($label);
}
