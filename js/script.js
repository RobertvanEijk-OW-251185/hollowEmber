const turnPageButtons = document.querySelectorAll(".turnPageBtn");
const logInPage = document.getElementById("logInPageDisplay");
const signUpPage = document.getElementById("signUpPageDisplay");

turnPageButtons.forEach((btn) => {
	btn.addEventListener("click", () => {
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
	});
});
