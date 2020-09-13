<?php
    // PHP preprocessing

    require_once("nocache.php"); // prevent the cache
    session_start(); // start the session

    if(isset($_REQUEST["searchSubmit"])) {
        $searchInput = $_POST["searchInput"];
        $searchInputLike = "%{$_POST['searchInput']}%";}
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Watermelon</title>
        <link href="https://fonts.googleapis.com/css2?family=Tenali+Ramakrishna&family=Anton&family=Ubuntu:wght@500&display=swap" rel="stylesheet">
        <link type="text/css" rel="stylesheet" href="styles.css">
        <script type="text/javascript" src="search.js"></script>
    </head>

    <body>
        <header>
        <!-- Navigation Bar -->
            <?php include_once("navbar.php"); ?>
        </header>

    <!-- Search Form -->
        <form id="searchForm" method="POST" action="search.php" onsubmit="return validateForm(this);">
            <div class="searchDiv">

            <!-- Header -->
                <div class="searchDivHeader">
                    <h1>Watermelon</h1>
                </div>

            <!-- Body -->
                <div class="searchDivBody">
                    <label>
                        <img class="searchIcon" src="images/search_icon.png">
                    </label>
                    <input class="searchField" type="text" id="searchInput" name="searchInput" value="<?php if (isset($searchInput)) echo $searchInput;?>">
                    <p><input class="searchButton" type="submit" value="Search!" name="searchSubmit"></p>
                </div>

            </div>
        </form>

    <!-- Search Output -->
        <div class="searchOutput">
            <?php
                if(isset($_REQUEST["searchSubmit"])) {
                    require_once("conn.php"); // connect to DBMS, using MySQLi

                    /* create a SQL query
                    * prevent the SQL injection using prepared statements
                    * 
                    * search matching records using LIKE %%
                    * the system will search artist, album, and tracks in different queries
                    * this might allows the system can provide the separated result sections */

                    // SQL query - artists
                    $querySearchArtist = $dbConn -> prepare("SELECT AR.artistID
                            , AR.name AS nameArtist
                            , AR.thumbnail AS thumbnailArtist
                        FROM Artist AR
                        WHERE AR.name LIKE ?
                        ORDER BY AR.name ASC;");
                    $querySearchArtist -> bind_param("s", $searchInputLike);
                    $querySearchArtist -> execute();
                    $searchResultArtist = $querySearchArtist -> get_result();
                    
                    // SQL query - albums, matching with album name, this has a higher priority on the display
                    $querySearchAlbumHi = $dbConn -> prepare("SELECT AR.artistID
                            , AL.albumID
                            , AR.name AS nameArtist
                            , AL.name AS nameAlbum
                            , AL.thumbnail AS thumbnailAlbum
                        FROM Album AL
                        INNER JOIN Artist AR
                            ON AL.artistID = AR.artistID
                        WHERE AL.name LIKE ?
                        ORDER BY AR.name ASC
                            , AL.albumID ASC;");
                    $querySearchAlbumHi -> bind_param("s", $searchInputLike);
                    $querySearchAlbumHi -> execute();
                    $searchResultAlbumHi = $querySearchAlbumHi -> get_result();

                    // SQL query - albums, matching with artist name, this has a low priority on the display
                    $querySearchAlbumLo = $dbConn -> prepare("SELECT AR.artistID
                            , AL.albumID
                            , AR.name AS nameArtist
                            , AL.name AS nameAlbum
                            , AL.thumbnail AS thumbnailAlbum
                        FROM Album AL
                        INNER JOIN Artist AR
                            ON AL.artistID = AR.artistID
                        WHERE AR.name LIKE ?
                            AND AL.name NOT LIKE ?
                        ORDER BY AR.name ASC
                            , AL.albumID ASC;");
                    $querySearchAlbumLo -> bind_param("ss", $searchInputLike, $searchInputLike);
                    $querySearchAlbumLo -> execute();
                    $searchResultAlbumLo = $querySearchAlbumLo -> get_result();

                    // SQL query - tracks, matching with track title, this has a higher priority on the display
                    $querySearchTrackHi = $dbConn -> prepare("SELECT TR.trackID
                            , AR.artistID
                            , AL.albumID
                            , TR.title
                            , AR.name AS nameArtist
                            , AL.name AS nameAlbum
                        FROM Track TR
                        INNER JOIN Album AL
                            ON TR.albumID = AL.albumID
                        INNER JOIN Artist AR
                            ON AL.artistID = AR.artistID
                        WHERE TR.title LIKE ?
                        ORDER BY AR.name ASC
                            , AL.albumID ASC
                            , TR.trackID ASC;");
                    $querySearchTrackHi -> bind_param("s", $searchInputLike);
                    $querySearchTrackHi -> execute();
                    $searchResultTrackHi = $querySearchTrackHi -> get_result();

                    // SQL query - tracks, matching with artist/album name, this has a lower priority on the display
                    $querySearchTrackLo = $dbConn -> prepare("SELECT TR.trackID
                            , AR.artistID
                            , AL.albumID
                            , TR.title
                            , AR.name AS nameArtist
                            , AL.name AS nameAlbum
                        FROM Track TR
                        INNER JOIN Album AL
                            ON TR.albumID = AL.albumID
                        INNER JOIN Artist AR
                            ON AL.artistID = AR.artistID
                        WHERE AR.name LIKE ?
                            OR AL.name LIKE ?
                            AND TR.title NOT LIKE ?
                        ORDER BY TR.trackID ASC
                        LIMIT 20;");
                    $querySearchTrackLo -> bind_param("sss", $searchInputLike, $searchInputLike, $searchInputLike);
                    $querySearchTrackLo -> execute();
                    $searchResultTrackLo = $querySearchTrackLo -> get_result();


                    if(!($searchResultArtist -> num_rows) && !($searchResultAlbumHi -> num_rows) && !($searchResultAlbumLo -> num_rows) && !($searchResultTrackHi -> num_rows) && !($searchResultTrackLo -> num_rows)) { // no results from the search
                        echo "<p class='searchError'><img class='searchIcon' src='images/error_icon.png'>Your search - <span class='bold'>'$searchInput'</span> - did not match any records</p>";
                        $dbConn->close(); // disconnecting
                    }

                    else { // display the result here

                        if($searchResultArtist -> num_rows) { // the result for artists
            ?>
        <!-- Search Results: Artists -->
            <div class="searchOutputTable">
                <h1>Artists</h1>
                <table>
                    <tr>
                        <th class="widthThumbnail"></th>
                        <th class="widthArtist">Artist</th>
                    </tr>
                    <?php while ($row = $searchResultArtist->fetch_assoc()) { ?>
                    <tr>
                        <td class="widthThumbnail"><?php echo "<img class='thumbnail' src='images/thumbs/artists/{$row['thumbnailArtist']}'>";?></td>
                        <td class="widthArtist"><a href="play.php?artistID=<?php echo $row["artistID"]?>"><?php echo $row["nameArtist"]?></a></td>
                    </tr>
                    <?php } // closing while statement for artists, this should be inside of the table ?>
                </table>
            </div>


            <?php
                } // closing the result for artists

                if(($searchResultAlbumHi -> num_rows) || ($searchResultAlbumLo -> num_rows)) { // the result for albums
            ?>
        <!-- Search Results: Albums -->
            <div class="searchOutputTable">
                <h1>Albums</h1>
                <table>
                    <tr>
                        <th class="widthThumbnail"></th>
                        <th class="widthAlbum1">Album</th>
                        <th class="widthAlbum2">Artist</th>
                    </tr>
                    <?php while ($row = $searchResultAlbumHi->fetch_assoc()) { ?>
                    <tr>
                        <td class="widthThumbnail"><?php echo "<img class='thumbnail' src='images/thumbs/albums/{$row['thumbnailAlbum']}'>";?></td>
                        <td class="widthAlbum1"><a href="play.php?albumID=<?php echo $row["albumID"]?>"><?php echo $row["nameAlbum"]?></a></td>
                        <td class="widthAlbum2"><a href="play.php?artistID=<?php echo $row["artistID"]?>"><?php echo $row["nameArtist"]?></a></td>
                    </tr>
                    <?php } // closing while statement for albums, high priority ?>
                    <?php while ($row = $searchResultAlbumLo->fetch_assoc()) { ?>
                    <tr>
                        <td class="widthThumbnail"><?php echo "<img class='thumbnail' src='images/thumbs/albums/{$row['thumbnailAlbum']}'>";?></td>
                        <td class="widthAlbum1"><a href="play.php?albumID=<?php echo $row["albumID"]?>"><?php echo $row["nameAlbum"]?></a></td>
                        <td class="widthAlbum2"><a href="play.php?artistID=<?php echo $row["artistID"]?>"><?php echo $row["nameArtist"]?></a></td>
                    </tr>
                    <?php } // closing while statement for albums, low priority ?>
                </table>
            </div>


            <?php
                } // closing the result for albums

                if(($searchResultTrackHi -> num_rows) || ($searchResultTrackLo -> num_rows)) { // the result for tracks
            ?>
        <!-- Search Results: Tracks -->
            <div class="searchOutputTable">
                <h1>Tracks</h1>
                <table>
                    <tr>
                        <th class="widthTrack1">Track</th>
                        <th class="widthTrack2">Album</th>
                        <th class="widthTrack3">Artist</th>
                    </tr>
                    <?php while ($row = $searchResultTrackHi->fetch_assoc()) { ?>
                    <tr>
                        <td class="widthTrack1"><a href="play.php?trackID=<?php echo $row["trackID"]?>"><?php echo $row["title"]?></a></td>
                        <td class="widthTrack2"><a href="play.php?albumID=<?php echo $row["albumID"]?>"><?php echo $row["nameAlbum"]?></a></td>
                        <td class="widthTrack3"><a href="play.php?artistID=<?php echo $row["artistID"]?>"><?php echo $row["nameArtist"]?></a></td>
                    </tr>
                    <?php } // closing while statement for tracks, high priority ?>
                    <?php while ($row = $searchResultTrackLo->fetch_assoc()) { ?>
                    <tr>
                        <td class="widthTrack1"><a href="play.php?trackID=<?php echo $row["trackID"]?>"><?php echo $row["title"]?></a></td>
                        <td class="widthTrack2"><a href="play.php?albumID=<?php echo $row["albumID"]?>"><?php echo $row["nameAlbum"]?></a></td>
                        <td class="widthTrack3"><a href="play.php?artistID=<?php echo $row["artistID"]?>"><?php echo $row["nameArtist"]?></a></td>
                    </tr>
                    <?php } // closing while statement for tracks, low priority ?>
                </table>
            </div>


            <?php
                } // closing the result for tracks
                $dbConn->close(); // disconnecting
                } // closing the display, result exists
                } // closing the output, submit button
            ?>


        </div>

    </body>
</html>