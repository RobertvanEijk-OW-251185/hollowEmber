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
    $topicSql = "SELECT topic_id FROM topics ORDER BY RAND() LIMIT 1";
    $topicStmt = $conn->prepare($topicSql);
    $topicStmt->execute();
    $topicResult = $topicStmt->get_result();
    $status = 'waiting';

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

if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "leave_session") {
    $leaveSessionId = isset($_POST["session_id"]) ? (int) $_POST["session_id"] : $sessionId;
    $deleteSql = "DELETE sp
                  FROM session_participants sp
                  WHERE sp.user_id = ? AND sp.session_id = ?";

    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param("ii", $userId, $leaveSessionId);
    $deleteStmt->execute();
    $deletedParticipants = $deleteStmt->affected_rows;
    $deleteStmt->close();

    header("Content-Type: application/json");
    echo json_encode(["success" => true, "deleted" => $deletedParticipants]);
    http_response_code(200);
    exit;
}

// count check for ammount of players in the campfire session
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "start_fire") {
    $countSql = "SELECT c.max_players, COUNT(sp.participant_id) AS participant_count
                 FROM campfire_sessions cs
                 INNER JOIN campfires c ON c.campfire_id = cs.campfire_id
                 LEFT JOIN session_participants sp ON sp.session_id = cs.session_id
                 WHERE cs.session_id = ?
                 GROUP BY c.max_players";
    $countStmt = $conn->prepare($countSql);
    $countStmt->bind_param("i", $sessionId);
    $countStmt->execute();
    $countRow = $countStmt->get_result()->fetch_assoc();
    $countStmt->close();

    $maxPlayersForStart = (int) ($countRow["max_players"] ?? 0);
    $participantCountForStart = (int) ($countRow["participant_count"] ?? 0);

    if ($participantCountForStart < $maxPlayersForStart) {
        http_response_code(409);
        echo json_encode(["success" => false, "error" => "The campfire is not full yet."]);
        exit;
    }

    $startSql = "UPDATE campfire_sessions
                 SET status = 'active', start_time = NOW()
                 WHERE session_id = ? AND status = 'waiting'";
    $startStmt = $conn->prepare($startSql);
    $startStmt->bind_param("i", $sessionId);
    $startStmt->execute();
    $startStmt->close();

    header("Content-Type: application/json");
    echo json_encode(["success" => true]);
    exit;
}

// 8. Retrieve participants
// Find other users in the same session
// Add and display users -_-
$sql = "SELECT sp.participant_id, sp.anonymous_name, u.username, u.user_id
        FROM session_participants sp
        INNER JOIN users u ON u.user_id = sp.user_id
        WHERE sp.session_id = ?
        ORDER BY sp.joined_at ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sessionId);
$stmt->execute();
$participantsResult = $stmt->get_result();

$participants = [];
while ($row = $participantsResult->fetch_assoc()) {
    $participants[] = $row;
}
$stmt->close();

if (($_GET["action"] ?? "") === "get_players") {
    $sql = "SELECT sp.participant_id, sp.anonymous_name, u.username, u.user_id,
                   t.topic_text
            FROM session_participants sp
            INNER JOIN users u ON u.user_id = sp.user_id
            INNER JOIN campfire_sessions cs ON cs.session_id = sp.session_id
            INNER JOIN topics t ON t.topic_id = cs.topic_id
            WHERE sp.session_id = ?
            ORDER BY sp.joined_at ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $sessionId);
    $stmt->execute();

    $result = $stmt->get_result();

    $players = [];
    $topicText = null;
    while ($row = $result->fetch_assoc()) {
        $topicText = $row["topic_text"];
        $players[] = [
            "participant_id" => (int)$row["participant_id"],
            "user_id" => (int)$row["user_id"],
            "username" => $row["username"],
            "anonymous_name" => $row["anonymous_name"],
        ];
    }

    // find the campfire's session status and update it from waiting -> active -> burned
    $stateSql = "SELECT status,
                        UNIX_TIMESTAMP(start_time) AS start_timestamp,
                        UNIX_TIMESTAMP(end_time) AS burned_timestamp
                 FROM campfire_sessions
                 WHERE session_id = ?
                 LIMIT 1";
    $stateStmt = $conn->prepare($stateSql);
    $stateStmt->bind_param("i", $sessionId);
    $stateStmt->execute();
    $state = $stateStmt->get_result()->fetch_assoc();
    $stateStmt->close();

    $sessionStatus = $state["status"] ?? "waiting";
    $startTimestamp = isset($state["start_timestamp"]) ? (int) $state["start_timestamp"] : null;
    $burnedTimestamp = isset($state["burned_timestamp"]) ? (int) $state["burned_timestamp"] : null;
    $cleanupRequired = false;

    if ($sessionStatus === "active" && $startTimestamp !== null
        && time() - $startTimestamp >= 180) {
        $burnSql = "UPDATE campfire_sessions
                     SET status = 'burned', end_time = NOW()
                     WHERE session_id = ? AND status = 'active'";
        $burnStmt = $conn->prepare($burnSql);
        $burnStmt->bind_param("i", $sessionId);
        $burnStmt->execute();
        $burnStmt->close();
        $sessionStatus = "burned";
        $burnedTimestamp = time();
    }

    // THE PURGEEEEE
    if ($sessionStatus === "burned" && $burnedTimestamp !== null
        && time() - $burnedTimestamp >= 10) {
        $topicSql = "SELECT topic_id FROM topics ORDER BY RAND() LIMIT 1";
        $topicStmt = $conn->prepare($topicSql);
        $topicStmt->execute();
        $newTopic = $topicStmt->get_result()->fetch_assoc();
        $topicStmt->close();

        $conn->begin_transaction();

        // delete messages from db
        $deleteMessagesSql = "DELETE FROM messages WHERE session_id = ?";
        $deleteMessagesStmt = $conn->prepare($deleteMessagesSql);
        $deleteMessagesStmt->bind_param("i", $sessionId);
        $deleteMessagesStmt->execute();
        $deleteMessagesStmt->close();

        // delete users from the session
        $deleteParticipantsSql = "DELETE FROM session_participants WHERE session_id = ?";
        $deleteParticipantsStmt = $conn->prepare($deleteParticipantsSql);
        $deleteParticipantsStmt->bind_param("i", $sessionId);
        $deleteParticipantsStmt->execute();
        $deleteParticipantsStmt->close();

        // grabs a new topic
        if ($newTopic) {
            $resetSql = "UPDATE campfire_sessions
                          SET topic_id = ?, status = 'waiting',
                              start_time = NULL, end_time = NULL
                          WHERE session_id = ?";
            $resetStmt = $conn->prepare($resetSql);
            $newTopicId = (int) $newTopic["topic_id"];
            $resetStmt->bind_param("ii", $newTopicId, $sessionId);
            $resetStmt->execute();
            $resetStmt->close();
        }

        $conn->commit();
        $cleanupRequired = true;
        $players = [];
        $messages = [];
        $topicText = null;
    }

    // messagingggggg
    $messageSql = "SELECT m.message_id, m.message_text, m.sent_at,
                          UNIX_TIMESTAMP(m.sent_at) AS sent_timestamp,
                          u.user_id, u.username
                   FROM messages m
                   INNER JOIN session_participants sp ON sp.participant_id = m.participant_id
                   INNER JOIN users u ON u.user_id = sp.user_id
                   WHERE m.session_id = ?
                   ORDER BY m.sent_at ASC, m.message_id ASC";
    $messageStmt = $conn->prepare($messageSql);
    $messageStmt->bind_param("i", $sessionId);
    $messageStmt->execute();
    $messageResult = $messageStmt->get_result();
    $messages = [];
    while ($message = $messageResult->fetch_assoc()) {
        $messages[] = [
            "message_id" => (int) $message["message_id"],
            "message_text" => $message["message_text"],
            "sent_timestamp" => (int) $message["sent_timestamp"],
            "user_id" => (int) $message["user_id"],
            "username" => $message["username"],
        ];
    }
    $messageStmt->close();

    if ($sessionStatus === "waiting" && count($players) === 0) {
        $cleanupRequired = true;
    }

    header("Content-Type: application/json");
    echo json_encode([
        "players" => $players,
        "topic_text" => $topicText,
        "messages" => $messages,
        "session_status" => $sessionStatus,
        "start_timestamp" => $startTimestamp,
        "cleanup_required" => $cleanupRequired,
    ]);
    exit;
}

// Add all users to the participants row
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

// Count amount of players in session
$sql = "SELECT c.max_players, COUNT(sp.participant_id) AS participant_count
        FROM campfire_sessions cs
        INNER JOIN campfires c ON c.campfire_id = cs.campfire_id
        LEFT JOIN session_participants sp ON sp.session_id = cs.session_id
        WHERE cs.session_id = ?
        GROUP BY c.max_players";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sessionId);
$stmt->execute();
$sessionStats = $stmt->get_result()->fetch_assoc();

$maxPlayers = (int) ($sessionStats["max_players"] ?? 0);
$participantCount = (int) ($sessionStats["participant_count"] ?? 0);
$isSessionFull = $participantCount >= $maxPlayers;

if (empty($anonymousName)) {
    $anonymousName = "Player" . $userId;
}



if ($participantCount > $maxPlayers) {
    $status = "active";
    header("Location: ../pages/campGrounds.php?error=campfire_full");
    exit;    
}

if ($result->num_rows > 0) {
    $participantRow = $result->fetch_assoc();
    $participantId = (int) $participantRow["participant_id"];

} else {

    {$sql = "INSERT INTO session_participants (session_id, user_id, anonymous_name, contribution_score, joined_at)
        VALUES (?, ?, ?, 0, NOW())";
    $stmt2 = $conn->prepare($sql);
    $stmt2->bind_param("iis", $sessionId, $userId, $anonymousName);
    $stmt2->execute();
    $participantId = $stmt2->insert_id;
    $participantCount++;
    $stmt2->close();}
}
$stmt->close();

// Topic Selection
$isSessionFull = $participantCount >= $maxPlayers;

$topicText = "Topic";
$topicSql = "SELECT t.topic_text
             FROM campfire_sessions cs
             INNER JOIN topics t ON t.topic_id = cs.topic_id
             WHERE cs.session_id = ?
             LIMIT 1";
$topicStmt = $conn->prepare($topicSql);
$topicStmt->bind_param("i", $sessionId);
$topicStmt->execute();
$topicRow = $topicStmt->get_result()->fetch_assoc();
$topicText = $topicRow["topic_text"] ?? "Topic";
$topicStmt->close();

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

$remaining = "SELECT COUNT(*) AS total FROM session_participants WHERE session_id = ?";


// 4. Check participant count

// 5. Add user to session

// 6. Determine session status



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
                    <h2 class="blackText" id="chat_topic">
                        <?php echo $isSessionFull ? htmlspecialchars($topicText) : "Topic"; ?>
                    </h2>
                </div>
                <div class="chat_section">
                    <h2 class="blackText">Chat Box</h1>
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
                            <div class="chat_message" data-message-id="<?php echo (int) $message['message_id']; ?>">
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
                    <?php foreach ($participants as $index => $participant): ?>
                    <div class="player-card player-entering"
                        data-participant-id="<?php echo (int) $participant['participant_id']; ?>"
                        data-user-id="<?php echo (int) $participant['user_id']; ?>">
                        <div class="player_info">
                            <h2><?php echo htmlspecialchars($participant["username"]); ?></h2>
                            <p class="player_score">Score: 0</p>
                            <!-- <h2>Player Name</h2> -->
                        </div>
                        <div class="player_icon"></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="timer_section">
                    <div class="timer_text">
                        <h2 id="timer"></h2>
                        <h2 class="bonus_timer_text" id="bonusTimer" style="display: none"></h2>
                    </div>
                    <div class="campFireBtns">
                        <button class="startFire" id="startFire" style="display: flex" onclick="">
                            <h2>Start Fire</h2>
                        </button>
                        <button class="leaveCamp" id="leaveCampfire" style="display: flex" onclick="leaveCampfire()">
                            <h2>Leave Fire</h2>
                        </button>
                    </div>

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

        <script>
        window.currentSessionId = <?php echo (int) $sessionId; ?>;
        window.currentCampfireId = <?php echo (int) $campfireId; ?>;
        window.currentUserId = <?php echo (int) $userId; ?>;
        window.isSessionFull = <?php echo ($isSessionFull ?? false) ? 'true' : 'false'; ?>;
        </script>

        <!-- JS Script Link -->
        <script src="../js/campfireLobby.js"></script>
    </body>

</html>