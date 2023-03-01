<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\FuelTransactions;
use App\Form\FuelTransactionsType;
use App\Repository\FuelTransactionsRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/fuel_transactions')]
class FuelTransactionsController extends AbstractController
{   
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }
    
    #[Route('/', name: 'app_fuel_transactions_index', methods: ['GET'])]
    public function index(FuelTransactionsRepository $fuelTransactionsRepository): Response
    {
        return $this->render('fuel_transactions/index.html.twig', [
            'fuel_transactions' => $fuelTransactionsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_fuel_transactions_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FuelTransactionsRepository $fuelTransactionsRepository): Response
    {
        $fuelTransaction = new FuelTransactions();
        $form = $this->createForm(FuelTransactionsType::class, $fuelTransaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fuelTransactionsRepository->save($fuelTransaction, true);

            return $this->redirectToRoute('app_fuel_transactions_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('fuel_transactions/new.html.twig', [
            'fuel_transaction' => $fuelTransaction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_fuel_transactions_show', methods: ['GET'])]
    public function show(FuelTransactions $fuelTransaction): Response
    {
        return $this->render('fuel_transactions/show.html.twig', [
            'fuel_transaction' => $fuelTransaction,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_fuel_transactions_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FuelTransactions $fuelTransaction, FuelTransactionsRepository $fuelTransactionsRepository): Response
    {
        $form = $this->createForm(FuelTransactionsType::class, $fuelTransaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fuelTransactionsRepository->save($fuelTransaction, true);

            return $this->redirectToRoute('app_fuel_transactions_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('fuel_transactions/edit.html.twig', [
            'fuel_transaction' => $fuelTransaction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_fuel_transactions_delete', methods: ['POST'])]
    public function delete(Request $request, FuelTransactions $fuelTransaction, FuelTransactionsRepository $fuelTransactionsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fuelTransaction->getId(), $request->request->get('_token'))) {
            $fuelTransactionsRepository->remove($fuelTransaction, true);
        }

        return $this->redirectToRoute('app_fuel_transactions_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/table', name: 'app_fuel_transactions_table', methods: ['GET', 'POST'])]
    public function table(Request $request, FuelTransactionsRepository $fuelTransactionsRepository): Response
    {  
        $datatableParameters = $request->query->all();
        $search =  $datatableParameters["search"];
        
    
        $start = $datatableParameters["start"];
        $length = $datatableParameters["length"];

        

        $data_table  = $fuelTransactionsRepository->findByDataTable(['page' => ($start/$length), 'pageSize' => $length, 'search' => $search['value']]);

    

        // Objeto requerido por Datatables

        $responseData = array(
           // "draw" => '',
            "recordsTotal" => $data_table['totalRecords'],
            "recordsFiltered" => $data_table['totalRecords'],
            "data" => $data_table['data']
        );
    

        return $this->json($responseData);
    }

    #[Route('/excel_f5', name: 'app_fuel_transactions_excel_f5')]
    public function importar5f(): Response
    {   
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        return $this->render('fuel_transactions/importar_f5.html.twig', [
        ]);
    }

    #[Route('/importar_excel_f5', name: 'app_fuel_transactions_importar_excel_f5')]
    public function importarExcelF5(Request $request): Response
    {   
       
       
        if ($request->getMethod() == "POST") {
            $file = $request->files->has('archivo_excel') ? $request->files->get('archivo_excel') : null;


            if (!$file) {
                $errors[] = 'Missing File';
               
            }
            
        
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            $reader->setDelimiter(',');
            $reader->setEnclosure('');

            // Need this otherwise dates and such are returned formatted
            /** @noinspection PhpUndefinedMethodInspection */
            $reader->setReadDataOnly(true);
    
            // Just grab all the rows
            $wb = $reader->load($file->getRealPath());
            $ws = $wb->getSheet(0);
            $rows = $ws->toArray();          
            $i=0;
            $result = array();
            foreach($rows as $row) {

                if($i!=0){
                    $_log = $this->processEnvioExcelRowF5($row);


                    if ($_log) {
                        $result[] = $_log;
                    }
                    
                }
                $i++;
                // this is where you do your database stuff
               
            }
            $this->addFlash(
                'success',
                'Archivo de excel subido y procesado satisfactoriamente'
            );


        }else{
            $this->addFlash(
                'error',
                'El archivo de excel no pudo ser subido/procesado, por favor intente nuevamente'
            );
        }
        $this->addFlash(
            'result',
             $result
        );
        return $this->redirectToRoute('app_fuel_transactions_excel_f5', [], Response::HTTP_SEE_OTHER);

    }

      /**
     * Procesa cada fila del archivo excel subido.
     * 
     * @param Array $excel_row
     * @return boolean
     */
    private function processEnvioExcelRowF5($excel_row) {

        /* @var $grupoAutorizacion GrupoAutorizacion */
        $_log = array(
            "status" => "success",
            "messages" => array(),
            "data" => array()
        );
      

         
        $_rowData = array(
            "yard_name" => $excel_row[1],
            "date" => $excel_row[2],
            "paid_amount" =>  $excel_row[6],
            "code" =>  $excel_row[0],
            "settlement_id" =>  $excel_row[8]
        );

       

        $_log["excel_data"] = $_rowData;

        // Si todos los elementos de la fila estan vacíos, saltelo ...
        if (!array_filter($excel_row)) {
            return false;
        }

        if(!$_rowData['paid_amount']){
            return false;
        }
      
        // 1. Validadores previos a inserción de datos
       
            $fuelTransaction = $this->manager->getRepository(FuelTransactions::class)->findOneBy(['code'=> $_rowData["code"]]);

           if(!$fuelTransaction){
            $fuelTransaction = new FuelTransactions();
            }
            $fecha = new DateTime($_rowData["date"]);
            $fuelTransaction->setYardName($_rowData['yard_name']);
            $fuelTransaction->setDate($fecha);
            $fuelTransaction->setPaidAmount($_rowData['paid_amount']);
            $fuelTransaction->setCode($_rowData['code']);
            $fuelTransaction->setSettlementId($_rowData['settlement_id']);
            

            $company= $this->manager->getRepository(Company::class)->find(1);
            $fuelTransaction->setCompany($company);

            $this->manager->persist( $fuelTransaction);
            $this->manager->flush();
          
        
        
        return $_log;
    }
}
