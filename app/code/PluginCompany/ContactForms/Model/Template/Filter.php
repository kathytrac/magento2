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
namespace PluginCompany\ContactForms\Model\Template;

use Magento\Cms\Model\Template\Filter as CmsFilter;
use Magento\Framework\Registry;
use Magento\Framework\DataObject;
use PluginCompany\ContactForms\Model\Template\FilterObjects\Customer;
use PluginCompany\ContactForms\Model\Template\FilterObjects\Product;

class Filter
{

    private $filter;
    private $registry;
    private $customer;
    private $product;

    public function __construct(
        CmsFilter $filter,
        Registry $registry,
        Customer $customer,
        Product $product
    ){
        $this->filter = $filter;
        $this->customer = $customer;
        $this->product = $product;
        $this->registry = $registry;
        return $this;
    }

    public function initFrontEndVariables()
    {
        $this
            ->addCustomerVariables()
            ->addProductVariables()
            ->addCategoryVariables()
        ;
        return $this;
    }

    public function addCustomerVariables()
    {
        $this->setVariables(
            [
                'customer' => $this->getCurrentCustomer(),
                'billing_address' => $this->getCurrentBillingAddress(),
                'shipping_address' => $this->getCurrentShippingAddress()
            ]
        );
        return $this;
    }

    public function getCurrentCustomer()
    {
        return $this->customer;
    }

    public function getCurrentBillingAddress()
    {
        return $this
            ->getCurrentCustomer()
            ->getDefaultBillingAddress();
    }

    public function getCurrentShippingAddress()
    {
        return $this
            ->getCurrentCustomer()
            ->getDefaultShippingAddress();
    }

    public function addProductVariables()
    {
        $this->setVariables(
            ['product' => $this->getCurrentProduct()]
        );
        return $this;
    }

    /**
     * @return Product
     */
    public function getCurrentProduct()
    {
        return $this->product;
    }

    public function addCategoryVariables()
    {
        $this->setVariables(
            ['category' => $this->getCurrentCategory()]
        );
        return $this;
    }

    /**
     * @return Category
     */
    public function getCurrentCategory()
    {
        $category = $this->registry->registry('current_category');
        if (!$category || !$category->getId()) {
            $category = new DataObject();
        }

        return $category;
    }

    public function setVariables($variables)
    {
        $this->filter
            ->setVariables($variables);
        return $this;
    }

    public function filter($string)
    {
        return $this->filter->filter($string);
    }

    public function getFilter()
    {
        return $this->filter;
    }

}


