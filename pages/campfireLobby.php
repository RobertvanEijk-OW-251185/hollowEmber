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
        <link rel="stylesheet" href="../css/campfireLobby.css" />
    </head>

    <body>
        <!-- Fire Body -->
        <section class="camp_fire">
            <div class="left_side">
                <div class="chat_title">
                    <h1> Discussion: </h1>
                </div>
                <div class="chat_section">
                    <h1>Chat Box</h1>
                    <div class="chat_messages"></div>
                    <input class="chat_input" type="text" placeholder="Type your thoughts here..." />
                </div>

            </div>
            <div class="right_side">
                <div class="player_display">
                    <div class="player1">
                        <div class="player_info">
                            <h2>Player Name</h2>
                            <h3>Player Score</h3>
                        </div>
                        <div class="player_icon"></div>
                    </div>
                    <div class="player2">
                        <div class="player_info">
                            <h2>Player Name</h2>
                            <h3>Player Score</h3>
                        </div>
                        <div class="player_icon"></div>
                    </div>
                    <div class="player3">
                        <div class="player_info">
                            <h2>Player Name</h2>
                            <h3>Player Score</h3>
                        </div>
                        <div class="player_icon"></div>
                    </div>
                    <div class="player4">
                        <div class="player_info">
                            <h2>Player Name</h2>
                            <h3>Player Score</h3>
                        </div>
                        <div class="player_icon"></div>
                    </div>
                </div>
                <div class="timer_section">
                    <div class="timer_text">
                        <h2 id="timer"></h2>
                        <h2 class="bonus_timer_text" id="bonusTimer" style="display: none"></h2>
                    </div>
                    <button class="startFire" id="startFire" style="display: flex" onclick="">
                        <h2>Start Fire</h2>
                    </button>
                    <button class="leaveCamp" id="leaveCampfire" style="display: none" onclick="leaveCampfire()">
                        <h2>Leave Fire</h2>
                    </button>
                </div>
            </div>
        </section>



        <!-- Bootstrap Script Link -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
        </script>
        <!-- JS Script Link -->
        <script src="../js/campfireLobby.js"></script>
    </body>

</html>