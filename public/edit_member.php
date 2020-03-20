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

<?php
  $Member = FindMemberById($_GET["id"]);

  if (!$Member) {
    // admin ID was missing or invalid or 
    // admin couldn't be found in database
    RedirectTo("manage_members.php");
  }
?>

<?php
if (isset($_POST['submit'])) {
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
    // Perform Update
    $id = $Member["MemberId"];
    $FirstName = MysqlPrep($_POST["FirstName"]);    
    $LastName = MysqlPrep($_POST["LastName"]);    
    $Email = MysqlPrep($_POST["Email"]);
    $CellPhone = MysqlPrep($FormattedCellPhone);

    // If Password is not empty, then encrypt else bypass encryption
    if(strlen($_POST["Password"]) > 0) {
        $HashedPassword = PasswordEncrypt($_POST["Password"]);
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

    $query  = "UPDATE Members SET ";
    $query .= "FirstName = '{$FirstName}', ";
    $query .= "LastName = '{$LastName}', ";
    $query .= "Email = '{$Email}', ";

    // If Password is not empty, then save new password else keep old one
    if(strlen($_POST["Password"]) > 0) {
        $query .= "HashedPassword = '{$HashedPassword}', ";
    }

    $query .= "CurrentGrade = {$CurrentGrade}, ";
    $query .= "Gender = {$Gender}, ";
    $query .= "MemberTypeId = {$MemberTypeId}, ";
    $query .= "Ethnicity = {$Ethnicity}, ";
    $query .= "Race = {$Race}, ";
    $query .= "Birthdate = '{$Birthdate}', ";
    $query .= "HomePhone = '{$HomePhone}', ";
    $query .= "CellPhone = '{$CellPhone}', ";
    $query .= "EditMembers = {$EditMembers}, ";
    $query .= "ShirtSize = {$ShirtSize}, ";
    $query .= "Parent1Name = '{$Parent1Name}', ";
    $query .= "Parent1Email = '{$Parent1Email}', ";
    $query .= "Parent1Phone = '{$Parent1Phone}', ";
    $query .= "Parent2Name = '{$Parent2Name}', ";
    $query .= "Parent2Email = '{$Parent2Email}', ";
    $query .= "Parent2Phone = '{$Parent2Phone}', ";
    $query .= "Address = '{$Address}', ";
    $query .= "City = '{$City}', ";
    $query .= "State = '{$State}', ";
    $query .= "ZipCode = '{$ZipCode}', ";
    // Last field does not have a comma
    $query .= "Subdivision = '{$Subdivision}' ";
    $query .= "WHERE MemberId = {$id} ";
    $query .= "LIMIT 1";
    $result = mysqli_query($connection, $query);

    if ($result == 1) {
      // Success
      $_SESSION["message"] = "Member updated.";
      RedirectTo("manage_members.php");
    } else {
      // Failure
      $_SESSION["message"] = "Member update failed.";
    }
  }
} else {
  // This is probably a GET request
} // end: if (isset($_POST['submit']))
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
    <h2>Edit Member: <?php echo htmlentities($Member["FirstName"]) . " " . htmlentities($Member["LastName"]); ?></h2>
    <form action="edit_member.php?id=<?php echo urlencode($Member["MemberId"]); ?>" method="post">
                <p>First Name:
                    <input type="text" name="FirstName" value="<?php echo htmlentities($Member["FirstName"]); ?>" />      
                </p>      
                <p>Last Name:
                    <input type="text" name="LastName" value="<?php echo htmlentities($Member["LastName"]); ?>" />      
                </p>
                <p>Email:
                    <input type="text" name="Email" value="<?php echo htmlentities($Member["Email"]); ?>" />      
                </p>
                <p>Cell Phone:
                    <?php
                        // Add the dashes to the phone number for display
                        if(strlen($Member["CellPhone"]) > 0) {
                            $DisplayCellPhone = htmlentities($Member["CellPhone"]);
                            $DisplayCellPhone = substr_replace($DisplayCellPhone, "-", 3, 0);
                            $DisplayCellPhone = substr_replace($DisplayCellPhone, "-", 7, 0);
                        } else {
                            $DisplayCellPhone = "";
                        }
                    ?>
                    <input type="text" name="CellPhone" value="<?php echo $DisplayCellPhone; ?>" />Enter phone number in XXX-XXX-XXXX format      
                </p>
                <p>Home Phone:
                    <?php
                        // Add the dashes to the phone number for display
                        if(strlen($Member["HomePhone"]) > 0) {
                            $DisplayHomePhone = htmlentities($Member["HomePhone"]);
                            $DisplayHomePhone = substr_replace($DisplayHomePhone, "-", 3, 0);
                            $DisplayHomePhone = substr_replace($DisplayHomePhone, "-", 7, 0);
                        } else {
                            $DisplayHomePhone = "";
                        }
                    ?>
                    <input type="text" name="HomePhone" value="<?php echo $DisplayHomePhone; ?>" />Enter phone number in XXX-XXX-XXXX format      
                </p>
                <?php
                    if($Member["EditMembers"] == 1) {
                        echo "<p>User can edit Members <input type=\"checkbox\" name=\"EditMembers\" checked=\"checked\" ></p>";
                    } else {
                        echo "<p>User can edit Members <input type=\"checkbox\" name=\"EditMembers\" ></p>";
                    }
                ?>
                <p>Member Type:
                    <?php $MemberTypeSet = FindAllMemberTypes(); ?>
                    <select name="MemberTypeSelect" onchange="HideGrade(this.value);">
                        <option value="">Select...</option>
                    <?php
                        while($MemberType = mysqli_fetch_assoc($MemberTypeSet)) {
                            if($MemberType["MemberTypeId"] == $Member["MemberTypeId"]) {
                                echo "<option value=\"" . $MemberType["MemberTypeId"] . "\" selected >" . $MemberType["MemberTypeName"] . "</option>";
                            } else {
                                echo "<option value=\"" . $MemberType["MemberTypeId"] . "\">" . $MemberType["MemberTypeName"] . "</option>";
                            }
                        } 
                    ?>
                    </select>
                <div id="DivPassword">
                    <p>Password:
                        <input type="password" name="Password" value="" /> To keep existing password, do not enter a value
                    </p>
                </div>
                <div id="DivGrade">
                    <p>Current Grade:
                        <select name="CurrentGradeSelect">
                            <option value="">Select...</option>
                            <?php
                                if($Member["CurrentGrade"] == 9) {
                                    echo "<option value=\"9\" selected >9th</option>";
                                } else {
                                    echo "<option value=\"9\">9th</option>";
                                }

                                if($Member["CurrentGrade"] == 10) {
                                    echo "<option value=\"10\" selected >10th</option>";
                                } else {
                                    echo "<option value=\"10\">10th</option>";
                                }

                                if($Member["CurrentGrade"] == 11) {
                                    echo "<option value=\"11\" selected >11th</option>";
                                } else {
                                    echo "<option value=\"11\">11th</option>";
                                }

                                if($Member["CurrentGrade"] == 12) {
                                    echo "<option value=\"12\" selected >12th</option>";
                                } else {
                                    echo "<option value=\"12\">12th</option>";
                                }
                           ?>
                        </select>
                    </p>
                </div>
                <?php
                    // When this page is first rendered, make the Password and CurrentGrade fields visible or invisible
                    // based on the MemberTypeId.
                    if($Member["MemberTypeId"] == 1) {
                        echo "<script>document.getElementById(\"DivPassword\").style.display = \"none\";</script>";
                        echo "<script>document.getElementById(\"DivGrade\").style.display = \"block\";</script>";
                    } else if($Member["MemberTypeId"] == 2) {
                        echo "<script>document.getElementById(\"DivPassword\").style.display = \"block\";</script>";
                        echo "<script>document.getElementById(\"DivGrade\").style.display = \"none\";</script>";
                    } else {
                        echo "<script>document.getElementById(\"DivPassword\").style.display = \"none\";</script>";
                        echo "<script>document.getElementById(\"DivGrade\").style.display = \"none\";</script>";
                    }
                ?>
                <div id="DivGender">
                    <p>Gender:
                        <select name="GenderSelect">
                            <option value="">Select...</option>
                            <?php
                                if($Member["Gender"] == 1) {
                                    echo "<option value=\"1\" selected >Female</option>";
                                } else {
                                    echo "<option value=\"1\">Female</option>";
                                }

                                if($Member["Gender"] == 2) {
                                    echo "<option value=\"2\" selected >Male</option>";
                                } else {
                                    echo "<option value=\"2\">Male</option>";
                                }
                           ?>
                        </select>
                    </p>
                </div>
                <div id="DivEthnicity">
                    <p>Ethnicity:
                        <select name="EthnicitySelect">
                            <option value="">Select...</option>
                            <?php
                                if($Member["Ethnicity"] == 1) {
                                    echo "<option value=\"1\" selected >Not of Hispanic/Latino/Spanish Origin</option>";
                                } else {
                                    echo "<option value=\"1\">Not of Hispanic/Latino/Spanish Origin</option>";
                                }

                                if($Member["Ethnicity"] == 2) {
                                    echo "<option value=\"2\" selected >Of Hispanic/Latino/Spanish Origin</option>";
                                } else {
                                    echo "<option value=\"2\">Of Hispanic/Latino/Spanish Origin</option>";
                                }
                           ?>
                        </select>
                    </p>
                </div>
                <div id="DivRace">
                    <p>Race:
                        <select name="RaceSelect">
                            <option value="">Select...</option>
                            <?php
                                if($Member["Race"] == 1) {
                                    echo "<option value=\"1\" selected >Black/African American</option>";
                                } else {
                                    echo "<option value=\"1\">Black/African American</option>";
                                }

                                if($Member["Race"] == 2) {
                                    echo "<option value=\"2\" selected >Asian Indian</option>";
                                } else {
                                    echo "<option value=\"2\">Asian Indian</option>";
                                }

                                if($Member["Race"] == 3) {
                                    echo "<option value=\"3\" selected >White/Caucasian</option>";
                                } else {
                                    echo "<option value=\"3\">White/Caucasian</option>";
                                }

                                if($Member["Race"] == 4) {
                                    echo "<option value=\"4\" selected >American Indian/Native Alaskan</option>";
                                } else {
                                    echo "<option value=\"4\">American Indian/Native Alaskan</option>";
                                }

                                if($Member["Race"] == 5) {
                                    echo "<option value=\"5\" selected >Native Hawaiian/Other Pacific Islander</option>";
                                } else {
                                    echo "<option value=\"5\">Native Hawaiian/Other Pacific Islander</option>";
                                }

                                if($Member["Race"] == 6) {
                                    echo "<option value=\"6\" selected >Chinese</option>";
                                } else {
                                    echo "<option value=\"6\">Chinese</option>";
                                }

                                if($Member["Race"] == 7) {
                                    echo "<option value=\"7\" selected >Filipino</option>";
                                } else {
                                    echo "<option value=\"7\">Filipino</option>";
                                }

                                if($Member["Race"] == 8) {
                                    echo "<option value=\"8\" selected >Japanese</option>";
                                } else {
                                    echo "<option value=\"8\">Japanese</option>";
                                }

                                if($Member["Race"] == 9) {
                                    echo "<option value=\"9\" selected >Korean</option>";
                                } else {
                                    echo "<option value=\"9\">Korean</option>";
                                }

                                if($Member["Race"] == 10) {
                                    echo "<option value=\"10\" selected >Vietnamese</option>";
                                } else {
                                    echo "<option value=\"10\">Vietnamese</option>";
                                }

                                if($Member["Race"] == 11) {
                                    echo "<option value=\"11\" selected >Other Asian</option>";
                                } else {
                                    echo "<option value=\"11\">Other Asian</option>";
                                }

                                if($Member["Race"] == 12) {
                                    echo "<option value=\"12\" selected >Other Race</option>";
                                } else {
                                    echo "<option value=\"12\">Other Race</option>";
                                }

                            ?>
                        </select>
                    </p>
                </div>
                <p>Birthdate:
                    <?php // Convert a MySQL Date into United State format mm/dd/yyyy
                        $Date = strtotime(htmlentities($Member["Birthdate"])); 
                        $FormatedDate = date("m/d/Y", $Date); ?>
                    <input type="text" name="Birthdate" value="<?php echo $FormatedDate; ?>" />Enter Date in mm/dd/yyyy format
                </p>
                <p>Address:
                    <input type="text" name="Address" value="<?php echo htmlentities($Member["Address"]); ?>" />      
                </p>      
                <p>City:
                    <input type="text" name="City" value="<?php echo htmlentities($Member["City"]); ?>" />      
                </p>      
                <p>State:
                    <input type="text" name="State" value="<?php echo htmlentities($Member["State"]); ?>" />Enter two letter, all caps state abbreviation
                </p>      
                <p>ZipCode:
                    <input type="text" name="ZipCode" value="<?php echo htmlentities($Member["ZipCode"]); ?>" />      
                </p>      
                <p>Subdivision:
                    <input type="text" name="Subdivision" value="<?php echo htmlentities($Member["Subdivision"]); ?>" />      
                </p>      
                <div id="DivShirtSize">
                    <p>Shirt Size:
                        <select name="ShirtSizeSelect">
                            <option value="">Select...</option>
                            <?php
                                if($Member["ShirtSize"] == 1) {
                                    echo "<option value=\"1\" selected >S</option>";
                                } else {
                                    echo "<option value=\"1\">S</option>";
                                }

                                if($Member["ShirtSize"] == 2) {
                                    echo "<option value=\"2\" selected >M</option>";
                                } else {
                                    echo "<option value=\"2\">M</option>";
                                }

                                if($Member["ShirtSize"] == 3) {
                                    echo "<option value=\"3\" selected >L</option>";
                                } else {
                                    echo "<option value=\"3\">L</option>";
                                }

                                if($Member["ShirtSize"] == 4) {
                                    echo "<option value=\"4\" selected >XL</option>";
                                } else {
                                    echo "<option value=\"4\">XL</option>";
                                }
 
                                if($Member["ShirtSize"] == 5) {
                                    echo "<option value=\"5\" selected >XXL</option>";
                                } else {
                                    echo "<option value=\"5\">XXL</option>";
                                }
                           ?>
                        </select>
                    </p>
                </div>
                <p>Parent 1 Name:
                    <input type="text" name="Parent1Name" value="<?php echo htmlentities($Member["Parent1Name"]); ?>" />      
                </p>
                <p>Parent 1 Email:
                    <input type="text" name="Parent1Email" value="<?php echo htmlentities($Member["Parent1Email"]); ?>" />      
                </p>
                <p>Parent 1 Phone:
                    <?php
                        // Add the dashes to the phone number for display
                        if(strlen($Member["Parent1Phone"]) > 0) {
                            $DisplayParent1Phone = htmlentities($Member["Parent1Phone"]);
                            $DisplayParent1Phone = substr_replace($DisplayParent1Phone, "-", 3, 0);
                            $DisplayParent1Phone = substr_replace($DisplayParent1Phone, "-", 7, 0);
                        } else {
                            $DisplayParent1Phone = "";
                        }
                    ?>
                    <input type="text" name="Parent1Phone" value="<?php echo $DisplayParent1Phone; ?>" />Enter phone number in XXX-XXX-XXXX format      
                </p>
                <p>Parent 2 Name:
                    <input type="text" name="Parent2Name" value="<?php echo htmlentities($Member["Parent2Name"]); ?>" />      
                </p>
                <p>Parent 2 Email:
                    <input type="text" name="Parent2Email" value="<?php echo htmlentities($Member["Parent2Email"]); ?>" />      
                </p>
                <p>Parent 2 Phone:
                    <?php
                        // Add the dashes to the phone number for display
                        if(strlen($Member["Parent2Phone"]) > 0) {
                            $DisplayParent2Phone = htmlentities($Member["Parent2Phone"]);
                            $DisplayParent2Phone = substr_replace($DisplayParent2Phone, "-", 3, 0);
                            $DisplayParent2Phone = substr_replace($DisplayParent2Phone, "-", 7, 0);
                        } else {
                            $DisplayParent2Phone = "";
                        }
                    ?>
                    <input type="text" name="Parent2Phone" value="<?php echo $DisplayParent2Phone; ?>" />Enter phone number in XXX-XXX-XXXX format      
                </p>
      <input type="submit" name="submit" value="Save Changes" />
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