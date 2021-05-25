<?php

session_start();

require("Functions.php");

$_SESSION['CURRENT_PAGE'] = "EDIT_POST";
$connection = DB_Connect();

if (isset($_POST['deletePost']))
{
    $HIDE_ON_DELETE = false;

    if ($_POST['postID'][0] == "T")
    {
        DeleteArticle(substr($_POST['postID'], 1, strlen($_POST['postID']) - 1), $connection);
        header("location: admin/index.php?show=1");
    }
    else
    {
        Deletecomment(substr($_POST['postID'], 1, strlen($_POST['postID']) - 1), $connection);
        header("location: admin/index.php?show=2");
    }
    $HIDE_ON_DELETE = true;
    return;
}

if (isset($_POST['saveChanges']))
{
    if ($_POST['postID'][0] == "T")
    {
        $isImage = isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']["error"] != 4; // 4 == no file uploaded 
        $imgId = $_POST['imageId'];

        if (empty($imgId) || $imgId == "")
            $imgId = NULL;

        if ($isImage)
        {
            $imgInfo = CreateUploadedImage($_FILES["fileToUpload"]["name"], $_FILES["fileToUpload"]["tmp_name"], $_FILES["fileToUpload"]["size"]);

            if (!$imgInfo['errorCreating'])
                $imgId = InsertImage($imgInfo['path'], $imgInfo['hash'], $connection);
        }

        $txt = $_POST['postBodyText'];

        // if they're admin add text to say they are admin
        if ($_SESSION['permissions'] == ADMIN_PERMISSION_VALUE || $_SESSION['permissions'] == MODERATOR_PERMISSION_VALUE)
            $txt = $txt . "<br><span style='color:red;'>This Post Has Been Edited By An Administrator</span>";

        UpdateArticle(substr($_POST['postID'], 1, strlen($_POST['postID']) - 1), $_POST['threadTitleText'], $txt, $imgId, $_POST['selectCategory'], $_POST['selectVisibility'], $connection);
    }

    if ($_POST['postID'][0] == "C")
    {
        $isImage = isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']["error"] != 4; // 4 == no file uploaded 
        $imgId = $_POST['imageId'];

        if (empty($imgId) || $imgId == "")
            $imgId = NULL;

        if ($isImage)
        {
            $imgInfo = CreateUploadedImage($_FILES["fileToUpload"]["name"], $_FILES["fileToUpload"]["tmp_name"], $_FILES["fileToUpload"]["size"]);

            if (!$imgInfo['errorCreating'])
                $imgId = InsertImage($imgInfo['path'], $imgInfo['hash'], $connection);
        }

        $txt = $_POST['commentText'];

        // if they're admin add text to say they are admin
        if ($_SESSION['permissions'] == ADMIN_PERMISSION_VALUE || $_SESSION['permissions'] == MODERATOR_PERMISSION_VALUE)
            $txt = $txt . "<br><span style='color:red;'>This Post Has Been Edited By An Administrator</span>";

        UpdateComment(substr($_POST['postID'], 1, strlen($_POST['postID']) - 1), $txt, $imgId, $_POST['selectVisibility'], $connection);
    }
    header("location: " . $_SERVER['PHP_SELF'] . "?post=" . $_POST['postID']);
    return;
}

if (!isset($_GET['post']) || !$_SESSION['LOGGED_IN'])
{
    header("location: index.php");
    return;
}

$toEdit = strtoupper(trim($_GET['post']));

if ($toEdit[0] != "C" && $toEdit[0] != "T")
{
    header("location: index.php");
    return;
}

$isThread = $toEdit[0] == "T";
$id = substr($toEdit, 1, strlen($toEdit) - 1);

if (!is_numeric($id) || !VerifyPostUserOwnsPostOrAdmin($_SESSION['permissions'], $id, $_SESSION['userid'], $isThread))
{
    header("location: index.php");
    return;
}

if ($isThread)
{
    $selectToEditQuery =  RunQuery(
        $connection,
        "SELECT `articles`.`visible`, `articles`.`articleId`, `articles`.`title`, `users`.`userId`,`users`.`username`, `articles`.`dateOfPublish`, `articles`.`publishedText`, `categories`.`categoryName`, `categories`.`categoryId`, `images`.`imageId`, `images`.`imagePath` 
    FROM `articles` 
    left JOIN `users` ON `articles`.`publisher` = `users`.`userId` 
    INNER JOIN `categories` ON `articles`.`category` = `categories`.`categoryId` 
    left JOIN `images` ON `articles`.`image` = `images`.`imageId` 
    WHERE `articles`.`articleId` = '$id'"
    );
}
elseif ($toEdit[0] == "C")
{
    $selectToEditQuery = RunQuery(
        $connection,
        "SELECT `comments`.`visible`, `comments`.`commentId`, `users`.`username`, `comments`.`publisher`, `comments`.`comment`, `comments`.`publishDate`, `images`.`imageId`, `images`.`imagePath` 
    FROM `comments`
    INNER JOIN `users` ON `comments`.`publisher` = `users`.`userId`
    left JOIN `images` ON `comments`.`image` = `images`.`imageId` 
    WHERE `comments`.`commentId` = '$id'"
    );
}
else
{
    header("location: index.php");
    return;
}

if (mysqli_num_rows($selectToEditQuery) < 1)
{
    header("location: index.php");
    return;
}

$row = mysqli_fetch_array($selectToEditQuery);
$categories = RunQuery($connection, "SELECT * FROM `categories`");
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
        <form method='post' action='<?php echo $_SERVER['PHP_SELF']; ?>' enctype="multipart/form-data">
            <h4 class="card-header "><?php if ($isThread) echo "Editing Post " . AsBoldText("\"") . $row['title'] . AsBoldText("\"") . " by " . AsBoldText($row['username']);
                                        else echo "Editing Comment by " . AsBoldText("\"") . $row['username'] . AsBoldText("\""); ?></h4>
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

                if ($isThread)
                {
                    echo AsTableRow(AsTableColumn(AsBoldText("Title")) . AsTableColumn("<input type='text' name='threadTitleText' maxlength='55' placeholder='Title' style='width:100%;' value='" . $row['title'] . "'>"));
                    echo AsTableRow(AsTableColumn(AsBoldText("Text")) . AsTableColumn("<textarea id='articleBodyTextID' type='text' name='postBodyText' cols='45' rows='6' maxlength='2000'>" . CreateMsgLinks($row['publishedText']) . "</textarea><script> initMCEexact('articleBodyTextID'); </script>"));

                    $categoriesHTML = "<select name = 'selectCategory'>";
                    while ($cat = mysqli_fetch_array($categories))
                    {
                        if ($cat['categoryId'] == $row['categoryId'])
                        {
                            $categoriesHTML = $categoriesHTML . "<option value='" . $cat['categoryId'] . "' selected>" . $cat['categoryName'] . "</option>";
                            continue;
                        }
                        $categoriesHTML = $categoriesHTML . "<option value='" . $cat['categoryId'] . "'>" . $cat['categoryName'] . "</option>";
                    }
                    $categoriesHTML = $categoriesHTML . "</select>";


                    echo AsTableRow(AsTableColumn(AsBoldText("ImageID: " . ($row['imageId'] ?? "NULL"))) . AsTableColumn("
                    <img class='float-start image' loading='lazy' src='" . $row['imagePath'] . "' alt='Failed To load image'>
                    <input class='float-end' type='file' name='fileToUpload' id='fileToUploadId'>"));

                    echo AsTableRow(AsTableColumn(AsBoldText("Category")) . AsTableColumn($categoriesHTML));

                    echo AsTableRow(AsTableColumn(AsBoldText("Visible")) . AsTableColumn($setVisibleHTML));
                    echo AsTableRow(AsTableColumn(AsBoldText("Date Published")) . AsTableColumn($row["dateOfPublish"]));
                    echo "<input type='hidden' name='imageId' value='" . $row['imageId'] . "'>";
                }
                else
                {
                    echo AsTableRow(AsTableColumn(AsBoldText("Text")) . AsTableColumn("<textarea id='articleBodyTextID' type='text' name='commentText' cols='45' rows='6' maxlength='2000'>" . CreateMsgLinks($row['comment']) . "</textarea><script> initMCEexact('articleBodyTextID'); </script>"));
                    echo AsTableRow(AsTableColumn(AsBoldText("ImageID: " . ($row['imageId'] ?? "NULL"))) . AsTableColumn("
                    <img class='float-start image' loading='lazy' src='" . $row['imagePath'] . "' alt='Failed To load image'>
                    <input class='float-end' type='file' name='fileToUpload' id='fileToUploadId'>"));
                    echo AsTableRow(AsTableColumn(AsBoldText("Visible")) . AsTableColumn($setVisibleHTML));
                    echo AsTableRow(AsTableColumn(AsBoldText("Date Published")) . AsTableColumn($row["publishDate"]));
                    echo "<input type='hidden' name='imageId' value='" . $row['imageId'] . "'>";
                }

                ?>
            </table>
            <input type='hidden' name='postID' value='<?php echo $toEdit; ?>'>
            <input class='float-start' type='submit' name='deletePost' value='Delete Post'>
            <input class='float-end' type='submit' name='saveChanges' value='Update Post'>
        </form>
    </div>
</body>

</html>