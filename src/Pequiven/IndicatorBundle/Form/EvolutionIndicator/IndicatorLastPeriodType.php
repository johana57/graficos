<?php

namespace Pequiven\IndicatorBundle\Form\EvolutionIndicator;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class IndicatorLastPeriodType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
        $period = $this->period;

        $builder            
            ->add('indicatorlastPeriod', null, array(
                    'query_builder' => function(\Pequiven\IndicatorBundle\Repository\IndicatorRepository $repository) use($period){
                        return $repository->getLastPeriod($period);
                    },
                    'label' => 'Indicador Período Anterior',
                    'label_attr' => array('class' => 'label'),
                    'attr' => array(
                        'class' => "input-xlarge select2",
                        'style' => 'width: 270px',
                        //'multiple' => 'multiple'
                    ),
                    'empty_value' => 'Seleccione...',
                    'required' => true,
                ))            
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Pequiven\IndicatorBundle\Entity\Indicator'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'lastPeriod';
    }

    protected $period;    
    
    public function __construct ($period)
    {
        $this->period = $period;               
    }
}