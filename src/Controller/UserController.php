<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{   
    protected  $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }
    
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {   
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/table', name: 'app_user_table', methods: ['GET', 'POST'])]
    public function table(Request $request, UserRepository $userRepository): Response
    {  
        $datatableParameters = $request->query->all();
        $search =  $datatableParameters["search"];
        
    
        $start = $datatableParameters["start"];
        $length = $datatableParameters["length"];
        $order = $datatableParameters["order"][0];

        

        $data_table  = $userRepository->findByDataTable(['page' => ($start/$length), 'pageSize' => $length, 'search' => $search['value'], 'order' =>$order]);

    

        // Objeto requerido por Datatables

        $responseData = array(
           // "draw" => '',
            "recordsTotal" => $data_table['totalRecords'],
            "recordsFiltered" => $data_table['totalRecords'],
            "data" => $data_table['data']
        );
    

        return $this->json($responseData);
    }


    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            $userRepository->save($user, true);
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('notificaciones@packagelight.com', 'Iron tree Logistics'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/buscadorAjaxUser', name: 'app_user_buscador_ajax_user', methods: ['GET', 'POST'])]
    public function executeBuscadorAjaxUser(
        Request $request,
        UserRepository $userRepository
    ) {
        $busqueda = $request->query->get('term');
        $start = 0;
        $length = 20;


        $data_table  = $userRepository->findByDataShearch([
            'page' => ($start / $length),
            'pageSize' =>  $length,
            'search' =>  $busqueda
        ]);


        $responseData = array(
            "results" => $data_table['data'],
            "pagination" => array(
                // Determinar si hay mas paginas disponibles
                "more" => (false)
            )
        );
        return $this->json($responseData);
    }

}
