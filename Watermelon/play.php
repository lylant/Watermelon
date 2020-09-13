<?php
    /* PHP preprocessing
    * 
    * this page must be approached by hyperlinks from search.php or playlist.php
    * each hyperlink gives ID of artist, album, playlist, or track as a GET request
    * this script will determine that which ID is given in the PHP preprocessing
    */

    require_once("nocache.php"); // prevent the cache
    session_start(); // start the session

    $inputType = "none"; // bind the input type here
    if(isset($_GET["artistID"])) {
        $inputType = "artist";
    }
    elseif(isset($_GET["albumID"])) {
        $inputType = "album";
    }
    elseif(isset($_GET["trackID"])) {
        $inputType = "track";
    }
    elseif(isset($_GET["playlistID"])) {
        $inputType = "playlist";
    }
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Watermelon</title>
        <link href="https://fonts.googleapis.com/css2?family=Tenali+Ramakrishna&family=Neuton:wght@300;700&family=Ubuntu:wght@500&display=swap" rel="stylesheet">
        <link type="text/css" rel="stylesheet" href="styles.css">
    </head>

    <body>
        <header>
        <!-- Navigation Bar -->
            <?php include_once("navbar.php"); ?>
        </header>

        <div class="playDiv">

            <?php
                if($inputType == "none") { // no input, invalid approach
                    echo "<p class='invalidError'><img class='invalidIcon' src='images/error_icon.png'>Invalid approach was detected</p>";
                }

                else {
                    require_once("conn.php"); // connect to DBMS, using MySQLi

                    /*
                    * * * * Artist * * * *
                    */
                    if($inputType == "artist") {
                        /* create a SQL query
                        * parse the input using the function MySQLi::escape_string */
                        $inputID = $dbConn -> escape_string($_GET["artistID"]);

                        // SQL query - header
                        $queryPlayHeader = ("SELECT DISTINCT AR.name AS nameArtist
                                , AR.thumbnail AS thumbnailArtist
                            FROM Artist AR
                            WHERE AR.artistID = $inputID;");
                        $queryPlayHeaderResult = $dbConn -> query($queryPlayHeader)
                            or die("Problem with query: artist header | " . $dbConn->error);

                        // SQL query - body
                        $queryPlayBody = ("SELECT AL.albumID
                                , AL.name AS nameAlbum
                                , AL.releaseYear
                                , AL.thumbnail AS thumbnailAlbum
                            FROM Album AL
                            INNER JOIN Artist AR
                                ON AL.artistID = AR.artistID
                            WHERE AR.artistID = $inputID
                            ORDER BY releaseYear ASC
                                , albumID ASC;");
                        $queryPlayBodyResult = $dbConn -> query($queryPlayBody)
                        or die("Problem with query: artist body | " . $dbConn->error);

                        
                        if(!($queryPlayHeaderResult -> num_rows) || !($queryPlayBodyResult -> num_rows)) { // no results from the ID, invalid approach
                            echo "<p class='invalidError'><img class='invalidIcon' src='images/error_icon.png'>Invalid approach was detected</p>";
                            $dbConn->close(); // disconnecting
                        }

                        else { // valid approach, display the result
            ?>
        <!---- Artist ---->
        <!-- Header -->
            <div class="playDivHeader">
                <?php while ($row = $queryPlayHeaderResult->fetch_assoc()) {
                    echo "<img src='images/thumbs/artists/{$row['thumbnailArtist']}'>";
                    echo "<p>{$row['nameArtist']}</p>";
                }
                ?>
            </div>
        <!-- Body -->
            <div class="playDivBody">
                <h1>Albums</h1>
                <table>
                    <tr>
                        <th class="widthThumbnail"></th>
                        <th class="widthArtist1">Album</th>
                        <th class="widthArtist2">Released</th>
                    </tr>
                    <?php while ($row = $queryPlayBodyResult->fetch_assoc()) { ?>
                    <tr>
                        <td class="widthThumbnail"><?php echo "<img class='thumbnail' src='images/thumbs/albums/{$row['thumbnailAlbum']}'>";?></td>
                        <td class="widthArtist1"><a href="play.php?albumID=<?php echo $row["albumID"]?>"><?php echo $row["nameAlbum"]?></a></td>
                        <td class="widthArtist2"><?php echo $row["releaseYear"]?></td>
                    </tr>
                    <?php } // closing while statement ?>
                </table>
            </div>

            <?php
                $dbConn->close(); // disconnecting
                } // closing the display for artist
                } // closing the query script for artist

                /*
                * * * * Album * * * *
                */
                elseif($inputType == "album") { // result for the album
                    // create a SQL query
                    $inputID = $dbConn -> escape_string($_GET["albumID"]);

                    // SQL query - header
                    $queryPlayHeader = ("SELECT DISTINCT AR.artistID
                            , AL.name AS nameAlbum
                            , AR.name AS nameArtist
                            , AL.releaseYear
                            , AL.thumbnail AS thumbnailAlbum
                        FROM Album AL
                        INNER JOIN Artist AR
                            ON AL.artistID = AR.artistID
                        WHERE AL.albumID = $inputID;");
                    $queryPlayHeaderResult = $dbConn -> query($queryPlayHeader)
                        or die("Problem with query: album header | " . $dbConn->error);

                    // SQL query - body
                    $queryPlayBody = ("SELECT TR.trackID
                            , TR.title
                            , TR.length
                        FROM Track TR
                        INNER JOIN Album AL
                            ON TR.albumID = AL.albumID
                        WHERE AL.albumID = $inputID
                        ORDER BY trackID ASC;");
                    $queryPlayBodyResult = $dbConn -> query($queryPlayBody)
                    or die("Problem with query: album body | " . $dbConn->error);

                    
                    if(!($queryPlayHeaderResult -> num_rows) || !($queryPlayBodyResult -> num_rows)) { // no results from the ID, invalid approach
                        echo "<p class='invalidError'><img class='invalidIcon' src='images/error_icon.png'>Invalid approach was detected</p>";
                        $dbConn->close(); // disconnecting
                    }

                    else { // valid approach, display the result
                ?>
        <!---- Album ---->
        <!-- Header -->
            <div class="playDivHeader">
                    <?php while ($row = $queryPlayHeaderResult->fetch_assoc()) {
                        echo "<img src='images/thumbs/albums/{$row['thumbnailAlbum']}'>";
                        echo "<p>{$row['nameAlbum']}";
                        echo "<br><span class='subText'><a href='play.php?artistID={$row['artistID']}'>{$row['nameArtist']}</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;{$row['releaseYear']}</span></p>";
                    }
                    ?>
            </div>
        <!-- Body -->
            <div class="playDivBody">
                <h1>Tracks</h1>
                <table>
                    <tr>
                        <th class="widthAlbum1">Title</th>
                        <th class="widthAlbum2">Length</th>
                    </tr>
                    <?php while ($row = $queryPlayBodyResult->fetch_assoc()) { ?>
                    <tr>
                        <td class="widthAlbum1"><a href="play.php?trackID=<?php echo $row["trackID"]?>"><?php echo $row["title"]?></a></td>
                        <td class="widthAlbum2"><?php echo $row["length"]?></td>
                    </tr>
                    <?php } // closing while statement ?>
                </table>
            </div>

            <?php
                $dbConn->close(); // disconnecting
                } // closing the display for album
                } // closing the query script for album

                /*
                * * * * Track * * * *
                */
                elseif($inputType == "track") { // result for the track
                    // create a SQL query
                    $inputID = $dbConn -> escape_string($_GET["trackID"]);

                    // SQL query - header
                    $queryPlayHeader = ("SELECT DISTINCT AR.artistID
                            , AL.albumID
                            , AR.name AS nameArtist
                            , AL.name AS nameAlbum
                            , TR.title
                            , TR.length
                            , TR.youtubeAddr
                            , AL.thumbnail AS thumbnailAlbum
                        FROM Track TR
                        INNER JOIN Album AL
                            ON TR.albumID = AL.albumID
                        INNER JOIN Artist AR
                            ON AL.artistID = AR.artistID
                        WHERE TR.trackID = $inputID;");
                    $queryPlayHeaderResult = $dbConn -> query($queryPlayHeader)
                        or die("Problem with query: track header | " . $dbConn->error);
                    
                    if(!($queryPlayHeaderResult -> num_rows)) { // no results from the ID, invalid approach
                        echo "<p class='invalidError'><img class='invalidIcon' src='images/error_icon.png'>Invalid approach was detected</p>";
                        $dbConn->close(); // disconnecting
                    }

                    else { // valid approach, display the result
            ?>
        <!---- Track ---->
        <!-- Header -->
            <div class="playDivHeader">
                <?php
                    while ($row = $queryPlayHeaderResult->fetch_assoc()) {
                        echo "<img src='images/thumbs/albums/{$row['thumbnailAlbum']}'>";
                        echo "<p>{$row['title']}";
                        echo "<br><span class='subText'><a href='play.php?artistID={$row['artistID']}'>{$row['nameArtist']}</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;";
                        echo "<a href='play.php?albumID={$row['albumID']}'>{$row['nameAlbum']}</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;";
                        echo "{$row['length']}</span>";

                        $previewID = $row['youtubeAddr']; // bind the spotify track ID to build a preview
                    }

                    if (isset($_SESSION["userID"])){ // add to playlist function is only shown when the user is logged in
                        echo "<br><a class='subHyperlink' href='playlist.php?trackID=$inputID'>+ Add to Playlist</a></p>";
                    }
                ?>
            </div>
        <!-- Body -->
            <div class="playDivBody">
                <p>
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/<?php echo $previewID?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </p>
            </div>

            <?php
                $dbConn->close(); // disconnecting
                } // closing the display for album
                } // closing the query script for album

                /*
                * * * * Playlist * * * *
                */
                elseif($inputType == "playlist") { // result for the playlist
                    // create a SQL query
                    $inputID = $dbConn -> escape_string($_GET["playlistID"]);

                    if (isset($_SESSION["userID"])){
                        // SQL query - check the direct access
                        $queryPlayCheck = ("SELECT *
                            FROM UserPlaylist UP
                            WHERE UP.playlistID = $inputID
                                AND UP.userID = {$_SESSION['userID']};");
                        $queryPlayCheckResult = $dbConn -> query($queryPlayCheck)
                        or die("Problem with query: playlist validity check | " . $dbConn->error);
                    }

                    // SQL query - header
                    $dbConn -> query("SET SQL_BIG_SELECTS=1");
                    $queryPlayHeader = ("SELECT UP.name AS namePlaylist
                        , COUNT(PL.trackID) 'trackCount'
                        , IFNULL(SUM(TR.length), 0) 'totalLength'
                        FROM Track TR
                        -- using RIGHT OUTER JOINs for the case of empty playlist
                        RIGHT OUTER JOIN Playlist PL
                            ON PL.trackID = TR.trackID
                        RIGHT OUTER JOIN UserPlaylist UP
                            ON UP.playlistID = PL.playlistID
                        WHERE UP.playlistID = $inputID
                        GROUP BY UP.playlistID;");
                    $queryPlayHeaderResult = $dbConn -> query($queryPlayHeader)
                        or die("Problem with query: playlist header | " . $dbConn->error);

                    /* SQL query - body
                    * allowed to display duplicated tracks in the playlist
                    */
                    $queryPlayBody = ("SELECT TR.trackID
                            , AR.artistID
                            , AL.albumID
                            , TR.title
                            , AR.name AS nameArtist
                            , AL.name AS nameAlbum
                            , TR.length
                        FROM Track TR
                        INNER JOIN Artist AR
                            ON TR.artistID = AR.artistID
                        INNER JOIN Album AL
                            ON TR.albumID = AL.albumID
                        INNER JOIN Playlist PL
                            ON TR.trackID = PL.trackID
                        WHERE PL.playlistID = $inputID
                        LIMIT 200;");
                    $queryPlayBodyResult = $dbConn -> query($queryPlayBody)
                    or die("Problem with query: playlist body | " . $dbConn->error);

                    if (!isset($_SESSION["userID"])){ // guest cannot approach playlists
                        echo "<p class='invalidError'><img class='invalidIcon' src='images/error_icon.png'>Invalid approach was detected</p>";
                        $dbConn->close(); // disconnecting
                    }

                    elseif(!($queryPlayHeaderResult -> num_rows)) { // no results from the ID, invalid approach
                        echo "<p class='invalidError'><img class='invalidIcon' src='images/error_icon.png'>Invalid approach was detected</p>";
                        $dbConn->close(); // disconnecting
                    }

                    elseif(!($queryPlayCheckResult -> num_rows)) { // userID and playlistID is not matching, invalid approach
                        echo "<p class='invalidError'><img class='invalidIcon' src='images/error_icon.png'>Invalid approach was detected</p>";
                        $dbConn->close(); // disconnecting
                    }

                    else { // valid approach, display the result
            ?>
        <!---- Playlist ---->
        <!-- Header -->
            <div class="playDivHeader">
                <?php
                    while ($row = $queryPlayHeaderResult->fetch_assoc()) {
                        // filter the invalid name of playlist, just in case
                        $playlistName = $row["namePlaylist"];
                        if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\s]{0,29}$/', $playlistName)) { $playlistName = "(invalid name)"; }

                        echo "<img src='images/playlist_default.png'>";
                        echo "<p>{$playlistName}";
                        echo "<br><span class='subText'><a href='playlist.php'>{$_SESSION['userRealname']}'s Playlist</a>";
                        echo "&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;{$row['trackCount']} songs";
                        echo "&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;{$row['totalLength']} mins</span></p>";
                    }
                ?>
            </div>

            <?php
                if(!($queryPlayBodyResult -> num_rows)) { // empty playlist
                    echo "<p class='invalidError'><img class='invalidIcon' src='images/error_icon.png'>This playlist is empty</p>";
                    $dbConn->close(); // disconnecting
                }
                else { // not empty, display the results
            ?>

        <!-- Body -->
            <div class="playDivBody">
                <h1>Tracks</h1>
                <table>
                    <tr>
                        <th class="widthPlaylist1">Title</th>
                        <th class="widthPlaylist2">Artist</th>
                        <th class="widthPlaylist3">Album</th>
                        <th class="widthPlaylist4">Length</th>
                    </tr>
                    <?php while ($row = $queryPlayBodyResult->fetch_assoc()) { ?>
                    <tr>
                        <td class="widthPlaylist1"><a href="play.php?trackID=<?php echo $row["trackID"]?>"><?php echo $row["title"]?></a></td>
                        <td class="widthPlaylist2"><a href="play.php?artistID=<?php echo $row["artistID"]?>"><?php echo $row["nameArtist"]?></a></td>
                        <td class="widthPlaylist3"><a href="play.php?albumID=<?php echo $row["albumID"]?>"><?php echo $row["nameAlbum"]?></a></td>
                        <td class="widthPlaylist4"><?php echo $row["length"]?></td>
                    </tr>
                    <?php } // closing while statement ?>
                </table>
            </div>

            <?php
                $dbConn->close(); // disconnecting
                } // closing the display for playlist, not empty
                } // closing the display for playlist, entire display
                } // closing the query script for playlist
                } // closing the valid approach, checked with inputType=='none'
            ?>


        </div>
    </body>
</html>