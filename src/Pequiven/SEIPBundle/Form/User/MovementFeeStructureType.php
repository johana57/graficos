<?php

namespace Pequiven\SEIPBundle\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Pequiven\SEIPBundle\Repository\UserRepository;

class MovementFeeStructureType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $builder
            ->add('date', 'date', array(
                'label'=>'Fecha',
                'label_attr' => array('class' => 'label'),
                'format' => 'd/M/y',
                'widget' => 'single_text',
                'attr' => array('class' => 'input input-large')               
            ))
            ->add('cause', 'text', array(
                'label'=>'Causa',
                'label_attr' => array('class' => 'label'),
                'attr' => array('class' => 'input input-large')               
            ))
            ->add('type', 'text', array(
                'label'=>'Tipo',
                'label_attr' => array('class' => 'label'),
                'attr' => array('class' => 'input input-large')               
            ))
            ->add('observations', 'textarea', array(
                'label'=>'Observación',
                'label_attr' => array('class' => 'label'),
                'attr' => array('class' => 'input input-large')               
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Pequiven\SEIPBundle\Entity\User\MovementFeeStructure'));
    }

    public function getName() {
        return 'fee_structure_add';
    }

}
