<?php

namespace Nassau\KunstmaanImportBundle\Import\Spreadsheet\AttributeMatcher;

class IdentityAttributeMatcher implements AttributeMatcher
{
    /**
     * @var AttributeSettings[]
     */
    private $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = array_map(function ($settings) {
            return new AttributeSettings($settings['name'], $settings['type'], $settings['ignore_empty']);
        }, $attributes);
    }

    public function getAttributeSettings($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }

        throw new UnknownAttributeException;
    }
}
