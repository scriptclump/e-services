import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { DataTableModule } from "angular-6-datatable";
import { AppComponent } from './app.component';
import { FilterService } from './filter.service';
import { HttpModule } from '@angular/http';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import {MatDatepickerModule} from '@angular/material/datepicker';
import {MatFormFieldModule} from '@angular/material/form-field';
import {MatSelectModule} from '@angular/material/select';
import {MatNativeDateModule} from '@angular/material';
import {MatInputModule} from '@angular/material';
import {NgbModule} from '@ng-bootstrap/ng-bootstrap';
import { AngularFontAwesomeModule } from 'angular-font-awesome';
import { DatePipe } from "@angular/common";
import { MultiselectDropdownModule } from 'angular-2-dropdown-multiselect';
import { AngularMultiSelectModule } from 'angular2-multiselect-dropdown/angular2-multiselect-dropdown';
import { NgMultiSelectDropDownModule } from 'ng-multiselect-dropdown';
import { HeadingPipe } from './heading.pipe';
import { NgxLoadingModule } from 'ngx-loading';

@NgModule({
  declarations: [
    AppComponent,
    HeadingPipe    
  ],
  imports: [
    BrowserModule,DataTableModule,HttpModule,FormsModule,ReactiveFormsModule,BrowserAnimationsModule,
    MatSelectModule,MatDatepickerModule,MatFormFieldModule,MatNativeDateModule,MatInputModule,NgbModule,NgMultiSelectDropDownModule.forRoot(),AngularFontAwesomeModule,MultiselectDropdownModule,AngularMultiSelectModule,NgxLoadingModule.forRoot({})
  ],
  providers: [FilterService,MatDatepickerModule,DatePipe],
  bootstrap: [AppComponent]
})
export class AppModule { }
