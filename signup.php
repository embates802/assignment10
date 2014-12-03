<?php require("head.php");
require("../bin/mail-message.php");?>
    <h2>Bottoms Up! In Your Inbox</h2>
    <?php
    include("../bin/validation-functions.php");
    error_reporting(E_All);
    require_once('../bin/myDatabase.php');
    $dbUserName = get_current_user() . '_admin';
    $whichPass = "a";
    $dbName = strtoupper(get_current_user()) . '_BOTTOMS_UP';
    
    $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
    $yourURL = $domain . $phpSelf;
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
    
    $errorMsg = array();
    

    $mailed = false;
    $messageA = "";
    $messageB = "";
    $messageC = "";
    

    if (isset($_POST["btnSubmit"])) {

        $email = filter_var($_POST["txtEmail"], FILTER_SANITIZE_EMAIL);
        $firstName = filter_var($_POST["txtFirstName"], FILTER_SANITIZE_STRING);
        $lastName = filter_var($_POST["txtLastName"], FILTER_SANITIZE_STRING);
        $address = filter_var($_POST["txtAddress"], FILTER_SANITIZE_STRING);
        $city = filter_var($_POST["txtCity"], FILTER_SANITIZE_STRING);
        $state = filter_var($_POST["txtState"], FILTER_SANITIZE_STRING);
        $zip = filter_var($_POST["txtZip"], FILTER_SANITIZE_STRING);
        $phoneNumber = filter_var($_POST["txtPhoneNumber"], FILTER_SANITIZE_STRING);
        


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
        
        if (!verifyAlpha($state)) {
            $errorMsg[] = "Your state appears to have invalid characters.";
            $stateERROR = true;
        }        
        if (!verifyNumeric($zip)) {
            $errorMsg[] = "Your zip code appears to have invalid characters.";
            $zipERROR = true;
        }
        
        if (!verifyPhone($phoneNumber)) {
            $errorMsg[] = "Your phone number is not in the correct format.";
            $phoneERROR = true;
        }

        if (!$errorMsg) {

        $primaryKey = "";
        $dataEntered = false;
        try {
            $thisDatabase->db->beginTransaction();
            $query = "INSERT INTO tblUser (fldEmail, fldFirstName, fldLastName, fldAddress, fldCity, fldState, fldZip, fldPhoneNumber, fldFeatured, fldTips, fldLowCal) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $data = array($email, $firstName, $lastName, $address, $city, $state, $zip, $phoneNumber, $featured, $tips, $lowCal);

            $results = $thisDatabase->insert($query, $data);

            $primaryKey = $thisDatabase->lastInsert();

            $dataEntered = $thisDatabase->db->commit();
            $dataEntered = true;

        } catch (PDOExecption $e) {
            $thisDatabase->db->rollback();

            $errorMsg[] = "There was a problem with accepting your data; please contact us directly.";
        }
        if ($dataEntered) {

            $messageA = "You are on our list! ";

            $messageB = "You should start receiving our emails within one business week.";

            $messageC .= "<p><b>Email Address:</b><i>   " . $email . "</i></p>";

            $to = $email;
            $cc = "";
            $bcc = "";
            $from = "Bottoms Up! <noreply@yoursite.com>";
            $subject = "Welcome to the Bottoms Up! Newsletter";
            $mailed = sendMail($to, $cc, $bcc, $from, $subject, $messageA . $messageB . $messageC);
      
        }
    }
}

?>
<article id="main">
    <?php

    if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) { // closing of if marked with: end body submit
        print "<h2>Your Request has ";
        if (!$mailed) {
            print "not ";
        }
        print "been processed.</h2>";

    } else {
        

        if ($errorMsg) {
            print '<div id="errors">';
            print "<ul>\n";
            foreach ($errorMsg as $err) {
                print "<li>" . $err . "</li>\n";
            }
            print "</ul>\n";
            print '</div>';
        
    }
    
        ?>
        <form method="post"
              id="frmRegister">
            Interested in hearing more from us? Sign up to receive the weekly Bottoms Up! Drink Specials newsletter.
            <br>Choose if you'd like to learn more about our weekly featured cocktail, fun party-hosting tips, and/or drinking game ideas!
                    <p>Please enter your information below.</p>
                        <label for="txtEmail" class="required">Email:
                            <input type="text" id="txtEmail" name="txtEmail"
                                   value="<?php print $email; ?>"
                                   tabindex="120" maxlength="45"
                                   <?php if ($emailERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   >
                        </label>
                        <label for="txtPhoneNumber" class="required">Phone Number:
                            <input type="text" id="txtPhoneNumber" name="txtPhoneNumber"
                                   value="<?php print $phoneNumber; ?>"
                                   tabindex="120" maxlength="45"
                                   <?php if ($phoneNumberERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   >
                        </label>                    
                        <br>
                        <label for="txtFirstName" class="required">First Name:
                            <input type="text" id="txtFirstName" name="txtFirstName"
                                   value="<?php print $firstName; ?>"
                                   tabindex ="130" maxlength ="20"
                                   <?php if ($firstNameERROR) print 'class="mistake"'; ?>
                                   onfocus ="this.select()"
                                   >
                        </label>
                        <label for="txtLastName" class="required">Last Name:
                            <input type="text" id="txtLastName" name="txtLastName"
                                   value="<?php print $lastName; ?>"
                                   tabindex ="130" maxlength ="20"
                                   <?php if ($lastNameERROR) print 'class="mistake"'; ?>
                                   onfocus ="this.select()"
                                   >
                        </label>
                        <br>
                        <label for="txtAddress" class="required">Address:
                            <input type="text" id="txtAddress" name="txtAddress"
                                   value="<?php print $address; ?>"
                                   tabindex ="130" maxlength ="50"
                                   <?php if ($addressERROR) print 'class="mistake"'; ?>
                                   onfocus ="this.select()"
                                   >
                        </label>
                        <label for="txtCity" class="required">City:
                            <input type="text" id="txtCity" name="txtCity"
                                   value="<?php print $city; ?>"
                                   tabindex ="130" maxlength ="50"
                                   <?php if ($cityERROR) print 'class="mistake"'; ?>
                                   onfocus ="this.select()"
                                   >
                        </label>
                        <label for="txtState" class="required">State:
                            <input type="text" id="txtState" name="txtState" size="4"
                                   value="<?php print $state; ?>"
                                   tabindex ="130" maxlength ="2"
                                   <?php if ($stateERROR) print 'class="mistake"'; ?>
                                   onfocus ="this.select()"
                                   >
                        </label>
                        <label for="txtZip" class="required">Zip:
                            <input type="text" id="txtZip" name="txtZip" size="8"
                                   value="<?php print $zip; ?>"
                                   tabindex ="130" maxlength ="5"
                                   <?php if ($zipERROR) print 'class="mistake"'; ?>
                                   onfocus ="this.select()"
                                   >
                        </label>
                        <p>Please indicate what info you would like to learn more about:<br>
                        <label><input type="checkbox" id="chkFeatured" name="chkFeatured" value="featured"
                              <?php if ($featured) print'checked';?>
                                      tabindex="321" >Featured Cocktails</label><br>
                <label><input type="checkbox" id="chkTips" name="chkTips" value="tips"
                              <?php if ($tips) print'checked';?>
                              tabindex="323" >Party-Hosting Tips</label><br>
                  <label><input type="checkbox" id="chkLowCal" name="chkLowCal" value="lowCal"
                              <?php if ($lowCal) print'checked';?>
                              tabindex="323" >Drinking Games</label>
                        <p>    
                    <input type="submit" id="btnSubmit" name="btnSubmit" value="Sign Me Up!" tabindex="900" class="button">
        </form>
        <?php
    }
    ?>
</article>



<?php
if ($debug)
    print "<p>END OF PROCESSING</p>";
?>
</html>
        