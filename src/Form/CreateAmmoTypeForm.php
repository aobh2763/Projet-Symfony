<?php

namespace App\Form;

use App\Entity\{Ammo, Gun};
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CreateAmmoTypeForm extends CreateProductTypeForm
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('quantity', null, [
                'attr' => [
                    'placeholder' => 'Enter ammo quantity',
                ],
            ])
            ->add('gun', EntityType::class, [
                'class' => Gun::class,
                'choice_label' => 'name',
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ammo::class,
        ]);
    }
}
