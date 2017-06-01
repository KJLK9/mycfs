<?php
/**
 * Created by PhpStorm.
 * User: KJ
 * Date: 24-4-2017
 * Time: 21:26
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity
 * @ORM\Table(name="prijzen")
 */
class prijzen
{

    /**
     * @ORM\Id
     * @ORM\Column(type="string", name="id")
     */
    private $soort;

    /**
     * @return mixed
     */
    public function getSoort()
    {
        return $this->soort;
    }

    /**
     * @param mixed $soort
     */
    public function setSoort($soort)
    {
        $this->soort = $soort;
    }


    /**
    * @ORM\Column(type="float", name="soort")
    */
    private $prijs;

    /**
     * prijzen constructor.
     * @param $soort
     * @param $prijs
     */
    public function __construct($soort, $prijs)
    {
        $this->soort = $soort;
        $this->prijs = $prijs;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->soort;
    }

    /**
     * @param mixed $soort
     */
    public function setId($soort)
    {
        $this->soort = $soort;
    }

    /**
     * @return mixed
     */
    public function getPrijs()
    {
        return $this->prijs;
    }

    /**
     * @param mixed $prijs
     */
    public function setPrijs($prijs)
    {
        $this->prijs = $prijs;
    }


}