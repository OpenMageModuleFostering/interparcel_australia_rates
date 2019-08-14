<?php
class Ebpearls_Interparcel_Adminhtml_InterparcelController extends Mage_Adminhtml_Controller_Action
{
    public function getPostCodesAction(){
        $query = $this->getRequest()->getParam('query');
        $html = '<ul><li style="display:none"></li>';
        $suggestData = array(
                            '1' =>  array(
                                        'row_class'=>'row1',
                                        'title'=>'abiral'
                                    ),
                            '2' =>  array(
                                        'row_class'=>'row1',
                                        'title'=>'test'
                                    )
                            );
        foreach ($suggestData as $index => $item) {
            if ($index == 0) {
                $item['row_class'] .= ' first';
            }

            if ($index == $count) {
                $item['row_class'] .= ' last';
            }

            $html .=  '<li title="'.$item['title'].'" class="'.$item['row_class'].'">'
                .$item['title'].'</li>';
        }

        $html.= '</ul>';
        die($html);
//      $this->getResponse()->setBody($html);
//        return $html;
//        die('here');
    }
}