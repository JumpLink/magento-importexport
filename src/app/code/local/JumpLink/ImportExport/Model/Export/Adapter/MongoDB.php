<?php

/**
 * Export adapter array.
 *
 * @category   JumpLink
 * @package    JumpLink_DNode
 * @author     Pascal Garber <jumplink@gmail.com>
 */
class JumpLink_ImportExport_Model_Export_Adapter_MongoDB extends Mage_ImportExport_Model_Export_Adapter_Abstract
{

    /**
     * MongoDB ProductCache 
     *
     * @var mongodb productCache Collection
     */
    protected $productcache;

    /**
     * Last updated/inserted sku
     *
     * @var string
     */
    protected $last_sku;
    protected $collection;

    /**
     * Method called as last step of object instance creation. Can be overrided in child classes.
     *
     * @return Mage_ImportExport_Model_Export_Adapter_Abstract
     */
    protected function _init()
    {

    }

    public function setCollection($collection)
    {
        $this->collection = $collection;
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
            $last_store = $this->collection->findOne(array('sku' => $this->last_sku))['_store'];
            if (!is_array($last_store))
                $last_store = array();
            $last_store[] = $rowData;
            return $this->collection->update(array('sku'=>$this->last_sku), array('$set' => array("_store" => $last_store)));
        }
        // check if row is an Website
        else if (!isset($rowData['sku']) && isset($rowData['_product_websites'])) {
            $last_product_websites = $this->collection->findOne(array('sku' => $this->last_sku))['_product_websites'];
            if (!is_array($last_product_websites))
                $last_product_websites = array();
            $last_product_websites[] = $rowData;
            return $this->collection->update(array('sku'=>$this->last_sku), array('$set' => array("_product_websites" => $last_product_websites)));
        }
        // check if row is an Category
        else if (!isset($rowData['sku']) && isset($rowData['_category'])) {
            $last_category = $this->collection->findOne(array('sku' => $this->last_sku))['_category'];
            if (!is_array($last_category))
                $last_category = array();
            $last_category[] = $rowData;
            return $this->collection->update(array('sku'=>$this->last_sku), array('$set' => array("_category" => $last_category)));
        }
        // check if row is an Tier Price
        else if (!isset($rowData['sku']) && isset($rowData['_tier_price_website'])) {
            $last_tier_price_website = $this->collection->findOne(array('sku' => $this->last_sku))['_tier_price_website'];
            if (!is_array($last_tier_price_website))
                $last_tier_price_website = array();
            $last_tier_price_website[] = $rowData;
            return $this->collection->update(array('sku'=>$this->last_sku), array('$set' => array("_tier_price_website" => $last_tier_price_website)));
        }
        // check if row is an Media Image
        else if (!isset($rowData['sku']) && isset($rowData['_media_image'])) {
            $last_media_image = $this->collection->findOne(array('sku' => $this->last_sku))['_media_image'];
            if (!is_array($last_media_image))
                $last_media_image = array();
            $last_media_image[] = $rowData;
            return $this->collection->update(array('sku'=>$this->last_sku), array('$set' => array("_media_image" => $last_media_image)));
        }
        // is product ?
        else {
            if (!array_key_exists('product_id', $rowData) && isset($rowData['sku']))
                $this->last_sku = $rowData['sku'];
                $rowData['product_id'] = Mage::getModel("catalog/product")->getIdBySku($rowData['sku']);
            return $this->collection->insert($rowData);
        }
    }
}
