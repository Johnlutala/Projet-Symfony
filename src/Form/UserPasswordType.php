<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints as Assert;


class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'options' => [
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'label' => 'Mot de Pass',
                    'label_attr' => [
                        'class' => 'form-label mt-0'
                    ],
                    'row_attr' => [
                        'class' => 'col-md-6'
                    ],
                ],
                'required' => true,
                'first_options'  => [
                    'label' => 'Mot de Pass'
                ],
                
                'second_options' => [
                    'label' => 'Confirmation du Mot de Pass'
                ],
                'invalid_message' => 'Le Mot de Pass ne correspond pas !',
            ])

            ->add('newPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Nouveau Mot de Pass',
                'label_attr' => [
                    'class' => 'form-label mt-0'
                ],
                'row_attr' => [
                    'class' => 'col-md-6'
                ],
                'required' => true,

                'constraints' => [
                    new Assert\NotBlank()
                ]

            ])

            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt-4'
                ]
            ]);
    }
}
