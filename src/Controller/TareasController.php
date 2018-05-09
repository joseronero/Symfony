<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
            $SuccessMessage = 'La tarea ha sido creada correctamente';
            $request->getSession()->getFlashBag()->add('mensaje', $SuccessMessage);
            return $this->redirectToRoute('_nueva_tarea');
        }
        
        return $this->render('addTarea.html.twig', array('form' => $form->createView()));
    }
}
