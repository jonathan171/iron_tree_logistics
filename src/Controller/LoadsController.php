<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Loads;
use App\Entity\Trier;
use App\Form\LoadsType;
use App\Repository\LoadsRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/loads')]
class LoadsController extends AbstractController
{   
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }


    #[Route('/', name: 'app_loads_index', methods: ['GET'])]
    public function index(LoadsRepository $loadsRepository): Response
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        return $this->render('loads/index.html.twig', [
            'loads' => $loadsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_loads_new', methods: ['GET', 'POST'])]
    public function new(Request $request, LoadsRepository $loadsRepository): Response
    {
        $load = new Loads();
        $form = $this->createForm(LoadsType::class, $load);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $loadsRepository->save($load, true);

            return $this->redirectToRoute('app_loads_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('loads/new.html.twig', [
            'load' => $load,
            'form' => $form,
        ]);
    }

    #[Route('/table', name: 'app_loads_table', methods: ['GET', 'POST'])]
    public function table(Request $request, LoadsRepository $loadsRepository): Response
    {  
        $datatableParameters = $request->query->all();
        $search =  $datatableParameters["search"];
        
    
        $start = $datatableParameters["start"];
        $length = $datatableParameters["length"];

        

        $data_table  = $loadsRepository->findByDataTable(['page' => ($start/$length), 'pageSize' => $length, 'search' => $search['value']]);

    

        // Objeto requerido por Datatables

        $responseData = array(
           // "draw" => '',
            "recordsTotal" => $data_table['totalRecords'],
            "recordsFiltered" => $data_table['totalRecords'],
            "data" => $data_table['data']
        );
    

        return $this->json($responseData);
    }

    #[Route('/{id}/show', name: 'app_loads_show', methods: ['GET'])]
    public function show(Loads $load): Response
    {
        return $this->render('loads/show.html.twig', [
            'load' => $load,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_loads_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Loads $load, LoadsRepository $loadsRepository): Response
    {
        $form = $this->createForm(LoadsType::class, $load);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $loadsRepository->save($load, true);

            return $this->redirectToRoute('app_loads_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('loads/edit.html.twig', [
            'load' => $load,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_loads_delete', methods: ['POST'])]
    public function delete(Request $request, Loads $load, LoadsRepository $loadsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$load->getId(), $request->request->get('_token'))) {
            $loadsRepository->remove($load, true);
        }

        return $this->redirectToRoute('app_loads_index', [], Response::HTTP_SEE_OTHER);
    }
     
    #[Route('/excel_f5', name: 'app_loads_excel_f5')]
    public function importarups(): Response
    {   
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        return $this->render('loads/importar_f5.html.twig', [
        ]);
    }

    #[Route('/importar_excel_f5', name: 'app_loads_importar_excel_f5')]
    public function importarExcelF5(Request $request): Response
    {   
       
       
        if ($request->getMethod() == "POST") {
            $file = $request->files->has('archivo_excel') ? $request->files->get('archivo_excel') : null;


            if (!$file) {
                $errors[] = 'Missing File';
               
            }
            
        
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getRealPath());

            // Need this otherwise dates and such are returned formatted
            /** @noinspection PhpUndefinedMethodInspection */
            $reader->setReadDataOnly(true);
    
            // Just grab all the rows
            $wb = $reader->load($file->getRealPath());
            $ws = $wb->getSheet(1);
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
        return $this->redirectToRoute('app_loads_excel_f5', [], Response::HTTP_SEE_OTHER);

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
            "fuel_surcharge" => $excel_row[4],
            "well_name" => $excel_row[6],
            "code" =>  $excel_row[9],
            "dispatched_loader" =>  $excel_row[11],
            "driver_name" =>  $excel_row[22],
            "arrived_at_loader" =>  $excel_row[29],
            "loaded_distance" =>  $excel_row[41],
            "line_haul" =>  $excel_row[47],
            "order_status" =>  $excel_row[50],
            "billing_status" =>  $excel_row[53]
        );

       

        $_log["excel_data"] = $_rowData;

        // Si todos los elementos de la fila estan vacíos, saltelo ...
        if (!array_filter($excel_row)) {
            return false;
        }
      
        // 1. Validadores previos a inserción de datos
        if ($_rowData["billing_status"] == 'Invoice Approved') {
            $load = $this->manager->getRepository(Loads::class)->findOneBy(['code'=> $_rowData["code"]]);

           if(!$load){
                $load = new Loads();
            }
            $fecha = new DateTime($_rowData["arrived_at_loader"]);
            $load->setFuelSurcharge($_rowData["fuel_surcharge"]);
            $load->setWellName($_rowData["well_name"]);
            $load->setCode($_rowData["code"]);
            $load->setDispatchedLoader($_rowData["dispatched_loader"]);
            $load->setDriverName($_rowData["driver_name"]);
            $load->setArrivedAtLoader($fecha);
            $load->setLoadedDistance($_rowData["loaded_distance"]);
            $load->setLineHaul($_rowData["line_haul"]);
            $load->setOrderStatus($_rowData["order_status"]);
            $load->setBillingStatus($_rowData["billing_status"]);

            $company= $this->manager->getRepository(Company::class)->find(1);
            $load->setCompany($company);

            $this->manager->persist( $load);
            $this->manager->flush();
          
        }else{
            $_log["status"] = "warning";
            $_log["messages"][] = " no se pudo subir  esta carga por que la factura no ha sido aprobada";
            return $_log;
        }
        
        return $_log;
    }

    #[Route('/save_tier', name: 'app_loads_save_tier', methods: ['GET', 'POST'])]
    public function executeSaveTier(
        Request $request,
        EntityManagerInterface $entityManager
    ) {

        $load = $this->manager->getRepository(Loads::class)->find($request->request->get('id'));
        $tier = $this->manager->getRepository(Trier::class)->find($request->request->get('tier_id'));

        $load->setTrier($tier);

        $this->manager->persist( $load);
        $this->manager->flush();


        $responseData = array(
            "results" => true,
        );
        return $this->json($responseData);
    }
}
