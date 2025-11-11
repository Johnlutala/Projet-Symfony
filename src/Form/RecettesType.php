<?php

namespace App\Form;

use App\Entity\Ingredients;
use App\Entity\Recettes;
use App\Repository\IngredientsRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RecettesType extends AbstractType
{

    private $token;

    public function __construct(TokenStorageInterface $token)
    {
        $this->token = $token;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Première ligne - Nom et Temps
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlength' => '50',
                    'placeholder' => 'Nom de la recette'
                ],
                'label' => 'Nom',
                'label_attr' => [
                    'class' => 'form-label mt-3'
                ],
                'row_attr' => [
                    'class' => 'col-md-6'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom est obligatoire']),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères'
                    ]),
                ]
            ])

            ->add('time', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'min' => '1',
                    'placeholder' => 'En minutes'
                ],
                'label' => 'Temps de préparation (min)',
                'label_attr' => [
                    'class' => 'form-label mt-3'
                ],
                'row_attr' => [
                    'class' => 'col-md-6'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le temps est obligatoire']),
                    new Assert\Positive(['message' => 'Le temps doit être positif']),
                    new Assert\LessThan([
                        'value' => 1440,
                        'message' => 'Le temps ne peut pas dépasser 24 heures'
                    ])
                ]
            ])

            // Deuxième ligne - Difficulté et Nombre de personnes
            ->add('difficulty', RangeType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'min' => '1',
                    'max' => '5',
                    'placeholder' => 'De 1 à 5'
                ],
                'label' => 'Difficulté',
                'label_attr' => [
                    'class' => 'form-label mt-3'
                ],
                'row_attr' => [
                    'class' => 'col-md-6'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La difficulté est obligatoire']),
                    new Assert\Range([
                        'min' => 1,
                        'max' => 5,
                        'notInRangeMessage' => 'La difficulté doit être entre {{ min }} et {{ max }}'
                    ])
                ]
            ])

            ->add('nbrepersonne', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'min' => '1',
                    'placeholder' => 'Nombre de personnes'
                ],
                'label' => 'Nombre de personnes',
                'label_attr' => [
                    'class' => 'form-label mt-3'
                ],
                'row_attr' => [
                    'class' => 'col-md-6'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nombre de personnes est obligatoire']),
                    new Assert\Positive(['message' => 'Le nombre doit être positif']),
                    new Assert\LessThan([
                        'value' => 100,
                        'message' => 'Le nombre de personnes ne peut pas dépasser {{ compared_value }}'
                    ])
                ]
            ])

            // Troisième ligne - Prix et Ingrédients
            ->add('price', MoneyType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '0.00'
                ],
                'label' => 'Prix',
                'label_attr' => [
                    'class' => 'form-label mt-3'
                ],
                'row_attr' => [
                    'class' => 'col-md-6'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prix est obligatoire']),
                    new Assert\Positive(['message' => 'Le prix doit être positif']),
                    new Assert\LessThan([
                        'value' => 200,
                        'message' => 'Le prix ne peut pas dépasser {{ compared_value }}€'
                    ])
                ]
            ])

            ->add('isfavorite', CheckboxType::class, [
                'label' => 'Is Favorite ? ',
                'attr' => [
                    'class' => ' form-check-input',
                    'style' => 'margin-left: 8px;'
                ],
                'required' => '',
                'constraints' => [
                    new Assert\NotNull(),
                ]
            ])
            ->add('ispublic', CheckboxType::class, [
                'label' => 'Is Public ?',
                'attr' => [
                    'class' => 'form-check-input',
                    'style' => 'margin-left: 8px;'
                ],
                'required' => false,
                'constraints' => [
                    new Assert\NotNull(),
                ],
            ])

            ->add('ingredients', EntityType::class, [
                'class' => ingredients::class,
                // QueryBuilder, permet à faire des requettes dans la BD
                'query_builder' => function (IngredientsRepository $r): QueryBuilder {

                    return $r->createQueryBuilder('i')
                        ->where('i.user = :user')
                        ->setParameter('user', $this->token->getToken()->getUser())
                        ->orderBy('i.name', 'ASC');
                },
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Ingrédients',
                'label_attr' => [
                    'class' => 'form-label mt-3'
                ],
                'row_attr' => [
                    'class' => 'col-md-6'
                ],
                'required' => false
            ])

            // Quatrième ligne - Description (pleine largeur)
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => '5',
                    'placeholder' => 'Décrivez votre recette...'
                ],
                'label' => 'Description',
                'label_attr' => [
                    'class' => 'form-label mt-3'
                ],
                'row_attr' => [
                    'class' => 'col-md-12'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La description est obligatoire']),
                    new Assert\Length([
                        'min' => 10,
                        'minMessage' => 'La description doit contenir au moins {{ limit }} caractères'
                    ])
                ]
            ])

            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-dark mt-4',
                    'name' => 'submit',
                ],
                'label' => 'Ajouter un Ingrédient',
                'row_attr' => [
                    'class' => 'col-md-12'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recettes::class,
        ]);
    }
}
