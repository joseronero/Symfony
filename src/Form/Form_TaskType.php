<?php

namespace App\Form;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class Form_TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titulo')
            ->add('descripcion')
            ->add('user',EntityType::class, array('class'=>User::Class,
                  'query_builder'=>function(EntityRepository $er){
                     return $er->createQueryBuilder('u')
                           ->where ('u.role = :only')
                           ->setParameter('only', 'ROLE_USER');
                   },
                   'choice_label'=>'getFullName'))    
            ->add('guardar',SubmitType::class, array('label'=>'Guardar Tarea'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
