<?php
/**
 * Created by PhpStorm.
 * User: KJ
 * Date: 24-4-2017
 * Time: 21:09
 */

namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity
 * @ORM\Table(name="betaal")
 */
class betaal
{


    /**
     * @ORM\Id @ORM\Column(type="string", name="id")
     */
    private $status;

    /**
     * betaal constructor.
     * @param $status
     */
    public function __construct($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }



}