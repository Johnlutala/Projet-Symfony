<?php

namespace App\Controller;


use App\Entity\Ingredients;
use App\Form\IngredientsType;

use App\Repository\IngredientsRepository;

use Doctrine\ORM\EntityManagerInterface; 
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


final class IngedientsController extends AbstractController
{

    
// Afficher tous les ingedients disponibles dans la Base des données 
    #[Route('/ingredients', name: 'Afficher_Ingedients')]
    #[IsGranted('ROLE_USER')]
    public function index(
        IngredientsRepository $repository, 
        PaginatorInterface $paginator, 
        HttpFoundationRequest $request): Response
    {

        $ingrediens = $paginator->paginate(
        $repository->findBy(['user'=> $this->getUser()]),
        $request->query->getInt('page', 1), 
        10 
    );
        return $this->render('pages/ingredients/index.html.twig',[
            'ingrediens'=>$ingrediens
        ]);

    }
    
// Ajouter un ingedient
    #[Route('/ingredients/ajouter', name: 'Ajouter_Ingedient', methods:['GET', 'POST'])] 
    public function new(
     Request $request,
     EntityManagerInterface $manager): Response
    {
        // Création de formulaire
        $mesingredients = new Ingredients();
        $form = $this->createForm(IngredientsType::class, $mesingredients);

        // Recupération des données du formulaire
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $mesingredients = $form->getData();
            $mesingredients ->setUser($this->getUser());

            $manager->persist($mesingredients);
            $manager->flush();

        // Message flash
            $this->addFlash('success', 'Un Ingédient a été enregistré avec succès !');

           return $this->redirectToRoute('Ajouter_Ingedient');
        }   
        
        return $this->render('pages/ingredients/new.html.twig',[
            'form'=>$form->createView()
        ]
    );
    }


// Modifier un ingedientù
// #[Security("Is_granted('ROLE_USER') and user==")]
#[Route('/ingrediens/update/{id}', name:'update', methods:['GET', 'POST'])]
public function edit(
    Ingredients $ingrediens,
    Request $request,
    EntityManagerInterface $manager
):Response
{


      // Recupération de x ingredient dans le formulaire 
  $form = $this->createForm(IngredientsType::class, $ingrediens);

          // Recupération des données du formulaire
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $ingrediens = $form->getData();

            $manager->persist($ingrediens);
            $manager->flush();

        // Message flash
            $this->addFlash('success', 'Votre Ingédient a été Modifier avec Succès !');

           return $this->redirectToRoute('Afficher_Ingedients');
        }   

            return $this->render("pages/ingredients/update.html.twig",   
            ['form' => $form->createView()]);
 }

//  Suppression d'un ingredient

 #[Route('/ingrediens/suppression/{id}', name:'delete', methods:['GET', 'POST'])]
    public function delete(
    EntityManagerInterface $manager,
    Ingredients $ingrediens
    ):Response
     {

        if(!$ingrediens){
        
            $this->addFlash('success', `Echec de la suppression car l'Ingédient en question n'a pas été trouvé!`);


        }
        $manager->remove($ingrediens);
        $manager->flush();

        $this->addFlash('success', 'Votre Ingédient a été Supprimé avec Succès !');

        return $this->redirectToRoute('Afficher_Ingedients');
    }
}
