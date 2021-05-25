<?php

session_start();

require("Functions.php");

$_SESSION['CURRENT_PAGE'] = "EDIT_CATEGORIES";
$connection = DB_Connect();

if (isset($_POST['deletePost']))
{
    $HIDE_ON_DELETE = false;

    RunQuery($connection, "UPDATE `articles` SET `publisher` = '-1' WHERE `articles`.`publisher` = '" . $_POST['userID'] . "'");
    RunQuery($connection, "UPDATE `comments` SET `publisher` = '-1' WHERE `comments`.`publisher` = '" . $_POST['userID'] . "'");
    DeleteUser($_POST['userID'], $connection);

    $HIDE_ON_DELETE = true;
    header("location: admin/index.php?show=4");
    return;
}

if (isset($_POST['saveChanges']))
{
    UpdateUser($_POST['userID'], $_POST['usernameText'], $_POST['emailText'], $_POST['selectPermissions'], $connection);

    header("location: " . $_SERVER['PHP_SELF'] . "?id=" . $_POST['userID']);
    return;
}

if (!isset($_GET['id']))
{
    header("location: index.php?show=4");
    return;
}

$toEdit = trim($_GET['id']);

if (!is_numeric($toEdit))
{
    header("location: index.php?show=4");
    return;
}

$selectToEditQuery = RunQuery(
    $connection,
    "SELECT * FROM `users` WHERE `users`.`userId` = '$toEdit'"
);

if (mysqli_num_rows($selectToEditQuery) < 1)
{
    header("location: index.php?show=4");
    return;
}

$row = mysqli_fetch_array($selectToEditQuery);
$permissions = RunQuery($connection, "SELECT * FROM `permissions`");

?>


<!DOCTYPE html>
<html>

<header>
    <script>
        <?php include("script.js"); ?>
    </script>
    <?php require('head.php'); ?>
    <?php require('NavBar.php') ?>

    <script src="tinymce\js\tinymce\tinymce.min.js"></script>

    <script type="text/javascript">
        function initMCEexact(e) {
            tinyMCE.init({
                // https://www.tiny.cloud/docs/advanced/available-toolbar-buttons/
                mode: "exact",
                menubar: false,
                plugins: 'autolink',
                toolbar: " undo redo | bold italic strikethrough underline | subscript superscript | link unlink | removeformat | formatselect fontselect fontsizeselect",
                elements: e,

            });
        }
    </script>
</header>

<body style='background-color: #EEF2FF' class='postTimestamp'>
    <div class='container'>
        <form method='post' action='<?php echo $_SERVER['PHP_SELF']; ?>'>
            <h4 class="card-header "><?php if (isset($row['username'])) echo "Editing User: \"" . $row['username'] . "\""; ?></h4>
            <table class="table table-striped" border='1px'>
                <?php

                $permissionHTML = "<select name = 'selectPermissions'>";
                while ($perm = mysqli_fetch_array($permissions))
                {
                    if ($perm['permissionId'] == $row['permissions'])
                    {
                        $permissionHTML = $permissionHTML . "<option value='" . $perm['permissionId'] . "' selected>" . $perm['permissionName'] . "</option>";
                        continue;
                    }
                    $permissionHTML = $permissionHTML . "<option value='" . $perm['permissionId'] . "'>" . $perm['permissionName'] . "</option>";
                }
                $permissionHTML = $permissionHTML . "</select>";

                echo AsTableRow(AsTableColumn(AsBoldText("Username")) . AsTableColumn("<input type='text' name='usernameText' maxlength='55' placeholder='Username' style='width:100%;' value='" . $row['username'] . "'>"));
                echo AsTableRow(AsTableColumn(AsBoldText("Email")) . AsTableColumn("<input type='text' name='emailText' placeholder='Email' style='width:100%;' value='" . $row['email'] . "'"));
                echo AsTableRow(AsTableColumn(AsBoldText("Permission")) . AsTableColumn($permissionHTML));

                ?>
            </table>
            <input type='hidden' name='userID' value='<?php echo $toEdit; ?>'>
            <input class='float-start' type='submit' name='deletePost' value='Delete User'>
            <input class='float-end' type='submit' name='saveChanges' value='Update User'>
        </form>
    </div>
</body>

</html>