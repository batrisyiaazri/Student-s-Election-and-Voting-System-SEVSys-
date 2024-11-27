<?php
// Start session
session_start();

// Database connection (replace with your actual connection details)
include('header.php');

// Add Election Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title'])) {
    $title = $_POST['title'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $electionStatus = $_POST['electionStatus'];

    $sql = "INSERT INTO election (title, startDate, endDate, electionStatus) VALUES ('$title', '$startDate', '$endDate', '$electionStatus')";
    if (mysqli_query($connect, $sql)) {
        $_SESSION['message'] = "Election added successfully!";
    } else {
        $_SESSION['message'] = "Error adding election.";
    }
    header("Location: election.php");
    exit();
}

//Update status Logic
if (isset($_POST['updateStatus'])) {
    $electionID = mysqli_real_escape_string($connect, $_POST['electionID']);
    $newStatus = mysqli_real_escape_string($connect, $_POST['electionStatus']);

    $updateQuery = "UPDATE election SET electionStatus = '$newStatus' WHERE electionID = '$electionID'";
    if (mysqli_query($connect, $updateQuery)) {
        echo "<p style='color: green;'>Election status updated successfully to '$newStatus'.</p>";
        header("Location: ".$_SERVER['PHP_SELF']); // Refresh the page
    } else {
        echo "<p style='color: red;'>Error: " . mysqli_error($connect) . "</p>";
    }
}

// Delete Election Logic
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $sql = "DELETE FROM election WHERE electionID = '$id'";
    if (mysqli_query($connect, $sql)) {
        $_SESSION['message'] = "Election deleted successfully!";
    } else {
        $_SESSION['message'] = "Error deleting election.";
    }
    header("Location: election.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Management</title>
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
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
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
            color: white;
            padding-top: 20px;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 60px;
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
            background-color: #f4f7f6;
            padding-left: 20px;
            transition: 0.3s;
            color: #2C3E50;
        }

         /* Sub Nav List */
         .sub-nav-list {
            list-style: none;
            padding-left: 20px;
            display: none; /* Initially hidden */
            margin-top: 10px;
        }

        .sub-nav-list li {
            padding: 10px 15px;
        }

        .sub-nav-list a {
            color: #2C3E50;
            text-decoration: none;
            font-size: 14px;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sub-nav-list a:hover {
            background-color: #16519E;
            color: white;
        }

        /* Active state for Sub Nav List */
        .nav-list li.active .sub-nav-list {
            display: block; /* Display when the parent is active */
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

        /* Main Content Styling */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            min-height: 100vh;
            padding-top: 55px; /* To account for the header */
        }

        .main-content.closed {
            margin-left: 70px;
            width: calc(100% - 70px);
        }

         /* Breadcrumbs Styling */
         .breadcrumbs {
            margin: 20px 0;
            font-size: 14px;
        }

        .breadcrumbs a {
            text-decoration: none;
            color: #3498db;
            margin-right: 10px;
        }

        .breadcrumbs span {
            margin-right: 10px;
            color: #7f8c8d;
        }
        /* Header Styling */
        header {
            background-color: #16519E;
            color: white;
            padding: 20px;
            text-align: left;
            border-radius: 8px;
            margin-top: 5px;
        }

        header h1 {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }

        header p {
            font-size: 14px;
            color: #ecf0f1;
            margin: 0;
        }

        .btn-add-election {
            padding: 10px 15px;
            background-color: #16519E;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 10px;
            margin-top: 10px;
            font-size: 14px;
        }

        /* Message Display */
        .message {
            text-align: center;
            font-size: 14px;
            color: red;
            margin-bottom: 10px;
        }

        .message.success {
            color: green;
        }

        /* Modal Styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Overlay */
            z-index: 9999; /* Make sure modal is on top */
            padding-top: 100px; /* Center vertically */
        }

        .modal-content {
            background-color: white;
            margin: auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 500px;
            position: relative;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            color: #333;
            cursor: pointer;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            color: #34495e;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        .form-group input:focus, .form-group select:focus {
            border-color: #16519e;
        }

        /* Button Styling */
        .btn-submit {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            color: white;
            background-color: #16519e;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-submit:hover {
            background-color: #0d3c72;
        }

        /* Responsive Modal */
        @media (max-width: 768px) {
            .modal-content {
                width: 90%;
                padding: 15px;
            }
            .form-group input, .form-group select {
                padding: 8px;
            }
            .btn-submit {
                padding: 10px;
                font-size: 14px;
            }
        }


        /* Election Item List */
        .election-list {
            margin-top: 20px;
        }

        .election-item {
            background-color: #e8f0fe;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .election-item h3 {
            color: #16519E;
            font-size: 18px;
            margin-bottom: 8px;
        }

        .election-item p {
            color: #34495e;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .btn-edit, .btn-delete, .btn-update-status {
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            color: white;
            margin-right: 5px;
        }

        .btn-edit { background-color: #16519E; }
        .btn-delete { background-color: #d9534f; }
        .btn-update-status { background-color: #28a745; }

        /* Media Queries for Responsiveness */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                padding: 10px 15px;
                align-items: flex-start;
            }

            .header h1 {
                font-size: 18px;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                top: 0;
                display: none; /* Sidebar hidden on mobile */
            }

            .sidebar.open {
                display: block;
                width: 100%;
            }

            .main-content {
                margin-left: 250px;
                padding: 20px;
                width: calc(100% - 250px);
                min-height: 100vh;
                padding-top: 80px; /* To account for the header */
            }
            .main-content.closed {
                margin-left: 70px;
                width: calc(100% - 70px);
            }

            .toggle-btn {
                display: block;
            }

            .nav-list li {
                padding: 10px;
            }

            .nav-list a {
                font-size: 14px;
            }

            .election-item {
                padding: 10px;
            }
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 16px;
            }

            .sidebar {
                display: none;
            }

            .sidebar.open {
                display: block;
                width: 100%;
            }

            .nav-list a {
                font-size: 12px;
            }

            .btn-add-election {
                font-size: 12px;
                padding: 8px 12px;
            }

            .election-item {
                padding: 8px;
            }

            .btn-edit, .btn-delete, .btn-update-status {
                font-size: 12px;
                padding: 4px 8px;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <h1>Student's Election and Voting System (SEVSys)</h1>
        <a href="#logout">Sign Out</a>
    </div>

     <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>Admin</h2>
            <div class="toggle-btn" onclick="toggleSidebar()">&#9776;</div>
        </div>
        <ul class="nav-list">
            <li><a href="adminHomepage.php"><i class="fa-solid fa-house-chimney fa-lg"></i> Dashboard</a></li>
            <li><a href="candidateManagement.php"><i class="fa-solid fa-address-card fa-lg"></i> Candidates</a></li>
            <li><a href="studManagement.php"><i class="fa-solid fa-users fa-lg"></i> Students</a></li>
            <li><a href="election.php"><i class="fa-solid fa-square-poll-vertical fa-xl"></i> Election</a></li>
            <li class="nav-item">
                <a href="javascript:void(0)" onclick="toggleSubNav()"> <i class="fa-solid fa-square-check fa-lg"></i> Verification</a>
                <!-- Sub Nav List -->
                <ul class="sub-nav-list">
                    <li><a href="approval.php">Student Approval</a></li>
                    <li><a href="reviewCandidate.php">Candidate Approval</a></li>
                    <li><a href="admin_review_manifesto.php">Manifesto Approval</a></li>
                </ul>
            </li>
        </ul>
    </div>


    <!-- Main Content -->
    <div class="main-content">
        <!-- Display Message if set -->
        <?php
        if (isset($_SESSION['message'])) {
            echo "<div class='message'>{$_SESSION['message']}</div>";
            unset($_SESSION['message']);
        }
        ?>
        

         <!-- Breadcrumbs -->
         <div class="breadcrumbs">
            <a href="adminHomepage.html">Dashboard</a>
            <span>&gt;</span>
            <span>Manage Election</span>
        </div>
        <!-- Election Management Header -->
    <header>
        <h1>Election Management</h1>
        <p>Manage election process here. You can add, update the statuses or remove election.</p>
    </header>

        <!-- Button to trigger modal -->
        <button class="btn-add-election" onclick="openModal()">Add Election</button>

        <!-- Modal for Adding Election -->
        <!-- Election Modal Form -->
        <div class="modal" id="electionModal">
            <div class="modal-content">
                <span class="close" id="closeModal">&times;</span>
                <h2>Create Election</h2>
                <form method="post">
                    <div class="form-group">
                        <label for="title">Election Title</label>
                        <input type="text" name="title" id="title" required>
                    </div>
                    <div class="form-group">
                        <label for="startDate">Start Date</label>
                        <input type="date" name="startDate" id="startDate" required>
                    </div>
                    <div class="form-group">
                        <label for="endDate">End Date</label>
                        <input type="date" name="endDate" id="endDate" required>
                    </div>
                    
                    <button type="submit" class="btn-submit">Create Election</button>
                </form>
            </div>
        </div>

        <!-- Election List -->
        <div class="election-list">
            <h2>Upcoming Elections</h2>
            <?php
            // Fetch and display elections from the database
            $result = mysqli_query($connect, "SELECT * FROM election");
            while ($row = mysqli_fetch_assoc($result)) {
                echo "
                <div class='election-item'>
                    <h3>{$row['title']}</h3>
                    <p>Start Date: {$row['startDate']}</p>
                    <p>End Date: {$row['endDate']}</p>
                    <p>Status: {$row['electionStatus']}</p>
                    <a href='?update_status_id={$row['electionID']}' class='btn-update-status'>Update Status</a>
                    <a href='?delete_id={$row['electionID']}' class='btn-delete'>Delete</a>
                </div>";

                // Check if update status link was clicked
                if (isset($_GET['update_status_id']) && $_GET['update_status_id'] == $row['electionID']) {
                    echo "<form action='' method='POST'>
                            <input type='hidden' name='electionID' value='{$row['electionID']}'>
                            <label for='electionStatus'>Select New Status:</label>
                            <select name='electionStatus' required>
                                <option value='upcoming'>Upcoming</option>
                                <option value='active'>Active</option>
                                <option value='completed'>Completed</option>
                            </select>
                            <button type='submit' name='updateStatus'>Update Status</button>
                        </form>";
                }
            }
            ?>
        </div>
    </div>

<!-- JavaScript for sidebar toggle -->
<script>
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            var mainContent = document.getElementById("main-content");

            sidebar.classList.toggle("closed");
            mainContent.classList.toggle("closed");
        }

        function toggleSubNav() {
        // Get the parent li element (which contains the sub-nav)
        var parentNavItem = event.target.closest('.nav-item');
        
        // Toggle the active class on the parent li to show/hide the sub-nav list
        parentNavItem.classList.toggle('active');
       }

        // Get modal and button
        const modal = document.getElementById('electionModal');
        const closeModal = document.getElementById('closeModal');

        // Open modal function (triggered by a button or event)
        function openModal() {
            modal.style.display = 'block';
        }

        // Close modal function
        closeModal.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        // Close the modal if the user clicks outside of the modal content
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });

</script>

</body>
</html>
