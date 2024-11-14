<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Forgot Password</title>
</head>
<body>
    <div class="login-container">
        <button type="button" class="back-button" onclick="window.location.href='login.php'">Back</button>
            <h2>Forgot Password</h2>
                <form method="POST" action="reset_link.php">
                    <div class="username-input-container">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="Enter your email" required>
                    <button type="submit" class="reset-button">Send Reset Link</button>
                    </div>
    </div>
</form>
</body>