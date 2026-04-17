const player = document.getElementById("player");
const container = document.getElementById("game-container");
const scoreElement = document.getElementById("score");
let score = 0;
let isGameOver = false;
let speed = 300;
let lastTime = 0;

function jump() {
    if (!player.classList.contains("jump")) {
        player.classList.add("jump");
        setTimeout(() => player.classList.remove("jump"), 700);
    }
}

function createObstacle() {
    if (isGameOver) return;

    const topOrBot = Math.random();
    const obstacle = document.createElement("div");
    obstacle.classList.add("obstacle");
    
   
    if (topOrBot < 0.33 && topOrBot > 0) {
        obstacle.style.bottom = '10px';
        obstacle.innerHTML = "<h4 class='text-danger'>BOT</h4>";
    } else if(topOrBot < 0.66 && topOrBot > 0.33) {
       
        obstacle.style.bottom = '40px'; 
        obstacle.innerHTML = "<h4 class='text-danger'>MID</h4>";
    } else {
      obstacle.style.bottom = '60px'; 
      obstacle.innerHTML = "<h4 class='text-danger'>TOP</h4>";
    }
    
  console.log('+1');

    container.appendChild(obstacle);
    let obstaclePos = container.clientWidth;

  
    function move(timestamp) {
      if (isGameOver) return; 
      
      if (!lastTime) lastTime = timestamp;
      
        const deltaTime = (timestamp - lastTime) / 1000;
        lastTime = timestamp;

        obstaclePos -= speed * deltaTime;
        obstacle.style.left = obstaclePos + "px";

        
        let pRect = player.getBoundingClientRect();
        let oRect = obstacle.getBoundingClientRect();

        if (
            pRect.right > oRect.left + 5 && 
            pRect.left < oRect.right - 5 && 
            pRect.bottom > oRect.top + 5 && 
            pRect.top < oRect.bottom - 5
        ) {
            isGameOver = true;
            alert("COUPEZ ! On la refait.");
            location.reload();
            return;
        }

      
        if (obstaclePos > -100) {
            requestAnimationFrame(move);
        } else {
            obstacle.remove(); 
        }
    }

    requestAnimationFrame(move);

    score += 1;
    scoreElement.textContent = score;
   
    speed *= 1.05; 

    let nextObstacleTime = Math.random() * 5000;
    setTimeout(createObstacle, nextObstacleTime);
}


createObstacle();


document.addEventListener("keydown", (e) => {
    if (e.code === "ArrowDown" || e.code === "ShiftLeft") {
        e.preventDefault();
        player.style.height = '20px';
    }
    if (e.code === "ArrowUp" || e.code === "Space") {
        e.preventDefault();
        jump();
    }
});

document.addEventListener("keyup", (e) => {
    if (e.code === "ArrowDown" || e.code === "ShiftLeft") {
        player.style.height = '50px';
    }
});