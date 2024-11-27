<?php
session_start();
include('header.php'); // Include database connection

// Query to get data for Total Candidates, Total Voters, Ongoing Elections, and Pending Approvals
$queryDashboardData = "
    SELECT 
        (SELECT COUNT(*) FROM candidates) AS totalCandidates,
        (SELECT COUNT(*) FROM students WHERE regStatus = 'approve') AS totalVoters,
        (SELECT COUNT(*) FROM election WHERE electionStatus = 'active') AS ongoingElections,
        (SELECT COUNT(*) FROM students WHERE regStatus = 'pending') AS pendingApprovals
";
$resultDashboardData = mysqli_query($connect, $queryDashboardData);

if ($resultDashboardData && mysqli_num_rows($resultDashboardData) > 0) {
    $data = mysqli_fetch_assoc($resultDashboardData);
    $totalCandidates = $data['totalCandidates'];
    $totalVoters = $data['totalVoters'];
    $ongoingElections = $data['ongoingElections'];
    $pendingApprovals = $data['pendingApprovals'];
} else {
    // Default values if no data is found
    $totalCandidates = $totalVoters = $ongoingElections = $pendingApprovals = 0;
}

// Initialize variables for chart data
$candidateNames = [];
$voteCounts = [];
$electionStatus = '';

// Get the election ID and status for the ongoing election
$queryElection = "SELECT electionID, title, electionStatus FROM election WHERE electionStatus IN ('active', 'completed', 'upcoming') LIMIT 1";
$resultElection = mysqli_query($connect, $queryElection);

if ($resultElection && mysqli_num_rows($resultElection) > 0) {
    $electionData = mysqli_fetch_assoc($resultElection);
    $electionID = $electionData['electionID'];
    $electionName = $electionData['title'];
    $electionStatus = $electionData['electionStatus'];

    if ($electionStatus == 'active') {
        // Query to get each candidate's vote count in the ongoing election
        $queryResults = "
            SELECT c.candidateName, COUNT(v.voteID) AS voteCount
            FROM candidates c
            LEFT JOIN votes v ON c.candidateID = v.candidateID
            WHERE c.electionID = '$electionID'
            GROUP BY c.candidateID
            ORDER BY voteCount DESC
        ";
        $resultResults = mysqli_query($connect, $queryResults);

        if ($resultResults && mysqli_num_rows($resultResults) > 0) {
            while ($row = mysqli_fetch_assoc($resultResults)) {
                $candidateNames[] = $row['candidateName'];
                $voteCounts[] = $row['voteCount'];
            }
        } else {
            $candidateNames = ["No candidates"];
            $voteCounts = [0];
        }
    } else if ($electionStatus == 'completed') {
        // Query to get vote count for completed election
        $queryResults = "
            SELECT c.candidateName, COUNT(v.voteID) AS voteCount
            FROM candidates c
            LEFT JOIN votes v ON c.candidateID = v.candidateID
            WHERE c.electionID = '$electionID'
            GROUP BY c.candidateID
        ";
        $resultResults = mysqli_query($connect, $queryResults);

        if ($resultResults && mysqli_num_rows($resultResults) > 0) {
            while ($row = mysqli_fetch_assoc($resultResults)) {
                $candidateNames[] = $row['candidateName'];
                $voteCounts[] = $row['voteCount'];
            }
        } else {
            $candidateNames = ["No results"];
            $voteCounts = [0];
        }
    }else if ($electionStatus == 'upcoming') {
        echo "<p>No result - Election is upcoming.</p>";
    }
} else {
    $candidateNames = ["No ongoing election"];
    $voteCounts = [0];
    $electionStatus = 'upcoming';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            color: white;
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
            margin-left: 5px;

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
/* Sub Nav List */
.sub-nav-list {
            list-style: none;
            padding-left: 20px;
            display: none; /* Initially hidden */
            margin-top: 10px;
        }

        .sub-nav-list li {
            padding: 10px 15px;
        }

        .sub-nav-list a {
            color: #2C3E50;
            text-decoration: none;
            font-size: 14px;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sub-nav-list a:hover {
            background-color: #16519E;
            color: white;
        }

        /* Active state for Sub Nav List */
        .nav-list li.active .sub-nav-list {
            display: block; /* Display when the parent is active */
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

        .content-dashboard {
            display: flex;
            gap: 20px;
            justify-content: space-between;
            padding-top: 20px;
        }

        .card {
            background-color: #f4f4f4;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            width: 20%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            cursor: pointer; /* Makes the card clickable */
            transition: background-color 0.3s, transform 0.3s;
        }

        .card:hover {
            background-color: #e1e1e1;
            transform: scale(1.05); /* Slight scale effect on hover */
        }

        .card h3 {
            margin-bottom: 10px;
            color:#2C3E50;
        }

        .card p {
            font-size: 24px;
            font-weight: bold;
            color:#1abc9c;
        }

        .card:focus {
            outline: none;
        }

        /* Remove default link styles */
        .card a {
            text-decoration: none; /* Removes underline */
            color: inherit; /* Inherit the text color */
        }

        /* Results Section */
        .results-section {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }

        .results-section h3 {
            font-size: 20px;
            color: #2C3E50;
            margin-bottom: 15px;
        }

    

    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <h1>Student's Election and Voting System (SEVSys)</h1>
        <a href="commiteeLogin.php">Sign Out</a>
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
            <li class="nav-item">
            <a href="javascript:void(0)" onclick="toggleSubNav()"> <i class="fa-solid fa-square-check fa-lg"></i> Verification</a>
            <!-- Sub Nav List -->
            <ul class="sub-nav-list">
                <li><a href="approval.php">Student Approval</a></li>
                <li><a href="reviewCandidate.php">Candidate Approval</a></li>
                <li><a href="admin_review_manifesto.php">Manifesto Approval</a></li>
            </ul>
        </li>
            </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <header>
            <h1>Welcome, Admin</h1>
            <p>Here’s a summary of the ongoing election process.</p>
        </header>
       <div class="content-dashboard">
            <!-- Card for Total Candidates -->
            <div class="card" onclick="window.location.href='candidateManagement.php';">
                <h3>Total Candidates</h3>
                <p><?php echo $totalCandidates; ?></p>
            </div>

            <!-- Card for Total Voters -->
            <div class="card" onclick="window.location.href='studManagement.php';">
                <h3>Total Voters Registered</h3>
                <p><?php echo $totalVoters; ?></p>
            </div>

            <!-- Card for Ongoing Elections -->
            <div class="card" onclick="window.location.href='election.php';">
                <h3>Ongoing Elections</h3>
                <p><?php echo $ongoingElections; ?></p>
            </div>

            <!-- Card for Pending Approvals -->
            <div class="card" onclick="window.location.href='approval.php';">
                <h3>Recent Approvals</h3>
                <p><?php echo $pendingApprovals; ?> Pending</p>
            </div>
        </div>
    
       <!-- Voting Results Section -->
       <div class="results-section">
            <h3>Live Voting Results for Election: <?php echo $electionName; ?></h3>
            <p id="election-status">
                <?php
                if ($electionStatus == 'active') {
                    echo "Ongoing Election - Live Results";
                } elseif ($electionStatus == 'completed') {
                    echo "Election Completed - Total Votes";
                } elseif ($electionStatus == 'upcoming') {
                    echo "No Result- Election is Upcoming";   
                }else {
                    echo "No Election Ongoing or Upcoming";
                }
                ?>
            </p>
            <canvas id="votingResultsChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- JavaScript for sidebar toggle -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            var mainContent = document.getElementById("main-content");

            sidebar.classList.toggle("closed");
            mainContent.classList.toggle("closed");
        }

        function toggleSubNav() {
        // Get the parent li element (which contains the sub-nav)
        var parentNavItem = event.target.closest('.nav-item');
        
        // Toggle the active class on the parent li to show/hide the sub-nav list
        parentNavItem.classList.toggle('active');
       }

        // Data passed from PHP to JavaScript
        const candidateNames = <?php echo json_encode($candidateNames); ?>;
        const voteCounts = <?php echo json_encode($voteCounts); ?>;

        // Chart.js for Live Voting Results
        const ctx = document.getElementById('votingResultsChart').getContext('2d');
        const votingResultsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: candidateNames,
                datasets: [{
                    label: 'Votes Received',
                    data: voteCounts,
                    backgroundColor: ['#1abc9c', '#2ecc71', '#3498db'],
                    borderColor: ['#16a085', '#27ae60', '#2980b9'],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,        // Ensure y-axis starts at 0
                        ticks: {
                            stepSize: 1,          // Set step size to 1 to display whole numbers
                            precision: 0          // Ensure no decimal values are shown
                        }
                    }
                }
            }
        });

    </script>

</body>
</html>
