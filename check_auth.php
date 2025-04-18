<?php
session_start();
require_once 'config.php'; // This should connect to your DB

// LOGIN handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username_or_email = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :input OR email = :input");
    $stmt->execute(['input' => $username_or_email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['last_activity'] = time();

        if (isset($_SESSION['redirect_after_login'])) {
            $redirect = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
            header("Location: $redirect");
        } else {
            header("Location: home.php"); // Preserve original redirect to home.php
        }
        exit;
    } else {
        $error = "Invalid username/email or password.";
        header("Location: auth.php?message=" . urlencode($error));
        exit;
    }
}

// SIGNUP handler (optional — only include if needed)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $email, $password])) {
        header("Location: auth.php?message=" . urlencode("Registration successful. Please log in."));
    } else {
        header("Location: auth.php?message=" . urlencode("Registration failed. Try again."));
    }
    exit;
}

// LOGOUT handler - new functionality
if (isset($_GET['logout'])) {
    // Destroy the session
    session_unset();
    session_destroy();
    
    // Redirect to index page
    header("Location: index.php");
    exit();
}

// List of public pages that don't require authentication
$public_pages = ['community.php', 'about.php', 'contact.php', 'index.php', 'auth.php'];

// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);

// If not logged in and trying to access a page that's not in the public pages list
if (!in_array($current_page, $public_pages) && !isset($_SESSION['logged_in'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI']; // Save current URL for redirection after login
    header("Location: auth.php");
    exit();
}

// If trying to access index.php while logged in - preserve original redirect behavior
if ($current_page === 'index.php' && isset($_SESSION['logged_in'])) {
    header("Location: home.php");
    exit();
}

// Function to render the appropriate auth button based on login status
function renderAuthButton() {
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        // Logged in - show red logout button
        echo '<a href="?logout=1" class="auth-button logout">Logout</a>';
    } else {
        // Not logged in - show green login button
        echo '<a href="auth.php" class="auth-button login">Login/Sign Up</a>';
    }
}

// Add the CSS for the circular buttons
echo '
<style>
.auth-button {
    display: inline-block;
    border-radius: 50%;
    width: 80px;
    height: 80px;
    line-height: 80px;
    text-align: center;
    text-decoration: none;
    color: white;
    font-weight: bold;
    transition: all 0.3s ease;
}
.login {
    background-color: #28a745; /* Green button for login */
}
.logout {
    background-color: #dc3545; /* Red button for logout */
}
.auth-button:hover {
    transform: scale(1.05);
}
</style>';
?>