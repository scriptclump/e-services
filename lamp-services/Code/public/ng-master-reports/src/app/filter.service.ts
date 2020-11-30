import { Injectable } from '@angular/core';
import { Http,Headers } from '@angular/http';
import * as FileSaver from 'file-saver';
import * as xlsx from 'xlsx';
import { environment as ENV } from "../environments/environment";
@Injectable({
  providedIn: 'root'
})
export class FilterService {
  host = ENV.BASE_URL;
  env = ENV.port;
  _header:any;
  constructor(public http:Http) {

    //this.host = "http://10.175.8.20:9527/";
    this.host = "http://35.154.71.65:1207/";
    /*this._header=new Headers();
    this._header.append("allow-credentials",true);*/
  }
  gatherData(data){
    console.log('iam in service call');
    console.log('env',this.env,'host',this.host);

  	return new Promise((resolve,reject)=>{
  		//var data={"table":"users","groupby":"password"};
  		this.http.post(this.host+'basic/getBasicDetails',{data}).toPromise().then(data=>{
  			resolve(data);
  		},err=>{
  			reject(err);
  		});
  	});
  }

  excelService(json:any[],excelFileName:string){
      const workSheet: xlsx.WorkSheet=xlsx.utils.json_to_sheet(json);
      const workBook: xlsx.WorkBook= {Sheets: {'data':workSheet },SheetNames: ['data']};
      const excelBuffer:any = xlsx.write(workBook,{ bookType:'xlsx', type: 'array' });
      this.saveExcelFile(excelBuffer,excelFileName);

  }
  saveExcelFile(buffer:any,fileName:string){
      const excelType='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8';
      const excelExtension='.xlsx';
      const data:Blob = new Blob([buffer],{type: excelType});
      FileSaver.saveAs(data, fileName+'_export_'+new Date().getTime()+excelExtension);
  }
}
