// Start Fire and Timer

// const startingTime = 3;
// let time = startingTime * 60;

// const count = document.getElementById("timer");

// const startFireBtn = document.getElementById("startFire");

// let timerInterval = null;

// startFireBtn.addEventListener("click", startFire);

// function startFire() {
// 	timerInterval = setInterval(updateTimer, 1000);

// 	const startBtn = document.getElementById("startFire");
// 	const leaveBtn = document.getElementById("leaveCampfire");

// 	if (startBtn.style.display === "flex" && leaveBtn.style.display === "none") {
// 		startBtn.style.display = "none";
// 		leaveBtn.style.display = "flex";
// 		console.log("Fire Started");
// 	} else {
// 		startBtn.style.display = "flex";
// 		leaveBtn.style.display = "none";
// 		console.log("Left The Fire");
// 	}
// }

// function updateTimer() {
// 	const minutes = Math.floor(time / 60);
// 	let seconds = time % 60;

// 	seconds = seconds < 10 ? "0" + seconds : seconds;
// 	count.innerHTML = `${minutes}:${seconds}`;
// 	time--;

// 	if (time < 0) {
// 		clearInterval(timerInterval);
// 		console.log("The fire burnt out!!");
// 	}
// }

const START_MINUTES = 3;
const REFRESH_MS = 1000;

// scoring variables:
const SCORE_THRESHOLD = 15000;
const BONUS_MINUTES = 1;

let timeRemaining = START_MINUTES * 60;

// halfway time - for background change
const halfwayPoint = Math.floor((START_MINUTES * 60) / 2);

let timerInterval = null;

// current active background
const currentPhase = null;

// Scoring stuff - placeholder, until scoring functionality
let score = 0;
let bonusActive = false;
let bonusInterval = null;
let bonusTimeRemaining = 0;

const timerDisplay = document.getElementById("timer");
const startBtn = document.getElementById("startFire");
const leaveBtn = document.getElementById("leaveCampfire");
const bonusTimerDisplay = document.getElementById("bonusTimer");

startBtn.addEventListener("click", startFire);
leaveBtn.addEventListener("click", leaveFire);

renderTimer();

updateBackground();

function startFire() {
	if (timerInterval) return; // if the fire's timer is already going...

	timerInterval = setInterval(updateTimer, REFRESH_MS);

	startBtn.style.display = "none";
	leaveBtn.style.display = "flex";
	console.log("Fire Started");
}

function leaveFire() {
	clearInterval(timerInterval);
	timerInterval = null;

	startBtn.style.display = "flex";
	leaveBtn.style.display = "none";
	console.log("Left The Fire");
}

function updateTimer() {
	timeRemaining--;

	renderTimer();

	if (timeRemaining <= 0) {
		clearInterval(timerInterval);
		timerInterval = null;

		timerDisplay.innerHTML = "00:00";

		console.log("The fire burnt out!!");
	}
}

function renderTimer() {
	const minutes = Math.floor(timeRemaining / 60);
	const seconds = String(timeRemaining % 60).padStart(2, "0");

	timerDisplay.innerHTML = `${minutes}:${seconds}`;
}

// Leave Lobby

function leaveCampfire() {
	window.location.href = "../pages/campGrounds.php";
}
