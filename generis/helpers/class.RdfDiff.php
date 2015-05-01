<?php

use oat\generis\model\data\Model;
class helpers_RdfDiff
{
    private $added = array();

    private $removed = array();

    private $toAdd = array();

    private $toRemove = array();

    /**
     * Creates a diff from a set of triples to a set of troples
     * 
     * 
     * @param Traversable $from
     * @param Traversable $to
     */
    public static function create(Traversable $from, Traversable $to) {
        $diff = new self();
        
        foreach ($to as $triple) {
            $diff->add($triple);
        }
        
        
        foreach ($from as $triple) {
            $diff->remove($triple);
        }
        return $diff;
    }
    
    /**
     * @return Iterator
     */
    public function getTriplesToAdd() {
        return new ArrayIterator($this->toAdd);
    }
    
    /**
     * @return Iterator
     */
    public function getTriplesToRemove() {
        return new ArrayIterator($this->toRemove);
    }
    
    protected function add(core_kernel_classes_Triple $triple)
    {
        $serial = $this->generateSerial($triple);
        $this->added[$serial] = true;
        if (! isset($this->removed[$serial])) {
            $this->toAdd[$serial] = $triple;
        } elseif (isset($this->toRemove[$serial])) {
            unset($this->toRemove[$serial]);
        }
    }

    protected function remove(core_kernel_classes_Triple $triple)
    {
        $serial = $this->generateSerial($triple);
        $this->removed[$serial] = true;
        if (! isset($this->added[$serial])) {
            $this->toRemove[$serial] = $triple;
        } elseif (isset($this->toAdd[$serial])) {
            unset($this->toAdd[$serial]);
        }
    }
    
    protected function generateSerial(core_kernel_classes_Triple $triple) {
        return md5(implode(' ', array($triple->subject, $triple->predicate, $triple->object, $triple->lg, $triple->modelid)));
    }
    
    public function getSummary() {
        return count($this->toAdd).' triples to add and '.count($this->toRemove).' triples to remove';
    }
    
    public function dump() {
        foreach ($this->toAdd as $triple) {
            echo '+ '.str_pad($triple->subject, 80).' '.str_pad($triple->predicate, 80).' '.str_pad($triple->object, 80).PHP_EOL;
        }
        foreach ($this->toRemove as $triple) {
            echo '- '.str_pad($triple->subject, 80).' '.str_pad($triple->predicate, 80).' '.str_pad($triple->object, 80).PHP_EOL;
        }
        
    }
    
    public function applyTo(Model $model) {
        $rdf = $model->getRdfInterface();
        foreach ($this->getTriplesToRemove() as $triple) {
            $rdf->remove($triple);
        }
        foreach ($this->getTriplesToAdd() as $triple) {
            $rdf->add($triple);
        }
    }
}
