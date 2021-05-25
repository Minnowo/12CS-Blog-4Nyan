<?php

session_start();

require("Functions.php");

$_SESSION['CURRENT_PAGE'] = "EDIT_CATEGORIES";
$connection = DB_Connect();

if (isset($_POST['deleteUser']))
{
    $HIDE_ON_DELETE = false;

    DeleteCategory($_POST['categoryID'], $connection);

    $HIDE_ON_DELETE = true;
    header("location: admin/index.php?show=3");
    return;
}

if (isset($_POST['saveChanges']))
{
    UpdateCategory($_POST['categoryID'], $_POST['categoryNameText'], $_POST['categoryDescriptionText'], $_POST['selectVisibility'], $connection);

    header("location: " . $_SERVER['PHP_SELF'] . "?category=" . $_POST['categoryID']);
    return;
}

if (!isset($_GET['category']))
{
    header("location: index.php?show=3");
    return;
}

$toEdit = trim($_GET['category']);

if (!is_numeric($toEdit))
{
    header("location: index.php?show=3");
    return;
}

$selectToEditQuery = RunQuery(
    $connection,
    "SELECT * FROM `categories` WHERE `categories`.`categoryId` = '$toEdit'"
);

if (mysqli_num_rows($selectToEditQuery) < 1)
{
    header("location: index.php?show=3");
    return;
}

$row = mysqli_fetch_array($selectToEditQuery);
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
            <h4 class="card-header "><?php if (isset($row['categoryName'])) echo "Editing Category: \"" . $row['categoryName'] . "\""; ?></h4>
            <table class="table table-striped" border='1px'>
                <?php

                $setVisibleHTML = "<select name = 'selectVisibility'>";

                if ($row['visible'])
                {
                    $setVisibleHTML = $setVisibleHTML . "<option value='1' selected>True</option>";
                    $setVisibleHTML = $setVisibleHTML . "<option value='0' >False</option>";
                }
                else
                {
                    $setVisibleHTML = $setVisibleHTML . "<option value='1' >True</option>";
                    $setVisibleHTML = $setVisibleHTML . "<option value='0' selected>False</option>";
                }

                $setVisibleHTML = $setVisibleHTML . "</select>";

                echo AsTableRow(AsTableColumn(AsBoldText("Name")) . AsTableColumn("<input type='text' name='categoryNameText' maxlength='55' placeholder='Title' style='width:100%;' value='" . $row['categoryName'] . "'>"));
                echo AsTableRow(AsTableColumn(AsBoldText("Description")) . AsTableColumn("<textarea id='categoryDescriptionTextID' type='text' name='categoryDescriptionText' cols='45' rows='6' maxlength='2000'>" . $row['categoryDescription'] . "</textarea><script> initMCEexact('categoryDescriptionTextID'); </script>"));

                echo AsTableRow(AsTableColumn(AsBoldText("Visible")) . AsTableColumn($setVisibleHTML));

                ?>
            </table>
            <input type='hidden' name='categoryID' value='<?php echo $toEdit; ?>'>
            <input class='float-start' type='submit' name='deleteUser' value='Delete User'>
            <input class='float-end' type='submit' name='saveChanges' value='Update Category'>
        </form>
    </div>
</body>

</html>