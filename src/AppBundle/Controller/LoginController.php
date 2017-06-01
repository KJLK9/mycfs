<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Person;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Form;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function Login(Request $request){

        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->add('email', EmailType::class, array('required' => true))
            ->add('password', PasswordType::class, array('required' => true))
            ->add('Login', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();


            $userManager = $this->container->get('fos_user.user_manager');
            $person = $userManager->findUserByEmail($data['email']);
            if($person){
                $hash = $person->getPassword();
                if(password_verify($data['password'], $hash)){
                    $_SESSION['id'] = $person->getId();
                    $_SESSION['name'] = $person->getUsername();
                    $roling = $person->getRoles();
                    $_SESSION['rechten'] = $roling;
                    return $this->render('menu.html.twig', [
                        'session' => $_SESSION
                    ]);
                }else{
                    return $this->render('login.html.twig', [
                        'form' => $form->createView(),
                        'fout' => 'Wachtwoord is incorrect.',
                        'title' => 'inloggen'
                    ]);
                }
            }else{
                return $this->render('login.html.twig', [
                    'form' => $form->createView(),
                    'fout' => 'Gebruiker niet gevonden.',
                    'title' => 'inloggen'
                ]);
            }
        }
        return $this->render('login.html.twig', [
            'form' => $form->createView(),
            'title' => 'inloggen',
        ]);
    }
    /**
     * @Route("/menu", name="menu")
     */
    public function menu(){
        if(!isset($_SESSION['id'])){
            $url = $this->generateUrl('home');
            return $this->redirect($url);
        }
        $userManager = $this->container->get('fos_user.user_manager');
        $person = $userManager->findUserBy(array('id'=>$_SESSION['id']));

        $rechten = $person->getRoles();
        $name = $person->getUserName();
        return $this->render('menu.html.twig', [
            'name' => $name,
            'rechten' => $rechten,
            'session' => $_SESSION
        ]);
    }
    /**
     * @Route("/logout", name="logout")
     */
    public function logout(){
        if(!isset($_SESSION)){
            session_start();
        }
        session_destroy();
        $url = $this->generateUrl('home');
        return $this->redirect($url);
    }
}
