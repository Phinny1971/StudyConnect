<?php

ob_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once 'session_check.php';
requirePermission('student.view');

require_once 'includes/db_connection.php';
require_once 'includes/access_helper.php';
require_once 'includes/flash_message.php';

?>

<script>
const csrfToken = <?= json_encode($_SESSION['csrf_token']) ?>;
</script>

<?php

$student_id = isset($_GET['student_id'])
    ? intval($_GET['student_id'])
    : 0;

if ($student_id <= 0) {

    setFlashMessage(
        'error',
        'Invalid Student',
        'Invalid student selected.'
    );

    header("Location: student_list.php");
    exit();
}

$stmt = $conn->prepare("
    SELECT *
    FROM studentdetails
    WHERE student_id = ?
");

$stmt->bind_param("i", $student_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    setFlashMessage(
        'error',
        'Student Not Found',
        'The requested student does not exist.'
    );

    header("Location: student_list.php");
    exit();
}

$student = $result->fetch_assoc();

$accessibleBranches = getAccessibleBranches($conn);

$allowedBranches = array_column(
    $accessibleBranches,
    'Branch_name'
);

if (
    !in_array(
        $student['Branch_name'],
        $allowedBranches,
        true
    )
) {

    setFlashMessage(
        'error',
        'Access Denied',
        'You are not authorized to view this student.'
    );

    $conn->close();

    header("Location: student_list.php");
    exit();
}

$stmt->close();

$student_name = $student['name'];
$email = $student['email'];

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

.doc-badge{
    display:inline-block;
    background:#e3f2fd;
    color:#1565c0;
    padding:4px 10px;
    border-radius:20px;
    font-size:12px;
    font-weight:bold;
    margin-top:8px;
}

.doc-actions{
    margin-top:10px;
    padding-top:8px;
    border-top:1px solid #eee;
}

.doc-actions a{
    margin-right:12px;
    font-size:13px;
    font-weight:600;
}

/* PAYMENT TOGGLE */

.payment-container{
    display:flex;
    align-items:center;
    gap:6px;
}

.switch{
    position:relative;
    display:inline-block;
    width:40px;
    height:22px;
}

.switch input{
    opacity:0;
    width:0;
    height:0;
}

.slider{
    position:absolute;
    cursor:pointer;
    top:0;
    left:0;
    right:0;
    bottom:0;
    background:#ccc;
    transition:.3s;
    border-radius:22px;
}

.slider:before{
    position:absolute;
    content:"";
    height:16px;
    width:16px;
    left:3px;
    bottom:3px;
    background:white;
    transition:.3s;
    border-radius:50%;
}

.switch input:checked + .slider{
    background:#28a745;
}

.switch input:checked + .slider:before{
    transform:translateX(18px);
}

.payment-status{
    font-size:11px;
    font-weight:600;
}

.payment-status.paid{
    color:#28a745;
}

.payment-status.unpaid{
    color:#888;
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

<div style="
    margin-top:8px;
    display:flex;
    justify-content:space-between;
    align-items:center;">

    <div>

        <?php if(!empty($row['Course_URL'])) { ?>

            <a href="<?= htmlspecialchars($row['Course_URL']) ?>"
               target="_blank"
               onclick="event.stopPropagation();">

               Course URL

            </a>

        <?php } ?>

    </div>

    <div class="payment-container">

        <label class="switch">

            <input
                type="checkbox"
                <?= ($row['Payment_Status'] == 1 ? 'checked' : '') ?>

                onclick="
                    event.stopPropagation();

                    togglePaymentStatus(
                        <?= $row['student_id'] ?>,
                        '<?= addslashes($row['University_Name']) ?>',
                        this
                    );
                ">

            <span class="slider"></span>

        </label>

        <span
			class="payment-status <?= ($row['Payment_Status'] == 1 ? 'paid' : 'unpaid') ?>">
			<?= ($row['Payment_Status'] == 1 ? 'Paid' : 'Unpaid') ?>
		</span>
		
		

    </div>

</div>

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

           <!-- <form id="messageForm"> -->
		   <form id="messageForm" enctype="multipart/form-data">

                <input type="hidden"
                       name="student_id"
                       value="<?= $student_id ?>">

                <input type="hidden"
                       name="university"
                       id="hiddenUniversity">

				<div style="display:flex; gap:15px; margin-bottom:12px;">

				<div style="flex:1;">

					<label><b>Your Docs (SOPs & Other Docs)</b></label><br>

					<input type="file"
						   name="your_docs"
						   accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
						   style="margin-top:5px; width:100%;">

					</div>

					<div style="flex:1;">

						<label><b>HO Docs</b></label><br>

					<input type="file"
					   name="ho_docs"
					   accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
					   style="margin-top:5px; width:100%;">

					</div>

				</div>

				<div style="margin-bottom:12px;">

					<label><b>Payment Link</b></label><br>

					<input type="url"
						   name="payment_link"
						   placeholder="Paste payment URL here"
						   style="width:100%;
								  padding:8px;
								  border:1px solid #ccc;
								  border-radius:6px;
								  margin-top:5px;">

				</div>

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

         /*   $("#messages").scrollTop(
                $("#messages")[0].scrollHeight
            );
		*/
		
        }
		
		
    });
}

/* SEND MESSAGE */
/*
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
*/

$("#messageForm").submit(function(e){

    e.preventDefault();

    if(currentUniversity === ""){

        alert("Please select an application first");
        return;
    }

    let messageText =
        $('textarea[name="message"]').val();

    let formData = new FormData(this);
	
	formData.append("csrf_token", csrfToken);

    $.ajax({

        url:"insert_message.php",

        method:"POST",

        data: formData,

        processData:false,

        contentType:false,

		success:function(){

			$("#messageForm")[0].reset();

			fetchMessages();
		}
    });
});

function openDocument(filePath)
{
    let ext =
        filePath.split('.').pop().toLowerCase();

    if(
        ext === 'pdf' ||
        ext === 'jpg' ||
        ext === 'jpeg' ||
        ext === 'png'
    )
    {
        $("#docFrame").attr("src", filePath);

        $("#docModal").show();
    }
    else
    {
        window.open(filePath, "_blank");
    }
}

function closeDocument()
{
    $("#docFrame").attr("src", "");
    $("#docModal").hide();
}

function togglePaymentStatus(studentId, university, checkbox)
{
    let paymentStatus =
        checkbox.checked ? 1 : 0;

    let statusText =
        $(checkbox)
        .closest(".payment-container")
        .find(".payment-status");

    $.ajax({

        url:"update_payment_status.php",

        method:"POST",

		data:{
			student_id:studentId,
			university:university,
			payment_status:paymentStatus,
			csrf_token:csrfToken
		},

        success:function()
        {
            if(paymentStatus)
            {
                statusText
                    .text("Paid")
                    .removeClass("unpaid")
                    .addClass("paid");
            }
            else
            {
                statusText
                    .text("Unpaid")
                    .removeClass("paid")
                    .addClass("unpaid");
            }
        }
    });
}


/* AUTO REFRESH */

setInterval(fetchMessages, 5000);

</script>


<div id="docModal"
     style="
     display:none;
     position:fixed;
     top:0;
     left:0;
     width:100%;
     height:100%;
     background:rgba(0,0,0,0.7);
     z-index:9999;">

    <div style="
         position:absolute;
         top:5%;
         left:5%;
         width:90%;
         height:90%;
         background:white;
         border-radius:10px;
         overflow:hidden;">

        <div style="
             background:#1e3a5f;
             color:white;
             padding:10px;
             font-weight:bold;">

            Document Preview

            <button
                onclick="closeDocument()"
                style="
                float:right;
                background:red;
                color:white;
                border:none;
                padding:5px 10px;
                cursor:pointer;">
                Close
            </button>

        </div>

        <iframe
            id="docFrame"
            style="
            width:100%;
            height:calc(100% - 45px);
            border:none;">
        </iframe>

    </div>

</div>
</body>
</html>
