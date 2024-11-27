<!-- submit_manifesto_update.php -->
<?php
session_start();
if (!isset($_SESSION['candidateID'])) {
    header("Location: candidateLogin.php");
    exit();
}

include('header.php');
$candidateID = $_SESSION['candidateID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newManifesto'])) {
    $newManifesto = mysqli_real_escape_string($connect, $_POST['newManifesto']);
    
    // Update the new manifesto and set status to 'submitted'
    $query = "UPDATE candidates SET newManifesto = '$newManifesto', manifestoStatus = 'submitted' WHERE candidateID = $candidateID";
    if (mysqli_query($connect, $query)) {
        header("Location: candidateProfile.php?status=manifesto_submitted");
        exit();
    } else {
        echo "Error submitting manifesto: " . mysqli_error($connect);
    }
} else {
    echo "Invalid submission.";
}

mysqli_close($connect);
?>
