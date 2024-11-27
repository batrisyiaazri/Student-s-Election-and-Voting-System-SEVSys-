<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - Campus Election & Voting System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 40%;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        h2 {
            text-align: center;
            color: #34495e;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            margin-left:10px;
        }
        input[type="text"],
        input[type="password"],
        input[type="email"],
        input[type="number"] {
            width: 90%;
            padding: 10px;
            margin-left: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        select {
            width: 90%;
            padding: 10px;
            margin-left: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 95%;
            padding: 10px;
            margin-left: 10px;
            background-color: #16519E;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0f3a78;
        }
        .error {
            color: red;
            margin-top: 10px;
        }

        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4); /* Black with transparency */
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 4px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Student Registration</h2>
    <?php
    // Include the database connection
    include("header.php");

    // Initialize error array
    $error = array();

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Validate input fields
        $name = !empty($_POST['studentName']) ? mysqli_real_escape_string($connect, trim($_POST['studentName'])) : $error[] = 'Name is required.';
        $email = !empty($_POST['studentEmail']) ? mysqli_real_escape_string($connect, trim($_POST['studentEmail'])) : $error[] = 'Email is required.';
        $password = !empty($_POST['studentPassword']) ? mysqli_real_escape_string($connect, trim($_POST['studentPassword'])) : $error[] = 'Password is required.';
        $faculty = !empty($_POST['faculty']) ? mysqli_real_escape_string($connect, trim($_POST['faculty'])) : $error[] = 'Faculty is required.';
        $program = isset($_POST['program']) ? mysqli_real_escape_string($connect, trim($_POST['program'])) : $error[] = 'Program is required.';
        $code = isset($_POST['programCode']) ? mysqli_real_escape_string($connect, trim($_POST['programCode'])) : $error[] = 'Program Code is required.';
        $cgpa = isset($_POST['cgpa']) ? mysqli_real_escape_string($connect, trim($_POST['cgpa'])) : $error[] = 'CGPA is required.';
        $semester = !empty($_POST['semester']) ? mysqli_real_escape_string($connect, trim($_POST['semester'])) : $error[] = 'Semester is required.';
        $status = isset($_POST['studentStatus']) ? mysqli_real_escape_string($connect, trim($_POST['studentStatus'])) : 'active';

        // Check if email already exists in the database
        if (empty($error)) {
            $emailCheckQuery = "SELECT * FROM students WHERE studentEmail = '$email'";
            $result = mysqli_query($connect, $emailCheckQuery);

            if (mysqli_num_rows($result) > 0) {
                // Email already exists
                $error[] = 'This email is already registered.';
            } else {
                $query = "INSERT INTO students (studentID, studentName, studentEmail, studentPassword, faculty, program, programCode, cgpa, semester, studentStatus, regStatus) 
                          VALUES ('', '$name', '$email', '$password', '$faculty', '$program','$code', '$cgpa', '$semester', '$status', 'pending')";
                $result = @mysqli_query($connect, $query);

                // Check for successful registration
                if ($result) {
                    echo '<script>alert("Registration successful! Please wait for admin approval."); window.location.href="studentLogin.php";</script>';
                    exit();
                } else {
                    echo '<div class="error">Error: ' . mysqli_error($connect) . '</div>';
                }
            }
        }
    }
    ?>

    <form action="studRegistration.php" method="POST" class="registration-form">
        <div class="form-group">
            <label for="studentName">Name:</label>
            <input type="text" id="studentName" name="studentName" required value="<?php if (isset($_POST['studentName'])) echo $_POST['studentName']; ?>">
        </div>

        <div class="form-group">
            <label for="studentEmail">Email:</label>
            <input type="email" id="studentEmail" name="studentEmail" required value="<?php if (isset($_POST['studentEmail'])) echo $_POST['studentEmail']; ?>">
        </div>

        <div class="form-group">
            <label for="studentPassword">Password:</label>
            <input type="password" id="studentPassword" name="studentPassword" required>
        </div>

        <div class="form-group">
            <label for="faculty">Faculty:</label>
            <input type="text" id="faculty" name="faculty" required value="<?php if (isset($_POST['faculty'])) echo $_POST['faculty']; ?>">
        </div>

        <div class="form-group">
            <label for="program">Program:</label>
            <select id="program" name="program" required>
                <option value="Diploma" <?php if (isset($_POST['program']) && $_POST['program'] == 'Diploma') echo 'selected'; ?>>Diploma</option>
                <option value="Degree" <?php if (isset($_POST['program']) && $_POST['program'] == 'Bachelor') echo 'selected'; ?>>Degree</option>
                <option value="Professional" <?php if (isset($_POST['program']) && $_POST['program'] == 'Professional') echo 'selected'; ?>>Professional</option>
            </select>
        </div>

        <div class="form-group">
            <label for="programCode">Program Code:</label>
            <input type="text" id="programCode" name="programCode" required value="<?php if (isset($_POST['programCode'])) echo $_POST['programCode']; ?>"  oninput="convertToUppercase(event)">
        </div>

        <div class="form-group">
            <label for="cgpa">CGPA:</label>
            <input type="number" id="cgpa" name="cgpa" step="0.01" required value="<?php if (isset($_POST['cgpa'])) echo $_POST['cgpa']; ?>">
        </div>

        <div class="form-group">
            <label for="semester">Semester:</label>
            <input type="number" id="semester" name="semester" required value="<?php if (isset($_POST['semester'])) echo $_POST['semester']; ?>" min= 1 max = 8 required>
        </div>
        

        <div class="form-group">
            <label for="studentStatus">Status:</label>
            <select id="studentStatus" name="status">
                <option value="active" <?php if (isset($_POST['studentStatus']) && $_POST['studentStatus'] == 'active') echo 'selected'; ?>>Active</option>
                <option value="inactive" <?php if (isset($_POST['studentStatus']) && $_POST['studentStatus'] == 'inactive') echo 'selected'; ?>>Inactive</option>
            </select>
        </div>

        <button type="submit">Register</button>

        
    </form>
</div>
<!-- Modal -->
<div id="errorModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p id="errorMessage"></p>
    </div>
</div>

<script>
    // JavaScript to show the error modal
    <?php if (!empty($error)): ?>
        document.getElementById('errorModal').style.display = 'block';
        document.getElementById('errorMessage').textContent = '<?php echo implode("<br>", $error); ?>';
    <?php endif; ?>

    // When the user clicks the close button, close the modal
    document.querySelector('.close').onclick = function() {
        document.getElementById('errorModal').style.display = 'none';
    }

    // When the user clicks outside the modal, close it
    window.onclick = function(event) {
        if (event.target == document.getElementById('errorModal')) {
            document.getElementById('errorModal').style.display = 'none';
        }
    }

    function convertToUppercase(event) {
        let inputValue = event.target.value;
        // Convert only alphabetic characters to uppercase, leave numbers as they are
        event.target.value = inputValue.toUpperCase();
    }
</script>
</body>
</html>
