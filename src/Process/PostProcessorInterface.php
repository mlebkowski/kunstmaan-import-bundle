<?php

namespace Nassau\KunstmaanImportBundle\Process;

use Nassau\KunstmaanImportBundle\Entity\ImportedEntity;

interface PostProcessorInterface
{
    public function postProcess(ImportedEntity $entity);
}
