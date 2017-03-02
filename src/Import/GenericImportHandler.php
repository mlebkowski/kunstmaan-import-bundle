<?php

namespace Nassau\KunstmaanImportBundle\Import;

use Nassau\KunstmaanImportBundle\Entity\Import;
use Nassau\KunstmaanImportBundle\Process\ProcessItemInterface;
use Nassau\KunstmaanImportBundle\Process\ImportProcessor;
use Symfony\Component\HttpFoundation\File\File;

class GenericImportHandler implements ImportHandlerInterface
{
    /**
     * @var FileImporter
     */
    private $fileImporter;

    /**
     * @var ImportProcessor
     */
    private $processor;

    public function __construct(FileImporter $fileImporter, ImportProcessor $processor)
    {
        $this->fileImporter = $fileImporter;
        $this->processor = $processor;
    }


    /**
     * @param Import $import
     * @param File $file
     * @return \Iterator|\Generator|ProcessItemInterface[]
     * @throws ImportException
     */
    public function handleFile(Import $import, File $file)
    {
        $import = $this->fileImporter->import($import, $file);

        return $this->processor->getProcessIterator($import);
    }


    /**
     * @param Import $import
     * @return ProcessItemInterface|null
     */
    public function getNextItem(Import $import)
    {
        foreach ($this->processor->getProcessIterator($import) as $item) {
            return $item;
        }

        return null;
    }
}
