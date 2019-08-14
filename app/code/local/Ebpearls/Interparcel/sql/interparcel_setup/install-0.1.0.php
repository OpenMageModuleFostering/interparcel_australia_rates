<?php
/**
 * News installation script
 *
 * @author Magento
 */

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;

$installer->startSetup();

$installer->run("
    CREATE TABLE IF NOT EXISTS `{$installer->getTable('interparcel/postcodes')}` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `code` int(11) NOT NULL,
    `city` varchar(255) NOT NULL,
    `state` varchar(55) NOT NULL,
    `country` varchar(55) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
    
$postcodeCsv = Mage::getModuleDir('sql', 'Ebpearls_Interparcel') . DS . 'interparcel_setup' . DS . 'pc.csv';
if(file_exists($postcodeCsv)){
    $file_handle = fopen($postcodeCsv, 'r');
    $i = 0;
    while (!feof($file_handle) ) {
        $data = fgetcsv($file_handle, 1024);
        if($i == 0){
            $i++;
            continue;
        }
        $postcode = $data[0];
        $city =     $data[1];
        $state =    $data[2];
        $country =  $data[3];
        $installer->getConnection()->insertForce($installer->getTable('interparcel/postcodes'), array(
            'code'      => $postcode,
            'city'      => $city,
            'state'     => $state,
            'country'   => $country
        ));                
    }
    fclose($file_handle);
}
$installer->endSetup();