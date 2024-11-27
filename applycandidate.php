<?php
session_start();
include('header.php');  // Make sure to include your database connection file

// Ensure the user is logged in
if (!isset($_SESSION['studentEmail'])) {
    echo "You must be logged in to apply.";
    exit();
}

$studentEmail = $_SESSION['studentEmail'];  // Get studentEmail from session

// Query to fetch student information, including name and email
$queryStudent = "SELECT studentID, studentName, studentEmail, program, cgpa, semester, studentStatus FROM students WHERE studentEmail = '$studentEmail'";
$resultStudent = mysqli_query($connect, $queryStudent);
$student = mysqli_fetch_assoc($resultStudent);

if (!$student) {
    echo "Error: Student not found.";
    exit();
}

$studentID = $student['studentID'];
$studentName = $student['studentName']; // Fetch the name
$program = $student['program'];
$cgpa = $student['cgpa'];
$semester = $student['semester'];
$status = $student['studentStatus'];

// Query to get the electionID for an active election
$query = "SELECT electionID FROM election WHERE electionStatus = 'upcoming' LIMIT 1";
$result = mysqli_query($connect, $query);

$electionID = null; // Default value if no active election is found

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $electionID = $row['electionID']; // Assign the electionID if available
} else {
    echo "<p style='text-align: center; color: red;'>Sorry, Candidate applications are currently closed.</p>";
}

// Check if the student has already applied as a candidate in the current election
if ($electionID !== null) {
    $queryCheckApplication = "SELECT * FROM candidates WHERE studentID = '$studentID' AND electionID = '$electionID'";
    $resultCheckApplication = mysqli_query($connect, $queryCheckApplication);

    if (mysqli_num_rows($resultCheckApplication) > 0) {
        echo "<div class='submitted-message' style='text-align: center; color: #d9534f; font-size: 18px; font-weight: bold;'>
        You have already submitted an application for this election.
      </div>";
    }
}

// Check if form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $electionID !== null) {
    // Criteria checks
    $criteriaMet = true;
    $errorMessage = "";

    // CGPA check based on program
    if ($program == 'Diploma' || $program == 'Bachelor') {
        if ($cgpa < 2.5) {
            $criteriaMet = false;
            $errorMessage .= "Your CGPA must be 2.5 or above.<br>";
        }
    } elseif ($program == 'Professional' && $cgpa < 50) {
        $criteriaMet = false;
        $errorMessage .= "Your CGPA must be 50% or above.<br>";
    }

    // Semester check based on program
    if (($program == 'Diploma' || $program == 'Professional') && ($semester < 2 || $semester > 4)) {
        $criteriaMet = false;
        $errorMessage .= "Only students from semester 2 to 4 are eligible for diploma/professional programs.<br>";
    } elseif ($program == 'Bachelor' && ($semester < 1 || $semester > 6)) {
        $criteriaMet = false;
        $errorMessage .= "Only students from semester 1 to 6 are eligible for degree programs.<br>";
    }

    // Status check
    if ($status !== 'active') {
        $criteriaMet = false;
        $errorMessage .= "You must be an active student.<br>";
    }

    // If all criteria are met, proceed with candidate application
    if ($criteriaMet) {
        // Handle image upload
        if (isset($_FILES['imagePath']) && $_FILES['imagePath']['error'] === UPLOAD_ERR_OK) {
            $imageTmpPath = $_FILES['imagePath']['tmp_name'];
            $imageName = basename($_FILES['imagePath']['name']);
            $uploadDir = 'uploads/';  // Define the directory to store images
            $imagePath = $uploadDir . $imageName;

            // Move the file to the upload directory
            if (move_uploaded_file($imageTmpPath, $imagePath)) {
                $imagePath = mysqli_real_escape_string($connect, $imagePath);  // Escape the image path for database entry
            } else {
                $errorMessage .= "Error uploading the image.<br>";
                $criteriaMet = false;
            }
        } else {
            $errorMessage .= "Please upload an image.<br>";
            $criteriaMet = false;
        }
    
    // If all criteria are met, proceed with candidate application
    if ($criteriaMet) {
        // Get form data
        $candidateName = mysqli_real_escape_string($connect, $_POST['candidateName']);
        $email = mysqli_real_escape_string($connect, $_POST['candidateEmail']);
        $manifesto = mysqli_real_escape_string($connect, $_POST['manifesto']);

        // Insert candidate data into the candidates table
        $query = "INSERT INTO candidates (studentID, candidateName, candidateEmail, manifesto, cStatus, imagePath, electionID) 
                  VALUES ('$studentID', '$candidateName', '$email', '$manifesto', 'pending', '$imagePath', '$electionID')";

        if (mysqli_query($connect, $query)) {
            echo "<p class='success-message'>Candidate application submitted successfully.</p>";
        } else {
            $errorMessage = "Error: " . mysqli_error($connect);
        }
    }
}}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Application Form</title>
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
        /* Form Container */
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
            margin-top: 45px;
        }
        h2 { color: #333; }
        .form-group { margin-bottom: 15px; text-align: left; }
        .form-group label { display: block; color: #555; font-weight: bold; margin-bottom: 5px; }
        .form-group input, .form-group textarea {
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
        .btn-submit:hover { background-color: #0f3a78; }
        .success-message { color: green; font-size: 16px; margin-top: 10px; }

       /* Modal Styles */
       .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: #fefefe;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            width: 80%;
            max-width: 500px;
        }

        .close-modal {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-modal:hover {
            color: #000;
        }

        .error-message {
            color: red;
            font-size: 16px;
            margin-top: 10px;
            line-height: 1.5;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
            <h1>Student's Election and Voting System (SEVSys)</h1>
            <a href="studentLogin.php">Sign Out</a>
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


    <!-- Form only displayed if there's an active election -->
    <?php if ($electionID && mysqli_num_rows($resultCheckApplication) == 0): ?>
        <div class="form-container">
        <h2>Candidate Application Form</h2>
        <form action="applycandidate.php" method="POST" enctype="multipart/form-data">

            <div class="form-group">
                <label for="candidateName">Full Name</label>
                <input type="text" name="candidateName" id="candidateName" value="<?php echo $studentName; ?>" readonly required>
            </div>

            <div class="form-group">
                <label for="candidateEmail">Email</label>
                <input type="email" name="candidateEmail" id="candidateEmail" value="<?php echo $studentEmail; ?>" readonly required>
            </div>

            <div class="form-group">
                <label for="manifesto">Manifesto</label>
                <textarea name="manifesto" id="manifesto" required></textarea>
            </div>

            <div class="form-group">
                <label for="candidateImage">Upload Image</label>
                <input type="file" name="imagePath" id="imagePath" accept="image/*" required>
            </div>

            <!-- Hidden field to store electionID -->
            <input type="hidden" name="electionID" value="<?php echo $electionID; ?>">

            <button type="submit" class="btn-submit">Submit Application</button>
        </form>
    </div>
    <?php endif; ?>

    <!-- Modal for error messages -->
    <div id="errorModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3>Sorry!</h3>
            <p class="error-message"><?php echo $errorMessage; ?></p>
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

         // JavaScript to handle the modal
         document.addEventListener("DOMContentLoaded", function() {
            var modal = document.getElementById("errorModal");
            var closeModal = document.querySelector(".close-modal");

            <?php if (!empty($errorMessage)): ?>
                modal.style.display = "flex";
            <?php endif; ?>

            closeModal.onclick = function() {
                modal.style.display = "none";
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        });
    </script>

</body>
</html>
