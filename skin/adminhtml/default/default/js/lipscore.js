document.observe('dom:loaded', function () {
    reminder = $('ls-reminder');
    if (reminder) {
        result = reminder.readAttribute('data-result');

        if (!result) {
            return;
        }

        result = JSON.parse(result);
        if (result.completed) {
            msg  = sendingResultMessage(result.stores);
        } else {
            msg = 'Emails scheduling is in progress... ' + result.processed + ' emails have been processed.'
        }
        renderLsReminderMessage('success', msg);
    }
});

function previewLsReminder(url) {
    $statusEl = $('order_status');
    $fromEl   = $('remind_from');
    $toEl     = $('remind_to');

    new Ajax.Request(url, {
        method: 'post',
        parameters: {
            'status[]': $statusEl.getValue(),
            from:       $fromEl.getValue(),
            to:         $toEl.getValue(),
        },
        onSuccess: function(response) {
            var msg = previewMessage(response);
            openLsPreviewPopup(msg);
        },
        onFailure: function(response) {
            showLsReminderMessage(response);
        }
    });
};

function sendLsReminder(url) {
    $statusEl = $('order_status');
    $fromEl   = $('remind_from');
    $toEl     = $('remind_to');

    request = new Ajax.Request(url, {
        method: 'post',
        parameters: {
            'status[]': $statusEl.getValue(),
            from:       $fromEl.getValue(),
            to:         $toEl.getValue(),
        },
        onCreate: function(request) {
            request.timeoutId = window.setTimeout(function() {
                request.transport.abort();
            }, 5000);
        },
        onSuccess: function(response) {
            showLsReminderMessage(response);
        },
        onFailure: function(response) {
            showLsReminderMessage(response);
        },
        onComplete : function(request) {
            window.clearTimeout(request.timeoutId);
        }
    });
    closeLsPreviewPopup();
};

function showLsReminderMessage(response) {
    var txt = '';
    var type = 'error';
    var commonErr = "We're sorry, but something went wrong with reminders, please try again or contact support.<br/><br/>";

    var errMessage = function(response) {
        return commonErr + response.status + ': ' + response.request.url + '.<br/> Params: ' +
               response.request.body + '.<br/> Text: ' + response.responseText;
    };

    switch(response.status) {
    case 200:
        if (response.responseJSON && 'data' in response.responseJSON) {
            txt  = sendingResultMessage(response.responseJSON.data);
            type = 'success';
        } else {
            txt  = errMessage(response);
        }
        break;
    case 403:
        txt = '403 Forbidden';
        break;
    case 404:
        txt = commonErr + '404 Not Found: ' + response.request.url;
        break;
    case 422:
        txt = response.responseJSON.data;
        break;
    case 408:
        txt = 'The server timed out waiting for the request.';
        break;
    case 0:
        txt = 'Emails scheduling is in progress... It might take some time, please refresh page to check results.';
        type = 'success';
        break;
    default:
        txt = errMessage(response);
        break;
    }

    renderLsReminderMessage(type, txt);
}

function renderLsReminderMessage(type, txt) {
    var html = '<ul class="messages"><li class="' + type + '-msg"><ul><li>' + txt + '</li></ul>';
    if (type == 'success') {
        html += '<ul><li><br/>Scheduled emails can be found in <a href="https://members.lipscore.com/purchases">Lipscore Emails page</a></li></ul>';
    }
    html += '</li></ul>';
    $('messages').update(html);
}

function previewMessage(response) {
    var data = response.responseJSON.data;
    var statuses = data.statuses.join(', ');

    html = '<h3>Include orders in statuses:</h3>' + statuses +
        '<h3>Orders from period:</h3>' + data.from + ' - ' + data.to +
        '<h3>Stores:</h3>';

    var storesMessage = resultMessageByStores(data.stores);
    var total = totalEmails(data.stores);
    var totalRowHtml = '<h3>Total:</h3><b>' + total + '</b> emails will be scheduled for sending by clicking Continue.';

    return html + storesMessage + totalRowHtml;
}

function sendingResultMessage(stores) {
    var total = totalEmails(stores);
    var html = '<h3 class="ls-reminder-result-header">' + total + ' emails were scheduled successfully:</h3>';
    var storesMessage = resultMessageByStores(stores);

    return html + storesMessage;
}

function resultMessageByStores(stores) {
    resMsg = function(storeName, isError, msg) {
        var msgClass = isError ? 'ls-reminder-err' : 'ls-reminder-succsess';
        return '<b>' + storeName + ':</b> ' + '<span class="' + msgClass + '">' + msg + '</span>';
    }

    var storeRows = [];
    var total = 0;
    stores.each(function(store) {
        var msg = '';
        switch(store.err) {
        case 'invalid_key':
            msg = resMsg(store.store, true, 'invalid Lipscore API key.');
            break;
        case 'demo_key':
            msg = resMsg(store.store, true, 'demo Lipscore API key found, please set up a correct key.');
            break;
        case 'no_orders':
            msg = resMsg(store.store, true, 'no orders found.');
            break;
        case 'disabled':
            msg = resMsg(store.store, true, 'Lipscore is disabled for the shop.');
            break;
        default:
            msg = resMsg(store.store, false, store.count + ' emails.');
            total += store.count;
            break;
        }
        storeRows.push(msg);
    });
    return storeRows.join('<br/>');
}

function totalEmails(stores) {
    var total = 0;
    stores.each(function(store) {
        if (!store.err) {
            total += store.count;
        }
    });
    return total;
}

var lsPreviewPopupClosed = false;
function openLsPreviewPopup(text) {
    $('ls-reminder-preview-text').update(text);

    var $mask = $$('.ls-preview-popup-mask')[0];
    var height = $('html-body').getHeight();
    $mask.setStyle({'height':height+'px'});
    toggleSelectsUnderBlock($mask, false);
    $mask.show();

    var $popup = $$('.ls-preview-popup')[0];
    var vpHeight = $(document).viewport.getHeight();
    var height = $popup.getLayout().get('margin-box-height');
    var scrollTop = $(document).viewport.getScrollOffsets().top;
    var avTop = (vpHeight / 2) - (height / 2) + scrollTop;
    $popup.style.top = avTop+ 'px';
    $popup.show();

    lsPreviewPopupClosed = false;
}

function closeLsPreviewPopup() {
    toggleSelectsUnderBlock($$('.ls-preview-popup-mask')[0], true);
    $$('.ls-preview-popup-mask')[0].hide();
    $$('.ls-preview-popup')[0].hide();
    lsPreviewPopupClosed = true;
}

Event.observe(window, 'keyup', function(evt) {
    if (lsPreviewPopupClosed) {
        return;
    }
    var code;
    if (evt.keyCode) {
        code = evt.keyCode;
    } else if (evt.which) {
        code = evt.which;
    }
    if (code == Event.KEY_ESC) {
        closeLsPreviewPopup();
    }
});
