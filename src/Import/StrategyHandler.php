<?php

namespace Nassau\KunstmaanImportBundle\Import;

use Nassau\KunstmaanImportBundle\Entity\Import;
use Nassau\KunstmaanImportBundle\Process\ProcessItemInterface;
use Symfony\Component\HttpFoundation\File\File;

class StrategyHandler implements ImportHandlerInterface
{

    /**
     * @var ImportHandlerInterface[]
     */
    private $handlers;

    /**
     * @param ImportHandlerInterface[]|\Traversable $handlers
     */
    public function __construct(\Traversable $handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * @param Import $import
     * @param File $file
     * @return ProcessItemInterface[]|\Generator
     * @throws ImportException
     */
    public function handleFile(Import $import, File $file)
    {
        $handler = $this->getHandler($import);

        return $handler->handleFile($import, $file);
    }

    /**
     * @param Import $import
     * @return ProcessItemInterface|null
     */
    public function getNextItem(Import $import)
    {
        return $this->getHandler($import)->getNextItem($import);
    }

    /**
     * @param Import $import
     * @return ImportHandlerInterface
     * @throws ImportException
     */
    private function getHandler(Import $import)
    {
        if (false === isset($this->handlers[$import->getType()])) {
            throw new ImportException(sprintf('There is no registered handler for %s import', $import->getType()));
        }

        return $this->handlers[$import->getType()];
    }
}
