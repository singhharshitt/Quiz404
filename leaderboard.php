<?php
// Start session for user authentication
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "quiz404");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

// Get filter values
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';
$difficulty = isset($_GET['difficulty']) ? $conn->real_escape_string($_GET['difficulty']) : '';

// Fetch top 3 scores (for the podium)
$topScoresQuery = "SELECT users.username, quiz_scores.score, quiz_scores.category, quiz_scores.difficulty
                 FROM quiz_scores 
                 JOIN users ON quiz_scores.user_id = users.id
                 ORDER BY quiz_scores.score DESC LIMIT 3";
$topScoresResult = $conn->query($topScoresQuery);

// Fetch all scores with filters
$scoresQuery = "SELECT 
                users.username, 
                quiz_scores.score, 
                quiz_scores.category, 
                quiz_scores.difficulty, 
                quiz_scores.date_played
              FROM quiz_scores 
              JOIN users ON quiz_scores.user_id = users.id
              WHERE 1=1";

// Add filters if specified
if (!empty($category) && $category != 'All Categories') {
    $scoresQuery .= " AND quiz_scores.category = '$category'";
}
if (!empty($difficulty) && $difficulty != 'All Difficulties') {
    $scoresQuery .= " AND quiz_scores.difficulty = '$difficulty'";
}

$scoresQuery .= " ORDER BY quiz_scores.score DESC LIMIT 100";
$scoresResult = $conn->query($scoresQuery);

// Fetch available categories and difficulties for filters
$categoriesQuery = "SELECT DISTINCT category FROM quiz_scores ORDER BY category";
$categoriesResult = $conn->query($categoriesQuery);

$difficultiesQuery = "SELECT DISTINCT difficulty FROM quiz_scores ORDER BY difficulty";
$difficultiesResult = $conn->query($difficultiesQuery);

// Debug information - can be removed in production
$debugInfo = '';
if ($scoresResult === false) {
    $debugInfo = "Error executing query: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz 404 | Leaderboard</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Black+Ops+One&display=swap" rel="stylesheet">
    
    <!-- Animation Library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    
    <style>
        body {
            font-family: 'Quicksand', sans-serif;
            background: radial-gradient(circle at 50% 50%, #1e293b, #000);
            color: white;
            min-height: 100vh;
        }
        
        /* Text gradient animation */
        @keyframes shine {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        
        .gradient-text {
            background: linear-gradient(45deg, #FFD700, #FFA500, #FF8C00, #FFD700);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: shine 3s linear infinite;
        }
        
        .black-ops {
            font-family: 'Black Ops One', cursive;
        }
        
        .podium {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            margin: 40px 0;
            height: 180px;
        }
        
        .podium-position {
            text-align: center;
            padding: 10px;
            width: 150px;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.8s ease-out;
        }
        
        .podium.show .podium-position {
            opacity: 1;
            transform: translateY(0);
        }
        
        .podium-position:nth-child(1) {
            transition-delay: 0.2s;
        }
        
        .podium-position:nth-child(2) {
            transition-delay: 0s;
        }
        
        .podium-position:nth-child(3) {
            transition-delay: 0.4s;
        }
        
        .position-1 {
            background: linear-gradient(to bottom, #FFD700, #e6c100);
            height: 140px;
            border-radius: 15px 15px 0 0;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.5);
            position: relative;
            overflow: hidden;
        }
        
        .position-2 {
            background: linear-gradient(to bottom, #C0C0C0, #a3a3a3);
            height: 100px;
            border-radius: 15px 15px 0 0;
            box-shadow: 0 0 15px rgba(192, 192, 192, 0.5);
            position: relative;
            overflow: hidden;
        }
        
        .position-3 {
            background: linear-gradient(to bottom, #CD7F32, #b26a25);
            height: 70px;
            border-radius: 15px 15px 0 0;
            box-shadow: 0 0 10px rgba(205, 127, 50, 0.5);
            position: relative;
            overflow: hidden;
        }

        /* Glow effect for podium positions */
        .position-1:after, .position-2:after, .position-3:after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.3), transparent);
            transform: skewX(-25deg);
            animation: shimmer 3s infinite;
        }
        
        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 200%; }
        }
        
        /* Table row animation */
        .table-row-animate {
            opacity: 0;
            transform: translateX(-20px);
            transition: all 0.3s ease-out;
        }
        
        .table-row-animate.show {
            opacity: 1;
            transform: translateX(0);
        }
        
        /* Button pulse effect */
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
        }
        
        .home-button {
            animation: pulse 2s infinite;
        }
        
        /* Trophy shake animation */
        @keyframes shake {
            0% { transform: rotate(0deg); }
            25% { transform: rotate(5deg); }
            50% { transform: rotate(0deg); }
            75% { transform: rotate(-5deg); }
            100% { transform: rotate(0deg); }
        }
        
        .trophy {
            display: inline-block;
            animation: shake 1s ease-in-out infinite;
        }
        
        /* Filter section animations */
        .filter-container {
            transition: all 0.3s ease;
        }
        
        .filter-container:hover {
            transform: translateY(-5px);
        }
        
        select {
            transition: all 0.3s ease;
        }
        
        select:hover {
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(138, 43, 226, 0.4);
        }
    </style>
</head>
<body>
    <div class="container mx-auto px-4 py-10 animate__animated animate__fadeIn">
        <h1 class="text-5xl font-bold text-center mb-10 gradient-text black-ops animate__animated animate__bounceIn">
            <span class="trophy">🏆</span> Leaderboard
        </h1>
        
        <!-- Filters -->
        <div class="flex justify-center gap-4 mb-8 animate__animated animate__fadeInDown">
            <form action="" method="GET" class="flex gap-4 filter-container">
                <select name="category" class="bg-gray-800 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition-all">
                    <option value="All Categories">All Categories</option>
                    <?php if ($categoriesResult && $categoriesResult->num_rows > 0): ?>
                        <?php while($category = $categoriesResult->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($category['category']); ?>" 
                                <?php echo (isset($_GET['category']) && $_GET['category'] == $category['category']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['category']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
                
                <select name="difficulty" class="bg-gray-800 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition-all">
                    <option value="All Difficulties">All Difficulties</option>
                    <?php if ($difficultiesResult && $difficultiesResult->num_rows > 0): ?>
                        <?php while($difficulty = $difficultiesResult->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($difficulty['difficulty']); ?>"
                                <?php echo (isset($_GET['difficulty']) && $_GET['difficulty'] == $difficulty['difficulty']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($difficulty['difficulty']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
                
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg transition transform hover:scale-110 hover:shadow-lg hover:shadow-purple-500/50">
                    Filter
                </button>
            </form>
        </div>
        
        <?php if (!empty($debugInfo)): ?>
            <div class="bg-red-500/50 p-4 rounded-lg mb-6">
                Debug info: <?php echo $debugInfo; ?>
            </div>
        <?php endif; ?>
        
        <!-- Podium (Top 3) -->
        <div class="podium">
            <?php
            $topScores = array('---', '---', '---');
            $topPoints = array(0, 0, 0);
            
            if ($topScoresResult && $topScoresResult->num_rows > 0) {
                $i = 0;
                while($row = $topScoresResult->fetch_assoc()) {
                    if ($i < 3) {
                        $topScores[$i] = $row['username'];
                        $topPoints[$i] = $row['score'];
                    }
                    $i++;
                }
            }
            
            // Reorder for podium display (1st in middle, 2nd on left, 3rd on right)
            $displayOrder = array(1, 0, 2);
            $positionClasses = array('position-2', 'position-1', 'position-3');
            $numbers = array(2, 1, 3);
            
            foreach ($displayOrder as $i => $pos) {
                echo '<div class="podium-position">';
                echo '<div class="' . $positionClasses[$i] . ' flex items-center justify-center">';
                echo '<span class="text-4xl font-bold">' . $numbers[$i] . '</span>';
                echo '</div>';
                echo '<div class="bg-gray-800/50 p-2 rounded-b-lg">';
                echo '<div class="font-bold">' . $topScores[$pos] . '</div>';
                echo '<div>' . $topPoints[$pos] . ' pts</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
        
        <!-- Leaderboard Table -->
        <div class="overflow-x-auto animate__animated animate__fadeInUp">
            <table class="w-full bg-gray-800/30 rounded-lg overflow-hidden">
                <thead>
                    <tr class="bg-gray-700/50">
                        <th class="py-3 px-4 text-left">Rank</th>
                        <th class="py-3 px-4 text-left">Username</th>
                        <th class="py-3 px-4 text-left">Category</th>
                        <th class="py-3 px-4 text-left">Difficulty</th>
                        <th class="py-3 px-4 text-left">Score</th>
                        <th class="py-3 px-4 text-left">Date</th>
                    </tr>
                </thead>
                <tbody id="leaderboard-table">
                    <?php
                    if ($scoresResult && $scoresResult->num_rows > 0) {
                        $rank = 1;
                        while($row = $scoresResult->fetch_assoc()) {
                            echo '<tr class="border-t border-gray-700/50 hover:bg-gray-700/20 table-row-animate" data-rank="' . $rank . '">';
                            echo '<td class="py-3 px-4">' . $rank . '</td>';
                            echo '<td class="py-3 px-4">' . htmlspecialchars($row['username']) . '</td>';
                            echo '<td class="py-3 px-4">' . htmlspecialchars($row['category']) . '</td>';
                            echo '<td class="py-3 px-4">' . htmlspecialchars($row['difficulty']) . '</td>';
                            echo '<td class="py-3 px-4">' . $row['score'] . '</td>';
                            echo '<td class="py-3 px-4">' . date('M d, Y', strtotime($row['date_played'])) . '</td>';
                            echo '</tr>';
                            $rank++;
                        }
                    } else {
                        echo '<tr><td colspan="6" class="py-4 px-4 text-center">No scores found for the selected filters</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <!-- Return to Home button -->
        <div class="flex justify-center mt-8 animate__animated animate__bounceIn" style="animation-delay: 1s;">
            <a href="home.php" class="home-button bg-gradient-to-r from-yellow-400 to-orange-500 text-black font-bold py-3 px-8 rounded-full hover:scale-105 transform transition-transform duration-300 hover:shadow-lg hover:shadow-yellow-500/50">
                🏠 Back to Home
            </a>
        </div>
    </div>
    
    <script>
        // Auto-submit the form when filters change
        document.querySelectorAll('select').forEach(select => {
            select.addEventListener('change', () => {
                select.form.submit();
            });
        });
        
        // Animation for podium
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.querySelector('.podium').classList.add('show');
            }, 300);
            
            // Animated entry for table rows
            const tableRows = document.querySelectorAll('.table-row-animate');
            let delay = 100;
            
            tableRows.forEach((row, index) => {
                setTimeout(() => {
                    row.classList.add('show');
                }, delay + (index * 50)); // Staggered animation
            });
            
            // CountUp animation for scores
            const scoreElements = document.querySelectorAll('#leaderboard-table td:nth-child(5)');
            
            scoreElements.forEach(element => {
                const finalScore = parseInt(element.textContent);
                let currentScore = 0;
                const duration = 1500; // 1.5 seconds
                const frameRate = 30; // frames per second
                const increment = Math.ceil(finalScore / (duration / 1000 * frameRate));
                
                const counter = setInterval(() => {
                    currentScore += increment;
                    if (currentScore >= finalScore) {
                        clearInterval(counter);
                        element.textContent = finalScore;
                    } else {
                        element.textContent = currentScore;
                    }
                }, 1000 / frameRate);
            });
        });
        
        // Confetti effect when the page loads
        setTimeout(function() {
            // Simple confetti effect (you can replace this with a more sophisticated library)
            function createConfetti() {
                const confetti = document.createElement('div');
                confetti.style.position = 'fixed';
                confetti.style.width = '10px';
                confetti.style.height = '10px';
                confetti.style.backgroundColor = ['#FFD700', '#FFA500', '#FF8C00', '#9370DB', '#6A5ACD'][Math.floor(Math.random() * 5)];
                confetti.style.borderRadius = '50%';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.top = '-10px';
                confetti.style.opacity = Math.random() + 0.5;
                confetti.style.pointerEvents = 'none';
                confetti.style.zIndex = '9999';
                document.body.appendChild(confetti);
                
                const animationDuration = Math.random() * 3 + 2;
                confetti.style.animation = `fall ${animationDuration}s linear forwards`;
                
                // Add falling animation
                const style = document.createElement('style');
                style.textContent = `
                    @keyframes fall {
                        to {
                            transform: translateY(100vh) rotate(${Math.random() * 360}deg);
                            opacity: 0;
                        }
                    }
                `;
                document.head.appendChild(style);
                
                setTimeout(() => {
                    confetti.remove();
                    style.remove();
                }, animationDuration * 1000);
            }
            
            // Create confetti particles
            for (let i = 0; i < 50; i++) {
                setTimeout(createConfetti, Math.random() * 2000);
            }
        }, 500);
    </script>
</body>
</html>