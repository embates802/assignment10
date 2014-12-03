<?php require("head.php");?>

<?php
if($_SESSION["admin"]){ //This will log the user out, if logged in
    $_SESSION["admin"] = false;

    header('Location: index.php');
}else{ //Otherwise, log in.

        require_once('../bin/myDatabase.php');
//    $searchQuery = $_GET['searchQuery']; //Get the SQL Statement that was requested
    $dbUserName = get_current_user() . '_admin';
    $whichPass = "a"; //flag for which one to use.
    $dbName = strtoupper(get_current_user()) . '_BOTTOMS_UP';
    
    $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);

//Send the username to the database, and seee if the account as admin privs
//if it does, set the session variable '$admin' to true. This will give the user access
//to more items.
$_SESSION["admin"] = false;
//Connect to the database
$yourURL = $domain . $phpSelf;
//Initalize all the variables
$username = '';
$password = '';

//This will hold the entry errors
$usernameERROR = false;
$passwordERROR = false;
$errorMsg = array();
//Once the button has been pressed
if (isset($_POST["btnLogin"])) {
    
    
//Get the input from the forms, and sanitize them
    $username = htmlentities($_POST["txtUsername"], ENT_QUOTES, "UTF-8");
    $password = htmlentities($_POST["txtPassword"], ENT_QUOTES, "UTF-8");

//This section passes the sanitized data through validation functions to 
//ensure that there is something in both of the fields
    if ($username == "") {
        $errorMsg[] = "Please enter your username.";
        $usernameERROR = true;
    }
    if ($password == "") {
        $errorMsg[] = "Please enter your password.";
        $passwordERROR = true;
    }
    //Section get the password and admin status from the database, for
    //the user name that was taken from the form.
    try {
        if (empty($errorMsg)) {
            $data = array();
            $data[] = $username;
            $thisDatabase->db->beginTransaction();
            //Get the users password and admin status
            $query = "Select fldPassword, fldAdmin from tblAdmin where fldUsername = ?";
            
            $results = $thisDatabase->select($query, $data);
            //Remove the usersPassword and Admin status from the results
            $userPassword = $results[0]["fldPassword"];
            $userAdmin = $results[0]["fldAdmin"];
            $dataEntered = $thisDatabase->db->commit();

            
            //If the passwords match, set the admin status for the page to true
            if ($password == $userPassword) {
                if ($userAdmin == 1) { //If the admin status is 1 (yes)
                    $_SESSION["admin"] = true;
                } else {
                    $errorMsg[] = "You are not authorized to continue.";
                }
            } else {
                $errorMsg[] = "The password you entered was incorrect. Please try again.";
            }
        }
            } catch (PDOExecption $e) {
        $thisDatabase->db->rollback();
        if ($debug)
            print "Error!: " . $e->getMessage() . "</br>";
        $errorMsg[] = "There was a problem with your entry.";
    }
} //If the button is pressed
if (isset($_POST["btnLogin"]) AND empty($errorMsg)) { // closing of if marked with: end body submit
    if ($dataEntered) {
    header('Location: index.php');
    } //Close if dataEntered
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
//#############################################################################
//
// SECTION 3 Display Form
//
    ?>
    <form method="POST"  method="post" id="frmLogin">
            <section class="username">
                <label for="txtUsername">Username: </label>
                <input type="text" id="txtUsername" name="txtUsername"
                       value=""
                       tabindex="100" maxlength="45" placeholder="Enter your username"
                       <?php if ($usernameERROR) print 'class="mistake"'; ?>autofocus/>
            </section>
            <section class="password">
                <label for="txtPassword">Password: </label>
                <input type="text" id="txtPassword" name="txtPassword"
                       value=""
                       tabindex="100" maxlength="45" placeholder="Enter your password"
                       <?php if ($usernameERROR) print 'class="mistake"'; ?>autofocus/>
            </section>
            <input type="submit" id="btnLogin" name="btnLogin" value="Login" tabindex="900" class="button">


    </form>

    <?php
}
print"</aside>";
}
?>

</body>
</html>