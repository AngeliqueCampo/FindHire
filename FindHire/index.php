<?php 
    require_once 'core/dbConfig.php'; 
    require_once 'core/models.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindHire - Login</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        h3 {
            text-align: center;
            font-size: 36px;
            color: #000000;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .form-container {
            width: 100%;
            max-width: 400px;
            background-color: #FFFFFF;
            border: 1px solid #d0d1d1;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .form-header {
            background-color: #FF4400;
            padding: 20px;
            text-align: center;
        }

        .form-header h4 {
            color: #FFFFFF;
            font-size: 24px;
            margin: 0;
            font-weight: 700;
        }

        .form-body {
            padding: 30px 20px;
        }

        label {
            font-size: 16px;
            color: #000000;
            margin-bottom: 8px;
            display: block;
            font-weight: 600;
        }

        input {
            font-size: 16px;
            padding: 10px;
            margin-bottom: 20px;
            width: 100%;
            border: 1px solid #d0d1d1;
            border-radius: 6px;
            box-sizing: border-box;
            background-color: #FFFFFF;
        }

        input:focus {
            outline: none;
            border-color: #FF4400;
            box-shadow: 0 0 4px rgba(255, 68, 0, 0.4);
        }

        input[type="submit"] {
            background-color: #FF4400;
            color: #FFFFFF;
            border: none;
            cursor: pointer;
            font-size: 18px;
            padding: 12px;
            border-radius: 6px;
            font-weight: 700;
            transition: background-color 0.3s, transform 0.2s;
        }

        input[type="submit"]:hover {
            background-color: #cc3600;
            transform: translateY(-2px);
        }

        input[type="submit"]:active {
            transform: translateY(0);
        }

        .register-link {
            text-align: center;
            margin-top: 10px;
            font-size: 16px;
        }

        .register-link a {
            color: #FF4400;
            text-decoration: none;
            font-weight: 700;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <h4>Login</h4>
        </div>
        <div class="form-body">
            <form action="core/handleForms.php" method="POST">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Enter your email" required>
                
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
                
                <input type="submit" name="loginBtn" value="Login">
            </form>

            <div class="register-link">
                Don't have an account? <a href="register.php">Register here</a>
            </div>
        </div>
    </div>
</body>
</html>
