<?php
$servername = "localhost";
$username = "phpmyadmin";
$password = "rehan123";
$dbname = "smartcallassistant";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT id,contact_name,contact_number FROM tbl_user_contacts where user_id='178'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "id: " . $row["id"]. " - Name: " . $row["contact_name"]. " " . $row["contact_number"]. "<br>";
    }
} else {
    echo "0 results";
}

$query = "select count(name) from tbl_users where email = '{$updateAccObj -> email}' and id <> '{$updateAccObj -> id}'";
$result = $conn->query($query);

echo $result;

$conn->close();
?>