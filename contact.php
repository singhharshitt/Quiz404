<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


// Include the Composer autoloader
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
// Backend: Handle form submission
$success = "";
$error = "";

// DB config
$host = 'localhost';
$db = 'quiz404';
$user = 'root';
$pass = '';


// Start session and authentication control
session_start();

// Database connection (if needed for user-specific content)
$conn = new mysqli("localhost", "root", "", "quiz404");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to render auth button (same as before)
function renderAuthButton() {
    if (isset($_SESSION['user_id'])) {
        // Logged in - show red logout button
        echo '<a href="?logout=1" class="auth-btn logout flex items-center justify-center">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
              </a>';
    } else {
        // Not logged in - show green login button
        echo '<a href="auth.php" class="auth-btn login flex items-center justify-center">
                <i class="fas fa-user"></i>
                <span>Login</span>
              </a>';
    }
}

// Handle logout if requested
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}


// Connect to DB
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    $error = "Connection failed: " . $conn->connect_error;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = htmlspecialchars($_POST['name']);
    $email   = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } else {
        // Store the message in the database
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            // Send an email using PHPMailer
            try {
                // Create an instance of PHPMailer
                $mail = new PHPMailer(true);

                // Server settings
                $mail->isSMTP();                                          // Set mailer to use SMTP
                $mail->Host       = 'smtp.gmail.com';                       // Set the SMTP server to Gmail
                $mail->SMTPAuth   = true;                                  // Enable SMTP authentication
                $mail->Username   = 'harshiitthoon25@gmail.com';                // SMTP username (your Gmail)
                // $mail->Password   = getenv('MAIL_PASSWORD');
                // $config = include('something.php');
                // $mail->Password = $config['MAIL_PASSWORD'];
                $mail->Password = $_ENV['MAIL_PASSWORD'];
                   // SMTP password (app-specific password)
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;        // Enable TLS encryption
                $mail->Port       = 587;                                   // TCP port to connect to (for Gmail it's 587)

                // Recipients
                $mail->setFrom('your-email@gmail.com', 'Your Name');       // Sender's email
                $mail->addAddress($_POST['email'], $_POST['name']); // Recipient's email

                // Content
                $mail->isHTML(true);                                      // Set email format to HTML
                $mail->Subject = 'New Message from Contact Form';
                $mail->Body    = 'You have a new message from ' . $name . ' (' . $email . ')<br><br>' . nl2br($message);

                // Send the email
                $mail->send();
                $success = "✅ Message sent successfully!";
            } catch (Exception $e) {
                $error = "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $error = "❌ Something went wrong while sending your message.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Quiz 404 - Contact Us to connect and share your ideas or queries." />
    <title>Quiz 404 | Contact Us</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />

    <!-- Custom Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&family=Black+Ops+One&family=Major+Mono+Display&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
      html, body {
        font-family: 'Quicksand', sans-serif;
        background: radial-gradient(circle at 50% 50%, #1e293b, #000);
        color: white;
        min-height: 100vh;
        overflow: hidden;
        width: 100%;
        margin: 0;
        padding: 0;
      }
      canvas {
        position: fixed;
        top: 0;
        left: 0;
        z-index: -10;
        width: 100%;
        height: 100%;
        pointer-events: none;
      }
      * {
        box-sizing: border-box;
      }
      
      /* Nav Title Style */
      .nav-title {
        font-family: "Major Mono Display", monospace;
        font-size: 24px;
        padding: 10px 0;
        box-sizing: border-box;
      }
      
      .gradient-text {
        background: linear-gradient(45deg, #FFD700, #FFA500);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
      }
      
      .gradient-title {
        background: linear-gradient(45deg, #fbbf24, #3b82f6);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
      }
      
      .animate-float {
        animation: float 6s ease-in-out infinite;
      }
      
      @keyframes float {
        0%, 100% {
          transform: translateY(0);
        }
        50% {
          transform: translateY(-20px);
        }
      }
      
      .floating {
        animation: float 3s ease-in-out infinite;
      }
      
      .animate-pulse-glow {
        animation: pulse-glow 2s infinite;
      }
      
      @keyframes pulse-glow {
        0%, 100% { 
          opacity: 1;
          text-shadow: 0 0 20px rgba(255, 221, 0, 0.7);
        }
        50% { 
          opacity: 0.8;
          text-shadow: 0 0 40px rgba(255, 221, 0, 0.9);
        }
      }
      
      /* Auth button styles */
      .auth-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        color: white;
        font-size: 0;
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
      }
      
      .auth-btn.login {
        background: linear-gradient(45deg, #10b981, #34d399);
        animation: pulse 2s infinite;
      }
      
      .auth-btn.logout {
        background: linear-gradient(45deg, #ef4444, #dc2626);
      }
      
      @keyframes pulse {
        0% {
          transform: scale(1);
          box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }
        70% {
          transform: scale(1.05);
          box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
        }
        100% {
          transform: scale(1);
          box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
      }
      
      .auth-btn:hover {
        width: 120px;
        border-radius: 20px;
        font-size: 12px;
        gap: 6px;
      }
      
      .auth-btn:hover span {
        display: inline;
      }
      
      .auth-btn span {
        display: none;
      }
      
      /* Mobile menu styles */
      #mobile-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        width: 200px;
        background-color: rgba(0, 0, 0, 0.9);
        border-radius: 0 0 8px 8px;
        padding: 1rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      }
      
      #mobile-menu.active {
        display: block;
      }
      
      #mobile-menu a {
        display: block;
        padding: 0.5rem 0;
        color: white;
        transition: color 0.3s;
      }
      
      #mobile-menu a:hover {
        color: #fbbf24;
      }
      
      /* Stronger text outline on larger screens */
      @media (min-width: 768px) {
        .black-ops {
          text-shadow: 
            -2px -2px 0 #000,
            2px -2px 0 #000,
            -2px 2px 0 #000,
            2px 2px 0 #000;
        }
      }
    </style>
</head>

<body class="overflow-hidden">
<canvas id="particles" class="fixed top-0 left-0 w-full h-full pointer-events-none"></canvas>


    <!-- Updated Navbar -->
    <header class="fixed w-full z-50 bg-black/20 backdrop-blur-md">
    <nav class="container mx-auto px-4 py-4">
      <div class="flex justify-between items-center">
        <h1 class="nav-title gradient-text animate-pulse-glow">QUIZ 404</h1>
        <div class="flex items-center gap-8">
          <div class="hidden md:flex items-center gap-6">
            <a href="home.php" class="hover:text-yellow-400 transition-colors">Home</a>
            <a href="quiz.php" class="hover:text-yellow-400 transition-colors">Quiz</a>
            <a href="community.php" class="hover:text-yellow-400 transition-colors">Community</a>
            <a href="about.php" class="hover:text-yellow-400 transition-colors">About</a>
            <a href="contact.php" class="hover:text-yellow-400 transition-colors">Contact</a>
          </div>
          
          <!-- Auth Button - shows correct state automatically -->
          <?php renderAuthButton(); ?>
          </div>
          
            
            <!-- Mobile menu toggle -->
            <button id="menu-toggle" class="md:hidden text-yellow-400 p-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
              </svg>
            </button>
          </div>
        </div>
        
        <!-- Mobile menu -->
        <div id="mobile-menu" class="md:hidden absolute top-full right-0 w-48 bg-black/90 backdrop-blur-md rounded-b-lg shadow-lg py-2">
          <a href="home.php" class="block px-4 py-2 text-yellow-400 hover:bg-black/50">Home</a>
          <a href="quiz.php" class="block px-4 py-2 text-white hover:bg-black/50">Quiz</a>
          <a href="community.php" class="block px-4 py-2 text-white hover:bg-black/50">Community</a>
          <a href="about.php" class="block px-4 py-2 text-white hover:bg-black/50">About</a>
          <a href="contact.php" class="block px-4 py-2 text-white hover:bg-black/50">Contact</a>
        </div>
      </nav>
    </header>

    <!-- Contact Section -->
    <section class="px-6 pt-28 pb-4 max-w-2xl mx-auto h-[calc(100vh-96px)] flex flex-col justify-between relative z-10" data-aos="fade-up">
        <div>
            <h2 class="text-4xl font-bold text-center mb-2 text-purple-400 gradient-title">📡 Contact Us</h2>
            <p class="text-center text-gray-300 mb-6 text-sm">Have questions, suggestions, or cosmic ideas? Reach out to us!</p>
        </div>

        <!-- Success or Error Message -->
        <?php if ($success): ?>
            <div class="bg-green-600/20 border border-green-500 text-green-300 p-3 rounded mb-4 text-sm text-center">
                <?= $success ?>
            </div>
        <?php elseif ($error): ?>
            <div class="bg-red-600/20 border border-red-500 text-red-300 p-3 rounded mb-4 text-sm text-center">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="contact.php" method="POST" class="bg-black/10 p-6 rounded-2xl shadow-lg space-y-4">
            <!-- Add CSRF token input -->
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div>
                <label for="name" class="block mb-1 text-sm">Your Name</label>
                <input type="text" id="name" name="name" required class="w-full px-4 py-2 bg-black border border-white/20 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500" />
            </div>

            <div>
                <label for="email" class="block mb-1 text-sm">Your Email</label>
                <input type="email" id="email" name="email" required class="w-full px-4 py-2 bg-black border border-white/20 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500" />
            </div>

            <div>
                <label for="message" class="block mb-1 text-sm">Your Message</label>
                <textarea id="message" name="message" rows="3" required class="w-full px-4 py-2 bg-black border border-white/20 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
            </div>

            <button type="submit" class="bg-purple-600 hover:bg-purple-800 transition px-6 py-3 rounded-xl font-semibold w-full">
                🚀 Send Message
            </button>
        </form>
          <footer>
        <div class="text-center pt-5 mt-5 border-t border-[#34495e] text-[#bdc3c7] text-sm">
        <p>&copy; 2025 QUIZ 404. All Rights Reserved. | <a href="#" class="hover:text-[#f39c12] transition-colors duration-300">Privacy Policy</a> | <a href="#" class="hover:text-[#f39c12] transition-colors duration-300">Terms of Service</a></p>
    </div>
  </footer>
    </section>

    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
      AOS.init({ once: true, duration: 800, offset: 200 });
    </script>

    <!-- Particle Animation -->
    <script>
      const canvas = document.getElementById('particles');
      const ctx = canvas.getContext('2d');

      function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
      }
      resizeCanvas();
      window.addEventListener('resize', resizeCanvas);

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
        
        particles.forEach(particle => {
          particle.update();
          particle.draw();
        });

        requestAnimationFrame(animate);
      }

      // Start animation when the page loads
      window.addEventListener('load', () => {
        animate();
      });

      document.getElementById('menu-toggle').addEventListener('click', function() {
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenu.classList.toggle('active');
      });

      // Auth status simulation (replace with actual auth check)
      function checkAuthStatus() {
        const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
        const authStatus = document.getElementById('auth-status');
        const authBtn = document.querySelector('.auth-btn');
        
        if (isLoggedIn) {
          authStatus.classList.remove('logged-out');
          authStatus.classList.add('logged-in');
          authBtn.innerHTML = '<i class="fas fa-user-check"></i><span>Profile</span>';
          authBtn.href = 'profile.html';
        } else {
          authStatus.classList.remove('logged-in');
          authStatus.classList.add('logged-out');
          authBtn.innerHTML = '<i class="fas fa-user"></i><span>Login/Signup</span>';
          authBtn.href = 'login.html';
        }
      }

      // Check auth status on page load
      checkAuthStatus();

      // For demo purposes - add click handlers to simulate login/logout
      document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('.auth-btn').addEventListener('click', function(e) {
          if (this.href.includes('profile.html')) {
            e.preventDefault();
            localStorage.setItem('isLoggedIn', 'false');
            checkAuthStatus();
          }
        });
      });
    </script>
</body>
</html>
