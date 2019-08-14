<?php
class Ebpearls_Interparcel_Model_Carrierslist{
    public function toOptionArray()
    {
        return array(
            array('value'=>'fedex', 'label'=>Mage::helper('adminhtml')->__('Fedex')),
            array('value'=>'dhl', 'label'=>Mage::helper('adminhtml')->__('DHL')),
        );
    }
}