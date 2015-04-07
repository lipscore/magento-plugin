<?php
 
class Lipscore_RatingsReviews_Model_System_Config_Source_Productattr_Brand
{
    public function toOptionArray()
    {
        $attrs = array(
            array('value' =>'', 'label' => Mage::helper('adminhtml')->__('--Please Select--'))
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
