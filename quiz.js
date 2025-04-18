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

// Audio setup
const audioContext = new (window.AudioContext || window.webkitAudioContext)();
let isAudioEnabled = localStorage.getItem('audioEnabled') === 'true';

// Sound effects
const sounds = {
  correct: new Audio('sounds/correct.mp3'),
  wrong: new Audio('sounds/wrong.mp3'),
  tick: new Audio('sounds/tick.mp3'),
  hint: new Audio('sounds/hint.mp3')
};

// Quiz Logic
const questionSets = {};

// Quiz state
let current = 0;
let timer;
let countdown;
let score = 0;
let currentCategory = "";
let selectedSet = [];
let difficulty = "medium";
let hintsRemaining = 3;
let fiftyFiftyUsed = false;
let currentTheme = localStorage.getItem('theme') || 'dark';
let isQuizActive = false;

// Initialize theme and audio
document.documentElement.setAttribute('data-theme', currentTheme);
updateThemeIcon();
updateAudioIcon();

// Difficulty settings
const difficultySettings = {
  easy: { time: 45, points: 1 },
  medium: { time: 30, points: 2 },
  hard: { time: 15, points: 3 }
};

// Function to load questions from API
async function loadQuestionsFromAPI(category, difficulty) {
    try {
        const response = await fetch(`api/quiz.php?action=get_questions&category=${category}&difficulty=${difficulty}`);
        const data = await response.json();
        
        if (data.success) {
            questionSets[category] = data.questions.map(q => ({
                q: q.question,
                options: [q.option1, q.option2, q.option3, q.option4],
                answer: q.correct_answer
            }));
            return true;
        } else {
            console.error('Failed to load questions:', data.error);
            return false;
        }
    } catch (error) {
        console.error('Error loading questions:', error);
        return false;
    }
}

// Update selectCategory function
async function selectCategory(cat) {
  currentCategory = cat;
  document.getElementById("categorySelection").classList.add("hidden");
    document.getElementById("difficultySelection").classList.remove("hidden");
}

// Update selectDifficulty function
async function selectDifficulty(level) {
    difficulty = level;
    countdown = difficultySettings[level].time;
    
    // Load questions for the selected category and difficulty
    const success = await loadQuestionsFromAPI(currentCategory, level);
    if (!success) {
        alert('Failed to load questions. Please try again.');
        return;
    }
    
    selectedSet = questionSets[currentCategory];
    document.getElementById("difficultySelection").classList.add("hidden");
  document.getElementById("quizSection").classList.remove("hidden");
    isQuizActive = true;
  loadQuestion();
}

function loadQuestion() {
  if (current >= 10) {
    showResults();
    return;
  }

  const q = selectedSet[current];
  document.getElementById("questionNum").innerText = current + 1;
  document.getElementById("questionText").innerText = q.q;
  document.getElementById("scoreDisplay").innerText = score;
  
  // Update progress bar
  const progress = ((current) / 10) * 100;
  document.getElementById("progressBar").style.width = `${progress}%`;

  const optionsEl = document.getElementById("options");
  optionsEl.innerHTML = '';
  q.options.forEach(opt => {
    const btn = document.createElement("button");
    btn.innerText = opt;
    btn.className = "option-hover bg-white/10 hover:bg-purple-700 px-6 py-4 rounded-xl transition-all text-lg font-medium";
    btn.onclick = () => handleAnswer(btn, opt, q.answer);
    optionsEl.appendChild(btn);
  });

  // Reset lifelines for new question
  fiftyFiftyUsed = false;
  document.getElementById("fiftyFiftyButton").disabled = false;
  document.getElementById("fiftyFiftyButton").classList.remove("opacity-50");

  startTimer();
}

function handleAnswer(button, chosen, correct) {
  if (!isQuizActive) return;
  
  clearInterval(timer);
  const allButtons = document.querySelectorAll("#options button");
  allButtons.forEach(btn => btn.disabled = true);

  const pointEl = document.getElementById("pointIndicator");
  if (chosen === correct) {
    button.classList.add("bg-green-500", "correct-answer");
    pointEl.innerText = `+${difficultySettings[difficulty].points}`;
    pointEl.style.color = "#22c55e";
    pointEl.classList.remove("hidden");
    score += difficultySettings[difficulty].points;
    playSound('correct');
  } else {
    button.classList.add("bg-red-500", "wrong-answer");
    allButtons.forEach(btn => {
      if (btn.innerText === correct) {
        btn.classList.add("bg-green-500");
      }
    });
    pointEl.innerText = "❌";
    pointEl.style.color = "#ef4444";
    pointEl.classList.remove("hidden");
    playSound('wrong');
  }

  setTimeout(() => {
    pointEl.classList.add("hidden");
    nextQuestion();
  }, 1500);
}

function startTimer() {
  clearInterval(timer);
  countdown = difficultySettings[difficulty].time;
  const timerEl = document.getElementById("timer");
  updateTimerDisplay(timerEl);

  timer = setInterval(() => {
    countdown--;
    updateTimerDisplay(timerEl);
    
    if (countdown <= 10) {
      timerEl.classList.add("text-red-500", "animate-pulse");
      if (isAudioEnabled) {
        playSound('tick');
      }
    }
    
    if (countdown <= 0) {
      clearInterval(timer);
      nextQuestion();
    }
  }, 1000);
}

function updateTimerDisplay(timerEl) {
  const minutes = Math.floor(countdown / 60);
  const seconds = countdown % 60;
  timerEl.innerText = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
}

function nextQuestion() {
  current++;
  loadQuestion();
}

function useHint() {
  if (hintsRemaining <= 0 || !isQuizActive) return;
  
  const currentQuestion = selectedSet[current];
  const hint = getHint(currentQuestion.answer);
  
  // Show hint
  const hintEl = document.createElement('div');
  hintEl.className = 'mt-4 p-4 bg-blue-500/20 rounded-lg';
  hintEl.innerText = `💡 Hint: ${hint}`;
  document.getElementById('questionText').after(hintEl);
  
  // Update hint count
  hintsRemaining--;
  document.getElementById('hintCount').innerText = hintsRemaining;
  
  // Disable button if no hints left
  if (hintsRemaining <= 0) {
    document.getElementById('hintButton').disabled = true;
    document.getElementById('hintButton').classList.add('opacity-50');
  }
  
  // Deduct points
  score = Math.max(0, score - 1);
  document.getElementById('scoreDisplay').innerText = score;
  
  playSound('hint');
}

function getHint(answer) {
  const length = answer.length;
  const hintLength = Math.ceil(length * 0.4);
  return answer.split('').map((char, i) => i < hintLength ? char : '_').join('');
}

function useFiftyFifty() {
  if (fiftyFiftyUsed || !isQuizActive) return;
  
  const currentQuestion = selectedSet[current];
  const options = document.querySelectorAll("#options button");
  const correctIndex = currentQuestion.options.indexOf(currentQuestion.answer);
  
  let removedCount = 0;
  options.forEach((option, index) => {
    if (index !== correctIndex && removedCount < 2) {
      option.classList.add('opacity-20', 'cursor-not-allowed');
      option.disabled = true;
      removedCount++;
    }
  });
  
  fiftyFiftyUsed = true;
  document.getElementById("fiftyFiftyButton").disabled = true;
  document.getElementById("fiftyFiftyButton").classList.add("opacity-50");
}

function toggleTheme() {
  currentTheme = currentTheme === 'dark' ? 'light' : 'dark';
  document.documentElement.setAttribute('data-theme', currentTheme);
  localStorage.setItem('theme', currentTheme);
  updateThemeIcon();
}

function updateThemeIcon() {
  const icon = document.getElementById('themeIcon');
  icon.textContent = currentTheme === 'dark' ? '🌙' : '☀️';
}

function toggleAudio() {
  isAudioEnabled = !isAudioEnabled;
  localStorage.setItem('audioEnabled', isAudioEnabled);
  updateAudioIcon();
}

function updateAudioIcon() {
  const icon = document.getElementById('audioIcon');
  if (icon) {
    icon.textContent = isAudioEnabled ? '🔊' : '🔇';
  }
}

function playSound(type) {
  if (!isAudioEnabled) return;
  
  const sound = sounds[type];
  if (sound) {
    sound.currentTime = 0;
    sound.play().catch(e => console.log('Audio play failed:', e));
  }
}

function shareResult(platform) {
  const score = document.getElementById('finalScore').innerText;
  const category = currentCategory;
  const difficultyText = difficulty.charAt(0).toUpperCase() + difficulty.slice(1);
  const message = `I scored ${score}/30 in the ${category} quiz (${difficultyText} difficulty) on Quiz 404! Can you beat my score? 🎯`;
  
  const shareUrls = {
    twitter: `https://twitter.com/intent/tweet?text=${encodeURIComponent(message)}`,
    facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}`,
    whatsapp: `https://wa.me/?text=${encodeURIComponent(message)}`
  };
  
  window.open(shareUrls[platform], '_blank');
}

// Update showResults function
async function showResults() {
  const modal = document.getElementById("resultsModal");
  const finalScoreEl = document.getElementById("finalScore");
  const messageEl = document.getElementById("resultMessage");
  
  finalScoreEl.innerText = score;
  
  let message = "";
    if (score >= 25) {
    message = "🌟 Outstanding! You're a true expert!";
    } else if (score >= 20) {
    message = "👏 Great job! You know your stuff!";
    } else if (score >= 15) {
    message = "💪 Not bad! Keep learning!";
  } else {
    message = "📚 Keep practicing! You'll get better!";
  }
  
  messageEl.innerText = message;
  modal.classList.remove("hidden");
    
    // Save score to backend
    try {
        const response = await fetch('api/quiz.php?action=save_score', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                category: currentCategory,
                difficulty: difficulty,
                score: score,
                total_questions: 10,
                time_taken: 300 - countdown // Assuming 5 minutes total time
            })
        });
        
        const data = await response.json();
        if (!data.success) {
            console.error('Failed to save score:', data.error);
        }
    } catch (error) {
        console.error('Error saving score:', error);
    }
}

// Audio toggle button
document.addEventListener('DOMContentLoaded', () => {
  const audioToggle = document.createElement('button');
  audioToggle.className = 'fixed bottom-4 right-4 p-2 rounded-lg bg-white/10 hover:bg-white/20 transition-all';
  audioToggle.innerHTML = '🔇';
  audioToggle.onclick = () => {
    isAudioEnabled = !isAudioEnabled;
    audioToggle.innerHTML = isAudioEnabled ? '🔊' : '🔇';
  };
  document.body.appendChild(audioToggle);
});