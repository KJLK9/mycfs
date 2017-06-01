<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Person;
use FOS\UserBundle\Model\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class GebruikersController extends Controller
{
    /**
     * @route("/gebruikers/toevoegen", name="lalal")
     * @method('GET')
     */
    public function showAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if(!isset($_SESSION['id'])){
            $url = $this->generateUrl('home');
            return $this->redirect($url);
        }

        $rechten = $em->getRepository('AppBundle:rechten')->findAll();

        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->add('naam', TextType::class, array('required' => true))
            ->add('email', EmailType::class, array('required' => true))
            ->add('password', PasswordType::class, array('required' => true))
            ->add('rechten', ChoiceType::class, array('choices' => $rechten, 'choice_label' => 'rechten',))
            ->add('Submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

            $rechtje = [0 => 'ROLE_USER', 1 => 'ROLE_'.$data['rechten']];

            $userManager = $this->container->get('fos_user.user_manager');
            $person = $userManager->createUser();
            $person->setUserName($data['naam']);
            $person->setEmail($data['email']);
            $person->setPassword($data['password']);
            $person->setRoles($rechtje);

            $userManager->updateUser($person);

            $url = $this->generateUrl('lalal');
            return $this->redirect($url);
        }
        return $this->render('add.html.twig', [
            'session' => $_SESSION,
            'rechten' => $rechten,
            'form' => $form->CreateView(),
        ]);
    }

    /**
     * @route("/gebruikers/bekijken", name="viewem")
     * @method('GET')
     */
    public function showUsers()
    {
        $userManager = $this->container->get('fos_user.user_manager');
        if(!isset($_SESSION['id'])){
            $url = $this->generateUrl('home');
            return $this->redirect($url);
        }
        $amnt = 100;
        $rep = $this->getDoctrine()->getRepository('AppBundle:Person');
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
                    ->where('z.username LIKE :name')
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
            $lp = ceil($rawlp / $amnt);
            return $this->render('view.html.twig', [
                'personen' => $personen,
                'curpage' => $ReqPage,
                'lastpage' => $lp,
                'GET' => $_GET,
                'session' => $_SESSION
            ]);
        }else{
            $rawlp = count($personen);
            $lp = ceil($rawlp / $amnt);
            return $this->render('view.html.twig', [
                'personen' => $personen,
                'curpage' => $ReqPage,
                'lastpage' => $lp,
                'GET' => $_GET,
                'session' => $_SESSION
            ]);
        }
    }

    /**
     * @route("gebruikers/edit/{id}")
     * @method('GET')
     */
    public function updateAction($id, Request $request)
    {
        if(!isset($_SESSION['id'])){
            $url = $this->generateUrl('home');
            return $this->redirect($url);
        }
        $userManager = $this->container->get('fos_user.user_manager');

        $em = $this->getDoctrine()->getManager();
        $person = $userManager->findUserBy(array('id'=>$_SESSION['id']));
        $rechten = $em->getRepository('AppBundle:rechten')->findAll();

        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->add('naam', TextType::class, array('data' => $person->getUserName()))
            ->add('email', EmailType::class, array('data' => $person->getEmail()))
            ->add('password', PasswordType::class)
            ->add('rechten', ChoiceType::class, array('choices' => $rechten, 'choice_label' => 'rechten', 'data' => $person->getRoles()))
            ->add('Submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if($data['password'] != ""){
                $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
                $person->setPassword($data['password']);
            }
            $data['rechten'] = $em->getReference('AppBundle:rechten', $data['rechten']);
            $rechtje = [0 => 'ROLE_USER', 1 => 'ROLE_'.$data['rechten']];
            if($data['naam'] != "") {
                $person->setUserName($data['naam']);
            }
            if($data['email'] != ""){
                $person->setEmail($data['email']);
            }
            if($data['rechten'] != "") {
                $person->setRoles($rechtje);
            }
            $userManager->updateUser($person);

            $url = $this->generateUrl('viewem');
            return $this->redirect($url);
        }

        return $this->render('edit.html.twig', [
            'persoon' => $person,
            'session' => $_SESSION,
            'rechten' => $rechten,
            'form' => $form->CreateView()
        ]);

    }
}
