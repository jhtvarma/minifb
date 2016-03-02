OUR CHOICE PORTAL
----------------------------------------------------------------------------------------------------------------
This file is part of web-portal developed as part of the AMBITIONBOX internship level-2 problem statement.
This file contains about the implementation and how site works.
----------------------------------------------------------------------------------------------------------------
Personal Details:
Name 	: J.Hari Teja Varma
College : Maharaj Vijayaram Gajapathi Raj College Of Engineering (MVGR-Vizianagaram)
Email	: jhtvarma@live.com
----------------------------------------------------------------------------------------------------------------
Web-Portal Name      : OUR CHOICE
Files Included       : index.php,register.php,user.php,friends.php,logout.php,error.php,README.txt
Server-side Language : PHP
Database			 : MySQL
-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Database Structure:
This site is designed based on three tables USERS,FRIENDS,POSTS

USERS Table: 
It stores the user_id(PRIMARY KEY),username and password(encrypted form).The user_id is unique and AUTO INCREMENT.To store password method md5() is applied on
password and stored result in database.When user registered,his/her details are stored in this table.While Login it checks the entered username and password
in this table.If no of rows retrieved is 1,he/she is a valid user else alerts with message "No User found".
If everything is fine it allows user to go to user page(user.php)

Query:create table users (user_id int AUTO_INCREMENT,primary key(user_id),username varchar(20),password varchar(33));

FRIENDS Table:
It contains one_user_id,two_user_id and status.The PRIMARY KEY is combination of (one_user_id,two_user_id).The one_user_id and two_user_id are foreign keys
references user_id in users table.The status stores the relationship between two users.
1->Accepted or Friends 
0->Request Pending

Query :create table friends(one int,two int,status enum ('0','1') default '0',primary key (one,two),foreign key (one) references users(user_id),foreign key (two) references users(user_id) );

POSTS Table:
It contains update_id(PRIMARY KEY),post,user_id who posted and time when he/she posted or updated.The user_id is foreign key references user_id in users.
The post can have length upto 200 chars as per given in problem statement .The php function time() is used to get time in integer format.
Based on the time created the last 20 posts are displayed in users page.

Query : create table posts(update_id int AUTO_INCREMENT,post varchar(200),user_id_fk int,created int(12),primary key (update_id),foreign key (user_id_fk) references users (user_id));

-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Web-Pages

index.php
The login page contains username,password,submit button and a link to register page.A sessiion is creted and stored user_id in sessions_message.
The java script is used to check username validity and password validity.When user enters his credentials and submitted ,it checks those details in database.
If credentials are valid,the user is redirected to User.php

register.php
It contains username ,password and confirm password text boxesand a link to index page.Java script is used to check whether password and confirm password 
is same and also password constraints.If everything is fine.It inserts username and encrypted password into users table and displays successful message below the form.

user.php
It retrieves user_id from session and displays username from users table.It contains two buttons for friends page and for logout.
It contains a div section to view updates on left side and textarea to post something.First it retrieves all friends id based on status '1' in friends table 
and stores in array.It adds all friends id to sql query and retrieves post from his/her friends based on created or time or last posted in descending order.
The posts are limited to 20.Java script is used to prevent user to post empty string.When he posts ,the web page is reloaded displaying last 20 posts.

friends.php
It contains three sections Friend Requests,Friends,All Users.
Friend Request:
If status to this user_id is '0',it is printed in friend requests with yes or no buttons.If user presses yes it changes status '0'->'1'.If user presses NO then it
removes that entry from friends table.It adds all user_id under friend request to an array named $avoid
Friends:
If status to this user_id with another user is '1',it is displayed in Friends page.It adds all user_id in friend section to an array named $frdsid
All Users:
It displays all users those who are not present in avoid array and friends array or those who in friend request and friends.

logout.php:
It destroys the session and redirects to login page i.e., index.php

error.php:
If any error occurs in above page,it redirects to error page with error message.

-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**********Hosted Online**********

I hosted it in a free hosting site to test with multiple users in internet.Some of my friends created account or registered and used to identify bugs.
Go to this portal and register and login to check my work.

url:http://ourchoice.16mb.com/ambox/index.php
database hosted in: http://www.db4free.net/
