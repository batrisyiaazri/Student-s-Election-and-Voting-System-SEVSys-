<!-- candidate_dashboard.php -->

<?php
session_start();

// Assuming the candidateID is stored in the session after login
if (!isset($_SESSION['candidateID'])) {
    header("Location: candidateLogin.php"); // Redirect to login if candidateID is not set
    exit();
}

// Fetch unread notifications for the logged-in candidate
$candidateID = $_SESSION['candidateID']; // Assuming candidateID is stored in session

include('header.php'); // Database connection


$query = "SELECT notificationID, message, created_at 
          FROM notifications 
          WHERE candidateID = '$candidateID' AND status = 'unread'";
$result = mysqli_query($connect, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Candidate Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Basic styling */
        body { font-family: Arial, sans-serif; }
        .container { max-width: 800px; margin: 50px auto; }
        .notification { background-color: #f9f9f9; padding: 10px; margin: 10px 0; border-radius: 5px; }
        .notification .message { font-size: 16px; }
        .notification .timestamp { font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, Candidate</h1>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <h3>Notifications</h3>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="notification">
                    <p class="message"><?php echo htmlspecialchars($row['message']); ?></p>
                    <p class="timestamp">Received on: <?php echo $row['created_at']; ?></p>
                    <!-- Optionally, mark as read when viewed -->
                    <a href="mark_as_read.php?notificationID=<?php echo $row['notificationID']; ?>">Mark as Read</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No new notifications.</p>
        <?php endif; ?>

        <?php mysqli_close($connect); ?>
    </div>
</body>
</html>
