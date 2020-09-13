-- generate the tables

CREATE TABLE Artist (
    artistID    INT NOT NULL AUTO_INCREMENT
    , `name`    VARCHAR(200) NOT NULL
    , thumbnail VARCHAR(200) DEFAULT NULL
    
    , PRIMARY KEY(artistID)
);


CREATE TABLE Album (
    albumID         INT NOT NULL AUTO_INCREMENT
    , `name`        VARCHAR(200) NOT NULL
    , releaseYear   YEAR DEFAULT NULL
    , thumbnail     VARCHAR(200) DEFAULT NULL
    , artistID      INT NOT NULL
    
    , PRIMARY KEY(albumID)
    , CONSTRAINT FK_Album_Artist FOREIGN KEY(artistID) REFERENCES Artist(artistID)
);


CREATE TABLE Track (
    trackID         INT NOT NULL AUTO_INCREMENT
    , title         VARCHAR(200) NOT NULL
    , `length`      VARCHAR(6) DEFAULT NULL
    , youtubeAddr   VARCHAR(40) DEFAULT NULL
    , artistID      INT NOT NULL
    , albumID       INT NOT NULL
    
    , PRIMARY KEY(trackID)
    , CONSTRAINT FK_Track_Artist FOREIGN KEY(artistID) REFERENCES Artist(artistID)
    , CONSTRAINT FK_Track_Album FOREIGN KEY(albumID) REFERENCES Album(albumID)
);


CREATE TABLE `User` (
    userID          INT NOT NULL AUTO_INCREMENT
    , username      VARCHAR(100) NOT NULL
    , lName         VARCHAR(50) NOT NULL
    , fName         VARCHAR(50) NOT NULL
    , `password`    VARCHAR(300) NOT NULL
    , category      VARCHAR(10) NOT NULL
    
    , PRIMARY KEY(userID)
);


CREATE TABLE UserPlaylist (
    playlistID  INT NOT NULL AUTO_INCREMENT
    , userID    INT NOT NULL
    , `name`    VARCHAR(30) NOT NULL
    
    , PRIMARY KEY(playlistID)
    , CONSTRAINT FK_UserPlaylist_User FOREIGN KEY(userID) REFERENCES `User`(userID)
);

CREATE TABLE Playlist (
    ID              INT NOT NULL AUTO_INCREMENT
    , playlistID    INT NOT NULL
    , trackID       INT NOT NULL
    
    , PRIMARY KEY(ID)
    , CONSTRAINT FK_Playlist_UserPlaylist FOREIGN KEY(playlistID) REFERENCES UserPlaylist(playlistID)
    , CONSTRAINT FK_Playlist_Track FOREIGN KEY(trackID) REFERENCES Track(trackID)
);