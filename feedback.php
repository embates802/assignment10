<?php require("head.php");?>
<body>
    <h2>We Love Feedback!</h2>
    <?php
    include("../bin/validation-functions.php");
//    $debug = false;
    error_reporting(E_All);
//    if (isset($_GET["debug"])) { // ONLY do this in a classroom environment
//        $debug = true;
//    }
//    if ($debug)
//        print "<p>DEBUG MODE IS ON</p>";
       
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
       //%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
    //
    // SECTION: 1c form variables
    //
    // Initialize variables one for each form element
    // in the order they appear on the form
    $feedback = "";
    $rating = 0;
    
    //%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
    //
    // SECTION: 1d form error flags
    //
    // Initialize Error Flags one for each form element we validate
    // in the order they appear in section 1c.
    $feedbackERROR = false;
    $ratingERROR = false;
    
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
        $feedback = filter_var($_POST["txtaFeedback"], FILTER_SANITIZE_STRING);
        $rating = htmlentities($_POST["radRating"], ENT_QUOTES, "UTF-8");
        
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


        if ($feedback == "") {
            $errorMsg[] = "Please enter your feedback below.";
            $feedbackERROR = true;
        }
 
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2d Process Form - Passed Validation
    //
    // Process for when the form passes validation (the errorMsg array is empty)
    //
    if (!$errorMsg) {
//        if ($debug)
//            print "<p>Form is valid</p>";  
   
        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //
        // SECTION: 2e Save Data
        //
        $primaryKey = "";
        $dataEntered = false;
        try {
            $thisDatabase->db->beginTransaction();
            $query = "INSERT INTO tblFeedback (fldFeedback, fldRating) values (?, ?)";
            $data = array($feedback, $rating);
//            if ($debug) {
//                print "<p>sql " . $query;
//                print"<p><pre>";
//                print_r($data);
//                print"</pre></p>";
//            }
            $results = $thisDatabase->insert($query, $data);
            $primaryKey = $thisDatabase->lastInsert();
//            if ($debug)
//                print "<p>pmk= " . $primaryKey;
            // all sql statements are done so lets commit to our changes
            $dataEntered = $thisDatabase->db->commit();
            $dataEntered = true;
//            if ($debug)
//                print "<p>transaction complete ";
        } catch (PDOExecption $e) {
            $thisDatabase->db->rollback();
//            if ($debug)
//                print "Error!: " . $e->getMessage() . "</br>";
            $errorMsg[] = "There was a problem with accepting your data; please contact us directly.";
        }
        // If the transaction was successful, give success message
        if ($dataEntered) {
            print("Thank you for your feedback.");
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
//        print "<h2>Your Request has ";
//        print "been processed.</h2>";
    } else {
              
//####################################
//
// SECTION 3b Error Messages
//
// display any error messages before we print out the form
        if ($errorMsg) {
            print '<div id="errors">';
            print "<ul>\n";
            foreach ($errorMsg as $err) {
                print "<li>" . $err . "</li>\n";
            }
            print "</ul>\n";
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
            What do you think of this website? Please rate and leave your anonymous comments below.<p>
                        <label for="txtaFeedback" class="required">Comments:
                            <textarea name="txtaFeedback" id="txtaFeedback"
                                      rows ="6" cols ="80"
                                   onfocus="this.select()"
                                   ></textarea>
                        </label>
                    <p>How helpful was this website?</p>
                    <label><input type="radio" id="1" name="radRating"
                                  value="1" tabindex="420" checked="checked"
                                  <?php if($rating=="1") print 'checked';?>>1</label>
                    <label><input type="radio" id="2" name="radRating"
                                  value="2" tabindex="420" checked="checked"
                                  <?php if($rating=="2") print 'checked';?>>2</label>
                    <label><input type="radio" id="3" name="radRating"
                                  value="3" tabindex="420" checked="checked"
                                  <?php if($rating=="3") print 'checked';?>>3</label>
                    <label><input type="radio" id="4" name="radRating"
                                  value="4" tabindex="420" checked="checked"
                                  <?php if($rating=="4") print 'checked';?>>4</label>
                    <label><input type="radio" id="5" name="radRating"
                                  value="5" tabindex="420" checked="checked"
                                  <?php if($rating=="5") print 'checked';?>>5</label>
                        <p>
                    <input type="submit" id="btnSubmit" name="btnSubmit" value="Submit" tabindex="900" class="button">
        </form>
        <?php
    } // end body submit
    ?>
</article>

    
