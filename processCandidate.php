<?php
include("header.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['candidateID'], $_POST['action'])) {
    $candidateID = mysqli_real_escape_string($connect, $_POST['candidateID']);
    $action = $_POST['action'];

    // Determine the new status based on the action
    $status = $action === 'approve' ? 'approve' : 'reject';

    // Update the candidate's status in the database
    $query = "UPDATE candidates SET cStatus = '$status' WHERE candidateID = '$candidateID'";

    if (mysqli_query($connect, $query)) {
        $message = $status === 'approved' ? "Candidate approved successfully." : "Candidate rejected.";
    } else {
        $message = "Error updating candidate status: " . mysqli_error($connect);
    }

    // Redirect back to the reviewCandidate page with a message
    header("Location: reviewCandidate.php?message=" . urlencode($message));
    exit();
}
?>
