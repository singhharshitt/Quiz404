<?php
session_start();

// If user clicks any protected link
if (isset($_GET['redirect'])) {
    // Check if user is already logged in
    if (isset($_SESSION['logged_in'])) {
        header("Location: home.php"); // Send to dashboard if logged in
    } else {
        header("Location: auth.php"); // Send to auth if not logged in
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quiz 404 - Test Your Knowledge</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Black+Ops+One&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Major+Mono+Display&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Cutive+Mono&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <!-- Add GSAP CDN -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/gsap.min.js"></script>
  <style>
    body {
      font-family: 'Quicksand', sans-serif;
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
    header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 50;
      background-color: rgba(0, 0, 0, 0.3);
      backdrop-filter: blur(12px);
      padding: 1rem 0;
      transition: all 0.3s ease;
    }
    header.scrolled {
      background-color: rgba(0, 0, 0, 0.7);
      padding: 0.5rem 0;
      box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    }
    .nav-link {
      position: relative;
      padding: 0.5rem 0;
      transition: color 0.3s ease;
      color: white;
      text-decoration: none;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 1px;
      font-size: 0.875rem;
    }
    .nav-link::after {
      content: '';
      position: absolute;
      width: 0;
      height: 1px;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      background-color: #f39c12;
      transition: width 0.3s ease;
    }
    .nav-link:hover::after,
    .nav-link.active::after {
      width: 70%;
    }
    .nav-link:hover {
      color: #f39c12;
    }
    .nav-link.active {
      color: #f39c12;
    }
    /* Floating animation */
    .animate-float {
      animation: float 6s ease-in-out infinite;
    }
    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-20px); }
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
    
    /* Particle canvas */
    canvas {
      position: absolute;
      top: 0;
      left: 0;
      z-index: -10;
    }
    
    /* Expanded main section */
    .main-hero {
      min-height: 120vh;
      padding-top: 6rem;
      padding-bottom: 6rem;
    }
    
    /* Space animation elements */
    .space-container {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      overflow: visible;
      z-index: 0;
      pointer-events: none;
    }
    
    .space-blob {
      background-color: rgba(0, 0, 0, 0.3);
      position: absolute;
      border-radius: 50%;
      filter: blur(1px);
      animation-duration: 20s;
      animation-iteration-count: infinite;
      animation-timing-function: linear;
    }
    
    .space-img {
      position: absolute;
      z-index: 1;
    }
    
    @keyframes typing {
      from { width: 0 }
      to { width: 100% }
    }
    
    @keyframes blink {
      0%, 100% { opacity: 0 }
      50% { opacity: 1 }
    }
    
    /* Blob sizes with adjusted positions */
    .blob-1 {
      width: 220px;
      height: 260px;
      top: 5%;
      left: 5%;
      animation-name: blob-anim-1;
    }
    
    .blob-2 {
      width: 300px;
      height: 295px;
      top: 0%;
      right: 5%;
      animation-name: blob-anim-2;
    }
    
    .blob-3 {
      width: 280px;
      height: 270px;
      bottom: 10%;
      left: 5%;
      animation-name: blob-anim-3;
    }
    
    .blob-4 {
      width: 380px;
      height: 370px;
      right: 3%; /* Moved further right */
      bottom: 8%; /* Moved further down */
      animation-name: blob-anim-4;
    }
    
    /* Image sizes with adjusted positions */
    .spacecraft {
      width: 150px;
      top: 15%;
      right: 10%;
    }
    
    .planet-1 {
      width: 180px;
      bottom: 25%;
      left: 10%;
    }
    
    .planet-2 {
      width: 150px;
      top: 25%;
      right: 15%;
    }
    
    .astronaut {
      width: 280px;
      left: 3%; /* Moved further left */
      bottom: 12%; /* Moved further down */
    }
    
    .ufo {
      width: 220px;
      left: 50%;
      transform: translateX(-50%);
      top: 60%;
    }
    
    /* Blob keyframe animations */
    @keyframes blob-anim-1 {
      0%, 100% { border-radius: 38% 62% 66% 34%/60% 41% 59% 40%; }
      33% { border-radius: 59% 41% 55% 45%/64% 22% 78% 36%; }
      66% { border-radius: 59% 41% 35% 65%/46% 67% 33% 54%; }
    }
    
    @keyframes blob-anim-2 {
      0%, 100% { border-radius: 59% 41% 19% 81%/34% 72% 28% 66%; }
      33% { border-radius: 78% 22% 20% 80%/53% 46% 54% 47%; }
      66% { border-radius: 57% 43% 40% 60%/49% 34% 66% 51%; }
    }
    
    @keyframes blob-anim-3 {
      0%, 100% { border-radius: 57% 43% 40% 60%/49% 34% 66% 51%; }
      33% { border-radius: 51% 49% 30% 70%/37% 63% 37% 63%; }
      66% { border-radius: 51% 49% 28% 72%/77% 75% 25% 23%; }
    }
    
    @keyframes blob-anim-4 {
      0%, 100% { border-radius: 78% 22% 20% 80%/53% 46% 54% 47%; }
      33% { border-radius: 65% 35% 42% 58%/64% 28% 72% 36%; }
      66% { border-radius: 32% 68% 46% 54%/29% 22% 78% 71%; }
    }
    
    /* Button click effect styles */
    .click-effect {
      position: absolute;
      pointer-events: none;
      transform: translate(-50%, -50%);
      z-index: 9999;
    }
    
    .spike {
      position: absolute;
      background: radial-gradient(circle, rgba(255,215,0,0.8) 0%, rgba(255,140,0,0) 70%);
      width: 15px;
      height: 15px;
      border-radius: 50%;
      opacity: 0;
      transform: scale(0);
    }
    
    .effect .spike {
      animation: spike-anim 0.75s ease-out forwards;
    }
    
    @keyframes spike-anim {
      0% {
        opacity: 1;
        transform: scale(0) translate(0, 0);
      }
      100% {
        opacity: 0;
        transform: scale(1) translate(
          calc(cos(var(--angle)) * var(--distance)),
          calc(sin(var(--angle)) * var(--distance))
        );
      }
    }
    
    /* Responsive adjustments */
    @media (min-width: 768px) {
      .main-hero {
        min-height: 140vh;
      }
      
      .blob-1 {
        width: 250px;
        height: 300px;
        top: 10%;
        left: 10%;
      }
      
      .blob-2 {
        width: 350px;
        height: 345px;
        top: 5%;
        right: 10%;
      }
      
      .blob-3 {
        width: 320px;
        height: 310px;
        bottom: 15%;
        left: 10%;
      }
      
      .blob-4 {
        width: 420px;
        height: 410px;
        right: 5%; /* Adjusted for desktop */
        bottom: 10%; /* Adjusted for desktop */
      }
      
      .spacecraft {
        width: 180px;
        top: 20%;
        right: 15%;
      }
      
      .planet-1 {
        width: 220px;
        bottom: 30%;
        left: 15%;
      }
      
      .planet-2 {
        width: 180px;
        top: 30%;
        right: 20%;
      }
      
      .astronaut {
        width: 320px;
        left: 5%; /* Adjusted for desktop */
        bottom: 18%; /* Adjusted for desktop */
      }
      
      .ufo {
        width: 250px;
        top: 65%;
      }
      
      /* Stronger text outline on larger screens */
      .black-ops {
        text-shadow: 
          -2px -2px 0 #000,
          2px -2px 0 #000,
          -2px 2px 0 #000,
          2px 2px 0 #000;
      }
    }

    /* Preloader styles */
    /* Preloader styles */
    .preloader {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: black;
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
      transition: opacity 0.5s, visibility 0.5s;
    }
    
    .preloader .center {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      background: black;
    }
    
    .preloader .rain {
      position: absolute;
      top: 0;
      left: 50%;
      transform: translate(-50%, 0);
      width: 250px;
      height: 100%;
    }
    
    .preloader .drop {
      width: 2px;
      height: fit-content;
      border-left: 3px solid transparent;
      border-right: 3px solid transparent;
      border-bottom: 50px solid white;
      border-radius: 2mm;
      position: absolute;
      top: calc(100% - 150px);
      left: 50%;
      animation: fall var(--duration) ease-in var(--delay) infinite backwards;
    }
    
    @keyframes fall {
      0% {
        transform: translateY(-150vh);
      }
      45% {
        transform: translateY(0%);
        opacity: 1;
      }
      46% {
        opacity: 0;
      }
      100% {
        opacity: 0;
      }
    }
    
    .preloader .ripples {
      width: 100%;
      height: 100px;
      position: absolute;
      bottom: 10px;
      left: 50%;
      transform: translate(-50%, 0);
    }
    
    .preloader .ripple {
      display: block;
      width: 100%;
      height: 80px;
      border-radius: 50%;
      border: 1.5mm solid white;
      position: absolute;
      animation: spread var(--duration) ease-out var(--delay) infinite backwards;
    }
    
    .preloader .ripple:nth-child(2) {
      animation-delay: calc(var(--delay) + 0.4s);
    }
    
    @keyframes spread {
      0% {
        transform: scale(0);
        opacity: 1;
      }
      40% {
        transform: scale(0);
        opacity: 1;
      }
      100% {
        transform: scale(1);
        opacity: 0;
      }
    }
    
    .preloader .splash {
      position: absolute;
      bottom: 60px;
      left: 50%;
      width: 50px;
      height: 50px;
      transform: translate(-50%, 0);
      border-radius: 8px;
      clip-path: polygon(
        7% 100%,
        5% 95%,
        3% 80%,
        11% 50%,
        17% 38%,
        23% 44%,
        30% 53%,
        37% 28%,
        40% 29%,
        45% 43%,
        51% 53%,
        59% 36%,
        64% 22%,
        67% 23%,
        70% 34%,
        72% 46%,
        79% 37%,
        83% 37%,
        93% 61%,
        96% 76%,
        96% 94%,
        94% 100%
      );
      background: white;
      transform-origin: bottom;
      animation: watersplash var(--duration) ease-out var(--delay) infinite backwards;
    }
    
    @keyframes watersplash {
      0% {
        transform: translate(-50%, 0) scale(0.3, 0);
      }
      49% {
        transform: translate(-50%, 0) scale(0.3, 0);
      }
      50% {
        transform: translate(-50%, 0) scale(0.3, 0.3);
      }
      60% {
        transform: translate(-50%, 0) scale(0.7, 1);
      }
      90% {
        transform: translate(-50%, 0) scale(1, 0);
      }
      100% {
        transform: translate(-50%, 0) scale(1, 0);
      }
    }
    
    .preloader .bubbles {
      width: 100%;
    }
    
    .preloader .bubble {
      display: block;
      position: absolute;
      border-radius: 50%;
      background: white;
    }
    
    .preloader .bubble:nth-child(1) {
      width: 7px;
      height: 7px;
      bottom: 30px;
      left: 45%;
      animation: jumpLeft var(--duration) ease-out calc(var(--delay) + 0.2s) infinite backwards;
    }
    
    .preloader .bubble:nth-child(2) {
      width: 5px;
      height: 5px;
      bottom: 100px;
      left: 40%;
      animation: jumpLeft var(--duration) ease-out calc(var(--delay) + 0s) infinite backwards;
    }
    
    .preloader .bubble:nth-child(3) {
      width: 6px;
      height: 6px;
      bottom: 110px;
      right: 50%;
      animation: jumpRight var(--duration) ease-out calc(var(--delay) + 0.3s) infinite backwards;
    }
    
    .preloader .bubble:nth-child(4) {
      width: 7px;
      height: 7px;
      bottom: 70px;
      right: 35%;
      animation: jumpRight var(--duration) ease-out calc(var(--delay) + 0.1s) infinite backwards;
    }
    
    @keyframes jumpLeft {
      0%,
      45% {
        transform: translate(0, 0) scale(0);
      }
      60% {
        transform: translate(-50px, -90px) scale(1);
      }
      100% {
        transform: translate(-60px, 0px) scale(0.1);
      }
    }
    
    @keyframes jumpRight {
      0%,
      45% {
        transform: translate(0, 0) scale(0);
      }
      60% {
        transform: translate(30px, -80px) scale(1);
      }
      100% {
        transform: translate(50px, 0px) scale(0.1);
      }
    }
    
    .preloader .rain:nth-child(1) {
      --delay: 1s;
      --duration: 2.2s;
    }
    
    .preloader .rain:nth-child(2) {
      top: -10%;
      left: 25%;
      --delay: 1.1s;
      --duration: 2.1s;
    }
    
    .preloader .rain:nth-child(3) {
      top: -30%;
      left: 75%;
      --delay: 2.3s;
      --duration: 2.2s;
    }
    
    .preloader .rain:nth-child(4) {
      top: -5%;
      left: 70%;
      --delay: 1.4s;
      --duration: 2.1s;
    }
    
    .preloader .rain:nth-child(5) {
      top: -15%;
      left: 40%;
      --delay: 2.5s;
      --duration: 2.2s;
    }
    
    .preloader .rain:nth-child(6) {
      top: -30%;
      left: 55%;
      --delay: 1.2s;
      --duration: 2s;
    }
    
    .preloader .rain:nth-child(7) {
      top: -40%;
      left: 28%;
      --delay: 1.5s;
      --duration: 2s;
    }
    
    .preloader .rain:nth-child(8) {
      top: -40%;
      left: 60%;
      --delay: 1.7s;
      --duration: 2.3s;
    }
    
    .preloader .rain:nth-child(9) {
      top: -50%;
      left: 80%;
      --delay: 1.3s;
      --duration: 2.2s;
    }
    
    .preloader .rain:nth-child(10) {
      top: -30%;
      left: 20%;
      --delay: 2.3s;
      --duration: 2.5s;
    }
    
    .preloader .rain:nth-child(11) {
      top: -25%;
      left: 10%;
      --delay: 0.9s;
      --duration: 2.3s;
    }
    
    .preloader .rain:nth-child(12) {
      top: -25%;
      left: 90%;
      --delay: 1.7s;
      --duration: 2.3s;
    }
    
    .preloader .rain:nth-child(13) {
      top: -60%;
      left: 40%;
      --delay: 2s;
      --duration: 2s;
    }
  </style>
</head>
<body class="bg-gradient-to-br from-gray-900 via-blue-900 to-gray-900 text-white overflow-x-hidden">
  
<div class="preloader" id="preloader">
    <div class="center">
      <div class="rain">
        <div class="drop"></div>
        <div class="ripples">
          <span class="ripple"></span>
          <span class="ripple"></span>
        </div>
        <div class="splash"></div>
        <div class="bubbles">
          <span class="bubble"></span>
          <span class="bubble"></span>
          <span class="bubble"></span>
          <span class="bubble"></span>
        </div>
      </div>
      <div class="rain">
        <div class="drop"></div>
        <div class="ripples">
          <span class="ripple"></span>
          <span class="ripple"></span>
        </div>
        <div class="splash"></div>
        <div class="bubbles">
          <span class="bubble"></span>
          <span class="bubble"></span>
          <span class="bubble"></span>
          <span class="bubble"></span>
        </div>
      </div>
      <div class="rain">
        <div class="drop"></div>
        <div class="ripples">
          <span class="ripple"></span>
          <span class="ripple"></span>
        </div>
        <div class="splash"></div>
        <div class="bubbles">
          <span class="bubble"></span>
          <span class="bubble"></span>
          <span class="bubble"></span>
          <span class="bubble"></span>
        </div>
      </div>
      <div class="rain">
        <div class="drop"></div>
        <div class="ripples">
          <span class="ripple"></span>
          <span class="ripple"></span>
        </div>
        <div class="splash"></div>
        <div class="bubbles">
          <span class="bubble"></span>
          <span class="bubble"></span>
          <span class="bubble"></span>
          <span class="bubble"></span>
        </div>
      </div>
      <div class="rain">
        <div class="drop"></div>
        <div class="ripples">
          <span class="ripple"></span>
          <span class="ripple"></span>
        </div>
        <div class="splash"></div>
        <div class="bubbles">
          <span class="bubble"></span>
          <span class="bubble"></span>
          <span class="bubble"></span>
          <span class="bubble"></span>
        </div>
      </div>
      <div class="rain">
        <div class="drop"></div>
        <div class="ripples">
          <span class="ripple"></span>
          <span class="ripple"></span>
        </div>
        <div class="splash"></div>
        <div class="bubbles">
          <span class="bubble"></span>
          <span class="bubble"></span>
          <span class="bubble"></span>
          <span class="bubble"></span>
        </div>
      </div>
      <div class="rain">
        <div class="drop"></div>
        <div class="ripples">
          <span class="ripple"></span>
          <span class="ripple"></span>
        </div>
        <div class="splash"></div>
        <div class="bubbles">
          <span class="bubble"></span>
          <span class="bubble"></span>
          <span class="bubble"></span>
          <span class="bubble"></span>
        </div>
      </div>
      <div class="rain">
        <div class="drop"></div>
        <div class="ripples">
          <span class="ripple"></span>
          <span class="ripple"></span>
        </div>
        <div class="splash"></div>
        <div class="bubbles">
          <span class="bubble"></span>
          <span class="bubble"></span>
          <span class="bubble"></span>
          <span class="bubble"></span>
        </div>
      </div>
      <div class="rain">
        <div class="drop"></div>
        <div class="ripples">
          <span class="ripple"></span>
          <span class="ripple"></span>
        </div>
        <div class="splash"></div>
        <div class="bubbles">
          <span class="bubble"></span>
          <span class="bubble"></span>
          <span class="bubble"></span>
          <span class="bubble"></span>
        </div>
      </div>
      <div class="rain">
        <div class="drop"></div>
        <div class="ripples">
          <span class="ripple"></span>
          <span class="ripple"></span>
        </div>
        <div class="splash"></div>
        <div class="bubbles">
          <span class="bubble"></span>
          <span class="bubble"></span>
          <span class="bubble"></span>
          <span class="bubble"></span>
        </div>
      </div>
      <div class="rain">
        <div class="drop"></div>
        <div class="ripples">
          <span class="ripple"></span>
          <span class="ripple"></span>
        </div>
        <div class="splash"></div>
        <div class="bubbles">
          <span class="bubble"></span>
          <span class="bubble"></span>
          <span class="bubble"></span>
          <span class="bubble"></span>
        </div>
      </div>
    </div>
  </div>


<!-- Updated Navbar -->
  <header class="fixed w-full z-50 bg-black/20 backdrop-blur-md">
    <nav class="container mx-auto px-4 py-4">
      <div class="flex justify-between items-center">
        <h1 class="nav-title gradient-text animate-pulse-glow">QUIZ 404</h1>
        <div class="flex items-center gap-8">
          <div class="hidden md:flex items-center gap-6">
            <a href="community.php" class="hover:text-yellow-400 transition-colors">Community</a>
            <a href="about.php" class="hover:text-yellow-400 transition-colors">About</a>
            <a href="contact.php" class="hover:text-yellow-400 transition-colors">Contact</a>
          </div>
          
          <!-- Auth Button (Login/Signup or Logout) -->
          <div class="flex items-center">
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
              <a href="logout.php" class="auth-btn logout flex items-center justify-center text-white hover:text-yellow-400 transition-colors">
                <i class="fas fa-sign-out-alt mr-2"></i>
                Logout
              </a>
            <?php else: ?>
              <a href="auth.php" class="auth-btn login flex items-center justify-center text-white hover:text-yellow-400 transition-colors">
                <i class="fas fa-user mr-2"></i>
                Login/Signup
              </a>
            <?php endif; ?>
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

  <!-- Expanded Hero Section -->
  <main class="main-hero relative flex items-center justify-center px-4">
    <canvas id="particles" class="absolute inset-0 -z-10"></canvas>
    
    <!-- Space Animation Elements -->
    <div class="space-container">
      <div class="space-blob blob-1">
        <img src="assets/spacecraft.png" alt="spacecraft" class="space-img spacecraft">
      </div>
      <div class="space-blob blob-2">
        <img src="assets/planet-2.png" alt="planet" class="space-img planet-2">
      </div>
      <div class="space-blob blob-3"><img src="assets/planet-1.png" alt="planet" class="space-img planet-1"></div>
      <div class="space-blob blob-4"><img src="assets/astronaut.png" alt="astronaut" class="space-img astronaut"></div>
      
      
      
      
      
      <img src="assets/ufo.png" alt="ufo" class="space-img ufo">
    </div>
    
    <!-- Content -->
    <div class="text-center space-y-8 animate-float max-w-5xl mx-auto relative z-10">
      <h2 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold black-ops leading-tight px-4">
        Welcome to the
        <br class="md:hidden">
        <span class="whitespace-nowrap">Knowledge Zone</span>
      </h2>
      <p class="text-xl md:text-2xl text-gray-300 max-w-2xl mx-auto px-4">
        Enter the <span class='text-yellow-400 font-bold'>ultimate knowledge galaxy</span>, where every question unlocks new possibilities.
      </p>
      <div class="space-y-4">
        <a href="auth.php" class="hover:text-yellow-400 transition-colors">
          <button type="button" class="btn bg-gradient-to-r from-yellow-400 to-orange-500 text-black font-bold py-4 px-8 rounded-full 
                       hover:scale-105 transform transition-transform duration-300 hover:shadow-lg hover:shadow-yellow-500/50">
            🚀 Get Started
          </button>
        </a>
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
      </div>
    </div>
  </main>

  <!-- Features Section -->
  <section class="py-20 px-6 relative z-10">
    <div class="container mx-auto grid md:grid-cols-3 gap-12">
      <div class="bg-black/30 backdrop-blur-md p-8 rounded-2xl hover:scale-105 transition-transform duration-300">
        <h3 class="text-2xl font-bold text-yellow-400 mb-4">Multiple Categories</h3>
        <p class="text-gray-300">Explore diverse topics from science to pop culture.</p>
      </div>
      <div class="bg-black/30 backdrop-blur-md p-8 rounded-2xl hover:scale-105 transition-transform duration-300">
        <h3 class="text-2xl font-bold text-yellow-400 mb-4">Real-time Scoring</h3>
        <p class="text-gray-300">Track your progress with instant feedback and scoring.</p>
      </div>
      <div class="bg-black/30 backdrop-blur-md p-8 rounded-2xl hover:scale-105 transition-transform duration-300">
        <h3 class="text-2xl font-bold text-yellow-400 mb-4">Global Rankings</h3>
        <p class="text-gray-300">Compete with players worldwide and climb the leaderboard.</p>
      </div>
    </div>
  </section>

  <!-- New Detailed Footer -->
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

  <script>
    // Preloader
    window.addEventListener('load', function() {
        setTimeout(function() {
          const preloader = document.getElementById('preloader');
          preloader.style.opacity = '0';
          preloader.style.visibility = 'hidden';
          
          setTimeout(function() {
            preloader.style.display = 'none';
          }, 500);
        }, 5000); // Show preloader for 2 seconds
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

    // Button click effect
    let btn = document.querySelector(".btn");
    let animationInProgress = false;
    let animationId;
    
    btn.addEventListener("click", (e) => {
      const clickEffect = document.querySelector(".click-effect");

      if (animationInProgress) {
        clearTimeout(animationId);
        clickEffect.classList.remove("effect");
        void clickEffect.offsetWidth;
      }

      clickEffect.style.top = e.clientY + window.scrollY + "px";
      clickEffect.style.left = e.clientX + window.scrollX + "px";
      clickEffect.classList.add("effect");
      animationInProgress = true;

      animationId = setTimeout(() => {
        clickEffect.classList.remove("effect");
        animationInProgress = false;
      }, 750);
    });

    // GSAP Animations - Run when document is fully loaded
    document.addEventListener('DOMContentLoaded', () => {
      // Basic animations for space elements
      gsap.from('.spacecraft', {duration: 2, y: -100, opacity: 0, ease: "power2.out", delay: 0.5});
      
      let tl1 = gsap.timeline();
      tl1.from('.planet-1', {duration: 1, x: -400, y: -50, rotation: 32, scale: 0.5, opacity: 0})
         .to('.planet-1', {duration: 1, rotation: 15, scale: 1.2})
         .to('.planet-1', {duration: 1, rotation: 0, scale: 1});
      
      let tl2 = gsap.timeline({repeat: -1, yoyo: true});
      tl2.from('.planet-2', {duration: 1, x: 50, y: -50, rotation: 30, opacity: 0})
         .to('.planet-2', {duration: 5, rotation: 360});
      
      gsap.from('.astronaut', {duration: 3, y: -100, scale: 1.3, opacity: 0, ease: "back.out(1.7)", delay: 1});
      gsap.from('.ufo', {duration: 2, y: 100, opacity: 0, ease: "power1.out", delay: 1.5});
      
      // Create floating animations for space objects
      gsap.to('.spacecraft', {duration: 4, y: "+=30", repeat: -1, yoyo: true, ease: "sine.inOut"});
      gsap.to('.planet-1', {duration: 5, y: "+=20", repeat: -1, yoyo: true, ease: "sine.inOut", delay: 0.5});
      gsap.to('.planet-2', {duration: 7, y: "+=15", repeat: -1, yoyo: true, ease: "sine.inOut", delay: 1});
      gsap.to('.astronaut', {duration: 6, y: "+=25", repeat: -1, yoyo: true, ease: "sine.inOut", delay: 0.7});
      gsap.to('.ufo', {duration: 3, y: "+=15", x: "+=25", repeat: -1, yoyo: true, ease: "sine.inOut", delay: 0.2});
      
      // Add slow rotation to planets
      gsap.to('.planet-1', {duration: 25, rotation: 360, repeat: -1, ease: "none"});
      gsap.to('.planet-2', {duration: 18, rotation: -360, repeat: -1, ease: "none"});
    });
  </script>
</body>
</html>