galleryPlugin();
getAllComments();
$('li.showicons').mouseover(function()
{
    $('div.defaulticons', this).show();
});
$('li.showicons').mouseout(function(){
    $('div.defaulticons', this).hide();
});
$("#editproduct_sumbit").click(function()
{
    $("#productStatus").val("1");
    $("#saveToDraft").click();
});
$('#demo-1 .simpleLens-thumbnails-container img').simpleGallery(
{
    loading_image: 'demo/images/loading.gif'
});
$('#demo-1 .simpleLens-big-image').simpleLens(
{
    loading_image: 'demo/images/loading.gif'
});
$('#demo-2 .simpleLens-thumbnails-container img').simpleGallery({
    loading_image: 'demo/images/loading.gif',
            show_event: 'click'
});
$('#demo-2 .simpleLens-big-image').simpleLens({
loading_image: 'demo/images/loading.gif',
        open_lens_event: 'click'
});

$('.urlimage').on('click', function() {
    var image_preview = $(".urlimage_preview").val();    
    var testRegex = /^https?:\/\/(?:[0-9.\-a-z]+\.)+[a-z]{2,6}(?:\/[^\/#?]+)+\.(?:jpe?g|gif|png)$/;
    if (testRegex.test(image_preview)==false) {
        $(".urlimage_preview").val('');
      alert('Please enter vaild image URL.');
    }else
    {     
        $('.preview-image').attr('src',image_preview).fadeIn();
                        $("#preview_image").css({width: "20%",height: "20%",border:"2px solid #c3c3c3 !important",margin:"7px" });
        product_id = $('#product_id').val();
        imageData = {image_preview:image_preview};
        $.ajax({
        headers: {'X-CSRF-TOKEN': token},
                url: '/saveProductUrlImage/' + product_id,
                data:imageData,
                async: false,
                type: 'POST',
                success: function (rs)
                {
                    if(rs=='success')
                    {
                        $('.preview-image').attr('src',image_preview).fadeIn();
                        $("#preview_image").css({width: "20%",height: "20%",border:"2px solid #c3c3c3 !important",margin:"7px" });
                        $('.urlimage').prop('disabled',true); 
                        $('.urlimage_preview').prop('disabled', true); 
                    }else{
                        alert("Please upload Maximum 8 Images.");
                    }
                   
                }
        });
    }
});
 var resetOthers = function(id){
    $('.pop_holder').not('#' + id).each(function(){
    var id = $(this).attr('id');
    $('[data-id="' + id + '"]').data('clicked', false);
    });
    };
    var mouseEnter = function(){
    var id = $(this).data('id');
    if ($(this).data('clicked')){
    resetOthers(id);
    return;
    }
    $(this).data('hover', true);
    $('.pop_holder').not('#' + id).hide();
    $('.pop_holder').not('#' + id).each(function(){
    var id = $(this).attr('id');
    $('[data-id="' + id + '"]').data('hover', false);
    });
    resetOthers(id);
    //console.log( $(this).data() );
    $("#" + id).show('fast');
    };
    $(".cancelBtn").click(function(){
    $("#comments").val("");
    $("#pop1").removeAttr("style");
    });
    var mouseLeave = function(){
    var id = $(this).data('id');
    if ($(this).data('clicked')){
    resetOthers(id);
    return;
    }
    $(this).data('hover', false);
    var id = $(this).data('id');
    resetOthers(id);
    //console.log( $(this).data() );
    $("#" + id).hide('fast');
    };
    $("a.click").click(function(){
    var id = $(this).data('id');
    if ($(this).data('clicked')){
    $(this).data('clicked', false);
    } else{ $(this).data('clicked', true); }
    if ($(this).data('hover') == true){
    $(this).data('hover', false);
    $("#" + id).find('input').focus();
    return;
    }

    //console.log( $(this).data() );
    $('.pop_holder').not('#' + id).hide();
    resetOthers(id);
    $('.pop_holder').not('#' + id).data('hover', false);
    $("#" + id).toggle('fast', function(){
    $("#" + id).find('input').focus();
    }).finish(function(){
    //console.log(this);
    });
    }).mouseleave(mouseLeave).mousedown(function(){ return false; })

$(document).on('keyup', '.txtBox', function(){
    var val = this.value;
    var el = $(this).closest('.form-body');
    //console.log(val);
    if (val.length){
    el.find('.submitBtn').prop('disabled', false);
    } else{ el.find('.submitBtn').prop('disabled', true); }
    }).keyup();
 $('.txtBox').keyup();


$("#comment_submit").click(function()
{
    var comments_data = $("#comments").val();
    var product_id = $("#product_id").val();
    url = "/productComments";
    var token = $("#csrf-token").val();
    commentsData = {comments:comments_data, product_id:product_id};
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
            url: url,
            async: false,
            type: 'POST',
            data:commentsData,
            success: function (rs)
            {

            var reviewsHtml = [];
            var comments_counts = $("#comments_count").text();
            $.map(JSON.parse(rs), function(value, index) {
            reviewsHtml.push('<div class="row pop_inner "><div class="row"><div class="col-lg-2 col-md-2 col-xs-3"><img src="' + value.pic + '" class="img-responsive popimg"></div><div class="col-lg-10 col-md-9 col-xs-9"><div class="row"><p class="name">' + value.name + ' <span class="comments_time">' + value.created_on + ' </span></p></div></div></div><div ><p >' + value.comments + '</p></div></div>');
            comments_counts = parseInt(comments_counts) + parseInt(value.count);
            });
            reviewsHtml = reviewsHtml.join('');
            $("#comments_count").text(comments_counts);
            $('.add_comment_text').append(reviewsHtml);
            $("#comments").val('');
            },
            error: function (err) {
           // console.log('Error: ' + err);
      }
    });
});
function setAsDefaultImage(url_path, asDefautlImageId)
{
	product_id = $('#product_id').val();
	imageData = {url_path:url_path, asDefautlImageId:asDefautlImageId};
	if (confirm('Do You want Set this image to as Default ...?'))
	{
		$.ajax({
		headers: {'X-CSRF-TOKEN': token},
		        url: '/setAsDefaultImage/' + product_id,
		        data:imageData,
		        async: false,
		        type: 'POST',
		        success: function (rs)
		        {
		        alert("Successfully updated Default image.");
		        location.reload();
		        }
		});
	}
}
function deleteProductImage(dropProductId)
{
	product_id = $('#product_id').val();
	imageData = {dropProductId:dropProductId};
	if (confirm('Do You want to delete this image...?'))
	{
		$.ajax({
		headers: {'X-CSRF-TOKEN': token},
		        url: '/deleteProductImage/' + product_id,
		        data:imageData,
		        async: false,
		        type: 'POST',
		        success: function (rs)
		        {
		        if (rs == 1)
		        {
		        alert("Successfully Deleted image.");
		        location.reload();
		        }

		        }
		});
	}
}
function galleryPlugin()
{
    var carsousel = $('#demo4carousel').elastislide({start:0, minItems:3,
            onReady:function(){
	            //init imagezoom with many options
	            $('#demo4').ImageZoom({type:'standard', zoomSize:[480, 300], bigImageSrc:'', offset:[10, - 4], zoomViewerClass:'standardViewer', onShow:function(obj){obj.$viewer.hide().fadeIn(500); }, onHide:function(obj){obj.$viewer.show().fadeOut(500); }});
	            $('#demo4carousel li:eq(0)').addClass('active');
	            // change zoomview size when window resize
	            $(window).resize(function(){
	            var demo4obj = $('#demo4').data('imagezoom');
	            winWidth = $(window).width();
	            if (winWidth > 900)
	            {
	            demo4obj.changeZoomSize(480, 300);
	            }
	            else
	            {
	            demo4obj.changeZoomSize(winWidth * 0.4, winWidth * 0.4 * 0.625);
	            }
	            });
            }
    });
}
function delete_product_supplier(pid)
{
            //console.log("deleted id is ="+wh_id);
    if (confirm('Are you sure you want to delete?'))
    {
        $.ajax({
        headers: {'X-CSRF-TOKEN': token},
                url: '/deletesupplierproduct/' + pid,
                processData: false,
                contentType: false,
                success: function (rs)
                {
                $("#relatedProductsGrid").igHierarchicalGrid({"dataSource":'/relatedproducts/' + $('#product_id').val()});
                //alert(rs);
                }
        });
    }
}
function getAllComments()
{
    url = "/getProductComments";
    var product_id = $("#product_id").val();
    var token = $("#csrf-token").val();
    commentsData = {product_id:product_id};
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
            url: url,
            async: false,
            type: 'POST',
            data:commentsData,
            success: function (rs)
            {

            var reviewsHtml = [];
            var comments_count = 0;
            $.map(JSON.parse(rs), function(value, index) {

            reviewsHtml.push('<div class="row pop_inner "><div class="row"><div class="col-lg-2 col-md-2 col-xs-3"><img src="' + value.pic + '" class="img-responsive popimg"></div><div class="col-lg-10 col-md-9 col-xs-9"><div class="row"><p class="name">' + value.name + ' <span class="comments_time">' + value.created_on + ' </span></p></div></div></div><div ><p >' + value.comments + '</p></div></div>');
            comments_count = comments_count + value.count;
            });
            //reviewsHtml = reviewsHtml.join('');

            $('.add_comment_text').html(reviewsHtml);
            $("#comments").val('');
            $("#comments_count").text(comments_count);
            },
            error: function (err) {
           // console.log('Error: ' + err);
            }
    });
}