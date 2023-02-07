<?php 
/*
    Daniel Zayas 
    CS 415 - Design of Database Systems
    Assignment #10 - Web Application - Due: 4/12/2019, before 4:00 pm
*/

# Script - mysqli_connect.php
// This file contains the database access information. 
// This file also establishes a connection to MySQL 
// and selects the database.

// Set the database access information as constants:
DEFINE ('DB_USER', '*****');
DEFINE ('DB_PASSWORD', '*****');
DEFINE ('DB_HOST', '*****');
DEFINE ('DB_NAME', '*****');

// Make the connection:
$dbc = @mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// If no connection could be made, trigger an error:
if (!$dbc) 
{
	//trigger_error ('Could not connect to MySQL: ' . mysqli_connect_error() );
	die("Connection failed: " . mysqli_connect_error());
} 
else 
{ // Otherwise, set the encoding:
	mysqli_set_charset($dbc, 'utf8');
}