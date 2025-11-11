<?php

namespace App\Controller;

use App\Entity\Recettes;
use App\Form\RecettesType;
use App\Repository\IngredientsRepository;
use App\Repository\RecettesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class RecettesController extends AbstractController

{
    // Page d'accueil pour les ingedients
    #[Route('/recettes', name: 'app_recettes', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        return $this->render(
            'pages/recettes/index.html.twig'

        );
    }


    //   Show Recettes
    #[Route('/RecettesShow/{id}', name: 'show.recettes', methods: ['GET'])]
    public function Show_Recettes(Recettes $recette): Response
    {
        $user = $this->getUser();

        // Si ROLE_ADMIN, accès autorisé
        if ($this->isGranted('ROLE_ADMIN')) {
            // ok, tout passe
        }
        // Si ROLE_USER, vérifier que la recette est publique
        elseif ($this->isGranted('ROLE_USER') && $recette->getIsPublic() === true) {
            // ok, accès autorisé
        } else {
            throw $this->createAccessDeniedException('Désolé:  Vous n’avez pas les droits pour accéder à cette recette car Cette Recette est privé,');
        }

        return $this->render('pages/recettes/show.html.twig', [
            'recettes' => $recette
        ]);
    }

    //Afficher les Recettes Publiques
    #[Route('/recette/recette_public', name: 'index_public', methods: ['GET'])]
    public function pubrecette(
        PaginatorInterface $paginator,
        RecettesRepository $repository,
        Request $request
    ) {

        $recettes = $paginator->paginate(
            $repository->findPublicRecette(null),
            $request->query->getInt('page', 1),
            10
        );


        return $this->render('pages/recettes/index_public.html.twig', [
            'recettes' => $recettes

        ]);
    }
    // Afficher tous les recettes disponibles dans la Base des données 
    #[Route('/recettes/afficher', 'afficher_recettes', methods: ['GET', 'POST'])]
    #[isGranted('ROLE_USER')]
    public function afficher(
        RecettesRepository $repository,
        PaginatorInterface $paginator,
        HttpFoundationRequest $request
    ): Response {

        $recettes = $paginator->paginate(
            $repository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('pages/recettes/index.html.twig', [
            'recettes' => $recettes
        ]);
    }


    // Ajouter une recette dans le formulaire
    #[Route('/recettes/ajouter', name: 'ajouter_recettes', methods: ['GET', 'POST'])]
    public function Ajouter(
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        // Création de formulaire
        $recettes = new Recettes();
        $form = $this->createForm(RecettesType::class, $recettes);
        // 
        // Recupération des données du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recettes = $form->getData();
            $recettes->setUser($this->getUser());
            $manager->persist($recettes);
            $manager->flush();
            // 
            // Message flash
            $this->addFlash('success', 'Recette a été enregistré avec succès');
            return $this->redirectToRoute('ajouter_recettes');
        }
        return $this->render(
            'Pages/recettes/new.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    //  Suppression d'une recette
    #[Route('/recettes/suppression/{id}', name: 'delete_recettes', methods: ['GET', 'POST'])]
    public function delete(
        EntityManagerInterface $manager,
        Recettes $recettes
    ): Response {

        if (!$recettes) {

            $this->addFlash('success', `Echec de la suppression car l'Ingédient en question n'a pas été trouvé!`);
        }
        $manager->remove($recettes);
        $manager->flush();

        $this->addFlash('success', 'Votre Ingédient a été Supprimé avec Succès !');

        return $this->redirectToRoute('afficher_recettes');
    }


    // Modifier une Recette
    #[Route('/recettes/update/{id}', name: 'update_recettes', methods: ['GET', 'POST'])]
    public function edit(
        Recettes $recettes,
        Request $request,
        EntityManagerInterface $manager
    ): Response {


        // Recupération de x ingredient dans le formulaire 
        $form = $this->createForm(RecettesType::class, $recettes);

        // Recupération des données du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recettes = $form->getData();
            $manager->persist($recettes);
            $manager->flush();

            // Message flash
            $this->addFlash('success', 'Votre Ingédient a été Modifier avec Succès !');

            return $this->redirectToRoute('afficher_recettes');
        }

        return $this->render(
            "pages/recettes/update.html.twig",
            ['form' => $form->createView()]
        );
    }
}
