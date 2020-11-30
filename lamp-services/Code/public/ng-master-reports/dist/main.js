(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["main"],{

/***/ "./src/$$_lazy_route_resource lazy recursive":
/*!**********************************************************!*\
  !*** ./src/$$_lazy_route_resource lazy namespace object ***!
  \**********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function webpackEmptyAsyncContext(req) {
	// Here Promise.resolve().then() is used instead of new Promise() to prevent
	// uncaught exception popping up in devtools
	return Promise.resolve().then(function() {
		var e = new Error('Cannot find module "' + req + '".');
		e.code = 'MODULE_NOT_FOUND';
		throw e;
	});
}
webpackEmptyAsyncContext.keys = function() { return []; };
webpackEmptyAsyncContext.resolve = webpackEmptyAsyncContext;
module.exports = webpackEmptyAsyncContext;
webpackEmptyAsyncContext.id = "./src/$$_lazy_route_resource lazy recursive";

/***/ }),

/***/ "./src/app/app.component.css":
/*!***********************************!*\
  !*** ./src/app/app.component.css ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = ""

/***/ }),

/***/ "./src/app/app.component.html":
/*!************************************!*\
  !*** ./src/app/app.component.html ***!
  \************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "<div class=\"container-fluid\">\r\n  <div class=\"row content\">\r\n    <!-- <div class=\"col-sm-2 sidenav hidden-xs\">\r\n      <h2>Reports</h2>\r\n      <ul class=\"nav nav-pills nav-stacked\">\r\n        <li class=\"active\"><a href=\"#section1\">Dashboard<br/><br/></a></li>\r\n        <li><a href=\"#section2\">Age</a> <br/><br/></li>\r\n        <li><a href=\"#section3\">Gender</a><br/><br/></li>\r\n        <li><a href=\"#section3\">Geo</a><br/><br/></li>\r\n      </ul><br>\r\n    </div> -->\r\n    <br>\r\n    \r\n    <div class=\"col-sm-10\">\r\n     \r\n        <div class=\"row\" style=\"font-size:12px;padding-bottom:10px\">\r\n        <div class=\"col-md-2\">\r\n          <label>Report Name test</label>\r\n          <select  style=\"padding-left:5%;width: 100%;height: 39px;border: 1px solid #f2f2f2;\" [(ngModel)]=\"tablename\" (change)=\"getData();changeInTable();\">\r\n            <option value=\"vw_dynamic_order_details\">Order</option>\r\n            <!-- <option value=\"pjp_pincode_area\">someother</option>\r\n            <option value=\"users\">users</option> -->\r\n          </select>\r\n        </div>\r\n        <div class=\"col-md-2\">\r\n          <label>Group By</label>\r\n          <ng-multiselect-dropdown\r\n              [data]=\"KeysForDropDown\"\r\n              [(ngModel)]=\"groupByColumn\"\r\n              [settings]=\"dropdownSettings\"\r\n              (ngModelchange)=\"getData()\"\r\n            >\r\n          </ng-multiselect-dropdown>\r\n        </div>\r\n        <div class=\"col-md-2\">\r\n          <label>Filetrs</label>\r\n          <ng-multiselect-dropdown\r\n              [data]=\"KeysForFilters\"\r\n              [(ngModel)]=\"filterByColumn\"\r\n              [settings]=\"dropdownSettings\"\r\n            >\r\n          </ng-multiselect-dropdown>\r\n        </div>        \r\n        <div class=\"col-md-2\" style=\"padding-right: 10px;\">\r\n          <label>FromDate</label>\r\n\r\n          <div class=\"input-group\" >\r\n            <input class=\"form-control\" placeholder=\"yyyy-mm-dd\"\r\n                   [(ngModel)]=\"fromDate\" ngbDatepicker #d=\"ngbDatepicker\" [maxDate]=\"maxDate\" (ngModelChange)=\"fixMinDateForTo()\">\r\n            <div class=\"input-group-append\">\r\n              <button class=\"btn btn-outline-secondary calendar_today\" (click)=\"d.toggle()\" type=\"button\"></button>\r\n            </div>\r\n          </div>\r\n        </div>\r\n        <div class=\"col-md-2\" style=\"padding-right: 10px;\">\r\n          <label>ToDate</label>\r\n\r\n          <div class=\"input-group\" >\r\n            <input class=\"form-control\" placeholder=\"yyyy-mm-dd\"\r\n                    [(ngModel)]=\"toDate\" ngbDatepicker #p=\"ngbDatepicker\" [minDate]=\"minDate\" (ngModelChange)=\"fixMinDateForFrom()\">\r\n            <div class=\"input-group-append\">\r\n              <button class=\"btn btn-outline-secondary calendar\" (click)=\"p.toggle()\" type=\"button\"></button>\r\n            </div>\r\n          </div>\r\n        </div>\r\n        <div class=\"col-md-1\" style=\"padding-top: 24px;margin-right: -1px\">\r\n          <button class=\"btn btn-primary\" style=\"font-size:15px\" (click)=\"getData()\">Submit</button>\r\n        </div>\r\n        <div class=\"col-md-1\" style=\"padding-top: 24px;margin-right: -1px\">\r\n          <button class=\"btn btn-primary\" style=\"font-size:15px\" (click)=\"exportData()\">export</button>\r\n        </div>\r\n        </div>\r\n        <div class=\"row\" style=\"overflow:auto;height: 480px;width: 1244px;\">\r\n          <table class=\"table table-striped\" [mfData]='showResponse' #mf=\"mfDataTable\" [mfRowsOnPage]=\"10\" style=\"font-size:12px;\">\r\n            <thead style=\"width:900px;background-color:#f2f2f2\" >\r\n              <tr>\r\n                <th style=\"width:20%\" *ngFor=\"let i of keys\" >\r\n                  <mfDefaultSorter [by]=i >{{i | heading}}\r\n                 \r\n                  </mfDefaultSorter><br />\r\n                  <input type=\"text\" style=\"width:65%\" [(ngModel)]=\"filtertext[i]\" (change)=\"getData()\"/>\r\n                </th>\r\n              </tr>\r\n            </thead>\r\n            <tbody style=\"width:900px\">\r\n                <tr  *ngFor=\"let res of mf.data;\">\r\n\r\n                    <td  *ngFor=\"let i of keys\">{{res[i]}}</td>\r\n\r\n                </tr>\r\n            </tbody>\r\n\r\n            <tfoot>\r\n              <tr>\r\n                <td colspan=\"12\">\r\n                  <mfBootstrapPaginator [rowsOnPageSet]=\"[5,10,25]\"></mfBootstrapPaginator>\r\n                </td>\r\n              </tr>\r\n            </tfoot>\r\n\r\n          </table>\r\n        </div>\r\n      </div>\r\n  </div>\r\n</div>"

/***/ }),

/***/ "./src/app/app.component.ts":
/*!**********************************!*\
  !*** ./src/app/app.component.ts ***!
  \**********************************/
/*! exports provided: AppComponent */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "AppComponent", function() { return AppComponent; });
/* harmony import */ var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @angular/core */ "./node_modules/@angular/core/fesm5/core.js");
/* harmony import */ var _filter_service__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./filter.service */ "./src/app/filter.service.ts");
/* harmony import */ var _angular_common__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @angular/common */ "./node_modules/@angular/common/fesm5/common.js");
var __decorate = (undefined && undefined.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (undefined && undefined.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};




var AppComponent = /** @class */ (function () {
    function AppComponent(filter, changedet, datepipe) {
        this.filter = filter;
        this.changedet = changedet;
        this.datepipe = datepipe;
        this.title = 'app';
        this.response = [];
        this.keys = [];
        this.filtertext = [];
        this.showResponse = [];
        this.groupByColumn = [];
        this.filterByColumn = [];
        this.inputData = {};
        this.arrangeKeys();
        this.fromDate = new Date();
        var month = this.fromDate.getMonth() + 1;
        this.minDate = { year: 1970, month: 1, day: 1 };
        this.fromDate = {
            'year': this.fromDate.getFullYear(),
            'month': month,
            'day': this.fromDate.getDate()
        };
        this.toDate = this.fromDate;
        this.dropdownSettings = {
            singleSelection: false,
            idField: 'item_id',
            textField: 'item_text',
            selectAllText: 'Select All',
            unSelectAllText: 'UnSelect All',
            itemsShowLimit: 1,
            allowSearchFilter: true
        };
        this.KeysForFilters = [
            { 'item_id': 'brand', 'item_text': 'brand' },
            { 'item_id': 'category', 'item_text': 'category' },
            { 'item_id': 'manufacturer', 'item_text': 'manufacturer' },
            { 'item_id': 'retailer', 'item_text': 'retailer' },
            { 'item_id': 'beat', 'item_text': 'beat' },
            { 'item_id': 'hub', 'item_text': 'hub' },
            { 'item_id': 'warehouse', 'item_text': 'warehouse' },
            { 'item_id': 'product', 'item_text': 'product' },
            { 'item_id': 'product_group', 'item_text': 'product Group' }
        ];
        this.getData();
    }
    AppComponent.prototype.getData = function () {
        var _this = this;
        console.log('1');
        this.KeysForDropDown = [];
        console.log(this.tablename);
        this.getTheKeys().then(function (data) {
            console.log('5');
            var from = _this.fromDate.year + '-' + _this.fromDate.month + '-' + _this.fromDate.day;
            var to = _this.toDate.year + '-' + _this.toDate.month + '-' + _this.toDate.day;
            console.log(from);
            console.log(_this.groupByColumn);
            console.log(_this.groupByColumn.length);
            var groupByData = '';
            if (_this.groupByColumn.length > 0) {
                console.log(_this.groupByColumn.sort());
                groupByData = _this.groupByColumn.join(',');
            }
            var displayData = '';
            var displayFields = '';
            console.log(_this.filterByColumn);
            if (_this.filterByColumn.length > 0) {
                console.log(_this.filterByColumn.sort());
                displayData = _this.filterByColumn.join(',');
                displayFields = "order_code";
                if (displayData.includes('brand')) {
                    displayFields += ",brand_name,manufacturer_name";
                }
                if (displayData.includes('category')) {
                    if (!displayFields.includes('brand_name')) {
                        displayFields += ",brand_name";
                    }
                    if (!displayFields.includes('manufacturer_name')) {
                        displayFields += ",manufacturer_name";
                    }
                    if (!displayFields.includes('category_name')) {
                        displayFields += ",category_name";
                    }
                }
                if (displayData.includes('manufacturer')) {
                    if (!displayFields.includes('manufacturer_name')) {
                        displayFields += ",manufacturer_name";
                    }
                }
                if (displayData.includes('retailer')) {
                    if (!displayFields.includes('brand_name')) {
                        displayFields += ",brand_name";
                    }
                    if (!displayFields.includes('manufacturer_name')) {
                        displayFields += ",manufacturer_name";
                    }
                    if (!displayFields.includes('category_name')) {
                        displayFields += ",category_name";
                    }
                    if (!displayFields.includes('retailer')) {
                        displayFields += ",retailer";
                    }
                    if (!displayFields.includes('retailer_code')) {
                        displayFields += ",retailer_code";
                    }
                }
                if (displayData.includes('beat')) {
                    if (!displayFields.includes('beat')) {
                        displayFields += ",beat";
                    }
                    if (!displayFields.includes('warehouse')) {
                        displayFields += ",warehouse";
                    }
                    if (!displayFields.includes('hub')) {
                        displayFields += ",hub";
                    }
                }
                if (displayData.includes('hub')) {
                    if (!displayFields.includes('hub')) {
                        displayFields += ",hub";
                    }
                }
                if (displayData.includes('warehouse')) {
                    if (!displayFields.includes('warehouse')) {
                        displayFields += ",warehouse";
                    }
                }
                if (displayData.includes('product')) {
                    if (!displayFields.includes('product_id')) {
                        displayFields += ",product_id";
                    }
                    if (!displayFields.includes('product_name')) {
                        displayFields += ",product_name";
                    }
                    if (!displayFields.includes('sku')) {
                        displayFields += ",sku";
                    }
                    if (!displayFields.includes('brand_name')) {
                        displayFields += ",brand_name";
                    }
                    if (!displayFields.includes('category_name')) {
                        displayFields += ",category_name";
                    }
                    if (!displayFields.includes('manufacturer_name')) {
                        displayFields += ",manufacturer_name";
                    }
                    if (!displayFields.includes('mrp')) {
                        displayFields += ",mrp";
                    }
                    if (!displayFields.includes('unit_price')) {
                        if (groupByData != '')
                            displayFields += ",sum(unit_price)";
                        else
                            displayFields += ",unit_price";
                    }
                    if (!displayFields.includes('elp')) {
                        if (groupByData != '')
                            displayFields += ",sum(elp)";
                        else
                            displayFields += ",elp";
                    }
                }
                if (displayData.includes('product')) {
                    if (!displayFields.includes('product_name')) {
                        displayFields += ",product_name";
                    }
                    if (!displayFields.includes('brand_name')) {
                        displayFields += ",brand_name";
                    }
                    if (!displayFields.includes('category_name')) {
                        displayFields += ",category_name";
                    }
                    if (!displayFields.includes('manufacturer_name')) {
                        displayFields += ",manufacturer_name";
                    }
                    if (!displayFields.includes('mrp')) {
                        displayFields += ",mrp";
                    }
                    if (!displayFields.includes('unit_price')) {
                        if (groupByData != '')
                            displayFields += ",sum(unit_price)";
                        else
                            displayFields += ",unit_price";
                    }
                    if (!displayFields.includes('elp')) {
                        if (groupByData != '')
                            displayFields += ",sum(elp)";
                        else
                            displayFields += ",elp";
                    }
                }
                displayFields += ",order_qty, invoice_qty, return_qty, cancel_qty,booked_tbv,delivered_tbv,booked_tgm,delivered_tgm";
                if (groupByData != '') {
                    displayFields += ",sum(order_qty),sum(invoice_qty),sum(return_qty),sum(cancel_qty),sum(booked_tbv),sum(delivered_tbv),sum(booked_tgm),sum(delivered_tgm)";
                }
                console.log(displayFields);
            }
            console.log(groupByData);
            _this.inputData = {
                'table': _this.tablename,
                'groupby': groupByData,
                'filtertext': _this.filterTextForDb,
                'displayData': displayFields,
                'fromdate': from,
                'todate': to
            };
            console.log(_this.inputData);
            _this.filter.gatherData(_this.inputData).then(function (result) {
                console.log('6');
                _this.response = result['_body'];
                _this.response = JSON.parse(_this.response);
                if (_this.response['Status'] == 200) {
                    _this.response = _this.response['ResponseBody'];
                    _this.keys = Object.keys(_this.response[0]);
                    _this.arrangeKeys();
                    _this.showResponse = [];
                    console.log(_this.response);
                    for (var i = 0; i < _this.response.length; i++) {
                        _this.showResponse[_this.showResponse.length] = _this.response[i];
                    }
                }
                else {
                    _this.showResponse = [];
                    _this.keys = [];
                }
            }, function (err) {
                console.log(err);
                _this.showResponse = [];
                _this.keys = [];
            });
        });
    };
    AppComponent.prototype.getTheKeys = function () {
        var _this = this;
        return new Promise(function (resolve, reject) {
            console.log('2');
            var from = _this.fromDate.year + '-' + _this.fromDate.month + '-' + _this.fromDate.day;
            var to = _this.toDate.year + '-' + _this.toDate.month + '-' + _this.toDate.day;
            var input = {
                'table': _this.tablename,
                'groupby': '',
                'filtertext': '',
                'displayData': '*',
                'fromdate': from,
                'todate': to
            };
            _this.filter.gatherData(input).then(function (res) {
                var data = res['_body'];
                data = JSON.parse(data);
                console.log(data['ResponseBody']);
                data = data['ResponseBody'];
                console.log(data.length);
                _this.keys = Object.keys(data[0]);
                _this.arrangeKeys();
                console.log('3');
                _this.filterTextForDb = '';
                for (var i = 0; i < _this.keys.length; i++) {
                    if (_this.filtertext[_this.keys[i]] != '' && _this.filtertext[_this.keys[i]] != undefined) {
                        if (_this.filterTextForDb == '') {
                            _this.filterTextForDb = 'where ' + _this.keys[i] + ' like ' + '\'%' + _this.filtertext[_this.keys[i]] + '%\'';
                        }
                        else {
                            _this.filterTextForDb = _this.filterTextForDb + ' and ' + _this.keys[i] + ' like ' + '\'%' + _this.filtertext[_this.keys[i]] + '%\'';
                        }
                    }
                }
                resolve(_this.filterTextForDb);
            });
            console.log('4');
        });
    };
    AppComponent.prototype.exportData = function () {
        if (this.showResponse.length > 0)
            this.filter.excelService(this.showResponse, this.tablename);
    };
    AppComponent.prototype.changeInTable = function () {
        console.log('in cgane');
        this.groupByColumn = '';
        this.getTheKeys();
    };
    AppComponent.prototype.fixMinDateForTo = function () {
        console.log(this.fromDate);
        this.minDate = this.fromDate;
    };
    AppComponent.prototype.fixMinDateForFrom = function () {
        this.maxDate = this.toDate;
    };
    AppComponent.prototype.arrangeKeys = function () {
        var _this = this;
        console.log(this.keys);
        var newKeys = this.keys;
        console.log(Object.keys(newKeys));
        this.KeysForDropDown = [];
        Object.values(newKeys).forEach(function (key) {
            _this.KeysForDropDown.push({ 'item_id': key, 'item_text': key });
        });
        console.log(this.KeysForDropDown);
    };
    AppComponent = __decorate([
        Object(_angular_core__WEBPACK_IMPORTED_MODULE_0__["Component"])({
            selector: 'app-root',
            template: __webpack_require__(/*! ./app.component.html */ "./src/app/app.component.html"),
            styles: [__webpack_require__(/*! ./app.component.css */ "./src/app/app.component.css")]
        }),
        __metadata("design:paramtypes", [_filter_service__WEBPACK_IMPORTED_MODULE_1__["FilterService"], _angular_core__WEBPACK_IMPORTED_MODULE_0__["ChangeDetectorRef"], _angular_common__WEBPACK_IMPORTED_MODULE_2__["DatePipe"]])
    ], AppComponent);
    return AppComponent;
}());



/***/ }),

/***/ "./src/app/app.module.ts":
/*!*******************************!*\
  !*** ./src/app/app.module.ts ***!
  \*******************************/
/*! exports provided: AppModule */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "AppModule", function() { return AppModule; });
/* harmony import */ var _angular_platform_browser__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @angular/platform-browser */ "./node_modules/@angular/platform-browser/fesm5/platform-browser.js");
/* harmony import */ var _angular_core__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @angular/core */ "./node_modules/@angular/core/fesm5/core.js");
/* harmony import */ var angular_6_datatable__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! angular-6-datatable */ "./node_modules/angular-6-datatable/index.js");
/* harmony import */ var angular_6_datatable__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(angular_6_datatable__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _app_component__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./app.component */ "./src/app/app.component.ts");
/* harmony import */ var _filter_service__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./filter.service */ "./src/app/filter.service.ts");
/* harmony import */ var _angular_http__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @angular/http */ "./node_modules/@angular/http/fesm5/http.js");
/* harmony import */ var _angular_forms__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @angular/forms */ "./node_modules/@angular/forms/fesm5/forms.js");
/* harmony import */ var _angular_platform_browser_animations__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @angular/platform-browser/animations */ "./node_modules/@angular/platform-browser/fesm5/animations.js");
/* harmony import */ var _angular_material_datepicker__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @angular/material/datepicker */ "./node_modules/@angular/material/esm5/datepicker.es5.js");
/* harmony import */ var _angular_material_form_field__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @angular/material/form-field */ "./node_modules/@angular/material/esm5/form-field.es5.js");
/* harmony import */ var _angular_material_select__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @angular/material/select */ "./node_modules/@angular/material/esm5/select.es5.js");
/* harmony import */ var _angular_material__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! @angular/material */ "./node_modules/@angular/material/esm5/material.es5.js");
/* harmony import */ var _ng_bootstrap_ng_bootstrap__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! @ng-bootstrap/ng-bootstrap */ "./node_modules/@ng-bootstrap/ng-bootstrap/fesm5/ng-bootstrap.js");
/* harmony import */ var angular_font_awesome__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! angular-font-awesome */ "./node_modules/angular-font-awesome/dist/angular-font-awesome.es5.js");
/* harmony import */ var _angular_common__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! @angular/common */ "./node_modules/@angular/common/fesm5/common.js");
/* harmony import */ var angular_2_dropdown_multiselect__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! angular-2-dropdown-multiselect */ "./node_modules/angular-2-dropdown-multiselect/esm5/angular-2-dropdown-multiselect.js");
/* harmony import */ var angular2_multiselect_dropdown_angular2_multiselect_dropdown__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! angular2-multiselect-dropdown/angular2-multiselect-dropdown */ "./node_modules/angular2-multiselect-dropdown/angular2-multiselect-dropdown.js");
/* harmony import */ var ng_multiselect_dropdown__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ng-multiselect-dropdown */ "./node_modules/ng-multiselect-dropdown/fesm5/ng-multiselect-dropdown.js");
/* harmony import */ var _heading_pipe__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./heading.pipe */ "./src/app/heading.pipe.ts");
var __decorate = (undefined && undefined.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};




















var AppModule = /** @class */ (function () {
    function AppModule() {
    }
    AppModule = __decorate([
        Object(_angular_core__WEBPACK_IMPORTED_MODULE_1__["NgModule"])({
            declarations: [
                _app_component__WEBPACK_IMPORTED_MODULE_3__["AppComponent"],
                _heading_pipe__WEBPACK_IMPORTED_MODULE_18__["HeadingPipe"]
            ],
            imports: [
                _angular_platform_browser__WEBPACK_IMPORTED_MODULE_0__["BrowserModule"], angular_6_datatable__WEBPACK_IMPORTED_MODULE_2__["DataTableModule"], _angular_http__WEBPACK_IMPORTED_MODULE_5__["HttpModule"], _angular_forms__WEBPACK_IMPORTED_MODULE_6__["FormsModule"], _angular_forms__WEBPACK_IMPORTED_MODULE_6__["ReactiveFormsModule"], _angular_platform_browser_animations__WEBPACK_IMPORTED_MODULE_7__["BrowserAnimationsModule"],
                _angular_material_select__WEBPACK_IMPORTED_MODULE_10__["MatSelectModule"], _angular_material_datepicker__WEBPACK_IMPORTED_MODULE_8__["MatDatepickerModule"], _angular_material_form_field__WEBPACK_IMPORTED_MODULE_9__["MatFormFieldModule"], _angular_material__WEBPACK_IMPORTED_MODULE_11__["MatNativeDateModule"], _angular_material__WEBPACK_IMPORTED_MODULE_11__["MatInputModule"], _ng_bootstrap_ng_bootstrap__WEBPACK_IMPORTED_MODULE_12__["NgbModule"], ng_multiselect_dropdown__WEBPACK_IMPORTED_MODULE_17__["NgMultiSelectDropDownModule"].forRoot(), angular_font_awesome__WEBPACK_IMPORTED_MODULE_13__["AngularFontAwesomeModule"], angular_2_dropdown_multiselect__WEBPACK_IMPORTED_MODULE_15__["MultiselectDropdownModule"], angular2_multiselect_dropdown_angular2_multiselect_dropdown__WEBPACK_IMPORTED_MODULE_16__["AngularMultiSelectModule"]
            ],
            providers: [_filter_service__WEBPACK_IMPORTED_MODULE_4__["FilterService"], _angular_material_datepicker__WEBPACK_IMPORTED_MODULE_8__["MatDatepickerModule"], _angular_common__WEBPACK_IMPORTED_MODULE_14__["DatePipe"]],
            bootstrap: [_app_component__WEBPACK_IMPORTED_MODULE_3__["AppComponent"]]
        })
    ], AppModule);
    return AppModule;
}());



/***/ }),

/***/ "./src/app/filter.service.ts":
/*!***********************************!*\
  !*** ./src/app/filter.service.ts ***!
  \***********************************/
/*! exports provided: FilterService */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "FilterService", function() { return FilterService; });
/* harmony import */ var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @angular/core */ "./node_modules/@angular/core/fesm5/core.js");
/* harmony import */ var _angular_http__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @angular/http */ "./node_modules/@angular/http/fesm5/http.js");
/* harmony import */ var file_saver__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! file-saver */ "./node_modules/file-saver/FileSaver.js");
/* harmony import */ var file_saver__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(file_saver__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var xlsx__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! xlsx */ "./node_modules/xlsx/xlsx.js");
/* harmony import */ var xlsx__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(xlsx__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _environments_environment__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../environments/environment */ "./src/environments/environment.ts");
var __decorate = (undefined && undefined.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (undefined && undefined.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};





var FilterService = /** @class */ (function () {
    function FilterService(http) {
        this.http = http;
        this.host = _environments_environment__WEBPACK_IMPORTED_MODULE_4__["environment"].BASE_URL;
        this.env = _environments_environment__WEBPACK_IMPORTED_MODULE_4__["environment"].port;
        //this.host = "http://localhost:1338/";
        this.host = "http://10.175.8.20:9527/";
        /*this._header=new Headers();
        this._header.append("allow-credentials",true);*/
    }
    FilterService.prototype.gatherData = function (data) {
        var _this = this;
        console.log('iam in service call');
        return new Promise(function (resolve, reject) {
            //var data={"table":"users","groupby":"password"};
            _this.http.post(_this.host + 'basic/getBasicDetails', data).toPromise().then(function (data) {
                resolve(data);
            }, function (err) {
                reject(err);
            });
        });
    };
    FilterService.prototype.excelService = function (json, excelFileName) {
        var workSheet = xlsx__WEBPACK_IMPORTED_MODULE_3__["utils"].json_to_sheet(json);
        var workBook = { Sheets: { 'data': workSheet }, SheetNames: ['data'] };
        var excelBuffer = xlsx__WEBPACK_IMPORTED_MODULE_3__["write"](workBook, { bookType: 'xlsx', type: 'array' });
        this.saveExcelFile(excelBuffer, excelFileName);
    };
    FilterService.prototype.saveExcelFile = function (buffer, fileName) {
        var excelType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8';
        var excelExtension = '.xlsx';
        var data = new Blob([buffer], { type: excelType });
        file_saver__WEBPACK_IMPORTED_MODULE_2__["saveAs"](data, fileName + '_export_' + new Date().getTime() + excelExtension);
    };
    FilterService = __decorate([
        Object(_angular_core__WEBPACK_IMPORTED_MODULE_0__["Injectable"])({
            providedIn: 'root'
        }),
        __metadata("design:paramtypes", [_angular_http__WEBPACK_IMPORTED_MODULE_1__["Http"]])
    ], FilterService);
    return FilterService;
}());



/***/ }),

/***/ "./src/app/heading.pipe.ts":
/*!*********************************!*\
  !*** ./src/app/heading.pipe.ts ***!
  \*********************************/
/*! exports provided: HeadingPipe */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "HeadingPipe", function() { return HeadingPipe; });
/* harmony import */ var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @angular/core */ "./node_modules/@angular/core/fesm5/core.js");
var __decorate = (undefined && undefined.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};

var HeadingPipe = /** @class */ (function () {
    function HeadingPipe() {
    }
    HeadingPipe.prototype.transform = function (value, args) {
        var text = value.split('_').join(' ');
        text = text.toLowerCase()
            .split(' ')
            .map(function (s) { return s.charAt(0).toUpperCase() + s.substring(1); })
            .join(' ');
        return text;
    };
    HeadingPipe = __decorate([
        Object(_angular_core__WEBPACK_IMPORTED_MODULE_0__["Pipe"])({
            name: 'heading'
        })
    ], HeadingPipe);
    return HeadingPipe;
}());



/***/ }),

/***/ "./src/environments/environment.ts":
/*!*****************************************!*\
  !*** ./src/environments/environment.ts ***!
  \*****************************************/
/*! exports provided: environment */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "environment", function() { return environment; });
// This file can be replaced during build by using the `fileReplacements` array.
// `ng build ---prod` replaces `environment.ts` with `environment.prod.ts`.
// The list of file replacements can be found in `angular.json`.
var environment = {
    production: false,
    BASE_URL: "http://10.175.8.20:1338/",
    port: "default"
};
/*
 * In development mode, to ignore zone related error stack frames such as
 * `zone.run`, `zoneDelegate.invokeTask` for easier debugging, you can
 * import the following file, but please comment it out in production mode
 * because it will have performance impact when throw error
 */
// import 'zone.js/dist/zone-error';  // Included with Angular CLI.


/***/ }),

/***/ "./src/main.ts":
/*!*********************!*\
  !*** ./src/main.ts ***!
  \*********************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _angular_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @angular/core */ "./node_modules/@angular/core/fesm5/core.js");
/* harmony import */ var _angular_platform_browser_dynamic__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @angular/platform-browser-dynamic */ "./node_modules/@angular/platform-browser-dynamic/fesm5/platform-browser-dynamic.js");
/* harmony import */ var _app_app_module__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./app/app.module */ "./src/app/app.module.ts");
/* harmony import */ var _environments_environment__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./environments/environment */ "./src/environments/environment.ts");




if (_environments_environment__WEBPACK_IMPORTED_MODULE_3__["environment"].production) {
    Object(_angular_core__WEBPACK_IMPORTED_MODULE_0__["enableProdMode"])();
}
Object(_angular_platform_browser_dynamic__WEBPACK_IMPORTED_MODULE_1__["platformBrowserDynamic"])().bootstrapModule(_app_app_module__WEBPACK_IMPORTED_MODULE_2__["AppModule"])
    .catch(function (err) { return console.log(err); });


/***/ }),

/***/ 0:
/*!***************************!*\
  !*** multi ./src/main.ts ***!
  \***************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\xampp\htdocs\bin_config\Code\public\ng-master-reports\src\main.ts */"./src/main.ts");


/***/ }),

/***/ 1:
/*!********************!*\
  !*** fs (ignored) ***!
  \********************/
/*! no static exports found */
/***/ (function(module, exports) {

/* (ignored) */

/***/ }),

/***/ 2:
/*!************************!*\
  !*** crypto (ignored) ***!
  \************************/
/*! no static exports found */
/***/ (function(module, exports) {

/* (ignored) */

/***/ }),

/***/ 3:
/*!************************!*\
  !*** stream (ignored) ***!
  \************************/
/*! no static exports found */
/***/ (function(module, exports) {

/* (ignored) */

/***/ })

},[[0,"runtime","vendor"]]]);
//# sourceMappingURL=main.js.map