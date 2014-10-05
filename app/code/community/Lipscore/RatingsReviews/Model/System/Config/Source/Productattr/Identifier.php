<?php
 
class Lipscore_RatingsReviews_Model_System_Config_Source_Productattr_Identifier
{
    public function toOptionArray()
    {
        $attrs = array();
        $collection = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addStoreLabel(Mage::app()->getStore()->getId())
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
