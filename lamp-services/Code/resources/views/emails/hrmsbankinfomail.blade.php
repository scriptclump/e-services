<html>
  <head>
    <title> Employee Bank Details</title>
    <style>
      td, th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
      }
    </style>
  </head>   
  <body>

    Hello,
    <br/><br/>
    <p>Please check the updated bank details of <b> {{isset($empfullname['fullname']) ?  $empfullname['fullname'] : ""}} </b> with Employee Code - {{isset($empfullname['emp_code']) ?  $empfullname['emp_code'] : ""}} .</p>
    <br/><br/><br/>
    <h2 style="text-align:left;
                padding: 20px;
                margin-left: 200px;">Employee Bank Details</h1>
    <table style="font-family: arial, sans-serif;
                border-collapse: collapse;
                width: 75%;
                text-align:center;">
      <colgroup>
        <col span="2" style="background-color:white">
        <col style="background-color: #F0FFFF">
      </colgroup>
      <thead>
        <th>Bank Information</th>
        <th>Old Details</th>
        <th>Updated Details</th>
      </thead>
      <tbody id='bank_details'>
        <tr>
          <th>IFSC Code</th>
          <td>{{isset($oldbankdetails['ifsc_code']) ?  $oldbankdetails['ifsc_code'] : ""}}</td>
          <td>{{$bankInfo['ifsc_code']}}</td>
        </tr>
        <tr>
          <th>Bank Name</th>
          <td>{{isset($oldbankdetails['bank_name']) ?  $oldbankdetails['bank_name'] : ""}}</td>
          <td>{{$bankInfo['bank_name']}}</td>
        </tr>
        <tr>
          <th>Branch Name</th>
          <td>{{isset($oldbankdetails['branch_name']) ?  $oldbankdetails['branch_name'] : ""}}</td>
          <td>{{$bankInfo['branch_name']}}</td>
        </tr>
        <tr>
          <th>Name</th>
          <td>{{isset($oldbankdetails['acc_name']) ?  $oldbankdetails['acc_name'] : ""}}</td>
          <td>{{$bankInfo['acc_name']}}</td>
        </tr>
        <tr>
          <th>Account Number</th>
          <td>{{isset($oldbankdetails['acc_no']) ?  $oldbankdetails['acc_no'] : ""}}</td>
          <td>{{$bankInfo['acc_no']}}</td>
        </tr>
        <tr>
          <th>Account Type</th>
          <td>{{isset($oldbankdetails['acc_type']) ?  $oldbankdetails['acc_type'] : ""}}</td>
          <td>{{$bankInfo['acc_type']}}</td>
        </tr>
        <tr>
          <th>MICR Code</th>
          <td>{{isset($oldbankdetails['micr_code']) ?  $oldbankdetails['micr_code'] : ""}}</td>
          <td>{{$bankInfo['micr_code']}}</td>
        </tr>
      </tbody> 
    </table>
    
    <br/><br/><br/>
    <br/><br/>
  
    <p>                
      <div>Thanks,</div>
      <div>Ebutor.com</div>
    </p>   
  </body>
</html>


