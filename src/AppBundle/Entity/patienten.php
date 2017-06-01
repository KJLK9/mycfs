<?php
/**
 * Created by PhpStorm.
 * User: KJ
 * Date: 24-4-2017
 * Time: 22:15
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity
 * @ORM\Table(name="patienten")
 */

class patienten
{
    /**
    * @ORM\Column(type="integer", name="patid")
    * @orm\GeneratedValue(strategy="AUTO")
    */
    private $ID;
    /**
     * @ORM\Column(type="string")
     */
    private $naam;
    /**
     * @ORM\Column(type="string")
     */
    private $adres;
    /**
     * @ORM\Column(type="string")
     */
    private $plaats;
    /**
     * @ORM\Column(type="string")
     */
    private $bloedgroep;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Person")
     * @ORM\Column(type="string", name="pnaam")
     */
    private $verzekering;
    /**
     * @ORM\OneToMany(targetEntity="facturen", mappedBy="patienten")
     * @ORM\Id @ORM\Column(type="integer", name="id")
     */
    private $polisnr;

    /**
     * @ORM\Column(type="string", name="status")
     */
    private $status;

    /**
     * patienten constructor.
     * @param $ID
     * @param $naam
     * @param $adres
     * @param $plaats
     * @param $bloedgroep
     * @param $verzekering
     * @param $polisnr
     * @param $status
     */
    public function __construct($ID, $naam, $adres, $plaats, $bloedgroep, $verzekering, $polisnr)
    {
        $this->ID = $ID;
        $this->naam = $naam;
        $this->adres = $adres;
        $this->plaats = $plaats;
        $this->bloedgroep = $bloedgroep;
        $this->verzekering = $verzekering;
        $this->polisnr = $polisnr;
        $this->status = 1;
    }


    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->ID;
    }

    /**
     * @param mixed $ID
     */
    public function setID($ID)
    {
        $this->ID = $ID;
    }

    /**
     * @return mixed
     */
    public function getNaam()
    {
        return $this->naam;
    }

    /**
     * @param mixed $naam
     */
    public function setNaam($naam)
    {
        $this->naam = $naam;
    }

    /**
     * @return mixed
     */
    public function getAdres()
    {
        return $this->adres;
    }

    /**
     * @param mixed $adres
     */
    public function setAdres($adres)
    {
        $this->adres = $adres;
    }

    /**
     * @return mixed
     */
    public function getPlaats()
    {
        return $this->plaats;
    }

    /**
     * @param mixed $plaats
     */
    public function setPlaats($plaats)
    {
        $this->plaats = $plaats;
    }

    /**
     * @return mixed
     */
    public function getBloedgroep()
    {
        return $this->bloedgroep;
    }

    /**
     * @param mixed $bloedgroep
     */
    public function setBloedgroep($bloedgroep)
    {
        $this->bloedgroep = $bloedgroep;
    }

    /**
     * @return Person
     */
    public function getVerzekering()
    {
        return $this->verzekering;
    }

    /**
     * @param mixed Person $verzekering
     */
    public function setVerzekering(Person $verzekering)
    {
        $this->verzekering = $verzekering;
    }

    /**
     * @return mixed
     */
    public function getPolisnr()
    {
        return $this->polisnr;
    }

    /**
     * @param mixed $polisnr
     */
    public function setPolisnr($polisnr)
    {
        $this->polisnr = $polisnr;
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