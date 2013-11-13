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

    protected function normalize($category, $store_code)
    {
        $root_category_name = $category['_root_category'];
        $category_names = explode("/", $$category_string);

        $category_ids = array();


        $categoryModel = Mage::getModel('catalog/category');
        


        //$this->getRootCategoryByName($store_code, $root_category_name);


        for ($i=0; $i < count($category_names); $i++) {

            $_category = $categoryModel->loadByAttribute('name', $category_names[$i]);
            if(isset($_category)) {
                print_r(get_class_methods($_category)."\n".var_dump($_category)."\n");
                $category_ids[$category_names[$i]] = $_category->getId();
                print($category_ids[$category_names[$i]]." => ".$category_names[$i]."\n");
            } else {
                print("can't get category id by ".$category_names[$i]."\n");
            }
        }


        /*
        $parent = Mage::app()->getStore()->getRootCategoryId();
        
        $category->load($parentID)->addFieldToFilter('name', $category_names[$i]);

        $children = $category->getCategories($parent);

        foreach ($children as $category)
        {
              echo $category->getName(); // will return category name 
              echo $category->getRequestPath(); // will return category URL
        }

        for ($i=0; $i < count($category_names); $i++) {
            $category = Mage::getResourceModel('catalog/category');

            if ($i <= 0) {
                $category->addFieldToFilter('name', $category_names[$i]);
                $parentID = 
            } else {
                
            }
        }*/
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
     * @param array $currentRow
     * @throws Exception
     * @return product
     */
    public function writeRow(array $currentRow)
    {
        print ("writeRow\n");

        // check if row is an StoreView
        if (!isset($currentRow['sku']) && isset($currentRow['_store'])) {
            $last_store = $this->collection->findOne(array('sku' => $this->last_sku))['_store'];
            if (!is_array($last_store))
                $last_store = array();
            $last_store[$currentRow['_store']] = $currentRow;
            return $this->collection->update(array('sku'=>$this->last_sku), array('$set' => array("_store" => $last_store)));
        }
        // check if row is an Website
        else if (!isset($currentRow['sku']) && isset($currentRow['_product_websites'])) {
            $last_product_websites = $this->collection->findOne(array('sku' => $this->last_sku))['_product_websites'];
            if (!is_array($last_product_websites))
                $last_product_websites = array();
            $last_product_websites[] = $currentRow;
            return $this->collection->update(array('sku'=>$this->last_sku), array('$set' => array("_product_websites" => $last_product_websites)));
        }
        // check if row is an Category
        else if (!isset($currentRow['sku']) && isset($currentRow['_category'])) {

            //$currentRow['_category'] = $this->transformCategoryString($currentRow['_category'], $currentRow['_store']);

            $last_category = $this->collection->findOne(array('sku' => $this->last_sku))['_category'];
            if (!is_array($last_category))
                $last_category = array();
            $last_category[] = $currentRow;
            return $this->collection->update(array('sku'=>$this->last_sku), array('$set' => array("_category" => $last_category)));
        }
        // check if row is an Tier Price
        else if (!isset($currentRow['sku']) && isset($currentRow['_tier_price_website'])) {
            $last_tier_price_website = $this->collection->findOne(array('sku' => $this->last_sku))['_tier_price_website'];
            if (!is_array($last_tier_price_website))
                $last_tier_price_website = array();
            $last_tier_price_website[] = $currentRow;
            return $this->collection->update(array('sku'=>$this->last_sku), array('$set' => array("_tier_price_website" => $last_tier_price_website)));
        }
        // check if row is an Media Image
        else if (!isset($currentRow['sku']) && isset($currentRow['_media_image'])) {
            $last_media_image = $this->collection->findOne(array('sku' => $this->last_sku))['_media_image'];
            if (!is_array($last_media_image))
                $last_media_image = array();
            $last_media_image[] = $currentRow;
            return $this->collection->update(array('sku'=>$this->last_sku), array('$set' => array("_media_image" => $last_media_image)));
        }
        // check if row is an Cross Sell
        else if (!isset($currentRow['sku']) && isset($currentRow['_links_crosssell_sku'])) {
            $lastRow = $this->collection->findOne(array('sku' => $this->last_sku));
            $last_links_crosssell_sku = $lastRow['_links_crosssell_sku']; // TODO remove this attribute
            $last_links_crosssell_position = $lastRow['_links_crosssell_position']; // TODO remove this attribute
            
            // if links_crosssell exists, get
            if (isset($lastRow['links_crosssell']) && is_array($lastRow['links_crosssell'])) {
                $newRow = $lastRow['links_crosssell'];
            // if links_crosssell not exists, create new and insert existing values
            } else {
                $newRow = array();
                $newRow[] = array("_links_crosssell_sku" => $last_links_crosssell_sku, "_links_crosssell_position" => $last_links_crosssell_position, "product_id" =>  intval(Mage::getModel("catalog/product")->getIdBySku($last_links_crosssell_sku)));
            }
            // Append current links_crosssell
            $currentRow['product_id'] = intval(Mage::getModel("catalog/product")->getIdBySku($currentRow['_links_crosssell_sku']));
            $newRow[] = $currentRow;
            return $this->collection->update(array('sku'=>$this->last_sku), array('$set' => array("links_crosssell" => $newRow)));
        }
        // is product ?
        else if (isset($currentRow['sku'])){
            $this->last_sku = $currentRow['sku'];
            $currentRow['product_id'] = intval(Mage::getModel("catalog/product")->getIdBySku($currentRow['sku']));
            $currentRow['_id'] = $currentRow['product_id'];
            $_product = Mage::getModel('catalog/product')->load($currentRow['product_id']);
            $currentRow['category_ids'] = $_product->getCategoryIds();
            return $this->collection->insert($currentRow);
        }
        else {
            $currentRow['error'] = "unknown attribute";
            return $this->collection->insert($currentRow);
        }
    }
}
