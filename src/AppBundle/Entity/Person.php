<?php
/**
 * Created by PhpStorm.
 * User: KJ
 * Date: 21-4-2017
 * Time: 16:35
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity
 * @ORM\Table(name="person")
 */
class Person extends User
{

    /**
     * @ORM\Id @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;



}