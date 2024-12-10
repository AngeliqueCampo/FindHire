<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    header("Location: index.php");
    exit;
}

require_once 'core/dbConfig.php';
require_once 'core/models.php';

// fetch all messages
$messages = getMessagesForHR($pdo); 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Messages - HR</title>
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

        h1 {
            font-size: 2rem;
            font-weight: var(--font-weight-black);
            margin: 0;
            color: var(--primary-color);
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

        .container {
            width: 80%;
            margin: 20px auto;
            background: var(--white-color);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
        }

        .back-link {
            display: inline-block;
            color: var(--white-color);
            background-color: var(--primary-color);
            padding: 10px 15px;
            border-radius: 4px;
            text-decoration: none;
            margin-bottom: 20px;
        }

        .back-link:hover {
            background-color: #cc3700;
        }

        .message {
            padding: 20px;
            margin-bottom: 1.5rem;
            background-color: var(--light-grey-color);
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .message-header {
            font-weight: var(--font-weight-bold);
            color: var(--dark-color);
        }

        .message-content {
            margin: 10px 0;
        }

        .message-footer {
            font-size: 0.9rem;
            color: var(--p-color);
        }

        button {
            background-color: var(--primary-color);
            color: var(--white-color);
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #cc3700;
        }
    </style>
</head>
<body>

<nav class="navbar">
    <span class="navbar-brand">HR Management</span>
</nav>

<div class="container">
    <a href="manage_HR_applicant.php" class="back-link">Back to Dashboard</a>

    <?php foreach ($messages as $message) { ?>
        <div class="message">
            <p class="message-header">From: <?php echo htmlspecialchars($message['applicant_name']); ?></p>
            <p class="message-content"><strong>Message:</strong> <?php echo htmlspecialchars($message['message_content']); ?></p>
            <p class="message-footer"><strong>Sent on:</strong> <?php echo htmlspecialchars($message['created_at']); ?></p>
            <p class="message-footer"><strong>Status:</strong> <?php echo htmlspecialchars($message['message_status']); ?></p>

            <!-- Mark message as 'Read' -->
            <?php if ($message['message_status'] === 'Sent') { ?>
                <form method="POST" action="core/handleForms.php">
                    <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                    <button type="submit" name="markAsReadBtn">Mark as Read</button>
                </form>
            <?php } ?>
        </div>
    <?php } ?>
</div>

</body>
</html>
