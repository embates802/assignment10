<?php require("head.php");?>
<body>
    <h1>What are you drinking tonight?</h1>
<?php
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
    $alcoholicBeverage = "";
    $nonalcoholicBeverage = "";
    
    //%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
    //
    // SECTION: 1d form error flags
    //
    // Initialize Error Flags one for each form element we validate
    // in the order they appear in section 1c.
    $alcoholicBeverageERROR = false;
    $nonalcoholicBeverageERROR = false;
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
    // SECTION: 2a Security
    // 
    if (!securityCheck(true)) {
        $msg = "<p>Sorry you cannot access this page. ";
        $msg.= "Security breach detected and reported</p>";
        die($msg);
    }
    
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2b Sanitize (clean) data 
    // remove any potential JavaScript or html code from users input on the
    // form. Note it is best to follow the same order as declared in section 1c.
    $alcoholicBeverage = htmlentities($_POST["txtDepartment"], ENT_QUOTES, "UTF-8");
    $nonalcoholicBeverage = htmlentities($_POST["txtCourseNumber"], ENT_QUOTES, "UTF-8");
 
     //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2c Validation
    //
    // Don't need validation for this form - only dropdown lists.
    
 
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2d Process Form - Passed Validation
    //
    // Process for when the form passes validation (the errorMsg array is empty)
    //
    if (!$errorMsg) {
        if ($debug)
            print "<p>Form is valid</p>";  
    } // end form is valid
    
} // ends if form was submitted.
   
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
        
        $query ="
                Select 
                    fldCocktailName AS Cocktail Name, 
                    fldRecipe as Recipe, 
                    fldSpecialInstructions as Notes,
                    fldPhotoName as Photo Path";
        
        $query .="
                FROM
                    tblCocktails";
        
       $query .=" WHERE pmkABeverageID = fnkABeverageID";
       $query .=" AND pmkNBeverageID = fnkNBeverageID";
       
       $data = array($alcoholicBeverage, $nonalcoholicBeverage);
                
                
        $results = $thisDatabase->select($query, $data);
     
        /* ##### Step four
     * prepare output and loop through array
     *      */
?>
    <script>
        function resetForm() {
            window.location.href ="https://elbates.w3.uvm.edu/cs148/assignment10/index.php";
        }
    </script>
    <aside class="resetButton">
      <button  id ="btnReset" onclick="resetForm();">Start Over</button>  
    </aside>
    <aside id="backToTop">
        <a href="#form">Top</a>
    </aside>
    
            <?php
            
   if( empty( $results ) )
    {
     print ("Whoops! We couldn't find any cocktails with those ingredients. Why don't you try again?");
    }
    
    print "<table>";
    $firstTime = true;
    /* since it is associative array display the field names */
    foreach ($results as $row) {
        if ($firstTime) {
            print "<thead><tr>";
            $keys = array_keys($row);
            foreach ($keys as $key) {
                if (!is_int($key)) {
                    print "<th>" . $key . "</th>";
                }
            }
            print "</tr>";
            $firstTime = false;
        }
        
        /* display the data, the array is both associative and index so we are
         *  skipping the index otherwise records are doubled up */
        print "<tr>";
        foreach ($row as $field => $value) {
            if (!is_int($field)) {
                print "<td>" . $value . "</td>";
            }
        }
        print "</tr>";
    }
    print "</table>";
    
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
        
         <form action="<?php print $phpSelf ;?>" method="post" id="frmRegister">

            <fieldset class="wrapper">

                <fieldset class="wrapperTwo">
                <section class="fldInput">
                        <?php
                         $alcoholicSearch = "SELECT DISTINCT fldABeverageName FROM tblAlcoholicBeverages ORDER BY fldABeverageName ASC";
                         $alcoholicList = $thisDatabase->select($alcoholicSearch);
                                           
                         print "<label for=\"lstAlcoholicBeverages\">Choose a Spirit: </label>
                            <section class=\"inputWrapper\">
                            <select id=\"lstAlcoholicBeverages\"
                                    name=\"lstAlcoholicBeverages\"
                                    tabindex=\"300\" >";
                         
//                         print "<option value=\"%\"> All Buildings</option>";
//                         for ($row = 1; $row < count($buildingList); $row++) {
//                              for ($col = 0; $col < 1; $col++) {
//                                echo "<option value=\"".$buildingList[$row][$col]."\">".$buildingList[$row][$col]."</option>\n";
//                              }
//                              
//                        }
                            
                            print "</select>\n ";
                          print"</section>\n";
                                                
                         ?>
                       </section>
                    
                    
                <section class="fldInput">
                        <?php
                         $nonalcoholicSearch = "SELECT DISTINCT fldNBeverageName FROM tblNonalcoholicBeverages ORDER BY fldNBeverageName ASC";
                         $nonalcoholicList = $thisDatabase->select($nonalcoholicSearch);
                                           
                         print "<label for=\"lstNonalcoholicBeverages\">Choose a Mixer: </label>
                            <section class=\"inputWrapper\">
                            <select id=\"lstNonalcoholicBeverages\"
                                    name=\"lstNonalcoholicBeverages\"
                                    tabindex=\"300\" >";
                         
//                         print "<option value=\"%\"> All Buildings</option>";
//                         for ($row = 1; $row < count($buildingList); $row++) {
//                              for ($col = 0; $col < 1; $col++) {
//                                echo "<option value=\"".$buildingList[$row][$col]."\">".$buildingList[$row][$col]."</option>\n";
//                              }
//                              
//                        }
                            
                            print "</select>\n ";
                          print"</section>\n";
                                                
                         ?>
                       </section>
                    </fieldset> <!-- ends wrapper Two -->
                
                <fieldset class="buttons">
                    <input type="submit" id="btnSubmit" name="btnSubmit" value="Search" tabindex="10000" class="button">
                </fieldset> <!-- ends buttons -->
                
            </fieldset> <!-- Ends Wrapper -->
        </form>
    <?php
    } // end body submit
    ?>

</article>
</body>
</html>

