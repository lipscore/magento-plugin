<?php

class Lipscore_RatingsReviews_Model_System_Config_Source_Productattr_Id
{
    public function toOptionArray()
    {
        $attrs = array(
            array('value' => 'id', 'label' => Mage::helper('adminhtml')->__('ID')),
            array('value' => 'sku', 'label' => Mage::helper('adminhtml')->__('SKU')),
        );

        try {
            $attrs = array_merge($attrs, $this->_findAttrs());
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }

        return $attrs;
    }

    protected function _findAttrs()
    {
        $attrs = array();

        $collection = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addStoreLabel(Mage::app()->getStore()->getId())
            ->addFieldToFilter('frontend_input', array('in' => array('text', 'select', 'textarea')))
            ->addVisibleFilter();

        if ($collection->getSize() > 0) {
            $excludedAttrs = array('sku', 'id');
            foreach ($collection->getItems() as $attr) {
                if (in_array($attr->getAttributeCode(), $excludedAttrs)) {
                    continue;
                }

                $attrs[] = array(
                    'value' => $attr->getAttributeCode(),
                    'label' => $attr->getStoreLabel()
                );
            }
        }

        return $attrs;
    }
}
