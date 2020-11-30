import { Component } from '@angular/core';
import { FilterService } from './filter.service';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { ChangeDetectorRef } from '@angular/core';
import {MatSelectModule} from '@angular/material/select';
import {MatDatepickerModule} from '@angular/material/datepicker';
import {MatFormFieldModule} from '@angular/material/form-field';
import {MatInputModule} from '@angular/material';
import {NgbModule} from '@ng-bootstrap/ng-bootstrap';
import { DatePipe } from "@angular/common";
import { IMultiSelectOption } from 'angular-2-dropdown-multiselect';
import { HeadingPipe } from './heading.pipe';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  title = 'app';
  response:any=[];
  keys:any=[];
  filtertext:any=[];
  showResponse:any=[];
  groupByColumn:any=[];
  filterByColumn:any=[];
  tablename:any;
  filterTextForDb;
  inputData:any={};
  minDate:any;
  maxDate:any;
  dt;
  fromDate;
  toDate;
  dropdownSettings;
  KeysForDropDown;
  KeysForFilters;
  dcs_checked;
  loading = false;

  constructor(public filter:FilterService,public changedet:ChangeDetectorRef,public datepipe:DatePipe,
              ){
      this.arrangeKeys();
      this.fromDate=new Date();
      var month=this.fromDate.getMonth()+1;
      this.minDate={year: 1970, month: 1, day: 1}
      this.fromDate={
        'year': this.fromDate.getFullYear(),
        'month': month,
        'day': this.fromDate.getDate()
      };
      this.toDate=this.fromDate;
      this.dropdownSettings = {
      singleSelection: false,
      idField: 'item_id',
      textField: 'item_text',
      selectAllText: 'Select All',
      unSelectAllText: 'UnSelect All',
      itemsShowLimit: 1,
      allowSearchFilter: true
    }; 
    this.KeysForFilters=[
    {'item_id':'brand','item_text':'Brand'},
    {'item_id':'category','item_text':'Category'},
    {'item_id':'manufacturer','item_text':'Manufacturer'},
    {'item_id':'retailer','item_text':'Retailer'},
    {'item_id':'beat','item_text':'Beat'},
    {'item_id':'hub','item_text':'Hub'},
    {'item_id':'warehouse','item_text':'Warehouse'},
    {'item_id':'product','item_text':'Product'},
    {'item_id':'product_group','item_text':'Product Group'}
    ];
    this.getData();


  }

  getData(){
    this.loading = true;    
    var displayData='';
    var displayFields='';
    console.log('1');
    this.KeysForDropDown=[];
    console.log(this.tablename);
    this.getTheKeys().then((data)=>{
      console.log('5');
      var from=this.fromDate.year +'-'+ this.fromDate.month+'-'+ this.fromDate.day;
      var to=this.toDate.year +'-'+ this.toDate.month+'-'+ this.toDate.day;
      var groupByData='';
      if(this.groupByColumn.length>0){
        console.log(this.groupByColumn.sort());
        let result = this.groupByColumn.map(a => a.item_id);
        groupByData=result.join(',');
        if(groupByData.indexOf('gds_order_id')!=-1)
          displayFields="order_code,";
        else if(groupByData.indexOf('order_code')!=-1)
          displayFields="order_code,";
        console.log(groupByData);
      }
      console.log(this.filterByColumn);
      if(this.filterByColumn.length>0){
        console.log(this.filterByColumn.sort());
        let res = this.filterByColumn.map(a => a.item_id);
        displayData=res.join(',');
        //displayData=this.filterByColumn.join(',');
       // displayFields="order_code";
        if(displayData.includes('brand')){
          displayFields+="brand_name,manufacturer_name,";
        }
        if(displayData.includes('category')){
          if(!displayFields.includes('brand_name')){
            displayFields+="brand_name,";
          }if(!displayFields.includes('manufacturer_name')){
            displayFields+="manufacturer_name,";
          }if(!displayFields.includes('category_name')){
            displayFields+="category_name,";
          }
        }
        if(displayData.includes('manufacturer')){
          if(!displayFields.includes('manufacturer_name')){
            displayFields+="manufacturer_name,";
          }
        }
        if(displayData.includes('retailer')){
          if(!displayFields.includes('brand_name')){
            displayFields+="brand_name,";
          }if(!displayFields.includes('manufacturer_name')){
            displayFields+="manufacturer_name,";
          }if(!displayFields.includes('category_name')){
            displayFields+="category_name,";
          }if(!displayFields.includes('retailer')){
            displayFields+="retailer,";
          }if(!displayFields.includes('retailer_code')){
            displayFields+="retailer_code,";
          }
        }
        if(displayData.includes('beat')){
          if(!displayFields.includes('beat')){
            displayFields+="beat,";
          }if(!displayFields.includes('warehouse')){
            displayFields+="warehouse,";
          } if(!displayFields.includes('hub')){
            displayFields+="hub,";
          }
        }
        if(displayData.includes('hub')){
          if(!displayFields.includes('hub')){
            console.log('hi');
            displayFields+="hub,";
          }
        }
        if(displayData.includes('warehouse')){
          if(!displayFields.includes('warehouse')){
            displayFields+="warehouse,";
          }
        }
        if(displayData.includes('product')){
          if(!displayFields.includes('product_id')){
            displayFields+="product_id,";
          }if(!displayFields.includes('product_name')){
            displayFields+="product_name,";
          }if(!displayFields.includes('sku')){
            displayFields+="sku,";
          }if(!displayFields.includes('brand_name')){
            displayFields+="brand_name,";
          }if(!displayFields.includes('category_name')){
            displayFields+="category_name,";
          }if(!displayFields.includes('manufacturer_name')){
            displayFields+="manufacturer_name,";
          }if(!displayFields.includes('mrp')){
            displayFields+="mrp,";
          }if(!displayFields.includes('order_esp')){
            if(groupByData!='')
              displayFields+="sum(order_esp),";
            else
            displayFields+="order_esp,";
          }if(!displayFields.includes('order_elp')){
            if(groupByData!='')
              displayFields+="sum(order_elp),";
            else
            displayFields+="order_elp,";
          }
        }
        if(displayData.includes('product')){
          if(!displayFields.includes('product_name')){
            displayFields+="product_name,";
          }if(!displayFields.includes('brand_name')){
            displayFields+="brand_name,";
          }if(!displayFields.includes('category_name')){
            displayFields+="category_name,";
          }if(!displayFields.includes('manufacturer_name')){
            displayFields+="manufacturer_name,";
          }if(!displayFields.includes('mrp')){
            displayFields+="mrp,";
          }if(!displayFields.includes('order_esp')){
            if(groupByData!='')
              displayFields+="sum(order_esp),";
            else
            displayFields+="order_esp,";
          }if(!displayFields.includes('order_elp')){
            if(groupByData!='')
              displayFields+="sum(order_elp),";
            else
            displayFields+="order_elp,";
          }
        }
        if(groupByData!=''){
          console.log('ingroup');
          displayFields+="sum(order_qty),sum(invoice_qty),sum(return_qty),sum(cancel_qty),sum(delivered_qty),sum(booked_tbv),sum(delivered_tbv),sum(booked_tgm),sum(delivered_tgm),current_esp,current_elp,created_by";
        }else{
          console.log('ingroup else');
          displayFields+="order_qty, invoice_qty, return_qty, cancel_qty,delivered_qty,booked_tbv,delivered_tbv,booked_tgm,delivered_tgm,current_esp,current_elp,created_by";
        }

      }else if(this.filterByColumn.length==0 && groupByData!=''){
          displayFields="order_code,brand_name,manufacturer_name,category_name,retailer,retailer_code,beat,warehouse,hub,product_id,product_name,sku,mrp,order_esp,order_elp,sum(order_qty),sum(invoice_qty),sum(return_qty),sum(cancel_qty),sum(delivered_qty),sum(booked_tbv),sum(delivered_tbv),sum(booked_tgm),sum(delivered_tgm),current_esp,current_elp,created_by";

      }
      console.log(groupByData);
      this.inputData={
        'table': this.tablename,
        'groupby': groupByData,
        'filtertext': this.filterTextForDb,
        'displayData':displayFields,
        'fromdate': from,
        'todate': to,
        'dcs': this.dcs_checked
      };

      console.log(this.inputData);
      this.filter.gatherData(this.inputData).then(result=>{
      console.log('6');
        this.response=result['_body'];
        this.response=JSON.parse(this.response);
        if(this.response['Status']==200){
          this.response=this.response['ResponseBody'];
          this.keys=Object.keys(this.response[0]);
          this.arrangeKeys();
          this.showResponse=[];
          console.log(this.response);
         /* for(var i=0;i<this.response.length;i++){
            this.showResponse[this.showResponse.length]=this.response[i];
          }*/
          this.showResponse=this.response;
          this.loading = false;
        }
        else{
          this.showResponse=[];
          this.keys=[];
          this.loading = false;
        }

      },err=>{
        console.log(err);
        this.showResponse=[];
          this.keys=[];
      });
    });
  

      
  }
  getTheKeys(){
    return new Promise((resolve,reject)=>{

      console.log('2');
      var from=this.fromDate.year +'-'+ this.fromDate.month+'-'+ this.fromDate.day;
      var to=this.toDate.year +'-'+ this.toDate.month+'-'+ this.toDate.day;

      var input={
          'table': this.tablename,
          'groupby': '',
          'filtertext': '',
          'displayData':'*',
          'fromdate': from,
          'todate': to,
          'dcs': this.dcs_checked,
          'limit': 1
      }
      this.filter.gatherData(input).then(res=>{

        var data=res['_body'];
        data=JSON.parse(data);
        console.log(data['ResponseBody']);
        data=  data['ResponseBody'];
        console.log(data.length);
        this.keys=Object.keys(data[0]);
        this.arrangeKeys();
        console.log('3');
        this.filterTextForDb='';
        for(var i=0;i<this.keys.length;i++){
          if(this.filtertext[this.keys[i]]!='' && this.filtertext[this.keys[i]]!=undefined){
            if(this.filterTextForDb == ''){
              this.filterTextForDb='where '+this.keys[i] +' like '+'\'%'+this.filtertext[this.keys[i]]+'%\'';
            }else{

              this.filterTextForDb= this.filterTextForDb+' and '+this.keys[i] +' like '+'\'%'+this.filtertext[this.keys[i]]+'%\'';
            }
          }
        }
        resolve(this.filterTextForDb);

      });
      console.log('4');
    })
  }

  exportData(){
    if(this.showResponse.length>0)
    this.filter.excelService(this.showResponse,this.tablename);
  }
  changeInTable(){
    console.log('in cgane');
    this.groupByColumn='';
    //this.getTheKeys();

  }
  fixMinDateForTo(){
    console.log(this.fromDate);
    this.minDate=this.fromDate;
  }
  fixMinDateForFrom(){
    this.maxDate=this.toDate;
  }
  arrangeKeys(){
    console.log(this.keys);
    var newKeys=this.keys;
    console.log(Object.keys(newKeys));
    this.KeysForDropDown=[];
    Object.values(newKeys).forEach(key=>{
      console.log(key);
      let showColumn=true;
      let display_text = (<string>key).split("_");
      if(display_text.indexOf('qty')!=-1 || display_text.indexOf('tgm')!=-1 || display_text.indexOf('tbv')!=-1 || display_text.indexOf('qty)')!=-1 || display_text.indexOf('tgm)')!=-1 || display_text.indexOf('tbv)')!=-1){
        showColumn=false;
      }
      let a ="";
      if (showColumn){
        console.log(display_text);
        display_text.forEach(e=>{
          if(e == "qty" || e == "tbv" || e == "tgm" || e=="elp" || e=="esp" || e=="mrp" || e=="sku" || e=="id"){
            e = e.toUpperCase();
          }
          else{
            console.log(e);
            e= e.substring(0,1).toUpperCase() + e.substring(1);
          }
          a+=e+" ";
        });
        console.log(a);
        this.KeysForDropDown.push({'item_id': key, 'item_text': a});
      }
    })
    console.log(this.KeysForDropDown);
  }
}
