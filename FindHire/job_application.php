<?php 
session_start();
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Applicant') {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Applications</title>
    <style>
        :root {
            --white-color: #FFFFFF;
            --primary-color: #FF4400;
            --dark-color: #000000;
            --grey-color: #d0d1d1;
            --light-grey-color: #f4f7fc;
            --p-color: #717275;

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

        h1, h2 {
            font-weight: var(--font-weight-black);
        }

        h2 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        .navbar {
            background: var(--white-color);
            padding: 1rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: var(--font-weight-bold);
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

        .site-header {
            background-color: var(--grey-color);
            text-align: center;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }

        .site-header h1 {
            color: var(--primary-color);
        }

        .container {
            width: 80%;
            margin: 0 auto;
        }

        .section {
            background: var(--white-color);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 2rem;
        }

        .section h2 {
            color: var(--primary-color);
            border-bottom: 2px solid var(--grey-color);
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .table th, .table td {
            border: 1px solid var(--grey-color);
            padding: 10px;
            text-align: left;
        }

        .table th {
            background-color: var(--dark-color);
            color: var(--white-color);
        }

        .table tr:hover {
            background-color: var(--grey-color);
        }

        .form-control {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid var(--grey-color);
            border-radius: 5px;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>

<nav class="navbar">
    <span class="navbar-brand">FindHire</span>
    <form action="logout.php" method="POST">
        <button type="submit" name="logout" class="btn">Logout</button>
    </form>
</nav>

<header class="site-header">
    <h1>Job Application System</h1>
</header>

<div class="container">

    <!-- available job listings -->
    <div class="section">
        <h2>Available Job Listings</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Job ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Requirements</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $jobPosts = getAllJobPosts($pdo); 
                foreach ($jobPosts as $job) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($job['id']); ?></td>
                        <td><?php echo htmlspecialchars($job['job_title']); ?></td>
                        <td><?php echo htmlspecialchars($job['job_description']); ?></td>
                        <td><?php echo htmlspecialchars($job['job_requirements']); ?></td>
                        <td>
                            <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="jobID" value="<?php echo htmlspecialchars($job['id']); ?>">
                                <input type="hidden" name="applicantID" value="<?php echo $_SESSION['user_id']; ?>">
                                <label for="message">How does your experience and skill set align with the requirements of this role?</label>
                                <textarea name="message" id="message" class="form-control" required></textarea>
                                <label for="resume">Upload Resume (PDF):</label>
                                <input type="file" name="resume" id="resume" class="form-control" accept="application/pdf" required>
                                <button type="submit" name="applyToJobBtn" class="btn">Apply</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- your applications -->
    <div class="section">
        <h2>Your Applications</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Application ID</th>
                    <th>Job Title</th>
                    <th>Status</th>
                    <th>Follow Up</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $applications = getUserApplications($pdo, $_SESSION['user_id']); 
                foreach ($applications as $application) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($application['applicationID']); ?></td>
                        <td><?php echo htmlspecialchars($application['job_title']); ?></td>
                        <td><?php echo htmlspecialchars($application['status']); ?></td>
                        <td>
                            <form action="core/handleForms.php" method="POST">
                                <input type="hidden" name="applicationID" value="<?php echo htmlspecialchars($application['applicationID']); ?>">
                                <textarea name="followUpMessage" class="form-control" required></textarea>
                                <button type="submit" name="sendFollowUpBtn" class="btn">Send Follow-Up</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- your messages -->
    <div class="section">
        <h2>Your Messages</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Message ID</th>
                    <th>Message Content</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $messages = getApplicantMessages($pdo, $_SESSION['user_id']); 
                foreach ($messages as $message) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($message['id']); ?></td>
                        <td><?php echo htmlspecialchars($message['message_content']); ?></td>
                        <td><?php echo htmlspecialchars($message['message_status']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
