<?php require("head.php");

//if ($_SESSION["admin"]) {
//    $_SESSION['adminID']; //This array will hold all of the members Id
//    $_SESSION['updateAdmin']; //Hold the Id of the member to be updated
//    
//    //Get the array of all the current members
//            $adminID = array();
//    if (isset($_POST["btnDelete"])) {

$debug = false;
error_reporting(E_All);
if (isset($_GET["debug"])) { // ONLY do this in a classroom environment
    $debug = true;
}

if ($debug)
    print "<p>DEBUG MODE IS ON</p>";
    require_once('../bin/myDatabase.php');

    $dbUserName = get_current_user() . '_reader';
    $whichPass = "r"; //flag for which one to use.
    $dbName = strtoupper(get_current_user()) . '_BOTTOMS_UP';
    
    $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
    
    ?>

    <article id="main">
    <?php
    $query ="SELECT fldUsername, fldPassword, fldAdmin, pmkAdminID FROM tblAdmin ORDER BY fldUsername";
    $results = $thisDatabase->select($query, $data);
    if( empty( $results ) )
    {
     print"<h2 class=\"noResults\">There are no results for that search, try again.</h2>";
    }       
    print "<p><table class='center'>";
    $firstTime = true;
    /* since it is associative array display the field names */
    foreach ($results as $row) {
        if ($firstTime) {
            print "<thead>";
            $keys = array_keys($row);
            foreach ($keys as $key) {
                if (!is_int($key)) {
                    print "<th>" . $key . "</th>";
                }
            }
            print "<th>Update</th>";
            print "<th>Delete</th>";
            $firstTime = false;
        }
        
        /* display the data, the array is both associative and index so we are
         *  skipping the index otherwise records are doubled up */
        print "<tr>";
        $k = 0;
        foreach ($row as $field => $value) {
            if (!is_int($field)) {
                if ($k==2){
                    echo"<td><input type='radio' name='updateUser' value=$value</td>";
                    echo"<td><input type='radio' name='deleteUser' value=$value</td>";
                    $k=0;
                }
                print "<td>" . $value . "</td>";
                $k++;
            }
        }
        print "</tr>";
    }
    print "</table>";

    ?>
        <p><a href="adminAddAdmin.php">Add New Admin</a>
</body>
