<?php

class Lipscore_RatingsReviews_Block_System_Config_Form_Field_Reminderperiod
      extends Lipscore_RatingsReviews_Block_System_Config_Form_Field_Abstract
{
    protected static $_statusSelect  = 'order_status';

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $text = '';
        try {
            $text = "<div id='ls-reminder' data-result='{$this->reminderResult()}'>" .
                $this->getNote() .
                $this->getStatusField() .
                $this->getDateFields() .
                $this->previewButton() .
                '</div>' .
                $this->reminderPreview();
        } catch (Exception $e) {
            Lipscore_RatingsReviews_Logger::logException($e);
        }
        return $text;
    }

    public function getLabel($targetId, $title)
    {
        return '<label for="' . $targetId . '" class="ls-reminder-label">' . $this->__($title) . '</label>';
    }

    public function getStatusField()
    {
        $label = $this->getLabel(self::$_statusSelect, '<span>Include orders in these statuses<br/>(Ctrl+click to add more)</span>');

        $opts = Mage::getSingleton('lipscore_ratingsreviews/system_config_source_order_status_reminders')->toOptionArray();

        $select = $this->getLayout()->createBlock('adminhtml/html_select')
            ->setName(self::$_statusSelect . '[]')
            ->setId(self::$_statusSelect)
            ->setClass('select multiselect')
            ->setOptions($opts)
            ->setExtraParams('multiple="multiple"');

        return $label . $select->getHtml();
    }

    public function getDateFields()
    {
        $html  = $this->getLabel($this->dateFieldId('from'), 'Include orders made between');
        $html .= $this->getDateField('from', strtotime("-3 months"));
        $html .= $this->getLabel($this->dateFieldId('to'), 'and');
        $html .= $this->getDateField('to', time());
        return $html;
    }

    public function getDateField($name, $defaultValue)
    {
        $element = new Varien_Data_Form_Element_Date(
            array(
                'name'   => $name,
                'image'  => $this->getSkinUrl('images/grid-cal.gif'),
                'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                'time'   => false
            )
        );
        $element->setValue($defaultValue);
        $element->setForm(new Varien_Data_Form());
        $element->setId($this->dateFieldId($name));

        return $element->getElementHtml();
    }

    public function dateFieldId($name)
    {
        return 'remind_' . $name;
    }


    public function getNote()
    {
        $linkToDasboard = 'https://members.lipscore.com/';
        $linkToCoupons  = Mage::getModel('adminhtml/url')->getUrl('*/*/*', array('section' => 'lipscore_coupons'));

        $msg  = "After installation of Lipscore you can send emails to recent customers asking them to write reviews of the purchases they have done. This is done automatically for all future customers but customers from before Lipscore was installed will not get these emails unless you invoke it below. They will be delivered according to <a href='$linkToDasboard'>reminder delay settings</a> and will only be done once.";
        $coupons = "To increase the chance of getting reviews you can also add coupons to these emails. Set up coupons <a href='$linkToCoupons'>here</a>.";
        $settings = "<p>NOTE: Please make sure that your email settings are set up correctly in your <a href='$linkToDasboard'>Lipscore Dashboard</a> before invoking the Kickstart-feature!</p>";
        $heading = '<h4>Send review emails to these customers:</h4>';
        return "<p>$msg $coupons<p>$settings $heading";
    }

    public function previewButton()
    {
        $url = $this->getReminderUrl('preview');
        $apiKey = $this->getLipscoreConfig()->apiKey();
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => $this->__('Send emails'),
                'disabled'  => empty($apiKey),
                'title'     => $this->__('Send emails'),
                'onclick'   => "previewLsReminder('$url');",
                'id'        => 'ls-reminder-preview-button'
            ));
        return $button->toHtml();
    }

    protected function sendButton()
    {
        $url = $this->getReminderUrl('send');
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => $this->__('Continue'),
                'title'     => $this->__('Continue'),
                'onclick'   => "sendLsReminder('$url');",
                'id'        => 'ls-reminder-send-button'
            ));
        return $button->toHtml();
    }

    protected function cancelButton()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => $this->__('Cancel'),
                'title'     => $this->__('Cancel'),
                'onclick'   => 'closeLsPreviewPopup();',
                'id'        => 'ls-reminder-cancel-button'
            ));
        return $button->toHtml();
    }

    protected function reminderPreview()
    {
        $cancelLabel = $this->__('Cancel');
        $popup =
<<<EOT
    <div id='message-popup-window-mask' class='ls-preview-popup-mask' style='display:none;'></div>
    <div id='message-popup-window' class='message-popup ls-preview-popup'>
        <div class='message-popup-head'>
            <a href='#' onclick='closeLsPreviewPopup(); return false;' title='$cancelLabel'><span>$cancelLabel</span></a>
            <h2>Kick-start summary</h2>
        </div>
        <div class='message-popup-content'>
            <div class='message'>
                <p id='ls-reminder-preview-text'></p>
            </div>
            {$this->cancelButton()}{$this->sendButton()}
        </div>
    </div>
EOT;
        return $popup;
    }

    protected function getReminderUrl($action)
    {
        $scopeParams = "section/{$this->getSection()}/website/{$this->getWebsite()}/store/{$this->getStore()}";
        return Mage::getModel('adminhtml/url')->getUrl("*/purchases_reminders/$action/$scopeParams");
    }

    protected function reminderResult()
    {
        $config = Mage::getModel('lipscore_ratingsreviews/config_kickstart');
        $resultJson = $config->resultJson();
        if (empty($resultJson)) {
            return null;
        }

        try {
            $result = Zend_Json::decode($resultJson);
            if ($result['completed']) {
                $config->clearResult();
            } else {
                $timeSinceLastUpdate = time() - (int) $result['updated_at'];
                if ($timeSinceLastUpdate > (5 * 60)) {
                    $resultJson = null;
                }
            }
        } catch (Exception $e) {
            $resultJson = null;
            Lipscore_RatingsReviews_Logger::logException($e);
        }

        return $resultJson;
    }
}
