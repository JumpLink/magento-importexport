<?php

/**
 * Export adapter array.
 *
 * @category   JumpLink
 * @package    JumpLink_DNode
 * @author     Pascal Garber <jumplink@gmail.com>
 */
class JumpLink_ImportExport_Model_Export_Adapter_Callback extends Mage_ImportExport_Model_Export_Adapter_Abstract
{

    /**
     * calback for each row
     *
     * @var function
     */
    public $_callback;

    /**
     * Result
     *
     * @var array of products of array of attributes
     */
    public $result = array();

    /**
     * Method called as last step of object instance creation. Can be overrided in child classes.
     *
     * @return Mage_ImportExport_Model_Export_Adapter_Abstract
     */
    protected function _init()
    {
        return $this;
    }

    /**
     * Get contents of export file.
     *
     * @return array of products of array of attributes
     */
    public function getContents()
    {
        return null;
    }

    /**
     * Get contents of export file.
     *
     * @return array of products of array of attributes
     */
    public function setCallback(callable $callback)
    {
        $this->_callback = $callback;
        return $this;
    }

    /**
     * Write row data to source file.
     *
     * @param array $rowData
     * @throws Exception
     * @return product
     */
    public function writeRow(array $rowData)
    {
        // insert product_id if it dosn't exists but only if sku is set
        print ("writeRow\n");

        // check if row is an StoreView
        if (!isset($rowData['sku']) && isset($rowData['_store'])) {
            return $this->_callback("_store", $rowData);
        }
        // check if row is an Website
        else if (!isset($rowData['sku']) && isset($rowData['_product_websites'])) {
            return $this->_callback("_product_websites", $rowData);
        }
        // check if row is an Category
        else if (!isset($rowData['sku']) && isset($rowData['_category'])) {
            return $this->_callback("_category", $rowData);
        }
        // check if row is an Tier Price
        else if (!isset($rowData['sku']) && isset($rowData['_tier_price_website'])) {
            return $this->_callback("_tier_price_website", $rowData);
        }
        // check if row is an Media Image
        else if (!isset($rowData['sku']) && isset($rowData['_media_image'])) {
            return $this->_callback("_media_image", $rowData);
        }
        // is product ?
        else {
            if (!array_key_exists('product_id', $rowData) && isset($rowData['sku']))
                $rowData['product_id'] = Mage::getModel("catalog/product")->getIdBySku($rowData['sku']);
            return $this->_callback("product", $rowData);
        }
        return $this->_callback("error", array('message'=>'unknown row'));
    }
}
