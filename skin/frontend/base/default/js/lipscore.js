function magentoLipscoreInit() {
    tabSelector = '.lipscore-reviews-tab';
    if ($$(tabSelector).length > 0) {
        // show review count
        lipscore.on('review-count-set', function(data) {
            if (data.value > 0) {
                $$('.lipscore-reviews-tab-count').invoke('show');
            } 
        });
        
        // open reviews tab if reviews link clicked            
        lipscore.on('review-count-link-clicked', function(data) {
            $$(tabSelector)[0].up().click();
        });
    }
}

document.observe('dom:loaded', function() {
    if (typeof lipscore !== 'undefined') {
        magentoLipscoreInit();
    } else {
        document.observe('lipscore-created', magentoLipscoreInit);
    }
});
