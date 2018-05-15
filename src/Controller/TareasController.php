<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use FOS\ElasticaBundle\FOSElasticaBundle;
use App\Entity\Task;
use App\Form\Form_TaskType;

class TareasController extends Controller
{
    /**
     * @Route("/tareas/listatareas", name="_lista_tareas" )
     */ 
    public function listarTareas(Request $request) {
        $searchQuery= $request->request->get('query');
        if (!empty($searchQuery)) {
            $finder = $this->container->get('fos_elastica.finder.app.user');
            $tareas = $finder->createPaginatorAdapter($searchQuery);
        } else {
            $em = $this->getDoctrine()->getManager();
            $dql = "SELECT t FROM App\Entity\Task t ORDER BY t.id DESC";
            $tareas = $em->createQuery($dql);
        }

        $paginator= $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                           $tareas, $request->query->getInt('page', 1), 8);
               
        return $this->render('listaTareas.html.twig', array('pagination' => $pagination));
    }
      
    /**
     * @Route("/tareas/nuevatarea", name="_nueva_tarea")
     * 
     */
    public function nuevaTarea()
    {
       $task = new Task();
       $form = $this->createCreateForm($task);
       return $this->render('addTarea.html.twig', array('form'=>$form->createView()));
    }
    
    private function createCreateForm(Task $task){
        $form =$this->createForm(Form_TaskType::class, $task, array(
            'action' =>$this->generateUrl('_crear_tarea'),
            'method' =>'POST'
        ));
    return $form;    
    }
    
    /**
     * @Route("/tareas/creartarea", name="_crear_tarea")
     */
    public function crearTarea(Request $request){
        $task= new Task();
        $form= $this->createCreateForm($task);
        $form->handleRequest($request);
        
        if ( $form->isSubmitted() && $form->isValid()) {
            // se coloca estado a cero como tarea no realizada
            $task->setEstado(0);
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();
            $SuccessMessage = $this->get('translator')->trans('La tarea ha sido creada correctamente');
            $request->getSession()->getFlashBag()->add('mensaje', $SuccessMessage);
            return $this->redirectToRoute('_nueva_tarea');
        }
        
        return $this->render('addTarea.html.twig', array('form' => $form->createView()));
    }
    
    /**
     * @Route("/tareas/vertarea/{id}", name="_ver_tarea" )
     */
    public function verTarea($id){
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository(Task::class)->find($id);
        if(!$task){
            $ErrorMessage = $this->get('translator')->trans('Tarea no encontrada');
            throw $this->createNotFoundException($ErrorMessage);
        }
        $borrarForm=$this->createDeleteForm($task);
        return $this->render('viewTarea.html.twig', array('task' => $task, 'borrar_form'=>$borrarForm->createView()));
    }
    
     
    /**
     * @param type $task
     * @return type
    */
    private function createDeleteForm($task){
        return $this->createFormBuilder()
                ->setAction($this->generateUrl('_borrar_tarea', array('id' => $task->getId())))
                ->setMethod('DELETE')
                ->getForm();
    }
    
    /**
     * @Route("/tareas/borrartarea/{id}", name="_borrar_tarea")
     * @param \App\Controller\Request $request
     */
     public function borrarTarea(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository(Task::class)->find($id);
        if(!$task){
            $ErrorMessage = $this->get('translator')->trans('Tarea no encontrada');
            throw $this->createNotFoundException($ErrorMessage);
        }
        $form= $this->createDeleteForm($task);
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid()) {
            $em->remove($task);
            $em->flush();
            $SuccessMessage = $this->get('translator')->trans('La tarea ha sido borrada correctamente');
            $request->getSession ()->getFlashBag ()->add ( 'mensaje' , $SuccessMessage );
            return $this->redirectToRoute('_lista_tareas'); 
  
        }
        return $this->render('listaTareas.html.twig', array('form' => $form->createView()));
    }
    
    
     /**
     * @Route("/tareas/editartarea/{id}", name="_editar_tarea" )
     */
    public function editarTarea($id){
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository(Task::class)->find($id);
        if(!$task){
            $ErrorMessage = $this->get('translator')->trans('Tarea no encontrada');
            throw $this->createNotFoundException($ErrorMessage);
        }
        $form= $this->createEditForm($task);
        return $this->render('editTarea.html.twig', array('form' => $form->createView(), 'task' => $task));
    }
    
    /**
     * 
     * @param Task $task
     * @return type
     */
    private function createEditForm(Task $task) {
        $form = $this->createForm(Form_TaskType::class, $task,
                array('action' => $this->generateUrl('_update_tarea', 
                array('id'=> $task->getId())), 'method' =>'PUT'));
        return $form;
    }
    
    /**
     * @Route("/tareas/updatetarea/{id}", name="_update_tarea")
     * @param \App\Controller\Request $request
     */
     public function updateTarea($id, Request $request){
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository(Task::class)->find($id);
        if(!$task){
            $ErrorMessage = $this->get('translator')->trans('Tarea no encontrada');
            throw $this->createNotFoundException($ErrorMessage);
        }
        $form= $this->createEditForm($task);
        $form->handleRequest($request);
       if ( $form->isSubmitted() && $form->isValid()) {
            $task->setEstado(0);
            $em->flush();
            $SuccessMessage = $this->get('translator')->trans('La tarea ha sido modificada correctamente');
            $request->getSession ()->getFlashBag ()->add ( 'mensaje' , $SuccessMessage );
            return $this->redirectToRoute('_editar_tarea', array('id'=> $task->getId()));          
        }
        return $this->render('editTarea.html.twig', array('form' => $form->createView()));
    }
    
    /**
     *  @Route("/tareas/borrartarealistado/{id}", name="_borrar_tarea_listado")
     * @param \App\Controller\Request $request
     */
    public function borrarTareaListado(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository(Task::class)->find($id);
        if (!$task) {
            $ErrorMessage = $this->get('translator')->trans('Tarea no encontrada');
            throw $this->createNotFoundException($ErrorMessage);
        }
        $em->remove($task);
        $em->flush();
        $SuccessMessage = $this->get('translator')->trans('La tarea ha sido borrada correctamente');
        $request->getSession()->getFlashBag()->add('mensaje', $SuccessMessage);
        return $this->redirectToRoute('_lista_tareas');
    }
    
    /**
     *  @Route("/tareas/mistareas", name="_mis_tareas")
     * @param \App\Controller\Request $request
     */
    public function misTareas (Request $request){
        $idUser= $this->get('security.token_storage')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT t FROM App\Entity\Task t JOIN t.user u WHERE u.id = :idUser ORDER BY t.id DESC";
        $tasks = $em->createQuery($dql)->setParameter('idUser', $idUser);
        $paginator= $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                           $tasks, $request->query->getInt('page', 1), 8);
        
        return $this->render('misTareas.html.twig', array('pagination' => $pagination));
    }
    
 
    /**
     *  @Route("/tareas/procesartareas/{id}", name="procesar_tarea")
     * @param \App\Controller\Request $request
     */
    public function procesarTarea($id, Request $request){
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository(Task::class)->find($id);
        if (!$task) {
            $ErrorMessage = $this->get('translator')->trans('Tarea no encontrada');
            throw $this->createNotFoundException($ErrorMessage);
        }
        if ($task->getEstado() === 1) {
            $WarningMessage = $this->get('translator')->trans('La tarea ya había sido finalizada correctamente');
            $request->getSession()->getFlashBag()->add('advertencia', $WarningMessage);
        } else {
            $task->setEstado(1);
            $em->flush();
            $SuccessMessage = $this->get('translator')->trans('La tarea ha sido finalizada correctamente');
            $request->getSession()->getFlashBag()->add('mensaje', $SuccessMessage);
        }
        return $this->redirectToRoute('_mis_tareas');
    }
    
}
