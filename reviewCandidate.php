<?php
include("header.php");

// Fetch all pending candidates
$query = "SELECT * FROM candidates WHERE cStatus = 'pending'";
$result = mysqli_query($connect, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Candidate Applications</title>
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

        /* Table Container */
        .table-container {
            margin-left: 270px; /* Adjust left margin to avoid overlap with sidebar */
            padding: 20px;
            max-width: 80%; /* Limit the container width */
            margin-top: 80px; /* Ensure container is below the fixed header */
        }

        .table-container h2 {
            margin-bottom: 10px;
            text-align: center;

        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            border: 1px solid #ccc;
        }

        th {
            background-color: #16519E;
            color: white;

        }

        .btn-approve, .btn-reject {
            padding: 8px 16px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 5px;
        }

        .btn-approve {
            background-color: #4CAF50;
        }

        .btn-approve:hover {
            background-color: #45a049;
        }

        .btn-reject {
            background-color: #f44336;
        }

        .btn-reject:hover {
            background-color: #e53935;
        }


    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Student's Election and Voting System (SEVSys)</h1>
        <a href="commiteeLogin.php">Sign Out</a>
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

    <!-- Main Content Area -->
    <div class="table-container">
        <h2>Review Candidate Applications</h2>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Candidate Name</th>
                        <th>Email</th>
                        <th>Manifesto</th>
                        <th>Candidate Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['candidateName']); ?></td>
                            <td><?php echo htmlspecialchars($row['candidateEmail']); ?></td>
                            <td><?php echo htmlspecialchars($row['manifesto']); ?></td>
                            <td><?php echo htmlspecialchars($row['imagePath']); ?></td>
                            <td>
                                <form action="processCandidate.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="candidateID" value="<?php echo $row['candidateID']; ?>">
                                    <button type="submit" name="action" value="approve" class="btn-approve">Approve</button>
                                    <button type="submit" name="action" value="reject" class="btn-reject">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No pending candidate applications found.</p>
        <?php endif; ?>
    </div>

    <script>
        // Sidebar toggle function
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
    </script>
</body>
</html>
