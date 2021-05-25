<?php
// reserved for super admins

session_start();

require("../Functions.php");

$_SESSION['CURRENT_PAGE'] = "INDEX_ADMIN";
$connection = DB_Connect();

if (!isset($_SESSION['userid']) || !isset($_SESSION['IS_ADMIN']) || !$_SESSION['IS_ADMIN'])
{
    header("location: ../Login.php");
    return;
}

if (!isset($_GET['show']) && !is_numeric($_GET['show']))
{
    header("location:" . $_SERVER['PHP_SELF'] . "?show=1");
    return;
}

if (isset($_GET['add']) && $_GET['add'] == 1)
{
    RunQuery($connection, "INSERT INTO `categories` (`categoryId`, `categoryName`, `categoryDescription`, `visible`) VALUES (NULL, 'New Category', 'Filler Text', '0');");
}

// rather define empty value then check if isset later
$search = array("articles" => "", "comments" => "", "users" => "", "categories" => "");
if (isset($_GET['search']))
{
    switch ($_GET['show'])
    {
        case 1:
            $search['articles'] = CreateSearchWhereStatementForXTable(array("articles"), $_GET['search'], $connection);
            break;
        case 2:
            $search['comments'] = CreateSearchWhereStatementForXTable(array("comments"), $_GET['search'], $connection);
            break;
        case 3:
            $search['categories'] = CreateSearchWhereStatementForXTable(array("categories"), $_GET['search'], $connection);
            break;
        case 4:
            $search['users'] = CreateSearchWhereStatementForXTable(array("users"), $_GET['search'], $connection);
            break;
    }
}

$ordby = "ORDER BY `articles`.`dateOfPublish` DESC ";
if (!isset($_GET['ord']))
    $_GET['ord'] = 1;
else
{
    // if the user changes the value of ord it will still become 1 or 0
    if (isBoolean($_GET['ord']))
        $_GET['ord'] = 1;
    else
        $_GET['ord'] = 0;
}

if (isBoolean($_GET['ord']))
    $ord = "ASC";
else
    $ord = "DESC";

if (isset($_GET['orderBy']))
{
    switch ($_GET['orderBy'])
    {
        case "id":
            $ordby = "ORDER BY `articles`.`articleId` $ord ";
            break;
        case "title":
            $ordby = "ORDER BY `articles`.`title` $ord ";
            break;
        case "text":
            $ordby = "ORDER BY `articles`.`publishedText` $ord ";
            break;
        case "category":
            $ordby = "ORDER BY `articles`.`category` $ord ";
            break;
        case "creationdate":
            $ordby = "ORDER BY `articles`.`dateOfPublish` $ord ";
            break;
        case "imageid":
            $ordby = "ORDER BY `images`.`imageId` $ord ";
            break;
        case "visible":
            $ordby = "ORDER BY `articles`.`visible` $ord ";
            break;
        default:
            $ordby = "ORDER BY `articles`.`dateOfPublish` $ord ";
            break;
    }
}
$articleSelectQuery = RunQuery($connection, "SELECT `articles`.`visible`, `articles`.`articleId`, `articles`.`title`, `users`.`userId`,`users`.`username`, `articles`.`dateOfPublish`, `articles`.`publishedText`, `categories`.`categoryName`, `categories`.`categoryId`, `images`.`imageId`, `images`.`imagePath` 
FROM `articles` 
left JOIN `users` ON `articles`.`publisher` = `users`.`userId` 
INNER JOIN `categories` ON `articles`.`category` = `categories`.`categoryId` 
left JOIN `images` ON `articles`.`image` = `images`.`imageId` 
" . $search['articles'] . " " . $ordby);

?>

<html>
<header>
    <header>
        <script>
            <?php include("../script.js"); ?>
        </script>

        <?php require('head.php'); ?>
        <?php require('../NavBar.php') ?>
    </header>
</header>


<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <li><a class='nav-link' href='<?php echo $_SERVER['PHP_SELF']; ?>?show=1' <?php if (isset($_GET['show']) && $_GET['show'] == 1) echo "style='color:white;'"; ?>>Threads</a></li>
                <li><a class='nav-link' href='<?php echo $_SERVER['PHP_SELF']; ?>?show=2' <?php if (isset($_GET['show']) && $_GET['show'] == 2) echo "style='color:white;'"; ?>>Comments</a></li>
                <li><a class='nav-link' href='<?php echo $_SERVER['PHP_SELF']; ?>?show=3' <?php if (isset($_GET['show']) && $_GET['show'] == 3) echo "style='color:white;'"; ?>>Categories</a></li>
                <li><a class='nav-link' href='<?php echo $_SERVER['PHP_SELF']; ?>?show=4' <?php if (isset($_GET['show']) && $_GET['show'] == 4) echo "style='color:white;'"; ?>>Users</a></li>
            </ul>
        </div>
        <div class='float-end col-md-4'>
            <form method='GET' action='<?php echo $_SERVER['PHP_SELF']; ?>'>
                <input type='hidden' name='show' value='<?php if (isset($_GET['show'])) echo $_GET['show']; ?>'>
                <input name="search" type="text" class="form-control" style="width:100%;" placeholder="Search for...">
            </form>
        </div>
    </div>
</nav>


<body style='background-color: #EEF2FF' class='postTimestamp'>

    <hr>
    <div style='margin-left:10%; margin-right:10%'>
        <?php

        if (isset($_GET['show']) && $_GET['show'] == 1)
        {
        ?>
            <h4 class="card-header ">Threads</h4>
            <table class="table table-striped" border='1px'>

                <thead>
                    <th><a href='?show=1&orderBy=id&ord=<?php echo 1 - $_GET['ord'];
                                                        if (isset($_GET['search']))
                                                            echo "&search=" . $_GET['search']; ?>'>Id</a></th>
                    <th><a href='?show=1&orderBy=title&ord=<?php echo 1 - $_GET['ord'];
                                                            if (isset($_GET['search']))
                                                                echo "&search=" . $_GET['search']; ?>'>ThreadTitle</a></th>
                    <th><a href='?show=1&orderBy=text&ord=<?php echo 1 - $_GET['ord'];
                                                            if (isset($_GET['search']))
                                                                echo "&search=" . $_GET['search']; ?>'>Text</a></th>
                    <th><a href='?show=1&orderBy=category&ord=<?php echo 1 - $_GET['ord'];
                                                                if (isset($_GET['search']))
                                                                    echo "&search=" . $_GET['search']; ?>'>Category</a></th>
                    <th><a href='?show=1&orderBy=creationdate&ord=<?php echo 1 - $_GET['ord'];
                                                                    if (isset($_GET['search']))
                                                                        echo "&search=" . $_GET['search']; ?>'>CreationDate</a></th>
                    <th><a href='?show=1&orderBy=imageid&ord=<?php echo 1 -  $_GET['ord'];
                                                                if (isset($_GET['search']))
                                                                    echo "&search=" . $_GET['search']; ?>'>ImageID</a></th>
                    <th><a href='?show=1&orderBy=visible&ord=<?php echo 1 - $_GET['ord'];
                                                                if (isset($_GET['search']))
                                                                    echo "&search=" . $_GET['search']; ?>'>Visible</a></th>
                </thead>

                <?php
                if (isset($articleSelectQuery) && mysqli_num_rows($articleSelectQuery) > 0)
                    while ($row = mysqli_fetch_array($articleSelectQuery))
                    {
                        echo "<tr>";
                ?>
                    <td>
                        <label class='btn btn-outline-primary' onclick="window.location.href ='..\/editPosts.php?post=<?php echo 'T' . $row['articleId']; ?>'"> Edit Thread:<?php echo $row["articleId"]; ?> </label>
                    </td>

                <?php
                        echo AsTableColumn($row["title"]);
                        echo AsTableColumn("<blockquote>" . RemoveAllTextAfterXLineBreaksAndKeepXLineBreaks(CreateMsgLinks($row['publishedText'], true, $row['categoryId'], "../Index.php"), 8) . "</blockquote>");
                        echo AsTableColumn($row["categoryName"]);
                        echo AsTableColumn($row["dateOfPublish"]);
                        echo AsTableColumn($row['imageId'] ?? "");
                        echo AsTableColumn(DisplayBool($row['visible']));
                        echo "</tr>";
                    }
                ?>
            </table>
            <br>
            <hr>
            <br>
        <?php
        }


        if (isset($_GET['show']) && $_GET['show'] == 2)
        {
        ?>
            <h2>Comments</h2>
            <table class="table table-striped" border='1px'>

                <thead>
                    <th><a href='?show=2&orderBy=id&ord=<?php
                                                        echo 1 - $_GET['ord'];
                                                        if (isset($_GET['search']))
                                                            echo "&search=" . $_GET['search']; ?>'>Id</a></th>
                    <th><a href='?show=2&orderBy=publisher&ord=<?php
                                                                echo 1 - $_GET['ord'];
                                                                if (isset($_GET['search']))
                                                                    echo "&search=" . $_GET['search']; ?>'>Publisher</a></th>
                    <th><a href='?show=2&orderBy=comment&ord=<?php
                                                                echo 1 - $_GET['ord'];
                                                                if (isset($_GET['search']))
                                                                    echo "&search=" . $_GET['search']; ?>'>Comment</a></th>
                    <th><a href='?show=2&orderBy=creationdate&ord=<?php
                                                                    echo 1 - $_GET['ord'];
                                                                    if (isset($_GET['search']))
                                                                        echo "&search=" . $_GET['search']; ?>'>CreationDate</a></th>
                    <th><a href='?show=2&orderBy=visible&ord=<?php
                                                                echo 1 - $_GET['ord'];
                                                                if (isset($_GET['search']))
                                                                    echo "&search=" . $_GET['search']; ?>'>Visible</a></th>
                </thead>

                <?php

                $ordby = "ORDER BY `comments`.`publishDate` DESC ";

                if (isset($_GET['orderBy']))
                    switch ($_GET['orderBy'])
                    {
                        default:
                            $ordby = "ORDER BY `comments`.`publishDate` $ord ";
                            break;
                        case "id":
                            $ordby = "ORDER BY `comments`.`commentId` $ord ";
                            break;
                        case "publisher":
                            $ordby = "ORDER BY `comments`.`publisher` $ord ";
                            break;
                        case "comment":
                            $ordby = "ORDER BY `comments`.`comment` $ord ";
                            break;
                        case "creationdate":
                            $ordby = "ORDER BY `comments`.`publishDate` $ord ";
                            break;
                        case "visible":
                            $ordby = "ORDER BY `comments`.`visible` $ord ";
                            break;
                    }

                $articleSelectQuery = RunQuery($connection, "SELECT `comments`.`visible`, `comments`.`commentId`, `comments`.`publisher`, `users`.`username`, `comments`.`comment`, `comments`.`publishDate`, `articles`.`category`
                    FROM `article-comments` 
                    INNER JOIN `comments` ON `article-comments`.`commentId` = `comments`.`commentId`
                    INNER JOIN `articles` ON `article-comments`.`articleId` = `articles`.`articleId`
                    INNER JOIN `users` ON `comments`.`publisher` = `users`.`userId`  
                    " . $search['comments'] . " " . $ordby);

                if (isset($articleSelectQuery) && mysqli_num_rows($articleSelectQuery) > 0)
                    while ($row = mysqli_fetch_array($articleSelectQuery))
                    {
                        echo "<tr>";
                ?>
                    <td>
                        <label class='btn btn-outline-primary' onclick="window.location.href ='..\/editPosts.php?post=<?php echo 'C' . $row['commentId']; ?>'"> Edit<br>Comment:<?php echo $row["commentId"]; ?> </label>
                    </td>
                <?php
                        echo AsTableColumn($row['username']);
                        echo AsTableColumn("<blockquote>" . CreateMsgLinks(RemoveAllTextAfterXLineBreaksAndKeepXLineBreaks($row['comment'], 8), true, $row['category'], "../Index.php") . "</blockquote>");
                        echo AsTableColumn($row['publishDate']);
                        echo AsTableColumn(DisplayBool($row['visible']));
                        echo "</tr>";
                    }
                ?>
            </table>
            <br>
            <hr>
            <br>
        <?php
        }

        if (isset($_GET['show']) && $_GET['show'] == 3)
        {
        ?>

            <a href='<?php echo $_SERVER['PHP_SELF']; ?>?show=3&add=1'>Add Category</a>
            <h2>Categories</h2>
            <table class="table table-striped" border='1px'>

                <thead>
                    <th><a href='?show=3&orderBy=id&ord=<?php
                                                        echo 1 - $_GET['ord'];
                                                        if (isset($_GET['search']))
                                                            echo "&search=" . $_GET['search']; ?>'>Id</a></th>
                    <th><a href='?show=3&orderBy=name&ord=<?php
                                                            echo 1 - $_GET['ord'];
                                                            if (isset($_GET['search']))
                                                                echo "&search=" . $_GET['search']; ?>'>Name</a></th>
                    <th><a href='?show=3&orderBy=description&ord=<?php
                                                                    echo 1 - $_GET['ord'];
                                                                    if (isset($_GET['search']))
                                                                        echo "&search=" . $_GET['search']; ?>'>Description</a></th>
                    <th><a href='?show=3&orderBy=visible&ord=<?php
                                                                echo 1 - $_GET['ord'];
                                                                if (isset($_GET['search']))
                                                                    echo "&search=" . $_GET['search']; ?>'>Visible</a></th>
                </thead>
                <?php
                $ordby = "ORDER BY `categories`.`categoryId` DESC ";

                if (isset($_GET['orderBy']))
                    switch ($_GET['orderBy'])
                    {
                        default:
                            $ordby = "ORDER BY `categories`.`categoryId` $ord ";
                            break;
                        case "id":
                            $ordby = "ORDER BY `categories`.`categoryId` $ord ";
                            break;
                        case "name":
                            $ordby = "ORDER BY `categories`.`categoryName` $ord ";
                            break;
                        case "description":
                            $ordby = "ORDER BY `categories`.`categoryDescription` $ord ";
                            break;
                        case "visible":
                            $ordby = "ORDER BY `categories`.`visible` $ord ";
                            break;
                    }

                $categorySelectQuery = RunQuery($connection, "SELECT * FROM `categories` "  . $search['categories'] . " " . $ordby);
                if (isset($categorySelectQuery) && mysqli_num_rows($categorySelectQuery) > 0)
                    while ($row = mysqli_fetch_array($categorySelectQuery))
                    {
                        //print_r($row);
                        echo "<tr>";
                ?>
                    <td>
                        <label class='btn btn-outline-primary' onclick="window.location.href ='..\/editCategories.php?category=<?php echo $row['categoryId']; ?>'"> Edit<br>Category:<?php echo $row["categoryId"]; ?> </label>
                    </td>
                <?php
                        echo AsTableColumn($row['categoryName']);
                        echo AsTableColumn("<blockquote>" .  RemoveAllTextAfterXLineBreaksAndKeepXLineBreaks($row['categoryDescription'], 8) . "</blockquote>");
                        echo AsTableColumn(DisplayBool($row['visible']));
                        echo "</tr>";
                    }
                ?>
            </table>
            <br>
            <hr>
            <br>

        <?php }

        if (isset($_GET['show']) && $_GET['show'] == 4)
        {
        ?>
            <h2>Users</h2>
            <table class="table table-striped" border='1px'>

                <thead>
                    <th><a href='?show=4&orderBy=id&ord=<?php
                                                        echo 1 - $_GET['ord'];
                                                        if (isset($_GET['search']))
                                                            echo "&search=" . $_GET['search']; ?>'>Id</a></th>
                    <th><a href='?show=4&orderBy=username&ord=<?php
                                                                echo 1 - $_GET['ord'];
                                                                if (isset($_GET['search']))
                                                                    echo "&search=" . $_GET['search']; ?>'>Username</a></th>
                    <th><a href='?show=4&orderBy=email&ord=<?php
                                                            echo 1 - $_GET['ord'];
                                                            if (isset($_GET['search']))
                                                                echo "&search=" . $_GET['search']; ?>'>Email</a></th>
                    <th><a href='?show=4&orderBy=permission&ord=<?php
                                                                echo 1 - $_GET['ord'];
                                                                if (isset($_GET['search']))
                                                                    echo "&search=" . $_GET['search']; ?>'>PermissionLevel</a></th>
                </thead>
                <?php
                $ordby = "ORDER BY `users`.`userId` DESC ";

                if (isset($_GET['orderBy']))
                    switch ($_GET['orderBy'])
                    {
                        default:
                            $ordby = "ORDER BY `users`.`userId` $ord ";
                            break;
                        case "id":
                            $ordby = "ORDER BY `users`.`userId` $ord ";
                            break;
                        case "username":
                            $ordby = "ORDER BY `users`.`username` $ord ";
                            break;
                        case "email":
                            $ordby = "ORDER BY `users`.`email` $ord ";
                            break;
                        case "permission":
                            $ordby = "ORDER BY `users`.`permissions` $ord ";
                            break;
                    }

                $categorySelectQuery = RunQuery($connection, "SELECT * FROM `users` "  . $search['users'] . " " . $ordby);
                if (isset($categorySelectQuery) && mysqli_num_rows($categorySelectQuery) > 0)
                    while ($row = mysqli_fetch_array($categorySelectQuery))
                    {
                        if ($row['userId'] == ANONYMOUS_USER_ACCOUNT_ID)
                            continue;
                        echo "<tr>";
                ?>
                    <td>
                        <label class='btn btn-outline-primary' onclick="window.location.href ='..\/editUsers.php?id=<?php echo $row['userId']; ?>'"> Edit<br>User:<?php echo $row["userId"]; ?> </label>
                    </td>
                <?php
                        echo AsTableColumn($row['username']);
                        echo AsTableColumn($row['email']);
                        echo AsTableColumn($row['permissions']);
                        echo "</tr>";
                    }
                ?>
            </table>
            <br>
            <hr>
            <br>
        <?php
        }
        ?>
    </div>
</body>

</html>