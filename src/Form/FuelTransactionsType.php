<?php

namespace App\Form;

use App\Entity\FuelTransactions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FuelTransactionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('yard_name',  TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('date', null, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
                'html5' => true
            ])
            ->add('paid_amount', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])
            ->add('code',  TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('settlement_id',  TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false
            ])
            ->add('company',null, [
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FuelTransactions::class,
        ]);
    }
}
