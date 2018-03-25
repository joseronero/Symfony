<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NoticiaRepository")
 */
class Noticia
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=50)
     */
    private $seccion;
    
    
    /**
     * @ORM\Column(type="string", length=50)
     */
    private $equipo;
    
    /**
     * @ORM\Column(type="string", length=8)
     */
    private $fecha;
    
    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $textoNoticia;
    
    /**
     * @ORM\Column(type="string", length=200)
     */
    private $textoTitular;
    
    /**
     *@ORM\Column(type="string", length=50)
     */
    private $imagen;


    /**
     * 
     * @return type
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * 
     * @return type
     */
    function getSeccion() {
        return $this->seccion;
    }
    
    /**
     * 
     * @return type
     */
    function getEquipo() {
        return $this->equipo;
    }
    
    /**
     * 
     * @return type
     */
    function getFecha() {
        return $this->fecha;
    }
       
    /**
     * 
     * @param type $seccion
     */
    function setSeccion($seccion) {
        $this->seccion = $seccion;
    }

    /**
     * 
     * @param type $equipo
     */
    function setEquipo($equipo) {
        $this->equipo = $equipo;
    }

    /**
     * 
     * @param type $fecha
     */
    function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    /**
     * 
     * @return type
     */
    function getTextoNoticia() {
        return $this->textoNoticia;
    }
    
    /**
     * 
     * @return type
     */
    function getTextoTitular() {
        return $this->textoTitular;
    }

    /**
     * 
     * @return type
     */
    function getImagen() {
        return $this->imagen;
    }

    /**
     * 
     * @param type $textoNoticia
     */
    function setTextoNoticia($textoNoticia) {
        $this->textoNoticia = $textoNoticia;
    }

    /**
     * 
     * @param type $textoTitular
     */
    function setTextoTitular($textoTitular) {
        $this->textoTitular = $textoTitular;
    }

    /**
     * 
     * @param type $imagen
     */
    function setImagen($imagen) {
        $this->imagen = $imagen;
    }

}
