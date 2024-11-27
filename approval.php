<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management - Campus Election & Voting System</title>
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
            min-height: 100vh;
            transition: padding-left 0.3s ease;
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
        .container {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            min-height: 100vh;
            padding-top: 100px; /* To account for the header */
        }

        .container.closed {
            margin-left: 70px;
            width: calc(100% - 70px);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #16519E;
            color: white;
        }
        button {
            padding: 5px 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button.reject {
            background-color: #dc3545;
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

    <?php
    // Include database connection
    include ("header.php");

   // Handle approval or rejection
if (isset($_GET['action']) && isset($_GET['id'])) {
    $studentId = intval($_GET['id']);
    echo "Action: " . $_GET['action'] . ", ID: " . $studentId; // Debugging output
    if ($_GET['action'] == 'approve') {
        $query = "UPDATE students SET regStatus='approve' WHERE studentID='$studentId'";
        if (mysqli_query($connect, $query)) {
            echo "Student approved successfully."; // Debugging output
        } else {
            echo "Error approving student: " . mysqli_error($connect); // Debugging output
        }
    } elseif ($_GET['action'] == 'reject') {
        $query = "DELETE FROM students WHERE studentID='$studentId'";
        if (mysqli_query($connect, $query)) {
            echo "Student rejected successfully."; // Debugging output
        } else {
            echo "Error rejecting student: " . mysqli_error($connect); // Debugging output
        }
    }
}


    // Query to get pending student registrations
    $query = "SELECT * FROM students WHERE regStatus='pending'";
    $result = mysqli_query($connect, $query);
    ?>

    <div class="container">
        <h2>Student Registration Approval</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Faculty</th>
                <th>Semester</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['studentName']; ?></td>
                <td><?php echo $row['studentEmail']; ?></td>
                <td><?php echo $row['faculty']; ?></td>
                <td><?php echo $row['semester']; ?></td>
                <td><?php echo $row['regStatus']; ?></td>
                <td>
                    <a href="?action=approve&id=<?php echo $row['studentID']; ?>">
                        <button>Approve</button>
                    </a>
                    <a href="?action=reject&id=<?php echo $row['studentID']; ?>">
                        <button class="reject">Reject</button>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <script>
        // Sidebar toggle function
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            var mainContent = document.getElementById("container");
            sidebar.classList.toggle("closed");
            container.classList.toggle("closed");
        }

        function toggleSubNav() {
        // Get the parent li element (which contains the sub-nav)
        var parentNavItem = event.target.closest('.nav-item');
        
        // Toggle the active class on the parent li to show/hide the sub-nav list
        parentNavItem.classList.toggle('active');
       }

    </script>
</body>
</html>
