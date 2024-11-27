<?php
session_start();
include('header.php'); // Include database connection


// Get the election ID for the ongoing election
$queryElection = "SELECT electionID, title FROM election WHERE electionStatus = 'active' LIMIT 1";
$resultElection = mysqli_query($connect, $queryElection);

if ($resultElection && mysqli_num_rows($resultElection) > 0) {
    $electionData = mysqli_fetch_assoc($resultElection);
    $electionID = $electionData['electionID'];
    $electionName = $electionData['title'];

    echo "<h2>Ongoing Results for Election: $electionName</h2>";

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
        echo "<table border='1'>";
        echo "<tr><th>Candidate Name</th><th>Votes</th></tr>";
        
        // Display each candidate's name and vote count
        while ($row = mysqli_fetch_assoc($resultResults)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['candidateName']) . "</td>";
            echo "<td>" . htmlspecialchars($row['voteCount']) . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No votes have been cast yet.</p>";
    }
} else {
    echo "<p>No ongoing election found.</p>";
}
?>