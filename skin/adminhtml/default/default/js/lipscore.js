function sendLipscoreReminder(url, periodElementId) {
    $periodEl = $(periodElementId);
    new Ajax.Request(url, {
        method: 'post',
        parameters: {period: $periodEl.getValue()},
        onSuccess: function(response) {
            showReminderMessage(response);
            $periodEl.setValue('');
        },
        onFailure: function(response) {
            showReminderMessage(response);
        }
    });
};

function showReminderMessage(response) {
    var type = txt = '';
    
    switch(response.status) {
    case 200:
        txt  = response.responseJSON.message;
        type = 'success';
        break;
    case 422:
        txt  = response.responseJSON.message;
        type = 'error';
        break;
    case 408:
    case 503:
        txt  = 'The server timed out waiting for the request.';
        type = 'error';
        break;
    default:
        txt  = "We're sorry, but something went wrong with reminders, please try again or contact support.";
        type = 'error';
        break;
    }
    
    var html = '<ul class="messages"><li class="' + type + '-msg"><ul><li>' + txt + '</li></ul></li></ul>';
    $('messages').update(html);
}
