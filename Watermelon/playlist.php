<?php
    /* PHP preprocessing */

    require_once("nocache.php"); // prevent the cache
    session_start(); // start the session

    // check if the user is logged in
    if (!isset($_SESSION["userID"])){
        header("location: logout.php"); // if not, redirect to logout.php
    }

    // check how the user approached this page
    $functionType = "showList"; // bind the functionality here
    if(isset($_GET["trackID"])) {
        $functionType = "addTrack";
    }

    // create a new playlist
    if(isset($_REQUEST["newPlaylistButton"])) {
        require_once("conn.php"); // connect to DBMS, using MySQLi

        // parse the input using the function MySQLi::escape_string
        $newPlaylistName = $dbConn -> escape_string($_POST['newPlaylistField']);

        // create a SQL query to insert a new playlist to the table memberPlaylist
        $queryNewPlaylist = ("INSERT INTO UserPlaylist (userID, `name`)
            VALUES ({$_SESSION['userID']}, '$newPlaylistName');");
        $queryNewPlaylistResult = $dbConn -> query($queryNewPlaylist)
            or die("Problem with query: inserting a new playlist into memberPlaylist | " . $dbConn->error);

        // do not disconnect yet
    }

    // add the track to playlist(s)
    if(isset($_REQUEST["addPlaylistBox"])) {
        require_once("conn.php"); // connect to DBMS, using MySQLi
        $addPlaylistTrackID = $dbConn -> escape_string($_POST['addPlaylistTrackID']); // bind the trackID

        foreach($_POST['addPlaylistBox'] as $addPlaylistID) {

            // parse the input using the function MySQLi::escape_string, just in case
            $addPlaylistID = $dbConn -> escape_string($addPlaylistID);

            // create a SQL query to insert the track to selected playlist(s)
            $queryaddPlaylist = ("INSERT INTO Playlist (playlistID, trackID)
                VALUES ($addPlaylistID, $addPlaylistTrackID);");
            $queryaddPlaylistResult = $dbConn -> query($queryaddPlaylist)
                or die("Problem with query: inserting the track into selected playlist(s) | " . $dbConn->error);
        }

        // do not disconnect yet
    }
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Watermelon</title>
        <link href="https://fonts.googleapis.com/css2?family=Tenali+Ramakrishna&family=Neuton:wght@300;700&family=Ubuntu:wght@500&display=swap" rel="stylesheet">
        <link type="text/css" rel="stylesheet" href="styles.css">
        <script type="text/javascript" src="playlist.js"></script>
    </head>

    <body>
        <header>
        <!-- Navigation Bar -->
            <?php include_once("navbar.php"); ?>
        </header>


        <div class="playlistDiv">

            <?php
                /*
                * * * * Display the user's playlist * * * *
                */
                if($functionType == "showList") {
                    require_once("conn.php"); // connect to DBMS, using MySQLi

                    /* create a SQL query
                    * parse the session value using the function MySQLi::escape_string, just in case */
                    $userID = $dbConn -> escape_string($_SESSION["userID"]);

                    // SQL query - Check the user has at least one playlist
                    $queryPlaylistCheck = ("SELECT UP.playlistID
                        FROM UserPlaylist UP
                        WHERE UP.userID = $userID;");
                    $queryPlaylistCheckResult = $dbConn -> query($queryPlaylistCheck)
                        or die("Problem with query: (display) checking the user has at least one playlist | " . $dbConn->error);

                    // SQL query - Playlist List
                    $dbConn -> query("SET SQL_BIG_SELECTS=1");
                    $queryPlaylistSelect = ("SELECT UP.playlistID
                            , UP.name AS namePlaylist
                            , COUNT(PL.trackID) 'trackCount'
                            , IFNULL(SUM(TR.length), 0) 'totalLength'
                        FROM Track TR
                        -- using RIGHT OUTER JOINs to contain the playlists with no tracks
                        RIGHT OUTER JOIN Playlist PL
                            ON PL.trackID = TR.trackID
                        RIGHT OUTER JOIN UserPlaylist UP
                            ON UP.playlistID = PL.playlistID
                        RIGHT OUTER JOIN `User` US
                            ON UP.userID = US.userID
                        WHERE US.userID = $userID
                        GROUP BY UP.playlistID
                        ORDER BY UP.playlistID ASC
                        LIMIT 200;");
                    $queryPlaylistSelectResult = $dbConn -> query($queryPlaylistSelect)
                        or die("Problem with query: (display) listing the playlists | " . $dbConn->error);
            ?>

        <!-- Header -->
            <div class="playlistDivHeader">
                <img src="images/profile_default.png">
                <?php
                    echo "<p>{$_SESSION['userRealname']}";
                    echo "<br><span class='{$_SESSION['userRank']}'>{$_SESSION['userRank']}</span>";
                ?>
                <br><a class="subHyperlink" href="#" onclick="newPlaylistToggle(event, 'newPlaylistDiv');">+ Create New Playlist</a></p>
            </div>

        <!-- New Playlist Div -->
            <div id="newPlaylistDiv">
                <form id="newPlaylistForm" method="POST" action="playlist.php" onsubmit="return validateForm(this);">
                    <p><input class="newPlaylistField" type="text" id="newPlaylistField" name="newPlaylistField" placeholder="Enter playlist name..."></p>
                    <p><input class="newPlaylistButton" type="submit" value="Create!" name="newPlaylistButton"></p>
                    <p id="newPlaylistError"></p>
                </form>
            </div>
        

            <?php
                if(!($queryPlaylistCheckResult -> num_rows)) { // no playlist found
                    echo "<p class='invalidError'><img class='invalidIcon' src='images/error_icon.png'>You have no playlists</p>";
                    $dbConn->close(); // disconnecting
                }
                else { // display the playlists
            ?>

        <!-- Body -->
            <div class="playlistDivBody">
                <h1>Playlists</h1>
                <table>
                    <?php
                        while ($row = $queryPlaylistSelectResult->fetch_assoc()) {

                        // filter the invalid name of playlist, just in case
                        $playlistName = $row["namePlaylist"];
                        if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\s]{0,29}$/', $playlistName)) { $playlistName = "(invalid name)"; }
                    ?>
                    <tr>
                        <td class="width1"><a href="play.php?playlistID=<?php echo $row["playlistID"]?>"><?php echo $playlistName?></td>
                        <td class="width2"><?php echo $row["trackCount"]?> songs</td>
                        <td class="width3"><?php echo $row["totalLength"]?> mins</td>
                    </tr>
                    <?php } // closing while statement ?>
                </table>
            </div>

            <?php
                $dbConn->close(); // disconnecting
                } // closing the display playlist, playlist found
                } // closing the display playlist, checked with functionType=='showList'

                /*
                * * * * Add the track to selected playlist(s) * * * *
                */
                elseif($functionType == "addTrack") {
                    require_once("conn.php"); // connect to DBMS, using MySQLi

                    // create a SQL query
                    $inputID = $dbConn -> escape_string($_GET["trackID"]);
                    $userID = $dbConn -> escape_string($_SESSION["userID"]);

                    // SQL query - Check the user has at least one playlist
                    $queryPlaylistCheck = ("SELECT UP.playlistID
                        FROM UserPlaylist UP
                        WHERE UP.userID = $userID;");
                    $queryPlaylistCheckResult = $dbConn -> query($queryPlaylistCheck)
                        or die("Problem with query: (addPlaylist) checking the user has at least one playlist | " . $dbConn->error);

                    // SQL query - Track Detail
                    $queryPlaylistTrack = ("SELECT DISTINCT AR.artistID
                            , AL.albumID
                            , AR.name AS nameArtist
                            , AL.name AS nameAlbum
                            , TR.title
                            , TR.length
                            , AL.thumbnail AS thumbnailAlbum
                        FROM Track TR
                        INNER JOIN Album AL
                            ON TR.albumID = AL.albumID
                        INNER JOIN Artist AR
                            ON AL.artistID = AR.artistID
                        WHERE TR.trackID = $inputID;");
                    $queryPlaylistTrackResult = $dbConn -> query($queryPlaylistTrack)
                        or die("Problem with query: (addPlaylist) getting the track details | " . $dbConn->error);

                    // SQL query - Playlist List
                    $dbConn -> query("SET SQL_BIG_SELECTS=1");
                    $queryPlaylistSelect = ("SELECT UP.playlistID
                            , UP.name AS namePlaylist
                            , COUNT(PL.trackID) 'trackCount'
                            , IFNULL(SUM(TR.length), 0) 'totalLength'
                        FROM Track TR
                        -- using RIGHT OUTER JOINs to contain the playlists with no tracks
                        RIGHT OUTER JOIN Playlist PL
                            ON PL.trackID = TR.trackID
                        RIGHT OUTER JOIN UserPlaylist UP
                            ON UP.playlistID = PL.playlistID
                        RIGHT OUTER JOIN `User` US
                            ON UP.userID = US.userID
                        WHERE US.userID = $userID
                        GROUP BY UP.playlistID
                        ORDER BY UP.playlistID ASC
                        LIMIT 200;");
                    $queryPlaylistSelectResult = $dbConn -> query($queryPlaylistSelect)
                        or die("Problem with query: (addPlaylist) listing the playlists | " . $dbConn->error);     


                    if(!($queryPlaylistTrackResult -> num_rows)) { // no results from the trackID, invalid approach
                        echo "<p class='invalidError'><img class='invalidIcon' src='images/error_icon.png'>Invalid approach was detected</p>";
                        $dbConn->close(); // disconnecting
                    }

                    else { // valid approach, allow to proceed
            ?>

        <!-- Header -->
            <div class="playlistDivHeader">
                <?php
                    while ($row = $queryPlaylistTrackResult->fetch_assoc()) {
                        echo "<img src='images/thumbs/albums/{$row['thumbnailAlbum']}'>";
                        echo "<p>{$row['title']}";
                        echo "<br><span class='subText'><a href='play.php?artistID={$row['artistID']}'>{$row['nameArtist']}</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;";
                        echo "<a href='play.php?albumID={$row['albumID']}'>{$row['nameAlbum']}</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;";
                        echo "{$row['length']}</span></p>";
                    }
                ?>
            </div>

            <?php
                if(!($queryPlaylistCheckResult -> num_rows)) { // no playlist found
                    echo "<p class='invalidError'><img class='invalidIcon' src='images/error_icon.png'>You have no playlists - create a new playlist first</p>";
                    $dbConn->close(); // disconnecting
                }
                else { // display the playlists
            ?>

        <!-- Body -->
            <div class="playlistDivBody">
                <form id="addPlaylistForm" method="POST" action="playlist.php">
                    <input type="hidden" id="addPlaylistTrackID" name="addPlaylistTrackID" value="<?php echo $inputID?>">
                    <h2>Save to...<input class="addPlaylistButton" type="submit" value="Done!" name="addPlaylistSubmit"></h2>
                    <div class="pseudoTable">
                        <div class="pseudoTableBody">
                            <?php
                                while ($row = $queryPlaylistSelectResult->fetch_assoc()) {

                                // filter the invalid name of playlist, just in case
                                $playlistName = $row["namePlaylist"];
                                if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\s]{0,29}$/', $playlistName)) { $playlistName = "(invalid name)"; }
                            ?>
                            <div class="pseudoTableRow">
                                <div class="pseudoTableCell1">
                                    <label class="addPlaylistContainer"><span class="label"><?php echo $playlistName?></span>
                                        <input type="checkbox" id="addPlaylistBox" name="addPlaylistBox[]" value="<?php echo $row["playlistID"]?>">
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <div class="pseudoTableCell2"><?php echo $row["trackCount"]?> songs</div>
                                <div class="pseudoTableCell3"><?php echo $row["totalLength"]?> mins</div>
                            </div>
                            <?php } // closing while statement ?>
                        </div>
                    </div>
                </form>
            </div>
                    
            <?php
                $dbConn->close(); // disconnecting
                } // closing the display the playlists
                } // closing the valid approach for adding the track to playlist
                } // closing the adding the track to playlist (checked with $functionType == "addTrack")
            ?>

        </div>
    </body>
</html>