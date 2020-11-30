(function(){
var 
	form = $('.form'),
	cache_width = form.width(),
	a4  =[ 595.28,  841.89];  // for a4 size paper width and height

$('#create_pdf').on('click',function(){
	$('body').scrollTop(0);
	createPDF();
});

//create pdf
function createPDF(){
	getCanvas().then(function(canvas){
		var img = canvas.toDataURL("image/png"),
		doc = new jsPDF({
          unit:'px', 
          format:'a4'
        });  
        doc.setFontSize(12);   
        doc.addImage(img, 'JPEG', 30, 30);
        doc.save('Delivery-Executive-Trip-List.pdf');
        form.width(cache_width);
	});
}

// create canvas object
function getCanvas(){
	form.width((a4[0]*1.3333) -60).css('max-width','100%');
	return html2canvas(form,{
    	imageTimeout:2000,
    	removeContainer:true
    });	
}
}());

/*$("#btnSave").click(function() {*/
function convertMap() {
    html2canvas($("#map_canvas2"), {
        useCORS: true,
        onrendered: function(testCanvas) {
            theCanvas = testCanvas;
            $("#img-out").empty();
            $("#img-out").append(testCanvas);
        }
    });
}
   /* });*/