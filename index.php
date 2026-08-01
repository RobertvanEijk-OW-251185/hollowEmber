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
            <div class="logInForm" id="logInPageDisplay" style="display: flex;">
                <div class="pageLeft">
                    <h1 class="logInTitle pixelify-sans-h1">Log In Form</h1>
                    <div class="logInInfoUsername">
                        <h2 class="pixelify-sans-h2">Username</h2>
                        <input type="text" class="info pixelify-sans-p" placeholder="Enter Username..."></input>
                    </div>
                </div>
                <div class="pageRight">
                    <div class="turnPageButton">
                        <button class="turnPageBtn"></button>
                    </div>
                    <div class="logInInfoPassword">
                        <h2 class="pixelify-sans-h2">Password</h2>
                        <input type="text" class="info pixelify-sans-p" placeholder="Enter Password..."></input>
                    </div>
                    <button class="logInBtn pixelify-sans-h3">Continue To Camp</button>
                </div>
            </div>
            <div class="signUpForm" id="signUpPageDisplay" style="display: none;">
                <div class="pageLeft">
                    <h1 class="logInTitle pixelify-sans-h1">Sign Up Form</h1>
                    <div class="logInInfoUsername">
                        <h2 class="pixelify-sans-h2">Username</h2>
                        <input type="text" class="info pixelify-sans-p" placeholder="Enter Username..."></input>
                        <h2 class="pixelify-sans-h2">Email</h2>
                        <input type="text" class="info pixelify-sans-p" placeholder="Enter Email..."></input>
                    </div>
                </div>
                <div class="pageRight">
                    <div class="turnPageButton">
                        <button class="turnPageBtn"></button>
                    </div>
                    <div class="logInInfoPassword">
                        <h2 class="pixelify-sans-h2">Password</h2>
                        <input type="text" class="info pixelify-sans-p" placeholder="Enter Password..."></input>
                        <h2 class="pixelify-sans-h2">Re-Enter Password</h2>
                        <input type="text" class="info pixelify-sans-p" placeholder="Re-Enter Password..."></input>
                    </div>
                    <button class="logInBtn pixelify-sans-h3">Sign Up For Camp</button>
                </div>
            </div>
        </section>

        <!-- Bootstrap Script Link -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
        </script>
        <!-- JS Script Link -->
        <script src="./js/script.js"></script>
    </body>

</html>