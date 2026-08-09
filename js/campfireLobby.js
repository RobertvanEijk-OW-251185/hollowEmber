// Start Fire and Timer

function startFire() {
	// Buttons
	const startFireBtn = document.getElementById("startFire");
	const leaveCampBtn = document.getElementById("leaveCampfire");

	// Timer

	const isStartVisible = startFireBtn.style.display === "flex";
	const isLeaveHidden = leaveCampBtn.style.display === "none";

	if (isStartVisible && isLeaveHidden) {
		startFireBtn.style.display = "none";
		leaveCampBtn.style.display = "flex";

		console.log("Fire Started!");
	} else {
		startFireBtn.style.display = "flex";
		leaveCampBtn.style.display = "none";
		console.log("Left Camp!!");
	}
}

// Leave Lobby

function leaveCampfire() {
	window.location.href = "../pages/campGrounds.php";
}
