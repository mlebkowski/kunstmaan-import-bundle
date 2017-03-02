<?php

namespace Nassau\KunstmaanImportBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;

interface ImportWizardAdminListConfiguratorInterface extends AdminListConfiguratorInterface
{
    public function getImportType();
}
