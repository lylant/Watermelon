<?php
    require_once("nocache.php"); // prevent the cache
    session_start(); // start the session

    // check if the user is already logged in
    if (isset($_SESSION["userID"])){
        header("location: logout.php"); // if so, redirect to logout.php
    }

    $loginErrorMsg = "";
    $loginErrorIcon = "hidden";

    if(isset($_REQUEST["loginSubmit"])) { // login attempt

        if(empty($_POST['loginUsername']) || empty($_POST['loginPassword'])) { // missing input
            $loginErrorMsg = "Both username and password are required";
            $loginErrorIcon = "visible";
    }
        else { // valid input
            require_once("conn.php"); // connect to DBMS, using MySQLi

            // parse the input using the function MySQLi::escape_string
            $username = $dbConn -> escape_string($_POST['loginUsername']);
            $password = $dbConn -> escape_string($_POST['loginPassword']);

            $passwordHashed = hash("sha256", $password); // hash the password using the sha256 algorithm

            // create a SQL query to compare the input with the DB record
            $queryLogin = ("SELECT US.username
                                , US.password
                                , US.userID
                                , US.lName
                                , US.fName
                                , RO.name AS category
                            FROM `User` US
                            INNER JOIN UserRole UR
                                ON US.userID = UR.userID
                            INNER JOIN `Role` RO
                                ON UR.roleID = RO.roleID
                            WHERE US.username = '$username'
                                AND US.password = '$passwordHashed';");
            $queryLoginResult = $dbConn -> query($queryLogin)
                or die("Problem with query: comparing the login information | " . $dbConn->error);

            if(!($queryLoginResult -> num_rows)) { // no match found
                $loginErrorMsg = "Username or password is invalid";
                $loginErrorIcon = "visible";
                $dbConn->close(); // disconnecting
            }

            else { // match found

                // store the user details in session variables
                $user = $queryLoginResult -> fetch_assoc();
                $_SESSION["userID"] = $user["userID"];
                $_SESSION["userRank"] = $user["category"];
                $_SESSION["userRealname"] = "{$user['fName']} {$user['lName']}";

                $dbConn->close(); // disconnecting
                header("Location: search.php"); // redirect to search.php
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Watermelon</title>
        <link href="https://fonts.googleapis.com/css2?family=Tenali+Ramakrishna&family=Anton&display=swap" rel="stylesheet">
        <link type="text/css" rel="stylesheet" href="styles.css">
        <script type="text/javascript" src="login.js"></script>
    </head>

    <body>
        <header>
        <!-- Navigation Bar -->
            <?php include_once("navbar.php"); ?>
        </header>

        <div class="loginDiv">

    <!-- Login Form -->        
            <form id="loginForm" method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
            <!-- Header -->
                <div class="loginDivHeader">
                    <h1>Watermelon</h1>
                    <h2>Sign In</h2>
                </div>

            <!-- Body -->
                <div class="loginDivBody">
                    <p><input class="loginField" type="text" id="loginUsername" name="loginUsername" placeholder="Username"></p>
                    <p><input class="loginField" type="password" id="loginPassword" name="loginPassword" placeholder="Password"></p>
                    <p>
                        <input class="loginButton" type="submit" value="Login!" name="loginSubmit">
                        <a href="#" onclick="newMemberToggle(event, 'newMemberDiv');">+ Sign Up</a>
                    </p>
                </div>
            </form>

        <!-- Login Error Message -->
            <?php
                echo "<img class='$loginErrorIcon' src='images/error_icon.png'><span class='loginError'>$loginErrorMsg</span>";
            ?>

        <!-- New Member Div -->
        <!-- Disable this section since this function cannot be implemented yet -->
            <div id="newMemberDiv">
                <form id="newMemberForm" method="POST" action="#">
                    <h1>Welcome to Watermelon</h1>
                    <p><input class="signUpField" type="text" id="signUpFirstName" name="signUpFirstName" placeholder="First Name" disabled></p>
                    <p><input class="signUpField" type="text" id="signUpSurName" name="signUpSurName" placeholder="Surname" disabled></p>
                    <p><input class="signUpField" type="text" id="signUpUsername" name="signUpUsername" placeholder="Username" disabled></p>
                    <p><input class="signUpField" type="password" id="signUpPassword" name="signUpPassword" placeholder="Password" disabled></p>
                    <p><input class="signUpButton" type="submit" value="Sign Up!" name="signUpButton" disabled></p>
                    <p id="signUpError">You cannot sign up here, please contact the site administrator</p>
                </form>
            </div>

        </div>
    </body>
</html>