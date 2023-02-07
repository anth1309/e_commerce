<?php

namespace App\Controller;

use App\Form\ResetPasswordRequestFormType;
use App\Repository\UsersRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        //permet de renvoyer l user sur son compte si retente de se logger
        //if ($this->getUser()) {
        //  return $this->redirectToRoute('app_login');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route(path: '/déconnexion', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    //en donnant mon adresse mail je recoit un lien

    #[Route('/oubli-pass', name: 'forgotten_password')]

    public function forgottenPassword(
        Request $request,                       //on va chercher ce qu on a besoin pourla fction
        UsersRepository $usersRepository,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $entityManager,


    ): Response {

        $form = $this->createForm(ResetPasswordRequestFormType::class);

        //recuperer les données du form
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            //on va chercher le user par son mail
            $user = $usersRepository->findOneByEmail($form->get('email')->getData());

            //on verifi si on a un user

            if ($user) {
                //on genere un token de réinitialisation
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();

                //on genere un lien de reinitialisation mdp
                $url = $this->generateUrl(
                    'reset_pass',
                    ['token' => $token],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
                //on cree les donnees du mail
                // $context = compact('url', 'user');
                // //envoi du mail
                // $mail->send(
                //     'no-reply@e-commerce.fr',
                //     $user->getEmail(),
                //     'Réinitialisation de mot de passe',
                //     'password_reset',
                //     $context
                // );

                // $this->addFlash('success', 'Email envoyé avec succés');
                // return $this->redirectToRoute('app_login');
            }

            //user est mauvais
            $this->addFlash('danger', 'Un probléme est survenu');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password_request.html.twig', [
            'requestPassForm' => $form->createView()
        ]);
        //route du lien pour creer le new mdp

        // #[Route('/oubli-pass/{token}', name:'reset_pass')]
        // public function resetPass(): Response 
        // {

        // }
    }




    // nouvelle route en cliquant sur le lien me permet de choisir un new mdp
}
