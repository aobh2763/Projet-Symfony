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
                'In progress' => 'in-progress',
                'Delivered' => 'delivered',
                'Canceled' => 'canceled',
            ],
            'data' => 'all',
            'attr' => [
                'onchange' => 'this.form.submit();',
            ],
            ])
            ->add('time', ChoiceType::class, [
            'choices' => [
                'For all time' => 'all-time',
                'Last year' => 'last-year',
                'Last month' => 'last-month',
                'Last 30 days' => 'last-30-days',
            ],
            'data' => 'all-time',
            'attr' => [
                'onchange' => 'this.form.submit();',
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
