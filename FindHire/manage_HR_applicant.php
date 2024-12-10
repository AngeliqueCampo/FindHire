<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    header("Location: index.php");
    exit;
}

// fetch job posts and applications
require_once 'core/dbConfig.php';
require_once 'core/models.php';

$jobPosts = getJobPosts($pdo); 
$messages = getMessagesForHR($pdo); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard - FindHire</title>
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
    <h1>HR Dashboard</h1>
</header>

<div class="container">

    <!-- create job post -->
    <div class="section">
        <h2>Create Job Post</h2>
        <form method="POST" action="core/handleForms.php">
            <input type="text" name="title" placeholder="Job Title" class="form-control" required>
            <textarea name="description" placeholder="Job Description" class="form-control" required></textarea>
            <textarea name="requirements" placeholder="Requirements" class="form-control" required></textarea>
            <button type="submit" name="createJob" class="btn">Create Job</button>
        </form>
    </div>

    <!-- view messages -->
    <div class="section">
        <h2>
            <a href="view_messages.php" style="text-decoration: none; color: var(--primary-color);">
                View All Messages
            </a>
        </h2>
    </div>

    <!-- job posts -->
    <div class="section">
        <h2>Job Posts</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Requirements</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jobPosts as $jobPost) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($jobPost['job_title']); ?></td>
                        <td><?php echo htmlspecialchars($jobPost['job_description']); ?></td>
                        <td><?php echo htmlspecialchars($jobPost['job_requirements']); ?></td>
                        <td>
                            <a href="manage_applications.php?job_id=<?php echo $jobPost['id']; ?>" class="btn">Manage Applications</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
