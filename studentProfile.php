<?php
session_start();
include('header.php');  // Database connection

// Ensure the user is logged in
if (!isset($_SESSION['studentEmail'])) {
    echo "You must be logged in to view this page.";
    exit();
}

// Get student email from session
$studentEmail = $_SESSION['studentEmail'];

// Fetch student information from the database
$query = "SELECT studentID, studentName, studentEmail, program, cgpa, semester FROM students WHERE studentEmail = '$studentEmail'";
$result = mysqli_query($connect, $query);
$student = mysqli_fetch_assoc($result);

if (!$student) {
    echo "Error: Student not found.";
    exit();
}

// Handle form submission for updating profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $studentName = mysqli_real_escape_string($connect, $_POST['studentName']);
    $program = mysqli_real_escape_string($connect, $_POST['program']);
    $cgpa = mysqli_real_escape_string($connect, $_POST['cgpa']);
    $semester = mysqli_real_escape_string($connect, $_POST['semester']);

    $updateQuery = "UPDATE students SET studentName = '$studentName', program = '$program', cgpa = '$cgpa', semester = '$semester' WHERE studentEmail = '$studentEmail'";
    
    if (mysqli_query($connect, $updateQuery)) {
        // Set a session variable to indicate success
        $_SESSION['update_success'] = true;
        // Update session data if needed
        $_SESSION['studentName'] = $studentName;
        // Redirect to avoid form resubmission
        header("Location: studentProfile.php");
        exit();
    } else {
        echo "<p class='error-message'>Error updating profile: " . mysqli_error($connect) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        /* CSS code */
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
            height: 100vh;
            margin: 0;
            background-color: #f4f4f9;
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
            margin-right: 5px;

        }

        .nav-list a.active, .nav-list a:hover {
            background-color: #f4f7f6;
            padding-left: 20px;
            transition: 0.3s;
            color: #2C3E50;
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
        .profile-container {
            background-color: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 750px;
            text-align: center;
            margin-top:60px;
            justify-content: center;
            align-items: center;

        }
        h2 {
            padding-top:10px;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group label {
            display: block;
            color: #555;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #16519E;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .btn-submit:hover {
            background-color: #0f3a78;
        }
        .success-message {
            color: green;
            font-size: 16px;
            margin-top: 10px;
        }
        .error-message {
            color: red;
            font-size: 16px;
            margin-top: 10px;
        }

        /* The Modal (background) */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.4); /* Black with opacity */
        }

        /* Modal Content */
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Close Button */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover, .close:focus {
            color: black;
            text-decoration: none;
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
            <h2>Student</h2>
            <div class="toggle-btn" onclick="toggleSidebar()">&#9776;</div>
        </div>
        <ul class="nav-list">
            <li><a href="studentHomepage.php"><i class="fa-solid fa-id-card fa-lg"></i> Candidate Profiles</a></li>
            <li><a href="applycandidate.php"><i class="fa-solid fa-file-lines fa-lg"></i> Apply as Candidate</a></li>
            <li><a href="studentProfile.php"><i class="fa-solid fa-user fa-lg"></i> Profile</a></li>
        </ul>
    </div>

<div class="profile-container">
    <h2>Student Profile</h2>
    <form method="POST" action="studentProfile.php">
        <div class="form-group">
            <label for="studentName">Name:</label>
            <input type="text" id="studentName" name="studentName" value="<?php echo htmlspecialchars($student['studentName']); ?>" required>
        </div>
        <div class="form-group">
            <label for="program">Program:</label>
            <input type="text" id="program" name="program" value="<?php echo htmlspecialchars($student['program']); ?>" required>
        </div>
        <div class="form-group">
            <label for="cgpa">CGPA:</label>
            <input type="number" step="0.01" id="cgpa" name="cgpa" value="<?php echo htmlspecialchars($student['cgpa']); ?>" max="4.00" required>
        </div>
        <div class="form-group">
            <label for="semester">Semester:</label>
            <input type="number" id="semester" name="semester" value="<?php echo htmlspecialchars($student['semester']); ?>" min="1" max="8" required>
        </div>
        <button type="submit" class="btn-submit">Update Profile</button>
    </form>
</div>

<!-- Success Modal -->
<div id="successModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <p>Your profile has been updated successfully!</p>
    </div>
</div>

<script>
// Check for the session variable to trigger modal
document.addEventListener("DOMContentLoaded", function() {
    <?php if (isset($_SESSION['update_success']) && $_SESSION['update_success']) : ?>
        showSuccessModal();
        <?php unset($_SESSION['update_success']); ?>
    <?php endif; ?>
});

function showSuccessModal() {
    var modal = document.getElementById("successModal");
    modal.style.display = "block";
}

function closeModal() {
    var modal = document.getElementById("successModal");
    modal.style.display = "none";
}

window.onclick = function(event) {
    var modal = document.getElementById("successModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
};

function toggleSidebar() {
    var sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("closed");
}
</script>

</body>
</html>
