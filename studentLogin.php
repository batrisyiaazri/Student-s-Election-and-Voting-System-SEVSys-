<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
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
        }

        .login-container {
            background-color: white;
            width: 300px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 20px;
            font-size: 22px;
            color: #34495e;
        }

        .login-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #16519E;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-container button:hover {
            background-color: #0f3a78;
        }

        .login-container .error {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Student Login</h2>

        <?php
        // Include the database connection
        include("header.php");

        // Initialize error variable
        $error = '';

        // Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input fields
    $email = !empty($_POST['studentEmail']) ? mysqli_real_escape_string($connect, trim($_POST['studentEmail'])) : '';
    $password = !empty($_POST['studentPassword']) ? $_POST['studentPassword'] : '';

    if (empty($email)) {
        $error = 'Email is required.';
    } elseif (empty($password)) {
        $error = 'Password is required.';
    } else {
        // Prepare the SQL query
        $query = "SELECT studentPassword, regStatus FROM students WHERE studentEmail = '$email' LIMIT 1";
        $result = mysqli_query($connect, $query);

        // Check if email exists
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            $stored_password = $row['studentPassword'];
            $regStatus = $row['regStatus'];

            // Verify the plain-text password (not recommended for real systems)
            if ($password === $stored_password) {
                // Check registration status
                if ($regStatus === 'approve') {
                    session_start();
                    $_SESSION['studentEmail'] = $email; // or any other identifier
                    header("Location: studentHomepage.php"); // Change to your dashboard
                    exit();
                } else {
                    $error = 'Your account is pending approval. Please contact admin.';
                }
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
        ?>

        <!-- Display error message if login fails -->
        <?php if ($error != ''): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="studentLogin.php" method="post">
            <input type="email" name="studentEmail" placeholder="Student Email" required value="<?php if (isset($_POST['studentEmail'])) echo htmlspecialchars($_POST['studentEmail']); ?>">
            <input type="password" name="studentPassword" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>