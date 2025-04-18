<?php
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

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quiz 404 | About Us</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&family=Black+Ops+One&family=Major+Mono+Display&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Quicksand', sans-serif;
      background: radial-gradient(circle at 50% 50%, #1e293b, #000);
      color: white;
      overflow-x: hidden;
    }
    
    /* Nav Title Style */
    .nav-title {
      font-family: "Major Mono Display", monospace;
      font-size: 24px;
      padding: 10px 0;
      box-sizing: border-box;
    }
    
    /* Gradient text */
    .gradient-text {
      background: linear-gradient(45deg, #FFD700, #FFA500);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }
    
    /* Special font with black outline */
    .black-ops {
      font-family: 'Black Ops One', cursive;
      text-shadow: 
        -1px -1px 0 #000,
        1px -1px 0 #000,
        -1px 1px 0 #000,
        1px 1px 0 #000;
    }
    
    .gradient-title {
      background: linear-gradient(45deg, #fbbf24, #3b82f6);
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    
    .about-font {
      font-family: 'Black Ops One', sans-serif;
    }
    
    .animate-float {
      animation: float 6s ease-in-out infinite;
    }
    
    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-20px); }
    }
    
    .button-hover:hover {
      transform: scale(1.1);
      transition: transform 0.3s ease;
    }
    
    canvas {
      position: fixed;
      top: 0;
      left: 0;
      z-index: -1;
      pointer-events: none;
    }
    
    /* Glow animation */
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

    /* Team Button Styles */
    .team-btn {
      position: relative;
      overflow: hidden;
      background: linear-gradient(45deg, #FFD700, #FFA500);
      color: white;
      text-shadow: 0 2px 4px rgba(0,0,0,0.3);
      box-shadow: 0 4px 15px rgba(255,215,0,0.3);
      transition: all 0.3s ease;
    }

    .team-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(
        90deg,
        transparent,
        rgba(255,255,255,0.2),
        transparent
      );
      transition: 0.5s;
    }

    .team-btn:hover::before {
      left: 100%;
    }

    .team-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(255,215,0,0.4);
    }

    .team-btn:active {
      transform: translateY(1px);
    }

    @keyframes pulse {
      0% {
        box-shadow: 0 0 0 0 rgba(255,215,0,0.4);
      }
      70% {
        box-shadow: 0 0 0 10px rgba(255,215,0,0);
      }
      100% {
        box-shadow: 0 0 0 0 rgba(255,215,0,0);
      }
    }

    .team-btn {
      animation: pulse 2s infinite;
    }
  </style>
</head>
<body class="overflow-x-hidden">
  <canvas id="particles"></canvas>
  
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
          
          <!-- Auth Button - automatically shows correct state -->
          <?php renderAuthButton(); ?>
          
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

  <!-- Hero Section -->
  <section class="relative text-center min-h-screen flex items-center justify-center px-6 pt-24 animate-float">
    <div class="max-w-3xl mx-auto relative z-10">
      <h2 class="text-5xl sm:text-6xl font-extrabold mb-6 gradient-title about-font">
        About Quiz 404
      </h2>
      <p class="text-gray-300 text-lg mb-8">
        We're building a cosmic platform where curiosity meets competition. Learn, play, and grow in this galaxy of knowledge.
      </p>
      <a href="random.php" class="team-btn inline-block px-8 py-4 text-lg font-bold rounded-full transform transition-all duration-300 hover:scale-105 hover:shadow-lg">
        <span class="relative z-10">OUR TEAM</span>
        <span class="absolute inset-0 rounded-full bg-gradient-to-r from-purple-500 via-pink-500 to-red-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
        <span class="absolute inset-0 rounded-full bg-gradient-to-r from-yellow-400 via-orange-500 to-red-500"></span>
      </a>
    </div>
  </section>

  <!-- Our Mission -->
  <section class="py-16 px-6 bg-gradient-to-r from-gray-800 to-gray-900 rounded-lg shadow-lg mx-4 my-8 relative z-10">
    <div class="max-w-4xl mx-auto text-center p-8">
      <h3 class="text-4xl font-bold mb-6 gradient-title">Our Mission</h3>
      <p class="text-gray-300 text-lg leading-relaxed">
        At Quiz 404, we aim to redefine learning by blending education with fun, competition, and accessibility. 
        Our mission is to create a platform that gamifies knowledge, inspires curiosity, and unites passionate learners across the globe. 
        Together, we transform learning into a thrilling adventure.
      </p>
    </div>
  </section>

  <!-- Unique Features -->
  <section class="py-16 px-6 bg-black rounded-lg shadow-lg mx-4 my-8 relative z-10">
    <h3 class="text-4xl text-center font-bold mb-10 gradient-title">Why We're Different</h3>
    <div class="grid md:grid-cols-3 gap-12 max-w-6xl mx-auto">
      <div class="card p-6 text-center bg-gradient-to-br from-gray-800 to-gray-900 rounded-lg hover:scale-105 transform transition duration-300">
        <h4 class="text-2xl mb-3 text-yellow-400">Real-Time Quiz Battles</h4>
        <p class="text-gray-300 leading-relaxed">
          Challenge real players or intelligent bots in high-stakes quiz battles. Test your skills, beat the clock, and prove your knowledge supremacy!
        </p>
      </div>
      <div class="card p-6 text-center bg-gradient-to-br from-gray-800 to-gray-900 rounded-lg hover:scale-105 transform transition duration-300">
        <h4 class="text-2xl mb-3 text-yellow-400">Leaderboard & Rewards</h4>
        <p class="text-gray-300 leading-relaxed">
          Compete for the top spot on the leaderboard, earn exclusive rewards, and showcase your achievements to the world. Your knowledge earns you glory!
        </p>
      </div>
      <div class="card p-6 text-center bg-gradient-to-br from-gray-800 to-gray-900 rounded-lg hover:scale-105 transform transition duration-300">
        <h4 class="text-2xl mb-3 text-yellow-400">Ever-Growing Topics</h4>
        <p class="text-gray-300 leading-relaxed">
          Discover a diverse array of topics and quizzes updated regularly. From science to pop culture, there's always something new to explore.
        </p>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-[#2c3e50] text-[#ecf0f1] pt-10 pb-5 font-sans relative z-20">
    <div class="max-w-[1200px] mx-auto px-5 flex flex-wrap justify-between">
        <!-- About Us Section -->
        <div class="w-full md:w-1/4 min-w-[200px] mb-5 px-4">
            <h3 class="text-[#f39c12] mb-5 text-lg">About Us</h3>
            <p class="text-[#bdc3c7]">We are a company dedicated to providing the best service to our customers with quality products and support.</p>
        </div>
        
        <!-- Quick Links Section -->
        <div class="w-full md:w-1/4 min-w-[200px] mb-5 px-4">
            <h3 class="text-[#f39c12] mb-5 text-lg">Quick Links</h3>
            <ul class="space-y-2.5">
                <li><a href="home.php" class="text-[#bdc3c7] hover:text-[#f39c12] transition-colors duration-300">Home</a></li>
                <li><a href="quiz.php" class="text-[#bdc3c7] hover:text-[#f39c12] transition-colors duration-300">Quiz</a></li>
                <li><a href="community.php" class="text-[#bdc3c7] hover:text-[#f39c12] transition-colors duration-300">Community</a></li>
                <li><a href="about.php" class="text-[#bdc3c7] hover:text-[#f39c12] transition-colors duration-300">About</a></li>
                <li><a href="contact.php" class="text-[#bdc3c7] hover:text-[#f39c12] transition-colors duration-300">Contact</a></li>
            </ul>
        </div>
        
        <!-- Contact Info Section -->
        <div class="w-full md:w-1/4 min-w-[200px] mb-5 px-4">
            <h3 class="text-[#f39c12] mb-5 text-lg">Contact Info</h3>
            <ul class="space-y-2.5 text-[#bdc3c7]">
                <li>Lovely Professional University</li>
                <li>Email: harshiitthoon25@gmail.com</li>
                <li>Phone: +91 9140795875</li>
            </ul>
        </div>
        
        <!-- Follow Us Section -->
        <div class="w-full md:w-1/4 min-w-[200px] mb-5 px-4">
            <h3 class="text-[#f39c12] mb-5 text-lg">Follow Us</h3>
            <div class="flex gap-4 mb-4">
                <a href="#" class="text-white hover:text-[#f39c12] transition-colors duration-300 text-xl" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                <a href="#" class="text-white hover:text-[#f39c12] transition-colors duration-300 text-xl" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-white hover:text-[#f39c12] transition-colors duration-300 text-xl" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" class="text-white hover:text-[#f39c12] transition-colors duration-300 text-xl" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
            </div>
            <p class="text-[#bdc3c7] mb-2">Subscribe to our newsletter</p>
            <form class="flex">
                <input type="email" placeholder="Your email" class="px-3 py-2 text-gray-700 flex-grow">
                <button type="submit" class="bg-[#f39c12] hover:bg-[#e67e22] text-white px-4 py-2 transition-colors duration-300">Subscribe</button>
            </form>
        </div>
    </div>
    
    <!-- Footer Bottom -->
    <div class="text-center pt-5 mt-5 border-t border-[#34495e] text-[#bdc3c7] text-sm">
        <p>&copy; 2025 QUIZ 404. All Rights Reserved. | <a href="#" class="hover:text-[#f39c12] transition-colors duration-300">Privacy Policy</a> | <a href="#" class="hover:text-[#f39c12] transition-colors duration-300">Terms of Service</a></p>
    </div>
  </footer>

  <!-- Particle Animation Script -->
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
    animate();

    // Mobile menu toggle
    document.getElementById('menu-toggle').addEventListener('click', function() {
      const mobileMenu = document.getElementById('mobile-menu');
      mobileMenu.classList.toggle('active');
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
      const mobileMenu = document.getElementById('mobile-menu');
      const menuToggle = document.getElementById('menu-toggle');
      if (!mobileMenu.contains(event.target) && !menuToggle.contains(event.target)) {
        mobileMenu.classList.remove('active');
      }
    });
  </script>
  <!-- Font Awesome for icons -->
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>