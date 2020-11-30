import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'heading'
})
export class HeadingPipe implements PipeTransform {

  transform(value: any, args?: any): any {
  	var text=value.split('_').join(' ');
  	var a='';
  	//text = text.toLowerCase().split(' ').map((s) => s.charAt(0).toUpperCase() + s.substring(1)).join(' ');
    text = text.toLowerCase().split(' ');
    text.forEach(e=>{
    	if(e == "qty" || e == "tbv" || e == "tgm" || e=="elp" || e=="esp" || e=="mrp" || e=="sku" || e=="id"){
    		e = e.toUpperCase();
    	}else if (e == "qty)" || e == "tbv)" || e == "tgm)"){
	        console.log('elseif');
	        e= e.substring(0,3).toUpperCase() + e.substring(3);
	    }else if(e == "sum(delivered" || e == "sum(booked" || e == "sum(order" || e == "sum(invoice" || e == "sum(return" || e == "sum(cancel"){
    		console.log(e);
    		let d = e.split('(');
    		console.log(d);
    		d[1]= d[1].substring(0,1).toUpperCase() + d[1].substring(1);
    		e=d.join('(');
    	}else{
    		console.log(e);
    		e= e.substring(0,1).toUpperCase() + e.substring(1);
    	}
    	a+=e+" ";
    })

    console.log(a);
    return a;
  }

}
