<?php
// Start output buffering at the very top
ob_start();

// Start session at the very beginning before any output
session_start();

// Debug settings - uncomment for troubleshooting
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Database configuration
$db_host = "localhost";
$db_user = "root"; // Default XAMPP username
$db_pass = "";     // Default XAMPP password
$db_name = "quiz404";

// Connect to database
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message variables
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';

// Clear session messages after displaying them
unset($_SESSION['error']);
unset($_SESSION['success']);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if (!in_array($action, ['login', 'signup'])) {
        $_SESSION['error'] = "Invalid action.";
        header("Location: auth.php");
        ob_end_flush();
        exit();
    }

    // Login logic
    if ($action === 'login') {
        $username_or_email = trim($_POST['login'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username_or_email) || empty($password)) {
            $_SESSION['error'] = "Username/Email and password are required.";
            header("Location: auth.php");
            ob_end_flush();
            exit();
        }

        // Updated query: check username or email
        $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username_or_email, $username_or_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // Regenerate session ID for security
                session_regenerate_id(true);
                
                // Ensure no output before header
                ob_end_clean();
                header("Location: home.php");
                exit();
            }
        }

        $_SESSION['error'] = "Invalid username/email or password.";
        header("Location: auth.php");
        ob_end_flush();
        exit();
    }

    // Signup logic
    elseif ($action === 'signup') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');
        $fullname = trim($_POST['fullname'] ?? '');

        if (empty($username) || empty($email) || empty($password) || empty($fullname)) {
            $_SESSION['error'] = "All fields are required.";
            header("Location: auth.php?form=signup");
            ob_end_flush();
            exit();
        }

        // Check if passwords match
        if ($password !== $confirm_password) {
            $_SESSION['error'] = "Passwords do not match.";
            header("Location: auth.php?form=signup");
            ob_end_flush();
            exit();
        }

        // Check if username/email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $_SESSION['error'] = "Username or email already exists.";
            header("Location: auth.php?form=signup");
            ob_end_flush();
            exit();
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, fullname) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $fullname);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Registration successful! Please login.";
                header("Location: auth.php");
                ob_end_flush();
                exit();
            } else {
                $_SESSION['error'] = "Registration failed. Please try again.";
                header("Location: auth.php?form=signup");
                ob_end_flush();
                exit();
            }
        }
    }
}

// Flush output buffer if we reach here (for the HTML portion)
ob_end_flush();
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign In/Sign Up</title>
    <link
      href="https://fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Outlined"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap"
      rel="stylesheet"
    />
    <style>
      body {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        font-family: "Poppins", sans-serif;
        margin: 0;
        background: radial-gradient(circle at 50% 50%, #1e293b, #000);
        color: white;
        overflow: hidden;
        position: relative;
      }

      #particles {
        position: absolute;
        top: 0;
        left: 0;
        z-index: -10;
        width: 100%;
        height: 100%;
      }

      form {
        width: 400px;
        padding: 3rem 2.5rem;
        border: 3px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        background-color: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(12px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2rem;
        transition: all 0.5s ease;
      }

      .form-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 1rem;
      }

      .input-wrapper {
        width: 80%;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
      }

      .input {
        width: 100%;
        height: 50px;
        padding: 0 16px;
        padding-right: 50px;
        font-size: 16px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 8px;
        outline: none;
        font-family: "Poppins", sans-serif;
        font-weight: 500;
        transition: 0.3s;
        background: rgba(255, 255, 255, 0.1);
        color: white;
      }

      .input:focus {
        box-shadow: 0 0 5px rgba(255, 255, 255, 0.8);
        border: 2px solid rgba(255, 255, 255, 0.8);
        padding-top: 12px;
      }

      .input::placeholder {
        color: rgba(255, 255, 255, 0.7);
      }

      .label {
        position: absolute;
        top: 14px;
        left: 16px;
        font-size: 12px;
        font-weight: 600;
        opacity: 0;
        visibility: hidden;
        transition: 0.3s;
        color: rgba(255, 255, 255, 0.9);
      }

      .input:focus + .label,
      .input:not(:placeholder-shown) + .label {
        top: 4px;
        font-size: 11px;
        opacity: 1;
        visibility: visible;
      }

      .input:focus::placeholder {
        opacity: 0;
      }

      .btn {
        position: relative;
        height: 55px;
        width: 80%;
        font-size: 18px;
        font-weight: 600;
        font-family: "Poppins", sans-serif;
        color: white;
        background: rgb(85, 81, 255);
        border: 2px solid rgba(255, 255, 255, 0.8);
        border-radius: 8px;
        cursor: pointer;
        overflow: hidden;
        transition: all 0.3s ease;
      }

      .btn-text {
        position: relative;
        z-index: 1;
        transition: opacity 0.3s ease;
      }

      .btn::after {
        content: "arrow_forward";
        font-family: "Material Icons Outlined";
        position: absolute;
        top: 0;
        right: 0;
        width: 50px;
        height: 100%;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        transition: all 0.5s ease;
      }

      .load .btn::after {
        content: "loop";
        background: linear-gradient(
          45deg,
          rgba(81, 81, 255, 0.8) 50%,
          rgba(255, 255, 255, 0.2) 50%,
          rgba(255, 255, 255, 0.2) 100%
        );
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: rotate 1s infinite linear;
      }

      .signin-submit .btn::after {
        content: "Welcome Back!";
        font-family: "Poppins", sans-serif;
        font-size: 16px;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        width: 100%;
        justify-content: center;
        animation: slide 0.5s forwards;
      }

      .signup-submit .btn::after {
        content: "Account Created";
        font-family: "Poppins", sans-serif;
        font-size: 16px;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        width: 100%;
        justify-content: center;
        animation: slide 0.5s forwards;
      }

      .signin-submit .btn-text,
      .signup-submit .btn-text {
        opacity: 0;
      }

      @keyframes rotate {
        from {
          transform: rotate(0deg);
        }
        to {
          transform: rotate(-360deg);
        }
      }

      @keyframes slide {
        from {
          width: 50px;
        }
        to {
          width: 100%;
        }
      }

      .toggle-text {
        font-size: 14px;
        margin-top: 0.5rem;
        color: rgba(255, 255, 255, 0.8);
      }

      .toggle-text a {
        color: rgb(200, 200, 255);
        font-weight: 600;
        text-decoration: none;
        transition: 0.3s;
        cursor: pointer;
      }

      .toggle-text a:hover {
        text-decoration: underline;
        color: white;
      }

      .google-btn {
        position: relative;
        width: 80%;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        font-weight: 600;
        font-family: "Poppins", sans-serif;
        padding: 12px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
      }

      .google-btn:hover {
        transform: scale(1.05);
        background: rgba(255, 255, 255, 0.2);
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
      }

      .google-btn img {
        width: 20px;
        height: 20px;
        margin-right: 10px;
      }

      .forgot-password {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.7);
        text-align: right;
        width: 80%;
        margin-top: -15px;
      }

      .forgot-password a {
        color: rgb(200, 200, 255);
        text-decoration: none;
        transition: 0.3s;
      }

      .forgot-password a:hover {
        text-decoration: underline;
        color: white;
      }

      .click-effect {
        position: absolute;
        pointer-events: none;
        z-index: 10;
      }

      .spike {
        position: absolute;
        bottom: 0;
        left: 0;
        transform-origin: bottom;
        width: 0;
        height: 0;
        background: white;
        border-radius: 0.3mm;
        transform: rotate(var(--angle)) translateY(var(--distance));
      }

      .effect .spike {
        animation: animate 0.75s;
      }

      @keyframes animate {
        0% {
          width: 3px;
          height: 12px;
          opacity: 0;
          transform: rotate(var(--angle)) translateY(0);
        }
        2% {
          width: 4px;
          height: 16px;
          opacity: 1;
          transform: rotate(var(--angle)) translateY(0);
        }
        100% {
          width: 0px;
          height: 0px;
          opacity: 1;
          transform: rotate(var(--angle)) translateY(var(--distance));
        }
      }

      /* Hide the signup form by default */
      #signupForm {
        display: none;
      }

      /* Error message styles */
      .alert {
        width: 80%;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
        text-align: center;
      }

      .alert-danger {
        background-color: rgba(255, 0, 0, 0.2);
        border: 1px solid rgba(255, 0, 0, 0.5);
        color: white;
      }

      .alert-success {
        background-color: rgba(0, 255, 0, 0.2);
        border: 1px solid rgba(0, 255, 0, 0.5);
        color: white;
      }

      .message-container {
        position: absolute;
        top: 20px;
        width: 100%;
        display: flex;
        justify-content: center;
        z-index: 100;
      }
    </style>
  </head>
  <body>
    <!-- Particle Animation Canvas -->
    <canvas id="particles"></canvas>

    <!-- Burst effect container -->
    <div class="click-effect">
      <div class="spike" style="--angle: 5deg; --distance: 30px"></div>
      <div class="spike" style="--angle: 55deg; --distance: 31px"></div>
      <div class="spike" style="--angle: 75deg; --distance: 27px"></div>
      <div class="spike" style="--angle: 135deg; --distance: 30px"></div>
      <div class="spike" style="--angle: 190deg; --distance: 28px"></div>
      <div class="spike" style="--angle: 210deg; --distance: 32px"></div>
      <div class="spike" style="--angle: 280deg; --distance: 31px"></div>
      <div class="spike" style="--angle: 330deg; --distance: 30px"></div>
    </div>

    <!-- Message container for errors and success messages -->
    <div class="message-container">
      <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      
      <?php if (!empty($success)): ?>
          <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>
    </div>

    <!-- Sign In Form -->
    <form action="auth.php" method="POST" id="signinForm" autocomplete="off" class="<?php echo (empty($_GET['form']) || $_GET['form'] === 'signin') ? 'active' : ''; ?>">
        <h1 class="form-title">Welcome Back</h1>
        <input type="hidden" name="action" value="login">

        <div class="input-wrapper">
            <input
                type="text"
                class="input"
                name="login"
                placeholder="Username or Email"
                required
            />
            <span class="label">USERNAME / EMAIL</span>
        </div>

        <div class="input-wrapper">
            <input
                type="password"
                class="input"
                name="password"
                placeholder="Password"
                required
            />
            <span class="label">PASSWORD</span>
        </div>

        <div class="forgot-password">
            <a href="#">Forgot password?</a>
        </div>

        <button type="submit" class="btn">
            <span class="btn-text">Sign In</span>
        </button>

        <div class="toggle-text">
            Don't have an account? <a href="auth.php?form=signup">Sign Up</a>
        </div>
    </form>

    <!-- Sign Up Form -->
    <form action="auth.php" method="POST" id="signupForm" autocomplete="off" class="<?php echo isset($_GET['form']) && $_GET['form'] === 'signup' ? 'active' : ''; ?>">
        <h1 class="form-title">Create an Account!</h1>
        <input type="hidden" name="action" value="signup">

        <div class="input-wrapper">
            <input
                type="text"
                class="input"
                name="fullname"
                placeholder="Full Name"
                required
            />
            <span class="label">FULL NAME</span>
        </div>

        <div class="input-wrapper">
            <input
                type="text"
                class="input"
                name="username"
                placeholder="Username"
                required
                minlength="3"
                maxlength="50"
            />
            <span class="label">USERNAME</span>
        </div>

        <div class="input-wrapper">
            <input
                type="email"
                class="input"
                name="email"
                placeholder="Email"
                required
            />
            <span class="label">EMAIL</span>
        </div>

        <div class="input-wrapper">
            <input
                type="password"
                class="input"
                name="password"
                placeholder="Password"
                required
                minlength="6"
                pattern="^(?=.*[A-Za-z])(?=.*\d).{6,}$"
                title="Password must be at least 6 characters with at least one letter and one number"
            />
            <span class="label">PASSWORD</span>
        </div>

        <div class="input-wrapper">
            <input
                type="password"
                class="input"
                name="confirm_password"
                placeholder="Confirm Password"
                required
            />
            <span class="label">CONFIRM PASSWORD</span>
            <div id="passwordMatchError" class="error-message" style="display:none; color:red;"></div>
        </div>

        <button type="submit" class="btn">
            <span class="btn-text">Sign Up</span>
        </button>

        <div class="toggle-text">
            Already have an account? <a href="auth.php?form=signin">Sign In</a>
        </div>
    </form>

<script>
// Particle Animation
const canvas = document.getElementById("particles");
const ctx = canvas?.getContext("2d");

function resizeCanvas() {
    if (canvas) {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }
}
resizeCanvas();
window.addEventListener("resize", resizeCanvas);

class Particle {
    constructor() {
        this.reset();
    }

    reset() {
        this.x = Math.random() * canvas.width;
        this.y = Math.random() * canvas.height;
        this.size = Math.random() * 2 + 1;
        this.speedX = Math.random() * 2 - 1;
        this.speedY = Math.random() * 2 - 1;
        this.opacity = Math.random() * 0.5 + 0.2;
    }

    update() {
        this.x += this.speedX;
        this.y += this.speedY;

        if (this.x > canvas.width || this.x < 0) this.speedX *= -1;
        if (this.y > canvas.height || this.y < 0) this.speedY *= -1;
    }

    draw() {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(255, 255, 255, ${this.opacity})`;
        ctx.fill();
    }
}

const particles = Array.from({ length: 100 }, () => new Particle());

function animate() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    particles.forEach(p => {
        p.update();
        p.draw();
    });
    requestAnimationFrame(animate);
}
animate();

// Burst effect animation
let animationInProgress = false;
let animationId;

function createBurstEffect(event) {
    const clickEffect = document.querySelector(".click-effect");
    if (!clickEffect) return;

    if (animationInProgress) {
        clearTimeout(animationId);
        clickEffect.classList.remove("effect");
        void clickEffect.offsetWidth; // Force reflow
    }

    clickEffect.style.top = `${event.clientY + window.scrollY}px`;
    clickEffect.style.left = `${event.clientX + window.scrollX}px`;
    clickEffect.classList.add("effect");
    animationInProgress = true;

    animationId = setTimeout(() => {
        clickEffect.classList.remove("effect");
        animationInProgress = false;
    }, 750);
}

// Add click event listener to the document for burst effect
document.addEventListener('click', createBurstEffect);

// Password live validation
document.getElementById('signupForm')?.querySelector('input[name="confirm_password"]')?.addEventListener('input', function() {
    const password = this.form.querySelector('input[name="password"]').value;
    const errorElement = document.getElementById('passwordMatchError');

    if (this.value !== password) {
        errorElement.textContent = "Passwords do not match!";
        errorElement.style.display = 'block';
    } else {
        errorElement.style.display = 'none';
    }
});

// Toggle form view based on URL param
document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const formParam = urlParams.get('form');

    const signinForm = document.getElementById('signinForm');
    const signupForm = document.getElementById('signupForm');

    if (formParam === 'signup') {
        signinForm.style.display = 'none';
        signupForm.style.display = 'flex';
    } else {
        signinForm.style.display = 'flex';
        signupForm.style.display = 'none';
    }
    
    // Add loading animation to forms
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Don't prevent the default submission
            const btn = this.querySelector('.btn');
            btn.disabled = true;
            this.classList.add('load');
            
            // Just add visual animation while normal form submission proceeds
            setTimeout(() => {
                this.classList.add(this.id === 'signinForm' ? 'signin-submit' : 'signup-submit');
            }, 500);
        });
    });
});
</script>

  </body>
</html>