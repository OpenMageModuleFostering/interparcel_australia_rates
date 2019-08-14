<?php
class Ebpearls_Interparcel_Helper_Data extends Mage_Core_Helper_Abstract{
    
    function getPostcodes($like){
        $postcodes = Mage::getModel('interparcel/postcodes')->getCollection();        
        if(isset($like['postcode']) && $like['postcode']){
            $postcodes->addFieldToFilter('code', array('like' => $like['postcode']. '%'));
        }
        if(isset($like['city']) && $like['city']){
            $postcodes->addFieldToFilter('city', array('like' => $like['city']. '%'));
//            $attributes[] = 'city';
//            $conditions[] = array('like'=>$like['city'].'%');
        }
        if(isset($like['state']) && $like['state']){
            $postcodes->addFieldToFilter('state', array('like' => $like['state']. '%'));            
//            $attributes[] = 'state';
//            $conditions[] = array('like'=>$like['state'].'%');
        }
        if(isset($like['country']) && $like['country']){
            $postcodes->addFieldToFilter('country', array('like' => $like['country']. '%'));                        
//            $attributes[] = 'country';
//            $conditions[] = array('like'=>$like['country'].'%');
        }
//        $postcodes
//            ->addFieldToFilter(
//                $attributes,
//                $conditions                
//            );
        return $postcodes;
    }    
}

?>
