<?php
/**
 * Created by PhpStorm.
 * User: KJ
 * Date: 25-4-2017
 * Time: 15:20
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity
 * @ORM\Table(name="rechten")
 */
class rechten
{
    /**
    * @ORM\Id @ORM\Column(type="string", name="id")
    */
    public $rechten;

    /**
     * rechten constructor.
     * @param $rechten
     */
    public function __construct($rechten)
    {
        $this->rechten = $rechten;
    }

    /**
     * @return mixed
     */
    public function getRechten()
    {
        return $this->rechten;
    }

    /**
     * @param mixed $rechten
     */
    public function setRechten($rechten)
    {
        $this->rechten = $rechten;
    }
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->rechten;
    }
}