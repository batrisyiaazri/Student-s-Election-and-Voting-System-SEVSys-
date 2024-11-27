<?php
// Include the database connection file
include('header.php'); 

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the selected candidates from the form
    $electionID = $_POST['electionID'];
    if (isset($_POST['candidates']) && !empty($_POST['candidates'])) {
        // Loop through the selected candidates and assign them to the election
        foreach ($_POST['candidates'] as $candidateID) {
            // Update the candidate with the selected electionID
            $query = "UPDATE candidates SET electionID = ? WHERE candidateID = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $electionID, $candidateID);
            if ($stmt->execute()) {
                // Successfully assigned candidate to the election
                echo "Candidate $candidateID has been assigned to election $electionID.<br>";
            } else {
                // Handle error if the assignment fails
                echo "Error assigning candidate $candidateID.<br>";
            }
        }
        echo "Candidates have been successfully assigned!";
    } else {
        echo "No candidates selected.";
    }
} else {
    echo "Invalid request.";
}

?>
