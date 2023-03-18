<?php

namespace App\Controller;

use App\Entity\UserDocument;
use App\Form\UserDocumentType;
use App\Repository\UserDocumentRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user_document')]
class UserDocumentController extends AbstractController
{
    #[Route('/', name: 'app_user_document_index', methods: ['GET'])]
    public function index(UserDocumentRepository $userDocumentRepository): Response
    {
        return $this->render('user_document/index.html.twig', [
            'user_documents' => $userDocumentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_document_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        UserDocumentRepository $userDocumentRepository,
        FileUploader $fileUploader,
        ParameterBagInterface $params
    ): Response {
        $userDocument = new UserDocument();
        $user =  $this->getUser();
        $userDocument->setUser($user);
        $form = $this->createForm(UserDocumentType::class, $userDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $cdl = $form->get('cdl')->getData();
            $medical_card = $form->get('medical_card')->getData();
            $h2s = $form->get('h2s')->getData();
            $pec = $form->get('pec')->getData();
            $cuestionario = $form->get('cuestionario')->getData();

            if ($cdl) {
                $fileName = $fileUploader
                    ->setTargetDirectory($params->get("upload_dir_images"))
                    ->upload($cdl);

                $userDocument->setCdl($fileName);
            }
            if ($medical_card) {
                $fileName = $fileUploader
                    ->setTargetDirectory($params->get("upload_dir_images"))
                    ->upload($medical_card);

                $userDocument->setMedicalCard($fileName);
            }
            if ($h2s) {
                $fileName = $fileUploader
                    ->setTargetDirectory($params->get("upload_dir_images"))
                    ->upload($h2s);

                $userDocument->setH2s($fileName);
            }
            if ($pec) {
                $fileName = $fileUploader
                    ->setTargetDirectory($params->get("upload_dir_images"))
                    ->upload($pec);

                $userDocument->setPec($fileName);
            }
            if ($cuestionario) {
                $fileName = $fileUploader
                    ->setTargetDirectory($params->get("upload_dir_images"))
                    ->upload($cuestionario);

                $userDocument->setCuestionario($fileName);
            }


            $userDocumentRepository->save($userDocument, true);

            return $this->redirectToRoute('app_user_document_edit', ['id' => $userDocument->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_document/new.html.twig', [
            'user_document' => $userDocument,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_user_document_show', methods: ['GET'])]
    public function show(UserDocument $userDocument): Response
    {
        return $this->render('user_document/show.html.twig', [
            'user_document' => $userDocument,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_document_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        UserDocument $userDocument,
        UserDocumentRepository $userDocumentRepository,
        FileUploader $fileUploader,
        ParameterBagInterface $params
    ): Response {
        $initialCdl = $userDocument->getCdl();
        $initialMedicalCard = $userDocument->getMedicalCard();
        $initialH2s = $userDocument->getH2s();
        $initialPec = $userDocument->getPec();
        $initialCuestionario = $userDocument->getCuestionario();
        $form = $this->createForm(UserDocumentType::class, $userDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $cdl = $form->get('cdl')->getData();
            $medical_card = $form->get('medical_card')->getData();
            $h2s = $form->get('h2s')->getData();
            $pec = $form->get('pec')->getData();
            $cuestionario = $form->get('cuestionario')->getData();

            if ($cdl) {
                $fileName = $fileUploader
                    ->setTargetDirectory($params->get("upload_dir_images"))
                    ->upload($cdl);

                if ($initialCdl != null) {
                    $fileUploader->delete($initialCdl);
                }

                $userDocument->setCdl($fileName);
            } else {
                $userDocument->setCdl($initialCdl);
            }
            if ($medical_card) {
                $fileName = $fileUploader
                    ->setTargetDirectory($params->get("upload_dir_images"))
                    ->upload($medical_card);

                if ($initialMedicalCard != null) {
                    $fileUploader->delete($initialMedicalCard);
                }

                $userDocument->setMedicalCard($fileName);
            } else {
                $userDocument->setMedicalCard($initialMedicalCard);
            }
            if ($h2s) {
                $fileName = $fileUploader
                    ->setTargetDirectory($params->get("upload_dir_images"))
                    ->upload($h2s);

                if ($initialH2s != null) {
                    $fileUploader->delete($initialH2s);
                }

                $userDocument->setH2s($fileName);
            } else {
                $userDocument->setH2s($initialH2s);
            }
            if ($pec) {
                $fileName = $fileUploader
                    ->setTargetDirectory($params->get("upload_dir_images"))
                    ->upload($pec);

                if ($initialPec != null) {
                    $fileUploader->delete($initialPec);
                }

                $userDocument->setPec($fileName);
            } else {
                $userDocument->setPec($initialPec);
            }
            if ($cuestionario) {
                $fileName = $fileUploader
                    ->setTargetDirectory($params->get("upload_dir_images"))
                    ->upload($cuestionario);

                if ($initialCuestionario != null) {
                    $fileUploader->delete($initialCuestionario);
                }

                $userDocument->setCuestionario($fileName);
            } else {
                $userDocument->setCuestionario($initialCuestionario);
            }

            $userDocumentRepository->save($userDocument, true);

            return $this->redirectToRoute('app_user_document_edit', ['id' => $userDocument->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_document/edit.html.twig', [
            'user_document' => $userDocument,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_user_document_delete', methods: ['POST'])]
    public function delete(Request $request, UserDocument $userDocument, UserDocumentRepository $userDocumentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $userDocument->getId(), $request->request->get('_token'))) {
            $userDocumentRepository->remove($userDocument, true);
        }

        return $this->redirectToRoute('app_user_document_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/table', name: 'app_user_document_table', methods: ['GET', 'POST'])]
    public function table(Request $request, UserDocumentRepository $userDocumentRepository): Response
    {
        $datatableParameters = $request->query->all();
        $search =  $datatableParameters["search"];


        $start = $datatableParameters["start"];
        $length = $datatableParameters["length"];



        $data_table  = $userDocumentRepository->findByDataTable(['page' => ($start / $length), 'pageSize' => $length, 'search' => $search['value']]);



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
