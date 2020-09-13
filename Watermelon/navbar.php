<?php
    // PHP preprocessing
    // a session will be already started in main pages

    $userRank = "Guest"; // set a default value as guest, or logged off

    // bind the userRank with session value
    if (isset($_SESSION["userRank"])) {
        $userRank = $_SESSION["userRank"];
    }

?>
<div class="navigationBar">
    <!-- Note: these menu will appear from the right -->
    <?php
        if($userRank == "Guest") { // sign in
            echo "<a href='login.php'>Sign In</a>";
        }
        else { // sign out
            echo "<a href='logout.php'>Sign Out</a>";
        }
    ?>
    <a href="search.php">Search</a>
    <?php
        if($userRank != "Guest") { // is membership
            echo "<a href='playlist.php'>Playlists</a>";
            echo "<p>Welcome, <span class='username'>{$_SESSION['userRealname']}</span> the ";
            echo "<span class='$userRank'>{$_SESSION['userRank']}</span></p>";
        }
    ?>
</div>