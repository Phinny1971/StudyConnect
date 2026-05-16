<?php

require_once 'includes/db.php';

$result = $conn->query("
    SELECT
        s.session_id,
        s.student_id,
        s.status,
        s.started_at
    FROM magi_chat_sessions s
    ORDER BY s.started_at DESC
");

?>

<!DOCTYPE html>
<html>

<head>

<title>MAGI AI Admin Panel</title>

<style>

body{
    font-family: Arial;
    background:#F1F5F9;
    padding:20px;
}

.panel{
    background:white;
    border-radius:16px;
    padding:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

table{
    width:100%;
    border-collapse:collapse;
}

th, td{
    padding:14px;
    border-bottom:1px solid #E5E7EB;
}

th{
    background:#2563EB;
    color:white;
}

.badge{
    padding:6px 12px;
    border-radius:20px;
    color:white;
    font-size:12px;
}

.active{
    background:#10B981;
}

.escalated{
    background:#EF4444;
}

button{
    background:#2563EB;
    color:white;
    border:none;
    padding:10px 14px;
    border-radius:8px;
    cursor:pointer;
}

</style>

</head>

<body>

<div class="panel">

<h2>✨ MAGI AI - Counselor Dashboard</h2>

<table>

<tr>
    <th>Session</th>
    <th>Student ID</th>
    <th>Status</th>
    <th>Started</th>
    <th>Action</th>
</tr>

<?php while($row = $result->fetch_assoc()) { ?>

<tr>

<td>
    <?php echo $row['session_id']; ?>
</td>

<td>
    <?php echo $row['student_id']; ?>
</td>

<td>

<span class="badge <?php echo $row['status']; ?>">

<?php echo ucfirst($row['status']); ?>

</span>

</td>

<td>
    <?php echo $row['started_at']; ?>
</td>

<td>

<button onclick="openChat(<?php echo $row['session_id']; ?>)">
    Open Chat
</button>

</td>

</tr>

<?php } ?>

</table>

</div>

<script>

function openChat(sessionId)
{
    window.location =
        'view_chat.php?session_id=' + sessionId;
}

</script>

</body>

</html>