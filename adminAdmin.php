<?php require("head.php");

if ($_SESSION["admin"]) {
    $_SESSION['adminID']; //This array will hold all of the members Id
    $adminID = array();
    require_once('../bin/myDatabase.php');

    $dbUserName = get_current_user() . '_reader';
    $whichPass = "r"; //flag for which one to use.
    $dbName = strtoupper(get_current_user()) . '_BOTTOMS_UP';
    
    $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
    
    ?>

<?php
//If it is trying to be updated
    if (isset($_POST["btnDelete"])) {
        //Get the ID of the member to be updated       
        $deleteAdmin = htmlentities(($_POST["deleteAdmin"]), ENT_QUOTES, "UTF-8");
        $adminID = $_SESSION["adminID"];

        try {
            //Delete from the members table
            $thisDatabase->db->beginTransaction();
            
        
            $query = "DELETE FROM tblAdmin WHERE pmkAdminID in (?)";
            
            $results = $thisDatabase->update($query, $deleteAdmin);
            $dataEntered = $thisDatabase->db->commit();
           
//Once the changes have been made, reload the page
            header('Location: adminAdmin.php');
        } catch (PDOExecption $e) {
            $thisDatabase->db->rollback();
            print "An error occurred.";
        }
    }

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
//            $keys = array_keys($row);
//            foreach ($keys as $key) {
//                if (!is_int($key)) {
//                    print "<th>" . $key . "</th>";
//                }
//            }
            print "<th>Username</th>";
            print "<th>Password</th>";
            print "<th>Admin?</th>";
            print "<th>Delete</th>";
            print "<th>Change</th>";
            $firstTime = false;
        }
        
        /* display the data, the array is both associative and index so we are
         *  skipping the index otherwise records are doubled up */
        print "<tr>";
        $k = 0;
        foreach ($row as $field => $value) {
            if (!is_int($field)) {
                if ($k==3){
                    echo"<td><input type='radio' name='deleteUser' value=$value</td>";
                    echo"<td><input type='radio' name='updateUser' value=$value</td>";
                    $k=0;
                }
                else{
                   print "<td>" . $value . "</td>"; 
                }
                
                $k++;
            }
        }
        print "</tr>";
    }
    print "</table>";
}
    ?>
        
 
        <input type="submit" id="btnDelete" name="btnDelete" value="Delete" tabindex="900" class="button">
        <input type="submit" id="btnUpdate" name="btnUpdate" value="Change" tabindex="1000" class="button">      

        
        </body>
