<?php
include("header.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if each parameter is present
    if (isset($_POST['studentID'], $_POST['studentName'], $_POST['studentEmail'], $_POST['faculty'], $_POST['semester'])) {
        $studentID = mysqli_real_escape_string($connect, $_POST['studentID']);
        $name = mysqli_real_escape_string($connect, $_POST['studentName']);
        $email = mysqli_real_escape_string($connect, $_POST['studentEmail']);
        $faculty = mysqli_real_escape_string($connect, $_POST['faculty']);
        $semester = mysqli_real_escape_string($connect, $_POST['semester']);

        $query = "UPDATE students SET studentName='$name', studentEmail='$email', faculty='$faculty', semester='$semester' WHERE studentID='$studentID'";

        if (mysqli_query($connect, $query)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Error updating student details: ' . mysqli_error($connect)]);
        }
    } else {
        // Debug output if parameters are missing
        echo json_encode([
            'error' => 'Missing parameters.',
            'received' => $_POST // Print received POST data for debugging
        ]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}
?>
