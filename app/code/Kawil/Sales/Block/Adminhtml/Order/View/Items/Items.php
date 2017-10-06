<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kawil\Sales\Block\Adminhtml\Order\View;


/**
 * Adminhtml order items grid
 */
class Items extends \Magento\Sales\Block\Adminhtml\Order\View\Items
{
    /**
     * @return array
     */
    public function getColumns()
    {
        $columns = array_key_exists('columns', $this->_data) ? $this->_data['columns'] : [];
        return $columns;
    }
}
