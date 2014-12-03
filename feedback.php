<?php require("head.php");?>
    <h2>We Love Feedback!</h2>
    <?php
    include("../bin/validation-functions.php");
 
    error_reporting(E_All);
    require_once('../bin/myDatabase.php');
 
    $dbUserName = get_current_user() . '_admin';
    $whichPass = "a"; //flag for which one to use.
    $dbName = strtoupper(get_current_user()) . '_BOTTOMS_UP';
    
    $thisDatabase = new myDatabase($dbUserName, $whichPass, $dbName);
    
    $yourURL = $domain . $phpSelf;

  
    $feedback = "";
    $rating = 0;
    
    $feedbackERROR = false;
    $ratingERROR = false;
    
    $errorMsg = array();
    
    if (isset($_POST["btnSubmit"])) {

        $feedback = filter_var($_POST["txtaFeedback"], FILTER_SANITIZE_STRING);
        $rating = htmlentities($_POST["radRating"], ENT_QUOTES, "UTF-8");
        


        if ($feedback == "") {
            $errorMsg[] = "Please enter your feedback below.";
            $feedbackERROR = true;
        }
 
    if (!$errorMsg) {
        $primaryKey = "";
        $dataEntered = false;
        try {
            $thisDatabase->db->beginTransaction();
            $query = "INSERT INTO tblFeedback (fldFeedback, fldRating) values (?, ?)";
            $data = array($feedback, $rating);
 
            $results = $thisDatabase->insert($query, $data);
            $primaryKey = $thisDatabase->lastInsert();
 
            $dataEntered = $thisDatabase->db->commit();
            $dataEntered = true;

        } catch (PDOExecption $e) {
            $thisDatabase->db->rollback();

            $errorMsg[] = "There was a problem with accepting your data; please contact us directly.";
        }

        if ($dataEntered) {
            print("Thank you for your feedback.");
        }
    }
}

?>
<article id="main">
    <?php

    if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) { // closing of if marked with: end body submit

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
        <form method="post"
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
    }
    ?>
</article>
    </html>

    
