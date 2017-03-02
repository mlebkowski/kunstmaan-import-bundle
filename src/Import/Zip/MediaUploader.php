<?php

namespace Nassau\KunstmaanImportBundle\Import\Zip;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Entity\Media;
use Symfony\Component\HttpFoundation\File\File;

class MediaUploader
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Folder[]
     */
    private $folders = [];

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param $fileName
     * @param $folder
     * @return Media
     */
    public function uploadFileToMedia($fileName, $folder)
    {
        $media = (new Media)
            ->setFolder($this->getFolder($folder))
            ->setContent(new File($fileName))
            ->setOriginalFilename(basename($fileName))
            ->setMetadataValue('imported', true);

        $this->em->persist($media);
        $this->em->flush();

        return $media;
    }

    private function getFolder($name)
    {
        $internalName = '_imported_' . $name;

        if (isset($this->folders[$name])) {
            return $this->folders[$name];
        }

        $folderRepository = $this->em->getRepository('KunstmaanMediaBundle:Folder');

        $folder = $folderRepository->findOneBy([
            'internalName' => $internalName
        ]);

        if (null === $folder) {
            $folder = (new Folder)
                ->setRel('image')
                ->setInternalName($internalName)
                ->setName(sprintf('Files from „%s” import', $name));

            array_reduce($folderRepository->getRootNodes(), function (Folder $folder, Folder $root) {
                return $folder->setParent($root);
            }, $folder);

            $this->em->persist($folder);
        }

        return $this->folders[$name] = $folder;
    }
}
