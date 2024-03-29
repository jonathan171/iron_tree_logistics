<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class LoginController extends AbstractController
{   
    #[Route('/', name: 'app_inicio')]
    public function inicio(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();


        if($this->getUser()){

           
            if($this->isGranted('ROLE_ADMIN')){
                return $this->redirectToRoute('app_user_index');  
            }
            if($this->isGranted('ROLE_DRIVER')){
                return $this->redirectToRoute('app_driver');  
            }
            
         }

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        if($this->getUser()){

           
            if($this->isGranted('ROLE_ADMIN')){
                return $this->redirectToRoute('app_user_index');  
            }
            
            if($this->isGranted('ROLE_DRIVER')){
                return $this->redirectToRoute('app_driver');  
            }
         }

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route(
        path: '/{_locale}/update_locale',
        name: 'update_locale'
    )]
    public function update(): Response
    {
        return $this->redirectToRoute('app_inicio');  
    }

    

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout()
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
