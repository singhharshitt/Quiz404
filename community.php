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
  <title>Quiz 404 | Community</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&family=Black+Ops+One&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Major+Mono+Display&display=swap" rel="stylesheet">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
    .black-ops {
      font-family: 'Black Ops One', cursive;
      text-shadow: 
        -1px -1px 0 #000,
        1px -1px 0 #000,
        -1px 1px 0 #000,
        1px 1px 0 #000;
    }
    
    /* Gradient text */
    .gradient-text {
      background: linear-gradient(45deg, #FFD700, #FFA500);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }
    .title-font {
      font-family: 'Black Ops One', sans-serif;
    }
    .floating {
      animation: floating 3s ease-in-out infinite;
    }
    @keyframes floating {
      0% { transform: translate(0, 0px); }
      50% { transform: translate(0, 15px); }
      100% { transform: translate(0, -0px); }
    }
    .hero-glow {
      position: absolute;
      width: 600px;
      height: 600px;
      background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, rgba(251, 191, 36, 0) 70%);
      border-radius: 50%;
      pointer-events: none;
      z-index: -1;
    }
    .section-gap {
      margin-top: 8rem;
      margin-bottom: 8rem;
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
<body>

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
  <!-- Particle Animation Canvas -->
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


  <!-- Hero Section -->
  <section id="home" class="relative min-h-screen flex items-center justify-center px-6 pt-24">
    <div class="hero-glow floating"></div>
    <div class="container mx-auto text-center" data-aos="fade-up" data-aos-duration="1000">
      <div class="floating">
        <h2 class="text-5xl sm:text-7xl font-extrabold mb-8 gradient-title title-font leading-tight">
          Join a Galaxy of<br>Knowledge Seekers
        </h2>
        <p class="text-xl md:text-2xl text-gray-300 max-w-3xl mx-auto mb-12">
          Be part of an amazing community driven by the quest for learning and fun. Challenge yourself and others today!
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-6 mt-10">
          <a href="https://discord.gg/SADwW2pYKd" class="bg-teal-500 text-black font-semibold py-4 px-8 rounded-full hover:scale-105 transform transition-all duration-300 text-lg">
            ✯ Join Now
          </a>
          <a href="https://discord.gg/SADwW2pYKd" class="border-2 border-teal-400 text-teal-400 py-4 px-8 rounded-full hover:bg-teal-500 hover:text-black hover:border-transparent transform transition-all duration-300 text-lg">
            📅 Explore Events
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Events Section -->
  
  <section id="events" class="section-gap py-20 bg-gradient-to-t from-gray-800 to-transparent">
    <div class="container mx-auto px-6">
      <div class="text-center mb-16" data-aos="fade-up">
        <h3 class="text-4xl font-bold gradient-title mb-4">Upcoming Events</h3>
        <p class="text-gray-400 text-lg max-w-2xl mx-auto">
          Stay updated with our latest challenges and trivia nights.
        </p>
      </div>
      <div class="relative overflow-hidden">
        <!-- Event Items Container -->
        <div id="event-container" class="flex gap-8 transition-transform duration-300">
          <!-- Individual Event Cards -->
          <div class="bg-black/50 p-8 rounded-xl flex-none w-[calc(33.333%-16px)]" data-aos="fade-up" data-aos-delay="100">
            <h4 class="text-2xl font-bold text-teal-400 mb-4">Trivia Night</h4>
            <p class="text-gray-300 text-lg">Date: April 20, 2025</p>
            <p class="text-gray-300 text-lg">Time: 7:00 PM (GMT)</p>
          </div>
          <div class="bg-black/50 p-8 rounded-xl flex-none w-[calc(33.333%-16px)]" data-aos="fade-up" data-aos-delay="200">
            <h4 class="text-2xl font-bold text-teal-400 mb-4">Weekly Challenge</h4>
            <p class="text-gray-300 text-lg">Theme: Space Exploration</p>
            <p class="text-gray-300 text-lg">Ends: April 22, 2025</p>
          </div>
          <div class="bg-black/50 p-8 rounded-xl flex-none w-[calc(33.333%-16px)]" data-aos="fade-up" data-aos-delay="300">
            <h4 class="text-2xl font-bold text-teal-400 mb-4">Coding Marathon</h4>
            <p class="text-gray-300 text-lg">Date: April 25, 2025</p>
            <p class="text-gray-300 text-lg">Time: 10:00 AM - 6:00 PM (GMT)</p>
          </div>
          <div class="bg-black/50 p-8 rounded-xl flex-none w-[calc(33.333%-16px)]" data-aos="fade-up" data-aos-delay="400">
            <h4 class="text-2xl font-bold text-teal-400 mb-4">Quiz Master Tournament</h4>
            <p class="text-gray-300 text-lg">Date: April 30, 2025</p>
            <p class="text-gray-300 text-lg">Time: 5:00 PM (GMT)</p>
          </div>
          <!-- Add more events as needed -->
        </div>
  
        <!-- Navigation Buttons -->
        <button id="prev-btn" class="absolute top-1/2 left-4 transform -translate-y-1/2 bg-teal-500 text-black p-3 rounded-full shadow-lg hover:bg-teal-400 hidden">
          &#8592;
        </button>
        <button id="next-btn" class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-teal-500 text-black p-3 rounded-full shadow-lg hover:bg-teal-400">
          &#8594;
        </button>
      </div>
    </div>
  </section>
  


  <!-- Community Highlights -->
  <section class="section-gap py-20 px-6 bg-gray-900/50">
    <div class="container mx-auto">
      <div class="text-center mb-16" data-aos="fade-up">
        <h3 class="text-4xl font-bold gradient-title mb-4">Community Highlights</h3>
        <p class="text-gray-400 text-lg max-w-2xl mx-auto">Celebrating our community's achievements.</p>
      </div>
      <div class="relative overflow-hidden">
        <!-- Highlights Container -->
        <div id="highlights-container" class="flex gap-8 transition-transform duration-300">
          <div class="bg-black/50 p-8 rounded-xl flex-none w-[calc(33.333%-16px)] transform hover:scale-105 transition-transform duration-300" data-aos="fade-up" data-aos-delay="100">
            <h4 class="text-2xl font-bold text-teal-400 mb-4">Top Player: Sarah</h4>
            <p class="text-gray-300 text-lg">Score: 15,000 points</p>
          </div>
          <div class="bg-black/50 p-8 rounded-xl flex-none w-[calc(33.333%-16px)] transform hover:scale-105 transition-transform duration-300" data-aos="fade-up" data-aos-delay="200">
            <h4 class="text-2xl font-bold text-teal-400 mb-4">Most Active User: John</h4>
            <p class="text-gray-300 text-lg">Posts: 120</p>
          </div>
          <div class="bg-black/50 p-8 rounded-xl flex-none w-[calc(33.333%-16px)] transform hover:scale-105 transition-transform duration-300" data-aos="fade-up" data-aos-delay="300">
            <h4 class="text-2xl font-bold text-teal-400 mb-4">Event Winner: Amy</h4>
            <p class="text-gray-300 text-lg">Won the Spring Challenge 2023</p>
          </div>
          <div class="bg-black/50 p-8 rounded-xl flex-none w-[calc(33.333%-16px)] transform hover:scale-105 transition-transform duration-300" data-aos="fade-up" data-aos-delay="400">
            <h4 class="text-2xl font-bold text-teal-400 mb-4">Top Contributor: David</h4>
            <p class="text-gray-300 text-lg">Contributions: 50 Guides Published</p>
          </div>
          <div class="bg-black/50 p-8 rounded-xl flex-none w-[calc(33.333%-16px)] transform hover:scale-105 transition-transform duration-300" data-aos="fade-up" data-aos-delay="500">
            <h4 class="text-2xl font-bold text-teal-400 mb-4">Team Achievement</h4>
            <p class="text-gray-300 text-lg">Community hit 1 Million Members</p>
          </div>
        </div>
  
        <!-- Navigation Buttons -->
        <button id="highlights-prev-btn" class="absolute top-1/2 left-4 transform -translate-y-1/2 bg-teal-500 text-black p-3 rounded-full shadow-lg hover:bg-teal-400 hidden">
          &#8592;
        </button>
        <button id="highlights-next-btn" class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-teal-500 text-black p-3 rounded-full shadow-lg hover:bg-teal-400">
          &#8594;
        </button>
      </div>
    </div>
  </section>
  

  <!-- FAQs -->
  <section id="faqs" class="section-gap py-20 px-6 bg-gradient-to-t from-black to-gray-800">
    <div class="container mx-auto">
      <div class="text-center mb-16" data-aos="fade-up">
        <h3 class="text-4xl font-bold gradient-title mb-4">Frequently Asked Questions</h3>
        <p class="text-gray-400 text-lg max-w-2xl mx-auto">Got questions? We have answers.</p>
      </div>
      <div class="relative overflow-hidden">
        <!-- FAQs Container -->
        <div id="faqs-container" class="flex gap-8 transition-transform duration-300">
          <div class="bg-black/50 p-8 rounded-xl flex-none w-[calc(33.333%-16px)] transform hover:scale-105 transition-transform duration-300" data-aos="fade-up" data-aos-delay="100">
            <h4 class="text-2xl font-bold text-teal-400 mb-4">How do I join?</h4>
            <p class="text-gray-300 text-lg">Click the "Join Now" button and create an account to start participating.</p>
          </div>
          <div class="bg-black/50 p-8 rounded-xl flex-none w-[calc(33.333%-16px)] transform hover:scale-105 transition-transform duration-300" data-aos="fade-up" data-aos-delay="200">
            <h4 class="text-2xl font-bold text-teal-400 mb-4">Is it free?</h4>
            <p class="text-gray-300 text-lg">Yes, joining the community is completely free!</p>
          </div>
          <div class="bg-black/50 p-8 rounded-xl flex-none w-[calc(33.333%-16px)] transform hover:scale-105 transition-transform duration-300" data-aos="fade-up" data-aos-delay="300">
            <h4 class="text-2xl font-bold text-teal-400 mb-4">What activities are available?</h4>
            <p class="text-gray-300 text-lg">You can participate in events, challenges, forums, and much more.</p>
          </div>
          <div class="bg-black/50 p-8 rounded-xl flex-none w-[calc(33.333%-16px)] transform hover:scale-105 transition-transform duration-300" data-aos="fade-up" data-aos-delay="400">
            <h4 class="text-2xl font-bold text-teal-400 mb-4">How do I earn points?</h4>
            <p class="text-gray-300 text-lg">Earn points by completing tasks, challenges, and being active in the community.</p>
          </div>
          <div class="bg-black/50 p-8 rounded-xl flex-none w-[calc(33.333%-16px)] transform hover:scale-105 transition-transform duration-300" data-aos="fade-up" data-aos-delay="500">
            <h4 class="text-2xl font-bold text-teal-400 mb-4">Is there a mobile app?</h4>
            <p class="text-gray-300 text-lg">Yes, download the app from the App Store or Google Play.</p>
          </div>
          <div class="bg-black/50 p-8 rounded-xl flex-none w-[calc(33.333%-16px)] transform hover:scale-105 transition-transform duration-300" data-aos="fade-up" data-aos-delay="600">
            <h4 class="text-2xl font-bold text-teal-400 mb-4">Can I create a private group?</h4>
            <p class="text-gray-300 text-lg">Yes, create a group from your dashboard and invite members to join.</p>
          </div>
        </div>
  
        <!-- Navigation Buttons -->
        <button id="faqs-prev-btn" class="absolute top-1/2 left-4 transform -translate-y-1/2 bg-teal-500 text-black p-3 rounded-full shadow-lg hover:bg-teal-400 hidden">
          &#8592;
        </button>
        <button id="faqs-next-btn" class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-teal-500 text-black p-3 rounded-full shadow-lg hover:bg-teal-400">
          &#8594;
        </button>
      </div>
    </div>
  </section>
  

  <!-- Footer -->
  <footer class="bg-[#2c3e50] text-[#ecf0f1] pt-10 pb-5 font-sans relative z-10">
    <div class="max-w-[1200px] mx-auto px-5 flex flex-wrap justify-between">
        <!-- About Us Section - Shifted left with negative margin -->
        <div class="w-full md:w-1/4 min-w-[200px] mb-5 md:-ml-4 px-4">
            <h3 class="text-[#f39c12] mb-5 text-lg">About Us</h3>
            <p class="text-[#bdc3c7]">We are a dedicated Students providing the best service to our customers with quality services.</p>
        </div>
        
        <!-- Quick Links Section -->
        <div class="w-full md:w-1/4 min-w-[200px] mb-5 px-4">
            <h3 class="text-[#f39c12] mb-5 text-lg">Quick Links</h3>
            <ul class="space-y-2.5">
              <li><a href="home.php" class="text-[#bdc3c7] hover:text-[#f39c12] transition-colors duration-300">Home</a></li>
              <li><a href="auth.php" class="text-[#bdc3c7] hover:text-[#f39c12] transition-colors duration-300">Quiz</a></li>
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

  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>

//preloader animation
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
  }, 4000); // Adjust this time to control how long the preloader shows
});
    // Function to initialize carousel for any section
// Function to initialize carousel navigation
function initCarousel(containerId, prevBtnId, nextBtnId, visibleItems = 3) {
  const container = document.getElementById(containerId);
  const prevBtn = document.getElementById(prevBtnId);
  const nextBtn = document.getElementById(nextBtnId);

  let currentIndex = 0; // Start position
  const items = container.children; // Get all cards
  const totalItems = items.length;
  const itemWidth = items[0].getBoundingClientRect().width + 16; // Include margin

  // Set container width dynamically
  container.style.width = `${totalItems * itemWidth}px`;

  // Function to update button visibility
  function updateButtons() {
    prevBtn.classList.toggle('hidden', currentIndex === 0);
    nextBtn.classList.toggle('hidden', currentIndex >= totalItems - visibleItems);
  }

  // Move to the next group
  nextBtn.addEventListener('click', () => {
    if (currentIndex < totalItems - visibleItems) {
      currentIndex++;
      container.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
    }
    updateButtons();
  });

  // Move to the previous group
  prevBtn.addEventListener('click', () => {
    if (currentIndex > 0) {
      currentIndex--;
      container.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
    }
    updateButtons();
  });

  // Initialize button visibility
  updateButtons();
}

// Initialize carousels for all sections
document.addEventListener('DOMContentLoaded', () => {
  initCarousel('event-container', 'prev-btn', 'next-btn'); // Events Section
  initCarousel('highlights-container', 'highlights-prev-btn', 'highlights-next-btn'); // Community Highlights
  initCarousel('faqs-container', 'faqs-prev-btn', 'faqs-next-btn'); // FAQs Section
});


     

    AOS.init({
      duration: 1000,
      once: true,
      offset: 100
    });

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
        this.alpha = Math.random() * 0.5 + 0.1;
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
        ctx.fillStyle = `rgba(255, 255, 255, ${this.alpha})`;
        ctx.fill();
      }
    }
    
    const particles = Array.from({ length: 100 }, () => new Particle());
    
    function animate() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      particles.forEach((particle) => {
        particle.update();
        particle.draw();
      });
      requestAnimationFrame(animate);
    }
    
    animate();




    document.getElementById('menu-toggle').addEventListener('click', function() {
      const mobileMenu = document.getElementById('mobile-menu');
      mobileMenu.classList.toggle('active');
    });

    // Auth status simulation (replace with actual auth check)
    // For demo purposes, we'll use localStorage to simulate login state
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
      // This would be replaced with actual login/logout functionality
      document.querySelector('.auth-btn').addEventListener('click', function(e) {
        if (this.href.includes('profile.html')) {
          // Simulate logout
          e.preventDefault();
          localStorage.setItem('isLoggedIn', 'false');
          checkAuthStatus();
        }
      });
    });
  </script>
</body>
</html>

