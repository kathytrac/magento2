<?php
/*------------------------------------------------------------------------
 # SM Super Categories - Version 2.1.0
# Copyright (c) 2015 YouTech Company. All Rights Reserved.
# @license - Copyrighted Commercial Software
# Author: YouTech Company
# Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

namespace Sm\SuperCategories\Model\Config\Source;

class Target implements \Magento\Framework\Option\ArrayInterface
{
	public function toOptionArray()
	{
		return [
			['value' => '_self', 'label' => __('Same Window')],
			['value' => '_blank', 'label' => __('New Window')],
			['value' => '_windowopen', 'label' => __('Popup Window')]
		];
	}
}