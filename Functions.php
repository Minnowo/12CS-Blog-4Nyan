<?php

const USER_PERMISSION_VALUE = 1;
const MODERATOR_PERMISSION_VALUE = 3;
const ADMIN_PERMISSION_VALUE = 2;
const SUSPENDED_PERMISSION_VALUE = 0;

const ANONYMOUS_USER_ACCOUNT_ID = -1;
const SHOW_X_COMMENTS_BEOFRE_SHOW_MORE_BUTTON = 3;

const WHITESPACE = array("\r\n", "\n", "\r");

$HIDE_ON_DELETE = true;
// u817363039_ t8ab8/0?+p0J u817363039_
function DB_Connect($SERVER_NAME = "localhost", $SERVER_USER = "root", $SERVER_PASSWORD = "", $DATA_BASE_NAME = "blog_database")
{
    $result = mysqli_connect($SERVER_NAME, $SERVER_USER, $SERVER_PASSWORD, $DATA_BASE_NAME) or die("Unable to create connection to the database, check function parameters");
    return $result;
}

function RunQuery($connection, $query)
{
    $result =  mysqli_query($connection, $query) or die("there is an error in this query \"" . $query . "\"");
    return $result;
}

function DeleteArticle($id, $connection = null)
{
    global $HIDE_ON_DELETE;

    if ($connection == null)
        $connection = DB_Connect();

    if (!$HIDE_ON_DELETE)
        RunQuery($connection, "DELETE FROM `articles` WHERE `articles`.`articleId` = '$id'");
    else
        RunQuery($connection, "UPDATE `articles` SET `visible` = '0' WHERE `articles`.`articleId` = '$id'");
}

function DeleteUser($id, $connection = null)
{
    global $HIDE_ON_DELETE;

    if ($connection == null)
        $connection = DB_Connect();

    if (!$HIDE_ON_DELETE)
        RunQuery($connection, "DELETE FROM `users` WHERE `users`.`userId` = '$id'");
    else
        RunQuery($connection, "UPDATE `users` SET `permissions` = '0' WHERE `users`.`userId` = '$id'");
}

function DeleteComment($id, $connection = null)
{
    global $HIDE_ON_DELETE;

    if ($connection == null)
        $connection = DB_Connect();

    if (!$HIDE_ON_DELETE)
        RunQuery($connection, "DELETE FROM `comments` WHERE `comments`.`commentId` = '$id'");
    else
        RunQuery($connection, "UPDATE `comments` SET `visible` = '0' WHERE `comments`.`commentId` = '$id'");
}

function DeleteCategory($id, $connection = null)
{
    global $HIDE_ON_DELETE;

    if ($connection == null)
        $connection = DB_Connect();

    if (!$HIDE_ON_DELETE)
        RunQuery($connection, "DELETE FROM `categories` WHERE `categories`.`categoryId` = '$id'");
    else
        RunQuery($connection, "UPDATE `categories` SET `visible` = '0' WHERE `categories`.`categoryId` = '$id'");
}

function PostThread($userid, $title, $text, $category, $image = array(), $connection = null)
{
    if ($connection == null)
        $connection = DB_Connect();

    // if the image info is not empty
    // look in the database for the file hash
    // if it finds it use that, otherwise insert the image data
    if ($image != null || count($image) > 0)
    {
        $exists = RunQuery($connection, "SELECT * FROM `images` WHERE `images`.`imageHash` = \"" . $image['hash'] . "\"");

        if (mysqli_num_rows($exists) > 0)
        {
            $imageId = mysqli_fetch_array($exists)['imageId'];
        }
        else
        {
            $stmt = $connection->prepare("INSERT INTO `images` (`imageId`, `imagePath`, `imageHash`) VALUES (NULL, ?, ?);");
            $stmt->bind_param("ss", $image["path"], $image["hash"]);
            $stmt->execute();

            $imageId = $stmt->insert_id;

            $stmt->close();
        }
    }
    else
    {
        $imageId = NULL;
    }

    $stmt = $connection->prepare("INSERT INTO `articles` (`articleId`, `category`, `title`, `publisher`, `dateOfPublish`, `publishedText`, `image`) VALUES (NULL, ?, ?, ?, current_timestamp(), ?, ?);");
    $stmt->bind_param("isisi", $category, $title, $userid, $text, $imageId);
    $stmt->execute();
    $stmt->close();
}

function Last($type, $input)
{
    switch ($type)
    {
        case "s":
        case "string":
            $l = strlen($input);

            if ($l < 1)
                return "";
            if ($l == 1)
                return $input[0];

            return substr($input, -1);

        case "a":
        case "array":
            $l = count($input);
            if ($l < 1)
                return "";
            if ($l == 1)
                return $input[0];

            return $input[$l - 1];
    }
}

function CreateSearchWhereStatementForXTable($tableNames, $search, $connection = null)
{
    if (strlen($search) < 1)
        return "";

    if ($connection == null)
        $connection = DB_Connect(); // hello how are you

    if ($search[0] == "\"" && Last("string", $search) == "\"")
        $search = array(strtolower(trim(substr($search, 1, strlen($search) - 2))));
    else
        $search = explode(" ", strtolower(trim($search)));

    $table_column = array();
    $searchQuery = array();

    foreach ($tableNames as $functionName)
        switch ($functionName)
        {
            case "articles":
                array_push($table_column, "`articles`.`title`");
                array_push($table_column, "`articles`.`publishedText`");
                break;

            case "categories":
                array_push($table_column, "`categories`.`categoryName`");
                array_push($table_column, "`categories`.`categoryDescription`");
                break;

            case "comments":
                array_push($table_column, "`comments`.`comment`");
                break;

            case "users":
                array_push($table_column, "`users`.`username`");
                array_push($table_column, "`users`.`email`");
                break;
        }

    if (count($table_column) < 1)
        return "";

    foreach ($search as $term)
    {
        $term = mysqli_real_escape_string($connection, $term);
        foreach ($table_column as $tbl)
        {
            array_push($searchQuery, "$tbl like '%$term%'");
        }
    }
    return "WHERE " . implode(" OR ", $searchQuery) . " ";
}


function PostComment($userid, $articleid, $text, $image = array(), $connection = null)
{
    if ($connection == null)
        $connection = DB_Connect();

    // if the image info is not empty
    // look in the database for the file hash
    // if it finds it use that, otherwise insert the image data
    if (count($image) > 0 || $image != null)
    {
        $exists = RunQuery($connection, "SELECT * FROM `images` WHERE `images`.`imageHash` = \"" . $image['hash'] . "\"");

        if (mysqli_num_rows($exists) > 0)
        {
            $imageId = mysqli_fetch_array($exists)['imageId'];
        }
        else
        {
            $stmt = $connection->prepare("INSERT INTO `images` (`imageId`, `imagePath`, `imageHash`) VALUES (NULL, ?, ?);");
            $stmt->bind_param("ss", $image["path"], $image["hash"]);
            $stmt->execute();

            $imageId = $stmt->insert_id;

            $stmt->close();
        }
    }
    else
    {
        $imageId = NULL;
    }

    $stmt = $connection->prepare("INSERT INTO `comments` (`commentId`, `publisher`, `comment`, `publishDate`, `image`) VALUES (NULL, ?, ?, current_timestamp(), ?);");
    $stmt->bind_param("isi", $userid, $text, $imageId);
    $stmt->execute();

    $commentid = $stmt->insert_id;

    $stmt->close();

    RunQuery($connection, "INSERT INTO `article-comments` (`articleId`, `commentId`) VALUES ('$articleid', '$commentid');");
}

function InsertImage($imagePath, $imageHash, $connection = null)
{
    if ($connection == null)
        $connection = DB_Connect();

    $stmt = $connection->prepare("INSERT INTO `images` (`imageId`, `imagePath`, `imageHash`) VALUES (NULL, ?, ?);");
    $stmt->bind_param("ss", $imagePath, $imageHash);
    $stmt->execute();

    $imageId = $stmt->insert_id;

    $stmt->close();

    return $imageId;
}

function UpdateArticleText($articleid, $newText, $connection = null)
{
    if ($connection == null)
        $connection = DB_Connect();

    $stmt = $connection->prepare("UPDATE `articles` SET `publishedText` = ? WHERE `articles`.`articleId` = '$articleid'; ");
    $stmt->bind_param("s", $newText);
    $stmt->execute();
    $stmt->close();
}

function UpdateUser($userId, $username, $email, $permission, $connection = null)
{
    if ($connection == null)
        $connection = DB_Connect();

    $stmt = $connection->prepare("UPDATE `users` SET `username` = ?, `email` = ?, `permissions` = '$permission' WHERE `users`.`userId` = '$userId'");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->close();
}

function UpdateArticle($articleid, $newTitle, $newBody, $imageId, $categoryID, $visible, $connection = null)
{
    if ($connection == null)
        $connection = DB_Connect();

    $query = array();
    $binds = "";
    $updateTitle = false;

    if ($newTitle != null)
    {
        array_push($query, "`title` = ?");
        $binds = $binds . "s";
        $updateTitle = true;
    }

    if ($newBody != null)
    {
        array_push($query, "`publishedText` = ?");
        $binds = $binds . "s";
    }

    if ($imageId != null)
    {
        array_push($query, "`image` = '$imageId'");
    }

    if ($categoryID != null)
    {
        array_push($query, "`category` = '$categoryID'");
    }

    if ($visible != null)
    {
        array_push($query, "`visible` = '$visible'");
    }

    $query = implode(", ", $query);

    $query = "UPDATE `articles` SET " . $query . " WHERE `articles`.`articleId` = '$articleid'";

    $stmt = $connection->prepare($query);

    if (strlen($binds) > 1)
        switch ($binds)
        {
            case "s":
                if ($updateTitle)
                {
                    $stmt->bind_param("s", $newTitle);
                    break;
                }
                $stmt->bind_param("s", $newBody);
                break;

            case "ss":
                $stmt->bind_param("ss", $newTitle, $newBody);
                break;
        }

    $stmt->execute();
    $stmt->close();
}

function CreateUploadedImage($file, $tmpFileName, $fileUploadSize, $target_dir = "imgs//upload//")
{
    $imgInfo = array("errorCreating" => true);

    $target_file = $target_dir . basename($file); //$_FILES["fileToUpload"]["name"]
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if ($fileUploadSize < 40000000) // 40 mb $_FILES["fileToUpload"]["size"]
        if (in_array($imageFileType, array("jpg", "png", "jpeg", "gif", "jpe", "jfif", "bmp")))
            if (move_uploaded_file($tmpFileName, $target_file)) //$_FILES["fileToUpload"]["tmp_name"]
            {
                $imgInfo['errorCreating'] = false;
                $imgInfo['hash'] = hash_file("sha256", $target_file);
                $imgInfo['path'] = $target_dir . $imgInfo['hash'] . "." . $imageFileType;

                if (!file_exists($imgInfo['path']))
                {
                    if (!rename($target_file, $imgInfo['path']))
                    {
                        $imgInfo['errorCreating'] = true;
                    }
                }
                else
                {
                    unlink($target_file);
                }
            }
    return $imgInfo;
}

function UpdateComment($commentid, $text, $image, $visible, $connection = null)
{
    if ($connection == null)
        $connection = DB_Connect();

    if (!is_numeric($image))
        $stmt = $connection->prepare("UPDATE `comments` SET `comment` = ?, `image` = NULL, `visible` = '$visible' WHERE `comments`.`commentId` = '$commentid'; ");
    else
        $stmt = $connection->prepare("UPDATE `comments` SET `comment` = ?, `image` = '$image', `visible` = '$visible' WHERE `comments`.`commentId` = '$commentid'; ");
    $stmt->bind_param("s", $text);
    $stmt->execute();
    $stmt->close();
}

function UpdateCommentText($commentid, $newText, $connection = null)
{
    if ($connection == null)
        $connection = DB_Connect();

    $stmt = $connection->prepare("UPDATE `comments` SET `comment` = ? WHERE `comments`.`commentId` = '$commentid'; ");
    $stmt->bind_param("s", $newText);
    $stmt->execute();
    $stmt->close();
}

function UpdateCategory($id, $newName, $newDescription, $visible, $connection = null)
{
    if ($connection == null)
        $connection = DB_Connect();

    if (isBoolean($visible))
        $visible = 1;
    else
        $visible = 0;

    $stmt = $connection->prepare("UPDATE `categories` SET `categoryName` = ?, `categoryDescription` = ?,  `visible` = '$visible' WHERE `categories`.`categoryId` = '$id'");
    $stmt->bind_param("ss", $newName, $newDescription);
    $stmt->execute();
    $stmt->close();
}

function CreateSaveCapchaImage($filepath = "imgs/tmp.png")
{
    try
    {
        unlink($filepath);
    }
    catch (Exception $exc)
    {
    }
    $im = @imagecreate(60, 20) or die("Cannot Initialize new GD image stream");
    $imlrg = @imagecreate(120, 40) or die("connot init new gd image");

    // generate random string of text
    $text = strtoupper(substr(md5(date("Y-m-d-h-m-s", strtotime("-5 days")), false), 0, 6));

    // set the background to black
    imagecolorallocate($im, 0, 0, 0);

    // draw the text (max font size 5) at (2, 2) of color rgb(223, 224, 224)
    imagestring($im, 5, 2, 2,  $text, imagecolorallocate($im, 223, 223, 223));

    for ($y = 0; $y < 9; $y += 3)
        // draw some black lines through the text 
        imagestring($im, 2, 0, $y, "----------", imagecolorallocate($im, 214, 33, 88));

    // resize the smaller image to fit the larger image
    // this is because we can't make the font size any bigger
    imagecopyresampled($imlrg, $im, 0, 0, 0, 0, 120, 40, 60, 20);

    imagepng($imlrg, $filepath); // save larger image to disk
    imagedestroy($im);          // dispose of resource
    imagedestroy($imlrg);       // dispose of resource
    return $text;
}

function DisplayBool($input)
{
    $input = "$input"; // convert to string i think?

    if (strlen($input) < 1)
        return "NULL";

    switch (trim(strtolower($input)))
    {
        case "f":
        case "0":
        case "false":
            return "False";

        case "t":
        case "1":
        case "true":
            return "True";
    }

    return "NULL";
}

function isBoolean($value)
{
    if ($value && strtolower($value) !== "false")
    {
        return true;
    }
    else
    {
        return false;
    }
}


function OverLoad($functionName, $args)
{
    if ($functionName == 'area')
    {
        switch (count($args))
        {
            case 1:
                return 3.14 * $args[0];

            case 2:
                return $args[0] * $args[1];
        }
    }
}


function AsTableRow($input)
{
    return "<tr>" . $input . "</tr>";
}

function AsTableColumn($input)
{
    return "<td>" . $input . "</td>";
}

function AsListItem($input)
{
    return "<li>" . $input . "</li>";
}

function AsBoldText($input)
{
    return "<strong>" . $input . "</strong>";
}

function VerifyPostUserOwnsPostOrAdmin($userPermsLevel, $postId, $userId, $isThread, $connection = null)
{
    if ($userId == ANONYMOUS_USER_ACCOUNT_ID)
        return false;

    if ($userPermsLevel == ADMIN_PERMISSION_VALUE || $userPermsLevel == MODERATOR_PERMISSION_VALUE)
        return true;

    if ($connection == null)
        $connection = DB_Connect();

    if ($isThread)
        $ans = RunQuery($connection, "SELECT * FROM `articles` WHERE `articles`.`articleId` = '$postId'");
    else
        $ans = RunQuery($connection, "SELECT * FROM `comments` WHERE `comments`.`commentId` = '$postId'");

    // nothing found in db so no reason to try deleting
    if (mysqli_num_rows($ans) < 1)
        return false;

    // the person who made the post is the person trying 
    // to delete the post, so we allow them too
    if (mysqli_fetch_array($ans)['publisher'] == $userId)
        return true;

    return false;
}


function DebugWrite($text, $mode = "a", $filePath = "newfile.txt")
{
    $myfile = fopen($filePath, $mode) or die("Unable to open file!");
    fwrite($myfile, $text);
    fclose($myfile);
}


function Clamp($input, $min, $max)
{
    if ($input < $min)
        return $min;
    if ($input > $max)
        return $max;
    return $input;
}

function StringRepeat($string, $repeat)
{
    $tmp = "";
    for ($i = 0; $i < $repeat; $i++)
    {
        $tmp = $tmp . $string;
    }
    return $tmp;
}


function RemoveAllTextAfterXLineBreaksAndKeepXLineBreaks($input, $numberToKeep)
{
    $htmlBR = array("<br>", "<br />", "&nbsp;");
    $remove = "<br/>";

    $textAfter = str_replace($htmlBR, $remove, $input);
    $newString = "";

    $len = strlen($textAfter);
    $lenRemove = strlen($remove);

    $total = substr_count($textAfter, $remove);

    if ($total < $numberToKeep)
        return $textAfter . StringRepeat(" ", strlen($input) - $len);

    $numberRemoved = 0;
    $inTag = false;
    for ($i = $len - 1; $i >= 0; $i--)
    {
        $check = "";
        for ($x = $lenRemove - 1; $x >= 0; $x--)
        {
            if ($x < 0 || $i - $x  < 0 || $i - $lenRemove - 1 < 0)
                break;

            $check = $check . $textAfter[Clamp($i - $x, 0, $len - 1)];
        }

        if ($check == $remove)
        {
            for ($x = $lenRemove - 1; $x >= 0; $x--)
            {
                if ($x < 0 || $i - $x  < 0 || $i - $lenRemove - 1 < 0)
                    break;

                $textAfter[Clamp($i - $x, 0, $len - 1)] = " ";
            }
            $numberRemoved++;
            if ($total - $numberRemoved <= $numberToKeep)
            {
                return substr($textAfter, 0, $i) . $newString;
            }
            continue;
        }

        if ($textAfter[$i] == ">")
        {
            $inTag = true;
            $newString = ">" . $newString;
            continue;
        }

        if ($textAfter[$i] == "<" && $inTag)
        {
            $inTag = false;
            $newString = "<" . $newString;
            continue;
        }

        if ($inTag)
        {
            $newString = $textAfter[$i] . $newString;
            continue;
        }
    }
    return $textAfter;
}

function KeepXLineBreaks($input, $numberToKeep)
{
    $htmlBR = array("<br>", "<br />", "&nbsp;");
    $remove = "<br/>";

    $textAfter = str_replace($htmlBR, $remove, $input);

    $len = strlen($textAfter);
    $lenRemove = strlen($remove);

    $total = substr_count($textAfter, $remove);
    $numberRemoved = 0;

    for ($i = $len - 1; $i >= 0; $i--)
    {
        $check = "";
        for ($x = $lenRemove - 1; $x >= 0; $x--)
        {
            if ($x < 0 || $i - $x  < 0 || $i - $lenRemove - 1 < 0)
                break;

            $check = $check . $textAfter[Clamp($i - $x, 0, $len - 1)];
        }

        if ($check == $remove)
        {
            for ($x = $lenRemove; $x >= 0; $x--)
            {
                if ($x < 0 || $i - $x  < 0 || $i - $lenRemove - 1 < 0)
                    break;
                $textAfter[Clamp($i - $x, 0, $len - 1)] = " ";
            }
            $numberRemoved++;
            if ($total - $numberRemoved <= $numberToKeep)
            {
                return $textAfter;
            }
        }
    }
    return $textAfter;
}


function CreateMsgLinks($comment, $useGet = false, $categoryID = null, $url = "")
{
    $str = $comment;

    $newString = preg_split('/&lt;@(c|t)\d+&gt;/i', $str);
    preg_match_all('/&lt;@(?<type>c|t)(?<id>\d+)&gt;/i', $str, $matches);

    $numOfMatches = count($matches['id']);
    for ($i = 0; $i < $numOfMatches; $i++)
    {
        if (!$useGet || $categoryID == null || $url == "")
            $newString[$i] = $newString[$i] . "<label onclick='scroll_To(\"" . $matches['type'][$i] . $matches['id'][$i] . "\")' class='buttonLink' style='letter-spacing: 0;'>>>" . $matches['type'][$i] . $matches['id'][$i] . " </label>&nbsp;";
        else
            $newString[$i] = $newString[$i] . "<a href ='$url?category=$categoryID&goto=" . $matches['type'][$i] . $matches['id'][$i] . "'>" . ">>" . $matches['type'][$i] . $matches['id'][$i] . "</a>";
    }

    return implode("", $newString);
}

function GetFirst($string, $num)
{
    if (strlen($string) < $num)
        return $string;

    return substr($string, $num);
}
