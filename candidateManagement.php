<?php
include("header.php");

// Fetch only approved candidates from the database
$query = "SELECT * FROM candidates WHERE cStatus = 'approve'";
$result = mysqli_query($connect, $query);

if (!$result) {
    die("Database query failed: " . mysqli_error($connect));
}

// If form is submitted, add new candidate
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addCandidate'])) {
    $electionID = mysqli_real_escape_string($connect, $_POST['electionID']);
    $candidateName = mysqli_real_escape_string($connect, $_POST['candidateName']);
    $candidateEmail = mysqli_real_escape_string($connect, $_POST['candidateEmail']);
    $cStatus = "approve";

    $addQuery = "INSERT INTO candidates (electionID,candidateName, candidateEmail, cStatus) VALUES ('$electionID','$candidateName', '$candidateEmail', '$cStatus')";
    
    if (mysqli_query($connect, $addQuery)) {
        echo "<script>alert('Candidate added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding candidate: " . mysqli_error($connect) . "');</script>";
    }
}

// If form is submitted, update candidate data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editCandidate'])) {
    $candidateID = mysqli_real_escape_string($connect, $_POST['candidateID']);
    $candidateName = mysqli_real_escape_string($connect, $_POST['candidateName']);
    $candidateEmail = mysqli_real_escape_string($connect, $_POST['candidateEmail']);

    $updateQuery = "UPDATE candidates SET candidateName = '$candidateName', candidateEmail = '$candidateEmail' WHERE candidateID = '$candidateID'";
    
    if (mysqli_query($connect, $updateQuery)) {
        echo "<script>alert('Candidate updated successfully!'); window.location.href = 'candidateManagement.php';</script>";
    } else {
        echo "<script>alert('Error updating candidate: " . mysqli_error($connect) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Candidate Management</title>
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

        /* Main Content Styling */
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

        header {
            background-color: #16519E;
            color: white;
            padding: 20px;
            text-align: left;
            border-radius: 8px;
        }

        header h1 {
            font-size: 24px;
            font-weight: 600;
        }

        header p {
            font-size: 14px;
            color: #ecf0f1;
        }

       /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 20;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.6); /* Darker background overlay */
            padding-top: 60px;
        }

        .modal-content {
            background-color: #ffffff;
            margin: auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 450px;
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .close {
            color: #333;
            float: right;
            font-size: 24px;
            font-weight: bold;
            margin-top: -10px;
            cursor: pointer;
            transition: color 0.3s;
        }

        .close:hover {
            color: #ff5252;
        }

        /* Form Styling */
        .modal-content h2 {
            font-size: 22px;
            color: #1a73e8;
            margin-bottom: 20px;
        }

        .modal-content label {
            display: block;
            font-size: 14px;
            color: #333;
            margin-top: 15px;
            text-align: left;
        }

        .modal-content input[type="text"],
        .modal-content input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .modal-content input[type="text"]:focus,
        .modal-content input[type="email"]:focus {
            border-color: #1a73e8;
            outline: none;
        }

        .btn-add {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: #1a73e8;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
            box-shadow: 0px 4px 8px rgba(26, 115, 232, 0.2);
        }

        .btn-add:hover {
            background-color: #005bb5;
        }


        .table-container {
            margin-top: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            text-align: left;
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #16519E;
            color: white;
        }

        td {
            font-size: 14px;
        }

        /* Button Styling */
        .btn {
            padding: 10px 20px;
            font-size: 14px;
            color: white;
            background-color: #3498db;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #2980b9;
        }

    

        .btn-delete {
            background-color: #e74c3c;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }

        .btn-add {
            margin-top: 20px;
            margin-bottom: 10px;
            background-color: #2ecc71;
        }

        .btn-add:hover {
            background-color: #27ae60;
        }

        /* Footer Styling */
        .footer {
            background-color: #34495e;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
        }

        .footer p {
            font-size: 14px;
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
    <div class="main-content" id="main-content">

        <!-- Breadcrumbs -->
        <div class="breadcrumbs">
            <a href="adminHomepage.html">Dashboard</a>
            <span>&gt;</span>
            <span>Manage Candidates</span>
        </div>

        <header>
            <h1>Candidate Management</h1>
            <p>Manage all candidate profiles. You can add, edit, or remove candidates.</p>
        </header>

       

        <!-- Edit Candidate Modal -->
    <div id="editCandidateModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('editCandidateModal').style.display='none'">&times;</span>
            <h2>Edit Candidate</h2>
            <form method="post" action="candidateManagement.php">
                <input type="hidden" id="editCandidateID" name="candidateID">
                
                <label for="editCandidateName">Candidate Name:</label>
                <input type="text" id="editCandidateName" name="candidateName" required>
                
                <label for="editCandidateEmail">Candidate Email:</label>
                <input type="email" id="editCandidateEmail" name="candidateEmail" required>
                
                <button type="submit" class="btn btn-add" name="editCandidate">Update</button>
            </form>
        </div>
    </div>


        <!-- Candidate Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Candidate Name</th>
                        <th>Candidate Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['candidateName']; ?></td>
                        <td><?php echo $row['candidateEmail']; ?></td>
                        <td>
                            <i class="fa-solid fa-pen-to-square fa-xl" style="color: #f68504; cursor: pointer;margin-left: 20px;" 
                            onclick="openEditModal('<?php echo $row['candidateID']; ?>', '<?php echo addslashes($row['candidateName']); ?>', '<?php echo addslashes($row['candidateEmail']); ?>')">
                            </i>

                            
                            <!-- Other action buttons like delete can go here -->
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
            </table>
        </div>
    </div>



    <script>
        // Sidebar toggle function
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            var mainContent = document.getElementById("main-content");
            sidebar.classList.toggle("closed");
            mainContent.classList.toggle("closed");
        }

         // Close modal when clicking outside
         window.onclick = function(event) {
            var modal = document.getElementById('addCandidateModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        function toggleSubNav() {
        // Get the parent li element (which contains the sub-nav)
        var parentNavItem = event.target.closest('.nav-item');
        
        // Toggle the active class on the parent li to show/hide the sub-nav list
        parentNavItem.classList.toggle('active');
       }

    </script>

    <script>
        function openEditModal(candidateID, candidateName, candidateEmail) {
            document.getElementById('editCandidateID').value = candidateID;
            document.getElementById('editCandidateName').value = candidateName;
            document.getElementById('editCandidateEmail').value = candidateEmail;
            document.getElementById('editCandidateModal').style.display = 'block';
        }
    </script>
</body>
</html>
