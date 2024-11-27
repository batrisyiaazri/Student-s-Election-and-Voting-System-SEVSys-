<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Student's Election & Voting System</title>
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

        .container {
            background-color: white;
            width: 300px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .container h2 {
            margin-bottom: 20px;
            font-size: 22px;
            color: #34495e;
        }

        .container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .container button {
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

        .container button:hover {
            background-color: #0f3a78;
        }

        .container .error {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Election Committee Login </h2>
    <?php
    // Include the database connection
    include("header.php");

    // Initialize error array
    $error = array();

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Sanitize and validate input fields
        $staff_email = !empty($_POST['staffEmail']) ? mysqli_real_escape_string($connect, trim($_POST['staffEmail'])) : $error[] = 'Staff Email is required.';
        $password = !empty($_POST['staffPassword']) ? mysqli_real_escape_string($connect, trim($_POST['staffPassword'])) : $error[] = 'Password is required.';

        // If no errors, proceed with login validation
        if (empty($error)) {
            // Query to check if the email exists in the database
            $query = "SELECT * FROM committee WHERE (staffEmail = '$staff_email' AND staffPassword = '$password')";
            $result = mysqli_query($connect, $query);
            

            if (@mysqli_num_rows ($result) == 1){
            //start session, fetch the record and insert the 3 values in an array
            session_start ();
            $_SESSION = mysqli_fetch_array ($result, MYSQLI_ASSOC);

             // Redirect to company profile page
             header("Location: adminHomepage.php");
             exit(); // Cancel the rest of the script
            }
            else {
                // Email not found
                $error[] = 'Incorrect password.';
            }
            
        }
        mysqli_close($connect);
    }
    ?>

    <form action="commiteeLogin.php" method="POST">

            <input type="email" name="staffEmail" placeholder="Staff Email" required value="<?php if (isset($_POST['staffEmail'])) echo htmlspecialchars($_POST['staffEmail']); ?>">
            <input type="password" name="staffPassword" placeholder="Password" required>
            <button type="submit" name="login">Login</button>

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
