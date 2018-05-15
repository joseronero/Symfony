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
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormError;
use Doctrine\DBAL\Schema\Constraint;
use Knp\Component\Pager\PaginatorInterface;
use FOS\ElasticaBundle\FOSElasticaBundle;

use App\Form\login;
use App\Entity\User;
use App\Form\Form_UserType;


class UserController extends Controller {
  
    /**
     * @Route("/user/inicio", name="_inicio")
     */
    public function inicio (Request $request) {
      
       return $this->render('layaout.html.twig');
    }
    
     /**
     * @Route("/user/home", name="_home")
     */
    public function home (Request $request) {
        
       return $this->render('home.html.twig');
    }
     
    /**
     * @Route("/user/login", name="_login_seguro" )
     */
    public function login (Request $request, AuthenticationUtils $authUtils)
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
     * @Route("/user/nuevousuario", name="_nuevo_usuario")
     */
    public function nuevoUsuario() {

        $oUusario = new User();
        $form = $this->createCreateForm($oUusario);
        $error ="";
        return $this->render('add.html.twig', array('form' => $form->createView(),'error'=> $error));
    }

    /**
     * @Route("/user/crearusuario", name="_crear_usuario")
     * 
     * @param \App\Controller\Request $request
     * @return User
     */
    public function crearUsuario(Request $request) {
        
        $oUsuario = new User();
        $form = $this->createCreateForm($oUsuario);
        $form->handleRequest($request);
        $error="";

        if ( $form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();    
            
            $passwordConstraint = new Assert\NotBlank();
            $errorPassword = $this->get('validator')->validate($password, $passwordConstraint);
            if(count($errorPassword)==0){
                $encoder = $this->container->get('security.password_encoder');
                $encoded = $encoder->encodePassword($oUsuario, $password);
                $oUsuario->setPassword($encoded);
                $em = $this->getDoctrine()->getManager();
                $em->persist($oUsuario);
                $em->flush();
                $SuccessMessage = $this->get('translator')->trans('El usuario ha sido creado correctamente');
                $request->getSession()->getFlashBag()->add('mensaje', $SuccessMessage);
                return $this->redirectToRoute('_crear_usuario');
            }
            else{
                $errorMessage = new FormError($errorPassword[0]->getMessage());
                $form->get('password')->addError($errorMessage);
            }
                   
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
                array('action' => $this->generateUrl('_crear_usuario'), 'method' => 'POST'));
        return $form;
    }
    
    
     /**
     * @Route("/user/listausuarios", name="_lista_usuarios" )
     */ 
    public function listarUsuarios(Request $request) {
        $searchQuery= $request->request->get('query');
        if (!empty($searchQuery)) {
            $finder = $this->container->get('fos_elastica.finder.app.user');
            $oUsuarios = $finder->createPaginatorAdapter($searchQuery);
        } else {
            $em = $this->getDoctrine()->getManager();
            $dql = "SELECT u FROM App\Entity\User u";
            $oUsuarios = $em->createQuery($dql);
        }

        $paginator= $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                           $oUsuarios, $request->query->getInt('page', 1), 8);
               
        return $this->render('lista.html.twig', array('pagination' => $pagination));
    }
    
     /**
     * @Route("/user/editarusuario/{id}", name="_editar_usuario" )
     */
    public function editarUsuario($id){
        $em = $this->getDoctrine()->getManager();
        $oUsuario = $em->getRepository(User::class)->find($id);
        if(!$oUsuario){
            $ErrorMessage = $this->get('translator')->trans('Usuario no encontrado');
            throw $this->createNotFoundException($ErrorMessage);
        }
        $form= $this->createEditForm($oUsuario);
        return $this->render('edit.html.twig', array('form' => $form->createView(), 'usuario' => $oUsuario));
    }
    
    /**
     * 
     * @param User $oUsuario
     * @return type
     */
    private function createEditForm(User $oUsuario) {
        $form = $this->createForm(Form_UserType::class, $oUsuario,
                array('action' => $this->generateUrl('_update_usuario', 
                array('id'=> $oUsuario->getId())), 'method' =>'PUT'));
        return $form;
    }
    
    /**
     * @Route("/user/updateusuario/{id}", name="_update_usuario")
     * @param \App\Controller\Request $request
     */
     public function updateUsuario($id, Request $request){
        $em = $this->getDoctrine()->getManager();
        $oUsuario = $em->getRepository(User::class)->find($id);
        if(!$oUsuario){
            $ErrorMessage = $this->get('translator')->trans('Usuario no encontrado');
            throw $this->createNotFoundException($ErrorMessage);
        }
       
        $form= $this->createEditForm($oUsuario);
        $form->handleRequest($request);
        
       if ( $form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData(); 
            if(!empty($password)){
                $encoder = $this->container->get('security.password_encoder');
                $encoded = $encoder->encodePassword($oUsuario, $password);
                $oUsuario->setPassword($encoded);
            }
            else {
                $password = $this->recuperaPassword($id);
                $oUsuario->setPassword($password[0]['password']);
            }
            if($form->get('role')->getData()== 'ROLE_ADMIN'){
                $oUsuario->setisActive('1');
            }
            $em->flush();
            $SuccessMessage = $this->get('translator')->trans('El usuario ha sido modificado correctamente');
            $request->getSession ()->getFlashBag ()->add ( 'mensaje' , $SuccessMessage );
            return $this->redirectToRoute('_editar_usuario', array('id'=> $oUsuario->getId()));          
        }
        return $this->render('edit.html.twig', array('form' => $form->createView()));
    }
   
    /**
     * 
     * @param type $id
     */
    private function recuperaPassword($id){
        $em = $this->getDoctrine()->getManager();
        $query= $em->createQuery(
             'SELECT u.password FROM App\Entity\User u WHERE u.id= :id'   
        )->setParameter('id',$id);
        $passwordActual= $query->getResult();
        return $passwordActual;
    }
    
     /**
     * @Route("/user/verusuario/{id}", name="_ver_usuario" )
     */
    public function verUsuario($id){
        $em = $this->getDoctrine()->getManager();
        $oUsuario = $em->getRepository(User::class)->find($id);
        if(!$oUsuario){
            $ErrorMessage = $this->get('translator')->trans('Usuario no encontrado');
            throw $this->createNotFoundException($ErrorMessage);
        }
        $borrarForm=$this->createDeleteForm($oUsuario);
        return $this->render('view.html.twig', array('user' => $oUsuario, 'borrar_form'=>$borrarForm->createView()));
    }
    
    /**
     * 
     * @param type $oUsuario
     * @return type
     */
    private function createDeleteForm($oUsuario){
        return $this->createFormBuilder()
                ->setAction($this->generateUrl('_borrar_usuario', array('id' => $oUsuario->getId())))
                ->setMethod('DELETE')
                ->getForm();
    }
    
    /**
     * @Route("/user/borrarusuario/{id}", name="_borrar_usuario")
     * @param \App\Controller\Request $request
     */
     public function borrarUsuario(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $oUsuario = $em->getRepository(User::class)->find($id);
        if(!$oUsuario){
            $ErrorMessage = $this->get('translator')->trans('Usuario no encontrado');
            throw $this->createNotFoundException($ErrorMessage);
        }
        $form= $this->createDeleteForm($oUsuario);
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid()) {
            $em->remove($oUsuario);
            $em->flush();
            $SuccessMessage = $this->get('translator')->trans('El usuario ha sido borrado correctamente');
            $request->getSession ()->getFlashBag ()->add ( 'mensaje' , $SuccessMessage );
            return $this->redirectToRoute('_lista_usuarios'); 
  
        }
        return $this->render('lista.html.twig', array('form' => $form->createView()));
    }
    
    /**
     *  @Route("/user/borraruserlistado/{id}", name="_borrar_usuario_listado")
     * @param \App\Controller\Request $request
     */
    public function borrarUserListado(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $oUsuario = $em->getRepository(User::class)->find($id);
        if (!$oUsuario) {
            $ErrorMessage = $this->get('translator')->trans('Usuario no encontrado');
            throw $this->createNotFoundException($ErrorMessage);
        }
        $em->remove($oUsuario);
        $em->flush();
        $SuccessMessage = $this->get('translator')->trans('El usuario ha sido borrado correctamente');
        $request->getSession()->getFlashBag()->add('mensaje', $SuccessMessage);
        return $this->redirectToRoute('_lista_usuarios');
    }

}