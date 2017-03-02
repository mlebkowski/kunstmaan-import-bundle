<?php

namespace Nassau\KunstmaanImportBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ImportFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', FileType::class, [
            'constraints' => new NotBlank(),
        ]);
    }

    public function getBlockPrefix()
    {
        return 'nassau_import_file';
    }

}
