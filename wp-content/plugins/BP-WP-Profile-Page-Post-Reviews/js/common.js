jQuery(document).ready(function($) {
    $('.prorevs-report-review').click(function(event) {
        event.preventDefault();
        var thisObj = $(this);
        $('.prorevs-report-form[data-id="' + thisObj.attr('data-id') + '"]').fadeIn();
    });

    var reportIsSending = false;

    $('.prorevs-report-send').click(function(event) {
        event.preventDefault();

        if (reportIsSending)
            return;

        reportIsSending = true;

        var form = $(this).parent();
        var id = form.attr('data-id');

        var loadingImg = $('<img src="' + proRevs.loadingImg + '">');
        form.append(loadingImg);

        $.ajax({
            type: 'POST',
            url: proRevs.ajaxurl,
            data: {
                action: 'report-review',
                nonce: proRevs.nonce,
                id: id,
                reason: $('#prorevs-reason-' + id).val()
            },
            success: function(data) {
                if (typeof data == "string") {
                    if (data == "") {
                        form.fadeOut();
                        alert('Report is sent. Thanks for your time.');
                        return;
                    } else {
                        alert(data);
                    }
                } else {
                    alert('Can\'t process the request. Unknown error.');
               }
            },
            dataType: 'json'
        }).always(function() {
            loadingImg.remove();
            reportIsSending = false;
        });
    });

    $('.prorevs-report-cancel').click(function(event) {
        event.preventDefault();
        $(this).parent().fadeOut();
    });
});
