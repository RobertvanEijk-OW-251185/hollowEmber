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

const backgroundImageANimation = document.querySelector("body");

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

// Background Fire Images preloading Start

const fireImageBaseURLS = [
	"../assets/campfireLobbyBase.png",
	"../assets/campfireLobbyBase2.png",
	"../assets/campfireLobbyBase3.png",
];

const fireImageBurningOutURLS = [
	"../assets/campfireLobbyBurnoutPhase1.png",
	"../assets/campfireLobbyBurnoutPhase2.png",
	"../assets/campfireLobbyBurnoutPhase3.png",
];

let imagesReady = false;

// function preloadImages(urls) {
// 	return Promise.all(
// 		urls.map((src) => {
// 				const img = new Image();
// 				img.src = src;
// 					return img.decode
// 						? img.decode().catch(() => {})
// 						: new Promise((resolve, reject) => {
// 							img.onload = resolve;
// 							img.onerror = reject;
// 					}),
// 		})

// 	);
// }

// function preloadImages(urls) {
// 	return Promise.all(
// 		urls.map((src) => {
// 			const img = new Image();
// 			img.src = src;
// 			return img.decode
// 				? img.decode().catch(() => {})
// 				: new Promise((resolve, reject) => {
// 						img.onload = resolve;
// 						img.onerror = reject;
// 					});
// 		}),
// 	);
// }

const preloadedImages = []; // keep references alive

function preloadImages(urls) {
	return Promise.all(
		urls.map((src) => {
			const img = new Image();
			img.src = src;
			preloadedImages.push(img); // hold onto it
			return img.decode().catch(() => {});
		}),
	);
}

startBtn.disabled = true;

preloadImages(fireImageBaseURLS)
	.then(() => {
		imagesReady = true;
		console.log("Fire Animations Set 1 have Been Preloaded");
		startBtn.disabled = false;
	})
	.catch((err) => {
		console.warn("Some Fires failed preloading", err);
		imagesReady = true;
	});

preloadImages(fireImageBurningOutURLS)
	.then(() => {
		imagesReady = true;
		console.log("Fire Animations Set 2 have Been Preloaded");
	})
	.catch((err) => {
		console.warn("Some Fires failed preloading", err);
		imagesReady = true;
	});

// Background Fire Images preloading End

startBtn.addEventListener("click", startFire);
leaveBtn.addEventListener("click", leaveFire);

renderTimer();

// updateBackground();

// Background Fire Sounds
const music = document.getElementById("fireCrackle");
const toggleBtn = document.getElementById("startFire");
let isPlaying = false;

music.addEventListener("error", (e) => {
	console.error("Audio failed to load:", music.error, music.currentSrc);
});

toggleBtn.addEventListener("click", () => {
	if (isPlaying) {
		music.pause();
	} else {
		music.play().catch((err) => console.error("Playback failed:", err));
	}
	isPlaying = !isPlaying;
});
//

function startFire() {
	if (timerInterval) return; // if the fire's timer is already going...

	timerInterval = setInterval(updateTimer, REFRESH_MS);

	backgroundImageANimation.classList.add("backgroundAnimationLobby1");

	animationTimeout = setTimeout(() => {
		backgroundImageANimation.classList.remove("backgroundAnimationLobby1");
		backgroundImageANimation.classList.add("backgroundAnimationLobby2");
	}, 90000);

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

		backgroundImageANimation.classList.remove("backgroundAnimationLobby2");

		music.pause();
		music.currentTime = 0;
		isPlaying = false;

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
