<?php

namespace Pequiven\SEIPBundle\Form\Sip\Center;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Pequiven\SEIPBundle\Model\Sip\Center\Ubch;

class UbchCargoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder            
            ->add('cargo', 'choice', array(
                'label' => 'Cargo ',                
                'choices' => Ubch::getCargoUbch(),
                'label_attr' => array('class' => 'label'),
                'attr' => array(
                    'class' => 'select2 input-large form-control',
                    'style' => 'width: 300px',
                    ),
                'required' => true,
                'empty_value' => 'Seleccione...',
                ))           
                         
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Pequiven\SEIPBundle\Entity\Sip\Ubch'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sip_ubch_cargo';
    }
}