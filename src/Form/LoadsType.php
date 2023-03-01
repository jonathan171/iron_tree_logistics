<?php

namespace App\Form;

use App\Entity\Loads;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoadsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fuel_surcharge', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])
            ->add('well_name',  TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('code',  TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('driver_name',  TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('arrived_at_loader', null, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
                'html5' => true
            ])
            ->add('loaded_distance', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])
            ->add('line_haul', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])
            ->add('order_status',ChoiceType::class, [
                'choices'  => [
                    'completed' => 'completed',
                    'manually completed' => 'manually_completed'
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('billing_status',ChoiceType::class, [
                'choices'  => [
                    'Invoice Approved' => 'Invoice Approved'
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('trier',null, [
                'attr' => [
                    'class' => 'form-control',
                ]
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
            'data_class' => Loads::class,
        ]);
    }
}
