<?php
// Include the database connection
include('header.php');

// Start the session
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user inputs and sanitize them
    $email = mysqli_real_escape_string($connect, $_POST['studentEmail']);
    $password = mysqli_real_escape_string($connect, $_POST['studentPassword']);
    
    // Query to check if the email exists in the students table
    $query = "SELECT studentID, studentPassword FROM students WHERE studentEmail = '$email'";
    $result = mysqli_query($connect, $query);
    
    if (mysqli_num_rows($result) > 0) {
        // Fetch student data
        $student = mysqli_fetch_assoc($result);
        
        // Direct comparison (since you're using plain text passwords)
        if ($password == $student['studentPassword']) {
            // Check if the student is approved as a candidate
            $studentID = $student['studentID'];
            $candidateQuery = "SELECT candidateID, cStatus FROM candidates WHERE studentID = '$studentID'";
            $candidateResult = mysqli_query($connect, $candidateQuery);
            
            if (mysqli_num_rows($candidateResult) > 0) {
                $candidate = mysqli_fetch_assoc($candidateResult);
                
                if ($candidate['cStatus'] == 'approve') {
                    // Set session variables
                    $_SESSION['candidateID'] = $candidate['candidateID'];
                    $_SESSION['studentID'] = $studentID;
                    $_SESSION['studentEmail'] = $email;
                    
                    // Redirect to the candidate profile page
                    header("Location: guideline.php");
                    exit();
                } else {
                    // If candidate is not approved
                    $errorMessage = "Your application is not yet approved.";
                }
            } else {
                $errorMessage = "You are not a candidate.";
            }
        } else {
            $errorMessage = "Invalid password.";
        }
    } else {
        $errorMessage = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Style for login page */
        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f9;
        }
        .login-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 20px;
        }
        .input-field {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .btn-submit {
            width: 100%;
            padding: 10px;
            background-color: #16519E;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-submit:hover {
            background-color: #0f3d7a;
        }
        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Candidate Login</h2>

        <?php if (isset($errorMessage)): ?>
            <div class="error-message"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="email" name="studentEmail" class="input-field" placeholder="Enter your email" required>
            <input type="password" name="studentPassword" class="input-field" placeholder="Enter your password" required>
            <button type="submit" class="btn-submit">Login</button>
        </form>
    </div>

</body>
</html>
