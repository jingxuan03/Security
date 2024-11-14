<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include PHPMailer's autoloader (if using Composer)

$host = 'localhost';
$db = 'task_master';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Verify if the email exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user) {
        // Generate a unique token
        $token = bin2hex(random_bytes(50));

        // Get the current time and add 1 hour
        $expiry = date("Y-m-d H:i:s", strtotime('+8 hour'));

        // Save the token and expiration date in the database
        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE email = ?");
        $stmt->bind_param("sss", $token, $expiry, $email);
        $stmt->execute();

        // Create PHPMailer object and set SMTP configuration
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';            // Set the SMTP server to Gmail's SMTP server
            $mail->SMTPAuth = true;                   // Enable SMTP authentication
            $mail->Username = 'jx.nyles@gmail.com';   // Your Gmail email address
            $mail->Password = 'xsnf ickj phdd tigq';  // Your generated App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use STARTTLS for secure communication
            $mail->Port = 587;                       // Set the SMTP port (587 for STARTTLS)

            //Recipients
            $mail->setFrom('jx.nyles@gmail.com', 'TaskMaster');
            $mail->addAddress($email);               // Add the recipient's email address

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "Click the link to reset your password: <a href='http://localhost/taskmaster/reset_password.php?token=" . $token . "'>Reset Password</a>";

            $mail->send();
            echo "<script>alert('Reset link sent to your email.'); window.location.href='login.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');</script>";
        }
    } else {
        echo "<script>alert('Email address not found.');</script>";
    }
}

$conn->close();
?>
