<?php
namespace oat\taoDeliveryRdf\model;

use oat\taoDelivery\model\Assignment as BaseAssignment;

class Assignment extends BaseAssignment
{
    private $displayOrder;
    
    public function __construct($deliveryId, $userId, $label, $desc, $startable, $displayOrder = 0)
    {
        parent::__construct(
            $deliveryId,
            $userId,
            $label,
            $desc,
            $startable
        );
        
        $this->displayOrder = intval($displayOrder);
    }
    
    /**
     * Get the display order.
     * 
     * @return int
     */
    public function getDisplayOrder()
    {
        return $this->displayOrder;
    }
}
