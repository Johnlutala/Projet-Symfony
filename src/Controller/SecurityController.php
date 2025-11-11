<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\InscriptionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController extends AbstractController
{
    #[Route('/connexion', name: 'security.login', methods: ['GET', 'POST'])]
    public function login(
        AuthenticationUtils $authentication
    ): Response {
        return $this->render('pages/security/login.html.twig', [
            'last_username' => $authentication->getLastUsername(),
            'error' => $authentication->getLastAuthenticationError()
        ]);
    }

    #[ROUTE('/deconnexion', 'security.logout')]
    public function logout()
    {
        #
    }

    
    #[ROUTE('/inscription', 'inscription', methods: ['GET', 'POST'])]
    public function inscription(
        Request $request,
        EntityManagerInterface $manager
    ) {
        $Inscription = new User();
        $form = $this->createForm(InscriptionType::class, $Inscription);;
        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {

            $Inscription = $form->getData();
            $manager->persist($Inscription);
            $manager->flush();

            $this->addFlash(
                'success',
                'Un Utilisateur a été crée avec succès !'
            );
            return $this->redirectToRoute('security.login');
        }

        return $this->render('pages/security/inscription.html.twig', [

            'form' => $form->createView()

        ]);
    }
}
