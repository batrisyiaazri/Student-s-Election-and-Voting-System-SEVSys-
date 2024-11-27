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

// Query to get unread notifications for the candidate
$query = "SELECT notificationID, message, created_at 
          FROM notifications 
          WHERE candidateID = '$candidateID' AND status = 'unread'";
$result = mysqli_query($connect, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campaign Guidelines</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
       /* General Reset */
       * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            display: flex;
            justify-content: center; /* Centers horizontally */
            align-items: center; /* Centers vertically */
            min-height: 100vh; /* Ensures full viewport height */
            flex-direction: column; /* Makes sure content flows vertically */
        }

        /* Header Styling */
        .header {
            width: 100%;
            background-color: #16519E;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 30px;
            position: fixed;
            top: 0;
            z-index: 10;
        }

        .header h1 {
            font-size: 22px;
            font-weight: 600;
        }

        .header a {
            color: white;
            text-decoration: none;
            font-size: 16px;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            background-color: #f4f7f6;
            color: #2C3E50;
            padding-top: 20px;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 60px; /* Below header */
            transition: all 0.3s ease;
        }

        .sidebar.closed {
            width: 70px;
        }

        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 10px;
        }

        .sidebar-header h2 {
            font-size: 18px;
            color: #2C3E50;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .nav-list {
            list-style: none;
            padding-left: 0;
        }

        .nav-list li {
            padding: 15px;
        }

        .nav-list a {
            color: #2C3E50;
            text-decoration: none;
            font-size: 16px;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .nav-list a.active, .nav-list a:hover {
            background-color: #16519E;
            padding-left: 20px;
            transition: 0.3s;
            color: white;
        }

        /* Toggle Button */
        .toggle-btn {
            width: 30px;
            height: 30px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f4f7f6;
            color: #2C3E50;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

    

        /* Notification Styling */
        .notification {
            background-color: #f9f9f9;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .notification .message {
            font-size: 16px;
        }

        .notification .timestamp {
            font-size: 12px;
            color: #999;
        }

        .notification a {
            color: #007bff;
            text-decoration: none;
        }

        .notification a:hover {
            text-decoration: underline;
        }

        /* Main Content Styling */
        .content-container {
            margin-left: 250px;
            margin-top: 10px;
            padding: 80px 20px;
            width: calc(100% - 250px);
            transition: all 0.3s ease;
        }

        .sidebar.closed ~ .content-container {
            margin-left: 70px;
            width: calc(100% - 70px);
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            text-align: justify;
        }

        .container ul li {
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .container ul ul {
            padding-left: 20px;
            margin-top: 5px;
            list-style-type: disc;
        }
    </style>
</head>
<body>
    <!-- Header -->
 <div class="header">
        <h1>Student's Election and Voting System (SEVSys)</h1>
        <a href="candidateLogin.php">Sign Out</a>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>Candidate</h2>
            <div class="toggle-btn" onclick="toggleSidebar()">&#9776;</div>
        </div>
        <ul class="nav-list">
            <li><a href="guideline.php"><i class="fa-solid fa-house-chimney fa-lg"></i> Dashboard</a></li>
            <li><a href="candidateProfile.php"><i class="fa-solid fa-address-card fa-lg"></i> Candidates Profile</a></li>

        </ul>
    </div>

    <!-- Main Content -->
    <div class="content-container">
        <!-- Notifications Section -->
        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="notification-section">
                <h3>Unread Notifications</h3>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="notification">
                        <p class="message"><?php echo htmlspecialchars($row['message']); ?></p>
                        <p class="timestamp">Received on: <?php echo $row['created_at']; ?></p>
                        <a href="mark_as_read.php?notificationID=<?php echo $row['notificationID']; ?>">Mark as Read</a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No new notifications.</p>
        <?php endif; ?>

        <!-- Campaign Guidelines Section -->
        <div class="container">
            <h2>Campaign Guidelines</h2>
            <ul>
                <li><strong>Hubungan Dengan Pihak Luar:</strong> Setiap calon atau wakil calon haruslah memastikan bahawa mereka tidak mempunyai apa-apa hubungan dengan pihak luar universiti bersabit dengan Pilihanraya Pemilihan MPP ini.</li>
                <li><strong>Pidato Umum dan Kempen:</strong> Calon atau wakil calon dibenarkan menyampaikan pidato umum bagi memperkenalkan calon-calon yang bertanding di media sosial yang bersesuaian seperti Whatsapp, Instagram, Facebook, Twitter, Youtube, atau laman web khas yang disediakan oleh pihak UPTM. Tempoh berkempen akan ditetapkan oleh Jawatankuasa Pilihan Raya Universiti.
                    <ul>
                        <li>Calon perlu memaklumkan kepada pihak Universiti mengenai butiran kempen sebelum ianya dijalankan.</li>
                        <li>Kempen boleh dilakukan melalui platform media sosial yang telah ditetapkan oleh pihak Universiti.</li>
                        <li>Calon atau wakil calon hanya dibenarkan menyampaikan pidato umum/kempen secara individu untuk menjelaskan visi kempimpinan peribadi ke arah kebajikan mahasiswa.</li>
                        <li>Calon atau wakil calon boleh menerbit, membahagi atau mengedarkan apa-apa dokumen yang berkaitan dengan kempen dalam tempoh kempen yang ditetapkan sahaja.</li>
                    </ul>
                </li>
            </ul>
            <a href="guidelineMPP.pdf" download>Download Full Guidelines</a>
        </div>

        <?php mysqli_close($connect); ?>
    </div>

    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            var contentContainer = document.querySelector(".content-container");
            sidebar.classList.toggle("closed");
            contentContainer.classList.toggle("closed");
        }
    </script>
</body>
</html>
