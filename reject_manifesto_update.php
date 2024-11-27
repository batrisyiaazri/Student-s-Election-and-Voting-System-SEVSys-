<!-- reject_manifesto_update.php -->

<?php
include('header.php'); // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['candidateID'])) {
    $candidateID = mysqli_real_escape_string($connect, $_POST['candidateID']);

    // Update candidate's status to rejected and reset newManifesto
    $query = "UPDATE candidates 
              SET newManifesto = NULL, 
                  manifestoStatus = 'rejected' 
              WHERE candidateID = $candidateID";
    
    if (mysqli_query($connect, $query)) {
        // Insert a rejection notification for the candidate
        $notificationMessage = "Your manifesto has been rejected. Please revise and resubmit your manifesto.";
        $notificationQuery = "INSERT INTO notifications (candidateID, message) VALUES ('$candidateID', '$notificationMessage')";
        
        if (mysqli_query($connect, $notificationQuery)) {
            echo "Manifesto update rejected. Notification sent to the candidate.";
        } else {
            echo "Error sending notification: " . mysqli_error($connect);
        }
    } else {
        echo "Error rejecting manifesto update: " . mysqli_error($connect);
    }
} else {
    echo "Invalid request.";
}

mysqli_close($connect);
header("Location: admin_review_manifesto.php");
exit();
?>
