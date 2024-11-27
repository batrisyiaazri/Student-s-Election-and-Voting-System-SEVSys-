<?php
include("header.php");
session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($connect, $_POST['studentName']);
    $email = mysqli_real_escape_string($connect, $_POST['studentPassword']);
    $email = mysqli_real_escape_string($connect, $_POST['studentEmail']);
    $faculty = mysqli_real_escape_string($connect, $_POST['faculty']);
    $semester = mysqli_real_escape_string($connect, $_POST['semester']);
    $code = mysqli_real_escape_string($connect, $_POST['programCode']);

    
    $query = "INSERT INTO students (studentID, studentName,studentPassword, studentEmail, studentPassword, faculty, programCode, semester, regStatus) VALUES ('', '$name', '$email', '', '$faculty', '$code', '$semester', 'pending')";
    if (mysqli_query($connect, $query)) {
        if (mysqli_query($connect, $query)) {
            // Set a success message in the session
            $_SESSION['message'] = "New student added successfully.";
            // Redirect back to the student management page
            header("Location: studManagement.php");
            exit(); // Stop executing the script after redirection
        } else {
            // Set an error message in the session
            $_SESSION['error_message'] = "Error: " . mysqli_error($connect);
            // Redirect back to the student management page
            header("Location: studManagement.php");
            exit(); // Stop executing the script after redirection
        }
    }
}
?>
