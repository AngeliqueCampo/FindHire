<?php
session_start();
require_once 'dbConfig.php';
require_once 'models.php';

// handle login form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loginBtn'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // check if input is valid
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../index.php");
        exit;
    }

    // verify user credentials
    $user = authenticateUser($pdo, $email, $password);
    if ($user) {
        // set session data
        $_SESSION['user_id'] = $user['user_id']; 
        $_SESSION['role'] = $user['role'];

        // redirect based on role
        $redirectPage = ($user['role'] === 'HR') ? '../manage_HR_applicant.php' : '../job_application.php';
        header("Location: $redirectPage");
        exit;
    } else {
        $_SESSION['error'] = "Invalid email or password.";
        header("Location: ../index.php");
        exit;
    }
}


// handle registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registerBtn'])) {
    $name = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // validate inputs
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../register.php");
        exit;
    }

    // hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // create user account
    if (registerUser($pdo, $name, $email, $hashedPassword, $role)) {
        $_SESSION['message'] = "Registration successful.";
        header("Location: ../index.php");
        exit;
    } else {
        $_SESSION['error'] = "Error registering user.";
        header("Location: ../register.php");
        exit;
    }
}

// handle job application submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['applyToJobBtn'])) {
    $jobID = $_POST['jobID'] ?? '';
    $applicantID = $_POST['applicantID'] ?? $_SESSION['user_id'];
    $message = $_POST['message'] ?? '';
    $resume = $_FILES['resume'] ?? null;

    // validate inputs
    if (empty($jobID) || empty($message) || !$resume || $resume['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = "All fields are required, please upload your resume in a PDF file.";
        header("Location: ../job_application.php");
        exit;
    }

    // Upload resume
    $uploadDirectory = '../uploads/'; 
    $resumePath = $uploadDirectory . basename($resume['name']); 

    if (move_uploaded_file($resume['tmp_name'], $resumePath)) {
        $isApplicationSaved = applyForJob($pdo, $applicantID, $jobID, $message, $resumePath);
        
        if ($isApplicationSaved) {
            $_SESSION['message'] = "Application submitted successfully!";
            header("Location: ../job_application.php");
            exit;
        } else {
            $_SESSION['error'] = "Error submitting application...";
            header("Location: ../job_application.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Error uploading resume...";
        header("Location: ../job_application.php");
        exit;
    }
}


// handle follow-up message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sendFollowUpBtn'])) {
    $applicationID = $_POST['applicationID'] ?? '';
    $followUpMessage = $_POST['followUpMessage'] ?? '';
    $senderID = $_SESSION['user_id'];

    // validate inputs
    if (empty($applicationID) || empty($followUpMessage)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../job_application.php");
        exit;
    }

    // save follow-up message
    if (sendFollowUpMessage($pdo, $senderID, $followUpMessage)) {
        $_SESSION['message'] = "Follow-up sent successfully!";
        header("Location: ../job_application.php");
        exit;
    } else {
        $_SESSION['error'] = "Error sending follow-up...";
        header("Location: ../job_application.php");
        exit;
    }
}

// handle creating a job post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createJob'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $requirements = $_POST['requirements'];

    if (createJobPost($pdo, $title, $description, $requirements)) {
        $_SESSION['message'] = "Job post created successfully!";
        header("Location: ../manage_HR_applicant.php");
        exit;
    } else {
        $_SESSION['error'] = "Error creating job post...";
        header("Location: ../manage_HR_applicant.php");
        exit;
    }
}

// handle application acceptance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acceptApplication'])) {
    $applicationID = $_POST['applicationID'];

    if (acceptApplication($pdo, $applicationID)) {
        $_SESSION['message'] = "Application accepted!";
        header("Location: ../manage_HR_applicant.php");
        exit;
    } else {
        $_SESSION['error'] = "Error accepting application...";
        header("Location: ../manage_HR_applicant.php");
        exit;
    }
}

// handle application status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateStatusBtn'])) {
    $applicationID = $_POST['applicationID'];
    $status = $_POST['status'];
    $hrID = $_SESSION['user_id'];

    // get applicant id from the application
    $sql = "SELECT user_id FROM job_applications WHERE id = :applicationID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':applicationID', $applicationID, PDO::PARAM_INT);
    $stmt->execute();
    $application = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($application) {
        $applicantID = $application['user_id'];

        // update application status and notify applicant
        if (updateApplicationStatus($pdo, $applicationID, $status)) {
            $messageContent = "Your application for the job has been $status.";
            if (sendMessage($pdo, $hrID, $applicantID, $messageContent)) {
                $_SESSION['message'] = "Status updated and notification sent!";
            } else {
                $_SESSION['error'] = "Error sending notification...";
            }
        } else {
            $_SESSION['error'] = "Error updating status...";
        }
    } else {
        $_SESSION['error'] = "Application not found...";
    }

    header("Location: manage_applications.php?job_id=" . $_GET['job_id']);
    exit;
}

// handle marking a message as read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['markAsReadBtn'])) {
    $messageID = $_POST['message_id'] ?? '';

    if (!empty($messageID)) {
        $sql = "UPDATE messages SET message_status = 'Read' WHERE id = :message_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':message_id', $messageID, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Message marked as read.";
        } else {
            $_SESSION['error'] = "Error marking message.";
        }
    } else {
        $_SESSION['error'] = "Invalid message ID.";
    }

    header("Location: ../view_messages.php");
    exit;
}

?>
