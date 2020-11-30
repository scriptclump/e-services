/**
* 扩展jquery插件
* options: {objClicked: 点击objClicked浏览图片 required, fileName: 图片标题元素 options, rotateDirection: 图片旋转方向, options}
*/
(function($){
  $.fn.imageBox = function(options){
    var options = $.extend({
      objClicked: '.img',      // 点击的元素
      rotateDirection: 'right' // 图片旋转方向， 默认是right => 顺时针
    }, options);   
    var obj = this, objClicked = options.objClicked, fileName = options.fileName, list_images = [];

    initHtml(obj);
    initCss(obj);

    $(objClicked).on('click', function(){
      var _url = $(this).data("url"), current = 0;
     
      if(list_images.length > 0){
        list_images.length = 0;
      }

      $(objClicked).each(function(index, element) {
        var $img = $(element), _src = $img.attr("src");
        if(_url == _src){
          current = index + 1;
        }
        list_images.push(_src);
      });
      
      $('#img-preview').html('<img src="'+ _url +'" width="100%" height="520px" class="image-box" style="cursor: move;"></img>')
      $('#img-preview').attr({'current': current});
      $(obj).find('#unbind-pos').modal('show');
    });

    btnCtrlImgEvent(options, list_images);
  };

  var rotateDeg = 0;
  /**
  *初始化html
  */
  function initHtml(obj){
    var div = $('<div id="unbind-pos" class="modal fade modal-scroll" style="display:none;" aria-hidden="true"></div>'); 
    div.append('<div class="modal-dialog modal-lg1 modal-lg">' +
                  '<div class="modal-content">'+
                        '<div class="modal-header">'+
                            '<button aria-hidden="true" data-dismiss="modal" class="close" type="button"><span>&times;</span></button>'+
                            '<h4 class="modal-title img-pre-title"></h4>'+
                        '</div>'+
                        '<div class="modal-body">'+
                            '<div id="img-preview"></div>'+
                            '<div class="img-op">'+
                                '<span class="btn btn-primary zoom-in"><i class="fa fa-search-plus" aria-hidden="true"></i></span>'+
                                '<span class="btn btn-primary zoom-out"><i class="fa fa-search-minus" aria-hidden="true"></i></span>'+
                                '<span class="btn btn-primary rotate"><i class="fa fa-repeat aria-hidden="true""></i></span>'+
                                '<span role="prev" class="btn btn-primary switch"><i class="fa fa-arrow-left" aria-hidden="true"></i></span>'+
                                '<span role="next" class="btn btn-primary switch"><i class="fa fa-arrow-right" aria-hidden="true"></i></i></span>'+
                                 '<span class="btn btn-primary switch Asize"><i class="fa fa-arrows-alt" aria-hidden="true"></i></i></span>'+


                            '</div>'+
                        '</div>'+
                        '</div>'+
                '</div>');
    $(obj).append(div);
  };

  /**
  * 初始化样式
  */
  function initCss(obj){
    $(obj).find('#img-preview').css({
      'height': '520px',
      'width': '100%',
      'overflow': 'hidden',
      'text-align': 'center'
    });
    $(obj).find('.img-op').css({

      'margin-top': '5px',
      'text-align': 'center'
    });
    $(obj).find('.modal .modal-content .btn').css('border-radius', '0');
    $(obj).find('.img-op .btn').css({
      'margin': '5px',
      
    });
    $(obj).find('.modal-footer .btn-default').css({
      'background-color': '#fff',
      'background-image': 'none',
      'border': '1px solid #ccc',
    });
  };

  /**
  * 按钮控制图片事件
  */
  function btnCtrlImgEvent(options, list_images){

    zoomIn();
    zoomOut();
    dragImage();
    rotateImage(options);
    switchImage(list_images);
    Asize();

  };

  //图片放大
  function zoomIn(){
    $('.zoom-in').click(function(){
      var imageHeight = $('#img-preview img').height();
      var imageWidth = $('#img-preview img').width();
      $('#img-preview img').css({
        height: '+=' + imageHeight * 0.1,
        width: '+=' + imageWidth * 0.1
      });
    });
  };


function Asize(){    
  $('.Asize').click(function(){   var imageHeight = $('#img-preview img').height();     
   var imageWidth = $('#img-preview img').width();  
    $('#img-preview img').css({       
     height:"520px",        width:"100%"       
   }); 
  }); 
   }
  //图片缩小
  function zoomOut(){
    $('.zoom-out').click(function(){
      var imageHeight = $('#img-preview img').height();
      var imageWidth = $('#img-preview img').width();
      $('#img-preview img').css({
        height: '-=' + imageHeight * 0.1,
        width: '-=' + imageWidth * 0.1
      });
    });
  };

  // 图片预览框中拖拽
  function dragImage(){
    $('#img-preview').on('mousedown', 'img', function(event) {
      var mousePos = { x: event.clientX, y: event.clientY };
      var _this = this;

      var scrollLeft = $(_this).parent().scrollLeft();
      var scrollTop = $(_this).parent().scrollTop();

      $(document).on('mousemove', function(event){
        var offsetX = event.clientX - mousePos.x;
        var offsetY = event.clientY - mousePos.y;

        $(_this).parent().scrollLeft(scrollLeft - offsetX);
        $(_this).parent().scrollTop(scrollTop - offsetY);
      });

      $(document).on('mouseup', function(){
        $(document).off("mousemove");     
      });
      return false;
    });
  };

  //图片旋转，默认方向是右旋转
  function rotateImage(options){
    $('.rotate').click(function() {
      if(options.rotateDirection == 'right'){
        rotateDeg += 90;
        if(rotateDeg == 360){
          rotateDeg = 0;
        }
      }
      if(options.rotateDirection == 'left'){
        rotateDeg -= 90;
        if(rotateDeg == -360){
          rotateDeg = 0;
        }
      }
      $('#img-preview img').css({
        'transform': 'rotate('+ rotateDeg +'deg)',
        '-webkit-transform': 'rotate('+ rotateDeg +'deg)',
        '-moz-transform':'rotate('+ rotateDeg +'deg)',
        '-o-transform': 'rotate('+ rotateDeg +'deg)',
        '-ms-transform': 'rotate('+ rotateDeg +'deg)'
      });
    });
  };

  //图片切换
  function switchImage(list_images){
    var $modal = $('#unbind-pos');
    $('#unbind-pos').on('click', '.switch', function(){
      var _list_images = list_images, _self = this, _role = $(_self).attr('role');
      var $image_container = $modal.find('#img-preview');
      var _current = parseInt($image_container.attr('current'));
      var _image_count = _list_images.length;
      var _index = _new_current = 0;
      switch (_role){
        case 'prev':
          if(_current - 1 > 0){
            _new_current = _current - 1;
          }else{
            _new_current = _image_count;
          }
          break;
        case 'next':
          if(_current +1 <= _image_count){
            _new_current = _current + 1;
          }else{
            _new_current = 1;
          }
      }
      _index = _new_current - 1;
      $modal.find('#img-preview').attr({'current': _new_current});
      $modal.find('#img-preview img').attr({'src': _list_images[_index]});
    });
  };

})(jQuery);
