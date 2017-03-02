<?php

namespace Nassau\KunstmaanImportBundle\Import\Spreadsheet;

use Box\Spout\Reader\XLSX\Reader;
use Box\Spout\Reader\XLSX\Sheet;
use Nassau\KunstmaanImportBundle\Entity\Import;
use Nassau\KunstmaanImportBundle\Entity\ImportItem;
use Nassau\KunstmaanImportBundle\Import\FileImporter;
use Nassau\KunstmaanImportBundle\Import\Spreadsheet\AttributeMatcher\AttributeSettings;
use Nassau\KunstmaanImportBundle\Import\Spreadsheet\AttributeMatcher\UnknownAttributeException;
use Nassau\KunstmaanImportBundle\Import\Spreadsheet\RowIterator\IdentityRowIteratorFactory;
use Nassau\KunstmaanImportBundle\Import\Spreadsheet\RowIterator\RowIteratorFactory;
use Nassau\KunstmaanImportBundle\Import\Spreadsheet\AttributeMatcher\AttributeMatcher;

class SpreadsheetImporter implements FileImporter
{
    const UNRECOGNIZED_ATTRIBUTE = 'unrecognized_attribute';

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var AttributeMatcher
     */
    private $matcher;

    /**
     * @var RowIteratorFactory
     */
    private $rowIteratorFactory;

    public function __construct(Reader $reader, AttributeMatcher $matcher, RowIteratorFactory $rowIteratorFactory = null)
    {
        $this->reader = $reader;
        $this->matcher = $matcher;
        $this->rowIteratorFactory = $rowIteratorFactory ?: new IdentityRowIteratorFactory();
    }

    /**
     * @param Import $import
     * @param \SplFileInfo $file
     * @return Import
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Reader\Exception\ReaderNotOpenedException
     */
    public function import(Import $import, \SplFileInfo $file)
    {
        $this->reader->open($file->getPathname());

        foreach ($this->reader->getSheetIterator() as $sheet) {
            $this->parseSheet($sheet, $import);
        }

        return $import;
    }

    private function parseSheet(Sheet $sheet, Import $import)
    {
        $settings = null;

        foreach ($this->rowIteratorFactory->getRowIterator($sheet) as $idx => $row) {
            // ignore empty rows
            if (0 === sizeof(array_filter($row))) {
                continue;
            }

            // first non-empty row is treated as attributes
            if (null === $settings) {
                $settings = $this->calculateAttributeNames($sheet, $import, $row);

                continue;
            }

            $values = array_map(function ($value, AttributeSettings $attribute = null) {
                if (null === $attribute) {
                    return null;
                }

                if ($attribute->isIgnoreEmpty() && "" === $value) {
                    return null;
                }

                return [$attribute->getName(), $value, $attribute->getType()];
            }, $row, $settings);

            $values = array_filter($values);

            array_reduce($values, function (ImportItem $item, $attribute) {
                return $item->addAttribute(...$attribute);
            }, new ImportItem($import));
        }

        return $import;
    }

    /**
     * @param Sheet $sheet
     * @param Import $import
     * @param array $row
     * @return AttributeSettings[]
     */
    private function calculateAttributeNames(Sheet $sheet, Import $import, array $row)
    {
        $result = [];

        foreach ($row as $columnIdx => $label) {
            try {
                $result[] = $this->matcher->getAttributeSettings($label);
            } catch (UnknownAttributeException $e) {
                $import->addError(self::UNRECOGNIZED_ATTRIBUTE, [
                    '%label%' => $label,
                    '%idx%' => $columnIdx + 1,
                    '%sheet%' => $sheet->getName()
                ]);

                $result[] = null;
            }
        }

        return $result;
    }

}
