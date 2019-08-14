<?php
class Ebpearls_Interparcel_Model_Interparcel extends Mage_Core_Model_Abstract{
    
    protected $_code = 'interparcel';
    
    function __construct() {
        parent::__construct();
        $this->url = "https://www.interparcel.com.au/api/xml/rates.php";
        $this->userId = Mage::getStoreConfig('carriers/'.$this->_code.'/username');
        $this->password = Mage::getStoreConfig('carriers/'.$this->_code.'/password');
        $this->origin = array(
                            'city'      =>  Mage::getStoreConfig('carriers/'.$this->_code.'/origin_city'),
                            'country'   =>  Mage::getStoreConfig('carriers/'.$this->_code.'/origin_country'),
                            'postcode'  =>  Mage::getStoreConfig('carriers/'.$this->_code.'/origin_postcode')
                        );
        $this->allowedCarriers = Mage::getStoreConfig('carriers/'.$this->_code.'/allowed_carriers');
        
//        $this->url = "https://www.interparcel.com.au/api/xml/rates.php";
//        $this->userId = 'akash@ebpearls.com.au';
//        $this->password = 'ebPearls';
//        $this->origin = array(
//                            'city'      =>  'Australia Square',//Mage::getStoreConfig('carriers/'.$this->_code.'/origin_city'),
//                            'country'   =>  'AU',//Mage::getStoreConfig('carriers/'.$this->_code.'/origin_country'),
//                            'postcode'  =>  '1215',//Mage::getStoreConfig('carriers/'.$this->_code.'/origin_postcode'),
//                        );
//        $this->allowedCarriers = Mage::getStoreConfig('carriers/'.$this->_code.'/allowed_carriers');
    }
    
    public function getRates($requestData){        
        $requestXml = $this->getPrepareRequest($requestData);        
        if(!$requestXml)return false;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt($ch, CURLOPT_URL,$this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestXml);
        curl_setopt($ch, CURLOPT_POST, 1);
        
        $data = curl_exec($ch);
        return $data;        
    }
    
    
    /**
    * 
    * @param array $product l,b,h,weight
    * @param array $package l,b,h,weight
    * @return boolean
    */
    public function checkFit($product, $package){               
       //check if volume of package > volume of product
       if($this->getVolume($package) < $this->getVolume($product)){
           return false;
       }       
       //check if height of product is greater
       if($product[2] > $package[2]){
           return false;
       }

       //remove height and weight factor
       unset($product[2]);
       unset($product[3]);
       unset($package[2]);
       unset($package[3]);

       //check if biger dimension of lenght and breadht of product is greater than bigger of lenght and breadth of package
       if(max($product) > max($package)){
           return false;
       }

       //check if smaller dimension of lenght and breadth of product is greater than smaller of length and breadth of package
       if(min($product) > min($package)){
           return false;
       }

       return true;
   }
   
   public function getVolume($item){
       return $item[0] * $item[1] * $item[2];
   }
   
   /**
    * 
    * @param array $products array of products with dimension as array(l,b,h)
    * @param array $packages array of packages with dimension as array(l,b,h)
    * @throws Exception if a package can't find for a product
    */
   public function createPackages($productItems){       
       foreach($productItems as $item){
           $products[] = array($item->getLength(),$item->getWidth(),$item->getHeight(),$item->getWeight());
       }                 
        $packages = array(
             0 => array(200,200,200),
             1 => array(500,100,300),
             2 => array(100,100,100),    
             4 => array(150,150,150),
             5 => array(250,250,250),
             6 => array(50,50,50),
             7 => array(300,300,300),
             8 => array(400,400,400),
             9 => array(450,200,500)
         );         

         $packageCollection = array();//variable to store the details of products and the package in which the product is packed

         foreach($packages as $key => $val){
             $packageVolumes[$key] = $this->getVolume($val);
         }
         foreach($products as $key => $val){
             $productVolumes[$key] = $this->getVolume($val);
         }
         
         asort($packageVolumes);
         arsort($productVolumes);

         //sort packages in ascending order by volume
         foreach($packageVolumes as $key => $val){
             $sortedPackages[] = $packages[$key];
         }
         
         //sort products in descending order by volume
         foreach($productVolumes as $key => $val){
             $sortedProducts[] = $products[$key];
         }
         
         $packedProducts = array();// variable to store the products and package combination
         
         $counter = 0;
         //loop through each product and find matching package for the product, if a package is not found for a product
         foreach($sortedProducts as $productIndex => $product){
             $canBePacked = false; // variable to flag is a product can be packed.
             $productVolume = $this->getVolume($product);

             if(in_array($productIndex, $packedProducts)){
                 $canBePacked = true;
                 continue;//skip if product already added to package     
             }
             foreach($sortedPackages as $packageIndex => $package){
                 $packageVolume = $this->getVolume($package);                 
                 if($packageVolume >= $productVolume){
                     if(!$this->checkFit($product,$package)){//if product doesn't fit in package then move on to next package
                         continue;
                     }

                     // we are here if current package is suitable for the product

                     $remainingHeight = $package[2] - $product[2];
                     $remainingVolume = $package[0] * $package[1] * $remainingHeight; // we calculate only the remaining volume above the item                                    

                     $packedProducts[] = $productIndex;
                     $canBePacked = true;
                     $packageCollection[$counter]['package'] = $package;
                     $packageCollection[$counter]['product'][] = $product;


                     //if remaining height is <= 0 or remaining volume is less than the remaining product with least volume, then continue to next product
                     if($remainingHeight <= 0){
                         break;
                     }

                     $remainingPackage = $package;            
                     $remainingPackage[2] = $remainingHeight;//remaining package is the package with same length and width but height reduced to remainaing height

                     //check if any other product fits in the package.
                     foreach($sortedProducts as $childProductIndex => $childProduct){                                
                         if(in_array($childProductIndex, $packedProducts)){                    
                             continue;
                         }

                         $childProductVolume = $this->getVolume($childProduct);
                         if($remainingVolume >= $childProductVolume){
                             if(!$this->checkFit($childProduct,$remainingPackage)){//if product doesn't fit in package then move on to next package
                                 continue;
                             }

                             // we are here if current remainingPackage is suitable for the childProduct                    
                             $packedProducts[] = $childProductIndex;                    
                             $packageCollection[$counter]['product'][] = $childProduct;
                             $remainingHeight = $remainingPackage[2] - $childProduct[2];
                             $remainingPackage[2] = $remainingHeight;//remaining package is the package with same length and width but height reduced to remainaing height
                             $remainingVolume = $remainingPackage[0] * $remainingPackage[1] * $remainingHeight; // we calculate only the remaining volume above the item                    
                         }                
                     }
                     break;
                 }
             }
             if(!$canBePacked){                 
                 throw new Exception("Package not found for the item with dimensions ". implode(',', $product));
             }
             $counter++;
         }
         return $packageCollection;
    }
    
    
    /**
     * 
     * @param Varien_Object $requestData
     */
    public function getPrepareRequest($requestData){
        try{
            $packages = $this->createPackages($requestData->getPackages());//create packages for the products in cart            
        }  catch (Exception $e){
            Mage::log($this->_code . " : ". $e->getMessage(),null,'interparcel.log');            
            return false;
        }        
        // Building your XML string
        $strXML = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $strXML .= '<Request>'."\n";
        $strXML .= '<Authentication>'."\n";
        $strXML .= '<UserID>'. $this->userId .'</UserID>'."\n";
        $strXML .= '<Password>'.$this->password.'</Password>'."\n";
        $strXML .= '<Version>1.0</Version>'."\n";
        $strXML .= '</Authentication>'."\n";
        $strXML .= '<RequestType>Rates</RequestType>'."\n";
        $strXML .= '<ShowAvailability>N</ShowAvailability>'."\n";
        $strXML .= '<Shipment>'."\n";
        $strXML .= '<Collection>'."\n";
        $strXML .= '<City>'.$this->origin['city'].'</City>'."\n";
        $strXML .= '<Country>'.$this->origin['country'].'</Country>'."\n";
        $strXML .= '<PostCode>'.$this->origin['postcode'].'</PostCode>'."\n";
        $strXML .= '</Collection>'."\n";
        $strXML .= '<Delivery>'."\n";
        $strXML .= '<City>'.$requestData->getCity().'</City>'."\n";
        $strXML .= '<Country>'.$requestData->getCountry().'</Country>'."\n";
        $strXML .= '<PostCode>'.$requestData->getPostcode().'</PostCode>'."\n";
        $strXML .= '</Delivery>'."\n";
        //$packages = $requestData->getPackages();
        if(count($packages)){
            foreach($packages as $package){
                $weight = 0;
                if(isset($package['product'])){
                    foreach($package['product'] as $item){
                        $weight =+ $item[3];
                    }
                }
                $strXML .= '<Package>'."\n";
                $strXML .= '<Weight>'.$weight.'</Weight>'."\n";
                $strXML .= '<Length>'.$package['package'][0].'</Length>'."\n";
                $strXML .= '<Width>'.$package['package'][1].'</Width>'."\n";
                $strXML .= '<Height>'.$package['package'][2].'</Height>'."\n";
                $strXML .= '</Package>'."\n";
            }
        }        
        $strXML .= '</Shipment>'."\n";	
        $strXML .= '</Request>'."\n";
        return $strXML;
    }
    
}

/*$url = "https://www.interparcel.com.au/api/xml/rates.php";
// Your post data
$post_city= 'Surry Hills';
$post_postcode = 2010;

// Building your XML string
$strXML = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
$strXML .= '<Request>'."\n";
$strXML .= '<Authentication>'."\n";
$strXML .= '<UserID>akash@ebpearls.com.au</UserID>'."\n";
$strXML .= '<Password>ebPearls</Password>'."\n";
$strXML .= '<Version>1.0</Version>'."\n";
$strXML .= '</Authentication>'."\n";

$strXML .= '<RequestType>Rates</RequestType>'."\n";
$strXML .= '<ShowAvailability>N</ShowAvailability>'."\n";

$strXML .= '<Shipment>'."\n";

$strXML .= '<Collection>'."\n";

$strXML .= '<City>Cranbourne</City>'."\n";
$strXML .= '<Country>Australia</Country>'."\n";
$strXML .= '<PostCode>3977</PostCode>'."\n";
$strXML .= '</Collection>'."\n";

$strXML .= '<Delivery>'."\n";
$strXML .= '<City>'.$post_city.'</City>'."\n";
$strXML .= '<Country>Australia</Country>'."\n";
$strXML .= '<PostCode>'.$post_postcode.'</PostCode>'."\n";
$strXML .= '</Delivery>'."\n";

$strXML .= '<Package>'."\n";
$strXML .= '<Weight>4</Weight>'."\n";
$strXML .= '<Length>4</Length>'."\n";
$strXML .= '<Width>4</Width>'."\n";
$strXML .= '<Height>4</Height>'."\n";
$strXML .= '</Package>'."\n";

$strXML .= '</Shipment>'."\n";	
$strXML .= '</Request>'."\n";
//$strXML = new SimpleXMLElement($strXML);
//echo("<pre>");print_r($strXML);die;


$post_string = $strXML;

///echo $post_string;
//die;


$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 4);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
curl_setopt($ch, CURLOPT_POST, 1);

$data = curl_exec($ch); 

echo("<pre>");
print_r($data);
echo("</pre>");
die;

if(curl_errno($ch))
print curl_error($ch);
else
curl_close($ch);
*/
?>