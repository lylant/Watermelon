# Watermelon

# 1. About the Project
This project is to build a website for music streaming service. This website should allow customers to search and look around artists, albums and titles, also allow them to manage their playlists and edit the contents of the playlists, etc.

## 1.1. Tech-Stacks for the Project
* HTML5
* CSS
* JavaScript
* PHP
* MariaDB

## 1.2. Test-Server System Environments
* Linux Ubuntu 20.04.1
* Apache 2.4.41
* PHP 7.4.3
* MariaDB 10.5.5

## 1.3. Functionalities
* Search with a keyword
* View detail about a specific artist/album/track
* Play a track
* View a track list in a specific playlist
* Add a track to playlists
* Add a playlist
* Authentication

# 2. Project Details

## 2.1. Database Schemas

<p align="center">
    <img src="/_miscs/erd.PNG">
</p>

#### Artist

> This table provides details about artists.

#### Album

> This table provides details about music albums.

#### Track

> This table provides details about songs. The table contains Youtube address for each track for play a Youtube clip.

#### UserPlaylist

> This table provides details about playlists that be created by members.

#### Playlist

> This table records which tracks are added for each playlist.

#### User

> This table provides details about members of Watermelon. It contains their personal information of the member, and login credentials. The passwords are encrypted using the **sha256** algorithm, the plain text passwords will not be saved the table.

#### Role

> This table explains each user role of the website. The role is used for the authorization.

#### UserRole

> This table contains each user's current role.


## 2.2. Security

### 2.2.1. URL Parameter Manipulation

There are several routines that checks if a given parameter comes in, directing an artist, album, track, or playlist is using GET requests to specify the ID. This website can detect some invalid approaches by tampering the URL parameter. For example, a playlist can be accessed by the owner, cannot be accessed by other user or an anonymous. If the invalid approaches are detected, the page will display an error message instead.

<p align="center">
    <img src="/_miscs/security01.PNG">
</p>

### 2.2.2. SQL Injection

There are some web forms to be submitted for executing queries, searching feature and sign in feature are using POST requests to pass over the parameter to the queries. Since there will be a potential threat of SQL Injection, all inputs from the user will be sanitised on the client-side. Also, the prepared statements are used to avoid SQL Injections on the server-side before the queries are executed.

### 2.2.3. Password Encryption

The passwords stored in the **password** field of the **User** table are encrypted using **sha256** algorithm.


## 2.3. Page Functionality

### 2.3.1. Search

<p align="center">
    <img src="/_miscs/search01.PNG">
</p>

The main page of the site allows the user to search Watermelon database for records that matches with the keyword entered in the search form. The script will search the matching records with artist names, album names, and track titles. The result will be generated as three separated lists of matching artists, albums and tracks underneath the search form. The user will be able to access the match if they want to view the details of a specific item.

<p align="center">
    <img src="/_miscs/search02.PNG">
</p>

If there is no record matching the keyword, the page will display an appropriate message underneath the search form.

<p align="center">
    <img src="/_miscs/search03.PNG">
</p>


### 2.3.2. Artist Detail

<p align="center">
    <img src="/_miscs/artist01.PNG">
</p>

The user can view detail information about a specific artist. The page shows the profile image, name of the artist, and a list of all albums by the artist. The list displays a thumbnail, a name, and the year released for each album. The user will be able to access each album if they want to view the details of a specific album.


### 2.3.3. Album Detail

<p align="center">
    <img src="/_miscs/album01.png">
</p>

The user can view detail information about a specific album. The page shows the album cover image, album title, artist name, the year the album was released, and a list of all songs from the album. The list displays a song title, and its length. The user can access each song if they want to view the details of a specific title.


### 2.3.4. Track Detail

<p align="center">
    <img src="/_miscs/track01.png">
</p>

The user can view detail information about a specific track. The page shows the cover image, track title, album title, track length, and a Youtube clip to play the song using the Youtube track address.


### 2.3.5. Playlist

<p align="center">
    <img src="/_miscs/mylist01.png">
</p>

Playlists can only be created and accessed by logged-in users. Playlists belongs to a specific user; logged-in user cannot access any other user's playlist. Anonymous cannot create or access the playlists. Logged-in user can access the page for the members which display the name of the user, the category of the user, and a list of all playlist names. The user can access each playlist from the list.

The web form to create a new playlist is hidden and will appear when the user click the hyperlink, by JavaScript. Once the form is submitted and it is valid, an empty new playlist will be added to the list.

<p align="center">
    <img src="/_miscs/mylist02.png">
</p>

<hr />

The user can view detail contents of a specific playlist. The page shows the owner of the playlist, a list of the tracks in the playlist, the artist name, track title, and song length of each track.

<p align="center">
    <img src="/_miscs/mylist04.png">
</p>

<hr />

The web form to add a track to playlists is hidden to anonymous but only seen by logged-in users. The user can add the song to their playlist(s) by clicking the hyperlink. A number of playlists can be chosen at the same time.

<p align="center">
    <img src="/_miscs/mylist06.PNG">
</p>


### 2.3.6. User Authentication

<p align="center">
    <img src="/_miscs/auth01.PNG">
</p>

The website implemented a basic authentication feature. The sign in page provides a login facility for members of Watermelon. Once the user submits their user credentials on the web form, the page will authenticate the credential by comparing the input values and stored values in the database. If there is a successful login attempt, the site will carry the user's information such as their name and the category by session variables. This session variables are discarded when the user signs out. This authentication process is implemented on the server-side.

The logged-in user can see their name and the category on the left-side of the navigator bar.

<p align="center">
    <img src="/_miscs/auth02.PNG">
</p>


# 3. Installation

## 3.1. Sample Database

You can establish a sample database on the server by executing quries in `/DBquery` folder.

1. `createTables.sql`
2. `insertRecords_Artist.sql`
3. `insertRecords_Album.sql`
4. `insertRecords_Track.sql`
5. `insertRecords_User.sql`
6. `insertRecords_UserPlaylist.sql`
7. `insertRecords_Playlist.sql`
8. `insertRecords_Role.sql`
9. `insertRecords_UserRole.sql`

<hr />

The password stored in the **password** field of the **User** table are encrypted using the **sha256** algorithm. These are the plain text passwords for the sample database records.

Username | Password
---------|----------
admin    |p@ssw0rd
pringles |original
ringfit  |adventure
refresh  |eyedrop


## 3.2. Main Website

Once the database is established, the website can be operational by following steps.

1. Create `conn.php` in the `/Watermelon` folder
2. Modify `conn.php` to invoke connection to the database (see below for the details)
3. Upload all contents in the `Watermelon` folder on the server

<hr />

#### conn.php

> To connect to the database, `conn.php` file with the following PHP code is needed:
```
<?php
   $dbConn = new mysqli("localhost", "USERNAME", "PASSWORD", "DATABASE_NAME");
   if($dbConn->connect_error) {
      die("Failed to connect to database " . $dbConn->connect_error);
   }
?>
```
