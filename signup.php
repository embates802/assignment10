<?php require("head.php");?>
<body>
    <h2>Sign Up For Our Newsletter</h2>
    <?php
    include "../bin/validation-functions.php";
    $debug = false;
    error_reporting(E_All);
    if (isset($_GET["debug"])) { // ONLY do this in a classroom environment
        $debug = true;
    }
    if ($debug)
        print "<p>DEBUG MODE IS ON</p>";
    
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    
    /* ##### Step one 
     * 
     * create your database object using the appropriate database username
    */
    require_once('../bin/myDatabase.php');
//    $searchQuery = $_GET['searchQuery']; //Get the SQL Statement that was requested
    $dbUserName = get_current_user() . '_admin';
    $whichPass = "a"; //flag for which one to use.
    $dbName = strtoupper(get_current_user()) . '_BOTTOMS_UP';
    
    $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
    
     //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
    //
    // SECTION: 1b Security
    //
    // define security variable to be used in SECTION 2a.
    $yourURL = $domain . $phpSelf;
    //%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
    //
    // SECTION: 1c form variables
    //
    // Initialize variables one for each form element
    // in the order they appear on the form
    $email = "";
    $firstName = "";
    $lastName = "";
    $address = "";
    $city = "";
    $state = "";
    $zip = "";
    $phoneNumber = "";
    $featured = 0;
    $tips = 0;
    $lowCal = 0;
    
    //%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
    //
    // SECTION: 1d form error flags
    //
    // Initialize Error Flags one for each form element we validate
    // in the order they appear in section 1c.
    $emailERROR = false;
    $firstNameERROR = false;
    $lastNameERROR = false;
    $addressERROR = false;
    $cityERROR = false;
    $stateERROR = false;
    $zipERROR = false;
    $phoneNumberERROR = false;
    $featuredERROR = false;
    $tipsERROR = false;
    $lowCalERROR = false;
    
    //%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
    //
    // SECTION: 1e misc variables
    //
    // create array to hold error messages filled (if any) in 2d displayed in 3c.
    $errorMsg = array();
    
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2 Process for when the form is submitted
    //
    if (isset($_POST["btnSubmit"])) {

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2b Sanitize (clean) data
    // remove any potential JavaScript or html code from users input on the
    // form. Note it is best to follow the same order as declared in section 1c.
        $email = filter_var($_POST["txtEmail"], FILTER_SANITIZE_EMAIL);
        $firstName = filter_var($_POST["txtFirstName"], FILTER_SANITIZE_STRING);
        $lastName = filter_var($_POST["txtLastName"], FILTER_SANITIZE_STRING);
        $address = filter_var($_POST["txtAddress"], FILTER_SANITIZE_STRING);
        $city = filter_var($_POST["txtCity"], FILTER_SANITIZE_STRING);
        $state = filter_var($_POST["txtState"], FILTER_SANITIZE_STRING);
        $zip = filter_var($_POST["txtzip"], FILTER_SANITIZE_STRING);
        $phoneNumber = filter_var($_POST["txtPhoneNumber"], FILTER_SANITIZE_STRING);
        
        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2c Validation
    //
    // Validation section. Check each value for possible errors, empty or
    // not what we expect. You will need an IF block for each element you will
    // check (see above section 1c and 1d). The if blocks should also be in the
    // order that the elements appear on your form so that the error messages
    // will be in the order they appear. errorMsg will be displayed on the form
    // see section 3b. The error flag ($emailERROR) will be used in section 3c.


        if ($email == "") {
            $errorMsg[] = "Please enter your email address.";
            $emailERROR = true;
        } elseif (!verifyEmail($email)) {
            $errorMsg[] = "Your email address appears to be incorrect.";
            $emailERROR = true;
        }
        
        if ($firstName == ""){
            $errorMsg[] = "Please enter your first name.";
            $firstNameERROR = true;
        } elseif (!verifyAlphaNum($firstName)) {
            $errorMsg[] = "Your first name appears to have invalid characters.";
            $firstNameERROR = true;
        }
        
        if ($lastName == ""){
            $errorMsg[] = "Please enter your last name.";
            $lastNameERROR = true;
        } elseif (!verifyAlphaNum($lastName)) {
            $errorMsg[] = "Your last name appears to have invalid characters.";
            $lastNameERROR = true;
        }
        
        if (!verifyAlphaNum($address)) {
            $errorMsg[] = "Your address appears to have invalid characters.";
            $addressERROR = true;
        }
        
        if (!verifyAlpha($city)) {
            $errorMsg[] = "Your city appears to have invalid characters.";
            $cityERROR = true;
        }
        
        if (!verifyNumeric($zip)) {
            $errorMsg[] = "Your zip code appears to have invalid characters.";
            $zipERROR = true;
        }
        
        if (!verifyAlpha($state)) {
            $errorMsg[] = "Your state appears to have invalid characters.";
            $cityERROR = true;
        }
        
        if (!verifyPhone($phoneNumber)) {
            $errorMsg[] = "Your phone number is not in the correct format.";
            $phoneERROR = true;
        }
        
        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //
        // SECTION: 2d Process Form - Passed Validation
        //
        // Process for when the form passes validation (the errorMsg array is empty)
        //
        if (!$errorMsg) {
            if ($debug)
                print "<p>Form is valid</p>";
    
        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //
        // SECTION: 2e Save Data
        //
        $primaryKey = "";
        $dataEntered = false;
        try {
            $thisDatabase->db->beginTransaction();
            $query = 'INSERT INTO tblUser (pmkEmail, fldFirstName, fldLastName, fldAddress, fldCity, fldState, fldZip, fldPhoneNumber, fldFeatured, fldTips, fldLowCal) values (?, ?, ?, ?, ?, ?, ?, ?)';
            $data = array($email, $firstName, $lastName, $address, $city, $state, $zip, $phoneNumber, $featured, $tips, $lowCal);
            if ($debug) {
                print "<p>sql " . $query;
                print"<p><pre>";
                print_r($data);
                print"</pre></p>";
            }
            $results = $thisDatabase->insert($query, $data);

            $primaryKey = $thisDatabase->lastInsert();
            if ($debug)
                print "<p>pmk= " . $primaryKey;
            // all sql statements are done so lets commit to our changes
            $dataEntered = $thisDatabase->db->commit();
            $dataEntered = true;
            if ($debug)
                print "<p>transaction complete ";
        } catch (PDOExecption $e) {
            $thisDatabase->db->rollback();
            if ($debug)
                print "Error!: " . $e->getMessage() . "</br>";
            $errorMsg[] = "There was a problem with accepting your data please contact us directly.";
        }
        // If the transaction was successful, give success message
        if ($dataEntered) {
            if ($debug)
                print "<p>data entered now prepare keys ";
        }
    }
}
//#############################################################################
//
// SECTION 3 Display Form
//
?>
<article id="main">
    <?php
//####################################
//
// SECTION 3a.
//
//
//
//
// If its the first time coming to the form or there are errors we are going
// to display the form.
    if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) { // closing of if marked with: end body submit
        print "<h1>You are on our list!</h1>";
        print "<h2>You should expect to start receiving emails from us within one business week.</h2>";
    } else {
        
               
//####################################
//
// SECTION 3b Error Messages
//
// display any error messages before we print out the form
        if ($errorMsg) {
            print '<div id="errors">';
            print "<ol>\n";
            foreach ($errorMsg as $err) {
                print "<li>" . $err . "</li>\n";
            }
            print "</ol>\n";
            print '</div>';
        
    }
    
//####################################
//
// SECTION 3c html Form
//
        /* Display the HTML form. note that the action is to this same page. $phpSelf
          is defined in top.php
          NOTE the line:
          value="<?php print $email; ?>
          this makes the form sticky by displaying either the initial default value (line 35)
          or the value they typed in (line 84)
          NOTE this line:
          <?php if($emailERROR) print 'class="mistake"'; ?>
          this prints out a css class so that we can highlight the background etc. to
          make it stand out that a mistake happened here.
         */
        ?>
        <form action="<?php print $phpSelf; ?>"
              method="post"
              id="frmRegister">
            <p>Interested in hearing more from us? Sign up to receive the weekly Bottoms Up! Drink Specials newsletter.</p>
            <p>Choose if you'd like to learn about our weekly featured cocktail, fun party-hosting tips, and/or drinking game ideas!</p>
                    <p>Please enter your information below.</p>
                        <label for="txtEmail" class="required">Email:
                            <input type="text" id="txtEmail" name="txtEmail"
                                   value="<?php print $email; ?>"
                                   tabindex="120" maxlength="45" placeholder="Enter your email address"
                                   <?php if ($emailERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   >
                        </label>
                        <br>
                        <label for="txtFirstName" class="required">First Name:
                            <input type="text" id="txtFirstName" name="txtFirstName"
                                   value="<?php print $firstName; ?>"
                                   tabindex ="130" maxlength ="20" placeholder="Enter your first name"
                                   <?php if ($firstNameERROR) print 'class="mistake"'; ?>
                                   onfocus ="this.select()"
                                   >
                        </label>
                        <label for="txtLastName" class="required">Last Name:
                            <input type="text" id="txtLastName" name="txtLastName"
                                   value="<?php print $lastName; ?>"
                                   tabindex ="130" maxlength ="20" placeholder="Enter your last name"
                                   <?php if ($lastNameERROR) print 'class="mistake"'; ?>
                                   onfocus ="this.select()"
                                   >
                        </label>
                        <br>
                        <label for="txtAddress" class="required">Address:
                            <input type="text" id="txtAddress" name="txtAddress"
                                   value="<?php print $address; ?>"
                                   tabindex ="130" maxlength ="50" placeholder="Enter your address"
                                   <?php if ($addressERROR) print 'class="mistake"'; ?>
                                   onfocus ="this.select()"
                                   >
                        </label>
                        <label for="txtCity" class="required">City:
                            <input type="text" id="txtCity" name="txtCity"
                                   value="<?php print $address; ?>"
                                   tabindex ="130" maxlength ="50" placeholder="Enter your address"
                                   <?php if ($addressERROR) print 'class="mistake"'; ?>
                                   onfocus ="this.select()"
                                   >
                        </label>
                        <p>
                    <input type="submit" id="btnSubmit" name="btnSubmit" value="Sign Me Up!" tabindex="900" class="button">
        </form>
        <?php
    } // end body submit
    ?>
</article>



<?php
if ($debug)
    print "<p>END OF PROCESSING</p>";
?>
</article>
</body>
</html>
        