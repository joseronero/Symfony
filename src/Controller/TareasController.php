<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Task;
use App\Form\Form_TaskType;

class TareasController extends Controller
{
    /**
     * @Route("/tareas/creartarea", name="_crear_tarea")
     */
    public function CrearTarea()
    {
       $task = new Task();
       $form = createCreateForm($task);
    }
    
    private function createCreateForm(){
        
    }
}
