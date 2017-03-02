<?php

namespace Nassau\KunstmaanImportBundle\Import\Zip;

use Nassau\KunstmaanImportBundle\Entity\Import;
use Nassau\KunstmaanImportBundle\Entity\ImportItem;
use Nassau\KunstmaanImportBundle\Entity\ImportItemAttribute;
use Nassau\KunstmaanImportBundle\Import\FileImporter;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ZipImporter implements FileImporter
{
    /**
     * @var array
     */
    private $dataFileExtensions;

    /**
     * @var \Closure
     */
    private $fileAttributes;

    /**
     * @var FileImporter
     */
    private $excel;

    /**
     * @var MediaUploader
     */
    private $mediaUploader;

    public function __construct(FileImporter $importer, MediaUploader $mediaUploader, $dataFileExtensions, array $fileAttributes)
    {
        $this->dataFileExtensions = $dataFileExtensions;
        $this->fileAttributes = $fileAttributes;
        $this->excel = $importer;
        $this->mediaUploader = $mediaUploader;
    }

    /**
     * @param Import $import
     * @param \SplFileInfo $file
     * @return Import
     */
    public function import(Import $import, \SplFileInfo $file)
    {
        if ($file instanceof UploadedFile) {
            foreach ($this->dataFileExtensions as $extension) {
                if ($extension === $file->getClientOriginalExtension()) {
                    return $this->excel->import($import, $file);
                }
            }
        }

        $targetDir = $this->extractArchive($file);

        $finder = array_reduce($this->dataFileExtensions, function (Finder $finder, $extension) {
            return $finder->name(sprintf('*.%s', $extension));
        }, (new Finder)->in($targetDir)->files());

        foreach ($finder as $dataFile) {
            $import = $this->excel->import($import, $dataFile);
        }

        foreach ($import->getItems() as $item) {
            foreach ($this->getFileAttributes($item) as $attribute) {
                $fileName = sprintf('%s/%s', $targetDir, $attribute->getValue());

                try {
                    $media = $this->mediaUploader->uploadFileToMedia($fileName, $import->getType());

                    $item->replaceAttributeValue($attribute, $media->getId());

                } catch (FileNotFoundException $e) {
                    $import->addError('nassau.import.error.file_not_found', [
                        "%name%" => basename($fileName),
                        '%attribute%' => $attribute->getName(),
                    ]);

                    $item->removeAttribute($attribute);
                }
            }
        }

        return $import;
    }

    /**
     * @param ImportItem $item
     * @return ImportItemAttribute[]
     */
    private function getFileAttributes(ImportItem $item)
    {
        return $item->getAttributes()->filter(function (ImportItemAttribute $attribute) {
            return in_array($attribute->getName(), $this->fileAttributes) && "" !== $attribute->getValue();
        });
    }

    /**
     * @param \SplFileInfo $file
     * @return string
     */
    private function extractArchive(\SplFileInfo $file)
    {
        $zip = new \ZipArchive();

        $zip->open($file->getPathname());

        $targetDir = tempnam(sys_get_temp_dir(), "zip_importer");
        unlink($targetDir);

        $zip->extractTo($targetDir);
        $zip->close();

        return $targetDir;
    }

}
