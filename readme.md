# Music Streaming application
User have to log into the application with a username and password. Once users are logged into the application, they can browse the list of songs under different genres and listen to their favourite songs. The application will automatically log out users once their login session has exceeded the expiration duration.

<div >
  <img alt="login" src="./login.png" width="400" />
  <img alt="music" src="./music.png" width="400" />
</div>

## Build with
This project was built using these technologies.
- PHP
- AJAX
- MySQL
- CSS
- JavaScript

## Features
### login page
- Users access the application by sending a GET request to /2425B-ASS3/index.php
-  Once the users press enter or click the “Log in” button, the program should send a POST request to /2425B-ASS3/index.php with the message body carries the parameters: username={username} and 
password={password}
- Authentication failure message is generated by PHP code
- Alert is display for missing input
- Created a table in the database (db3322) in our docker container, using MySQL, to store user account information
- Session control (displaying the notification message: "Session expired!!" after a 300 seconds)
- Return a status code of 401 for unauthorized access when there is no active 
session or incorrect access

### music page (active session)
- A button for playing or pausing the music
- The title of the music and the artist
- The total duration of the music (in mm:ss format)
- The licence & attribution icon and the play count
- The list of genres to which the music may be categorized
  
#### music entry
- Users can use the search bar or genre buttons to find music within a specific genre (GET request)
-  All songs are contained within the Music folder, concealed from users to prevent direct access to its file path (POST)
-  update Music database table (play count)
-  music playback functions/events, including play, pause, resume, and end
  
## Docker container
You should create a table in the database (db3322) in our docker container, to store user 
account information. Here is a reference SQL statement in creating the account table.  
    CREATE TABLE `account` ( 
    `id` smallint NOT NULL AUTO_INCREMENT, 
    `username` varchar(60) NOT NULL, 
    `password` varchar(50) NOT NULL, 
    PRIMARY KEY (`id`) 
    ); 

