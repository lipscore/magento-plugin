<?php
 
class Lipscore_RatingsReviews_Model_System_Config_Source_IdentifierType
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'ean', 'label' => Mage::helper('adminhtml')->__('EAN')),
            array('value' => 'mpn', 'label' => Mage::helper('adminhtml')->__('MPN')),
            array('value' => 'sku', 'label' => Mage::helper('adminhtml')->__('SKU')),
            array('value' => 'upc', 'label' => Mage::helper('adminhtml')->__('UPC')),
        );
    }
}
