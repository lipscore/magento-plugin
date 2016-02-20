function sendLipscoreReminder(url) {
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
            showReminderMessage(response);
        },
        onFailure: function(response) {
            showReminderMessage(response);
        }
    });
};

function showReminderMessage(response) {
    var txt = '';
    var type = 'error';
    var commonErr = "We're sorry, but something went wrong with reminders, please try again or contact support.<br/><br/>";

    var errMessage = function(response) {
        return commonErr + response.status + ': ' + response.request.url + '.<br/> Params: ' +
               response.request.body + '.<br/> Text: ' + response.responseText;
    };
    
    switch(response.status) {
    case 200:
        if (response.responseJSON && 'message' in response.responseJSON) {
            txt  = response.responseJSON.message;
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
        txt = response.responseJSON.message;
        break;
    case 408:
        txt = 'The server timed out waiting for the request.';
        break;
    default:
        txt = errMessage(response);
        break;
    }
    
    var html = '<ul class="messages"><li class="' + type + '-msg"><ul><li>' + txt + '</li></ul></li></ul>';
    $('messages').update(html);
}
