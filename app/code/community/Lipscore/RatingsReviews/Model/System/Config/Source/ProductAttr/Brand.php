<?php
 
class Lipscore_RatingsReviews_Model_System_Config_Source_ProductAttr_Brand
{
    public function toOptionArray()
    {
        $attrs = array(
            array('value' =>'', 'label' => Mage::helper('adminhtml')->__('--Please Select--'))
        );

        $collection = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addStoreLabel(Mage::app()->getStore()->getId())
            ->addFieldToFilter('frontend_input', array('in' => array('text', 'select')))
            ->addVisibleFilter();
        
        if ($collection->getSize() > 0) {
            foreach ($collection->getItems() as $attr) {
                $attrs[] = array(
                    'value' => $attr->getAttributeCode(),
                    'label' => $attr->getStoreLabel()
                );
            }
        }
        
        return $attrs;
    }
}
