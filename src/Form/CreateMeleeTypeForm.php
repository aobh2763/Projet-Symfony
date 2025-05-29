<?php

namespace App\Form;

use App\Entity\Melee;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CreateMeleeTypeForm extends CreateProductTypeForm
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('reach', null, [
                'attr' => [
                    'placeholder' => 'Enter reach',
                ],
            ])
            ->add('type', null, [
                'attr' => [
                    'placeholder' => 'Enter type',
                ],
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Melee::class,
        ]);
    }
}
