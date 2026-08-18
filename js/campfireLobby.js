// Messaging Functionality:
// // Form submission without page reload

const chatForm = document.getElementById("chatForm");
const chatInput = document.getElementById("chatInput");

const chatMessages = document.querySelector(".chat_messages");

function escapeHtml(str) {
	return str
		.replace(/&/g, "&amp;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;")
		.replace(/"/g, "&quot;")
		.replace(/'/g, "&#039;");
}

if (chatForm) {
	chatForm.addEventListener("submit", async function (event) {
		event.preventDefault();

		const message = chatInput.value.trim();
		if (!message) return;

		const formData = new FormData(chatForm);

		try {
			const response = await fetch(window.location.href, {
				method: "POST",
				body: formData,
			});

			if (!response.ok) {
				throw new Error("Message Send Failed");
			}

			chatInput.value = "";

			chatMessages.insertAdjacentHTML(
				"beforeend",
				`<div class="chat_message"><h3 class="chatMessageSelf"><strong>You:</strong> ${message}</h3></div>`,
				// `<div class="chat_message"><h3 class="chatMessageSelf"><strong>You:</strong> ${escapeHtml(message)}</h3></div>`,
			);
		} catch (error) {
			console.error("Message failed to send:", error);
		}
	});
}

// // Disable message chat box if timer is not active

// Start Fire and Timer

const backgroundImageANimation = document.querySelector("body");

const START_MINUTES = 3;
const REFRESH_MS = 1000;

// scoring variables:
const SCORE_THRESHOLD = 15000; // Threshold functionality postponed
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

//
// Scoring Functionality:
// // Scores calculated by sent messages per x amount of time
// // // +50 points for a message every 5 seconds
// // // +35 points for a message every 10 seconds
// // // +10 points for a message every 15 seconds
// // Scores reduced for not sending messages per x amount of time
// // // -5 points for no sent message every 7 seconds
// // // -12 points for no sent message every 12 seconds
// // Time addition on campfire [BIG BIG MAYBE]
// // // Every 5 messages sent, add 10 seconds to the fire
//

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

// Need to add disabled, until the player cap is met
// // If Players Cap not met, then not show the start fire btn, instead show leave button
// // Once Player cap is met, Generate random topic for this lobby, and automatically start the timer.

function checkPlayers() {
	let campMembers = 4;
	let reqCampMembers = 4;
	let membersNeeded = reqCampMembers - campMembers;

	const chatMessageInput = document.getElementById("chatInput");

	if (campMembers < reqCampMembers) {
		startBtn.style.display = "none";
		leaveBtn.style.display = "flex";

		chatMessageInput.disabled = true;

		console.log("Members Needed:", `${membersNeeded}`);
	} else if (campMembers === reqCampMembers) {
		startBtn.style.display = "flex";
		leaveBtn.style.display = "none";

		chatMessageInput.disabled = false;

		console.log("Members Needed:", `${membersNeeded}`);
	}
}

checkPlayers();

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
