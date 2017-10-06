<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kawil\Sales\Block\Adminhtml\Order\View\Items\Renderer;

use Magento\Sales\Model\Order\Item;

/**
 * Adminhtml sales order item renderer
 */
class DefaultRenderer extends \Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer
{
    /**
     * @var \Kawil\Catalog\Model\Product\Attribute\Source\Lieferant
     */
    protected $lieferantOptions;
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\GiftMessage\Helper\Message $messageHelper,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Kawil\Catalog\Model\Product\Attribute\Source\Lieferant $lieferant,
        \Magento\Eav\Model\Config $eavConfig,
        array $data = []
    )
    {
        $this->lieferantOptions = $lieferant;
        $this->eavConfig = $eavConfig;
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $messageHelper, $checkoutHelper, $data);
    }

    /**
     * @param \Magento\Framework\DataObject|Item $item
     * @param string $column
     * @param null $field
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getColumnHtml(\Magento\Framework\DataObject $item, $column, $field = null)
    {
        $html = '';
        switch ($column) {
            case 'product':
                if ($this->canDisplayContainer()) {
                    $html .= '<div id="' . $this->getHtmlId() . '">';
                }
                $html .= $this->getColumnHtml($item, 'name');
                if ($this->canDisplayContainer()) {
                    $html .= '</div>';
                }
                break;
            case 'status':
                $html = $item->getStatus();
                break;
            case 'price-original':
                $html = $this->displayPriceAttribute('original_price');
                break;
            case 'tax-amount':
                $html = $this->displayPriceAttribute('tax_amount');
                break;
            case 'tax-percent':
                $html = $this->displayTaxPercent($item);
                break;
            case 'discont':
                $html = $this->displayPriceAttribute('discount_amount');
                break;
            case 'cost':
                $html = $this->getCostOfTierPrice($item);
                break;
            case 'kawil_lieferant':
                $lieferantProduct = $this->getLabelOfOption($item->getProduct()->getData('kawil_lieferant'),'kawil_lieferant');
                $html = $lieferantProduct;
                break;
            case 'kawil_alternative_lieferantent':
                $altLieferantProduct = $this->getLabelOfOption($item->getProduct()->getData('kawil_alternative_lieferantent'),'kawil_alternative_lieferantent');
                $html =$altLieferantProduct;
                break;
            default:
                $html = parent::getColumnHtml($item, $column, $field);
        }
        return $html;
    }
    /**
     * @return array
     */
    public function getColumns()
    {
        $columns = array_key_exists('columns', $this->_data) ? $this->_data['columns'] : [];
        foreach ($columns as $columnName =>$columnTitle){
            if($columnName == 'tax-amount' || $columnName == 'tax-percent')
            {
                unset($columns[$columnName]);
            }
        }
        return $columns;
    }

    /**
     * @param \Magento\Framework\DataObject|Item $item
     */
    public function getCostOfTierPrice($item){
        $qtyOrdered = $item->getQtyOrdered();
        $tierPrices = $item->getProduct()->getTierPrices();
        $cost = $item->getPrice();
        foreach ($tierPrices as $tierPrice){
            if($qtyOrdered >= $tierPrice->getQty()){
                if($tierPrice->getCost()) {
                    $cost = $tierPrice->getCost();
                }
            }
        }
        return $this->displayRoundedPrices($cost,$cost,$this->getOrder()->getRowTaxDisplayPrecision());

    }
    /**
     * Get label of option (s)
     *
     * @param $value
     * @return mixed|null
     */
    public function getLabelOfOption($value,$attributeCode){
        if(is_null($value)){
            return null;
        }
        $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeCode);
        $options = $attribute->getSource()->getAllOptions();
        $values = explode(',', $value);
        $arrOption = [];
        foreach ($options as $option) {
            if(!($option['label'] == "" and $option['value'] == "")){
                $arrOption[$option['value']] = $option['label'];
            }
        }
        $result = array_intersect_key($arrOption, array_flip($values));
        if(is_array($result)){
            return implode(', ',$result);
        }
        return null;
    }
}
