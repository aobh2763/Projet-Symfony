<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{SubmitType, TextareaType, FileType};

class CreateProductTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class, [
                'label' => 'Product Description',
                'attr' => [
                    'placeholder' => 'Enter product description',
                ],
            ])
            ->add('name', null, [
                'label' => 'Product Name',
                'attr' => [
                    'placeholder' => 'Enter product name',
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Product Image',
                'mapped' => false,
                'required' => false,
            ])
            ->add('price', null, [
                'label' => 'Price',
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
            ->add('submit', SubmitType::class, [
                'label' => 'Submit',
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
