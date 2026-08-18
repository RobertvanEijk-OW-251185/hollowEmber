<!-- PHP Section Start -->
<?php
// ========================================
// PHP / BACKEND
// ========================================

session_start();

// // Not logged in, return to index
if (!isset($_SESSION["user_id"])) {
    header("Location: ../index.php");
    exit;
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


        <link rel="icon" type="image/x-icon" href="../assets/faviconCamp.ico">

        <title>Hollow Ember</title>

        <!-- Font Links -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">

        <!-- Bootstrap Link -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
            crossorigin="anonymous" />

        <!-- CSS Link -->
        <link rel="stylesheet" href="../css/campGrounds.css" />
    </head>

    <body>
        <!-- Camp Body -->
        <section class="camp_grounds">
            <!-- Hero Image -->
            <div class="hero_image">
                <div class="camp_name_sign"></div>
            </div>
            <!-- Camp Select -->
            <div class="camp_select">
                <div class="camp1">
                    <a class="fire1" href="./campfireLobby.php?campfire=1">
                        <p>fire 1</p>
                    </a>
                </div>
                <div class="camp2">
                    <a class="fire2" href="./campfireLobby.php?campfire=2">
                        <p>fire 2</p>
                    </a>
                </div>
                <div class="camp3">
                    <a class="fire3" href="./campfireLobby.php?campfire=3">
                        <p>fire 3</p>
                    </a>
                </div>
                <div class="camp4">
                    <a class="fire4" href="./campfireLobby.php?campfire=4">
                        <p>fire 4</p>
                    </a>
                </div>
                <!-- <div class="leaveCamps">
                    <a href="../index.php"></a>
                </div> -->

                <audio id="forrestWind" loop>
                    <source src="../assets/audio/forrestWind.mp3" type="audio/mpeg">
                </audio>
            </div>

        </section>



        <!-- Bootstrap Script Link -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
        </script>
        <!-- JS Script Link -->
        <script src="../js/campGrounds.js"></script>
    </body>

</html>