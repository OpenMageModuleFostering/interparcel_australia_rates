<?php
class Ebpearls_Interparcel_InterparcelController extends Mage_Core_Controller_Front_Action
{
    public function getPostCodesAction(){
        $conditions = array();
        $postcode = $this->getRequest()->getParam('postcode');
        if($postcode){
            $conditions['postcode'] = $postcode;
        }
        $city = $this->getRequest()->getParam('city');
        if($city){
            $conditions['city'] = $city;
        }
        $state = $this->getRequest()->getParam('state');
        if($state){
            $conditions['state'] = $state;
        }
        $country = $this->getRequest()->getParam('country');
        if($country){
            $conditions['country'] = $country;
        }
                
        $postcodes = Mage::helper('interparcel')->getPostcodes($conditions);                
        $html = '<ul><li style="display:none"></li>';
        foreach($postcodes as $postcode){
            $suggestData[] = array(
                                'row_class' =>  'row'.$i++,
                                'title'     =>  $postcode->getCode(). ', ' . $postcode->getCity() ,
                                'postcode'  =>  $postcode->getCode(),
                                'city'      =>  $postcode->getCity()
                            );
        }
        
        foreach ($suggestData as $index => $item) {
            if ($index == 0) {
                $item['row_class'] .= ' first';
            }

            if ($index == $count) {
                $item['row_class'] .= ' last';
            }

            $html .=  '<li postcode = "'. $item['postcode'] .'" city = "'. $item['city'] .'" title="'.$item['title'].'" class="'.$item['row_class'].'">'
                .$item['title'].'</li>';
        }

        $html.= '</ul>';
        die($html);
    }
}