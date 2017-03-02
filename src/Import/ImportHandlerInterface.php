<?php

namespace Nassau\KunstmaanImportBundle\Import;

use Nassau\KunstmaanImportBundle\Entity\Import;
use Nassau\KunstmaanImportBundle\Process\ProcessItemInterface;
use Symfony\Component\HttpFoundation\File\File;

interface ImportHandlerInterface
{
    /**
     * @param Import $import
     * @param File $file
     * @return \Iterator|\Generator|ProcessItemInterface[]
     * @throws ImportException
     */
    public function handleFile(Import $import, File $file);

    /**
     * @param Import $import
     * @return ProcessItemInterface|null
     */
    public function getNextItem(Import $import);


}
