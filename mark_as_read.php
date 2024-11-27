<!-- mark_as_read.php -->

<?php
include('header.php'); // Database connection

if (isset($_GET['notificationID'])) {
    $notificationID = mysqli_real_escape_string($connect, $_GET['notificationID']);

    // Mark the notification as read
    $query = "UPDATE notifications SET status = 'read' WHERE notificationID = '$notificationID'";

    if (mysqli_query($connect, $query)) {
        echo "Notification marked as read.";
    } else {
        echo "Error marking notification as read: " . mysqli_error($connect);
    }
} else {
    echo "Invalid request.";
}

mysqli_close($connect);
header("Location: guideline.php"); // Redirect back to dashboard
exit();
?>
