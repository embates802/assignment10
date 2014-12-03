<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="author" content="Emily Bates">
        <meta name="description" content="Bottoms Up!">
        <title>Bottoms Up!</title>
        <link href="style.css" type="text/css" rel="stylesheet" />
    </head>
    <nav>
            <?php session_start();?>
            <a href="index.php">HOME</a>
            <?php print("  |  ") ?>
            <a href="about.php">ABOUT</a>
            <?php print("  |  ") ?>
            <a href="signup.php">SIGN UP</a>
            <?php print("  |  ") ?>
            <a href="feedback.php">FEEDBACK</a>
            <?php print("  |  ") ?>
            <?php if (!$_SESSION["admin"])
            {
                print('<a href="login.php">ADMIN LOG IN</a>');
            }?>
            <?php if($_SESSION["admin"])
            {
                if (basename($_SERVER['PHP_SELF']) == "login.php")
                {
                    print('<a href="login.php">ADMIN LOG IN</a>');
                }
                else
                {
                    print('<a href="login.php">ADMIN LOG OUT</a>');
                }
                print("  |  ");
                print('<a href="admin.php">MODIFY DATABASE</a>');
            }
            ?>
            
    </nav>