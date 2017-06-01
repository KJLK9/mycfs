<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\patienten;
use AppBundle\Entity\Person;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class PatientenController extends Controller
{
    /**
     * @route("/patienten/bekijken", name="viewpats")
     */
    public function pats(){
        if(!isset($_SESSION['id'])){
            $url = $this->generateUrl('home');
            return $this->redirect($url);
        }
        $amnt = 100;
        $rep = $this->getDoctrine()->getRepository('AppBundle:patienten');
        $personen = $rep->createQueryBuilder('u')
            ->setMaxResults($amnt)
            ->getQuery()
            ->getResult();
        $ReqPage = 1;

        if(isset($_GET['page']) || isset($_GET['zoekwoord'])) {
            $offset = "";
            if ($_GET['page'] == 0) {
                $offset = 0;
                $ReqPage = 1;
            } else {
                $ReqPage = $_GET['page'];
                $offset = ($ReqPage * $amnt) - $amnt;
            }
            if (isset($_GET['zoekwoord'])) {
                $name = $_GET['zoekwoord'];
                $personen = $rep->createQueryBuilder('z')
                    ->where('z.naam LIKE :name')
                    ->setParameter('name', '%' . $name . '%')
                    ->setMaxResults($amnt)
                    ->setFirstResult($offset)
                    ->getQuery()
                    ->getResult();
            } else {
                $personen = $rep->createQueryBuilder('p')
                    ->setMaxResults($amnt)
                    ->setFirstResult($offset)
                    ->getQuery()
                    ->getResult();
            }
            $rawlp = count($personen);
            $lp = $rawlp / $amnt;
            return $this->render('patienten.html.twig', [
                'patienten' => $personen,
                'curpage' => $ReqPage,
                'lastpage' => $lp,
                'GET' => $_GET,
                'session' => $_SESSION
            ]);
        }else{
            $rawlp = count($personen);
            $lp = ceil($rawlp / $amnt);
            return $this->render('patienten.html.twig', [
                'patienten' => $personen,
                'curpage' => $ReqPage,
                'lastpage' => $lp,
                'GET' => $_GET,
                'session' => $_SESSION
            ]);
        }
    }
    /**
     * @route("/patienten/toevoegen")
     */
    public function patsboem(Request $request){
        if(!isset($_SESSION['id'])){
            $url = $this->generateUrl('home');
            return $this->redirect($url);
        }
        $em = $this->getDoctrine()->getManager();
        $rep =  $em->getRepository('AppBundle:Person');
        $verzekeringen = $rep->createQueryBuilder('z')
            ->setParameter('ver', 'verzekering')
            ->where('z.rechten = :ver')
            ->getQuery()
            ->getResult();

        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->add('polisnummer', NumberType::class, array('required' => true))
            ->add('naam', TextType::class, array('required' => true))
            ->add('adres', TextType::class, array('required' => true))
            ->add('bloedgroep', ChoiceType::class, array('choices' => array('A+' => 'A+', 'A-' => 'A-', 'B+' => 'B+', 'B-' => 'B-', 'AB+' => 'AB+', 'AB-' => 'AB-', 'O+' => 'O+', 'O-' => 'O-')))
            ->add('verzekering', ChoiceType::class, array('choices' => $verzekeringen, 'choice_label' => 'naam',))
            ->add('Submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $ID = $data['polisnummer'];
            $patid = rand(0, 999999999);
            $naam = $data['naam'];
            $adres = $data['adres'];
            $plaats = $data['plaats'];
            $bloedgroep = $data['bloedgroep'];
            $pnaam = $data['verzekering'];
            /** @var Person $person */
            $person = $em->getReference('AppBundle:Person', $pnaam);
            /** @var patienten $patient */
            $patient =  new patienten($ID,$naam, $adres, $plaats, $bloedgroep,$person, $patid);


            $em->persist($patient);
            $em->flush();



            $url = $this->generateUrl('viewpats');
            return $this->redirect($url);
        }


        return $this->render('add.html.twig', [
            'session' => $_SESSION,
            'verzekeringen' => $verzekeringen,
            'form' => $form->CreateView()
        ]);
    }
}
