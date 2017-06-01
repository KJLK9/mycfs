<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\prijzen;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class PrijzenController extends Controller
{
    /**
     * @route("/prijzen/bekijken", name="prices")
     */
    public function prijzen(){
        if(!isset($_SESSION['id'])){
            $url = $this->generateUrl('home');
            return $this->redirect($url);
        }
        $amnt = 100;
        $rep = $this->getDoctrine()->getRepository('AppBundle:prijzen');
        $prijzen = $rep->createQueryBuilder('u')
            ->setMaxResults($amnt)
            ->orderBy('u.prijs', 'ASC')
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
                $soort = $_GET['zoekwoord'];
                $prijzen = $rep->createQueryBuilder('z')
                    ->where('z.soort LIKE :soort')
                    ->setParameter('soort', '%' . $soort . '%')
                    ->setMaxResults($amnt)
                    ->setFirstResult($offset)
                    ->orderBy('z.prijs', 'ASC')
                    ->getQuery()
                    ->getResult();
            } else {
                $personen = $rep->createQueryBuilder('p')
                    ->setMaxResults($amnt)
                    ->setFirstResult($offset)
                    ->orderBy('p.prijs', 'ASC')
                    ->getQuery()
                    ->getResult();
            }
            $rawlp = count($prijzen);
            $lp = ceil($rawlp / $amnt);
            return $this->render('prijzen.html.twig', [
                'prijzen' => $prijzen,
                'curpage' => $ReqPage,
                'lastpage' => $lp,
                'GET' => $_GET,
                'session' => $_SESSION
            ]);
        }else{
            $rawlp = count($prijzen);
            $lp = ceil($rawlp / $amnt);
            return $this->render('prijzen.html.twig', [
                'prijzen' => $prijzen,
                'curpage' => $ReqPage,
                'lastpage' => $lp,
                'GET' => $_GET,
                'session' => $_SESSION
            ]);
        }
    }
    /**
     * @route("prijzen/edit/{id}")
     * @method('GET')
     */
    public function updateem($id, Request $request)
    {
        if(!isset($_SESSION['id'])){
            $url = $this->generateUrl('home');
            return $this->redirect($url);
        }
        $em = $this->getDoctrine()->getManager();
        $prijs = $em->getRepository('AppBundle:prijzen')->find($id);

        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->add('soort', TextType::class, array('required' => true, 'data' => $prijs->getSoort()))
            ->add('prijs', NumberType::class, array('required' => true, 'data' => $prijs->getPrijs()))
            ->add('Submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $prijs->SetSoort($data['soort']);
            $prijs->setPrijs($data['prijs']);
            $em->persist($prijs);
            $em->flush($prijs);

            $url = $this->generateUrl('prices');
            return $this->redirect($url);
        }

        return $this->render('edit.html.twig', [
            'prijs' => $prijs,
            'session' => $_SESSION,
            'form' => $form->CreateView(),
            'persoon' => $prijs
        ]);

    }
    /**
     * @route("/prijzen/toevoegen")
     */
    public function adding(Request $request)
    {
        if(!isset($_SESSION['id'])){
            $url = $this->generateUrl('home');
            return $this->redirect($url);
        }
        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->add('soort', TextType::class, array('required' => true))
            ->add('prijs', EmailType::class, array('required' => true))
            ->add('Submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $rubric = new prijzen($data['soort'], $data['prijs']);

            $em->persist($rubric);
            $em->flush($rubric);
            return $this->render('add.html.twig', [
                'actie' => 'succesvol',
                'session' => $_SESSION
            ]);
        }

        return $this->render('add.html.twig', [
            'session' => $_SESSION,
            'form' => $form->CreateView()
        ]);


    }
    /**
     * @route("/prijzen/delete/{id}")
     */
    public function del($id){
        if(!isset($_SESSION['id'])){
            $url = $this->generateUrl('home');
            return $this->redirect($url);
        }
        $em = $this->getDoctrine()->getManager();
        $rubric = $em->getRepository('AppBundle:prijzen')->find($id);

        $em->remove($rubric);
        $em->flush($rubric);

        $url = $this->generateUrl('prices');
        return $this->redirect($url);
    }
}
