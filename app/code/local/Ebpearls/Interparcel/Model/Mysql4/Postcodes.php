<?php

class Ebpearls_Interparcel_Model_Mysql4_Postcodes extends Mage_Core_Model_Resource_Db_Abstract {

    public function _construct() {
        $this->_init('interparcel/postcodes', 'id');
    }

}