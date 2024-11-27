<?php
include("header.php");

// Handle approval or rejection of students
if (isset($_GET['action']) && isset($_GET['id'])) {
    $studentId = intval($_GET['id']);
    if ($_GET['action'] == 'approve') {
        $query = "UPDATE students SET regStatus='approve' WHERE studentID='$studentId'";
        if (mysqli_query($connect, $query)) {
            echo "Student approved successfully.";
        } else {
            echo "Error approving student: " . mysqli_error($connect);
        }
    } elseif ($_GET['action'] == 'reject') {
        $query = "DELETE FROM students WHERE studentID='$studentId'";
        if (mysqli_query($connect, $query)) {
            echo "Student rejected successfully.";
        } else {
            echo "Error rejecting student: " . mysqli_error($connect);
        }
    }
}

// Fetch pending student registrations
$studentQuery = "SELECT * FROM students WHERE regStatus='pending'";
$studentResult = mysqli_query($connect, $studentQuery);

// Fetch pending candidates
$candidateQuery = "SELECT * FROM candidates WHERE cStatus = 'pending'";
$candidateResult = mysqli_query($connect, $candidateQuery);

// Fetch pending manifesto updates
$manifestoQuery = "SELECT candidateID, candidateName, manifesto, newManifesto, manifestoStatus 
                   FROM candidates 
                   WHERE manifestoStatus IN ('submitted', 'rejected')";
$manifestoResult = mysqli_query($connect, $manifestoQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification - Admin Approval</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .section {
            margin-bottom: 40px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        button {
            padding: 5px 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button.reject {
            background-color: #dc3545;
        }

        .table-container {
            max-width: 100%;
            overflow-x: auto;
        }

        .btn-approve, .btn-reject {
            padding: 8px 16px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-approve {
            background-color: #4CAF50;
        }

        .btn-approve:hover {
            background-color: #45a049;
        }

        .btn-reject {
            background-color: #f44336;
        }

        .btn-reject:hover {
            background-color: #e53935;
        }

        .manifesto-table td {
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Verification - Approve or Reject</h2>
        
        <!-- Student Registration Approval Section -->
        <div class="section">
            <h3>Student Registration Approval</h3>
            <?php
                // Include database connection
                include("header.php");

                // Handle approval or rejection
                if (isset($_GET['action']) && isset($_GET['id'])) {
                    $studentId = intval($_GET['id']);
                    if ($_GET['action'] == 'approve') {
                        $query = "UPDATE students SET regStatus='approve' WHERE studentID='$studentId'";
                        mysqli_query($connect, $query);
                    } elseif ($_GET['action'] == 'reject') {
                        $query = "DELETE FROM students WHERE studentID='$studentId'";
                        mysqli_query($connect, $query);
                    }
                }

                // Query to get pending student registrations
                $query = "SELECT * FROM students WHERE regStatus='pending'";
                $result = mysqli_query($connect, $query);
            ?>

            <div class="table-container">
                <table>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Faculty</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['studentName']; ?></td>
                        <td><?php echo $row['studentEmail']; ?></td>
                        <td><?php echo $row['faculty']; ?></td>
                        <td><?php echo $row['semester']; ?></td>
                        <td><?php echo $row['regStatus']; ?></td>
                        <td>
                            <a href="?action=approve&id=<?php echo $row['studentID']; ?>"><button>Approve</button></a>
                            <a href="?action=reject&id=<?php echo $row['studentID']; ?>"><button class="reject">Reject</button></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>

        <!-- Candidate Approval Section -->
        <div class="section">
            <h3>Review Candidate Applications</h3>
            <?php
                $query = "SELECT * FROM candidates WHERE cStatus = 'pending'";
                $result = mysqli_query($connect, $query);
            ?>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Candidate Name</th>
                            <th>Email</th>
                            <th>Manifesto</th>
                            <th>Candidate Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['candidateName']); ?></td>
                            <td><?php echo htmlspecialchars($row['candidateEmail']); ?></td>
                            <td><?php echo htmlspecialchars($row['manifesto']); ?></td>
                            <td><?php echo htmlspecialchars($row['imagePath']); ?></td>
                            <td>
                                <form action="processCandidate.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="candidateID" value="<?php echo $row['candidateID']; ?>">
                                    <button type="submit" name="action" value="approve" class="btn-approve">Approve</button>
                                    <button type="submit" name="action" value="reject" class="btn-reject">Reject</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Manifesto Review Section -->
        <div class="section">
            <h3>Review Manifestos</h3>
            <?php
                $query = "SELECT candidateID, candidateName, manifesto, newManifesto, manifestoStatus 
                          FROM candidates 
                          WHERE manifestoStatus IN ('submitted', 'rejected')";
                $result = mysqli_query($connect, $query);
            ?>

            <div class="table-container">
                <table class="manifesto-table">
                    <thead>
                        <tr>
                            <th>Candidate Name</th>
                            <th>Current Manifesto</th>
                            <th>New Manifesto</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['candidateName']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($row['manifesto'])); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($row['newManifesto'])); ?></td>
                            <td><?php echo ucfirst($row['manifestoStatus']); ?></td>
                            <td>
                                <?php if ($row['manifestoStatus'] == 'submitted'): ?>
                                    <form action="approve_manifesto_update.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="candidateID" value="<?php echo $row['candidateID']; ?>">
                                        <button type="submit" class="btn-approve">Approve</button>
                                    </form>
                                    <form action="reject_manifesto_update.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="candidateID" value="<?php echo $row['candidateID']; ?>">
                                        <button type="submit" class="btn-reject">Reject</button>
                                    </form>
                                <?php elseif ($row['manifestoStatus'] == 'rejected'): ?>
                                    <span class="text-danger">Rejected - Please revise and resubmit</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>


<?php mysqli_close($connect); ?>
