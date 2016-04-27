Dropzone.autoDiscover = false;
$(function () {
    bootstrap_alert = function (type, message) {
        $('#alert_placeholder').html('<div class="alert alert-' + type + '" role="alert">' + message + '</div>');
    };
    var myDropzone = new Dropzone("#rabobank-upload", {
        parallelUploads: 1,
        previewsContainer: false,
        uploadMultiple: false
    });
    var resetInfo = function () {
        bootstrap_alert('info', 'Drop Rabobank statements export file (.txt) or click here to convert it for Ynab usage.')
    };
    myDropzone.on("success", function (file, response) {
        bootstrap_alert('success', 'File(s) converted!');
        var dl = document.createElement('a');
        var result = $.parseJSON(response);
        $.each(result, function (account, lines) {
            dl.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(lines));
            dl.setAttribute('download', account + '.csv');
            dl.click();
        });
        setTimeout(resetInfo(), 5000);
    });
    myDropzone.on("error", function (file, response) {
        bootstrap_alert('danger', 'An error accord while converting your file.');
        setTimeout(resetInfo(), 5000);
    });
});