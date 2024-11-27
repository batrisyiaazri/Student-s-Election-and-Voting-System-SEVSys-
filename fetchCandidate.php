<style>
    /* Candidate Item Styling */
    .candidate-item {
        display: flex; /* Align items horizontally */
        align-items: center; /* Vertically align checkbox and name */
        margin-bottom: 10px; /* Space between candidate items */
    }

    .candidate-item input[type="checkbox"] {
        margin-right: 10px; /* Space between checkbox and name */
        width: 20px; /* Adjust checkbox size */
        height: 20px; /* Adjust checkbox size */
    }

    .candidate-item label {
        font-size: 16px; /* Adjust font size */
        color: #333; /* Color for the name */
    }

    .candidate-item:hover {
        background-color: #f0f0f0; /* Light background on hover */
        cursor: pointer; /* Change cursor on hover */
    }
</style>
<?php
// Include the database connection
include('header.php');

if (isset($_GET['electionID'])) {
    $electionID = $_GET['electionID'];

    // Fetch candidates (assumes there's a `candidate` table)
    $query = "SELECT * FROM candidates WHERE cStatus = 'approve'"; // Only fetch candidates not yet assigned to any election
    $result = mysqli_query($connect, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "
            <div class='candidate-item'>
                <input type='checkbox' name='candidates[]' value='{$row['candidateID']}' id='candidate_{$row['candidateID']}'>
                <label for='candidate_{$row['candidateID']}'> {$row['candidateName']}</label>
            </div>";
        }
    } else {
        echo "<p>No candidates available to assign.</p>";
    }
}
?>
