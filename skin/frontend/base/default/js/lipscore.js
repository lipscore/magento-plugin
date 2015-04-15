document.observe("dom:loaded", function() {
    if (lipscore) {
        tabSelector = '.lipscore-reviews-tab';
        if ($$(tabSelector)) {
            // show review count
            lipscore.on('review-count-set', function(data) {
                if (data.value > 0) {
                    $$('.lipscore-reviews-tab-count').invoke('show');
                } 
            });
            
            // open reviews tab if reviews link clicked            
            lipscore.on('review-count-link-clicked', function(data) {
                $$(tabSelector).each(function(el) {
                    el.up().click();
                });
            });
        }
    }   
});
