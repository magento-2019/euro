<?php
/**
 *
 * CART2QUOTE CONFIDENTIAL
 * __________________
 *
 *  [2009] - [2016] Cart2Quote B.V.
 *  All Rights Reserved.
 *
 * NOTICE OF LICENSE
 *
 * All information contained herein is, and remains
 * the property of Cart2Quote B.V. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to Cart2Quote B.V.
 * and its suppliers and may be covered by European and Foreign Patents,
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Cart2Quote B.V.
 *
 * @category    Ophirah
 * @package     Fakepro
 * @copyright   Copyright (c) 2016 Cart2Quote B.V. (http://www.cart2quote.com)
 * @license     http://www.cart2quote.com/ordering-licenses
 * @version     1.0.5
 */

/**
 * @since 1.0.5
 * Class Ophirah_Fakepro_Helper_Data
 */

final class Ophirah_Fakepro_Helper_Data extends Mage_Core_Helper_Abstract
{
    const NUM_OF_DEFAULT_OPTIONS = 3;

    /**
     * Default product settings
     */
    const PRODUCT_NAME = 'Custom Product';
    const PRODUCT_SKU = 'quote-product-custom';
    const PRODUCT_DESCRIPTION = 'This is Quote product that is customisable, please don\'t remove this product.';
    const PRODUCT_DESCRIPTION_SHORT = 'Quote product that is customisable';
    const PRODUCT_TYPE = 'simple';

    /**
     * Option types
     */
    const NAME = 'Name';
    const SKU = 'SKU';
    const DESCRIPTION = 'Description';
    const IMAGE = 'Product Image';

    /**
     * Tax types
     */
    const TAX_NONE      = 0;
    const TAX_DEFAULT   = 1;
    const TAX_TAXABLE   = 2;
    const TAX_SHIPPING  = 3;

    /**
     * This function creates a simple product to manage the fake product
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Product|string
     * @since 1.0.5
     */
    public function createProduct(Mage_Catalog_Model_Product $product){
        try{
            $attributeSetId = Mage::getModel('catalog/product')->getDefaultAttributeSetId();

            $product
                   /*->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID)*/
                ->setWebsiteIds(array_keys(Mage::app()->getWebsites()))
                ->setAttributeSetId($attributeSetId)
                ->setTypeId(self::PRODUCT_TYPE)
                ->setCreatedAt(now())
                ->setUpdatedAt(now())
                ->setSku(self::PRODUCT_SKU)
                ->setName(self::PRODUCT_NAME)
                ->setWeight(1.0000)
                ->setStatus(1)
                ->setTaxClassId(self::TAX_TAXABLE)
                ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE)
                ->setPrice(0.00)
                ->setDescription(self::PRODUCT_DESCRIPTION)
                ->setShortDescription(self::PRODUCT_DESCRIPTION_SHORT)
                ->setMediaGallery (array('images'=> array(), 'values'=> array()))
                ->setStockData(array(
                        'use_config_manage_stock' => 0,
                        'manage_stock'=>0,
                        'is_in_stock' => 1,
                        'qty' => 9999
                    )
                )->save();
            return $product;
        }catch(Exception $e){
            Mage::log($e->getMessage());
            return $e->getMessage();
        }
    }

    /**
     * Add default options to product
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Product|string
     * @since 1.0.5
     */
    public function addCustomOptionsToProduct(Mage_Catalog_Model_Product $product)  {
        $product->setHasOptions(1)->setRequiredOptions(1);
        $product->getResource()->save($product);
        foreach($this->_getDefaultOptionSettings() as $optionSettings){
            try{
                Mage::getModel('catalog/product_option')
                    ->addOption($optionSettings)
                    ->setProduct($product)
                    ->saveOptions();
            }catch(Exception $e){
                //TODO: log to own file
                Mage::log($e->getMessage());
                //return $e->getMessage();
            }
        }
        return $product;
    }

    /**
     * Adds a product to a Qquoteadv quote.
     * @param $product Mage_Catalog_Model_Product
     * @param $c2qId
     * @param $params
     * @since 1.0.5
     * @return bool
     */
    public function addProductToQuote(Mage_Catalog_Model_Product $product, $c2qId, $params)  {
        $product = Mage::getModel('catalog/product')->load($product->getEntityId());

        $paramsObj = new Varien_Object($params);
        if(!$paramsObj->hasData('qty')){
            $paramsObj->setQty(1);
        }

        $error = $product->getTypeInstance(true)->prepareForCartAdvanced($paramsObj, $product);

        $this->_getCustomerSession()->setQuoteadvId($c2qId);
        if(!is_string($error)){
            $error = $this->_create(
                $this->_prepareProductParams($product, $paramsObj),
                $this->_prepareOptionalAttributes($product, $paramsObj)
            );
        }
        return $error;
    }

    /**
     * Insert quote data
     * @param $params = array(
     * 'product' => $item->getProductId(),
     * 'qty'     => $item->getQty(),
     * 'price'   => $item->getPrice()
     * 'original_price' => $item->getProduct()->getPrice();
     * );
     * @param string $superAttribute
     * @return $this
     * @since 1.0.5
     */
    protected function _create($params, $superAttribute)
    {
        $modelProduct = Mage::getModel('qquoteadv/qqadvproduct');
        $options = $this->_prepareProductOptions($params, $superAttribute);
        $params = $this->_prepareQty($params, $superAttribute);
        $quoteId = $this->_getCustomerSession()->getQuoteadvId();
        $rate = $this->_getRate($params);

        try {
            $modelProduct
                ->setQuoteId($quoteId)
                ->setProductId($params['product_id'])
                ->setQty($params['qty'])
                ->setAttribute($superAttribute)
                ->setHasOptions($this->_hasOptions($options))
                ->setOptions($options)
                ->setUseDiscount($params['use_discount'])
                ->setStoreId(Mage::getSingleton('adminhtml/session_quote')->getStoreId())
                ->save();

            Mage::getModel('qquoteadv/requestitem')
                ->setQuoteId($quoteId)
                ->setProductId($params['product_id'])
                ->setRequestQty($params['qty'])
                ->setOwnerBasePrice($params['custom_price'] / $rate)
                ->setOwnerCurPrice($params['custom_price'])
                ->setOriginalPrice($params['original_price'])
                ->setOriginalCurPrice($params['original_price'] * $rate)
                ->setQuoteadvProductId($modelProduct->getId())->save();

        } catch (Exception $e) {
            Mage::log($e->getMessage());
            return $e->getMessage();
        }

        return $this;
    }

    /**
     * Retrieves the fake product
     * @return false|Mage_Catalog_Model_Product
     * @since 1.0.5
     */
    public function getFakeProduct(){
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        $product = Mage::getModel('catalog/product');

        if(!$product->getIdBySku(self::PRODUCT_SKU)){
            $product = $this->createProduct($product);
            if(!is_string($product)){
                $product = $this->addCustomOptionsToProduct($product);
            }
        } else {
            $product = Mage::getModel('catalog/product')->loadByAttribute('sku', self::PRODUCT_SKU);
        }
        return $product;
    }

    /**
     * Prepares the form post data for product creation
     * @param $params
     * @param int $qty
     * @return array|bool
     * @since 1.0.5
     */
    public function prepareParams($params, $qty = 1){
        if(array_key_exists('options', $params)){
            if(array_key_exists('qty', $params)){
                $qty = $params['qty'];
            }
            $newParams = array(
                'qty' => $qty,
                'options' => $params['options']
            );
            $fileOptions = $this->getFileDownloadOptions($params);
            $newParams = array_merge($newParams, $fileOptions);
            return $newParams;
        }
        return false;
    }

    /**
     * @param $params
     * @return array
     * @since 1.0.5
     */
    public function getFileDownloadOptions($params){
        $newParams = array();
        foreach($params as $paramKey => $paramValue){
            if($paramValue == 'save_new'){
                $newParams[$paramKey] = $paramValue;
            }
        }
        return $newParams;
    }

    /**
     * Creates an object by product options with the basic product attributes.
     *
     * @param $buyRequest
     * @return Varien_Object
     *          Product
     *          @method getName
     *          @method getSku
     *          @method getType
     *
     *          Image
     *          @method getTitle
     *          @method getImageUrl - Recommend for HTML
     *          @method getQuotePath
     *          @method getOrderPath
     *          @method getFullpath - Recommend for PDF
     *          @method getSize
     *          @method getWidth
     *          @method getSize
     *          @method getSecretKey
     *
     * @since 1.0.5
     */
    public function getProductOptionValues($buyRequest){
        $options = new Varien_Object();
        $attributes = unserialize($buyRequest);

        $product = $this->getFakeProduct();
        $buyRequest = new Varien_Object($attributes);
        $product->setCustomOptions($product->processBuyRequest($buyRequest)->getOptions());

        foreach($product->getProductOptionsCollection() as $option){
            switch($option->getTitle()){
                case self::NAME:
                case self::SKU:
                case self::DESCRIPTION:
                case self::IMAGE:
                    $customOption = $product->getCustomOption($option->getOptionId());
                    if(is_string($customOption)){
                        $customOption = array($this->formatOptionTitle($option->getTitle()) => $customOption);
                    }
                    if(is_array($customOption)){
                        $options->addData($customOption);
                    }
                default; // Do nothing
            }
        }
        $options = $this->prepareProductImageUrl($product, $options);
        return $options;
    }

    /**
     * Formats a string to be compatible with Magento's get methods.
     * e.g. getProductName();
     * @param $optionTitle
     * @return mixed|string
     * @since 1.0.5
     */
    protected function formatOptionTitle($optionTitle){
        if(is_string($optionTitle)){
            $optionTitle = str_replace(' ', '_', $optionTitle);
            $optionTitle = strtolower($optionTitle);
        }
        return $optionTitle;
    }

    /**
     * Getter for the custom product name
     * @return string
     */
    public function getProductName(){
        return self::PRODUCT_NAME;
    }

    /**
     * Sets the product image path to use in the front-end
     * @param $options
     * @param Mage_Catalog_Model_Product $product
     * @since 1.0.5
     */
    protected function prepareProductImageUrl($product, $options){
        try{
            $imagePath = $this->saveImageToTmp($options->getFullpath());
        }catch(Exception $e){
            Mage::log("[Ophirah_Fakepro] Error when preparing product image Image URL: ".$e->getMessage());
        }
        if(!empty($imagePath) && is_string($imagePath)){
            $pathArray = explode('/', $imagePath);
            $newUrl = array();
            $buildUrl = false;
            foreach($pathArray as $pathBit){
                if($buildUrl){
                    $newUrl[] = $pathBit;
                }
                if($pathBit == 'media'){
                    $buildUrl = true;
                }
            }
            $newUrl = implode('/', $newUrl);
            $imagePath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA, true).$newUrl;
        }else{
            try{
                $imagePath = Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(75);
            }catch(Exception $e){ // If error, no image will be set.
                if($e->getMessage() != 'Image file was not found.'){
                    Mage::log("[Ophirah_Fakepro] Error when preparing product image Image URL: ".$e->getMessage());
                }
                $imagePath = '';
            }
        }
        $options->setImageUrl($imagePath);
        return $options;
    }

    /**
     * @param $fullImagePath
     * @return string
     * @throws Exception
     */
    protected function saveImageToTmp($fullImagePath){
        if(is_file($fullImagePath)){
            $path = pathinfo($fullImagePath);
            $newDir = str_replace('media/', 'media/fake_products/', $path['dirname']);
            if(!is_dir($newDir)){
                if(!mkdir($newDir, 0777, true)){
                    throw new Exception('Error creating new product option dir.');
                }
            }
            if(is_dir($newDir) && !copy($fullImagePath, $newDir.$path['basename'])){
                throw new Exception('Error copying product option image.');
            }else{
                return $newDir.$path['basename'];
            }
        }
        return ''; // invalid url
    }

    /**
     * Unset's default product options.
     * (Otherwise you will see multiple times the values)
     * @param $options
     * @return array
     * @since 1.0.5
     */
    public function filterOptions(array $options){
        $count = 0;
        foreach($options as $key => $option){
            if($count > self::NUM_OF_DEFAULT_OPTIONS){
                break; // break if all default options are removed.
            }
            $label = $this->getOptionLabel($option, $key);
            if(!$this->isOptionAllowed($label)){
                unset($options[$key]);
                $count++;
            }
        }
        return $options;
    }

    /**
     * @param $option
     * @param $key
     * @return string
     * @since 1.0.5
     */
    protected function getOptionLabel($option, $key){
        if(is_array($option) && array_key_exists('label', $option)){
            $label = $option['label'];
        }else{
            $label = $key;
        }
        return $label;
    }

    /**
     * Checks if option is allowed
     * @param $label
     * @return bool
     * @since 1.0.5
     */
    public function isOptionAllowed($label){
        switch ($label) {
            case Ophirah_Fakepro_Helper_Data::IMAGE:
            case Ophirah_Fakepro_Helper_Data::NAME:
            case Ophirah_Fakepro_Helper_Data::SKU:
            return false;
            default:
            return true;
        }
    }

    /**
     * Checks if product ID is same as fakeProduct product ID
     * @param $productId
     * @return bool
     * @since 1.0.5
     */
    public function isFakeProduct($productId){
        $product = Mage::getModel('catalog/product')->load($productId);
        if(isset($product)){
            if($product->getSku() == self::PRODUCT_SKU){
                return true;
            }
        }

        return false;
    }

    /**
     * Get default option settings
     * @return array
     * @since 1.0.5
     */
    protected function _getDefaultOptionSettings(){
        return array(
            array(
                'title'         => self::NAME,
                'type'          => Mage_Catalog_Model_Product_Option::OPTION_TYPE_FIELD,
                'is_require'    => 1,
                'sort_order'    => 1
            ),
            array(
                'title'         => self::SKU,
                'type'          => Mage_Catalog_Model_Product_Option::OPTION_TYPE_FIELD,
                'is_require'    => 0,
                'sort_order'    => 2
            ),
            array(
                'title'         => self::DESCRIPTION,
                'type'          => Mage_Catalog_Model_Product_Option::OPTION_TYPE_AREA,
                'is_require'    => 0,
                'sort_order'    => 3
            ),
            array(
                'title'         => self::IMAGE,
                'type'          => Mage_Catalog_Model_Product_Option::OPTION_TYPE_FILE,
                'file_extension'=> 'jpg, jpeg, png',
                'is_require'    => 0,
                'sort_order'    => 4
            )
        );
    }

    /**
     * Get customer session data
     * @return Mage_Customer_Model_Session
     * @since 1.0.5
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Check if options is not an empty string
     * @param $options
     * @return bool
     * @since 1.0.5
     */
    protected function _hasOptions($options){
        $hasOptions = true;
        if($options == ''){
            $hasOptions = false;
        }
        return $hasOptions;
    }

    /**
     * If possible, prepare product options
     * @param $params
     * @param $superAttribute
     * @return array
     * @since 1.0.5
     */
    protected function _prepareProductOptions($params, $superAttribute)
    {
        $options = '';
        if (isset($params['options'])) {
            $options = serialize($params['options']);
        } elseif (isset($superAttribute)) {
            $attr = unserialize($superAttribute);
            if (isset($attr['options'])) {
                $options = serialize($attr['options']);
            }
        }
        return $options;
    }

    /**
     * Set qty if qty exists in super attribute
     * @param $params
     * @param $superAttribute
     * @return mixed
     * @since 1.0.5
     */
    protected function _prepareQty($params, $superAttribute)
    {
        $attr = unserialize($superAttribute);
        if (isset($attr['qty'])) {
            $params['qty'] = $attr['qty'];
            return $params;
        }
        return $params;
    }

    /**
     * Get product rate
     * @param $params
     * @return array
     * @since 1.0.5
     */
    protected function _getRate($params)
    {
        $rate = (isset($params['base_quote_rate'])) ? $params['base_quote_rate'] : 1;
        return $rate;
    }

    /**
     * Prepares the product attributes
     * @param Mage_Catalog_Model_Product $product
     * @param $paramsObj
     * @return string
     * @since 1.0.5
     */
    protected function _prepareOptionalAttributes(Mage_Catalog_Model_Product $product, $paramsObj)
    {
        $superAttribute = $product->getTypeInstance(true)->getOrderOptions($product);
        $optionalAttributes = '';
        if (isset($superAttribute['info_buyRequest'])) {
            if (isset($superAttribute['info_buyRequest']['uenc'])) {
                unset($superAttribute['info_buyRequest']['uenc']);
            }
            $superAttribute['info_buyRequest']['product'] = $product->getEntityId();
            $superAttribute['info_buyRequest']['qty'] = $paramsObj->getQty();

            return serialize($superAttribute['info_buyRequest']);
        }
        return $optionalAttributes;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param $paramsObj
     * @return array
     * @since 1.0.5
     */
    protected function _prepareProductParams(Mage_Catalog_Model_Product $product, $paramsObj)
    {
        $params = array(
            'product_id' => $product->getEntityId(),
            'qty' => $paramsObj->getQty(),
            'price' => '0.00',
            'custom_price' => '0.00',
            'original_price' => '0.00',
            'base_quote_rate' => '1.00',
            'use_discount' => 0//,
        );
        return $params;
    }

    /**
     * Accept option value and return its formatted view
     *
     * @param mixed $optionValue
     * Method works well with these $optionValue format:
     *      1. String
     *      2. Indexed array e.g. array(val1, val2, ...)
     *      3. Associative array, containing additional option info, including option value, e.g.
     *          array
     *          (
     *              [label] => ...,
     *              [value] => ...,
     *              [print_value] => ...,
     *              [option_id] => ...,
     *              [option_type] => ...,
     *              [custom_view] =>...,
     *          )
     *
     * @return array
     */
    public function getFormatedOptionValue($optionValue)
    {
        /* @var $helper Mage_Catalog_Helper_Product_Configuration */
        $helper = Mage::helper('catalog/product_configuration');
        $params = array(
            'max_length' => 55,
            'cut_replacer' => ' <a href="#" class="dots" onclick="return false">...</a>'
        );
        return $helper->getFormattedOptionValue($optionValue, $params);
    }
}
