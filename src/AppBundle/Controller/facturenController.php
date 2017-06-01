<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\facturen;
use FPDF;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Query\Expr\Join;


class facturenController extends Controller
{
    /**
     * @route("/facturen/betalen/{id}")
     */
    public function del($id){
        if(!isset($_SESSION['id'])){
            $url = $this->generateUrl('home');
            return $this->redirect($url);
        }
        $em = $this->getDoctrine()->getManager();
        $factuur = $em->getRepository('AppBundle:facturen')->find($id);

        $factuur->setBetaal(1);

        $em->persist($factuur);
        $em->flush();

        $url = $this->generateUrl('pay');
        return $this->redirect($url);
    }
    /**
     * @route("/facturen/betalen", name="pay")
     */
    public function betalen(){
        if(!isset($_SESSION['id'])){
            $url = $this->generateUrl('home');
            return $this->redirect($url);
        }
        $em = $this->getDoctrine()->getManager();
        $allepats = $em->getRepository('AppBundle:patienten')->findAll();
        if(isset($_POST['pat'])){
            $gebruikere = $_POST['pat'];
            $facs = $em->getRepository('AppBundle:facturen');
            $facturen = $facs->createQueryBuilder('z')
                ->where('z.patpolis = :name')
                ->andWhere('z.betaal = 0')
                ->setParameter('name', $gebruikere)
                ->getQuery()
                ->getResult();
            return $this->render('pay.html.twig', [
                'session' => $_SESSION,
                'facturen' => $facturen
            ]);

        }else{
            return $this->render('pay.html.twig', [
                'session' => $_SESSION,
                'patienten' => $allepats
            ]);
        }
    }
    /**
     * @route("/facturen/toevoegen")
     */
    public function particular(Request $request){
        if(!isset($_SESSION['id'])){
            $url = $this->generateUrl('home');
            return $this->redirect($url);
        }
        $em = $this->getDoctrine()->getManager();
        $prijzen = $em->getRepository('AppBundle:prijzen')->findAll();
        $patienten = $em->getRepository('AppBundle:patienten')->findAll();

        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->add('soort', ChoiceType::class, array('choices' => $prijzen, 'choice_label' => 'soort',))
            ->add('patient', ChoiceType::class, array('choices' => $patienten, 'choice_label' => 'ID' ,))
            ->add('tijd', NumberType::class, array('required' => true,))
            ->add('beschrijving', TextType::class, array('required' => true,))
            ->add('datum', DateType::class, array('required' => true,'widget' => 'single_text',))
            ->add('Submit', SubmitType::class)
            ->getForm();

        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $person = $em->getRepository('AppBundle:patienten')->find($id);

            $form = $this->createFormBuilder()
                ->setMethod('POST')
                ->add('soort', ChoiceType::class, array('choices' => $prijzen, 'choice_label' => 'soort',))
                ->add('patient', ChoiceType::class, array('choices' => $patienten, 'choice_label' => 'ID' , 'data' => $person->getPolisnr()))
                ->add('tijd', NumberType::class, array('required' => true,))
                ->add('beschrijving', TextType::class, array('required' => true,))
                ->add('datum', DateType::class, array('required' => true,'widget' => 'single_text',))
                ->add('Submit', SubmitType::class)
                ->getForm();

            return $this->render('add.html.twig', [
                'session' => $_SESSION,
                'form' => $form->CreateView()
            ]);
        }



        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data['soort'] = $em->getRepository('AppBundle:prijzen')->find($data['soort']);
            $data['betaal'] = $em->getRepository('AppBundle:betaal')->find(0);
            $data['arts'] = $em->getReference('AppBundle:Person', $_SESSION['id']);
            $data['patient'] = $em->getRepository('AppBundle:patienten')->find($data['patient']);

            $factuur = new facturen($data['soort'], $data['tijd'], $data['beschrijving'], $data['datum']->format('Y-m-d'), $data['betaal'],$data['arts'], $data['patient'] );

            $em->persist($factuur);
            $em->flush($factuur);

            $url = $this->generateUrl('viewpats');
            return $this->redirect($url);

        }

        return $this->render('add.html.twig', [
            'session' => $_SESSION,
            'form' => $form->CreateView()
        ]);

    }
    /**
     * @route("/facturen/download")
     */

    public function con(Request $request){
        if(!isset($_SESSION['id'])){
            $url = $this->generateUrl('home');
            return $this->redirect($url);
        }
        $em = $this->getDoctrine()->getManager();
        $personen = $em->getRepository('AppBundle:patienten')->findAll();


        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->add('patient', ChoiceType::class, array('choices' => $personen, 'choice_label' => 'ID',))
            ->add('Submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            define('EURO', chr(128));
            $gebruikere = $data['patient'];
            $docname = $gebruikere->getID();
            $facs = $em->getRepository('AppBundle:facturen');
            $facturen = $facs->createQueryBuilder('z')
                ->where('z.patpolis = :name')
                ->andWhere('z.betaal = 0')
                ->setParameter('name', $gebruikere)
                ->getQuery()
                ->getResult();

            $gebruiker = $em->getRepository('AppBundle:patienten')->find($gebruikere);




            $naam = ucwords($gebruiker->getNaam());
            $adres = ucfirst($gebruiker->getAdres());
            $plaats = ucwords($gebruiker->getPlaats());
            $verzekering = $gebruiker->getVerzekering();
            $polisnummer = $gebruiker->getID();
            $datum = date('d-m-Y');
            $factuurnummer = date('ymd').$polisnummer;


            //openstaande facturen
            $teller = 0;
            $kolom_datum ="";
            $kolom_omschrijving = "";
            $kolom_kosten = "";

            for($i = 0; $i < count($facturen); ++$i){
                $factuur = $facturen[$i];
                $soort = $factuur->getPrijzen();
                $kosten = "";
                $prijs = $em->getRepository('AppBundle:prijzen')->find($soort);
                $price = $prijs->getPrijs();
                $kosten = $price*$factuur->getTijd();
                $teller += $kosten;
                $vis_kosten = number_format($kosten,2,",",'.');

                $facdatum = $factuur->getDatum();
                $omschrijving = $factuur->getBeschrijving();


                $kolom_datum = $kolom_datum.$facdatum."\n";
                $kolom_omschrijving = $kolom_omschrijving.$omschrijving."\n";
                $kolom_kosten = $kolom_kosten.EURO.$vis_kosten."\n";

            }
            $incbtw = number_format((1.21*$teller),2,",",'.');
            $btw = number_format((0.21*$teller),2,",",'.');
            $kolom_datum = $kolom_datum."\n";
            $kolom_omschrijving = $kolom_omschrijving."21% BTW\n";
            $kolom_kosten = $kolom_kosten.EURO.$btw."\n";
            //nieuwe pdf aanmaken

            $pdf = new FPDF();
            $pdf -> AliasNbPages();
            $pdf -> AddPage();

            //header
            $pdf -> SetDrawColor(4,155,155);
            $pdf -> SetFont('Times', 'B', 16);
            $pdf -> SetTextColor(4,155,155);
            $pdf -> Cell(40,13,'MyCFS',7,46,12);
            $pdf -> Image('images/logo.png',155,7,46,35);
            $pdf -> SetTextColor(0,0,0);
            //klantgegevens

            $pdf -> SetFont('Times', '',12);
            $pdf ->SetY(20);
            $pdf -> Ln();
            $pdf -> Cell(40,5,"$naam");
            $pdf -> Ln();
            $pdf -> Cell(40,5,"$adres $plaats");
            $pdf -> Ln();
            $pdf -> Cell(40,5,"$verzekering $polisnummer");
            $pdf -> Ln();
            $pdf -> Cell(188,0,'',1,0,0,1);
            //factuur
            $pdf -> SetFont('Times', 'B', 12);
            $pdf -> SetY(65);
            $pdf -> Ln();
            $pdf -> SetX(75);
            $pdf -> SetFillColor(255,115,32);
            $pdf -> Cell(60,15,'FACTUUR',1,0,'C',1);
            $pdf -> SetY(80);
            $pdf -> SetFont('Times', '', 12);
            $pdf -> Ln();
            $pdf -> Cell(30,5,'factuurdatum:');
            $pdf -> SetFont('Times', 'B', 12);
            $pdf -> Ln();
            $pdf -> Cell(30,5,"$datum");
            $pdf -> SetFont('Times', '', 12);
            $pdf -> SetX(125);
            $pdf -> Cell(30,5,"Factuurnummer: ");
            $pdf -> SetFont('Times', 'B', 12);
            $pdf -> Cell(30,5,"$factuurnummer");
            $pdf -> SetFont('Times', '', 12);
            $pdf -> Ln();
            $pdf -> Cell(188,0,'',1,0,0,1);
            //veldnamen positie
            $Y_FieldsName_position = 115;
            $Y_Table_position = 122;

            //creëren van veldnamen
            $pdf -> SetY($Y_FieldsName_position);
            $pdf -> SetFillColor(5,155,155);
            $pdf -> SetFont('Times', 'B',12);
            $pdf -> Cell(45,6,'Datum',0,0,'C',1);
            $pdf -> Cell(100,6,'Omschrijving',0,0,'C',1);
            $pdf -> Cell(45,6,'Bedrag('.EURO.')',0,0,'R',1);
            $pdf -> Ln();



            //content
            $pdf -> SetFont('Times', '',12);
            $pdf -> SetY($Y_Table_position);
            $pdf -> MultiCell(45,6,$kolom_datum,'R','C');
            $pdf -> SetY($Y_Table_position);
            $pdf -> SetX(55);
            $pdf -> MultiCell(100,6,$kolom_omschrijving,0,'L');
            $pdf -> SetY($Y_Table_position);
            $pdf -> SetX(155);
            $pdf -> MultiCell(45,6,$kolom_kosten,'LB','R',0);
            $pdf -> Ln(1);
            $pdf -> SetFont('Times', 'B',12);
            $pdf -> SetFillColor(255,115,32);
            $pdf -> Cell(145,6,'Totaal door u te betalen inclusief BTW: ',0,0,'R',1);
            $pdf -> Cell(45,6,EURO.$incbtw,0,0,'R',1);

            $pdf -> SetFont('Times', '', 12);
            $pdf -> SetX(20);
            $pdf -> Cell(0,70,'Wij verzoeken vriendelijk genoemd bedrag binnen 5 werkdagen over te maken op ',0,0,'R');
            $pdf -> SetX(20);
            $pdf -> Cell(0,80,'REKENINGNUMMER | NL 84 SNSB 1324576809 ',0,0,'R');
            return new Response($pdf->Output($docname, "I"), 200, array(
                'Content-Type' => 'application/pdf'));


        }
        return $this->render('getdwnld.html.twig', [
            'session' => $_SESSION,
            'patienten' => $personen,
            'form' => $form->CreateView()
        ]);
    }
}
