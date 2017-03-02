<?php

namespace Nassau\KunstmaanImportBundle\Form;

use Nassau\KunstmaanImportBundle\Entity\ImportItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\EqualTo;

class ImportItemAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', HiddenType::class, [
            'constraints' => new EqualTo($options['next_id']),
        ]);

        $builder->add('imported_entity', $options['imported_entity_type'], [
            'label' => false
        ] + $options['imported_entity_options']);

        $builder->add('save', SubmitType::class, [
            'label' => 'nassau.import.button.save',
            'attr' => [
                'class' => 'btn btn-primary'
            ]
        ]);

        $builder->add('skip', SubmitType::class, [
            'label' => 'nassau.import.button.skip',
            'attr' => [
                'class' => 'btn btn-danger',
                'formnovalidate' => 'formnovalidate'
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ImportItem::class,
            'imported_entity_options' => [],
        ]);

        $resolver->setRequired([
            'imported_entity_type',
            'next_id',
        ]);

        $resolver->setAllowedTypes('imported_entity_options', 'array');
    }

}
