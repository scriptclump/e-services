<?php
// Connect
    echo "<pre>";
    if (!function_exists('mssql_connect')) {exit('No support for MSSQL');
    }

    $link = mssql_connect('192.168.122.111:1433', 'Ebutor', 'sunera@123') or exit('Could not connect to server (192.168.122.111:1433)');

    mssql_select_db('APPROD', $link) or exit('Could not select the database');

    mssql_query('SET ANSI_WARNINGS ON');
    mssql_query('SET ANSI_NULLS ON');

// Query 1

    $rst = mssql_query('SELECT * FROM dbo.Employees WHERE Organization_Id = 14 AND Emp_Id=400073', $link);

    $employee = array();
    while ($row = mssql_fetch_assoc($rst)) {
		$employee[] = $row;
    }

    /*echo count($employee)."'\n";
    print_r($employee);*/

    $link2 = new mysqli('10.175.8.31', 'fbedevapp', 'FbED@v2pP!$', 'devebutor');

    if ($link2->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    foreach($employee as $emp){
        // echo 'printing license';
        // echo ($emp['Driving_Licence_Expiry_Date'] === ''?$emp['Driving_Licence_Expiry_Date']:'NULL'); exit;

        $sql = "INSERT INTO 
                `devebutor`.`employee`
                (`legal_entity_id`,`business_unit_id`,`firstname`,`lastname`,`email_id`,`role_id`,`reporting_manager_id`,`designation`,`department`,`mobile_no`,`alternative_mno`,`employment_type`,`dob`,`father_name`,`mother_name`,`gender`,`marital_status`,`blood_group`,`nationality`,`aadhar_number`,`aadhar_name`,`pan_card_number`,`pan_card_name`,`driving_licence_number`,`dl_expiry_date`,`uan_number`,`passport_number`,`passport_valid_to`,`emp_code`,`doj`,`status`)
                VALUES (2,
                '".$emp['BusinessUnitId']."',
                '".$emp['First_Name']."',
                '".$emp['Last_Name']."',
                '".$emp['Email_Address']."',
                '".$emp['Authorization_Role_Id']."',
                '".$emp['Report_Manager_Id']."',
                '".$emp['Designation_Id']."',
                '".$emp['DepartmentId']."',
                '".$emp['Phone_Number']."',
                '".$emp['Home_Phone_Number']."',
                '".$emp['EmploymentTypeID']."',
                '".(!empty($emp['Date_Of_Birth'])?$emp['Date_Of_Birth']:NULL)."',
                '".$emp['Fathers_Name']."',
                '".$emp['MotherName']."',
                '".$emp['Gender']."',
                '".$emp['Martial_Status']."',
                '".$emp['Blood_Group']."',
                '".$emp['Nationality']."',
                '".$emp['Adhar_Card_Number']."',
                '".$emp['NameAsPerAdhar']."',
                '".$emp['Pancard_Number']."',
                '".$emp['NameAsPerPan']."',
                '".$emp['Driving_Licence_Number']."',
                '".($emp['Driving_Licence_Expiry_Date'] === ''?$emp['Driving_Licence_Expiry_Date']:NULL)."',
                '".(!empty($emp['UANNumber'])?$emp['UANNumber']:NULL)."',
                '".$emp['Passport_Number']."',
                '".(!empty($emp['Passport_Valid_To'])?$emp['Passport_Valid_To']:NULL)."',
                '".$emp['Emp_Id']."',
                '".(!empty($emp['Date_Of_Joining'])?$emp['Date_Of_Joining']:NULL)."',57148)";

        echo $emp['Driving_Licence_Expiry_Date']."\n";
        if ($link2->query($sql) === TRUE) {
            $last_id = $link2->insert_id;

            /*$sql2 = "Select user_id from devebutor.users where emp_code = '".$emp['Emp_Id']."'";

            $result = $link2->query($sql2);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $userId = $row['user_id'];
                    $updateSql = "UPDATE `devebutor`.`users`
                                    SET `emp_id` = '".$emp['Emp_Id']."' WHERE `user_id` = '".$userId."'";

                    $update = $link2->query($updateSql);
                }
            } */

            /*$sql2 = "INSERT INTO `devebutor`.`users`(`business_unit_id`,`emp_id`,`password`,`firstname`,`lastname`,`email_id`,`department`,`designation`,`mobile_no`,`landline_no`,`legal_entity_id`,`reporting_manager_id`,`emp_code`)VALUES($emp['BusinessUnitId'],$last_id,'57D2BE4CA4E5F73CAA4E99B29EFD69C7',$emp['First_Name'],$emp['Last_Name'],$emp['Email_Address'],$emp['DepartmentId'],$emp['Designation_Id'],$emp['Phone_Number'],$emp['Home_Phone_Number'],2,$emp['Report_Manager_Id'],$emp['Emp_Id'])";

            if ($link2->query($sql2) !== TRUE) {
                echo "Error: " . $sql2 . "<br>" . $link2->error;
            }*/
            
        } else {
            echo "Error: " . $sql . "<br>" . $link2->error;
        }


    }
    mysqli_close($link2);
?>