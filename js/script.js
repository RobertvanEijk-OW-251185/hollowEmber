// Turn Pages Function in Log In Book

document.addEventListener("DOMContentLoaded", function () {
	const signUpPage = document.getElementById("signUpPageDisplay");
	const signupInputs = signUpPage.querySelectorAll("input");

	signupInputs.forEach(function (input) {
		input.disabled = true;
	});
});

function turnPage(btn) {
	const logInPage = document.getElementById("logInPageDisplay");
	const signUpPage = document.getElementById("signUpPageDisplay");

	const loginInputs = logInPage.querySelectorAll("input");
	const signupInputs = signUpPage.querySelectorAll("input");

	if (
		logInPage.style.display === "flex" &&
		signUpPage.style.display === "none"
	) {
		logInPage.style.display = "none";
		signUpPage.style.display = "flex";
		console.log("Turned the Page, You're now on the Sign Up Page");

		// disable login form inputs
		loginInputs.forEach(function (input) {
			input.disabled = true;
		});

		// enable signup form inputs
		signupInputs.forEach(function (input) {
			input.disabled = false;
		});
	} else {
		logInPage.style.display = "flex";
		signUpPage.style.display = "none";
		console.log("Turned the Page, You're now on the Login Page");

		// disable signup form inputs
		signupInputs.forEach(function (input) {
			input.disabled = true;
		});

		// enable login form inputs
		loginInputs.forEach(function (input) {
			input.disabled = false;
		});
	}
}

const signupForm = document.getElementById("signupForm");

signupForm.addEventListener("submit", function (event) {
	// event.preventDefault();

	// const formData = new FormData(signupForm);

	// const password = signupForm.querySelector('input[name="password"]');

	// const confirmPassword = signupForm.querySelector(
	// 	'input[name="confirm_password"]',
	// );

	// if (!signupForm.checkValidity()) {
	// 	event.preventDefault();

	// 	signupForm.reportValidity();

	// 	console.log("Form contains invalid fields.");

	// 	return;
	// }

	// if (password.value !== confirmPassword.value) {
	// 	event.preventDefault();

	// 	const signupMessage = document.getElementById("signupMessage");

	// 	signupMessage.textContent = "Passwords do not match!";

	// 	console.log("Passwords do not match.");

	// 	return;
	// } else {
	// 	fetch("./index.php", {
	// 		method: "Post",
	// 		body: formData,
	// 	})
	// 		.then((response) => response.text())
	// 		.then((data) => {
	// 			const signupMessage = document.getElementById("signupMessage");

	// 			signupMessage.textContent = data;

	// 			console.log(data);
	// 		})
	// 		.catch((error) => {
	// 			console.log("Error: ", error);
	// 		});

	// 	console.log("Form is valid. Submitting normally.");
	// }

	const password = signupForm.querySelector('input[name="password"]');

	const confirmPassword = signupForm.querySelector(
		'input[name="confirm_password"]',
	);

	if (!signupForm.checkValidity()) {
		event.preventDefault();

		signupForm.reportValidity();

		console.log("Form contains invalid fields.");

		return;
	}

	if (password.value !== confirmPassword.value) {
		event.preventDefault();

		const signupMessage = document.getElementById("signupMessage");

		signupMessage.textContent = "Passwords do not match!";

		console.log("Passwords do not match.");

		return;
	}

	console.log("Form is valid. Submitting normally.");
});

// Go to Campfire Select Menu from Log In

function toCampGrounds() {
	window.location.href = "./pages/campGrounds.php";
}
