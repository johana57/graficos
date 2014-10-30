<?php

namespace Pequiven\ArrangementProgramBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Modelo del programa de gestion
 *
 * @author Carlos Mendoza<inhack20@gmail.com>
 */
abstract class ArrangementProgram
{
    const TYPE_ARRANGEMENT_PROGRAM_TACTIC = 1;
    const TYPE_ARRANGEMENT_PROGRAM_OPERATIVE = 2;
    const TYPE_ARRANGEMENT_PROGRAM_OTHER = 3;
    
    /**
     * Estatus borrador
     */
    const STATUS_DRAFT = 0;
    
    /**
     * Estatus en revision
     */
    const STATUS_IN_REVIEW = 1;
    
    /**
     * Estatus revisado
     */
    const STATUS_REVISED = 2;
    
    /**
     * Estatus aprobado
     */
    const STATUS_APPROVED = 3;
    
    /**
     * Estatus rechazado
     */
    const STATUS_REJECTED = 4;
    
    /**
     * Estatus finalizado
     */
    const STATUS_FINISHED = 5;
    
    /**
     * Estatus cerrado
     */
    const STATUS_CLOSED = 6;
    
    /**
     * Tipo de programa de gestion
     * @var integer
     */
    protected $type;
    
    /**
     * Responsables del programa
     * @var \Pequiven\SEIPBundle\Entity\User
     */
    protected $responsibles;
    
    /**
     * Linea de tiempo
     * @var \Pequiven\ArrangementProgramBundle\Entity\Timeline
     */
    protected $timeline;
    
    /**
     * Estatus del programa de gestion
     * @var integer
     */
    protected $status = self::STATUS_DRAFT;
    
    function getTypeLabel() {
        
        $labels = array(
            self::TYPE_ARRANGEMENT_PROGRAM_TACTIC => 'pequiven.arrangement_program.type.tactic',
            self::TYPE_ARRANGEMENT_PROGRAM_OPERATIVE => 'pequiven.arrangement_program.type.operative',
        );
        if(isset($labels[$this->type])){
            return $labels[$this->type];
        }
    }
    
    /**
     * Set status
     *
     * @param integer $status
     * @return ArrangementProgram
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * Set timeline
     *
     * @param \Pequiven\ArrangementProgramBundle\Entity\Timeline $timeline
     * @return ArrangementProgram
     */
    public function setTimeline(\Pequiven\ArrangementProgramBundle\Entity\Timeline $timeline = null)
    {
        $timeline->setArrangementProgram($this);
        $this->timeline = $timeline;

        return $this;
    }

    /**
     * Get timeline
     *
     * @return \Pequiven\ArrangementProgramBundle\Entity\Timeline 
     */
    public function getTimeline()
    {
        return $this->timeline;
    }
    
    /**
     * Add responsibles
     *
     * @param \Pequiven\SEIPBundle\Entity\User $responsibles
     * @return ArrangementProgram
     */
    public function addResponsible(\Pequiven\SEIPBundle\Entity\User $responsibles)
    {
        $this->responsibles->add($responsibles);

        return $this;
    }

    /**
     * Remove responsibles
     *
     * @param \Pequiven\SEIPBundle\Entity\User $responsibles
     */
    public function removeResponsible(\Pequiven\SEIPBundle\Entity\User $responsibles)
    {
        $this->responsibles->removeElement($responsibles);
    }

    /**
     * Get responsibles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getResponsibles()
    {
        return $this->responsibles;
    }
    
    /**
     * Valida el total del peso de la linea de tiempo
     * @return boolean
     */
    function isValidTimeLine()
    {
        $timeline = $this->getTimeline();
        //Sino se asigno ninguna meta al crear el programa de gestion
        if(!$timeline){
            return ;
        }
        $weight = 0;
        foreach ($timeline->getGoals() as $goal) {
            $weight+= $goal->getWeight();
        }
        if($weight > 100){
            return false;
        }
        return true;
    }
    
    function isValidResponsibles()
    {
        $responsibles = $this->getResponsibles();
        if($responsibles  != null && $responsibles->count() > 0){
            return true;
        }
        return false;
    }
    
    /**
     * Retorna las etiquetas definidas para los estatus del programa de gestion
     * 
     * @staticvar array $labelsStatus
     * @return string
     */
    static function getLabelsStatus()
    {
        static $labelsStatus = array(
            self::STATUS_DRAFT => 'pequiven.arrangement_program.status.draft',
            self::STATUS_IN_REVIEW => 'pequiven.arrangement_program.status.in_review',
            self::STATUS_REVISED => 'pequiven.arrangement_program.status.revised',
            self::STATUS_APPROVED => 'pequiven.arrangement_program.status.approved',
            self::STATUS_REJECTED => 'pequiven.arrangement_program.status.rejected',
            self::STATUS_FINISHED => 'pequiven.arrangement_program.status.finished',
        );
        return $labelsStatus;
    }
    
    function getLabelStatus()
    {
        $labels = $this->getLabelsStatus();
        if(isset($labels[$this->status])){
            return $labels[$this->status];
        }
    }
    
    /**
     * Retorna el porcentaje de avance del programa de gestion
     */
    function getSummary(array $options = array())
    {
        $summary = array(
            'weight' => 0,
            'advances' => 0,
            'advancesPlanned' => 0,
        );
        $limitMonthToNow = false;
        $month = null;
        if(isset($options['limitMonthToNow'])){
            $date = new \DateTime();
            $month = $date->format('m');
            $limitMonthToNow = (boolean)$options['limitMonthToNow'];
        }
        $totalWeight = 0;
        $advances = 0;
        $advancesPlanned = 0;
        $timeline = $this->getTimeline();
        
        if($timeline){
            $propertyAccessor = \Symfony\Component\PropertyAccess\PropertyAccess::createPropertyAccessor();
            foreach ($timeline->getGoals() as $goal) {
                $goalDetails = $goal->getGoalDetails();
                $weight = 0;
                if($goalDetails !== null && $goalDetails->getGoal() !== null){
                    $weight = $goalDetails->getGoal()->getWeight();
                    
                }
                $totalWeight += $weight;
                $reflection = new \ReflectionClass($goalDetails);
                $nameMatchReal = '^get\w+Real$';
                $nameMatchPlanned = '^get\w+Planned$';
                foreach ($reflection->getMethods() as $method) {
                    $methodName = $method->getName();
                    if(preg_match('/'.$nameMatchReal.'/i', $methodName)){
                        $class = $method->getDeclaringClass();
                        if(!strpos($class, 'Pequiven\ArrangementProgramBundle\Entity\GoalDetails')){
                            continue;
                        }
                        $real = $goalDetails->$methodName();
                        $advances +=  ($weight/100) * $real;
                    }
                    if(preg_match('/'.$nameMatchPlanned.'/i', $methodName)){
                        $class = $method->getDeclaringClass();
                        if(!strpos($class, 'Pequiven\ArrangementProgramBundle\Entity\GoalDetails')){
                            continue;
                        }
                        if($limitMonthToNow === true){
                            $plannedString = lcfirst(str_replace('get', '', $methodName));
                            $plannedMonth = GoalDetails::getMonthOfPlanned($plannedString);
                            if($plannedMonth > $month){
                                continue;
                            }
                        }
                        $planned = $goalDetails->$methodName();
                        $advancesPlanned +=  ($weight/100) * $planned;
                    }
                }
                
            }
        }
        $summary['advances'] = $advances;
        $summary['weight'] = $totalWeight;
        $summary['advancesPlanned'] = $advancesPlanned;
        return $summary;
    }
    
    /**
     * Retorna true si se puede editar el programa de gestion
     * 
     * @return boolean
     */
    final public function isEditable()
    {
        $valid = true;
        $status = array(self::STATUS_APPROVED,self::STATUS_FINISHED,self::STATUS_CLOSED);
        
        if(in_array($this->status, $status,true)){
            $valid = false;
        }
        return $valid;
    }

    /**
     * Retorna true si se puede reportar avances en el programa de gestion
     * 
     * @return boolean
     */
    final public function isNotificable(){
        $valid = true;
        $status = array(self::STATUS_FINISHED,self::STATUS_CLOSED);
        if(in_array($this->status, $status,true)){
            $valid = false;
        }
        return $valid;
    }
}