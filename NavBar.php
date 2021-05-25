 <?php

    include('head.php');

    $navbar = array();

    if (isset($_SESSION['LOGGED_IN']))
    {
        if ($_SESSION['LOGGED_IN'])
        {
            if ($_SESSION['CURRENT_PAGE'] != "INDEX_ADMIN")
                array_push($navbar, "<li class='nav-item'><a class='nav-link' href='Logout.php'>Logout</a></li>");
            else
                array_push($navbar, "<li class='nav-item'><a class='nav-link' href='..//Logout.php'>Logout</a></li>");
        }

        if ($_SESSION['CURRENT_PAGE'] != "REGISTER_PAGE" && !$_SESSION['LOGGED_IN'])
        {
            if ($_SESSION['CURRENT_PAGE'] != "INDEX_ADMIN")
                array_push($navbar, "<li class='nav-item'><a class='nav-link' href='Register.php'>Register</a></li>");
            else
                array_push($navbar, "<li class='nav-item'><a class='nav-link' href='..//Register.php'>Register</a></li>");
        }

        if ($_SESSION['CURRENT_PAGE'] != "LOGIN_PAGE" && !$_SESSION['LOGGED_IN'])
        {
            if ($_SESSION['CURRENT_PAGE'] != "INDEX_ADMIN")
                array_push($navbar, "<li class='nav-item'><a class='nav-link' href='Login.php'>Login</a></li>");
            else
                array_push($navbar, "<li class='nav-item'><a class='nav-link' href='..//Login.php'>Login</a></li>");
        }

        if (isset($_SESSION['IS_ADMIN']) && $_SESSION['IS_ADMIN'])
        {
            if ($_SESSION['CURRENT_PAGE'] != "INDEX_ADMIN")
                array_push($navbar, "<li class='nav-item'><a class='nav-link' href='admin//index.php'>Admin</a></li>");
            else
                array_push($navbar, "<li class='nav-item'><a class='nav-link' href='index.php'>Admin</a></li>");
        }
    }

    if ($_SESSION['CURRENT_PAGE'] != "INDEX_ADMIN")
        array_push($navbar, "<li class='nav-item'><a class='nav-link' href='Index.php'>Home</a></li>");
    else
        array_push($navbar, "<li class='nav-item'><a class='nav-link' href='..//Index.php'>Home</a></li>");


    ?>

 <html>

 <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
     <div class="container">
         <a class="navbar-brand" href="#!">~4Nyan~</a>

         <div class="collapse navbar-collapse" id="navbarResponsive">
             <ul class="navbar-nav ml-auto">
                 <?php echo join('', $navbar); ?>
             </ul>
         </div>
         <div class='nav-item nav-right col-md-4'>
             <span style='color:white;'><?php if (isset($_SESSION['IS_ADMIN']) && $_SESSION['IS_ADMIN']) echo AsBoldText("Administrator: ");
                                        echo $_SESSION['username'] ?? ""; ?> </span>
         </div>
     </div>
 </nav>

 </html>