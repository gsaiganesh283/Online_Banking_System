<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connect to your database
    $servername = "phpmyadmin";
    $username = "root";
    $password = "Daddy22@G";
    $dbname = "banking_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get the email entered by the user
    $email = $conn->real_escape_string($_POST['email']);

    // Query to check if the email exists in the database
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Email exists, generate a random password
        $new_password = generateRandomPassword();

        // Update the user's password in the database
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql_update = "UPDATE users SET password='$hashed_password' WHERE email='$email'";
        if ($conn->query($sql_update) === TRUE) {
            // Password updated successfully
            // Now send an email with the new password to the user
            $subject = "Password Reset";
            $message = "Your new password is: " . $new_password;
            // You can use PHP's built-in mail function to send the email
            mail($email, $subject, $message);
            echo "A new password has been sent to your email address.";
        } else {
            echo "Error updating password: " . $conn->error;
        }
    } else {
        // Email doesn't exist in the database
        echo "Email not found.";
    }

    // Close the database connection
    $conn->close();
}

// Function to generate a random password
function generateRandomPassword($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomPassword = '';
    for ($i = 0; $i < $length; $i++) {
        $randomPassword .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomPassword;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
</head>
<body>
    <h2>Forgot Password</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>
        <input type="submit" value="Reset Password">
    </form>
</body>
</html>
