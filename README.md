# Watermelon

# Introduction

## Project Backgrounds
This project is originally established as an assignment during the study at Western Sydney University. The assignment was not a group assignment, all works implemented in the project is my own work. Most requirements are introduced as the assignment marking rubric but also there are several further improvements after the semester.

The project aims to practice building a operating website without using web frameworks. This is a very simplified web application development and leaves out aspects that would be requirements of a real music streaming web application. The website should allow customers to search and look around artists, albums and titles, also allow them to manage their playlists and edit the contents of the playlists, and several minor functionalities.

## Demo Website Available
* [Link](http://ec2-13-239-62-184.ap-southeast-2.compute.amazonaws.com "Public Demo Website on AWS")

## Main Tech-Stacks for the Project
* HTML
* CSS
* JavaScript
* PHP
* MariaDB


# Architectures

## Software Architecture

<p align="center">
    <img src="/_miscs/architecture_software.PNG">
</p>

## Database Architecture

### Entity-Relationship Diagram

<p align="center">
    <img src="/_miscs/architecture_erd.PNG">
</p>

### Database Schema

#### Artist

Key     | Column  | Datatype   | Nullable | Note
--------|---------|------------|----------|---------
primary |artistID |INT         | NO       |
_       |name     |VARCHAR(200)| NO       |
_       |thumbnail|VARCHAR(200)| YES      |

#### Album

Key     | Column    | Datatype   | Nullable | Note
--------|-----------|------------|----------|---------
primary |albumID    |INT         | NO       |
_       |name       |VARCHAR(200)| NO       |
_       |releaseYear|YEAR        | YES      |
_       |thumbnail  |VARCHAR(200)| YES      |
foreign |artistID   |INT         | NO       | Artist

#### Track

Key     | Column    | Datatype   | Nullable | Note
--------|-----------|------------|----------|---------
primary |trackID    |INT         | NO       |
_       |title      |VARCHAR(200)| NO       |
_       |length     |VARCHAR(6)  | YES      |
_       |youtubeAddr|VARCHAR(40) | YES      |
foreign |artistID   |INT         | NO       | Artist
foreign |albumID    |INT         | NO       | Album


#### User

Key     | Column    | Datatype   | Nullable | Note
--------|-----------|------------|----------|---------
primary |userID     |INT         | NO       |
_       |username   |VARCHAR(100)| NO       |
_       |lName      |VARCHAR(50) | NO       |
_       |fName      |VARCHAR(50) | NO       |
_       |password   |VARCHAR(300)| NO       | encrypted

#### Role

Key     | Column    | Datatype   | Nullable | Note
--------|-----------|------------|----------|---------
primary |roleID     |INT         | NO       |
_       |name       |VARCHAR(10) | NO       |

#### UserRole

Key     | Column    | Datatype   | Nullable | Note
--------|-----------|------------|----------|---------
pri,for |roleID     |INT         | NO       | Role
pri,for |userID     |INT         | NO       | User

#### UserPlaylist

Key     | Column    | Datatype   | Nullable | Note
--------|-----------|------------|----------|---------
primary |playlistID |INT         | NO       |
foreign |userID     |INT         | NO       |
_       |name       |VARCHAR(30) | NO       |

#### Playlist

Key     | Column    | Datatype   | Nullable | Note
--------|-----------|------------|----------|---------
primary |ID         |INT         | NO       |
foreign |playlistID |INT         | NO       | UserPlaylist
foreign |trackID    |INT         | NO       | Track


## Security

### URL Parameter Manipulation

There are several routines that checks if a given parameter comes in, directing an artist, album, track, or playlist is using GET requests to specify the ID. This website can detect some invalid approaches by tampering the URL parameter. For example, a playlist can be accessed by the owner, cannot be accessed by other user or an anonymous. If the invalid approaches are detected, the page will display an error message instead.

### SQL Injection

There are some web forms to be submitted for executing queries, searching feature and sign in feature are using POST requests to pass over the parameter to the queries. Since there will be a potential threat of SQL Injection, all inputs from the user will be sanitised on the client-side. Also, the prepared statements are used to avoid SQL Injections on the server-side before the queries are executed.

### Password Encryption

The passwords stored in the **password** field of the **User** table are encrypted using **sha256** algorithm.


# Installation

## Required Packages
Following packages are required to implement the solution. Please prepare following packages installed on your server before the installation this product. Most recent stable version is recommended. If there is an issue due to the version, try to execute with legacy version. See details of Dev Server Environment below.

* Apache
* PHP 7
* MariaDB

## Dev Server Environment
The product is developed under the environment of:

* Amazon EC2
* Ubuntu Server 20.04 LTS
* Apache 2.4.41
* PHP 7.4.3
* MariaDB 10.5.5

## Installation
As the product is not using any web framework or software, the installation process is simple copy and paste.

1. Create a directory for the product.
2. Copy the product into the new directory.
3. Create and edit `conn.php` file (details on Env Configuration section below)
4. Migrate the database by executing SQL quries in `DBquery/createTables.sql`.
5. (Optional) Populate the database with sample records.


## Database Migration and Sample Records
The initial migration query to construct the essential database are provided with the product files. You can find them in `DBquery/createTables.sql`. Other files in the directory are used to populate the database with sample records. As the product is not using any web framework or software, you need to update the database by using raw SQL queries.

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


## Env Configuration

Once the solution and database are implemented, you need to specify your database credentials in the `conn.php` file. By doing this, your database confidentials will not be exposed to the public. The product will be ready by following the steps.

1. Create `conn.php` in the product root directory.
2. Modify `conn.php` by following instruction below.

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
