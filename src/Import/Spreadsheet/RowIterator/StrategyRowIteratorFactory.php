<?php

namespace Nassau\KunstmaanImportBundle\Import\Spreadsheet\RowIterator;

use Box\Spout\Reader\XLSX\Sheet;

class StrategyRowIteratorFactory implements RowIteratorFactory
{

    /**
     * @var RowIteratorFactory
     */
    private $factory;

    /**
     * @param \Traversable|RowIteratorFactory[] $factories
     * @param string $strategy
     */
    public function __construct(\Traversable $factories, $strategy)
    {
        if (false === isset($factories[$strategy])) {
            throw new \RuntimeException(sprintf('The RowIteratorFactory by the name %s is not configured', $strategy));
        }

        $this->factory = $factories[$strategy];
    }


    /**
     * @param Sheet $sheet
     * @return \Iterator
     */
    public function getRowIterator(Sheet $sheet)
    {
        return $this->factory->getRowIterator($sheet);
    }
}
