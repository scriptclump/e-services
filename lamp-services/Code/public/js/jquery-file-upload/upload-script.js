$(function () {
    'use strict';
    // Change this to the location of your server-side upload handler:
    var url = window.location.hostname === 'blueimp.github.io' ?
                '//jquery-file-upload.appspot.com/' : '/product/uploadhandler',
        uploadButton = $('<button/>')
            .addClass('btn btn-primary')
            .attr('type', 'button') 
            .prop('disabled', true)
            .text('Processing...')
            .on('click', function (event) {                
                event.preventDefault;
                var $this = $(this),
                    data = $this.data();
                $this
                    .off('click')
                    .text('Abort')
                    .on('click', function () {
                        $this.preventDefault;
                        $this.remove();
                        data.abort();
                    });
                data.submit().always(function () {
                    $this.remove();
                });
            });
    $('#fileupload').fileupload({        
        url: url,
        dataType: 'json',
        autoUpload: false,
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        maxFileSize: 999000,
        // Enable image resizing, except for Android and Opera,
        // which actually support image resizing, but fail to
        // send Blob objects via XHR requests:
        disableImageResize: /Android(?!.*Chrome)|Opera/
            .test(window.navigator.userAgent),
        previewMaxWidth: 100,
        previewMaxHeight: 100,
        previewCrop: false
    }).on('fileuploadadd', function (e, data) {
        data.context = $('<tr class="template-upload"/>').appendTo('#files');
        $.each(data.files, function (index, file) {
            var node = $('<td class="preview"/>');
            var node2 = $('<td/>').append($('<span/>').text(file.name));
            if (!index) {
                if(file.size)
                {
                    var tempButton = uploadButton.clone(true).data(data);
                    var tempTd = $('<td/>').append(tempButton);
                    var node3 = tempTd;
                }else{
                    var tempButton = uploadButton.clone(true).data(data);
                    var tempTd = $('<td/>').append(tempButton);
                    var node3 = tempTd;
                }
            }
            node.appendTo(data.context);
            node2.appendTo(data.context);
            node3.appendTo(data.context);
        });
    }).on('fileuploadprocessalways', function (e, data) {
        var index = data.index,
            file = data.files[index],
            node = $(data.context.children()[index]);
        if (file.preview) {
            node
                .prepend('<br>')
                .prepend(file.preview);
        }
        if (file.error) {
            node
                .append('<br>')
                .append($('<span class="text-danger"/>').text(file.error));
        }
        if (index + 1 === data.files.length) {
            data.context.find('button')
                .text('Upload')
                .prop('disabled', !!data.files.error);
        }
    }).on('fileuploadprogressall', function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#progress .progress-bar').css(
            'width',
            progress + '%'
        );
    }).on('fileuploaddone', function (e, data) {
        $.each(data.result.files, function (index, file) {
            if (file.url) {
                var link = $('<a>')
                    .attr('target', '_blank')
                    .prop('href', file.url);
                $(data.context.children('td.preview').children()[index])
                    .wrap(link);
            var inputData = $('<input/>').attr('type', 'hidden').attr('name', 'media[image][]').val(file.url);
            $('#fileupload').removeAttr('required');
            $(data.context.children()[index]).append(inputData);
            } else if (file.error) {
                var error = $('<span class="text-danger"/>').text(file.error);
                $(data.context.children()[index])
                    .append('<br>')
                    .append(error);
            }
        });
    }).on('fileuploadfail', function (e, data) {
        $.each(data.files, function (index) {
            var error = $('<span class="text-danger"/>').text('File upload failed.');
            $(data.context.children()[index])
                .append('<br>')
                .append(error);
        });
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
});