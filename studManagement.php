<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Student Management</title>
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

        /* Modal Overlay */
        .modal-overlay {
            display: none; /* Hidden by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
            z-index: 100; /* Above everything else */
        }

        /* Modal Content */
        .modal-content {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%); /* Center the modal */
            padding: 20px; /* Reduced padding */
            border-radius: 10px; /* Slightly smaller rounded corners */
            width: 450px; /* Smaller modal width */
            background-color: #fff; /* White background */
            z-index: 101; /* Above the overlay */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1); /* Softer shadow for depth */
            transition: all 0.3s ease; /* Smooth transition for appearance */
        }

        .modal-content h2 {
            text-align: center;
            font-size: 22px; /* Slightly smaller title font */
            color: #333; /* Darker title for better contrast */
            margin-bottom: 15px; /* Reduced margin */
        }

        .modal-content label {
            font-size: 14px; /* Smaller font size */
            color: #34495e;
            font-weight: 600;
            margin-bottom: 6px; /* Reduced margin */
            display: block;
        }

        .modal-content input[type="text"],
        .modal-content input[type="email"] {
            width: 100%;
            padding: 8px; /* Reduced padding */
            margin-bottom: 15px; /* Reduced margin */
            border: 1px solid #ddd;
            border-radius: 6px; /* Slightly smaller radius */
            font-size: 14px; /* Smaller input font size */
            transition: all 0.3s ease; /* Smooth transition for focus effect */
        }

        .modal-content input[type="text"]:focus,
        .modal-content input[type="email"]:focus {
            border-color: #16519e; /* Green border on focus */
            outline: none;
        }

        .modal-content .btn {
            width: 100%;
            padding: 10px; /* Smaller padding */
            background-color: #16519E; /* Green background */
            color: white;
            border: none;
            border-radius: 6px; /* Smaller radius */
            font-size: 14px; /* Smaller font size */
            cursor: pointer;
            transition: background-color 0.3s ease; /* Smooth hover effect */
        }

        .modal-content .btn:hover {
            background-color: #0d3c72; /* Darker green on hover */
        }

        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px; /* Smaller close icon */
            font-weight: bold;
            color: #888;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #f44336; /* Red color on hover */
        }

        /* Add animation for modal appearance 
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .modal-content {
            animation: fadeIn 0.3s ease-out;
        }*/


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
            margin-bottom: 40px;
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

        .btn-edit {
            background-color: #f39c12;
        }

        .btn-edit:hover {
            background-color: #e67e22;
        }

        .btn-delete {
        background-color: #e74c3c;
        color: white; /* Add text color for better contrast */
        border: none; /* Remove default border */
        padding: 8px 12px; /* Add padding for a better appearance */
        cursor: pointer; /* Change cursor to pointer */
        border-radius: 4px; /* Rounded corners */
        font-size: 14px; 
        transition: background-color 0.3s; /* Smooth transition for background color */
        }

        .btn-delete:hover {
         background-color: #c0392b; /* Darker red on hover */
        }

        .btn-add {
            margin-bottom: 10px;
            margin-top: 20px;
            background-color: #2ecc71;
        }

        .btn-add:hover {
            background-color: #27ae60;
        }

        .success-message {
           color: #28a745; /* Green for success */
           background-color: #d4edda;
           border: 1px solid #c3e6cb;
           padding: 15px;
           margin: 20px 0;
           border-radius: 5px;
        }

        .error-message {
            color: #dc3545; /* Red for error */
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
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
            <li><a href="#settings"><i class="fa-solid fa-square-check fa-lg"></i> Verification</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <div class="breadcrumbs">
            <a href="#dashboard">Dashboard</a>
            <span>&gt;</span>
            <span>Manage Students</span>
        </div>

        <header>
            <h1>Student Management</h1>
            <p>Manage all student profiles, and faculties. You can add, edit, or remove students and classify by faculty.</p>
        </header>

    

        <!-- Modal Overlay -->
        <div class="modal-overlay" id="modalOverlay"  style="display: none;" ></div>

        <!-- Modal Content -->
        <div class="modal-content" id="modalContent" style="display: none;">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Add Student</h2>
            <form id="addStudentForm" method="POST" action="addStudent.php">
                <label for="studentName">Name:</label>
                <input type="text" id="studentName" name="studentName" required>

                <label for="studentPassword">Password:</label>
                <input type="text" id="studentPassword" name="studentPassword" required>

                <label for="studentEmail">Email:</label>
                <input type="email" id="studentEmail" name="studentEmail" required>

                <label for="faculty">Faculty:</label>
                <input type="text" id="faculty" name="faculty" required>

                <label for="semester">Semester:</label>
                <input type="text" id="semester" name="semester" required>

                <label for="programCode">Program Code:</label>
                <input type="text" id="programCode" name="programCode" required>

                <input type="submit" class="btn" value="Add Student">
            </form>
        </div>


        <!-- Edit Modal -->
       <div class="modal-content" id="editModal" style="display:none;">
       <span class="close" onclick="document.getElementById('editModal').style.display='none'">&times;</span>
       <form id="editForm" method="POST" action="editStudent.php">
        <input type="hidden" name="studentID" id="studentID">
       
        <label for="studentName">Name:</label>
        <input type="text" name="studentName" id="studentName" required>

        <label for="studentEmail">Email:</label>
        <input type="email" name="studentEmail" id="studentEmail" required>

        <label for="faculty">Faculty:</label>
        <input type="text" name="faculty" id="faculty" required>

        <label for="semester">Semester:</label>
        <input type="text" name="semester" id="semester" required>

        <button type="submit">Update</button>
        <button type="button" id="closeModal">Close</button>
       </form>
       </div>




        <?php
        // Include database connection
        include("header.php");
        

        // Query to get approved students
        $faculties = ['FCOM', 'FBASS', 'FESSH', 'IPS']; // Replace these with your actual faculty names
        foreach ($faculties as $faculty) {
            echo "<div class='table-container'>";
            echo "<h2>$faculty</h2>";
            echo "<table><thead><tr><th>Student Email</th><th>Student Name</th><th>Semester</th><th>Program Code</th><th>Actions</th></tr></thead><tbody>";

            // Query to fetch approved students for each faculty
            $query = "SELECT * FROM students WHERE regStatus='approve' AND faculty='$faculty'";
            $result = mysqli_query($connect, $query);

            // Display each approved student
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['studentEmail'] . "</td>";
                    echo "<td>" . $row['studentName'] . "</td>";
                    echo "<td>" . $row['semester'] . "</td>";
                    echo "<td>" . $row['programCode'] . "</td>";
                     // Ensure this field exists in your database
                    //echo "<td>" . ($row['votedStatus'] ? 'Voted' : 'Not Voted') . "</td>"; // Replace with your actual column
                    echo "<td>";
                    
                   


                    // Delete Button
                    echo "<form action='deleteStudent.php' method='POST' style='display:inline;' id='deleteForm" . $row['studentID'] . "'>";
                    echo "<input type='hidden' name='studentID' value='" . $row['studentID'] . "'>";
                    echo "<i class='fa-solid fa-trash fa-lg' style='color: #ff4c4c; cursor: pointer;' 
                          onclick='if(confirm(\"Are you sure you want to delete this student?\")) { document.getElementById(\"deleteForm" . $row['studentID'] . "\").submit(); }'></i>";
                    echo "</form>";

                    echo "</td>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No students found.</td></tr>";
            }
            echo "</tbody></table></div>";
        }

        if (isset($_SESSION['message'])) {
            echo '<div class="success-message">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']); // Clear the message after displaying
        }
        
        if (isset($_SESSION['error_message'])) {
            echo '<div class="error-message">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']); // Clear the message after displaying
        }
    
        ?>
    </div>

    <!-- JavaScript for sidebar toggle -->
    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            var mainContent = document.getElementById("main-content");

            sidebar.classList.toggle("closed");
            mainContent.classList.toggle("closed");
        }

        // Open Modal
        function openModal() {
            document.getElementById('modalOverlay').style.display = 'block';
            document.getElementById('modalContent').style.display = 'block';
        }

        // Close Modal
        function closeModal() {
            document.getElementById('modalOverlay').style.display = 'none';
            document.getElementById('modalContent').style.display = 'none';
        }

        // Add Student Form Submission
        document.getElementById('addStudentForm').addEventListener('Add Student', function(event) {
            event.preventDefault();
            // Add your AJAX code here to submit form data
            closeModal();
        });

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modalOverlay = document.getElementById('modalOverlay');
            if (event.target === modalOverlay) {
                closeModal();
            }
        };
     


    </script>

<script>
    // JavaScript to handle the edit button click
    document.getElementById('editForm').addEventListener('Edit', function(event){
        event.preventDefault();
            // Add your AJAX code here to submit form data
            // Fetch student data
            fetch(`editStudent.php?id=${studentID}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        // Populate the form with student data
                        document.getElementById('studentID').value = data.studentID;
                        document.getElementById('studentName').value = data.studentName;
                        document.getElementById('studentEmail').value = data.studentEmail;
                        document.getElementById('faculty').value = data.faculty;
                        document.getElementById('semester').value = data.semester;

                        // Show the modal
                        document.getElementById('editModal').style.display = 'block';
                    }
                })
                .catch(error => console.error('Error:', error));
            closeModal();
        });
    
</script>
    
</body>
</html>
