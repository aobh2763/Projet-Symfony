<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class OrdersFilterTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
            'choices' => [
                'All statuses' => 'all',
                'In progress' => 'in_progress',
                'Delivered' => 'delivered',
                'Canceled' => 'canceled',
            ],
            'placeholder' => 'Select status',
            ])
            ->add('time', ChoiceType::class, [
            'choices' => [
                'For all time' => 'all_time',
                'Last year' => 'last_year',
                'Last month' => 'last_month',
                'Last 30 days' => 'last_30_days',
            ],
            'data' => 'all_time',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
