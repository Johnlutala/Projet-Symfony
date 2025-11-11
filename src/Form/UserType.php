<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Ligne 1 : fullname et pseudo côte à côte
            ->add('fullname', TextType::class, [
                'attr' => ['class' => 'form-control', 'minlength' => '2', 'maxlength' => '50'],
                'label' => 'Nom',
                'label_attr' => ['class' => 'form-label mt-0'],
                'row_attr' => ['class' => 'col-md-6 mb-3'], // moitié de la ligne
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 2, 'max' => 50]),
                ]
            ])
            ->add('pseudo', TextType::class, [
                'attr' => ['class' => 'form-control', 'minlength' => '2', 'maxlength' => '20'],
                'label' => 'Prénom',
                'required' => false,
                'label_attr' => ['class' => 'form-label mt-0'],
                'row_attr' => ['class' => 'col-md-6 mb-3'], // moitié de la ligne
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 20]),
                ]
            ])
            // Ligne 2 : mot de passe et bouton côte à côte
            ->add('plainPassword', PasswordType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Confirmer la Modification avec Votre Mot de passe',
                'label_attr' => ['class' => 'form-label mt-0'],
                'row_attr' => ['class' => 'col-md-6 mb-3'], // moitié de la ligne
                'required' => true,
                'invalid_message' => 'Le Mot de Pass ne correspond pas !',
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary w-100'
                ],
                'label' => 'Modifier', // bouton prend toute la moitié
                'row_attr' => ['class' => 'col-md-6 mb-3'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => User::class]);
    }
}
