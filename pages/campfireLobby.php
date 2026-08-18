<!-- PHP Section Start -->
<?php
// ========================================
// PHP / BACKEND
// ========================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once "../php/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../index.php");
    exit;
}

$userId = (int) $_SESSION["user_id"];
$campfireId = isset($_GET["campfire"]) ? (int) $_GET["campfire"] : 1;

// Get Username
$sql = "SELECT username FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$username = $user["username"] ?? "Guest";
$stmt->close();

// Find Campfire's session
$sql = "SELECT session_id
        FROM campfire_sessions
        WHERE campfire_id = ?
        ORDER BY session_id DESC
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $campfireId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $sessionRow = $result->fetch_assoc();
    $sessionId = (int) $sessionRow["session_id"];
} else {
    // create a new session for this campfire if needed
    $topicSql = "SELECT topic_id FROM topics ORDER BY topic_id ASC LIMIT 1";
    $topicStmt = $conn->prepare($topicSql);
    $topicStmt->execute();
    $topicResult = $topicStmt->get_result();
    $status = 'active';

    if ($topicResult->num_rows > 0) {
        $topicRow = $topicResult->fetch_assoc();
        $topicId = (int) $topicRow["topic_id"];
    } else {
        $topicId = 1;
    }
    $topicStmt->close();

    $sql = "INSERT INTO campfire_sessions (campfire_id, topic_id, start_time, status)
            VALUES (?, ?, NOW(), ?)";
    $stmt2 = $conn->prepare($sql);
    $stmt2->bind_param("iis", $campfireId, $topicId, $status);
    $stmt2->execute();
    $sessionId = $stmt2->insert_id;
    $stmt2->close();
}
$stmt->close();

// Find/Create Participants Row
$userId = (int) $_SESSION["user_id"];

$sql = "SELECT participant_id
        FROM session_participants
        WHERE user_id = ? AND session_id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $userId, $sessionId);
$stmt->execute();
$result = $stmt->get_result();
$anonymousName = $_SESSION["username"] ?? null;

if (empty($anonymousName)) {
    $anonymousName = "Player" . $userId;
}

if ($result->num_rows > 0) {
    $participantRow = $result->fetch_assoc();
    $participantId = (int) $participantRow["participant_id"];
} else {
    $sql = "INSERT INTO session_participants (session_id, user_id, anonymous_name, contribution_score, joined_at)
        VALUES (?, ?, ?, 0, NOW())";
    $stmt2 = $conn->prepare($sql);
    $stmt2->bind_param("iis", $sessionId, $userId, $anonymousName);
    $stmt2->execute();
    $participantId = $stmt2->insert_id;
    $stmt2->close();
}
$stmt->close();

// Insert Message
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "send_message") {
    $messageText = trim($_POST["message"] ?? "");

    if ($messageText !== "") {
        $sql = "INSERT INTO messages (participant_id, session_id, message_text, sent_at)
                VALUES (?, ?, ?, NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $participantId, $sessionId, $messageText);
        $stmt->execute();
        $stmt->close();

        // header("Location: campfireLobby.php?campfire=" . $campfireId);
        http_response_code(200);
        exit;
    }
}

// 4. Check participant count

// 5. Add user to session

// 6. Determine session status

// 7. Retrieve topic

// 8. Retrieve participants

// 9. Retrieve messages
$sql = "SELECT m.message_id, m.message_text, m.sent_at, u.username
        FROM messages m
        INNER JOIN session_participants sp ON sp.participant_id = m.participant_id
        INNER JOIN users u ON u.user_id = sp.user_id
        WHERE m.session_id = ?
        ORDER BY m.sent_at ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sessionId);
$stmt->execute();
$messageResult = $stmt->get_result();

$messages = [];
while ($row = $messageResult->fetch_assoc()) {
    $messages[] = $row;
}
$stmt->close();

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
        <link rel="stylesheet" href="../css/campfireLobby.css" />
    </head>

    <body>
        <!-- Fire Body -->
        <section class="camp_fire">
            <!-- Left Side -->
            <div class="left_side">
                <div class="chat_title">
                    <h1> Discussion: </h1>
                </div>
                <div class="chat_section">
                    <h1>Chat Box</h1>
                    <div class="chat_messages">
                        <!-- <div class="chat_message">
                            <h3 class="chatMessageSelf">Example chat Message1</h3>
                        </div>
                        <div class="chat_message">
                            <h3 class="chatMessageSelf">Example chat Message2</h3>
                        </div>
                        <div class="chat_message">
                            <h3 class="chatMessageSelf">Example chat Message3</h3>
                        </div> -->
                        <?php foreach ($messages as $message): ?>
                        <div class="chat_message">
                            <h3 class="chatMessageSelf">
                                <strong><?php echo htmlspecialchars($message["username"]); ?>:</strong>
                                <?php echo htmlspecialchars($message["message_text"]); ?>
                            </h3>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- <input class="chat_input" type="text" placeholder="Type your thoughts here..." /> -->
                    <form id="chatForm" method="POST">
                        <input type="hidden" name="action" value="send_message">
                        <input id="chatInput" type="text" name="message" class="chat_input"
                            placeholder="Type your thoughts here..." required>
                    </form>
                </div>
            </div>

            <!-- Right Side -->
            <div class="right_side">
                <div class="player_display">
                    <div class="player1">
                        <div class="player_info">
                            <h2><?php echo htmlspecialchars($username); ?></h2>
                            <!-- <h3><?php // echo htmlspecialchars($username); ?></h3> -->
                            <!-- <h2>Player Name</h2>
                            <h3>Player Score</h3> -->
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

                    <audio id="fireCrackle" loop>
                        <source src="../assets/audio/burningCampFireAudio.mp3" type="audio/mpeg">
                    </audio>
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