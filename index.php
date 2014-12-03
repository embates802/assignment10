<?php require("head.php");?>
    <h1>What are you drinking tonight?</h1>
<?php
        
    $debug = false;
    error_reporting(E_All);

    require_once('../bin/myDatabase.php');
    $dbUserName = get_current_user() . '_admin';
    $whichPass = "a";
    $dbName = strtoupper(get_current_user()) . '_BOTTOMS_UP';
    
    $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
    
    $yourURL = $domain . $phpSelf;
    $alcoholicBeverage = "";
    $nonalcoholicBeverage = "";

    $alcoholicBeverageERROR = false;
    $nonalcoholicBeverageERROR = false;

    $errorMsg = array();
    if (isset($_POST["btnSubmit"])) {

    $alcoholicBeverage = htmlentities($_POST["lstAlcoholicBeverages"], ENT_QUOTES, "UTF-8");
    $nonalcoholicBeverage = htmlentities($_POST["lstNonalcoholicBeverages"], ENT_QUOTES, "UTF-8");
    
    if (!$errorMsg) {
        if ($debug)
            print "<p>Form is valid</p>";  
    }
    
}
?>

<article id="main">

    <?php
    if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) {
        
        $query ="
                SELECT
                    tblCocktails.fldCocktailName, 
                    tblCocktails.fldRecipe, 
                    tblCocktails.fldSpecialInstructions,
                    tblCocktails.fldPhotoName";
        
        $query .="
                FROM
                    tblCocktails,
                    tblAlcoholicBeverages,
                    tblNonalcoholicBeverages";
        
        $query .=" WHERE tblAlcoholicBeverages.pmkABeverageID = tblCocktails.fnkABeverageID";
        $query .=" AND tblNonalcoholicBeverages.pmkNBeverageID = tblCocktails.fnkNBeverageID";
        $query .=" AND tblAlcoholicBeverages.fldABeverageName like ?";
        $query .=" AND tblNonalcoholicBeverages.fldNBeverageName like ?";        


       
       $data = array($alcoholicBeverage, $nonalcoholicBeverage);
                
                
        $results = $thisDatabase->select($query, $data);

?>
    <script>
//        function resetForm() {
//            window.location.href ="https://elbates.w3.uvm.edu/cs148/assignment10/index.php";
//        }
    </script>
    <aside class="resetButton">
      <button id ="btnReset" onclick="resetForm();">Start Over</button>  
    </aside>
    
            <?php
            
   if( empty( $results ) )
    {
     print ("<p>Whoops! We couldn't find any cocktails with those ingredients. Why don't you try again?");?>
    <figure>
    <img class="centered" src="photos/empty.jpg" alt="Empty Drinking Glass" title="Empty Glass"/>
    </figure>
    <?php
    }?>
    <?php
    $cocktailName = $results[0]["fldCocktailName"];
    $recipe = $results[0]["fldRecipe"];
    $specialInstructions = $results[0]["fldSpecialInstructions"];
    $photoName = $results[0]["fldPhotoName"];
    ?>
   <?php
    if ($cocktailName != "")
    {
        print("<h2>You should make: </h2>");
        print("<h3>".$cocktailName."</h3>");
        print('<img class = "centered" src="' . $photoName . '"/>');
        print('<br>Recipe: <br>' . $recipe);
            if ($specialInstructions != "none")
            {
                print('<p>'.$specialInstructions);
            }   
        
    }
  

 
    $firstTime = true;
   
    
     } else {
        if ($errorMsg) {
            print '<div id="errors">';
            print "<ol>\n";
            foreach ($errorMsg as $err) {
                print "<li>" . $err . "</li>\n";
            }
            print "</ol>\n";
            print '</div>';
        }
        
        ?>
        
         <form method="post" id="frmRegister">

                <section class="fldInput">
                        <?php
                         $alcoholicSearch = "SELECT DISTINCT fldABeverageName FROM tblAlcoholicBeverages ORDER BY fldABeverageName ASC";
                         $alcoholicList = $thisDatabase->select($alcoholicSearch);
                                           
                         print "<label for=\"lstAlcoholicBeverages\">Choose a Spirit: </label>
                            <section class=\"inputWrapper\">
                            <select id=\"lstAlcoholicBeverages\"
                                    name=\"lstAlcoholicBeverages\"
                                    tabindex=\"300\" >";
                         
                         for ($row = 0; $row < count($alcoholicList); $row++) {
                              for ($col = 0; $col < 1; $col++) {
                                echo "<option value=\"".$alcoholicList[$row][$col]."\">".$alcoholicList[$row][$col]."</option>\n";
                              }
                              
                        }
                            
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
                         
                         for ($row = 0; $row < count($nonalcoholicList); $row++) {
                              for ($col = 0; $col < 1; $col++) {
                                echo "<option value=\"".$nonalcoholicList[$row][$col]."\">".$nonalcoholicList[$row][$col]."</option>\n";
                              }
                              
                       }
                            
                            print "</select>\n ";
                          print"</section>\n";
                                                
                         ?>
                       </section>
                
             <input type="submit" id="btnSubmit" name="btnSubmit" value="Bottoms Up!" tabindex="10000" class="button">

        </form>
    <?php
    }
    ?>
</article>
</html>

