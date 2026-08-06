// Turn Pages Function in Log In Book

function turnPage(btn) {
	const logInPage = document.getElementById("logInPageDisplay");
	const signUpPage = document.getElementById("signUpPageDisplay");

	if (
		logInPage.style.display === "flex" &&
		signUpPage.style.display === "none"
	) {
		logInPage.style.display = "none";
		signUpPage.style.display = "flex";
		console.log("Turned the Page, You're now on the Sign Up Page");
	} else {
		logInPage.style.display = "flex";
		signUpPage.style.display = "none";
		console.log("Turned the Page, You're now on the Login Page");
	}
}

// Go to Campfire Select Menu from Log In

function toCampGrounds() {
	window.location.href = "./pages/campGrounds.php";
}
