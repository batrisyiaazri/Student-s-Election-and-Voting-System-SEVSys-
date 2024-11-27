<?php
session_start();
include("header.php");  // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['studentID'])) {
    $_SESSION['vote_message'] = "You must be logged in to vote.";
    header("Location: studentHomepage.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the student ID, candidate ID, and election ID from the form
    $studentID = $_SESSION['studentID'];  // Assuming student ID is stored in session after login
    $candidateID = mysqli_real_escape_string($connect, $_POST['candidateID']);
    $electionID = mysqli_real_escape_string($connect, $_POST['electionID']);

    // Debugging: Check if the POST data is set
    if (empty($candidateID) || empty($electionID)) {
        $_SESSION['vote_message'] = "Error: Missing candidate or election ID.";
        header("Location: studentHomepage.php");
        exit();
    }

    // Check if the student has already voted in this election
    $queryCheckVote = "SELECT * FROM votes WHERE studentID = '$studentID' AND electionID = '$electionID'";
    $resultCheckVote = mysqli_query($connect, $queryCheckVote);
    
    if (mysqli_num_rows($resultCheckVote) > 0) {
        // The student has already voted in this election
        $_SESSION['vote_message'] = "You have already voted in this election!";
    } else {
        // Proceed with inserting the vote if the student hasn't voted yet
        $queryInsertVote = "INSERT INTO votes (studentID, candidateID, electionID) VALUES ('$studentID', '$candidateID', '$electionID')";
        if (mysqli_query($connect, $queryInsertVote)) {
            // Vote inserted successfully
            $_SESSION['vote_message'] = "Your vote has been successfully submitted!";
        } else {
            // Error inserting vote
            $_SESSION['vote_message'] = "Error: Unable to submit your vote. Please try again later. " . mysqli_error($connect);
        }
    }

    // Redirect to the student homepage after submitting the vote
    header("Location: studentHomepage.php");
    exit();
}
?>
