<?php

namespace Nassau\KunstmaanImportBundle\Import\Spreadsheet\RowIterator;

use Box\Spout\Reader\XLSX\Sheet;

class InversedRowIteratorFactory implements RowIteratorFactory
{

    /**
     * @param Sheet $sheet
     * @return \Iterator
     */
    public function getRowIterator(Sheet $sheet)
    {
        $matrix = [];

        foreach ($sheet->getRowIterator() as $row) {
            $matrix[] = $row;
        }

        list ($columns) = array_pad($matrix, 1, []);

        foreach (array_keys($columns) as $idx) {
            yield $idx => array_map(function ($row) use ($idx) {
                return $row[$idx];
            }, $matrix);
        }
    }
}
