<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterTypeForm extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Search for products...',
                    'length' => 255,
                    'class' => 'form-control'
                ]
            ])
            ->add('limit', ChoiceType::class, [
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'choices' => [
                    '9' => '9',
                    '12' => '12',
                    '18' => '18',
                    '24' => '24',
                    '30' => '30'
                ],
            ])
            ->add('type', ChoiceType::class, [
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'choices' => [
                    'Any Type' => 'any',
                    'Guns' => 'guns',
                    'Ammo' => 'ammo',
                    'Melee' => 'melee',
                    'Accessories' => 'accessories'
                ],
            ])
            ->add('range', ChoiceType::class, [
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'choices' => [
                    'All Prices' => 'all',
                    'Under $25' => 'under_25',
                    '$25 to $50' => '25_to_50',
                    '$50 to $100' => '50_to_100',
                    '$100 to $200' => '100_to_200',
                    '$200 & Above' => '200above'
                ],
            ])
            ->add('sort', ChoiceType::class, [
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'choices' => [
                    'Featured' => 'featured',
                    'Price: Low to High' => 'price_low_to_high',
                    'Price: High to Low' => 'price_high_to_low',
                    'Customer Rating' => 'customer_rating',
                    'On Sale' => 'on_sale'
                ],
            ])
            ->add('submit', SubmitType::class)
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
