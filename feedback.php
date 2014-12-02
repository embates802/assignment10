<?php require("head.php");
require("../bin/mail-message.php");?>
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
    
    // used for building email message to be sent and displayed
    $mailed = false;
    $messageA = "";
    $messageB = "";
    $messageC = "";
    
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
        $userID = filter_var($_POST["txtEmail"], FILTER_SANITIZE_EMAIL);
        $firstName = filter_var($_POST["txtFirstName"], FILTER_SANITIZE_STRING);
        $lastName = filter_var($_POST["txtLastName"], FILTER_SANITIZE_STRING);
        $address = filter_var($_POST["txtAddress"], FILTER_SANITIZE_STRING);
        $city = filter_var($_POST["txtCity"], FILTER_SANITIZE_STRING);
        $state = filter_var($_POST["txtState"], FILTER_SANITIZE_STRING);
        $zip = filter_var($_POST["txtZip"], FILTER_SANITIZE_STRING);
        $phoneNumber = filter_var($_POST["txtPhoneNumber"], FILTER_SANITIZE_STRING);
        