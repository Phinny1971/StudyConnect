<?php

require_once 'includes/db.php';

$session_id = $_GET['session_id'] ?? 0;

$stmt = $conn->prepare("
    SELECT sender, message, created_at
    FROM magi_chat_messages
    WHERE session_id=?
    ORDER BY created_at ASC
");

$stmt->bind_param("i", $session_id);

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>

<head>

<title>MAGI AI Live Chat</title>

<style>

body{
    font-family:Arial;
    background:#F1F5F9;
    margin:0;
}

.header{
    background:linear-gradient(135deg,#2563EB,#7C3AED);
    color:white;
    padding:18px;
    font-size:22px;
    font-weight:bold;
}

.chat-container{
    padding:20px;
    height:calc(100vh - 90px);
    overflow-y:auto;
}

.message{
    margin-bottom:18px;
    max-width:75%;
    padding:14px;
    border-radius:16px;
}

.user{
    background:#2563EB;
    color:white;
    margin-left:auto;
}

.ai{
    background:white;
    color:#111827;
    box-shadow:0 3px 10px rgba(0,0,0,0.08);
}

.admin{
    background:#7C3AED;
    color:white;
}

.time{
    font-size:11px;
    opacity:0.7;
    margin-top:6px;
}

</style>

</head>

<body>

<div class="header">
✨ MAGI AI - Live Conversation
</div>

<div class="chat-container">

<?php while($row = $result->fetch_assoc()) { ?>

<div class="message <?php echo $row['sender']; ?>">

<div>
<?php echo nl2br(htmlspecialchars($row['message'])); ?>
</div>

<div class="time">
<?php echo $row['created_at']; ?>
</div>

</div>

<?php } ?>

</div>

</body>

</html>