<?php require("head.php");

if ($_SESSION["admin"]) {
    $_SESSION['adminID'];
    $adminID = array();
    require_once('../bin/myDatabase.php');

    $dbUserName = get_current_user() . '_reader';
    $whichPass = "r";
    $dbName = strtoupper(get_current_user()) . '_BOTTOMS_UP';
    
    $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
    
    ?>

<?php
    if (isset($_POST["btnDelete"])) {   
        $deleteAdmin = htmlentities(($_POST["deleteAdmin"]), ENT_QUOTES, "UTF-8");
        $adminID = $_SESSION["adminID"];

        try {
            
            $thisDatabase->db->beginTransaction();
            
        
            $query = "DELETE FROM tblAdmin WHERE pmkAdminID in (?)";
            $deleteArray = array($adminID);
            $results = $thisDatabase->update($query, $deleteArray);
            $dataEntered = $thisDatabase->db->commit();
           
            header('Location: adminAdmin.php');
        } catch (PDOExecption $e) {
            $thisDatabase->db->rollback();
            print "An error occurred.";
        }
        print("does this work?");
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
    foreach ($results as $row) {
        if ($firstTime) {
            print "<thead>";

            print "<tr><th>Username</th>";
            print "<th>Password</th>";
            print "<th>Admin?</th>";
            print "<th>Delete</th>";
            print "<th>Change</th></tr>";
            $firstTime = false;
        }
        
        print "<tr>";
        $k = 0;
        foreach ($row as $field => $value) {
            if (!is_int($field)) {
                if ($k==3){
                    echo"<td><input type='radio' name='deleteAdmin' value=$value></td>";
                    echo"<td><input type='radio' name='updateAdmin' value=$value></td>";
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
    </article>
</body>
