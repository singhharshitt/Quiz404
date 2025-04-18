<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Our Team</title>
    <!--Google Fonts Icons Font Awesome-->
    <link
      href="https://fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Round|Material+Icons+Sharp|Material+Icons+Two+Tone"
      rel="stylesheet"
    />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800&family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <!-- Tailwind CSS for background effects -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      :root {
        --primary: rgb(0, 0, 0);
        --secondary: rgb(255, 124, 10);
      }
      
      /* Preloader styles from 23.html */
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

      /* Main content styles */
      body {
        width: 100%;
        height: 100vh;
        margin: 0;
        padding: 0;
        background: radial-gradient(circle at 50% 50%, #1e293b, #000);
        color: white;
        font-family: 'Poppins', sans-serif;
        overflow-x: hidden;
      }
      
      .center {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
      }
      
      .team {
        width: 100%;
        height: fit-content;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        font-family: poppins;
        padding: 2rem;
        position: relative;
        z-index: 10;
      }
      
      /* Text effect from 15.html */
      .title {
        font-size: 60px;
        font-weight: 700;
        mix-blend-mode: difference;
        color: #ff7c0a;
        filter: contrast(70%);
        text-shadow: -3px -3px dodgerblue;
        margin-bottom: 2rem;
        text-align: center;
      }
      
      .profiles {
        width: 100%;
        height: fit-content;
        display: flex;
        align-items: center;
        justify-content: space-evenly;
        flex-wrap: wrap;
        gap: 2rem;
      }
      
      .profile {
        width: fit-content;
        height: fit-content;
        min-width: 350px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-direction: column;
        padding-top: 20px;
        margin: 20px;
        max-height: 100px;
        transition: 0.5s ease-in-out;
        z-index: 10;
      }
      
      .profile img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        position: relative;
        z-index: 2;
        transition: all 0.3s ease;
      }
      
      .details {
        display: flex;
        flex-direction: column;
        align-items: center;
        font-size: 16px;
        font-weight: 600;
        color: white;
        text-shadow: 0 0 10px rgba(255,255,255,0.5);
      }
      
      .details span {
        font-weight: 300;
      }
      
      .card {
        width: 320px;
        height: 300px;
        margin-bottom: 20px;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(10px);
        box-sizing: border-box;
        padding: 20px;
        display: none;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        color: white;
        border-radius: 6mm;
        position: relative;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
      }
      
      .head {
        width: 100%;
        display: flex;
        align-items: flex-end;
        justify-content: left;
        margin-bottom: 5px;
      }
      
      .head img {
        margin: 0;
        width: 60px;
        height: 60px;
        border-radius: 5mm;
        object-fit: cover;
      }
      
      .name {
        font-size: 16px;
        font-weight: 200;
        width: fit-content;
        margin-left: 10px;
        border: 1px solid rgba(255, 255, 255, 0.4);
        padding: 0 8px;
        border-radius: 3mm;
      }
      
      .content {
        flex-grow: 1;
        display: flex;
        justify-content: center;
        flex-direction: column;
        font-size: 15px;
        position: relative;
      }
      
      .role {
        font-weight: 200;
        font-size: 14px;
        color: #ff7c0a;
      }
      
      .icons {
        backdrop-filter: blur(9px) saturate(180%);
        -webkit-backdrop-filter: blur(9px) saturate(180%);
        background: rgba(255, 255, 255, 0.4);
        width: 100%;
        height: fit-content;
        box-sizing: border-box;
        padding: 0 20px;
        border-radius: 7mm;
        display: flex;
        align-items: center;
        justify-content: space-evenly;
      }
      
      .content::before {
        position: absolute;
        content: "";
        top: 100%;
        left: 70%;
        transform: translate(-50%, 0);
        background: var(--secondary);
        width: 60px;
        height: 30px;
        filter: blur(30px);
      }
      
      .icons a {
        width: 40px;
        height: 40px;
        line-height: 40px;
        text-align: center;
        color: white;
        text-decoration: none;
        font-size: 16px;
        transition: 0.35s;
      }
      
      .icons a:hover {
        background: white;
        color: var(--primary);
        border-radius: 50%;
        transform: scale(1.1);
      }
      
      .card::before {
        position: absolute;
        content: "location_pin";
        font-family: "Material Icons";
        color: var(--primary);
        font-size: 85px;
        top: calc(100% - 7px);
        left: 50%;
        transform: translate(-50%, 0) rotate(180deg);
      }
      
      .profile:hover {
        max-height: 500px;
      }
      
      .profile:hover #picture {
        border: 5px solid var(--primary);
        transform: scale(1.1);
      }
      
      .profile:hover .card {
        display: flex;
        animation: fade 0.5s ease-in-out;
      }
      
      @keyframes fade {
        0% {
          display: none;
          opacity: 0;
        }
        1%,
        50% {
          display: flex;
          opacity: 0;
        }
        100% {
          display: flex;
          opacity: 1;
        }
      }
      
      .profile:nth-child(odd) {
        --secondary: rgb(0, 0, 0);
        --primary: rgb(255, 124, 10);
      }
      
      /* Particle background from home.html */
      .canvas-container {
        position: fixed;
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

      /* Responsive adjustments */
      @media (max-width: 768px) {
        .title {
          font-size: 40px;
        }
        
        .profile {
          min-width: 300px;
        }
        
        .card {
          width: 280px;
        }
      }
    </style>
  </head>
  <body>
    <!-- Preloader from 23.html -->
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

    <!-- Particle background from home.html -->
    <canvas id="particles" class="fixed top-0 left-0 w-full h-full pointer-events-none"></canvas>

    <!-- Main content -->
    <div class="center">
      <div class="team">
        <div class="title">Our Team</div>
        <div class="profiles">
          <div class="profile">
            <div class="card">
              <div class="head">
                <img src="assets/vishwa.png" alt="" />
                <div class="name">Vishwajit</div>
              </div>
              <div class="content">
                <div class="role">Developer 1</div>
                "Contributed to the website's development by designing and implementing the Community page for effective user engagement.
                 Played a key role in refining the Footer of the page.
                 Also assisted in creating and integrating quiz page questions, ensuring proper formatting and functionality within the quiz system."
              </div>
              <div class="icons">
                <a href="https://www.linkedin.com/in/vishwajeet-yadav-b258052a3/"><i class="fa-brands fa-linkedin"></i></a>
                <a href="https://github.com/VishwajeetVortex"><i class="fa-brands fa-github"></i></a>
                <a href=""><i class="fa-solid fa-envelope"></i></a>
              </div>
            </div>

            <img src="assets/vishwa.png" alt="" id="picture" />
            <div class="details">
              Vishwajit yadav
              <span>Deveploper 1</span>
            </div>
          </div>

          <div class="profile">
            <div class="card">
              <div class="head">
                <img src="assets/harshit.jpg" alt="" />
                <div class="name">Harshit</div>
              </div>
              <div class="content">
                <div class="role">Developer 2</div>
                "Led the development of the website’s backend, ensuring efficient data handling and seamless functionality. 
                Took full ownership of the Quiz and Leaderboard pages, overseeing their complete implementation, 
                including logic, user interaction, and data integration."
              </div>
              <div class="icons">
                <a href="https://www.linkedin.com/in/harshit-singhh/"><i class="fa-brands fa-linkedin"></i></a>
                <a href="https://github.com/singhharshitt"><i class="fa-brands fa-github"></i></a>
                <a href="harshitsinghrajput242004@gmail.com"><i class="fa-solid fa-envelope"></i></a>
              </div>
              <div class="pattern"></div>
            </div>
            <img src="assets/harshit.jpg" alt="" id="picture" />
            <div class="details">
                Harshit
              <span>Developer 2</span>
            </div>
          </div>

          <div class="profile">
            <div class="card">
              <div class="head">
                <img src="assets/pranav.jpeg" alt="" />
                <div class="name">Pranav</div>
              </div>
              <div class="content">
                <div class="role">Developer 3</div>
                "Handled the design and implementation of the Contact page and Navigation Bar for seamless site navigation, 
                contributing to a cohesive and user-friendly interface. Also managed key backend tasks,
                 ensuring smooth data processing and overall website functionality."
              </div>
              <div class="icons">
                <a href="https://www.linkedin.com/in/pranav-chahar?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app"><i class="fa-brands fa-linkedin"></i></a>
                <a href="https://github.com/pranavchahartech"><i class="fa-brands fa-github"></i></a>
                <a href="pranavtheking.com@gmail.com"><i class="fa-solid fa-envelope"></i></a>
              </div>
              <div class="pattern"></div>
            </div>
            <img src="assets/pranav.jpeg" alt="" id="picture" />
            <div class="details">
              Pranav
              <span>Developer 3</span>
            </div>
          </div>

          <div class="profile">
            <div class="card">
              <div class="head">
                <img src="assets/kunal.JPG" alt="" />
                <div class="name">Kunal Tudu</div>
              </div>
              <div class="content">
                <div class="role">Developer 4</div>
                "Led the development of most frontend components, focusing on animations and interactive design elements to enhance user experience. 
                Also created and refined the 'Our Team' page and landing page, ensuring a visually appealing and seamless interface throughout."
              </div>
              <div class="icons">
                <a href=""><i class="fa-brands fa-linkedin"></i></a>
                <a href=""><i class="fa-brands fa-github"></i></a>
                <a href=""><i class="fa-solid fa-envelope"></i></a>
              </div>
              <div class="pattern"></div>
            </div>
            <img src="assets/kunal.JPG" alt="" id="picture" />
            <div class="details">
                Kunal Tudu
              <span>Developer 4</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      // Preloader removal
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

      // Particle Animation from home.html
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
    </script>
  </body>
</html>