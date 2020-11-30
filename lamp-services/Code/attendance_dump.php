<?php
// Connect
    echo "<pre>";
    if (!function_exists('mssql_connect')) {
        exit('No support for MSSQL');
    }

    $link = mssql_connect('192.168.122.111:1433', 'Ebutor', 'sunera@123') or exit('Could not connect to server (192.168.122.111:1433)');

    mssql_select_db('APPROD', $link) or exit('Could not select the database');

    mssql_query('SET ANSI_WARNINGS ON');
    mssql_query('SET ANSI_NULLS ON');

// Query 1

    $rst = mssql_query('SELECT Emp_Id  FROM dbo.Employees WHERE Organization_Id = 14', $link);

    $emp_code = array();
    while ($row = mssql_fetch_assoc($rst)) {
		$emp_code[] = $row['Emp_Id'];
    }

    echo count($emp_code)."\n";

    $link2 = mssql_connect('10.175.8.79:1433', 'Ebutor', 'Sunera121') or exit('Could not connect to server (10.175.8.79:1433)');

    mssql_select_db('EPushServer2', $link2) or exit('Could not select the database');
    
// Query 2

    $yDate = date('Y-m-d 00:00:00',strtotime("-1 days"));
    $qry = "SELECT *  FROM dbo.Attendance WHERE EmpId IN (400073) AND Date = '$yDate'";
    echo $qry."\n";
    $rst = mssql_query($qry, $link2);

    $emp_attendance = array();
    while ($row = mssql_fetch_assoc($rst)) {
        $emp_attendance[] = $row;
    }

    echo count($emp_attendance)."\n";
    print_r($emp_attendance);

?>