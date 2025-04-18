<?php
// Start session and authentication control
session_start();

// Redirect to auth if not logged in (quiz is protected)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: auth.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "quiz404");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user details if needed
$user = null;
$user_id = $_SESSION['user_id'];
$sql = "SELECT username, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}

// Handle logout if requested
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}


// Handle quiz submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_score') {
  $score = isset($_POST['score']) ? intval($_POST['score']) : 0;
  $category = isset($_POST['category']) ? $conn->real_escape_string($_POST['category']) : '';
  $difficulty = isset($_POST['difficulty']) ? $conn->real_escape_string($_POST['difficulty']) : '';
  
  // Insert score into database
  $insertSql = "INSERT INTO quiz_scores (user_id, score, category, difficulty, date_played) 
               VALUES (?, ?, ?, ?, NOW())";
  $insertStmt = $conn->prepare($insertSql);
  
  if ($insertStmt) {
      $insertStmt->bind_param("iiss", $user_id, $score, $category, $difficulty);
      $result = $insertStmt->execute();
      $insertStmt->close();
      
      if ($result) {
          echo json_encode(['success' => true, 'message' => 'Score saved successfully']);
      } else {
          echo json_encode(['success' => false, 'message' => 'Failed to save score: ' . $conn->error]);
      }
      exit();
  } else {
      echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $conn->error]);
      exit();
  }
}

// Fetch quiz questions API
if (isset($_GET['action']) && $_GET['action'] === 'get_questions') {
    $category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';
    $difficulty = isset($_GET['difficulty']) ? $conn->real_escape_string($_GET['difficulty']) : '';
    
    // Query to fetch questions based on category and difficulty
    $questionsSql = "SELECT * FROM questions WHERE 
                    category = ? AND difficulty = ? 
                    ORDER BY RAND() LIMIT 10";
    $questionsStmt = $conn->prepare($questionsSql);
    
    if ($questionsStmt) {
        $questionsStmt->bind_param("ss", $category, $difficulty);
        $questionsStmt->execute();
        $questionsResult = $questionsStmt->get_result();
        
        $questions = [];
        while ($row = $questionsResult->fetch_assoc()) {
            // Format the question data
            $options = [
                $row['option_a'],
                $row['option_b'],
                $row['option_c'],
                $row['option_d']
            ];
            
            $questions[] = [
                'id' => $row['id'],
                'question' => $row['question_text'],
                'options' => $options,
                'correct_answer' => $row['correct_answer'],
                'hint' => $row['hint']
            ];
        }
        
        $questionsStmt->close();
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'questions' => $questions]);
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Quiz 404 | Quiz</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Black+Ops+One&display=swap" rel="stylesheet">
  <!-- Preloader Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <style>
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

    body {
      font-family: 'Quicksand', sans-serif;
      background: radial-gradient(circle at 50% 50%, #1e293b, #000);
      color: white;
      min-height: 100vh;
      overflow-x: hidden;
      transition: background-color 0.3s ease;
    }

    [data-theme="light"] body {
      background: radial-gradient(circle at 50% 50%, #e2e8f0, #94a3b8);
      color: #1e293b;
    }

    [data-theme="light"] .bg-white\/10 {
      background-color: rgba(0, 0, 0, 0.1);
    }

    [data-theme="light"] .text-gray-300 {
      color: #4b5563;
    }

    [data-theme="light"] .bg-black\/80 {
      background-color: rgba(0, 0, 0, 0.8);
    }

    [data-theme="light"] .bg-white\/20 {
      background-color: rgba(0, 0, 0, 0.2);
    }

    .animate-float {
      animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-20px); }
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

    .gradient-text {
      background: linear-gradient(45deg, #FFD700, #FFA500);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }

    .black-ops {
      font-family: 'Black Ops One', cursive;
    }

    .point-anim {
      position: absolute;
      top: 20px;
      left: 20px;
      font-size: 2rem;
      transition: all 0.5s ease-in-out;
    }

    .correct-answer {
      animation: correctPulse 0.5s ease-in-out;
    }

    .wrong-answer {
      animation: wrongShake 0.5s ease-in-out;
    }

    @keyframes correctPulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.1); box-shadow: 0 0 30px rgba(34, 197, 94, 0.6); }
      100% { transform: scale(1); }
    }

    @keyframes wrongShake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-10px); }
      75% { transform: translateX(10px); }
    }

    .option-hover {
      transition: all 0.3s ease;
    }

    .option-hover:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .opacity-20 {
      opacity: 0.2;
    }

    .cursor-not-allowed {
      cursor: not-allowed;
    }

    /* Progress bar animation */
    @keyframes progress {
      from { width: 0%; }
      to { width: 100%; }
    }

    #progressBar {
      animation: progress 1s ease-out;
    }

    /* Theme transition */
    * {
      transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    }

    canvas {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
      pointer-events: none;
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

  <canvas id="particles"></canvas>

  <!-- Category Selection -->
  <section id="categorySelection" class="min-h-screen flex flex-col items-center justify-center space-y-10 text-center p-4">
    <h2 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold black-ops leading-tight px-4 animate-float gradient-text">
      Choose Your Quiz Category
    </h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 text-lg font-semibold max-w-6xl mx-auto">
      <button onclick="selectCategory('General')" class="p-6 bg-white/10 hover:bg-purple-700 rounded-xl transition-all shadow-md option-hover">
        🧠 General Knowledge
      </button>
      <button onclick="selectCategory('Technology')" class="p-6 bg-white/10 hover:bg-purple-700 rounded-xl transition-all shadow-md option-hover">
        💻 Technology
      </button>
      <button onclick="selectCategory('Geography')" class="p-6 bg-white/10 hover:bg-purple-700 rounded-xl transition-all shadow-md option-hover">
        🌍 Geography
      </button>
      <button onclick="selectCategory('Science')" class="p-6 bg-white/10 hover:bg-purple-700 rounded-xl transition-all shadow-md option-hover">
        🔬 Science
      </button>
      <button onclick="selectCategory('Movies')" class="p-6 bg-white/10 hover:bg-purple-700 rounded-xl transition-all shadow-md option-hover">
        🎬 Movies
      </button>
      <button onclick="selectCategory('Sports')" class="p-6 bg-white/10 hover:bg-purple-700 rounded-xl transition-all shadow-md option-hover">
        ⚽ Sports
      </button>
      <button onclick="selectCategory('History')" class="p-6 bg-white/10 hover:bg-purple-700 rounded-xl transition-all shadow-md option-hover">
        📚 History
      </button>
      <button onclick="selectCategory('Music')" class="p-6 bg-white/10 hover:bg-purple-700 rounded-xl transition-all shadow-md option-hover">
        🎵 Music
      </button>
    </div>
    <div class="flex gap-4">
      <button onclick="goToHome()" class="mt-6 bg-gradient-to-r from-yellow-400 to-orange-500 text-black font-bold py-3 px-8 rounded-full hover:scale-105 transform transition-transform duration-300 hover:shadow-lg hover:shadow-yellow-500/50">
        🏠 Back to Home
      </button>
    </div>
  </section>

  <!-- Difficulty Selection -->
  <section id="difficultySelection" class="hidden min-h-screen flex flex-col items-center justify-center space-y-10 text-center p-4">
    <h2 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold black-ops leading-tight px-4 animate-float gradient-text">
      Choose Difficulty Level
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-lg font-semibold max-w-4xl mx-auto">
      <button onclick="selectDifficulty('easy')" class="p-6 bg-green-500/20 hover:bg-green-500/40 rounded-xl transition-all shadow-md option-hover">
        🟢 Easy
      </button>
      <button onclick="selectDifficulty('medium')" class="p-6 bg-yellow-500/20 hover:bg-yellow-500/40 rounded-xl transition-all shadow-md option-hover">
        🟡 Medium
      </button>
      <button onclick="selectDifficulty('hard')" class="p-6 bg-red-500/20 hover:bg-red-500/40 rounded-xl transition-all shadow-md option-hover">
        🔴 Hard
      </button>
    </div>
  </section>

  <!-- Quiz Section -->
  <section id="quizSection" class="hidden text-center py-20 px-6">
    <h2 class="text-5xl font-bold mb-4 gradient-text animate-pulse-glow black-ops">
      🚀 <span id="quizCategory">Quiz</span> Mode
    </h2>
    <p class="text-lg text-gray-300 mb-8">Answer the questions quickly and accurately! ⏱️</p>

    <div class="bg-white/10 max-w-4xl mx-auto rounded-2xl p-8 shadow-lg backdrop-blur-sm relative">
      <div id="pointIndicator" class="point-anim hidden"></div>

      <!-- Score and Timer -->
      <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-4">
          <span class="text-xl">🧠 Question <span id="questionNum">1</span>/10</span>
          <span class="text-xl text-yellow-400">Score: <span id="scoreDisplay">0</span></span>
        </div>
        <span class="text-xl font-bold" id="timer">00:30</span>
      </div>

      <!-- Progress Bar -->
      <div class="w-full bg-gray-700/50 rounded-full h-2.5 mb-6">
        <div id="progressBar" class="bg-gradient-to-r from-purple-600 to-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
      </div>

      <!-- Question -->
      <h3 class="text-2xl font-semibold mb-6" id="questionText">Loading question...</h3>

      <!-- Options -->
      <div class="grid gap-4" id="options"></div>

      <!-- Quiz Controls -->
      <div class="flex justify-between items-center mt-6">
        <div class="flex space-x-4">
          <button id="hintButton" onclick="useHint()" class="bg-blue-500/20 hover:bg-blue-500/40 px-4 py-2 rounded-lg transition-all">
            💡 Hint (<span id="hintCount">3</span>)
          </button>
        </div>
        <button onclick="nextQuestion()" class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 px-8 py-3 rounded-xl transition-all transform hover:scale-105 font-semibold shadow-lg">
          Next Question
        </button>
      </div>

      <!-- Theme Toggle -->
      <div class="absolute top-4 right-4">
        <button id="themeToggle" onclick="toggleTheme()" class="p-2 rounded-lg bg-white/10 hover:bg-white/20 transition-all">
          <span id="themeIcon">🌙</span>
        </button>
      </div>
    </div>
  </section>

  <!-- Results Modal -->
  <div id="resultsModal" class="hidden fixed inset-0 bg-black/80 flex items-center justify-center p-4">
    <div class="bg-white/20 backdrop-blur-md p-8 rounded-2xl max-w-md w-full">
      <h3 class="text-3xl font-bold mb-4 gradient-text">Quiz Complete! 🎉</h3>
      <p class="text-xl mb-4">Your Final Score: <span id="finalScore" class="font-bold text-yellow-400">0</span></p>
      <p class="mb-6" id="resultMessage"></p>
      
      <!-- Social Sharing -->
      <div class="flex justify-center space-x-4 mb-6">
        <button onclick="shareResult('twitter')" class="bg-blue-400 hover:bg-blue-500 p-2 rounded-lg transition-all">
          <span class="text-white">🐦 Twitter</span>
        </button>
        <button onclick="shareResult('facebook')" class="bg-blue-600 hover:bg-blue-700 p-2 rounded-lg transition-all">
          <span class="text-white">📘 Facebook</span>
        </button>
        <button onclick="shareResult('whatsapp')" class="bg-green-500 hover:bg-green-600 p-2 rounded-lg transition-all">
          <span class="text-white">💬 WhatsApp</span>
        </button>
      </div>

      <button onclick="location.reload()" class="w-full bg-gradient-to-r from-yellow-400 to-orange-500 text-black font-bold py-3 px-6 rounded-xl hover:scale-105 transition-transform">
        Try Another Category
      </button>
      <a href="leaderboard.php" 
       class="block mt-6 px-6 py-3 text-center bg-yellow-400 text-black text-lg font-semibold rounded-full hover:bg-yellow-500 transition transform hover:scale-105">
      Go to the Leaderboard
    </a>
  
    </div>
  </div>

  <!-- JavaScript -->
  <script>
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

function goToHome() {
  window.location.href = 'home.php';
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

// Quiz variables
let questions = [];
let currentQuestionIndex = 0;
let score = 0;
let timer;
let timeLeft = 30;
let quizCategory = '';
let quizDifficulty = '';
let hintCount = 3;
let answerSelected = false;

// Select category
function selectCategory(category) {
  quizCategory = category;
  document.getElementById('categorySelection').classList.add('hidden');
  document.getElementById('difficultySelection').classList.remove('hidden');
  document.getElementById('quizCategory').textContent = category;
}

// Select difficulty and start quiz
function selectDifficulty(difficulty) {
  quizDifficulty = difficulty;
  document.getElementById('difficultySelection').classList.add('hidden');
  document.getElementById('quizSection').classList.remove('hidden');
  
  // Fetch questions from server
  fetchQuestions();
}

// Fetch questions from the server
function fetchQuestions() {
  fetch(`quiz.php?action=get_questions&category=${quizCategory}&difficulty=${quizDifficulty}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        questions = data.questions;
        showQuestion(0);
      } else {
        alert('Failed to load questions. Please try again.');
      }
    })
    .catch(error => {
      console.error('Error fetching questions:', error);
      alert('Error loading questions. Please check your connection and try again.');
    });
}

// Display current question
function showQuestion(index) {
  if (index >= questions.length) {
    endQuiz();
    return;
  }

  // Reset state for new question
  clearInterval(timer);
  timeLeft = 30;
  answerSelected = false;
  
  // Update question number and progress bar
  document.getElementById('questionNum').textContent = index + 1;
  document.getElementById('progressBar').style.width = `${(index / questions.length) * 100}%`;
  
  // Display question text
  const currentQuestion = questions[index];
  document.getElementById('questionText').textContent = currentQuestion.question;
  
  // Create option buttons
  const optionsContainer = document.getElementById('options');
  optionsContainer.innerHTML = '';
  
  currentQuestion.options.forEach((option, optionIndex) => {
    const button = document.createElement('button');
    button.className = 'p-4 bg-white/10 hover:bg-white/20 rounded-xl transition-all option-hover text-left';
    button.innerHTML = `<span class="font-bold mr-2">${String.fromCharCode(65 + optionIndex)}.</span> ${option}`;
    button.onclick = () => selectAnswer(optionIndex);
    optionsContainer.appendChild(button);
  });
  
  // Start timer
  startTimer();
}

// Start countdown timer
function startTimer() {
  updateTimerDisplay();
  timer = setInterval(() => {
    timeLeft--;
    updateTimerDisplay();
    
    if (timeLeft <= 0) {
      clearInterval(timer);
      // Time's up, move to next question
      setTimeout(() => nextQuestion(), 1000);
    }
  }, 1000);
}

// Update timer display
function updateTimerDisplay() {
  const minutes = Math.floor(timeLeft / 60);
  const seconds = timeLeft % 60;
  document.getElementById('timer').textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
  
  // Change color based on time remaining
  if (timeLeft <= 5) {
    document.getElementById('timer').className = 'text-xl font-bold text-red-500';
  } else if (timeLeft <= 10) {
    document.getElementById('timer').className = 'text-xl font-bold text-yellow-500';
  } else {
    document.getElementById('timer').className = 'text-xl font-bold';
  }
}

// Handle answer selection
function selectAnswer(optionIndex) {
  if (answerSelected) return;
  
  answerSelected = true;
  clearInterval(timer);
  
  const options = document.querySelectorAll('#options button');
  const correctAnswerIndex = questions[currentQuestionIndex].correct_answer;
  
  // Check if answer is correct
  if (optionIndex === correctAnswerIndex) {
    // Correct answer
    options[optionIndex].classList.add('bg-green-500/40', 'correct-answer');
    
    // Calculate points based on remaining time
    const points = 10 + Math.floor(timeLeft / 3);
    score += points;
    
    // Update score display
    document.getElementById('scoreDisplay').textContent = score;
    
    // Show point indicator animation
    const pointIndicator = document.getElementById('pointIndicator');
    pointIndicator.textContent = `+${points}`;
    pointIndicator.classList.remove('hidden');
    pointIndicator.style.top = `${Math.random() * 40 + 20}px`;
    pointIndicator.style.left = `${Math.random() * 40 + 20}px`;
    
    setTimeout(() => {
      pointIndicator.style.opacity = '0';
      setTimeout(() => {
        pointIndicator.classList.add('hidden');
        pointIndicator.style.opacity = '1';
      }, 500);
    }, 1000);
  } else {
    // Wrong answer
    options[optionIndex].classList.add('bg-red-500/40', 'wrong-answer');
    options[correctAnswerIndex].classList.add('bg-green-500/40');
  }
  
  // Disable all options
  options.forEach(option => {
    option.disabled = true;
    option.classList.add('cursor-not-allowed');
  });
  
  // Automatically move to next question after delay
  setTimeout(() => {
    nextQuestion();
  }, 2000);
}

// Move to next question
function nextQuestion() {
  currentQuestionIndex++;
  
  if (currentQuestionIndex < questions.length) {
    showQuestion(currentQuestionIndex);
  } else {
    endQuiz();
  }
}

// Use hint feature
function useHint() {
  if (hintCount <= 0 || answerSelected) return;
  
  hintCount--;
  document.getElementById('hintCount').textContent = hintCount;
  
  // Display hint
  const hint = questions[currentQuestionIndex].hint;
  alert(`Hint: ${hint}`);
  
  // Disable hint button if all used
  if (hintCount <= 0) {
    document.getElementById('hintButton').classList.add('opacity-50', 'cursor-not-allowed');
  }
}

// End quiz and show results
function endQuiz() {
  clearInterval(timer);
  
  // Save score to database
  saveScore();
  
  // Show results modal
  document.getElementById('finalScore').textContent = score;
  
  // Set result message based on score
  let resultMessage = '';
  if (score >= 90) {
    resultMessage = 'Outstanding! You\'re a quiz genius! 🏆';
  } else if (score >= 70) {
    resultMessage = 'Great job! Very impressive knowledge! 🌟';
  } else if (score >= 50) {
    resultMessage = 'Good effort! Keep learning! 👍';
  } else {
    resultMessage = 'Nice try! Practice makes perfect! 📚';
  }
  
  document.getElementById('resultMessage').textContent = resultMessage;
  document.getElementById('resultsModal').classList.remove('hidden');
}

// Save score to database
function saveScore() {
  console.log('Sending score data:', {score, category: quizCategory, difficulty: quizDifficulty});
  
  const formData = new FormData();
  formData.append('action', 'save_score');
  formData.append('score', score);
  formData.append('category', quizCategory);
  formData.append('difficulty', quizDifficulty);

  fetch('quiz.php', {
    method: 'POST',
    body: formData
  })
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(data => {
      console.log('Score saved:', data);
      if (!data.success) {
        console.error('Server reported error:', data.message);
      }
    })
    .catch(error => {
      console.error('Error saving score:', error);
    });
}

// Share result on social media
function shareResult(platform) {
  const message = `I scored ${score} points in the ${quizCategory} Quiz (${quizDifficulty} difficulty) on Quiz404! Can you beat my score?`;
  let shareUrl = '';
  
  switch (platform) {
    case 'twitter':
      shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(message)}`;
      break;
    case 'facebook':
      shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}&quote=${encodeURIComponent(message)}`;
      break;
    case 'whatsapp':
      shareUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;
      break;
  }
  
  if (shareUrl) {
    window.open(shareUrl, '_blank');
  }
}

// Toggle theme function
function toggleTheme() {
  const html = document.documentElement;
  const themeIcon = document.getElementById('themeIcon');
  
  if (html.getAttribute('data-theme') === 'dark') {
    html.setAttribute('data-theme', 'light');
    themeIcon.textContent = '☀️';
  } else {
    html.setAttribute('data-theme', 'dark');
    themeIcon.textContent = '🌙';
  }
}

// Particle background animation
(function setupParticles() {
  const canvas = document.getElementById('particles');
  const ctx = canvas.getContext('2d');
  
  // Resize canvas to window size
  function resizeCanvas() {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
  }
  
  window.addEventListener('resize', resizeCanvas);
  resizeCanvas();
  
  // Particle class
  class Particle {
    constructor() {
      this.x = Math.random() * canvas.width;
      this.y = Math.random() * canvas.height;
      this.size = Math.random() * 5 + 1;
      this.speedX = Math.random() * 3 - 1.5;
      this.speedY = Math.random() * 3 - 1.5;
      this.color = `rgba(255, 255, 255, ${Math.random() * 0.5})`;
    }
    
    update() {
      this.x += this.speedX;
      this.y += this.speedY;
      
      if (this.x < 0 || this.x > canvas.width) {
        this.speedX = -this.speedX;
      }
      
      if (this.y < 0 || this.y > canvas.height) {
        this.speedY = -this.speedY;
      }
    }
    
    draw() {
      ctx.fillStyle = this.color;
      ctx.beginPath();
      ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
      ctx.fill();
    }
  }
  
  // Create particles
  const particlesArray = [];
  const particleCount = Math.min(100, Math.floor(window.innerWidth * window.innerHeight / 9000));
  
  for (let i = 0; i < particleCount; i++) {
    particlesArray.push(new Particle());
  }
  
  // Animation loop
  function animate() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    for (let i = 0; i < particlesArray.length; i++) {
      particlesArray[i].update();
      particlesArray[i].draw();
    }
    
    requestAnimationFrame(animate);
  }
  
  animate();
})();
  </script>
</body>
</html>