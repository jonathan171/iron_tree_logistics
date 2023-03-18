<?php

namespace App\Form;

use App\Entity\UserDocument;
use App\Form\DataTransformer\UserToNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserDocumentType extends AbstractType
{   
    private $transformer;

    public function __construct(UserToNumberTransformer $transformer)
    {
        $this->transformer = $transformer;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cdl', FileType::class, [
                'required' => false,
                'data_class' => null,
                'attr' => [
                    'class' => 'form-control-file'
                ]
            ])
            ->add('medical_card', FileType::class, [
                'required' => false,
                'data_class' => null,
                'attr' => [
                    'class' => 'form-control-file'
                ]
            ])
            ->add('h2s', FileType::class, [
                'required' => false,
                'data_class' => null,
                'attr' => [
                    'class' => 'form-control-file'
                ]
            ])
            ->add('pec', FileType::class, [
                'required' => false,
                'data_class' => null,
                'attr' => [
                    'class' => 'form-control-file'
                ]
            ])
            ->add('cuestionario', FileType::class, [
                'required' => false,
                'data_class' => null,
                'attr' => [
                    'class' => 'form-control-file'
                ]
            ])
            ->add('user', TextType::class, [
                'attr' => [
                    "class" => "form-control",
                ],
            ])
        ;

        $builder->get('user')
        ->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserDocument::class,
        ]);
    }
}
