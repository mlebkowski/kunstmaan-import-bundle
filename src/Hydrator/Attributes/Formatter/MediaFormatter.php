<?php

namespace Nassau\KunstmaanImportBundle\Hydrator\Attributes\Formatter;

use Doctrine\ORM\EntityManagerInterface;

class MediaFormatter implements AttributeFormatter
{

    /**
     * @var EntityManagerInterface
     */
    private $em;
    private $repository;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @param FormattedAttribute $attribute
     * @return FormattedAttribute
     */
    public function format(FormattedAttribute $attribute)
    {
        $repo = $this->getRepository();

        return $attribute->withFormattedValue($repo->find($attribute->getOriginalValue()));
    }

    private function getRepository()
    {
        if (null === $this->repository) {
            $this->repository = $this->em->getRepository('KunstmaanMediaBundle:Media');
        }

        return $this->repository;
    }
}
