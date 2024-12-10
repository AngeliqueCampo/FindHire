<?php 

require_once 'dbConfig.php';

// authenticate user
function authenticateUser($pdo, $email, $password) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return $user; 
        }
    } catch (PDOException $e) {
        error_log("Error in authenticateUser: " . $e->getMessage());
    }
    return false; 
}

// register user
function registerUser($pdo, $name, $email, $password, $role) {
    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);

        return $stmt->execute(); 
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { 
            error_log("Duplicate email error in registerUser: " . $e->getMessage());
        } else {
            error_log("Error in registerUser: " . $e->getMessage());
        }
    }
    return false; 
}

// fetch all jobs
function getAllJobPosts($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM job_posts");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all job posts
    } catch (PDOException $e) {
        error_log("Error in getAllJobPosts: " . $e->getMessage());
    }
    return []; // Return an empty array if there's an error
}

// apply for job
function applyForJob($pdo, $applicantID, $jobID, $message, $resumePath) {
    try {
        // sets default status to pending
        $applicationStatus = 'Pending';

        // prepare SQL query
        $stmt = $pdo->prepare("INSERT INTO job_applications (applicant_id, job_post_id, application_status, applied_at, application_message, application_resume) 
                               VALUES (:applicant_id, :job_post_id, :application_status, CURRENT_TIMESTAMP, :application_message, :application_resume)");

        // bind parameters
        $stmt->bindParam(':applicant_id', $applicantID);
        $stmt->bindParam(':job_post_id', $jobID);
        $stmt->bindParam(':application_status', $applicationStatus);
        $stmt->bindParam(':application_message', $message);
        $stmt->bindParam(':application_resume', $resumePath);

        // execute the query and check if it was successful
        if ($stmt->execute()) {
            return true;
        } else {
            // If execution failed, log the error
            $errorInfo = $stmt->errorInfo();
            error_log("Error inserting application: " . implode(", ", $errorInfo));
            return false;
        }
    } catch (PDOException $e) {
        // log the PDO exception
        error_log("Error in applyForJob: " . $e->getMessage());
        return false;
    }
}


// send a follow up message
function sendFollowUpMessage($pdo, $senderID, $messageContent) {
    $sql = "INSERT INTO messages (sender_id, message_content) VALUES (:sender_id, :message_content)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':sender_id', $senderID, PDO::PARAM_INT);
    $stmt->bindParam(':message_content', $messageContent, PDO::PARAM_STR);  
    
    return $stmt->execute(); 
}

// get HR messages
function getMessagesForHR($pdo) {
    $sql = "SELECT m.id, m.sender_id, m.message_content, m.created_at, m.message_status, u.name AS applicant_name
            FROM messages m
            JOIN users u ON m.sender_id = u.user_id
            ORDER BY m.created_at DESC"; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// get applications
function getUserApplications($pdo, $userID) {
    $sql = "SELECT ja.id AS applicationID, jp.job_title AS job_title, ja.application_status AS status
            FROM job_applications ja
            JOIN job_posts jp ON ja.job_post_id = jp.id
            WHERE ja.applicant_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $userID, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}

// create job
function createJobPost($pdo, $title, $description, $requirements) {
    try {
        $stmt = $pdo->prepare("INSERT INTO job_posts (job_title, job_description, job_requirements) VALUES (:title, :description, :requirements)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':requirements', $requirements);
        return $stmt->execute(); 
    } catch (PDOException $e) {
        error_log("Error creating job post: " . $e->getMessage());
    }
    return false; 
}

// accept an application
function acceptApplication($pdo, $applicationID) {
    try {
        $stmt = $pdo->prepare("UPDATE job_applications SET application_status = 'Accepted' WHERE id = :id");
        $stmt->bindParam(':id', $applicationID);
        return $stmt->execute(); // Return true if update is successful
    } catch (PDOException $e) {
        error_log("Error accepting application: " . $e->getMessage());
    }
    return false; 
}

// get messages = HR
function getMessages($pdo, $hrUserId) {
    $sql = "SELECT m.id, m.sender_id, m.receiver_id, m.message_content AS message, m.message_status, m.created_at
            FROM messages m
            WHERE m.receiver_id = :hrUserId
            ORDER BY m.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':hrUserId', $hrUserId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// fetch all job posts
function getJobPosts($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM job_posts ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Fetch all posts as an associative array
    } catch (PDOException $e) {
        error_log("Error fetching job posts: " . $e->getMessage());
        return [];  // Return an empty array in case of an error
    }
}

// fetch job post by ID
function getJobPostById($pdo, $jobID) {
    $sql = "SELECT * FROM job_posts WHERE id = :jobID";
    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':jobID', $jobID, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// get application by job id
function getApplicationsByJobId($pdo, $jobId) {
    $sql = "SELECT ja.id AS application_id, ja.applicant_id, ja.application_status, 
                   ja.application_message, ja.application_resume, u.name AS applicant_name 
            FROM job_applications ja
            JOIN users u ON ja.applicant_id = u.user_id
            WHERE ja.job_post_id = :jobId";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':jobId', $jobId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// get user by job id
function getUserById($pdo, $userId) {
    $sql = "SELECT user_id, name FROM users WHERE user_id = :userId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
}


// updates application status
function updateApplicationStatus($pdo, $applicationID, $newStatus) {
    $sql = "UPDATE job_applications 
            SET application_status = :newStatus 
            WHERE id = :applicationID";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':newStatus', $newStatus, PDO::PARAM_STR);
    $stmt->bindParam(':applicationID', $applicationID, PDO::PARAM_INT);

    return $stmt->execute();
}

// get applicant message
function getApplicantMessages($pdo, $applicantId) {
    $stmt = $pdo->prepare("SELECT m.id, m.message_content, m.message_status, m.created_at
                            FROM messages m
                            WHERE m.sender_id = :applicant_id");
    $stmt->bindParam(':applicant_id', $applicantId, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// get application details
function getApplicationDetails($pdo, $userId, $jobId) {
    $sql = "SELECT application_resume, application_message 
            FROM job_applications 
            WHERE applicant_id = :userId AND job_post_id = :jobId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':jobId', $jobId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

?>
