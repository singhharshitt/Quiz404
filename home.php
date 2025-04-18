<?php
// Start session at the very beginning
session_start();

// Database connection with error handling
$conn = new mysqli("localhost", "root", "", "quiz404");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// List of public pages that don't require authentication
$public_pages = ['community.php', 'about.php', 'contact.php', 'index.php', 'auth.php'];

// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);

// If not logged in and trying to access a protected page
if (!in_array($current_page, $public_pages) && !isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: auth.php");
    exit();
}

// If trying to access index.php while logged in - redirect to home
if ($current_page === 'index.php' && isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

// Fetch user details if logged in
$user = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT username, email, fullname FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
    }
}

$conn->close();

// Function to render the auth button
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
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Quiz 404 - Explore your knowledge universe and engage in thrilling challenges with a community of curious minds." />
  <title>Quiz 404 | Home</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- AOS (Animate on Scroll) CSS -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />

  <!-- Custom Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&family=Black+Ops+One&family=Major+Mono+Display&display=swap" rel="stylesheet" />

  <style>
    body {
      font-family: 'Quicksand', sans-serif;
      background: radial-gradient(circle at 50% 50%, #1e293b, #000);
      color: white;
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

    /* Floating Animation */
    .animate-float {
      animation: float 6s ease-in-out infinite;
    }
    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-20px); }
    }

    /* Button hover effect */
    .button-hover:hover {
      transform: scale(1.1);
      transition: transform 0.3s ease;
    }

    /* Leaderboard Animated Container */
    .leaderboard-container {
      background-color: rgba(0, 0, 0, 0.6);
      padding: 40px;
      border-radius: 10px;
      text-align: center;
      animation: leaderboardAnimation 1s ease-in-out infinite alternate;
    }

    @keyframes leaderboardAnimation {
      0% { transform: scale(1); }
      100% { transform: scale(1.05); }
    }

    .canvas-container {
      position: absolute;
      top: 0;
      left: 0;
      z-index: -10;
      width: 100%;
      height: 100%;
    }

    canvas {
      position: absolute;
      top: 0;
      left: 0;
      z-index: -10;
    }
  
    /* Floating Animation */
    @keyframes float {
      0%, 100% {
        transform: translateY(0);
      }
      50% {
        transform: translateY(-10px);
      }
    }

    .floating {
      animation: float 3s ease-in-out infinite;
    }
    
    .title-font {
      font-family: 'Black Ops One', sans-serif;
    }

    @keyframes blink {
      0%, 100% { opacity: 1; }
      50% { opacity: 0; }
    }
    
    .animate-blink {
      animation: blink 1s infinite;
      display: inline-block;
      margin-left: 2px;
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

      /* Preloader Styles */
      .preloader {
      position: fixed;
      width: 100%;
      height: 100vh;
      background: rgb(188, 188, 188);
      z-index: 9999;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .preloader-circle {
      position: absolute;
      width: 400px;
      height: 400px;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      transform-style: preserve-3d;
      animation: rotate 10s ease-in-out infinite var(--delay);
    }
    .preloader-letter {
      position: absolute;
      display: block;
      font-size: 50px;
      font-family: poppins;
      font-weight: 900;
      color: white;
      text-shadow: 1px 1px 0 rgb(80, 80, 80);
    }
    .preloader-circle:nth-child(1) .preloader-letter {
      text-shadow: 1px 1px 0 black, 2px 2px 0 black, 2px 3px 0 black,
        2px 4px 0 black, 2px 5px 0 black;
    }
    .preloader-circle:nth-child(2) .preloader-letter {
      color: rgb(9, 201, 248);
      font-size: 47px;
    }
    .preloader-circle:nth-child(3) .preloader-letter {
      color: rgb(240, 194, 39);
      font-size: 44px;
    }
    .preloader-circle:nth-child(4) .preloader-letter {
      color: rgb(241, 89, 169);
      font-size: 41px;
    }
    .preloader-circle:nth-child(5) .preloader-letter {
      color: rgb(151, 0, 102);
      font-size: 38px;
    }
    .preloader-circle:nth-child(6) .preloader-letter {
      color: rgb(15, 11, 13);
      font-size: 35px;
      text-shadow: 5px 10px 0 rgba(0, 0, 0, 0.2);
    }
    .preloader-text {
      display: none;
    }
    @keyframes rotate {
      0%,
      100% {
        transform: rotate(0deg);
      }
      50% {
        transform: rotate(180deg);
      }
    }
  </style>
</head>
<body class="overflow-x-hidden">

<!-- Preloader -->
  <div class="preloader" id="preloader">
    <div class="preloader-circle" style="--delay: 0s">
      <span class="preloader-text">LOADING.HTML.CSS.JS.</span>
    </div>
    <div class="preloader-circle" style="--delay: 0.1s"></div>
    <div class="preloader-circle" style="--delay: 0.2s"></div>
    <div class="preloader-circle" style="--delay: 0.3s"></div>
    <div class="preloader-circle" style="--delay: 0.4s"></div>
    <div class="preloader-circle" style="--delay: 0.5s"></div>
  </div>
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
          
          <!-- Auth Button (Login/Signup or Logout) -->
          <?php renderAuthButton(); ?>
          
          <!-- Mobile menu button (hidden by default) -->
          <button id="mobile-menu-button" class="md:hidden text-white focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
          </button>
        </div>
      </div>
      
      <!-- Mobile menu (hidden by default) -->
      <div id="mobile-menu" class="md:hidden">
        <a href="home.php" class="block py-2 hover:text-yellow-400 transition-colors">Home</a>
        <a href="quiz.php" class="block py-2 hover:text-yellow-400 transition-colors">Quiz</a>
        <a href="community.php" class="block py-2 hover:text-yellow-400 transition-colors">Community</a>
        <a href="about.php" class="block py-2 hover:text-yellow-400 transition-colors">About</a>
        <a href="contact.php" class="block py-2 hover:text-yellow-400 transition-colors">Contact</a>
      </div>
    </nav>
  </header>

          
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
  <canvas id="particles" class="absolute inset-0 -z-10"></canvas>

  <div class="max-w-3xl mx-auto">
    
    <!-- Welcome Message (slightly above the main heading) -->
    <div id="welcome-message" 
         class="text-yellow-400 text-lg mb-6 font-mono overflow-hidden whitespace-nowrap"
         data-username="<?php echo htmlspecialchars($user['username']); ?>">
    </div>

    <!-- Main Heading -->
    <h2 class="text-5xl sm:text-6xl font-extrabold mb-6 gradient-title title-font">
      Explore Your Knowledge Universe
    </h2>

    <!-- Description -->
    <p class="text-gray-300 text-lg mb-8">
      Engage in thrilling challenges and connect with fellow knowledge enthusiasts across the cosmos.
    </p>

    <!-- Call-to-action Button -->
    <a href="community.php" class="inline-block px-6 py-3 bg-yellow-400 text-black font-semibold rounded-full button-hover">
      Join the Community
    </a>
  </div>
</section>


  <!-- How It Works -->
  <section class="bg-black/10 py-16 px-6 rounded-2xl mx-4 sm:mx-12" data-aos="fade-up">
    <h3 class="text-3xl font-bold text-center mb-6 gradient-title">How It Works</h3>
    <div class="grid sm:grid-cols-3 gap-10 text-center text-gray-300">
      <div 
        class="bg-black/50 p-6 rounded-lg  hover:scale-105 transition-transform duration-300">
        <h4 class="text-xl font-semibold mb-2">1. Choose Your Path</h4>
        <p>Select topics you love to kickstart your adventure.</p>
      </div>
      <div 
        class="bg-black/50 p-6 rounded-lg  hover:scale-105 transition-transform duration-300">
        <h4 class="text-xl font-semibold mb-2">2. Challenge Others</h4>
        <p>Compete in exciting battles with friends or bots.</p>
      </div>
      <div 
        class="bg-black/50 p-6 rounded-lg  hover:scale-105 transition-transform duration-300">
        <h4 class="text-xl font-semibold mb-2">3. Win Rewards</h4>
        <p>Earn badges, climb the leaderboard, and shine bright.</p>
      </div>
    </div>
  </section>

 <!-- Featured Categories -->
<section class="py-20 px-6" data-aos="fade-up">
  <h3 class="text-3xl font-bold text-center mb-8 gradient-title">Featured Categories</h3>
  <div class="grid sm:grid-cols-4 gap-6">
    <div class="p-6 bg-gray-800 rounded-xl shadow-lg text-center transition transform hover:scale-100 hover:shadow-2xl" data-aos="flip-left">
      <h4 class="text-xl font-semibold mb-2">Technology</h4>
      <a href="quiz.php?category=tech" class="text-sm text-yellow-300 hover:underline">Play Now →</a>
    </div>
    <div class="p-6 bg-gray-800 rounded-xl shadow-lg text-center transition transform hover:scale-100 hover:shadow-2xl" data-aos="flip-left" data-aos-delay="200">
      <h4 class="text-xl font-semibold mb-2">Culture</h4>
      <a href="quiz.php?category=culture" class="text-sm text-yellow-300 hover:underline">Play Now →</a>
    </div>
    <div class="p-6 bg-gray-800 rounded-xl shadow-lg text-center transition transform hover:scale-100 hover:shadow-2xl" data-aos="flip-left" data-aos-delay="400">
      <h4 class="text-xl font-semibold mb-2">Science</h4>
      <a href="quiz.php?category=science" class="text-sm text-yellow-300 hover:underline">Play Now →</a>
    </div>
    <div class="p-6 bg-gray-800 rounded-xl shadow-lg text-center transition transform hover:scale-100 hover:shadow-2xl" data-aos="flip-left" data-aos-delay="600">
      <h4 class="text-xl font-semibold mb-2">History</h4>
      <a href="quiz.php?category=history" class="text-sm text-yellow-300 hover:underline">Play Now →</a>
    </div>
  </div>
</section>

<!-- Leaderboard Section -->
<section class="bg-black/10 py-16 px-6 rounded-2xl mx-4 sm:mx-12" data-aos="fade-right">
  <div class="max-w-md mx-auto bg-gradient-to-r from-gray-800 via-gray-900 to-gray-800 p-6 rounded-2xl shadow-lg floating">
    <h3 class="text-2xl font-bold text-center gradient-title font-family-['Black Ops One']">Top Minds of the Week</h3>
    <div class="bg-black/60 p-4 rounded-lg mt-4 transition transform hover:scale-110">
      <ul class="space-y-2 text-center text-gray-300">
        <li class="text-lg">🥇 <span class="text-yellow-400">GalaxyGuru</span> - 12,400 pts</li>
        <li class="text-lg">🥈 <span class="text-gray-400">AstroAce</span> - 11,500 pts</li>
        <li class="text-lg">🥉 <span class="text-orange-400">StarWhiz</span> - 10,200 pts</li>
      </ul>
    </div>
    <a href="leaderboard.php" 
       class="block mt-6 px-6 py-3 text-center bg-yellow-400 text-black text-lg font-semibold rounded-full hover:bg-yellow-500 transition transform hover:scale-105">
      Go to the Leaderboard
    </a>
  </div>
</section>


  <!-- Footer -->
  <footer class="bg-[#2c3e50] text-[#ecf0f1] pt-10 pb-5 font-sans relative z-10">
    <div class="max-w-[1200px] mx-auto px-5 flex flex-wrap justify-between">
        <!-- About Us Section - Shifted left with negative margin -->
        <div class="w-full md:w-1/4 min-w-[200px] mb-5 md:-ml-4 px-4">
            <h3 class="text-[#f39c12] mb-5 text-lg">About Us</h3>
            <p class="text-[#bdc3c7]">We are a company dedicated to providing the best service to our customers with quality products and support.</p>
        </div>
        
        <!-- Quick Links Section -->
        <div class="w-full md:w-1/4 min-w-[200px] mb-5 px-4">
            <h3 class="text-[#f39c12] mb-5 text-lg">Quick Links</h3>
            <ul class="space-y-2.5">
                <li><a href="home.html" class="text-[#bdc3c7] hover:text-[#f39c12] transition-colors duration-300">Home</a></li>
                <li><a href="quiz.html" class="text-[#bdc3c7] hover:text-[#f39c12] transition-colors duration-300">Quiz</a></li>
                <li><a href="community.html" class="text-[#bdc3c7] hover:text-[#f39c12] transition-colors duration-300">Community</a></li>
                <li><a href="about.html" class="text-[#bdc3c7] hover:text-[#f39c12] transition-colors duration-300">About</a></li>
                <li><a href="contact.html" class="text-[#bdc3c7] hover:text-[#f39c12] transition-colors duration-300">Contact</a></li>
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
        
        <!-- Follow Us Section - Shifted right with positive margin -->
        <div class="w-full md:w-1/4 min-w-[200px] mb-5 md:mr-4 px-4">
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

  <!-- AOS.js -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({
      once: true, // Animations play once on scroll
      duration: 800, // Animation duration
      offset: 200, // Offset from the viewport
    });
    document.getElementById("menu-toggle").addEventListener("click", () => {
      const menu = document.getElementById("menu");
      menu.classList.toggle("hidden");
    });

    // Particle Animation
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
    document.getElementById('menu-toggle').addEventListener('click', function() {
      const mobileMenu = document.getElementById('mobile-menu');
      mobileMenu.classList.toggle('active');
    });

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

    // Preloader initialization
    let preloaderText = document.querySelector(".preloader-text");
    let preloaderCircles = document.querySelectorAll(".preloader-circle");
    let y = 0;
    
    if (preloaderText) {
      let count = Math.floor(360 / preloaderText.textContent.length);
      
      preloaderCircles.forEach((circle) => {
        let j = 0;
        for (var i = 0; i < 360; i += count) {
          let letter = document.createElement("span");
          letter.className = "preloader-letter";
          if (preloaderText.textContent[j]) {
            letter.innerHTML = preloaderText.textContent[j];
            j = j + 1;
          }
          letter.style.transform = `rotate(${i}deg) translateY(${-150 + y}px)`;
          circle.appendChild(letter);
        }
        circle.style.zIndex = 1000 - y;
        y = y + 10;
      });
    }

    // Hide preloader when page is loaded
    window.addEventListener('load', function() {
      setTimeout(function() {
        document.getElementById('preloader').style.opacity = '0';
        setTimeout(function() {
          document.getElementById('preloader').style.display = 'none';
        }, 500);
      }, 1500); // Adjust this time to control how long the preloader shows
    });


    const welcomeElement = document.getElementById('welcome-message');
        const username = welcomeElement.dataset.username;
        const welcomeText = `Welcome, ${username}!`;
        let i = 0;
        
        function typeWriter() {
            if (i < welcomeText.length) {
                welcomeElement.innerHTML += welcomeText.charAt(i);
                i++;
                setTimeout(typeWriter, 100); // Adjust speed here (milliseconds)
            } else {
                // Add blinking cursor effect after typing completes
                welcomeElement.innerHTML += '<span class="animate-blink">|</span>';
            }
        }
        
        // Start the animation when page loads
        document.addEventListener('DOMContentLoaded', typeWriter);
  </script>
</body>
</html> 