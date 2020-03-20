<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validation_functions.php"); ?>

<?php 
    // use the function ConfirmLoggedIn to see if the current user    
    // is allowed to view this page
    ConfirmLoggedIn();
?>

<?php
    $User = FindMemberById($_SESSION["MemberId"]);
    
    // Check to see if the current user can edit members.
    // If not, then the user cannot have access to this page.
    if($User["EditMembers"] == 0) {
        RedirectTo("index.php");
    }
?>

<?php $InvalidData = "No"; ?>

<?php
    if (isset($_POST['submit'])) {  

        // set $InvalidData to No
        $InvalidData = "No";
        // Process the form  
        // validations  
        $required_fields = array("FirstName", "LastName", "MemberTypeSelect", "Email");  
        ValidatePresences($required_fields);

        $fields_with_max_lengths = array("FirstName" => 30, "LastName" => 30, "Email" => 50, "Parent1Name" => 50, "Parent1Email" => 50, "Parent2Name" => 50, "Parent2Email" => 50, "Address" => 50, "City" => 30, "State" => 2, "ZipCode" => 10, "Subdivision" => 50);  
        ValidateMaxLengths($fields_with_max_lengths);
        
        ValidateEmail($_POST["Email"], "Email");
        
        // If Parent1Email is not empty, then validate else bypass validation
        if(strlen($_POST["Parent1Email"]) > 0) {
            ValidateEmail($_POST["Parent1Email"], "Parent1");
        }
        // If Parent2Email is not empty, then validate else bypass validation
        if(strlen($_POST["Parent2Email"]) > 0) {
            ValidateEmail($_POST["Parent2Email"], "Parent2");
        }
        // If Password is not empty, then validate else bypass validation
        if(strlen($_POST["Password"]) > 0) {
            ValidatePassword($_POST["Password"]);
        }

        // If HomePhone is not empty, then validate else bypass validation
        // Phone number validations also format the number to only store
        // digits 0-9 in the MySQL database.
        if(strlen($_POST["HomePhone"]) > 0) {
            $FormattedHomePhone = ValidatePhone($_POST["HomePhone"],"Home");
        } else {
            $FormattedHomePhone = "";
        }
        // If CellPhone is not empty, then validate else bypass validation
        // Phone number validations also format the number to only store
        // digits 0-9 in the MySQL database.
        if(strlen($_POST["CellPhone"]) > 0) {
            $FormattedCellPhone = ValidatePhone($_POST["CellPhone"],"Cell");
        } else {
            $FormattedCellPhone = "";
        }
        // If Parent1Phone is not empty, then validate else bypass validation
        // Phone number validations also format the number to only store
        // digits 0-9 in the MySQL database.
        if(strlen($_POST["Parent1Phone"]) > 0) {
            $FormattedParent1Phone = ValidatePhone($_POST["Parent1Phone"],"Parent1");
        } else {
            $FormattedParent1Phone = "";
        }
        // If Parent2Phone is not empty, then validate else bypass validation
        // Phone number validations also format the number to only store
        // digits 0-9 in the MySQL database.
        if(strlen($_POST["Parent2Phone"]) > 0) {
            $FormattedParent2Phone = ValidatePhone($_POST["Parent2Phone"],"Parent2");
        } else {
            $FormattedParent2Phone = "";
        }
        
        if (empty($errors)) {    
            // Perform Create    
            $FirstName = MysqlPrep($_POST["FirstName"]);    
            $LastName = MysqlPrep($_POST["LastName"]);    
            $Email = MysqlPrep($_POST["Email"]);
            
            
            // Check new member email to see if another user has the same email.
            // If another member has the same email, create the error message and 
            // return to the New Member form.
            $Member = FindMemberByEmail($Email);
            if(!$Member) {
            
                $CellPhone = MysqlPrep($FormattedCellPhone);

                // If Password is not empty, then encrypt else bypass encryption
                if(strlen($_POST["Password"]) > 0) {
                    $HashedPassword = PasswordEncrypt($_POST["Password"]);
                } else {
                    $HashedPassword = "";
                }

                $CurrentGrade = intval($_POST["CurrentGradeSelect"]);    
                $Gender = intval($_POST["GenderSelect"]);
                $MemberTypeId = $_POST["MemberTypeSelect"];
                $Ethnicity = intval($_POST["EthnicitySelect"]);
                $Race = intval($_POST["RaceSelect"]);
                // Convert a date string into a Unix date.
                $ModifiedDate = strtotime($_POST["Birthdate"]);
                // Convert a Unix date into a MySQL format date.
                $Birthdate = date('Y-m-d H:i:s', $ModifiedDate);
                $HomePhone = MysqlPrep($FormattedHomePhone);
                $ShirtSize = intval($_POST["ShirtSizeSelect"]);
                $Parent1Name = MysqlPrep($_POST["Parent1Name"]);    
                $Parent1Email = MysqlPrep($_POST["Parent1Email"]);    
                $Parent1Phone = MysqlPrep($FormattedParent1Phone);    
                $Parent2Name = MysqlPrep($_POST["Parent2Name"]);    
                $Parent2Email = MysqlPrep($_POST["Parent2Email"]);    
                $Parent2Phone = MysqlPrep($FormattedParent2Phone);    
                $Address = MysqlPrep($_POST["Address"]);
                $City = MysqlPrep($_POST["City"]);
                $State = MysqlPrep($_POST["State"]);
                $ZipCode = MysqlPrep($_POST["ZipCode"]);
                $Subdivision = MysqlPrep($_POST["Subdivision"]);

                // Cannot pass true/false to MySQL. Convert to 1/0.
                if(isset($_POST["EditMembers"])) {
                    $EditMembers = 1;
                } else {
                    $EditMembers = 0;
                }

                $query  = "INSERT INTO Members (";    
                $query .= "  FirstName, LastName, Email, HashedPassword, CurrentGrade, Gender, MemberTypeId, Ethnicity, Race, Birthdate, HomePhone, CellPhone, EditMembers, ShirtSize, Parent1Name, Parent1Email, Parent1Phone, Parent2Name, Parent2Email, Parent2Phone, Address, City, State, ZipCode, Subdivision";    
                $query .= ") VALUES (";    
                $query .= "  '{$FirstName}', '{$LastName}', '{$Email}', '{$HashedPassword}', {$CurrentGrade}, {$Gender}, {$MemberTypeId}, {$Ethnicity}, {$Race}, '{$Birthdate}', '{$HomePhone}', '{$CellPhone}', {$EditMembers}, {$ShirtSize}, '{$Parent1Name}', '{$Parent1Email}', '{$Parent1Phone}', '{$Parent2Name}', '{$Parent2Email}', '{$Parent2Phone}', '{$Address}', '{$City}', '{$State}', '{$ZipCode}', '{$Subdivision}'";    
                $query .= ")";    
                $result = mysqli_query($connection, $query);    

                if ($result) {      
                    // Success      
                    $_SESSION["message"] = "Member created.";      
                    RedirectTo("manage_members.php");    
                } else {      
                    // Failure      
                    $_SESSION["message"] = "Member creation failed.";    
                }
            } else {
                // Email is already in use
                $_SESSION["message"] = "Email matches another Member's email";
            }
        } else {
            $InvalidData = "Yes";
        }
    } else {  
        // This is probably a GET request
    }   // end: if (isset($_POST['submit']))

    ?>

    <?php $layout_context = "admin"; ?>
    <?php include("../includes/layouts/header.php"); ?>

    <div id="main">  
        <div id="navigation">    
            &nbsp;  
        </div>  
        <div id="page">    
            <?php echo message(); ?>    
            <?php echo FormErrors($errors); ?>
            <h2>New Member</h2>    
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">      
                <p>First Name:
                    <?php if ($InvalidData == "Yes") {
                        echo "<input type='text' name='FirstName' value=" . $_POST["FirstName"] . " />";
                    } else {
                        echo "<input type='text' name='FirstName' value='' />";
                    } ?>
                </p>      
                <p>Last Name:
                    <?php if ($InvalidData == "Yes") {
                        echo "<input type='text' name='LastName' value=" . $_POST["LastName"] . " />";
                    } else {
                        echo "<input type='text' name='LastName' value='' />";
                    } ?>
                </p>
                <p>Email:
                    <?php if ($InvalidData == "Yes") {
                        echo "<input type='text' name='Email' value=" . $_POST["Email"] . " />";
                    } else {
                        echo "<input type='text' name='Email' value='' />";
                    } ?>
                </p>
                <p>Cell Phone:
                    <?php if ($InvalidData == "Yes") {
                        echo "<input type='text' name='CellPhone' value=" . $_POST["CellPhone"] . " />Enter phone number in XXX-XXX-XXXX format";
                    } else {
                        echo "<input type='text' name='CellPhone' value='' />Enter phone number in XXX-XXX-XXXX format";
                    } ?>
                </p>
                <p>Home Phone:
                    <?php if ($InvalidData == "Yes") {
                        echo "<input type='text' name='HomePhone' value=" . $_POST["HomePhone"] . " />Enter phone number in XXX-XXX-XXXX format";
                    } else {
                        echo "<input type='text' name='HomePhone' value='' />Enter phone number in XXX-XXX-XXXX format";
                    } ?>
                </p>
                <p>User can edit Members <input type="checkbox" name="EditMembers" onchange="HidePassword(this);">
                </p>
                <p>Member Type:
                    <?php $MemberTypeSet = FindAllMemberTypes(); ?>
                    <select name="MemberTypeSelect" onchange="HideGrade(this.value);">
                        <option value="">Select...</option>
                    <?php while($MemberType = mysqli_fetch_assoc($MemberTypeSet)) { ?>                    
                        <option value="<?php echo $MemberType["MemberTypeId"]; ?>"><?php echo $MemberType["MemberTypeName"]; ?></option>
                    <?php } ?>
                    </select>
                <div id="DivPassword">
                    <p>Password:
                        <input type="password" name="Password" value="" />      
                    </p>
                </div>
                <div id="DivGrade">
                    <p>Current Grade:
                        <select name="CurrentGradeSelect">
                            <option value="">Select...</option>
                            <option value="9">9th</option>
                            <option value="10">10th</option>
                            <option value="11">11th</option>
                            <option value="12">12th</option>
                        </select>
                    </p>
                </div>
                <div id="DivGender">
                    <p>Gender:
                        <select name="GenderSelect">
                            <option value="">Select...</option>
                            <option value="1">Female</option>
                            <option value="2">Male</option>
                        </select>
                    </p>
                </div>
                <div id="DivEthnicity">
                    <p>Ethnicity:
                        <select name="EthnicitySelect">
                            <option value="">Select...</option>
                            <option value="1">Not of Hispanic/Latino/Spanish Origin</option>
                            <option value="2">Of Hispanic/Latino/Spanish Origin</option>
                        </select>
                    </p>
                </div>
                <div id="DivRace">
                    <p>Race:
                        <select name="RaceSelect">
                            <option value="">Select...</option>
                            <option value="1">Black/African American</option>
                            <option value="2">Asian Indian</option>
                            <option value="3">White/Caucasian</option>
                            <option value="4">American Indian/Native Alaskan</option>
                            <option value="5">Native Hawaiian/Other Pacific Islander</option>
                            <option value="6">Chinese</option>
                            <option value="7">Filipino</option>
                            <option value="8">Japanese</option>
                            <option value="9">Korean</option>
                            <option value="10">Vietnamese</option>
                            <option value="11">Other Asian</option>
                            <option value="12">Other Race</option>
                        </select>
                    </p>
                </div>
                <p>Birthdate:
                    <?php if ($InvalidData == "Yes") {
                        echo "<input type='text' name='Birthdate' value=" . $_POST["Birthdate"] . " />Enter Date in mm/dd/yyyy format";
                    } else {
                        echo "<input type='text' name='Birthdate' value='' />Enter Date in mm/dd/yyyy format";
                    } ?>
                </p>
                <p>Address:
                    <?php if ($InvalidData == "Yes") {
                        echo "<input type='text' name='Address' value=" . $_POST["Address"] . " />";
                        echo $_POST["Address"];
                    } else {
                        echo "<input type='text' name='Address' value='' />";
                    } ?>
                </p>      
                <p>City:
                    <?php if ($InvalidData == "Yes") {
                        echo "<input type='text' name='City' value=" . $_POST["City"] . " />";
                    } else {
                        echo "<input type='text' name='City' value='' />";
                    } ?>
                </p>      
                <p>State:
                    <?php if ($InvalidData == "Yes") {
                        echo "<input type='text' name='State' value=" . $_POST["State"] . " />";
                    } else {
                        echo "<input type='text' name='State' value='TX' />";
                    } ?>
                </p>      
                <p>ZipCode:
                    <?php if ($InvalidData == "Yes") {
                        echo "<input type='text' name='ZipCode' value=" . $_POST["ZipCode"] . " />";
                    } else {
                        echo "<input type='text' name='ZipCode' value='' />";
                    } ?>
                </p>      
                <p>Subdivision:
                    <?php if ($InvalidData == "Yes") {
                        echo "<input type='text' name='Subdivision' value=" . $_POST["Subdivision"] . " />";
                    } else {
                        echo "<input type='text' name='Subdivision' value='' />";
                    } ?>
                </p>      
                <div id="DivShirtSize">
                    <p>Shirt Size:
                        <select name="ShirtSizeSelect">
                            <option value="">Select...</option>
                            <option value="1">S</option>
                            <option value="2">M</option>
                            <option value="3">L</option>
                            <option value="4">XL</option>
                            <option value="5">XXL</option>
                        </select>
                    </p>
                </div>
                <p>Parent 1 Name:
                    <?php if ($InvalidData == "Yes") {
                        echo "<input type='text' name='Parent1Name' value=" . $_POST["Parent1Name"] . " />";
                    } else {
                        echo "<input type='text' name='Parent1Name' value='' />";
                    } ?>
                </p>
                <p>Parent 1 Email:
                    <?php if ($InvalidData == "Yes") {
                        echo "<input type='text' name='Parent1Email' value=" . $_POST["Parent1Email"] . " />";
                    } else {
                        echo "<input type='text' name='Parent1Email' value='' />";
                    } ?>
                </p>
                <p>Parent 1 Phone:
                    <?php if ($InvalidData == "Yes") {
                        echo "<input type='text' name='Parent1Phone' value=" . $_POST["Parent1Phone"] . " />Enter phone number in XXX-XXX-XXXX format";
                    } else {
                        echo "<input type='text' name='Parent1Phone' value='' />Enter phone number in XXX-XXX-XXXX format";
                    } ?>
                </p>
                <p>Parent 2 Name:
                    <?php if ($InvalidData == "Yes") {
                        echo "<input type='text' name='Parent2Name' value=" . $_POST["Parent2Name"] . " />";
                    } else {
                        echo "<input type='text' name='Parent2Name' value='' />";
                    } ?>
                </p>
                <p>Parent 2 Email:
                    <?php if ($InvalidData == "Yes") {
                        echo "<input type='text' name='Parent2Email' value=" . $_POST["Parent2Email"] . " />";
                    } else {
                        echo "<input type='text' name='Parent2Email' value='' />";
                    } ?>
                </p>
                <p>Parent 2 Phone:
                    <?php if ($InvalidData == "Yes") {
                        echo "<input type='text' name='Parent2Phone' value=" . $_POST["Parent2Phone"] . " />Enter phone number in XXX-XXX-XXXX format";
                    } else {
                        echo "<input type='text' name='Parent2Phone' value='' />Enter phone number in XXX-XXX-XXXX format";
                    } ?>
                </p>
                 <script>
                    document.getElementById("DivPassword").style.display = "none";
                    document.getElementById("DivGrade").style.display = "none";
                </script>
                <input type="submit" name="submit" value="Add Member" />    
            </form>    
            <br />    
            <a href="manage_members.php">Cancel</a>  
        </div>
    </div>

<script>

    function HideGrade(value) {
        //value is the MemberTypeId. The MemberTypeId for Mentor is 2.
        //If Mentor is selected, then make the CurrentGradeSelect invisible and the Password visible.
        //If Mentor is not selected, then make the CurrentGradeSelect visible and the Password invisible.
        if(value == 2) {
            document.getElementById("DivGrade").style.display = "none";
            document.getElementById("DivPassword").style.display = "block";
        } else {
            document.getElementById("DivGrade").style.display = "block";
            document.getElementById("DivPassword").style.display = "none";
        }
    }

</script>


<?php include("../includes/layouts/footer.php"); ?>