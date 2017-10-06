<?php
/**
 * Created by:  Milan Simek
 * Company:     Plugin Company
 *
 * LICENSE: http://plugin.company/docs/magento-extensions/magento-extension-license-agreement
 *
 * YOU WILL ALSO FIND A PDF COPY OF THE LICENSE IN THE DOWNLOADED ZIP FILE
 *
 * FOR QUESTIONS AND SUPPORT
 * PLEASE DON'T HESITATE TO CONTACT US AT:
 *
 * SUPPORT@PLUGIN.COMPANY
 */
namespace PluginCompany\ContactForms\Model\Template\FilterObjects;

use Magento\Product\Model\Session;
use Magento\Framework\Registry;
use Magento\Framework\DataObject;

class Product extends DataObject
{
    private $registry;
    private $product;

    public function __construct(
        Registry $registry
    ){
        $this->registry = $registry;
        return $this;
    }

    public function __call($method, $args)
    {
        if(substr($method, 0, 3) == 'get') {
            $key = $this->_underscore(substr($method, 3));
            if($this->getProductResource() && $this->isAttribute($key)){
                return $this->getAttributeText($key);
            }
        }
        return $this->getProduct()->{$method}($args);
    }

    public function isAttribute($key)
    {
        return $this->getProductResource()->getAttribute($key);
    }

    public function getProductResource()
    {
        return $this->getProduct()->getResource();
    }

    public function getAttributeText($attributeCode)
    {
        $data = $this->getProduct()->getData($attributeCode);
        if(!$this->isAttrSelect($attributeCode)){
            return $data;
        }
        $text = $this->getProduct()->getAttributeText($attributeCode);
        if(is_array($text)){
            $text = implode(', ', $text);
        }
        return $text;
    }

    public function isAttrSelect($key){
        if($key == 'store_id'){
            return false;
        }
        $input = $this
            ->getResource()
            ->getAttribute($key)
            ->getFrontendInput()
        ;
        return in_array($input,array('select','multiselect'));
    }

    public function getProduct()
    {
        if(!$this->product){
            $this->initProduct();
        }
        return $this->product;
    }

    public function initProduct()
    {
        $product = $this->registry->registry('product');
        if (!$product || !$product->getId()) {
            $product = new DataObject();
        }
        $this->product = $product;
        return $this;
    }
}
