<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', null, ['attr' => [
            'class' => 'form-control'
        ]])->add('plainPassword', PasswordType::class, [
            // instead of being set onto the object directly,
            // this is read and encoded in the controller
            'mapped' => false,
            'attr' => [
                'autocomplete' => 'new-password',
                'class' => 'form-control'
            ]
        ])->add('firstName', null, [
            'attr' => [
                'class' => 'form-control',
            ]
        ])->add('lastName', null, [
            'attr' => [
                'class' => 'form-control',
            ]
        ])->add('roles', ChoiceType::class, [
            'required' => true,
            'multiple' => true,
            'expanded' => true,
            'choices'  => [
                'Driver' => 'ROLE_DRIVER'
            ],
            'attr' => [
                'class' => 'form-control',
            ]
        ])->add('active', CheckboxType::class, [
            'required'=> false
        ])->add('isVerified',null, [
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
