<?php

namespace Nassau\KunstmaanImportBundle\Hydrator;

use Doctrine\ORM\EntityManagerInterface;
use Nassau\KunstmaanImportBundle\Entity\ImportedEntity;

class EntityMatcher
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ImportedEntity[][]
     */
    private $existing = [];

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param ImportedEntity $entity
     * @return ImportedEntity|null
     */
    public function findExistingEntity(ImportedEntity $entity)
    {
        $id = $entity->getImportId();

        foreach ($this->getExistingEntities(get_class($entity)) as $existing) {
            if ($existing->getImportId() === $id) {
                return $existing;
            }
        }

        return null;
    }

    /**
     * @param string $type
     * @return \Nassau\KunstmaanImportBundle\Entity\ImportedEntity[]
     */
    private function getExistingEntities($type)
    {
        if (false === isset($this->existing[$type])) {
            $this->existing[$type] = $this->em->getRepository($type)->findAll();
        }

        return $this->existing[$type];
    }
}
