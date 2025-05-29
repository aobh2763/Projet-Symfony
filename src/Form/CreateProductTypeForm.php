<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{TextareaType, FileType};

class CreateProductTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'attr' => [
                    'placeholder' => 'Enter product name',
                ],
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Enter product description',
                ],
            ])
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('price', null, [
                'attr' => [
                    'placeholder' => 'Enter the price (e.g., 19.99)',
                ],
            ])
            ->add('sale', null, [
                'label' => 'Sale (%)',
                'attr' => [
                    'placeholder' => 'Enter sale percentage (e.g., 10)',
                ],
            ])
            ->add('stock', null, [
                'label' => 'Stock Quantity',
                'attr' => [
                    'placeholder' => 'Enter available stock',
                ],
            ])
            ->add('weight', null, [
                'label' => 'Product Weight',
                'attr' => [
                    'placeholder' => 'Enter product weight',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
