<?php

namespace Nassau\KunstmaanImportBundle\Entity;

class ImportError
{
    private $id;

    /**
     * @var Import
     */
    private $import;

    /**
     * @var string
     */
    private $error;
    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @param Import $import
     * @param string $error
     * @param array $parameters
     */
    public function __construct(Import $import, $error, array $parameters = [])
    {
        $this->import = $import;
        $this->error = $error;
        $this->parameters = $parameters;
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
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

}
