<?php

/**
 * Export adapter array.
 *
 * @category   JumpLink
 * @package    JumpLink_DNode
 * @author     Pascal Garber <jumplink@gmail.com>
 */
class JumpLink_ImportExport_Model_Export_Adapter_Array extends Mage_ImportExport_Model_Export_Adapter_Abstract
{

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
        return $this->result;
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
        if (!array_key_exists('product_id', $rowData) && isset($rowData['sku']))
            $rowData['product_id'] = Mage::getModel("catalog/product")->getIdBySku($rowData['sku']);

        // check if row is an StoreView
        if (!isset($rowData['sku']) && isset($rowData['_store'])) {
            $tmp_store = $this->result[count($this->result)-1]['_store'];
            if (is_array($this->result[count($this->result)-1]['_store']))
                $this->result[count($this->result)-1][$rowData['_store']][] = $rowData; // push to existing array
            else {
                $this->result[count($this->result)-1]['_store'] = array();
                $this->result[count($this->result)-1]['_store'][] = $tmp_store;
                $this->result[count($this->result)-1]['_store'][] = $rowData;
            }
        }
        // check if row is an Website
        else if (!isset($rowData['sku']) && isset($rowData['_product_websites'])) {
            $tmp_website = $this->result[count($this->result)-1]['_product_websites'];
            if (is_array($this->result[count($this->result)-1]['_product_websites']))
                $this->result[count($this->result)-1][$rowData['_product_websites']][] = $rowData; // push to existing array
            else {
                $this->result[count($this->result)-1]['_product_websites'] = array();
                $this->result[count($this->result)-1]['_product_websites'][] = $tmp_website;
                $this->result[count($this->result)-1]['_product_websites'][] = $rowData;
            }
        }
        // check if row is an Category
        else if (!isset($rowData['sku']) && isset($rowData['_category'])) {
            $tmp_category = $this->result[count($this->result)-1]['_category'];
            if (is_array($tmp_category))
                $this->result[count($this->result)-1]['_category'][] = $rowData; // push to existing array
            else { // push old and new category to new array
                $this->result[count($this->result)-1]['_category'] = array();
                $this->result[count($this->result)-1]['_category'][] = $tmp_category;
                $this->result[count($this->result)-1]['_category'][] = $rowData;
            }
        }
        // check if row is an Tier Price
        else if (!isset($rowData['sku']) && isset($rowData['_tier_price_website'])) {
            $tmp_tier_price_website = $this->result[count($this->result)-1]['_tier_price_website'];
            if (is_array($tmp_category))
                $this->result[count($this->result)-1]['_tier_price_website'][] = $rowData; // push to existing array
            else { // push old and new category to new array
                $this->result[count($this->result)-1]['_tier_price_website'] = array();
                $this->result[count($this->result)-1]['_tier_price_website'][] = $tmp_tier_price_website;
                $this->result[count($this->result)-1]['_tier_price_website'][] = $rowData;
            }
        }
        // check if row is an Media Image
        else if (!isset($rowData['sku']) && isset($rowData['_media_image'])) {
            $tmp_media_image = $this->result[count($this->result)-1]['_media_image'];
            if (is_array($tmp_media_image))
                $this->result[count($this->result)-1]['_media_image'][] = $rowData; // push to existing array
            else { // push old and new category to new array
                $this->result[count($this->result)-1]['_media_image'] = array();
                $this->result[count($this->result)-1]['_media_image'][] = $tmp_media_image;
                $this->result[count($this->result)-1]['_media_image'][] = $rowData;
            }
        }
        else {
            $this->result[] = $rowData;
        }
        //print_r ($this->result[count($this->result)-1]);
        return $this->result[count($this->result)-1];
    }
}
