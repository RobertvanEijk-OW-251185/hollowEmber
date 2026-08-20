// Get displayed users refreshed/polled

const currentUserId = window.currentUserId;
const currentSessionId = window.currentSessionId;

const playerDisplay = document.querySelector(".player_display");
const currentCampfireId = window.currentCampfireId ?? 1;
const MAX_PLAYERS = 4;
let currentPlayerCount = 0;
let sessionStatus = "waiting";
let sessionStartTimestamp = null;

function renderPlayers(players) {
	if (!playerDisplay) return;

	const visibleIds = new Set();

	players.forEach((player) => {
		const userId = String(player.user_id);
		visibleIds.add(userId);

		let playerCard = playerDisplay.querySelector(`[data-user-id="${userId}"]`);

		if (!playerCard) {
			playerCard = document.createElement("div");
			playerCard.className = "player-card player-entering";
			playerCard.dataset.participantId = player.participant_id;
			playerCard.dataset.userId = userId;
			playerCard.innerHTML = `
                <div class="player_info">
                    <h2></h2>
                    <p class="player_score">Score: 0</p>
                </div>
                <div class="player_icon"></div>
            `;
			playerDisplay.appendChild(playerCard);
			playerCard.addEventListener(
				"animationend",
				() => {
					playerCard.classList.remove("player-entering");
				},
				{ once: true },
			);
		}

		const name = player.username || player.anonymous_name || "Player";
		const playerInfo = playerCard.querySelector(".player_info");
		let nameHeading = playerCard.querySelector("h2");

		if (!nameHeading && playerInfo) {
			nameHeading = document.createElement("h2");
			playerInfo.appendChild(nameHeading);
		}

		if (nameHeading) nameHeading.textContent = name;
	});

	playerDisplay.querySelectorAll("[data-user-id]").forEach((card) => {
		if (!visibleIds.has(card.dataset.userId)) {
			card.remove();
		}
	});

	currentPlayerCount = players.length;
	checkPlayers();
}

async function refreshPlayers() {
	try {
		const pollingUrl = new URL(window.location.href);
		pollingUrl.search = new URLSearchParams({
			campfire: String(currentCampfireId),
			action: "get_players",
			refresh: String(Date.now()),
		}).toString();

		const response = await fetch(pollingUrl, {
			cache: "no-store",
			credentials: "same-origin",
		});

		if (!response.ok) {
			throw new Error("Failed to load players");
		}

		const data = await response.json();
		if (data.cleanup_required) {
			window.location.href = "../pages/campGrounds.php";
			return;
		}

		renderPlayers(data.players || []);
		renderMessages(data.messages || []);
		renderScores(data.players || [], data.messages || []);
		sessionStatus = data.session_status || "waiting";
		sessionStartTimestamp = data.start_timestamp;
		checkPlayers();
		syncTimer(data);

		const topicElement = document.getElementById("chat_topic");
		if (topicElement) {
			topicElement.textContent =
				data.players?.length >= MAX_PLAYERS
					? data.topic_text || "Topic"
					: "Topic";
		}
	} catch (error) {
		console.log("Player refresh failed:", error);
	}
}

setInterval(refreshPlayers, 3000);
refreshPlayers();

//
// Messaging Functionality:
// // Form submission without page reload

const chatForm = document.getElementById("chatForm");
const chatInput = document.getElementById("chatInput");

const chatMessages = document.querySelector(".chat_messages");

function renderMessages(messages) {
	if (!chatMessages) return;

	chatMessages.innerHTML = "";

	messages.forEach((message) => {
		const messageElement = document.createElement("div");
		messageElement.className = "chat_message";
		messageElement.dataset.messageId = message.message_id;

		const textElement = document.createElement("h3");
		textElement.className = "chatMessageSelf";

		const usernameElement = document.createElement("strong");
		usernameElement.textContent = `${message.username}: `;
		textElement.appendChild(usernameElement);
		textElement.appendChild(document.createTextNode(message.message_text));

		messageElement.appendChild(textElement);
		chatMessages.appendChild(messageElement);
	});

	chatMessages.scrollTop = chatMessages.scrollHeight;
}

// function escapeHtml(str) {
// 	return str
// 		.replace(/&/g, "&amp;")
// 		.replace(/</g, "&lt;")
// 		.replace(/>/g, "&gt;")
// 		.replace(/"/g, "&quot;")
// 		.replace(/'/g, "&#039;");
// }

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

			refreshPlayers();
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
// let score = 0;
// let bonusActive = false;
// let bonusInterval = null;
// let bonusTimeRemaining = 0;

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

function calculateScores(players, messages) {
	const scores = Object.fromEntries(
		players.map((player) => [String(player.user_id), 0]),
	);
	const messagesByUser = {};

	messages.forEach((message) => {
		const userId = String(message.user_id);
		if (!messagesByUser[userId]) messagesByUser[userId] = [];
		messagesByUser[userId].push(message);
	});

	Object.entries(messagesByUser).forEach(([userId, userMessages]) => {
		userMessages.sort(
			(first, second) => first.sent_timestamp - second.sent_timestamp,
		);

		userMessages.forEach((message, index) => {
			if (index === 0) {
				scores[userId] += 50;
				return;
			}

			const gap =
				message.sent_timestamp - userMessages[index - 1].sent_timestamp;
			scores[userId] += gap <= 5 ? 50 : gap <= 10 ? 35 : 10;
		});

		const lastMessage = userMessages[userMessages.length - 1];
		const referenceTime =
			sessionStatus === "burned" && sessionStartTimestamp
				? sessionStartTimestamp + START_MINUTES * 60
				: Math.floor(Date.now() / 1000);
		const inactiveSeconds = referenceTime - lastMessage.sent_timestamp;
		const penaltyCount = Math.max(0, Math.floor(inactiveSeconds / 12));
		scores[userId] = Math.max(0, scores[userId] - penaltyCount * 12);
	});

	return scores;
}

function renderScores(players, messages) {
	const scores = calculateScores(players, messages);

	players.forEach((player) => {
		const card = playerDisplay?.querySelector(
			`[data-user-id="${player.user_id}"]`,
		);
		const scoreElement = card?.querySelector(".player_score");

		if (scoreElement) {
			scoreElement.textContent = `Score: ${scores[String(player.user_id)] || 0}`;
		}
	});
}

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
	const playersMissing = currentPlayerCount < MAX_PLAYERS;
	const chatLocked = playersMissing || sessionStatus === "burned";
	const startLocked = playersMissing || sessionStatus !== "waiting";

	if (chatInput) {
		chatInput.disabled = chatLocked;
	}

	if (startBtn) {
		startBtn.disabled = startLocked;
		// startBtn.style.display = locked ? "none" : "flex";
	}

	if (leaveBtn) {
		leaveBtn.style.display = "flex";
	}

	if (chatLocked) {
		if (chatInput) chatInput.placeholder = "Waiting for more players...";
	} else {
		if (chatInput) chatInput.placeholder = "Type your thoughts here...";
	}
}

checkPlayers();

function startFire() {
	if (currentPlayerCount < MAX_PLAYERS || sessionStatus !== "waiting") return;

	if ((startBtn.style.display = "flex")) {
		startBtn.style.display = "none";
	}

	fetch(window.location.pathname, {
		method: "POST",
		headers: { "Content-Type": "application/x-www-form-urlencoded" },
		body: new URLSearchParams({
			action: "start_fire",
			campfire: String(currentCampfireId),
		}),
		credentials: "same-origin",
	})
		.then((response) => {
			if (!response.ok) throw new Error("Fire start failed");
			return response.json();
		})
		.then(() => refreshPlayers())
		.catch((error) => console.error("Fire start failed:", error));
}

function syncTimer(data) {
	if (data.session_status === "burned") {
		clearInterval(timerInterval);
		timerInterval = null;
		timeRemaining = 0;
		timerDisplay.textContent = "0:00";
		backgroundImageANimation.classList.remove(
			"backgroundAnimationLobby1",
			"backgroundAnimationLobby2",
		);
		music.pause();
		music.currentTime = 0;
		isPlaying = false;
		checkPlayers();
		return;
	}

	if (data.session_status !== "active" || !data.start_timestamp) return;

	if (!timerInterval) {
		timerInterval = setInterval(updateTimer, REFRESH_MS);
	}

	const elapsedSeconds = Math.floor(Date.now() / 1000 - data.start_timestamp);
	backgroundImageANimation.classList.toggle(
		"backgroundAnimationLobby1",
		elapsedSeconds < 90,
	);
	backgroundImageANimation.classList.toggle(
		"backgroundAnimationLobby2",
		elapsedSeconds >= 90,
	);

	timeRemaining = Math.max(0, START_MINUTES * 60 - elapsedSeconds);
	renderTimer();
}

function leaveFire() {
	clearInterval(timerInterval);
	timerInterval = null;

	// startBtn.style.display = "flex";
	// leaveBtn.style.display = "none";
	// console.log("Left The Fire");
}

function updateTimer() {
	timeRemaining--;

	renderTimer();

	if (timeRemaining <= 0) {
		clearInterval(timerInterval);
		timerInterval = null;

		timerDisplay.innerHTML = "00:00";

		backgroundImageANimation.classList.remove(
			"backgroundAnimationLobby1",
			"backgroundAnimationLobby2",
		);
		checkPlayers();

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
	const leaveData = new URLSearchParams({
		action: "leave_session",
		campfire: String(currentCampfireId),
		session_id: String(currentSessionId),
	});

	fetch(window.location.pathname, {
		method: "POST",
		headers: {
			"Content-Type": "application/x-www-form-urlencoded",
		},
		body: leaveData,
		credentials: "same-origin",
	})
		.then(async (response) => {
			if (!response.ok) {
				throw new Error("Leave request failed");
			}

			const result = await response.json();
			if (!result.success || result.deleted < 1) {
				throw new Error("No participant row was deleted");
			}

			window.location.href = "../pages/campGrounds.php";
		})
		.catch((error) => {
			console.error("Leave failed:", error);
			window.location.href = "../pages/campGrounds.php";
		});
}
