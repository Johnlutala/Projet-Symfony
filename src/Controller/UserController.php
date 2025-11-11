<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserController extends AbstractController
{


    // Modifier un Utilisateur
    #[Route('/edition/utilisateur/{id}', name: 'updateUser', methods: ['GET', 'POST'])]
    public function update(
        User $user,
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $hasher
    ): Response {
        //Vérification si ce n'est pas le même user de {id} connecté !
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }
        //Vérification si c'est le même user {id} connecté avec la BD!
        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('afficher_recettes');
        }

        //Récuperation des information dans le formulaire USER
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        //Vérification si le formulaire est soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            //Vérification si le mot de pass user est valide
            if ($hasher->isPasswordValid($user, $form->getData()->getPlainPassword())) {

                $user = $form->getData();
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre identité a été modifier avec succès'
                );
                return $this->redirectToRoute('home');
            }
        }


        return $this->render('pages/security/user/updateUser.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    //Modification de Mot de Passe USER
    #[Route('/update/password/{id}', 'update.password', methods: ['GET', 'POST'])]
    public function Updatepassword(
        User $user,
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $hasher
    ): Response {

        // Vérifications de sécurité
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }
        // Vérifications de sécurité avec la BD
        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('afficher_recettes');
        }
        //Récuperation de formumaire
        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire 
            $plainPassword = $form->get('plainPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();

            // Vérifier que l'ancien mot de passe est correct
            if ($hasher->isPasswordValid($user, $plainPassword)) {
                // Mettre à jour le mot de passe
                $user->setPlainPassword($newPassword);

                // SOLUTION 1 : Encoder directement le mot de passe
                $hashedPassword = $hasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre mot de passe a été modifié avec succès'
                );

                return $this->redirectToRoute('afficher_recettes');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe actuel est incorrect'
                );
            }
        }

        return $this->render('pages/security/user/updatePassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
