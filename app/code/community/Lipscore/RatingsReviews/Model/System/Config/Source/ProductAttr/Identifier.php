<?php
 
class Lipscore_RatingsReviews_Model_System_Config_Source_ProductAttr_Identifier
{
    public function toOptionArray()
    {
        $attrs = array(
            array('value' =>'', 'label' => Mage::helper('adminhtml')->__('--Please Select--'))
        );
        $collection = Mage::getResourceModel('catalog/product_attribute_collection')
            ->setFrontendInputTypeFilter('text')
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
