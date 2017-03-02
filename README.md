# Kunstmaan Import Bundle

Allows easy and flexible importing of Excel files on top of Kunstmaan Bundles CMS.

## Installation

```
composer require nassau/kunstmaan-import-bundle
```

Run (generate) migrations to update your database schema.

## Configuration

Example configuration:

```yml
kunstmaan_import:
  foobar:
    entity: AcmeBundle:Foobar
    excel:
      format: "rows" # or "columns"
    
    # you may send a zip with an Excel file and some other files
    # the files referenced from columns specified in `file_attributes` will be
    # processed and uploaded to the Media module
    zip:
      enabled: true
      data_file_extension: ['xls', 'xlsx']
      file_attributes:
        - packshot

    # use your custom handler implementing `ImportHandlerInterface`
    handler_id: ~
    
    # after an entity is imported it may be postprocessed by given services
    # to create a postprocessor register a class implementing `PostProcessorInterface` in the container
    # and tag it with "kunstmaan_import.post_processor" name, and add an `alias` attribute to this tag
    # you may then list those aliases here
    post_processors: []

    # each attribute will have those defaults:
    default_attributes:
      # don’t attempt to import cells with empty value
      ignore_empty: true
    attributes:
      # the key is a field on the entity (or a setter/getter pair)
      id:
        # label is matched to the first row/column for each imported item
        # i.e. search for this header value in Excel file
        label: External ID
      name:
        label: Foobar name
      photo:
        label: Photo
        # either provide media id, or filename (when uploading a zip file)
        # the result will be Media instance (or null if not found)
        type: media
      active:
        label: Active
        # understand human readable values like „false” or „No”:
        type: boolean
```

## Setup

Implement `ImportWizardAdminListConfiguratorInterface` on your Entity’s AdminListConfigurator. The `getImportType` method
needs to return a type specified in the configuration. Your entity managed by this admin list needs to implement the `ImportedEntity` interface.

For example:

```php
class FoobarAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator implements ImportWizardAdminListConfiguratorInterface
{
    /**
     * Add a button to the admin list pointing to the import module
     */
    public function buildListActions()
    {
        $this->addListAction(new SimpleListAction([
            'path' => 'acmebundle_admin_foobar_import_upload',
            'params' => [],
        ], 'Upload Excel file', 'upload'));
    }

    public function getImportType()
    {
        return 'foobar';
    }
```

In your `FooBarAdminListController` you need to add two actions. Please adjust the route names.

```php

    /**
     * @Route("/import/{id}", name="acmebundle_admin_foobar_import_edit")
     * @param Request $request
     * @param Import $import
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function importAction(Request $request, Import $import)
    {
        $configurator = $this->getAdminListConfigurator();

        $result = $this->get('nassau.kunstmaan_import.import_wizard_action')->import($request, $import, $configurator);

        if (false === is_array($result)) {
            if ($result) {
                $this->addFlash('success', $result);
            }

            return $this->redirectToRoute('acmebundle_admin_foobar_import_edit', ['id' => $import->getId()]);
        }

        return $this->render('KunstmaanImportBundle::Import.html.twig', $result);
    }

    /**
     * @Route("/import", name="acmebundle_admin_foobar_import_upload")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function uploadAction(Request $request)
    {
        $configurator = $this->getAdminListConfigurator();

        $result = $this->get('nassau.kunstmaan_import.import_wizard_action')->upload($configurator, $request);

        if (null === $result) {
            $this->addFlash('success', 'nassau.import.flash.successfull_import');

            return $this->redirectToRoute('acmebundle_admin_foobar');
        }

        if ($result instanceof Import) {
            $this->addFlash('warning', 'nassau.import.flash.import_errors');

            return $this->redirectToRoute('acmebundle_admin_foobar_import_edit', ['id' => $result->getId(), 'errors' => true]);
        }

        return $this->render($configurator->getAddTemplate(), $result);

    }
```

## Extending

### Create your own formatters / types

1. Implement the `AttributeFormatter` interface
2. Register it in the container with `kunstmaan_import.formatter` tag and `type` attribute

For example:

```yml
services:
    acme.services.import_formatter.money_formatter:
        class: 'AcmeBundle\Services\ImportFormatter\MoneyFormatter'
        public: false
        tags:
            - name: kunstmaan_import.formatter
              alias: money
```
