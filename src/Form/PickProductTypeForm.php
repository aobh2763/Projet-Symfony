<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PickProductTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product_type', ChoiceType::class, [
                'choices' => [
                    'Gun' => 'gun',
                    'Ammo' => 'ammo',
                    'Accessory' => 'accessory',
                    'Melee' => 'melee',
                ],
                'data' => 'gun',
                    'attr' => [
                    'onchange' => 'this.form.submit();'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }

    public function getBlockPrefix(): string {
        return '';
    }
}
