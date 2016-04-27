Dropzone.autoDiscover = false;
$(function() {
    bootstrap_alert = function(type, message) {
        $('#alert_placeholder').html('<div class="alert alert-'+type+'" role="alert">'+message+'</div>');
    };
    var myDropzone = new Dropzone("#rabobank-upload", {
        parallelUploads: 1,
        previewsContainer: false,
        uploadMultiple: false
    });
    myDropzone.on("success", function(file, response) {
        var dl = document.createElement('a');
        console.log('test');
        bootstrap_alert('success', 'File(s) converted!');
        dl.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(response));
        dl.setAttribute('download', 'filename.txt');
        dl.click();
        setTimeout(function(){
            bootstrap_alert('info', 'Drop Rabobank statements export file (.csv) or click here to convert it for Ynab usage.');
        }, 5000);
    });
    myDropzone.on("error", function(file, response) {
        bootstrap_alert('danger', 'An error accord while converting your file.');
        setTimeout(function(){
            bootstrap_alert('info', 'Drop Rabobank statements export file (.csv) or click here to convert it for Ynab usage.');
        }, 5000);
    });
});