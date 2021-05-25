<?php
session_start();
require("Functions.php");

$_SESSION['CURRENT_PAGE'] = "INDEX";
$connection = DB_Connect();

$searchQuery = "";
$errorCreatingPost = false;

if (!isset($_SESSION['LOGGED_IN']) || !isset($_SESSION['BLOG_USERS']) || $_SESSION['BLOG_USERS'] != "19284791278172048172401824918204981340")
{
    $_SESSION['LOGGED_IN'] = false;
    $_SESSION['IS_ADMIN'] = false;
    $_SESSION['BLOG_USERS'] = "-1";

    $_SESSION['userid'] = ANONYMOUS_USER_ACCOUNT_ID;
    $_SESSION['username'] = "Anonymous";
    $_SESSION['email'] = "NULL";
    $_SESSION['permissions'] = USER_PERMISSION_VALUE; // included in functions.php at the top
}

$_SESSION['SUSPENDED'] = $_SESSION['permissions'] == SUSPENDED_PERMISSION_VALUE;

if (isset($_POST['postThreadButton']) && !$_SESSION['SUSPENDED'])
{
    // its cleaner to say there is an error until there isn't
    // than it is to say if works else error a whole bunch
    $errorCreatingPost = true;
    $isImage = isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']["error"] != 4; // 4 == no file uploaded 
    $imgInfo = array();

    // comment this if statement to remove need for capcha
    if (strtoupper($_POST['capchaInput']) == $_SESSION['capchaText'])
    {
        if (!empty($_POST['threadTitleText']) && !empty($_POST['threadBodyText']))
        {
            if ($isImage)
            {
                $imgInfo = CreateUploadedImage($_FILES["fileToUpload"]["name"], $_FILES["fileToUpload"]["tmp_name"], $_FILES["fileToUpload"]["size"]);
                $errorCreatingPost = $imgInfo['errorCreating'];
            }

            // if there is an image and there is no error
            // run the query, or if there is no image
            if ($isImage && !$errorCreatingPost || !$isImage)
                if (!isset($_SESSION['userid']))
                {
                    PostThread("-1", trim($_POST['threadTitleText']), trim($_POST['threadBodyText']), $_SESSION['CURRENT_CATEGORY']["id"], $imgInfo, $connection);
                    $errorCreatingPost = false;
                }
                else
                {
                    PostThread($_SESSION['userid'], trim($_POST['threadTitleText']), trim($_POST['threadBodyText']), $_SESSION['CURRENT_CATEGORY']["id"], $imgInfo, $connection);
                    $errorCreatingPost = false;
                }
        }
    }
}

if (isset($_POST['postCommentButton'])  && !$_SESSION['SUSPENDED'])
{
    $errorCreatingComment = false;
    $isImage = isset($_FILES['fileToUploadComment']) && $_FILES['fileToUploadComment']["error"] != 4; // 4 == no file uploaded 
    $imgInfo = array();

    // comment this if statement to remove need for capcha
    if (strtoupper($_POST['capchaInput']) == $_SESSION['capchaText'])

        if (!empty($_POST['commentBodyText']))
        {
            if ($isImage)
            {
                $imgInfo = CreateUploadedImage($_FILES["fileToUploadComment"]["name"], $_FILES["fileToUploadComment"]["tmp_name"], $_FILES["fileToUploadComment"]["size"]);
                $errorCreatingComment = $imgInfo['errorCreating'];
            }

            if ($isImage && !$errorCreatingComment || !$isImage)
                if (!isset($_SESSION['userid']))
                    PostComment("-1", $_POST['commentOn'], trim($_POST['commentBodyText']), $imgInfo, $connection);
                else
                    PostComment($_SESSION['userid'], $_POST['commentOn'], trim($_POST['commentBodyText']), $imgInfo, $connection);
        }
}


if (isset($_GET['delete']) && strlen($_GET['delete'] > 1) && !$_SESSION['SUSPENDED'])
{
    $toDelete = strtoupper($_GET['delete']);
    $isThread = $toDelete[0] == "T";
    $id = substr($toDelete, 1, strlen($toDelete) - 1);

    if (VerifyPostUserOwnsPostOrAdmin($_SESSION['permissions'], $id, $_SESSION['userid'], $isThread))
    {
        if ($isThread)
        {
            DeleteArticle($id, $connection);
        }
        elseif ($toDelete[0] == "C")
        {
            DeleteComment($id, $connection);
        }
    }
}


if (isset($_GET['viewThread']) && !empty($_GET['viewThread']))
{
    if (is_numeric($_GET['viewThread']))
        $viewThread = $_GET['viewThread'];
}

if (isset($_GET['search']) && !empty($_GET['search']) && !isset($viewThread))
{
    // only searching through articles 
    $searchQuery = CreateSearchWhereStatementForXTable(array("articles"), $_GET['search'], $connection);
}
elseif (isset($viewThread))
{
    $searchQuery = "WHERE `articles`.`articleId` = '$viewThread'";
}


if (isset($_GET['category']) && is_numeric($_GET['category']))
{
    $id = $_GET['category'];
    $selectedCategorySelectResult = RunQuery($connection, "SELECT * FROM `categories` WHERE `categories`.`categoryId` = '$id'");
}
elseif (isset($_SESSION['CURRENT_CATEGORY']))
{
    $id = $_SESSION['CURRENT_CATEGORY']['id'];
    $selectedCategorySelectResult = RunQuery($connection, "SELECT * FROM `categories` WHERE `categories`.`categoryId` = '$id'");
}
else
{
    $selectedCategorySelectResult = RunQuery($connection, "SELECT * FROM `categories`");
}

$_SESSION['CURRENT_CATEGORY'] = array();

$row = mysqli_fetch_array($selectedCategorySelectResult);

$_SESSION['CURRENT_CATEGORY']["id"] = $row["categoryId"];
$_SESSION['CURRENT_CATEGORY']["name"] = $row["categoryName"];
$_SESSION['CURRENT_CATEGORY']["description"] = $row["categoryDescription"];

//print_r($_SESSION['CURRENT_CATEGORY']);

// comment this to stop making new capchas
$_SESSION['capchaText'] = CreateSaveCapchaImage("imgs/capcha.png");

$articleSelectResult = RunQuery($connection, "SELECT `articles`.`visible`, `articles`.`articleId`, `articles`.`title`, `users`.`userId`,`users`.`username`, `articles`.`dateOfPublish`, `articles`.`publishedText`, `categories`.`categoryName`, `categories`.`categoryId`, `images`.`imageId`, `images`.`imagePath` FROM `articles` left JOIN `users` ON `articles`.`publisher` = `users`.`userId` INNER JOIN `categories` ON `articles`.`category` = `categories`.`categoryId` left JOIN `images` ON `articles`.`image` = `images`.`imageId` $searchQuery ORDER BY `articles`.`dateOfPublish` DESC ");
$categorySelectResult = RunQuery($connection, "SELECT * FROM `categories`");
?>

<!DOCTYPE html>
<html>

<header>
    <script>
        <?php include("script.js"); ?>
    </script>
    <?php require('head.php'); ?>
    <?php require('NavBar.php') ?>

    <script src="tinymce//js//tinymce//tinymce.min.js"></script>

    <script type="text/javascript">
        function initMCEexact(e) {
            tinyMCE.init({
                // https://www.tiny.cloud/docs/advanced/available-toolbar-buttons/
                mode: "exact",
                menubar: false,
                plugins: 'autolink',
                toolbar: " undo redo | bold italic strikethrough underline | subscript superscript | link unlink | removeformat | formatselect fontselect fontsizeselect",
                elements: e,
                force_br_newlines: false,
                force_p_newlines: false,
                forced_root_block: '',

            });
        }
    </script>
</header>

<body style='background-color: #EEF2FF' class='postTimestamp'>
    <div id='replyBox' class='postMain' style="position:fixed; z-index:100;<?php echo $_POST['winLoc'] ?? "right:0; min-width: 20%;min-height: 20%;" ?>">
        <img onclick="document.getElementById('replyBox').style.display = 'none';" class='float-end clickable' src='imgs//close.png' alt='&nbsp;X&nbsp;'>

        <div class='draggable' id='replyBoxDragBar' style='background:#CFD3E8;'>
            Reply To Thread <?php echo $_POST['commentOn'] ?? ""; ?>
        </div>

        <form name="replyBox" action='<?php echo $_SERVER['PHP_SELF']; ?>' method="post" enctype="multipart/form-data">
            <input id="hiddenElmArticleId" type='hidden' name='commentOn' value=''>
            <div>
                <textarea id='commentBodyTextId' type='text' name='commentBodyText' cols='45' rows='6' maxlength="2000"></textarea>
                <script>
                    initMCEexact('commentBodyTextId');
                </script>
                <input class='float-end' type="file" name="fileToUploadComment" id="fileToUpload">
                <img src="imgs/capcha.png">
                <br>
                <input type='text' name='capchaInput' maxlength="6" size='6' required>
                <input type='submit' name='postCommentButton' value='Post' <?php if ($_SESSION['SUSPENDED']) echo "disabled"; ?>>
            </div>
        </form>
    </div>

    <script>
        var dragItem = document.getElementById("replyBox");
        var container = document.getElementById("replyBoxDragBar");

        var elemLocation = dragItem.getBoundingClientRect();
        var left = elemLocation.left;
        var pageHeight = PageHeight();
        var active = false;
        var currentX;
        var currentY;
        var initialX;
        var initialY;
        var xOffset = 0;
        var yOffset = 0;

        dragItem.style.display = 'none';

        container.addEventListener("mousedown", dragStart, false);
        window.addEventListener("mouseup", dragEnd, false);
        // add mouse move to the whole page 
        // so that even if the mouse goes off the control it still drags
        window.addEventListener("mousemove", drag, false);

        function dragStart(e) {
            initialX = e.clientX - xOffset;
            initialY = e.clientY - yOffset;

            active = true;
        }

        function dragEnd(e) {
            initialX = currentX;
            initialY = currentY;

            active = false;
        }

        function drag(e) {
            if (active) {
                e.preventDefault();


                currentX = e.clientX - initialX;
                currentY = e.clientY - initialY;

                xOffset = currentX;
                yOffset = currentY;

                setTranslate(currentX, currentY, dragItem);
            }
        }

        function setTranslate(xPos, yPos, el) {
            el.style.transform = "translate3d(" + clamp(xPos, -left, 0) + "px, " + clamp(yPos, 0, pageHeight) + "px, 0)";
        }
    </script>

    <form method='GET' action='<?php echo $_SERVER['PHP_SELF']; ?>'>
        <div class="col-md-4 float-end position:fixed;">
            <div class="card my-4">
                <div class="card-body">
                    <!-- style="text-decoration: line-through;" -->
                    <div class="input-group">
                        <label class='<?php if (!$_SESSION['SUSPENDED']) echo "buttonLink"; ?> postCategory' style='color:maroon; font-size:20px;' for="createArticle">
                            <?php if (!$_SESSION['SUSPENDED']) echo "<input class='hidden' type='submit' id='createArticle' name='new' value='thread'></input>"; ?>
                            <?php if ($_SESSION['SUSPENDED']) echo "<s>"; ?>
                            Create Thread
                            <?php if ($_SESSION['SUSPENDED']) echo "</s>"; ?>
                        </label>

                    </div>
                </div>
            </div>
    </form>
    <form method='GET' action='<?php echo $_SERVER['PHP_SELF']; ?>'>

        <!-- Search widget-->
        <div class="card my-4">
            <h5 class="card-header">Search</h5>
            <div class="card-body">
                <div class="input-group">
                    <input class="form-control" name='search' type="text" placeholder="Search for...">
                    <span class="input-group-append"><input class="btn btn-secondary" type="submit" value='Go'></span>
                </div>
            </div>
        </div>

        <!-- Categories widget-->
        <div class="card my-4">
            <h5 class="card-header">Categories</h5>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <ul class="list-unstyled mb-0">

                            <?php
                            // we take the count because we want a nice split list
                            // something like this (this isn't tested with 1 or less categories so that will probably break)
                            //
                            //          `categories`
                            //  `category 1`     `category 3`
                            //  `category 2`     `category 2`
                            //
                            $count = -1;

                            // keep track of the middle of the cats so we can
                            // split into a second list
                            $countMid = floor(mysqli_num_rows($categorySelectResult) / 2);

                            while ($row = mysqli_fetch_array($categorySelectResult))
                            {
                                // if the category is hidden
                                // subtract from the middle
                                if (!$row['visible'])
                                {
                                    $countMid--;
                                    continue;
                                }
                                $count++;

                                echo AsListItem("<a href='" . $_SERVER['PHP_SELF'] . "?category=" . $row['categoryId'] . "'>" . $row['categoryName'] . "</a>");

                                // if the count of the loop is the middle
                                // end the list defined on line 313 or close to that
                                // and make a new list
                                if ($count == $countMid)
                                {
                                    echo "</ul></div>";
                                    echo "<div class='col-lg-6'>
                                                <ul class='list-unstyled mb-0'>";
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </form>

    <form method='post' action='<?php echo $_SERVER['PHP_SELF']; ?>' enctype="multipart/form-data">

        <div class='board  container'>

            <div class='col-md-8 my-4'>
                <div>
                    <?php if ($_SESSION['SUSPENDED']) echo "<span style='color:red;'>YOU ARE SUSPENDED AND WILL BE UNABLE TO POST OR COMMENT</span>"; ?>
                    <!-- show the category name and description above all the posts -->
                    <h4 class="card-header "> <?php echo '~' . strtoupper($_SESSION['CURRENT_CATEGORY']['name']) . '~'; ?></h4>
                    <h5>
                        <i>
                            <small class='card-body'>
                                <?php echo str_replace(WHITESPACE, "<br />", $_SESSION['CURRENT_CATEGORY']['description']); ?>
                            </small>
                        </i>
                    </h5>
                </div>
            </div>

            <hr class='col-md-8'>
            <hr class='col-md-8'>

            <?php
            if (isset($_GET['new']) && $_GET['new'] == "thread" || $errorCreatingPost && !$_SESSION['SUSPENDED'])
            {
            ?>
                <div class='postMain col-md-8'>
                    <h5 class="card-header">New Thread</h5>

                    <!-- if there is an error creating post -->
                    <!-- fill the last title again -->
                    <input type='text' name='threadTitleText' maxlength="55" placeholder="Title" style='width:100%;' value='<?php
                                                                                                                            if ($errorCreatingPost && isset($_POST['threadTitleText']))
                                                                                                                                echo trim($_POST['threadTitleText']); ?>'>
                    <br>
                    <!-- if there is an error creating the post -->
                    <!-- show the last typed text in the post again -->
                    <textarea type='text' id='threadBodyTextId' name='threadBodyText' cols='50' rows='6' maxlength="2000" placeholder="Body" style='width:100%;'>
                    <?php if ($errorCreatingPost && isset($_POST['threadBodyText'])) echo trim($_POST['threadBodyText']); ?></textarea>

                    <!-- this sets the textarea to the tinymce editor -->
                    <script>
                        initMCEexact('threadBodyTextId');
                    </script>

                    <input class='float-end' type="file" name="fileToUpload" id="fileToUpload">
                    <br>
                    <img src="imgs/capcha.png">
                    <br>
                    <input type='text' name='capchaInput' maxlength="6" size='6' required>
                    <input type='submit' name='postThreadButton' value='Post'>
                </div>
            <?php
            }
            ?>

            <hr class='col-md-8'>
            <!-- this is where all the threads / comments go -->

            <?php
            if (isset($articleSelectResult))
                while ($row = mysqli_fetch_array($articleSelectResult))
                {
                    // if the category isn't selected don't show posts from it
                    if ($row['categoryId'] != $_SESSION['CURRENT_CATEGORY']['id'])
                        continue;

                    // if the post isn't visible don't show it
                    if (!$row['visible'])
                        continue;

            ?>
                <!-- thread start -->
                <div class='col-md-8' style='margin:3px'>
                    <!-- op post -->

                    <div class='postMain '>
                        <!-- post info -->
                        <div class='postInfo ' id='T<?php echo $row['articleId']; ?>'>

                            <!--  show the title of the post -->
                            <span class='postCategory'>
                                <h5 class="card-header"><?php echo $row['title']; ?></h5>
                            </span>

                            <?php
                            // if the post has an image show it
                            if ($row['imageId'] != "")
                            {
                            ?>
                                <img class='float-start image' loading="lazy" src='<?php echo $row['imagePath']; ?>' alt='Failed To load image'>
                            <?php } ?>

                            <!-- `username` <`date of publish`> No.`id` [`delete`] [`reply`] -->
                            <span class='postName'>
                                <?php echo $row['username']; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                            </span>

                            <div class='float-end'>

                                <span class='postTimestamp'>
                                    <<?php echo $row['dateOfPublish']; ?>>
                                </span>
                                <span>
                                    No.<?php echo $row['articleId']; ?>&nbsp;&nbsp;
                                </span>

                                <!--  this comment is pointless but i just want to point out how good this looks -->
                                <span style='letter-spacing: -2px;'>
                                    <?php

                                    // if the user is not anonymous, and is the creator of the post
                                    // or is an admin / moderator show the delete button
                                    if (($_SESSION['userid'] == $row['userId'] && $row['userId'] != ANONYMOUS_USER_ACCOUNT_ID) || ($_SESSION['userid'] != ANONYMOUS_USER_ACCOUNT_ID && $_SESSION['permissions'] == ADMIN_PERMISSION_VALUE || $_SESSION['permissions'] == MODERATOR_PERMISSION_VALUE))
                                    {
                                        echo "&nbsp;[";
                                    ?>
                                        <label onclick="DeleteConfirmation('<?php echo $_SERVER['PHP_SELF']; ?>?delete=T<?php echo $row['articleId']; ?>')" class='buttonLink' style='letter-spacing: 0px;'>Delete</label>
                                        <?php
                                        echo "]&nbsp;["; // onclick="window.location.href ='..//editUsers.php?id=<?php echo $row['userId']; 
                                        ?>
                                        <label onclick="window.location.href ='editPosts.php?post=T<?php echo $row['articleId']; ?>'" class='buttonLink' style='letter-spacing: 0px;'>Edit</label>
                                    <?php
                                        echo "]";
                                    }

                                    // this shows the reply button
                                    if (!$_SESSION['SUSPENDED'])
                                    {
                                        echo "&nbsp;[";
                                    ?>
                                        <label onclick="changeLabel(<?php echo $row['articleId']; ?>)" class='buttonLink' style='letter-spacing: 0px;'>Reply</label>
                                    <?php

                                        // the javascript include that runs when clicking on the 
                                        // label of the reply button
                                        include("includes//replyButtonFunction.php");
                                        echo "]";
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                        <!-- post text -->
                        <div>
                            <blockquote class="postInfo">
                                <?php

                                // if not showing showing full thread
                                // determine if it should add a show more button because of truncated text
                                if (!isset($_GET['viewThread']))
                                {
                                    $totalCharLength = strlen($row['publishedText']);
                                    $t1 = trim(CreateMsgLinks(RemoveAllTextAfterXLineBreaksAndKeepXLineBreaks($row['publishedText'], 8)));
                                    $totalCharsLengthLeft = strlen($t1);

                                    if ($totalCharLength > $totalCharsLengthLeft)
                                    {
                                        echo $t1 . "<br><a class='buttonLink' href='" . $_SERVER['PHP_SELF'] . "?viewThread=" . $row['articleId'] . "'>View Full Thread</a>";
                                    }
                                    else
                                    {
                                        echo $t1;
                                    }
                                }
                                else
                                    echo trim(CreateMsgLinks($row['publishedText']));
                                ?>
                            </blockquote>
                        </div>
                    </div>

                    <!-- comments -->
                    <?php

                    $articleId = $row['articleId'];
                    $selectCommentsQuery = "SELECT `images`.`imageId`, `images`.`imagePath`, `comments`.`visible`, `article-comments`.`commentId`, `users`.`userId`,`users`.`username`, `comments`.`publisher`, `comments`.`comment`, `comments`.`publishDate` 
                                            FROM `article-comments` 
                                            INNER JOIN `comments` 
                                            ON `article-comments`.`commentId` = `comments`.`commentId`
                                            INNER JOIN `users`
                                            ON `comments`.`publisher` = `users`.`userId`

                                            left JOIN `images` ON `comments`.`image` = `images`.`imageId`

                                            WHERE `article-comments`.`articleId` = '$articleId'
                                            ORDER BY `comments`.`publishDate` Asc";

                    $comments = RunQuery($connection, $selectCommentsQuery);
                    if (mysqli_num_rows($comments) > 0)
                    {
                        $counter = 1;
                        while ($commentRow = mysqli_fetch_array($comments))
                        {
                            // no publish date = not valid comment
                            if (!isset($commentRow['publishDate']))
                                continue;

                            // if the comment is not set to visible don't show it
                            if (!$commentRow['visible'])
                                continue;

                            // after showing X number of comments add a View FUll Thread button
                            // and stop showing comments for this post
                            if ($counter > SHOW_X_COMMENTS_BEOFRE_SHOW_MORE_BUTTON && !isset($_GET['viewThread']))
                            {
                                echo "<a href='" . $_SERVER['PHP_SELF'] . "?viewThread=" . $row['articleId'] . "'>View Full Thread</a>";
                                break;
                            }
                            $counter++;
                    ?>

                            <!-- post reply -->
                            <!-- the id starts with a C followed by the id so that i can dell -->
                            <!-- the differance between a Article and a Comment -->
                            <!-- the class names don't really make sense cause i use them all over -->
                            <div class=' postReply' id='C<?php echo $commentRow['commentId']; ?>'>


                                <!-- this div holds the Username, followed by the date, then the comment Id, delete/reply button-->
                                <!-- it will look something like  -->
                                <!-- `Username` <`date of publish`> No.`id` [`delete button`] [`reply button`] -->
                                <div class='postInfo'>
                                    <?php
                                    // if the post has an image show it
                                    if ($commentRow['imageId'] != "")
                                    {
                                    ?>
                                        <img class='float-start image' loading="lazy" src='<?php echo $commentRow['imagePath']; ?>' alt='Failed To load image'>
                                    <?php } ?>

                                    <span class='postName'>
                                        <?php echo $commentRow['username']; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                                    </span>

                                    <div class='float-end'>
                                        <span class='postTimestamp'>
                                            <<?php echo $commentRow['publishDate']; ?>>
                                        </span>
                                        <span>
                                            No.<?php echo $commentRow['commentId']; ?>&nbsp;&nbsp;
                                        </span>
                                        <span style='letter-spacing: -2px;'>
                                            <?php

                                            // if the logged in user is the commenter or an admin / moderator show delete button
                                            // only show this if they're not anonymous
                                            if (($_SESSION['userid'] == $commentRow['userId'] && $commentRow['userId'] != ANONYMOUS_USER_ACCOUNT_ID) || ($_SESSION['userid'] != ANONYMOUS_USER_ACCOUNT_ID && $_SESSION['permissions'] == ADMIN_PERMISSION_VALUE || $_SESSION['permissions'] == MODERATOR_PERMISSION_VALUE))
                                            {
                                            ?>
                                                &nbsp;[&nbsp;<label onclick="DeleteConfirmation('<?php echo $_SERVER['PHP_SELF']; ?>?delete=C<?php echo $commentRow['commentId']; ?>')" class='buttonLink' style='letter-spacing: 0px;'>Delete</label>
                                                <?php
                                                echo "]&nbsp;[";
                                                ?>
                                                <label onclick="window.location.href ='editPosts.php?post=C<?php echo $commentRow['commentId']; ?>'" class='buttonLink' style='letter-spacing: 0px;'>Edit</label>
                                            <?php
                                                echo "]";
                                            }
                                            if (!$_SESSION['SUSPENDED'])
                                            {
                                            ?>
                                                &nbsp;[&nbsp;<label onclick="changeLabel(<?php echo $row['articleId']; ?>, <?php echo $commentRow['commentId']; ?>)" class='buttonLink' style='letter-spacing: 0px;'>Reply</label>]
                                            <?php
                                                include("includes//replyButtonFunction.php");
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- displays the comment -->
                                <blockquote>
                                    <?php

                                    // if its not on the view full thread page
                                    // take the current count, then truncate to 8 line breaks and the text
                                    // then take the differance to determine if it should add a Show Full Thread button
                                    if (!isset($_GET['viewThread']))
                                    {
                                        $totalCharLength = strlen($commentRow['comment']);
                                        $t1 = RemoveAllTextAfterXLineBreaksAndKeepXLineBreaks($commentRow['comment'], 8);
                                        $totalCharsLengthLeft = strlen($t1);

                                        // create the links the redirect to other comments / threads
                                        $t1 = trim(CreateMsgLinks($t1));

                                        // the + 1 is to fix something i forget what but its helpful
                                        if ($totalCharLength > $totalCharsLengthLeft + 1)
                                        {
                                            echo $t1 . "<br><a class='buttonLink' href='" . $_SERVER['PHP_SELF'] . "?viewThread=" . $row['articleId'] . "'>View Full Thread</a>";
                                        }
                                        else
                                        {
                                            echo $t1;
                                        }
                                        //echo trim(CreateMsgLinks(RemoveAllTextAfterXLineBreaksAndKeepXLineBreaks($commentRow['comment'], 8) . "<br>..."));
                                    }
                                    else
                                        echo trim(CreateMsgLinks($commentRow['comment']));
                                    //echo CreateMsgLinks($commentRow['comment']);
                                    ?>
                                </blockquote>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
                <hr>
            <?php
                }
            ?>
        </div>
    </form>
    <?php

    // if the get request for goto is set scroll to a post/comment 500ms after page load
    if (isset($_GET['goto']))
    { ?>
        <script>
            window.addEventListener('load', function() { // on page load
                setTimeout(function() {
                    scroll_To("<?php echo $_GET['goto']; ?>"); // wait 500 ms before scrolling to area
                }, (500));
            })
        </script>
    <?php
    }
    ?>
</body>

</html>