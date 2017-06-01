<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AccountController extends Controller
{
    /**
     * @route("/account/overzicht", name="overzicht")
     */
    public function overzicht(){
        if(!isset($_SESSION['id'])){
            $url = $this->generateUrl('home');
            return $this->redirect($url);
        }
        $id = $_SESSION['id'];
        $userManager = $this->container->get('fos_user.user_manager');
        $person = $userManager->findUserBy(array('id'=>$id));


        return $this->render('account.html.twig', [
            'session' => $_SESSION,
            'person' => $person
        ]);
    }
    /**
     * @route("/account/wijzigen")
     */
    public function ww(Request $request){
        if(!isset($_SESSION['id'])){
            $url = $this->generateUrl('home');
            return $this->redirect($url);
        }
        $id = $_SESSION['id'];
        $userManager = $this->container->get('fos_user.user_manager');
        $person = $userManager->findUserBy(array('id'=>$id));

        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->add('email', EmailType::class, array('required' => false, 'data' => $person->getEmail()))
            ->add('password', PasswordType::class, array('required' => false))
            ->add('Submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if($data['email'] != ""){
                $person->setEmail($data['email']);
            }
            if($data['password'] != ""){
                $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
                $person->setPassword($data['password']);
            }
            $userManager->updateUser($person);
            $url = $this->generateUrl('overzicht');
            return $this->redirect($url);
        }

        return $this->render('wachtwoord.html.twig', [
            'session' => $_SESSION,
            'persoon' => $person,
            'form' => $form->CreateView(),
        ]);
    }
}
