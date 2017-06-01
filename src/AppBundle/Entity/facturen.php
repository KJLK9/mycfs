<?php
/**
 * Created by PhpStorm.
 * User: KJ
 * Date: 24-4-2017
 * Time: 21:10
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity
 * @ORM\Table(name="facturen")
 */
class facturen
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="fid")
     * @orm\GeneratedValue(strategy="AUTO")
     */
    private $fID;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\prijzen")
     */
    private $prijzen;
    /**
     * @ORM\Column(type="integer")
     */
    private $tijd;
    /**
     * @ORM\Column(type="text")
     */
    private $beschrijving;
    /**
     * @ORM\Column(type="string")
     */
    private $datum;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\betaal")
     */
    private $betaal;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Person")
     * @ORM\joinColumn(name="id")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\patienten")
     */
    private $patpolis;

    /**
     * facturen constructor.
     * @param $prijzen
     * @param $tijd
     * @param $beschrijving
     * @param $datum
     * @param $betaal
     * @param $id
     * @param $patpolis
     */
    public function __construct($prijzen, $tijd, $beschrijving, $datum, $betaal, $id, $patpolis)
    {
        $this->prijzen = $prijzen;
        $this->tijd = $tijd;
        $this->beschrijving = $beschrijving;
        $this->datum = $datum;
        $this->betaal = $betaal;
        $this->id = $id;
        $this->patpolis = $patpolis;
    }


    /**
     * @return mixed
     */
    public function getFID()
    {
        return $this->fID;
    }

    /**
     * @param mixed $fID
     */
    public function setID($fID)
    {
        $this->fID = $fID;
    }

    /**
     * @return prijzen
     */
    public function getPrijzen()
    {
        return $this->prijzen;
    }

    /**
     * @param mixed prijzen $prijzen
     */
    public function setPrijzen(prijzen $prijzen)
    {
        $this->prijzen = $prijzen;
    }

    /**
     * @return mixed
     */
    public function getTijd()
    {
        return $this->tijd;
    }

    /**
     * @param mixed $tijd
     */
    public function setTijd($tijd)
    {
        $this->tijd = $tijd;
    }

    /**
     * @return mixed
     */
    public function getBeschrijving()
    {
        return $this->beschrijving;
    }

    /**
     * @param mixed $beschrijving
     */
    public function setBeschrijving($beschrijving)
    {
        $this->beschrijving = $beschrijving;
    }

    /**
     * @return mixed
     */
    public function getDatum()
    {
        return $this->datum;
    }

    /**
     * @param mixed $datum
     */
    public function setDatum($datum)
    {
        $this->datum = $datum;
    }

    /**
     * @return betaal
     */
    public function getBetaal()
    {
        return $this->betaal;
    }

    /**
     * @param mixed betaal $betaal
     */
    public function setBetaal(betaal $betaal)
    {
        $this->betaal = $betaal;
    }

    /**
     * @return Person
     */
    public function getPersId()
    {
        return $this->ID;
    }

    /**
     * @param mixed Person $id
     */
    public function setPersId(Person $id)
    {
        $this->ID = $id;
    }

    /**
     * @return patienten
     */
    public function getPatpolis()
    {
        return $this->patpolis;
    }

    /**
     * @param mixed patienten $patpolis
     */
    public function setPatpolis(patienten $patpolis)
    {
        $this->patpolis = $patpolis;
    }

}