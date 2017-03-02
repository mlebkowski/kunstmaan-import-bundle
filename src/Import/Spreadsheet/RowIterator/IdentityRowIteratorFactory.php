<?php

namespace Nassau\KunstmaanImportBundle\Import\Spreadsheet\RowIterator;

use Box\Spout\Reader\XLSX\Sheet;

class IdentityRowIteratorFactory implements RowIteratorFactory
{

    /**
     * @param Sheet $sheet
     * @return \Iterator
     */
    public function getRowIterator(Sheet $sheet)
    {
        return $sheet->getRowIterator();
    }
}
