<?php
include("header.php");
session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include("header.php"); // Ensure the database connection is included

    $studentID = mysqli_real_escape_string($connect, $_POST['studentID']);
    
    $query = "DELETE FROM students WHERE studentID = '$studentID'";
    
    if (mysqli_query($connect, $query)) {
        // Set a success message in the session
        $_SESSION['message'] = "Student deleted successfully.";
    } else {
        // Set an error message in case of failure
        $_SESSION['error'] = "Error deleting student: " . mysqli_error($connect);
    }
    
    // Redirect back to the student management page
    header("Location: studManagement.php");
    exit(); // Stop executing the script after redirection
}
?>
