<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Committee Registration - Campus Election & Voting System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 20%;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 100px;
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
        }
        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 90%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
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
    </style>
</head>
<body>

<div class="container">
    <h2>Election Committee Registration</h2>
    <?php
    // Include the database connection
    include("header.php");

    // Initialize error array
    $error = array();

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Validate input fields
        $staff_email = !empty($_POST['staffEmail']) ? mysqli_real_escape_string($connect, trim($_POST['staffEmail'])) : $error[] = 'Staff Email is required.';
        $password = !empty($_POST['staffPassword']) ? mysqli_real_escape_string($connect, trim($_POST['staffPassword'])) : $error[] = 'Password is required.';

        // Check if email already exists in the database
        if (empty($error)) {
            $email_check_query = "SELECT * FROM committee WHERE staffEmail = '$staff_email' LIMIT 1";
            $result = mysqli_query($connect, $email_check_query);
            $user = mysqli_fetch_assoc($result);

            if ($user) {
                // If email already exists
                $error[] = 'This email is already registered.';
            } else {
                // Insert data into the database
                $query = "INSERT INTO committee (staffEmail, staffPassword) VALUES ('$staff_email', '$password')";
                $result = @mysqli_query($connect, $query);

                // Check for successful registration
                if ($result) {
                    echo '<script>alert("Registration successful!"); window.location.href="commiteeLogin.php";</script>';
                    exit();
                } else {
                    echo '<div class="error">Error: ' . mysqli_error($connect) . '</div>';
                }
            }
        }
    }
    ?>

    <form action="commiteeRegistration.php" method="POST" class="registration-form">

        <div class="form-group">
            <label for="staffEmail">Staff Email:</label>
            <input type="email" id="staffEmail" name="staffEmail" required value="<?php if (isset($_POST['staffEmail'])) echo $_POST['staffEmail']; ?>">
        </div>

        <div class="form-group">
            <label for="staffPassword">Password:</label>
            <input type="password" id="staffPassword" name="staffPassword" required>
        </div>

        <button type="submit">Register</button>

        <?php
        // Display errors if there are any
        if (!empty($error)) {
            foreach ($error as $err) {
                echo '<div class="error">' . $err . '</div>';
            }
        }
        ?>
    </form>
</div>

</body>
</html>
