<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('fullname', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlength' => '50',
                ],
                'label' => 'Noms',
                'label_attr' => [
                    'class' => 'form-label mt-0'
                ],
                'row_attr' => [
                    'class' => 'col-md-6'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 50
                    ]),
                ]
            ])
            ->add('pseudo', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlength' => '20',
                ],
                'label' => 'Prenom (Facultatif)',
                'required' => '',
                'label_attr' => [
                    'class' => 'form-label mt-0'
                ],
                'row_attr' => [
                    'class' => 'col-md-6'
                ],
                'constraints' => [
                    new Assert\Length([
                        'min' => 2,
                        'max' => 20
                    ]),
                ]
            ])

            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlength' => '180',
                ],
                'label' => 'Adresse Gmail',
                'label_attr' => [
                    'class' => 'form-label mt-0'
                ],
                'row_attr' => [
                    'class' => 'col-md-6'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 180
                    ]),
                ]
            ])

            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
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
                    'label' => 'Password'
                ],
                'second_options' => [
                    'label' => 'Confirmation du Mot de Pass'
                ],
                'invalid_message' => 'Le Mot de Pass ne correspond pas !',
            ])

            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt-4'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
