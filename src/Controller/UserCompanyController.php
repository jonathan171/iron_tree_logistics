<?php

namespace App\Controller;

use App\Entity\UserCompany;
use App\Form\UserCompanyType;
use App\Repository\UserCompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user_company')]
class UserCompanyController extends AbstractController
{
    #[Route('/', name: 'app_user_company_index', methods: ['GET'])]
    public function index(UserCompanyRepository $userCompanyRepository): Response
    {
        return $this->render('user_company/index.html.twig', [
            'user_companies' => $userCompanyRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_company_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserCompanyRepository $userCompanyRepository): Response
    {
        $userCompany = new UserCompany();
        $form = $this->createForm(UserCompanyType::class, $userCompany);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userCompanyRepository->save($userCompany, true);

            return $this->redirectToRoute('app_user_company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_company/new.html.twig', [
            'user_company' => $userCompany,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_user_company_show', methods: ['GET'])]
    public function show(UserCompany $userCompany): Response
    {
        return $this->render('user_company/show.html.twig', [
            'user_company' => $userCompany,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_company_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserCompany $userCompany, UserCompanyRepository $userCompanyRepository): Response
    {
        $form = $this->createForm(UserCompanyType::class, $userCompany);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userCompanyRepository->save($userCompany, true);

            return $this->redirectToRoute('app_user_company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_company/edit.html.twig', [
            'user_company' => $userCompany,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_user_company_delete', methods: ['POST'])]
    public function delete(Request $request, UserCompany $userCompany, UserCompanyRepository $userCompanyRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userCompany->getId(), $request->request->get('_token'))) {
            $userCompanyRepository->remove($userCompany, true);
        }

        return $this->redirectToRoute('app_user_company_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/table', name: 'app_user_company_table', methods: ['GET', 'POST'])]
    public function table(Request $request, UserCompanyRepository $userCompanyRepository): Response
    {  
        $datatableParameters = $request->query->all();
        $search =  $datatableParameters["search"];
        
    
        $start = $datatableParameters["start"];
        $length = $datatableParameters["length"];

        

        $data_table  = $userCompanyRepository->findByDataTable(['page' => ($start/$length), 'pageSize' => $length, 'search' => $search['value']]);

    

        // Objeto requerido por Datatables

        $responseData = array(
           // "draw" => '',
            "recordsTotal" => $data_table['totalRecords'],
            "recordsFiltered" => $data_table['totalRecords'],
            "data" => $data_table['data']
        );
    

        return $this->json($responseData);
    }
}
