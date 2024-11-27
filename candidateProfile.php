<!-- candidateProfile.php -->
<?php
session_start();

// Assuming the candidateID is stored in the session after login
if (!isset($_SESSION['candidateID'])) {
    header("Location: candidateLogin.php"); // Redirect to login if candidateID is not set
    exit();
}

$candidateID = $_SESSION['candidateID'];

// Database connection
include('header.php');

// Fetch the approved candidate profile
$query = "SELECT * FROM candidates WHERE candidateID = $candidateID AND cStatus = 'approve'";
$result = mysqli_query($connect, $query);

if (mysqli_num_rows($result) > 0) {
    $candidate = mysqli_fetch_assoc($result);
} else {
    echo "No approved candidate profile found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Profile</title>
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
            justify-content: center; /* Centers horizontally */
            align-items: center; /* Centers vertically */
            min-height: 100vh; /* Ensures full viewport height */
            flex-direction: column; /* Makes sure content flows vertically */
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

        /* Profile Container */
        .profile-container {
            background-color: #fff;
            width: 100%;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            margin-top: 80px; /* Pushes the profile down below the fixed header */
        }

        .profile-header {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #16519E;
        }

        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            margin: 20px auto;
        }

        .profile-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-info {
            text-align: left;
            margin-top: 20px;
        }

        .profile-info label {
            font-weight: 600;
            color: #16519E;
        }

        .profile-info p {
            margin: 5px 0 15px;
            color: #333;
        }

        .manifesto {
            margin-top: 20px;
        }

        .manifesto textarea {
            width: 100%;
            padding: 10px;
            resize: none;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn-submit {
            background-color: #16519E;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }

        .btn-submit:hover {
            background-color: #123a7d;
        }

    </style>
</head>
<body>

 <!-- Header -->
 <div class="header">
        <h1>Student's Election and Voting System (SEVSys)</h1>
        <a href="candidateLogin.php">Sign Out</a>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>Candidate</h2>
            <div class="toggle-btn" onclick="toggleSidebar()">&#9776;</div>
        </div>
        <ul class="nav-list">
            <li><a href="guideline.php"><i class="fa-solid fa-house-chimney fa-lg"></i> Dashboard</a></li>
            <li><a href="candidateProfile.php"><i class="fa-solid fa-address-card fa-lg"></i> Candidates Profile</a></li>
        </ul>
    </div>

<div class="profile-container">
    <div class="profile-header">Candidate Profile</div>

    <!-- Display Candidate Image -->
    <div class="profile-image">
    <img src="<?php echo !empty($candidate['imagePath']) ? htmlspecialchars($candidate['imagePath']) : ''; ?>" alt="Candidate Image">

</div>


    <!-- Display Candidate Information -->
    <div class="profile-info">
        <label>Full Name:</label>
        <p><?php echo htmlspecialchars($candidate['candidateName']); ?></p>

        <label>Email:</label>
        <p><?php echo htmlspecialchars($candidate['candidateEmail']); ?></p>
    </div>

    <!-- Manifesto Section -->
    <div class="manifesto">
    <form action="submit_manifesto_update.php" method="POST">
        <label for="manifesto">Manifesto</label>
        <textarea name="newManifesto" id="manifesto" rows="5" required><?php echo htmlspecialchars($candidate['manifestoStatus'] === 'submitted' ? $candidate['newManifesto'] : $candidate['manifesto']); ?></textarea>
        <input type="hidden" name="candidateID" value="<?php echo $candidateID; ?>">
        <button type="submit" class="btn-submit">Submit for Approval</button>
    </form>
    </div>
</div>


<script>
        // Sidebar toggle function
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("closed");
            
            }
</script>
</body>
</html>


<?php
// Close database connection
mysqli_close($connect);
?>
