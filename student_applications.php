<?php

session_start();

$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$password = getenv('MYSQLPASSWORD');
$database = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');

/*
$host = "localhost";
$dbname = "studyconnect";
$username = "StudyConnect";
$password = "Study@2025";
*/

// Create connection
$conn = mysqli_connect($host, $user, $password, $database, $port);
//$conn = new mysqli($host, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


$student_id   = $_GET['student_id'] ?? '';
$student_name = urldecode($_GET['student_name'] ?? '');
$email        = urldecode($_GET['email'] ?? '');

$sql = "SELECT *
        FROM coursechoice
        WHERE student_id = ?
        ORDER BY university_name";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>

<title>Applications & Messages</title>

<link rel="stylesheet" href="css/style.css">

<script src="js/jquery-3.6.0.min.js"></script>

<style>

body{
    margin:0;
    font-family:Segoe UI, Arial;
    background:#f4f6f9;
}

/* HEADER */

.top-header{
    background:#1e3a5f;
    color:white;
    padding:15px 25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 2px 8px rgba(0,0,0,0.1);
}

.top-header h2{
    margin:0;
    font-size:22px;
}

.student-meta{
    font-size:14px;
}

/* MAIN LAYOUT */

.main-container{
    display:flex;
    height:calc(100vh - 70px);
}

/* LEFT PANEL */

.left-panel{
    width:32%;
    background:white;
    border-right:1px solid #ddd;
    overflow-y:auto;
}

/* RIGHT PANEL */

.right-panel{
    width:68%;
    display:flex;
    flex-direction:column;
    background:#f9fafc;
}

/* APPLICATION CARD */

.application-card{
    padding:15px;
    border-bottom:1px solid #eee;
    cursor:pointer;
    transition:0.2s;
}

.application-card:hover{
    background:#f0f7ff;
}

.application-card.active{
    background:#dceeff;
    border-left:4px solid #1e88e5;
}

.uni-name{
    font-size:16px;
    font-weight:600;
    color:#1e3a5f;
}

.course-name{
    font-size:14px;
    color:#555;
    margin-top:5px;
}

.intake{
    margin-top:8px;
    font-size:12px;
    color:#777;
}

/* MESSAGE HEADER */

.message-header{
    background:white;
    padding:15px 20px;
    border-bottom:1px solid #ddd;
}

.message-header h3{
    margin:0;
    color:#1e3a5f;
}

/* MESSAGE AREA */

#messages{
    flex:1;
    overflow-y:auto;
    padding:20px;
}

/* MESSAGE BUBBLES */

.msg{
    background:white;
    padding:12px;
    margin-bottom:12px;
    border-radius:10px;
    box-shadow:0 1px 5px rgba(0,0,0,0.08);
}

.meta{
    font-size:11px;
    color:gray;
    margin-bottom:6px;
}

/* FORM */

.message-form{
    background:white;
    padding:15px;
    border-top:1px solid #ddd;
}

.message-form textarea{
    width:100%;
    height:90px;
    resize:none;
    padding:10px;
    border:1px solid #ccc;
    border-radius:8px;
    font-size:14px;
}

.send-btn{
    margin-top:10px;
    background:#1e88e5;
    color:white;
    border:none;
    padding:10px 20px;
    border-radius:8px;
    cursor:pointer;
    font-size:14px;
}

.send-btn:hover{
    background:#1565c0;
}

.empty-state{
    padding:40px;
    text-align:center;
    color:#888;
}

a{
    color:#1565c0;
    text-decoration:none;
}

a:hover{
    text-decoration:underline;
}

</style>

</head>

<body>

<div class="top-header">

    <div>
        <h2>Applications & Messages</h2>
    </div>

    <div class="student-meta">
        <b>ID:</b> <?= htmlspecialchars($student_id) ?>
        &nbsp;&nbsp;
        <b>Name:</b> <?= htmlspecialchars($student_name) ?>
        &nbsp;&nbsp;
        <b>Email:</b> <?= htmlspecialchars($email) ?>
    </div>

</div>

<div class="main-container">

    <!-- LEFT PANEL -->

    <div class="left-panel">
			<div class="empty-state">
               <h3> University applications submitted</h3>
            </div>

		<?php if ($result && $result->num_rows <= 0) { ?>
			<div class="empty-state">
               <h4> No applications submitted</h4>
            </div>
		<?php } ?>


        <?php while($row = $result->fetch_assoc()) { ?>

           <div class="application-card"

    data-studentid="<?= $row['student_id'] ?>"

    data-university="<?= htmlspecialchars($row['University_Name'], ENT_QUOTES) ?>"

    data-country="<?= htmlspecialchars($row['COUNTRY_CODE'], ENT_QUOTES) ?>"

    data-course="<?= htmlspecialchars($row['Course_Name'], ENT_QUOTES) ?>"

    data-courseurl="<?= htmlspecialchars($row['Course_URL'], ENT_QUOTES) ?>"

    data-intakemonth="<?= htmlspecialchars($row['Intake_Month'], ENT_QUOTES) ?>"

    data-intakeyear="<?= htmlspecialchars($row['Intake_Year'], ENT_QUOTES) ?>"

    onclick="loadConversation(this)">

    <div class="uni-name">
        <?= htmlspecialchars($row['University_Name']) ?>
    </div>

    <div class="course-name">
        <?= htmlspecialchars($row['Course_Name']) ?>
    </div>

    <div class="intake">
        <?= htmlspecialchars($row['Intake_Month']) ?>
        <?= htmlspecialchars($row['Intake_Year']) ?>
    </div>

    <div style="margin-top:8px; font-size:12px;">
        🌍 <?= htmlspecialchars($row['COUNTRY_CODE']) ?>
    </div>

    <?php if(!empty($row['Course_URL'])) { ?>

        <div style="margin-top:8px;">

            <a href="<?= htmlspecialchars($row['Course_URL']) ?>"
               target="_blank"
               onclick="event.stopPropagation();">

               Course URL

            </a>

        </div>

    <?php } ?>

</div>


        <?php } ?>

    </div>

    <!-- RIGHT PANEL -->

    <div class="right-panel">

        <div class="message-header">
            <h3 id="selectedUniversity">
                Select an application
            </h3>
        </div>

        <div id="messages">

            <div class="empty-state">
               <h2> Select a university application from the left panel</h2>
            </div>

        </div>

        <div class="message-form">

            <form id="messageForm">

                <input type="hidden"
                       name="student_id"
                       value="<?= $student_id ?>">

                <input type="hidden"
                       name="university"
                       id="hiddenUniversity">

                <textarea
                    name="message"
                    placeholder="Type your message..."
                    required></textarea>

                <button type="submit" class="send-btn">
                    Send Message
                </button>

            </form>

        </div>

    </div>

</div>

<script>

let currentUniversity = "";
let currentStudentId = "<?= $student_id ?>";

/* LOAD CONVERSATION */

function loadConversation(card)
{
    let studentId   = card.dataset.studentid;
    let university  = card.dataset.university;
    let country     = card.dataset.country;
    let course      = card.dataset.course;
    let courseUrl   = card.dataset.courseurl;
    let intakeMonth = card.dataset.intakemonth;
    let intakeYear  = card.dataset.intakeyear;

    currentUniversity = university;

    $("#hiddenUniversity").val(university);

    let html = "";

    html += '<div style="font-size:22px;font-weight:600;">'
         + university +
         '</div>';

    html += '<div style="margin-top:6px;color:#666;">'
         + course +
         '</div>';

    html += '<div style="margin-top:6px;font-size:13px;">'
         + '🌍 ' + country +
         '&nbsp;&nbsp;&nbsp; Intake: '
         + intakeMonth + ' ' + intakeYear +
         '</div>';

    if(courseUrl !== '')
    {
        html += '<div style="margin-top:8px;">'
             + '<a href="' + courseUrl + '" target="_blank">'
             + 'Course URL Page'
             + '</a>'
             + '</div>';
    }

    $("#selectedUniversity").html(html);

    $(".application-card").removeClass("active");

    card.classList.add("active");

    fetchMessages();
}




/* FETCH MESSAGES */

function fetchMessages(){

    if(currentUniversity === "") return;

    $.ajax({

        url:"fetch_messages.php",

        method:"GET",

        data:{
            student_id: currentStudentId,
            university: currentUniversity
        },

        success:function(data){

            $("#messages").html(data);

            $("#messages").scrollTop(
                $("#messages")[0].scrollHeight
            );
        }
    });
}

/* SEND MESSAGE */

$("#messageForm").submit(function(e){

    e.preventDefault();

    if(currentUniversity === ""){

        alert("Please select an application first");
        return;
    }

    let messageText =
        $('textarea[name="message"]').val();

    $.ajax({

        url:"insert_message.php",

        method:"POST",

        data: $(this).serialize(),

        success:function(){

let newMsg =
    '<div class="msg">' +
        '<div class="meta">' +
            'You | Just now' +
        '</div>' +
        '<div>' + messageText + '</div>' +
    '</div>';


            $("#messages").append(newMsg);

            $('textarea[name="message"]').val('');

            $("#messages").scrollTop(
                $("#messages")[0].scrollHeight
            );
        }
    });
});

/* AUTO REFRESH */

setInterval(fetchMessages, 5000);

</script>

</body>
</html>
