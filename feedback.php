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
    $date = "";
    
    //%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
    //
    // SECTION: 1d form error flags
    //
    // Initialize Error Flags one for each form element we validate
    // in the order they appear in section 1c.
    $feedbackERROR = false;
    $ratingERROR = false;
    $dateERROR = false;
    
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
        $date = filter_var($_POST["txtDate"], FILTER_SANITIZE_STRING);
        
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
        
        if ($date == ""){
            $errorMsg[] = "Please enter today's date.";
            $dateERROR = true;
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
            $query = "INSERT INTO tblFeedback (fldFeedback, fldRating, fldDate) values (?, ?, ?)";
            $data = array($feedback, $rating, $date);
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
//            if ($debug)
