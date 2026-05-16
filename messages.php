<?php
session_start();

$student_id = $_GET['student_id'] ?? '';
$university = $_GET['university'] ?? '';

$student_name = $_GET['student_name'] ?? '';
$email = $_GET['email'] ?? '';
$student_email= $_GET['student_email'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="css/style.css">
    <title>Messages</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body { font-family: Arial; }
        #container { width: 700px; margin: auto; }
        #messages {
            border: 1px solid #ccc;
            height: 400px;
            overflow-y: auto;
            padding: 10px;
            border-radius: 6px;
            background: #f9f9f9;
        }
        .msg {
            border: 1px solid #ddd;
            padding: 8px;
            margin: 6px 0;
            border-radius: 6px;
            background: #fff;
        }
        .meta {
            font-size: 11px;
            color: gray;
        }
        textarea {
            width: 100%;
            height: 80px;
        }
    </style>
	
<style>

.student-info {
    background: #ffffff;
    border: 1px solid #dfe3e8;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.student-info table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 10px;
}

.student-info td {
    border: none;
    padding: 4px;
    font-size: 14px;
}

.student-info label {
    font-weight: 600;
    color: #444;
}

.student-info input[readonly],
.student-info textarea[readonly] {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #d0d7de;
    border-radius: 6px;
    background-color: #f9fafb;
    color: #333;
    font-size: 14px;
    outline: none;
    transition: 0.2s ease;
    box-sizing: border-box;
}

.student-info input[readonly]:focus,
.student-info textarea[readonly]:focus {
    border-color: #90caf9;
    box-shadow: 0 0 4px rgba(144,202,249,0.5);
}

</style>

</head>
<body>

<div id="container">
<!--
<h3>
Messages - Student ID: <?= $student_id ?> | University: <?= htmlspecialchars($university) ?>
</h3>
-->

<div class="student-info">

    <table width="100%" border="1" cellspacing="0" cellpadding="5">

        <tr>
            <td><b>Student ID</b></td>
            <td>
                <input type="text"
                       value="<?= htmlspecialchars($student_id) ?>"
                       readonly>
            </td>

            <td><b>University</b></td>
            <td>
                <input type="text"
                       value="<?= htmlspecialchars($university) ?>"
                       readonly>
            </td>
        </tr>

        <tr>
            <td><b>Student Name</b></td>
            <td>
                <input type="text"
                       value="<?= htmlspecialchars($student_name) ?>"
                       readonly>
            </td>

            <td><b>Email</b></td>
            <td>
                <input type="text"
                       value="<?= htmlspecialchars($student_email) ?>"
                       readonly>
            </td>
        </tr>

    </table>

</div>
<!-- ------------------------------------------- -->

<div id="messages"></div>

<br>

<form id="messageForm">
    <input type="hidden" name="student_id" value="<?= $student_id ?>">
    <input type="hidden" name="university" value="<?= htmlspecialchars($university) ?>">
	


    <textarea name="message" placeholder="Type your message..." required></textarea><br><br>
    <button type="submit">Send</button>
</form>

</div>

<script>

const studentId = "<?= $student_id ?>";
const university = "<?= $university ?>";

// Load messages
function loadMessages() {
    $.ajax({
        url: "fetch_messages.php",
        method: "GET",
        data: {
            student_id: studentId,
            university: university
        },
        success: function(data) {
            $("#messages").html(data);
        }
    });
}

// Send message
$("#messageForm").submit(function(e) {
    e.preventDefault();

    let messageText = $('textarea[name="message"]').val();

    $.ajax({
        url: "insert_message.php",
        method: "POST",
        data: $(this).serialize(),
        success: function() {

            // Show instantly
            let newMsg = `
                <div class="msg">
                    <div class="meta">You | Just now</div>
                    <p>${messageText}</p>
                </div>
            `;

            $("#messages").prepend(newMsg);

            $("#messageForm")[0].reset();
        }
    });
});

// Auto refresh every 5 seconds
setInterval(loadMessages, 5000);

// Initial load
loadMessages();

</script>

</body>
</html>