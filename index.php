<?php
// ========================================
// PHP / BACKEND
// ========================================

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    session_start();

    require_once "./php/db.php";

    $signupMessage = "";
    $loginMessage = "";

    
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        
        $action = $_POST["action"] ?? "";
        
        if ($action === "signup") {    

            // ========================================
            // SIGN UP
            // ========================================


            $username = trim($_POST["username"]);
            $email = trim($_POST["email"]);
            $password = $_POST["password"];
            $confirmPassword = $_POST["confirm_password"];

            if ( $username === "" || $email === "" || $password === "" || $confirmPassword === "") {

                $signupMessage = "Please fill in all fields.";

            } elseif ($password !== $confirmPassword) {

                $signupMessage = "Passwords do not match!";

            } else {

                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $sql = "INSERT INTO users (username, email, password_hash)
                        VALUES (?, ?, ?)";

                $stmt = $conn->prepare($sql);

                $stmt->bind_param(
                    "sss",
                    $username,
                    $email,
                    $passwordHash
                );

                if ($stmt->execute()) {

                    $signupMessage = "Account created successfully!";

                } else {

                    $signupMessage = "Error creating account: " . $stmt->error;

                }

                $stmt->close();
            }
            // echo $signupMessage;
            // exit;
        } elseif ($action === "login") {

            // ========================================
            // LOGIN
            // ========================================    

            $username = trim($_POST["username"] ?? "");
            $password = $_POST["password"] ?? "";

            if ($username === "" || $password === "") {

                $loginMessage = "Please enter your username and password.";

            } else {
                
                // Find the user
                $sql = "SELECT user_id, username, password_hash FROM users WHERE username = ?";

                $stmt = $conn->prepare($sql);

                $stmt->bind_param("s", $username);

                $stmt->execute();

                $result = $stmt->get_result();

                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc();

                    // check the password
                    if (password_verify($password, $user["password_hash"])) {
                        // successfully loggeed innnnn
                        // session_start();

                        $_SESSION["user_id"] = $user["user_id"];
                        $_SESSION["username"] = $user["username"];
                        
                        // To Camp Grounds for Campfire Selection
                        header("Location: ./pages/campGrounds.php");
                        exit;

                    } else {
                        $loginMessage = "Incorrect Username or Password";
                    }
                } else {
                    $loginMessage = "Incorrect Username or Password";
                }
                $stmt->close();
            }
        } 
    }

?>

<!-- PHP Section End -->


<!-- HTML Start -->

<!doctype html>
<!-- Doctype-->
<html lang="en">
    <!-- HTML Declaration -->

    <head>
        <!-- Character Set -->
        <meta charset="UTF-8" />

        <!-- Compatibility -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <!-- Viewport -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <!-- Author Name -->
        <meta name="robert" content="(Robert_van_Eijk_251185)" />

        <!-- Keywords -->
        <meta name="keywords" content="HTML, CSS, JavaScript, PHP, js, css, Robert_van_Eijk_251185" />


        <link rel="icon" type="image/x-icon" href="./assets/faviconCamp.ico">

        <title>Hollow Ember</title>

        <!-- Font Links -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">

        <!-- Bootstrap Link -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
            crossorigin="anonymous" />

        <!-- CSS Link -->
        <link rel="stylesheet" href="./css/main.css" />
    </head>

    <body>
        <!-- Sign In / Sign Up Form -->
        <section class="signInSignUpForm">
            <!-- Log In Form -->
            <form method="POST" id="loginForm">
                <input type="hidden" name="action" value="login">
                <div class="logInForm" id="logInPageDisplay" style="display: flex;">
                    <!-- Left Page -->
                    <div class="pageLeft">
                        <h1 class="logInTitle pixelify-sans-h1">Log In Form</h1>
                        <div class="logInInfoUsername">
                            <h2 class="pixelify-sans-h2">Username</h2>
                            <input type="text" name="username" class="info pixelify-sans-p"
                                placeholder="Enter Username..." required></input>
                        </div>
                    </div>
                    <!-- Right Page -->
                    <div class="pageRight">
                        <div class="turnPageButton">
                            <button type="button" class="turnPageBtn" onclick="turnPage()"></button>
                        </div>
                        <div class="logInInfoPassword">
                            <h2 class="pixelify-sans-h2">Password</h2>
                            <input type="password" name="password" class="info pixelify-sans-p"
                                placeholder="Enter Password..." required></input>
                        </div>
                        <div class="loginMessage pixelify-sans-p" id="loginMessage">
                            <?php echo $loginMessage; ?>
                        </div>
                        <button type="submit" class="logInBtn pixelify-sans-h3" id="toCampGrounds">Continue To
                            Camp</button>
                        <!-- <button type="submit" class="logInBtn pixelify-sans-h3" id="toCampGrounds"
                            onclick="toCampGrounds()">Continue To Camp</button> -->
                    </div>
                </div>
            </form>
            <!-- Sign up Form -->
            <form method="POST" id="signupForm">
                <div class="signUpForm" id="signUpPageDisplay" style="display: none;">
                    <input type="hidden" name="action" value="signup">

                    <!-- left page of book -->
                    <div class="pageLeft">
                        <h1 class="logInTitle pixelify-sans-h1">Sign Up Form</h1>
                        <div class="logInInfoUsername">
                            <h2 class="pixelify-sans-h2">Username</h2>
                            <input type="text" name="username" class="info pixelify-sans-p"
                                placeholder="Enter Username..." required></input>
                            <h2 class="pixelify-sans-h2">Email</h2>
                            <input type="email" name="email" class="info pixelify-sans-p" placeholder="Enter Email..."
                                required></input>
                        </div>
                    </div>
                    <!-- right page of book -->
                    <div class="pageRight">
                        <div class="turnPageButton">
                            <button type="button" class="turnPageBtn" onclick="turnPage()"></button>
                        </div>
                        <div class="logInInfoPassword">
                            <h2 class="pixelify-sans-h2">Password</h2>
                            <input type="password" name="password" class="info pixelify-sans-p"
                                placeholder="Enter Password..." required></input>
                            <h2 class="pixelify-sans-h2">Re-Enter Password</h2>
                            <input type="password" name="confirm_password" class="info pixelify-sans-p"
                                placeholder="Re-Enter Password..." required></input>
                        </div>
                        <div class="signupMessage pixelify-sans-p" id="signupMessage">
                            <?php echo $signupMessage; ?>
                        </div>
                        <button type="submit" class="logInBtn pixelify-sans-h3">Sign Up For
                            Camp</button>
                    </div>
                </div>
            </form>

        </section>

        <!-- Bootstrap Script Link -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
        </script>
        <!-- JS Script Link -->
        <script src="./js/script.js"></script>
    </body>

</html>