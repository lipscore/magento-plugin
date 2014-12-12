<?php
 
class Lipscore_RatingsReviews_Model_System_Config_Source_Identifiertype
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'ean',  'label' => Mage::helper('adminhtml')->__('EAN')),
            array('value' => 'isbn', 'label' => Mage::helper('adminhtml')->__('ISBN')),
            array('value' => 'mbid', 'label' => Mage::helper('adminhtml')->__('MBID')),
            array('value' => 'mpn',  'label' => Mage::helper('adminhtml')->__('MPN')),
            array('value' => 'sku',  'label' => Mage::helper('adminhtml')->__('SKU')),
            array('value' => 'upc',  'label' => Mage::helper('adminhtml')->__('UPC')),
        );
    }
}
