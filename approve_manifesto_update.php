<!-- approve_manifesto_update.php -->

<?php
include('header.php'); // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['candidateID'])) {
    $candidateID = mysqli_real_escape_string($connect, $_POST['candidateID']);

    // Update the manifesto and reset newManifesto and manifestoStatus
    $query = "UPDATE candidates 
              SET manifesto = newManifesto, 
                  newManifesto = NULL, 
                  manifestoStatus = 'approved' 
              WHERE candidateID = $candidateID";
    
    if (mysqli_query($connect, $query)) {
        echo "Manifesto update approved.";
    } else {
        echo "Error approving manifesto update: " . mysqli_error($connect);
    }
} else {
    echo "Invalid request.";
}

mysqli_close($connect);
header("Location: admin_review_manifesto.php");
exit();
?>
