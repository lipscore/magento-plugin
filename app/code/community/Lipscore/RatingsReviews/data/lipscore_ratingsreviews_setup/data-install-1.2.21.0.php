<?php
try {
    $tracker = Mage::getModel('lipscore_ratingsreviews/tracker_installation');
    foreach (Mage::app()->getWebsites() as $website) {
        $result = $tracker->track($website);
    }
} catch (Exception $e) {
    Lipscore_RatingsReviews_Logger::logException($e);
}
    