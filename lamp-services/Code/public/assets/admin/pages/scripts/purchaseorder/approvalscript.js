$(document).on('click', '#approval_submit', function () {
    if (confirm('Do you want to Submit?')) {
        var approval_unique_id = $('#approval_unique_id').val();
        var approval_status = $('#approval_status').val();
        var approval_comment = $('#approval_comment').val();
        var approval_module = $('#approval_module').val();
        var current_status = $('#current_status').val();
        var table_name = $('#table_name').val();
        var unique_column = $('#unique_column').val();
        var url = $('#approvalurl').val();
        $('.loderholder').show();
        if (url == '') {
            var url = '/po/approvalSubmit';
        }
        $.ajax({
            headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
            url: url,
            type: 'POST',
            data: {approval_unique_id: approval_unique_id, approval_status: approval_status,
                approval_comment: approval_comment, approval_module: approval_module,
                current_status: current_status, table_name: table_name, unique_column: unique_column
                },
            dataType: 'JSON',
            success: function (data) {
                $('#addSkubtn').attr('disabled', false);
                if (data.status == 200) {
                    location.reload();
                } else {
                    $('#appr_error-msg').html(data.message).show();
                    $('.loderholder').hide();
                    /*window.setTimeout(function () {
                     $('#error-msg').hide()
                     }, 3000);*/
                }
            },
            error: function (response) {

            }
        });
    }
});