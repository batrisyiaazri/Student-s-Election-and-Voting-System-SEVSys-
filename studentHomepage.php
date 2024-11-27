<?php
session_start();
if (!isset($_SESSION['studentEmail'])) {
    // Redirect to login if not logged in
    header("Location: studentLogin.php");
    exit();
}


if (isset($_SESSION['vote_message'])) {
    echo $_SESSION['vote_message'];
    unset($_SESSION['vote_message']);
}

include ("header.php");


// Get the active election ID
$queryElection = "SELECT electionID, electionStatus FROM election WHERE electionStatus IN ('active') LIMIT 1";
$resultElection = mysqli_query($connect, $queryElection);

if ($resultElection && mysqli_num_rows($resultElection) > 0) {
    $rowElection = mysqli_fetch_assoc($resultElection);
    $activeElectionID = $rowElection['electionID'];
    $electionStatus = $rowElection['electionStatus'];
} else {
    $activeElectionID = null;
    $electionStatus = null;
}

// Fetch approved candidates for the active election
$candidates = [];
if ($activeElectionID) {
    $queryCandidates = "SELECT candidateName, candidateEmail, manifesto, imagePath, candidateID FROM candidates WHERE electionID = '$activeElectionID' AND cStatus = 'approve'";
    $resultCandidates = mysqli_query($connect, $queryCandidates);

    if ($resultCandidates && mysqli_num_rows($resultCandidates) > 0) {
        while ($row = mysqli_fetch_assoc($resultCandidates)) {
            $candidates[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Homepage</title>
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
            flex-direction: column;
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
            color: #34495e; 
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
            color: #34495e;
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
            color: #34495e;
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
            color: #34495e;
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
            color: #34495e;
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

         /* Candidate List Styling */
         .candidate-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding-top: 20px;
        }

        .candidate {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            overflow: hidden;
        }

        .candidate:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .candidate img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .candidate-content {
            padding: 10px;
        }

        .candidate h3 {
            font-size: 18px;
            color: #34495e;
            margin-bottom: 5px;
        }

        .candidate p {
            font-size: 14px;
            color: #7f8c8d;
            padding: 10px;
            
        }

        .vote-btn {
            background-color: #16519E;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
            display: inline-block;
        }

        .vote-btn:hover {
            background-color: #0e3e6d;
        }

        .no-candidates {
            color: #34495e;
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            text-align: center;
            grid-column: span 3;
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


    <!-- Main Content -->
    <div class="main-content">
        <header>
            <h1>Candidate Profiles</h1>
            <p>View the details of all selected candidates for the election.</p>
        </header>

        <div class="candidate-list">
    <?php if (!empty($candidates)): ?>
        <?php $index = 1; // Start index from 1 ?>
        <?php foreach ($candidates as $candidate): ?>
            <div class="candidate">
                <!-- Display candidate image or a default placeholder if no image is available -->
                <img src="<?php echo !empty($candidate['imagePath']) ? htmlspecialchars($candidate['imagePath']) : ''; ?>" alt="Candidate Image">
                <div class="candidate-content">
                    <h3><?php echo 'Calon ' . str_pad($index++, 2, '0', STR_PAD_LEFT); ?>: <?php echo htmlspecialchars($candidate['candidateName']); ?></h3>
                    <p>Email: <?php echo htmlspecialchars($candidate['candidateEmail']); ?></p>
                    <p>Manifesto: "<?php echo htmlspecialchars($candidate['manifesto']); ?>"</p>
                    <?php if ($electionStatus == 'active'): ?>
                        <!-- Only show vote button if election is active -->
                        <form action="vote.php" method="POST">
                            <input type="hidden" name="candidateID" value="<?php echo $candidate['candidateID']; ?>">
                            <input type="hidden" name="electionID" value="<?php echo $activeElectionID; ?>">
                            <button type="submit" class="vote-btn">Vote</button>
                        </form>
                    <?php else: ?>
                        <p>Election is <?php echo $electionStatus; ?>. You can only view profiles.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-candidates">No approved candidates for the active election at this time.</p>
    <?php endif; ?>
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
    </script>
</body>
</html>
