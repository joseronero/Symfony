<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\DBAL\Schema\Constraint;

use App\Entity\Noticia;
use App\Form\login;
use App\Entity\User;
use App\Form\Form_UserType;


class DeportesController extends Controller {
  
    /**
     * @Route("/deportes/inicio", name="inicio")
     */
    public function inicio() {
       $error='';
       return $this->redirectToRoute('login_seguro');
    }
    
    /**
     * @Route("/deportes/login", name="login_seguro" )
     */
    public function loginUsuario(Request $request, AuthenticationUtils $authUtils)
    {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('Security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error
        ));
    }
    
     /**
     * @Route("/deportes/new", name="nuevo_usuario")
     */
    public function newAction() {

        $oUusario = new User();
        $form = $this->createCreateForm($oUusario);
        $error ="";
        return $this->render('add.html.twig', array('form' => $form->createView(),'error'=> $error));
    }

    /**
     * @Route("/deportes/crear", name="crear_usuario")
     * 
     * @param \App\Controller\Request $request
     * @return User
     */
    public function crearAction(Request $request) {
        
        $oUsuario = new User();
        $form = $this->createCreateForm($oUsuario);
        $form->handleRequest($request);
        $error="";

        if ( $form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();    
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($oUsuario, $password);
            $oUsuario->setPassword($encoded);
            $em = $this->getDoctrine()->getManager();
            $em->persist($oUsuario);
            $em->flush();
            $SuccessMessage = 'El usuario ha sido creado correctamente';
            $request->getSession ()->getFlashBag ()->add ( 'mensaje' , $SuccessMessage );
            return $this->redirectToRoute('crear_usuario');          
        }
        return $this->render('add.html.twig', array('form' => $form->createView(), 'error'=> $error));
     
    }
    
    /**
     * 
     * @param User $oUsuario
     * @return $form
     */
    private function createCreateForm(User $oUsuario) {
        $form = $this->createForm(Form_UserType::class, $oUsuario,
                array('action' => $this->generateUrl('crear_usuario'), 'method' => 'POST'));
        return $form;
    }
    
    /**
     * @Route("/deportes/cargarBd", name="add_usuario")
     */
    public function cargarBd() {
        
        $em= $this->getDoctrine()->getManager();
        $noticia = new Noticia();
        $noticia->setSeccion("Tenis");
        $noticia->setEquipo("Serena-Willians");
        $noticia->setFecha("19022018");
        $noticia->setTextoTitular("Serena-gana-su-sexto-USA-Open");
        $noticia->setTextoNoticia("La norteamericana gana su sexto USA Open
                                   tras un largo partido");
        $noticia->setImagen('serena.jpg');

        $em->persist($noticia);
        $em->flush();
        return new Response("Noticia guardada con éxito con id: "
                            . $noticia->getId());
                
    }
    
    /**
     * @Route("/deportes/actualizarBd", name="actualizarNoticia")
     */
    public function actualizarBd(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $id = $request->query->get('id');
        $noticia = $em->getRepository(Noticia::class)->find($id);
        $noticia->setTextoTitular("Roger-Federer-a-una-victoria-del-númerouno"
                                   . "-de-Nadal");
        $noticia->setTextoNoticia("El suizo Roger Federer, el tenista 
                                   más laureado de la historia, está a son un 
                                   paso de regresar a la cima del tenis mundial 
                                   a sus 36 años. Clasificado sin admitir ni 
                                   réplica para cuartos de final del torneo de Rotterdam, 
                                   si venceeste viernes a Robin Haase se convertirá 
                                   en el número uno del mundo");
        $noticia->setImagen('federer.jpg');
        $em->flush();
        return new Response("Noticia actualizada!");
        
    }
    
    /**
     * @Route("/deportes/eliminarBd", name="eliminarNoticia")
     */
    public function eliminarBd(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $id = $request->query->get('id');
        $noticia = $em->getRepository(Noticia::class)->find($id);
        $em->remove($noticia);
        $em->flush();
        return new Response("Noticia eliminada!");
    }

    /**
     * @Route("/deportes/{seccion}/{pagina}", name="lista_paginas",
     * requirements={"pagina"="\d+"},
     * defaults={"seccion":"tenis"})
     */
    public function lista($seccion, $pagina=1) {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(Noticia::class);
        //Buscamos las noticias de una sección
        $noticiaSec = $repository->findOneBy(['seccion' => $seccion]);
        // Si la sección no existe saltará una excepción
        $error='';
        if (!$noticiaSec) {
            $error= 'Error 404 Página no encontrada';
            return $this->render('base.html.twig', [
                    'error' => $error]);
        }
        else {
        // Almacenamos todas las noticias de una sección en una lista
        $noticias = $repository->findBy(["seccion" => $seccion]);
        
        return $this->render('noticias/listar.html.twig', [
                    // La función str_replace elimina los símbolos - de los títulos
                    'titulo' => ucwords(str_replace('-', ' ', $seccion)),
                    'noticias' => $noticias, 'error' => $error]);
        } 
    }
  
    /**
     * @Route("/deportes/{slug}")
     */
    public function mostrar($slug) {
        return new Response(sprintf('Mi artículo en mi pagina de deportes: ruta %s', 
                                     $slug));
    }
            
    /**
     * @Route("/deportes/{seccion}/{titular} ",
     * defaults={"seccion":"tenis"}, name="verNoticia")
     */
    public function noticia($titular, $seccion) {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(Noticia::class);
        $noticia = $repository->findOneBy(['textoTitular' => $titular]);
        // Si la noticia que buscamos no se encuentra lanzamos error 404
        $error='';
        if (!$noticia) {
            // Ahora que controlamos el manejo de plantilla twig, vamos a
            // redirigir al usuario a la página de inicio
            // y mostraremos el error 404, para así no mostrar la página de
            // errores generica de symfony
            //throw $this->createNotFoundException('Error 404 este deporte no
            //                                      está en nuestra Base de Datos');
            $error= 'Error 404 Página no encontrada';
            return $this->render('base.html.twig', [
                    'error' => $error]);
        }
        return $this->render('noticias/noticia.html.twig', [
                    // Parseamos el titular para quitar los símbolos -
                    'titulo' => ucwords(str_replace('-', ' ', $titular)),
                    'noticias' => $noticia,'error' => $error]);
    }

    /**
     * @Route("/deportes/{_idioma}/{fecha}/{seccion}/{equipo}/{pagina}",
     *        defaults={"slug": "1","_formato":"html","pagina":"1"},
     *        requirements={"_idioma": "es|en","_formato": "html|json|xml",
     *                      "fecha": "[\d+]{8}","pagina"="\d+"})
     */
    public function rutaAvanzadaListado($_idioma, $fecha, $seccion, $equipo, $pagina) {
        
       //Realizamos una consulta un poco más avanzada. La función para
        //realizar esta consulta está en /serc/Repository/NoticiasRepository.php
        //Esta página se genera automaticamente para dar soporte a estas consultas
        $em=$this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(Noticia::class);

        $noticias= $repository->listadoNoticias($seccion,$fecha,$equipo);
        return $this->render('noticias/listar.html.twig', [
            'titulo' => ucwords(str_replace('-', ' ', $seccion)),
            'noticias'=>$noticias]);
    }
    
    /**
     * @Route("/deportes/{_idioma}/{fecha}/{seccion}/{equipo}/{slug}.{_formato}",
     *        defaults={"slug": "1","_formato":"html"},
     *        requirements={"_idioma": "es|en","_formato": "html|json|xml",
     *                      "fecha": "[\d+]{8}"})
     */
    public function rutaAvanzada($_idioma, $fecha, $seccion, $equipo, $titular) {
        
       //Realizamos una consulta un poco más avanzada. La función para
        //realizar esta consulta está en /src/Repository/NoticiasRepository.php
        //Esta página se genera automaticamente para dar soporte a estas consultas
        $em=$this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(Noticia::class);

        $noticia= $repository->verNoticia($seccion,$fecha,$equipo,$titular);
        return $this->render('noticias/noticia.html.twig', [
            //pasaramos el titular para quitar los simbolos -
            'titulo' => ucwords(str_replace('-', ' ', $titular)),
            'noticias'=>$noticia[0]]);
    }
    
    /**
     * @Route("/deportes/usuario/sesion/user", name="usuario" )
     */
    public function sesionUsuario(Request $request) {
        $usuario_get = $request->query->get('nombre');
        $session = $request->getSession();
        $session->set('nombre', $usuario_get);
        
        return
        $this->redirectToRoute('usuario_session', array('nombre' => $usuario_get));
        
    }

    /**
     * @Route("/deportes/usuario/sesion/user/{nombre}", name="usuario_session" )
     */
    public function paginaUsuario() {
        $session = new Session();
        $usuario = $session->get('nombre');
        return new Response(sprintf('Sesión iniciada con el atributo '
                                    . 'nombre: %s', $usuario));
    }
    
    

}
