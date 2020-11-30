<?php
// Connect
    echo "<pre>";
    $link2 = new mysqli('10.175.8.31', 'fbedevapp', 'FbED@v2pP!$', 'devebutor');

    if ($link2->connect_error) {
        die("Connection failed: " . $link2->connect_error);
    } 

    $sql = "select * from users where legal_entity_id=2 and is_active =1";

    $users = $link2->query($sql);

    $userArr = array();
    if ($users->num_rows > 0) {
        // output data of each row
        while($row = $users->fetch_assoc()) {
            $userArr[] = $row;
        }
    } else {
        echo "0 results";
    }

    print_r($userArr);


    /*foreach($employee as $emp){

        $sql = "INSERT INTO `devebutor`.`employee`(`legal_entity_id`,`business_unit_id`,`firstname`,`lastname`,`email_id`,`role_id`,`reporting_manager_id`,`designation`,`department`,`mobile_no`,`alternative_mno`,`employment_type`,`dob`,`father_name`,`mother_name`,`gender`,`marital_status`,`blood_group`,`nationality`,`aadhar_number`,`aadhar_name`,`pan_card_number`,`pan_card_name`,`driving_licence_number`,`dl_expiry_date`,`uan_number`,`passport_number`,`passport_valid_to`,`emp_code`,`doj`,`status`)
                    VALUES(2,$emp['BusinessUnitId'],$emp['First_Name'],$emp['Last_Name'],$emp['Email_Address'],$emp['Authorization_Role_Id'],$emp['Report_Manager_Id'],$emp['Designation_Id'],$emp['DepartmentId'],$emp['Phone_Number'],$emp['Home_Phone_Number'],$emp['EmploymentTypeID'],$emp['Date_Of_Birth'],$emp['Fathers_Name'],$emp['MotherName'],$emp['Gender'],$emp['Martial_Status'],$emp['Blood_Group'],$emp['Nationality'],$emp['Adhar_Card_Number'],$emp['NameAsPerAdhar'],$emp['Pancard_Number'],$emp['NameAsPerPan'],$emp['Driving_Licence_Number'],$emp['Driving_Licence_Expiry_Date'],$emp['UANNumber'],$emp['Passport_Number'],$emp['Passport_Valid_To'],$emp['Emp_Id'],$emp['Date_Of_Joining'],57148)";
        if ($link2->query($sql) === TRUE) {
            $last_id = $link2->insert_id;

            $sql2 = "INSERT INTO `devebutor`.`users`(`business_unit_id`,`emp_id`,`password`,`firstname`,`lastname`,`email_id`,`department`,`designation`,`mobile_no`,`landline_no`,`legal_entity_id`,`reporting_manager_id`,`emp_code`)VALUES($emp['BusinessUnitId'],$last_id,'57D2BE4CA4E5F73CAA4E99B29EFD69C7',$emp['First_Name'],$emp['Last_Name'],$emp['Email_Address'],$emp['DepartmentId'],$emp['Designation_Id'],$emp['Phone_Number'],$emp['Home_Phone_Number'],2,$emp['Report_Manager_Id'],$emp['Emp_Id'])";

            if ($link2->query($sql2) !== TRUE) {
                echo "Error: " . $sql2 . "<br>" . $link2->error;
            }
            
        } else {
            echo "Error: " . $sql . "<br>" . $link2->error;
        }
        mysqli_query($link2, $sql);



    }*/
    mysqli_close($link2);
?>