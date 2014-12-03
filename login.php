<?php require("head.php");?>

<?php
if($_SESSION["admin"]){
    $_SESSION["admin"] = false;

    header('Location: index.php');
}
    else{

        require_once('../bin/myDatabase.php');
    $dbUserName = get_current_user() . '_admin';
    $whichPass = "a";
    $dbName = strtoupper(get_current_user()) . '_BOTTOMS_UP';
    
    $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);

$_SESSION["admin"] = false;
$yourURL = $domain . $phpSelf;
$username = '';
$password = '';

$usernameERROR = false;
$passwordERROR = false;
$errorMsg = array();

if (isset($_POST["btnLogin"])) {
    

    $username = htmlentities($_POST["txtUsername"], ENT_QUOTES, "UTF-8");
    $password = htmlentities($_POST["txtPassword"], ENT_QUOTES, "UTF-8");


    if ($username == "") {
        $errorMsg[] = "Please enter your username.";
        $usernameERROR = true;
    }
    if ($password == "") {
        $errorMsg[] = "Please enter your password.";
        $passwordERROR = true;
    }

    try {
        if (empty($errorMsg)) {
            $data = array();
            $data[] = $username;
            $thisDatabase->db->beginTransaction();
            $query = "Select fldPassword, fldAdmin from tblAdmin where fldUsername = ?";
            
            $results = $thisDatabase->select($query, $data);
            $userPassword = $results[0]["fldPassword"];
            $userAdmin = $results[0]["fldAdmin"];
            $dataEntered = $thisDatabase->db->commit();


            if ($password == $userPassword) {
                if ($userAdmin == 1) {
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
}
if (isset($_POST["btnLogin"]) AND empty($errorMsg)) {
    if ($dataEntered) {
    header('Location: index.php');
    } 
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
    <form method="POST" id="frmLogin">
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

}
?>

</body>
</html>