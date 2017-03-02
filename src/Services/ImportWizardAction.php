<?php

namespace Nassau\KunstmaanImportBundle\Services;

use Nassau\KunstmaanImportBundle\AdminList\ImportWizardAdminListConfiguratorInterface;
use Nassau\KunstmaanImportBundle\Entity\Import;
use Nassau\KunstmaanImportBundle\Form\ImportFileType;
use Nassau\KunstmaanImportBundle\Form\ImportItemAdminType;
use Nassau\KunstmaanImportBundle\Import\ImportHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;

class ImportWizardAction
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ImportHandlerInterface
     */
    private $importHandler;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(EntityManagerInterface $em, ImportHandlerInterface $importHandler, FormFactoryInterface $formFactory)
    {
        $this->em = $em;
        $this->importHandler = $importHandler;
        $this->formFactory = $formFactory;
    }


    public function upload(ImportWizardAdminListConfiguratorInterface $configurator, Request $request)
    {
        $form = $this->formFactory->create(ImportFileType::class);

        $form->handleRequest($request);

        if ($form->isValid() && $form->getData()) {

            $file = $form->get('file')->getData();

            $import = new Import($configurator->getImportType());

            $result = $this->importHandler->handleFile($import, $file);

            $hasErrors = $import->getErrors()->count() > 0;
            foreach ($result as $item) {
                if (0 !== $item->getErrors()->count()) {
                    $hasErrors = true;
                    continue;
                }

                $this->em->persist($item->getEntity());
            }

            $this->em->persist($import);
            $this->em->flush();

            if ($hasErrors) {
                return $import;
            }

            return null;
        }

        return ['form' => $form->createView(), 'adminlistconfigurator' => $configurator];
    }

    public function import(Request $request, Import $import, ImportWizardAdminListConfiguratorInterface $configurator)
    {
        $all = $import->getItems()->count();
        $pendingItems = $import->getPendingItems();
        $pendingCount = $pendingItems->count();

        $form = null;

        $next = $this->importHandler->getNextItem($import);

        if ($next) {

            $item = $next->getImportItem();

            // feed the form imported entity data:
            $item->setImportedEntity($next->getEntity());

            $form = $this->formFactory->create(ImportItemAdminType::class, $item, [
                'imported_entity_type' => $configurator->getAdminType($next->getEntity()),
                'next_id' => $item->getId(),
            ]);

            $form->handleRequest($request);

            // we submited the wrong ID, it’s a conflict. Just redirect to the import again:
            if ($form->has('id') && $form->get('id')->getErrors()->count()) {
                return null;
            }

            $skip = $form->has('skip') ? $form->get('skip') : null;
            if ($skip instanceof SubmitButton && $skip->isClicked()) {
                $this->em->remove($item);
                $this->em->flush();

                return 'nassau.import.flash.skipped';
            }

            if ($form->isValid()) {

                $this->em->persist($item->getImportedEntity());
                $this->em->persist($item->setEntityId($item->getImportedEntity()->getImportId()));
                $this->em->flush();

                return 'nassau.import.flash.saved';
            }
        }

        $percentage = $all ? floor(($all - $pendingCount) / $all * 100): 100;

        return [
            'next' => $next,
            'import' => $import,
            'percentage' => $percentage,
            'all' => $all,
            'saved' => $all - $pendingCount,
            'form' => $form ? $form->createView() : null,
        ];
    }

}
