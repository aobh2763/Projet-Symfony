<?php

namespace App\Form;

use App\Entity\{Gun, Ammo};
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CreateGunTypeForm extends CreateProductTypeForm
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('accuracy', null, [
                'attr' => [
                    'placeholder' => 'Enter accuracy value',
                ],
            ])
            ->add('caliber', null, [
                'attr' => [
                    'placeholder' => 'Enter caliber value',
                ],
            ])
            ->add('gun_range', null, [
                'label' => 'Gun Range',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Enter gun range',
                ],
            ])
            ->add('ammo', EntityType::class, [
                'class' => Ammo::class,
                'choice_label' => 'name',
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Gun::class,
        ]);
    }
}
