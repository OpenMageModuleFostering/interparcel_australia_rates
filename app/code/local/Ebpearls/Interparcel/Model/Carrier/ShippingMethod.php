<?php

/**
* Our test shipping method module adapter
*/
class Ebpearls_Interparcel_Model_Carrier_ShippingMethod extends Mage_Shipping_Model_Carrier_Abstract
{
  /**
   * unique internal shipping method identifier
   *
   * @var string [a-z0-9_]
   */
  protected $_code = 'interparcel';

  /**
   * Collect rates for this shipping method based on information in $request
   *
   * @param Mage_Shipping_Model_Rate_Request $data
   * @return Mage_Shipping_Model_Rate_Result
   */
  public function collectRates(Mage_Shipping_Model_Rate_Request $request)
  {
        $allItems = $request->getAllItems();
        $rawAllowedCarriers = Mage::getStoreConfig('carriers/'.$this->_code.'/allowed_carriers');
        $packageDimensionsCsv = explode(';', $packageDimensionsRaw);
        $allowedCarriers = explode(',',$rawAllowedCarriers);

        /** retrive the cart item details **/

        /**
         * here we are retrieving shipping rates from external service
         * or using internal logic to calculate the rate from $request
         * you can see an example in Mage_Usa_Model_Shipping_Carrier_Ups::setRequest()
         */

        if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active')) {
            return false;
        }

        // get necessary configuration values
        $handling = Mage::getStoreConfig('carriers/'.$this->_code.'/handling');

        // this object will be returned as result of this method
        // containing all the shipping rates of this method


        $show = true;
        if($show){ // This if condition is just to demonstrate how to return success and error in shipping methods
            $packages = new Varien_Data_Collection();
            foreach($allItems as $item){
                $productId = $item->getProduct()->getId();
                $product = Mage::getModel('catalog/product')->load($productId);
                for($i=0; $i < $item->getData('qty');$i++){//treat each number of item individually
                    $package = new Varien_Object();
                    $package->setLength($this->formatDimension($product->getLength()));
                    $package->setWidth($this->formatDimension($product->getWidth()));
                    $package->setHeight($this->formatDimension($product->getHeight()));
                    $package->setWeight(ceil($product->getWeight()));
                    $packages->addItem($package);
                }
            }

            $requestData = new Varien_Object();
            $requestData->setPackages($packages);
            $requestData->setCity($request->getDestCity());
            $requestData->setCountry($request->getDestCountryId());
            $requestData->setPostcode($request->getDestPostcode());

            /** call the shipping api **/
            $interparcelApi = Mage::getModel('interparcel/interparcel');
            $rawRates = $interparcelApi->getRates($requestData);

            if(!$rawRates)return false; // if no rates are found, skip this method

            $ratesObject = simplexml_load_string($rawRates);
            if($ratesObject->Status != 'OK'){
                return false;
            }
            $rates = $ratesObject->Rates->Service;

            if(count($rates)){
                $result = Mage::getModel('shipping/rate_result');
                foreach($rates as $rate){
                    /** Rate Format **
                     SimpleXMLElement Object
                        (
                            [Name] => StarTrack Local ATL
                            [Carrier] => StarTrack
                            [PrinterRequired] => y
                            [TransitCover] => 0
                            [Price] => 15.60
                            [Tax] => 1.56
                            [Total] => 17.16
                        )
                     */
                    if(in_array($rate->Name,$allowedCarriers)){
                        $method = Mage::getModel('shipping/rate_result_method');
                        $method->setCarrier($this->_code);
                        $methodCode = strtolower(str_replace(' ', '_', $rate->Name));
                        $method->setMethod($methodCode);
                        $method->setCarrierTitle($this->getConfigData('title'));
                        $method->setMethodTitle($rate->Name);
                        $shippingPrice = $this->getFinalPriceWithHandlingFee($rate->Total);
                        $method->setPrice($shippingPrice);
                        $method->setCost($shippingPrice);
                        $result->append($method);
                        unset($method);
                    }
                }
            }else{
                return false;
            }
        }else{
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        }
      // add this rate to the result
    return $result;
  }


  /**
   * formats the dimension of the product to the required dimension to be sent to interparcel
   * @param type $dimension
   * @return type $dimension
   */
  public function formatDimension($dimension){
      $unitOfProduct = Mage::getStoreConfig('carriers/'.$this->_code.'/unit_dimensions');
      switch ($unitOfProduct){
          case 'mm':
              $dimension = $dimension / 10;
              break;
          case 'm':
              $dimension = $dimension * 100;
              break;
          case 'km':
              $dimension = $dimension * 100000;
              break;
          default :
              $dimension =$dimension;
      }
      return ceil($dimension);
  }

  /**
   * This method is used when viewing / listing Shipping Methods with Codes programmatically
   */
  public function getAllowedMethods() {
    return array($this->_code => $this->getConfigData('name'));
  }

}