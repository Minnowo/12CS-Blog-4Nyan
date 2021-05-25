<?php

session_start();

$_SESSION['CURRENT_PAGE'] = "LOGIN_PAGE";
$_SESSION['LOGGED_IN'] = false;
$_SESSION['IS_ADMIN'] = false;

require("Functions.php");

if (isset($_POST['submitButton']))
{
    $email = $_POST['inputEmail'];
    $password = $_POST['inputPassword'];

    if (empty($email) || empty($password))
    {
        $msg = "please fill all the fields";
    }
    else
    {
        $connection = DB_Connect();

        $stmt = $connection->prepare("SELECT * FROM `users` WHERE `users`.`email` = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        if (count($result) > 0)
        {
            foreach ($result as $row)
            {
                if (password_verify($password, $row['password']))
                {
                    $msg = "success";

                    $_SESSION['BLOG_USERS'] = "19284791278172048172401824918204981340";
                    $_SESSION['LOGGED_IN'] = true;
                    $_SESSION['IS_ADMIN'] = $row['permissions'] == 2;

                    $_SESSION['userid'] = $row['userId'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['email'] = $email;
                    $_SESSION['permissions'] = $row['permissions'];

                    header("location: Index.php");
                }
                else
                {
                    $msg = "username or password is invalid";
                }
            }
        }
        else
        {
            $msg = "email or password is invalid";
        }
    }
}
else
{
    $msg = "";
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
        <h4 class="card-header "> Login</h4>
        <p style="color:red"><strong><?php echo $msg; ?></strong></p>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <table class="table table-striped">
                <tr>
                    <td>Email</td>
                    <td><input style='width:100%' type='email' name="inputEmail" size=50 maxlength="55"></td>
                </tr>
                <tr>
                    <td>Password</td>
                    <td><input style='width:100%' type='password' name="inputPassword" size=50 maxlength="255"></td>
                </tr>
            </table>
            <input type='submit' name='submitButton' value='Login'>
        </form>
    </div>
</body>

</html>