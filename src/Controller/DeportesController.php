<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeportesController {
  
    /**
     * @Route("/deportes")
     */
    public function inicio() {
        return new Response('Mi página de deportes!');
    }

    /**
     * @Route("/deportes/{slug}")
     */
    public function mostrar($slug) {
        return new Response(sprintf('Mi artículo en mi pagina de deportes: ruta %s', 
                                     $slug));
    }
    
    /**
     * @Route("/deportes/{seccion}/{pagina}", name="lista_paginas", 
     *        requirements={"pagina"="\d+"},
     *        defaults={"seccion":"tenis"})
     */
    public function lista($seccion, $pagina=1) {
        return new Response(sprintf('Deportes seccion: seccion %s, 
                                     listado de noticias página %s', 
                                     $seccion, $pagina));
    }
    
    /**
     * @Route("/deportes/{seccion}/{slug} ",
     * defaults={"seccion":"tenis"})
     */
    public function noticia($slug, $seccion) {
        return new Response(sprintf('Noticia de %s, con url dinámica=%s',
                                    $seccion, $slug));
    }

     /**
     * @Route("/deportes/{_idioma}/{fecha}/{seccion}/{equipo}/{pagina}",
     *        defaults={"slug": "1","_formato":"html","pagina":"1"},
     *        requirements={"_idioma": "es|en","_formato": "html|json|xml",
     *                      "fecha": "[\d+]{8}","pagina"="\d+"})
     */
    public function rutaAvanzadaListado($_idioma, $fecha, $seccion, $equipo, $pagina) {
        return new Response(sprintf('Listado de noticias en idioma=%s,
                                     fehca=%s,deporte=%s,equipo=%s, página=%s ', 
                                     $_idioma, $fecha, $seccion, $equipo, $pagina));
    }
    
    /**
     * @Route("/deportes/{_idioma}/{fecha}/{seccion}/{equipo}/{slug}.{_formato}",
     *        defaults={"slug": "1","_formato":"html"},
     *        requirements={"_idioma": "es|en","_formato": "html|json|xml",
     *                      "fecha": "[\d+]{8}"})
     */
    public function rutaAvanzada($_idioma, $fecha, $seccion, $equipo, $slug) {
        return new Response(sprintf( 'Mi noticia en idioma=%s,
                                     fehca=%s,deporte=%s,equipo=%s, noticia=%s ', 
                                     $_idioma, $fecha, $seccion, $equipo, $slug));
    }
    
   

}
