<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    header("Location: index.php");
    exit;
}

// fetch job applications
require_once 'core/dbConfig.php';
require_once 'core/models.php';

$jobID = $_GET['job_id'] ?? ''; 
if (empty($jobID)) {
    header("Location: manage_HR_applicant.php");
    exit;
}

// fetch job details
$jobPost = getJobPostById($pdo, $jobID);
if (!$jobPost) {
    header("Location: manage_HR_applicant.php");
    exit;
}

// handle application status 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateStatus'])) {
    $applicationID = $_POST['applicationID'];
    $newStatus = $_POST['status'];

    if (updateApplicationStatus($pdo, $applicationID, $newStatus)) {
        $_SESSION['message'] = "Application status updated successfully!";
        header("Location: manage_applications.php?job_id=$jobID");
        exit;
    } else {
        $_SESSION['error'] = "Failed to update application status.";
    }
}

$applications = getApplicationsByJobId($pdo, $jobID);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Job Applications</title>
    <style>
        :root {
            --white-color: #FFFFFF;
            --primary-color: #FF4400;
            --dark-color: #000000;
            --grey-color: #d0d1d1;
            --light-grey-color: #f4f7fc;

            --body-font-family: 'Inter', sans-serif;
            --font-weight-light: 300;
            --font-weight-bold: 700;
            --font-weight-black: 900;
        }

        body {
            font-family: var(--body-font-family);
            background-color: var(--light-grey-color);
            margin: 0;
            color: var(--dark-color);
        }

        h1 {
            font-weight: var(--font-weight-black);
            color: var(--primary-color);
        }

        .navbar {
            background: var(--white-color);
            padding: 1rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: var(--font-weight-bold);
        }

        .container {
            width: 80%;
            margin: 2rem auto;
            background: var(--white-color);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
        }

        .btn {
            background-color: var(--primary-color);
            color: var(--white-color);
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn:hover {
            background-color: #cc3700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        table th, table td {
            border: 1px solid var(--grey-color);
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: var(--dark-color);
            color: var(--white-color);
        }

        table tr:hover {
            background-color: var(--grey-color);
        }

        .message, .error {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
        }

        .message {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .form-control {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid var(--grey-color);
            border-radius: 5px;
        }
    </style>
</head>
<body>

<nav class="navbar">
    <span class="navbar-brand">FindHire</span>
</nav>

<div class="container">
    <h1>Manage Applications for Job: <?php echo htmlspecialchars($jobPost['job_title']); ?></h1>

    <?php
    if (isset($_SESSION['message'])) {
        echo "<div class='message'>" . $_SESSION['message'] . "</div>";
        unset($_SESSION['message']);
    }
    if (isset($_SESSION['error'])) {
        echo "<div class='error'>" . $_SESSION['error'] . "</div>";
        unset($_SESSION['error']);
    }
    ?>

    <h2>Applications</h2>
    <table>
        <thead>
            <tr>
                <th>Applicant ID</th>
                <th>Applicant Name</th>
                <th>Application Status</th>
                <th>Resume</th>
                <th>Message</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($applications as $application) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($application['applicant_id']); ?></td>
                    <td><?php echo htmlspecialchars($application['applicant_name']); ?></td>
                    <td><?php echo htmlspecialchars($application['application_status']); ?></td>
                    <td>
                        <?php if (!empty($application['application_resume'])) { ?>
                            <a href="uploads/<?php echo htmlspecialchars($application['application_resume']); ?>" target="_blank">View Resume</a>
                        <?php } else { ?>
                            No resume uploaded
                        <?php } ?>
                    </td>
                    <td><?php echo nl2br(htmlspecialchars($application['application_message'])); ?></td>
                    <td>
                        <form action="manage_applications.php?job_id=<?php echo $jobID; ?>" method="POST">
                            <input type="hidden" name="applicationID" value="<?php echo htmlspecialchars($application['application_id']); ?>">
                            <select name="status" class="form-control" required>
                                <option value="Pending" <?php echo ($application['application_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="Accepted" <?php echo ($application['application_status'] == 'Accepted') ? 'selected' : ''; ?>>Accepted</option>
                                <option value="Rejected" <?php echo ($application['application_status'] == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                            <button type="submit" name="updateStatus" class="btn">Update Status</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
