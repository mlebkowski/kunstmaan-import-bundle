<?php

namespace Nassau\KunstmaanImportBundle\Import;

use Nassau\KunstmaanImportBundle\Entity\Import;
use Symfony\Component\HttpFoundation\File\File;

interface FileImporter
{

    /**
     * @param Import $import
     * @param \SplFileInfo $file
     * @return Import
     */
    public function import(Import $import, \SplFileInfo $file);
}
