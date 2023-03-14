<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\FuelTransactions;
use App\Entity\LoadDeductions;
use App\Entity\Loads;
use App\Entity\User;
use App\Entity\UserCompany;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\PdfPersonalisado;
use DateTime;

class SettlementController extends AbstractController
{
    protected $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/settlement', name: 'app_settlement')]
    public function index(): Response
    {

        $companys = $this->manager->getRepository(Company::class)->findAll();
        return $this->render('settlement/index.html.twig', [
            'companys' => $companys,
        ]);
    }

    #[Route('/settlement_pdf', name: 'app_settlement_pdf')]
    public function pdf(Request $request, EntityManagerInterface $entityManager)
    {
        $id_company = $request->request->get('company');
        $fecha_inicio = $request->request->get('fecha_inicio');
        $fecha_fin = $request->request->get('fecha_fin');
        $usuario =   $request->request->get('user');
        $new_descuento = $request->request->get('new_deductions');
        $name = $request->request->get('deductions_name');
        $value = $request->request->get('deductions_value');

        switch ($id_company) {
            case 1:
                $this->processPdfF5($fecha_inicio, $fecha_fin, $usuario, $new_descuento, $name, $value);
                break;
            case 2:
                $this->processPdfPlg($fecha_inicio, $fecha_fin, $usuario, $new_descuento, $name, $value);
                break;
            case 3:
                echo "i es igual a 2";
                break;
        }
    }

    /**
     * Procesa pdf nomina 5F.
     * 
     * @param Date Fecha_inicio
     * @param Date  Fecha_ Fin
     * @param Company company
     * @return boolean
     */
    private function processPdfF5($fecha_inicio, $fecha_fin, $usuario_id, $new_descuento, $name, $value)
    {

        $pdf = new PdfPersonalisado('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Jonathan Cruz');
        $pdf->SetTitle('Impresion Settlement');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        // set default header data
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
        $pdf->setFooterData(array(0, 64, 0), array(0, 0, 128));

        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(3, 10, 2);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);



        // ---------------------------------------------------------

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->SetFont('Helvetica', '', 7, '', true);

        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->SetPrintHeader(false);



        $pdf->AddPage();

        $pdf->setCufe();


        // set text shadow effect
        $pdf->setTextShadow(array('enabled' => false, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

        //

        $usuario = $this->manager->getRepository(User::class)->find($usuario_id);

        $fecha_modificada = new DateTime($fecha_fin);
        $fecha_modificada->modify('+1 day');

        $html = "";

        $loads = $this->manager->getRepository(Loads::class)
            ->createQueryBuilder('l')
            ->where('l.driver_name = :name')
            ->setParameter('name', $usuario->getDriverName())
            ->andWhere('l.arrived_at_loader >= :fecha_in')
            ->setParameter('fecha_in', $fecha_inicio)
            ->andWhere('l.arrived_at_loader <= :fecha_fin')
            ->setParameter('fecha_fin', $fecha_modificada->format('Y-m-d'))
            ->andWhere('l.company = :company')
            ->setParameter('company', 1)
            ->getQuery()->getResult();

        $arrayIdLoads = array();



        foreach ($loads as $load) {
            array_push($arrayIdLoads, $load->getId());
        }

        $loadsDeductions = $this->manager->getRepository(LoadDeductions::class)
            ->createQueryBuilder('ld')
            ->where('ld.loads IN (:loads)')
            ->setParameter('loads',$arrayIdLoads)
            ->getQuery()->getResult();
            
         $total_deduction = 0;
        foreach($loadsDeductions as $deduction){
            $total_deduction = $total_deduction + $deduction->getAmount();
        }

        $fuels = $this->manager->getRepository(FuelTransactions::class)
            ->createQueryBuilder('ft')
            ->where('ft.yard_name = :name')
            ->setParameter('name', $usuario->getDriverName())
            ->andWhere('ft.date >= :fecha_in')
            ->setParameter('fecha_in', $fecha_inicio)
            ->andWhere('ft.date <= :fecha_fin')
            ->setParameter('fecha_fin', $fecha_fin)
            ->andWhere('ft.company = :company')
            ->setParameter('company', 1)
            ->getQuery()->getResult();





        $descuentos = $this->manager->getRepository(UserCompany::class)->createQueryBuilder('uc')
            ->where('uc.user = :user')
            ->setParameter('user', $usuario->getId())
            ->andWhere('uc.company = :company')
            ->setParameter('company', 1)
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();

        /*if($request->query->get('html')){
        
                    return $this->render('impresion/stiker.html.twig', [
                        'unidad'  => $unidad,
                        'remision' => $unidad->getEnvioNacional(),
                        'base_64'  => $base_64,
                        'key' => $found_key+1,
                    ]); 
                }*/

        $html .= $this->renderView('print/settlement.html.twig', [
            'loads'  => $loads,
            'driver' => $usuario,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'fuels' => $fuels,
            'discount' => $descuentos,
            'new_deductions' => $new_descuento,
            'name' => $name,
            'value' => $value,
            'total_deduction' => $total_deduction
        ]);




        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // ---------------------------------------------------------

        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $pdf->Output('settlement.pdf', 'I');
    }

     /**
     * Procesa pdf nomina 5F.
     * 
     * @param Date Fecha_inicio
     * @param Date  Fecha_ Fin
     * @param Company company
     * @return boolean
     */
    private function processPdfPlg($fecha_inicio, $fecha_fin, $usuario_id, $new_descuento, $name, $value)
    {

        $pdf = new PdfPersonalisado('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Jonathan Cruz');
        $pdf->SetTitle('Impresion Settlement');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        // set default header data
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
        $pdf->setFooterData(array(0, 64, 0), array(0, 0, 128));

        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(3, 10, 2);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);



        // ---------------------------------------------------------

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->SetFont('Helvetica', '', 7, '', true);

        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->SetPrintHeader(false);



        $pdf->AddPage();

        $pdf->setCufe();


        // set text shadow effect
        $pdf->setTextShadow(array('enabled' => false, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

        //

        $usuario = $this->manager->getRepository(User::class)->find($usuario_id);

        $fecha_modificada = new DateTime($fecha_fin);
        $fecha_modificada->modify('+1 day');

        $html = "";

        $loads = $this->manager->getRepository(Loads::class)
            ->createQueryBuilder('l')
            ->where('l.driver_name = :name')
            ->setParameter('name', $usuario->getDriverName())
            ->andWhere('l.arrived_at_loader >= :fecha_in')
            ->setParameter('fecha_in', $fecha_inicio)
            ->andWhere('l.arrived_at_loader <= :fecha_fin')
            ->setParameter('fecha_fin', $fecha_modificada->format('Y-m-d'))
            ->andWhere('l.company = :company')
            ->setParameter('company', 2)
            ->getQuery()->getResult();

        $arrayIdLoads = array();



        foreach ($loads as $load) {
            array_push($arrayIdLoads, $load->getId());
        }

        $loadsDeductions = $this->manager->getRepository(LoadDeductions::class)
            ->createQueryBuilder('ld')
            ->where('ld.loads IN (:loads)')
            ->setParameter('loads',$arrayIdLoads)
            ->getQuery()->getResult();
            
         $total_deduction = 0;
        foreach($loadsDeductions as $deduction){
            $total_deduction = $total_deduction + $deduction->getAmount();
        }

        $fuels = $this->manager->getRepository(FuelTransactions::class)
            ->createQueryBuilder('ft')
            ->where('ft.yard_name = :name')
            ->setParameter('name', $usuario->getDriverName())
            ->andWhere('ft.date >= :fecha_in')
            ->setParameter('fecha_in', $fecha_inicio)
            ->andWhere('ft.date <= :fecha_fin')
            ->setParameter('fecha_fin', $fecha_fin)
            ->andWhere('ft.company = :company')
            ->setParameter('company', 1)
            ->getQuery()->getResult();





        $descuentos = $this->manager->getRepository(UserCompany::class)->createQueryBuilder('uc')
            ->where('uc.user = :user')
            ->setParameter('user', $usuario->getId())
            ->andWhere('uc.company = :company')
            ->setParameter('company', 1)
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();

        /*if($request->query->get('html')){
        
                    return $this->render('impresion/stiker.html.twig', [
                        'unidad'  => $unidad,
                        'remision' => $unidad->getEnvioNacional(),
                        'base_64'  => $base_64,
                        'key' => $found_key+1,
                    ]); 
                }*/

        $html .= $this->renderView('print/settlement_plg.html.twig', [
            'loads'  => $loads,
            'driver' => $usuario,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'fuels' => $fuels,
            'discount' => $descuentos,
            'new_deductions' => $new_descuento,
            'name' => $name,
            'value' => $value,
            'total_deduction' => $total_deduction
        ]);




        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // ---------------------------------------------------------

        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $pdf->Output('settlement.pdf', 'I');
    }
}
