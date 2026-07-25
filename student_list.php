<?php
require_once 'session_check.php';
requirePermission('student.view');



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

if ($conn->connect_error) {
  http_response_code(500);
  die("Connection failed: " . $conn->connect_error);
}

/*
// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  $student_id = intval($_POST['delete_id']);
  $conn->begin_transaction();
  try {
    $delLang = $conn->prepare("DELETE FROM studentlanguagetests WHERE student_id = ?");
    $delLang->bind_param("i", $student_id);
    $delLang->execute();

    $delStudent = $conn->prepare("DELETE FROM studentdetails WHERE student_id = ?");
    $delStudent->bind_param("i", $student_id);
    $delStudent->execute();

    $conn->commit();
    echo "success";
  } catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo "DB Error: " . $e->getMessage();
  }
  exit;
}
*/

$sql = "SELECT student_id, name, email, branch_name, preferred_country,  DATE_FORMAT(DateOfBirth,'%d-%m-%Y') AS DateOfBirth, Passport_no, Passport_issue, country_code, DATE_FORMAT(created_at,'%d-%m-%Y %H:%i') AS created_at FROM studentdetails ORDER BY student_id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="css/style.css">

  <meta charset="UTF-8">
  <title>Student List</title>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
  <style>
    table.dataTable tbody tr:hover {
      background-color: #f0f8ff;
    }
    .delete-btn { color: red; cursor: pointer; }
    .edit-btn { color: blue; cursor: pointer; }
  </style>
  
      <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- jQuery + DataTables CSS/JS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">


  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
    }

    table {
      border-collapse: collapse;
      width: 100%;
      margin-top: 20px;
    }

    th, td {
      padding: 12px;
      border: 1px solid #ccc;
      text-align: left;
      position: relative;
    }

    tr.clickable-row {
      transition: background-color 0.2s ease;
    }

    tr.clickable-row:hover {
      background-color: #e8f4ff;
      cursor: pointer;
    }

    tr.clickable-row:hover::after {
      content: "Click to view full student details";
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 12px;
      color: #444;
      background-color: #f2f2f2;
      padding: 3px 6px;
      border-radius: 4px;
      pointer-events: none;
    }

    tr.clickable-row:hover td:last-child::after {
      content: "\f06e"; /* Font Awesome eye icon */
      font-family: "Font Awesome 6 Free";
      font-weight: 900;
      margin-left: 10px;
      font-size: 16px;
      color: #333;
    }

    .logo {
      max-width: 200px;
    }
  </style>
  
</head>
<body>


<h2>Student List </h2>

<hr>


<table id="studentTable" class="display nowrap" style="width:100%">
  <thead>
    <tr>
      <th>Code</th>
	  <th>Application Date</th>
      <th>Name</th>
      <th>Email</th>
      <th>Branch</th>
      <th>Country Applied</th>
      <th>Date of Birth</th>
      <th>Passport No</th>
    
   
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['country_code'] . $row['student_id']) ?></td>
		  <td><?= htmlspecialchars($row['created_at']) ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= htmlspecialchars($row['branch_name']) ?></td>
          <td><?= htmlspecialchars($row['preferred_country']) ?></td>
          <td><?= htmlspecialchars($row['DateOfBirth']) ?></td>
          <td><?= htmlspecialchars($row['Passport_no']) ?></td>
          
          
		
          <td>
            
			<?php if (hasPermission('student.edit')) : ?>
			<button
				class="edit-btn"
				title="Edit/Modify Student Details"
				onclick="editStudent(<?= $row['student_id'] ?>)">
				✏️
			</button>
			<?php endif; ?>
	
			<button class="applications-btn" title="Applications & Messages"
			onclick='openApplications(<?= (int)$row["student_id"] ?>,<?= json_encode($row["name"]) ?>,<?= json_encode($row["email"]) ?>)'>
			📚 
			</button>

			<?php if (hasPermission('student.delete')) : ?>
			<button
				class="delete-btn"
				title="DELETE Student Details"
				onclick="deleteStudent(<?= $row['student_id'] ?>, this)">
				🗑️
			</button>
			<?php endif; ?>
			
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
     <!--  <tr><td colspan="10">No students found.</td></tr> -->
     <tr>
    <td colspan="10">No students found.</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
    <?php endif; ?>
  </tbody>
</table>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>

const csrfToken = <?= json_encode($_SESSION['csrf_token']) ?>;

console.log("CSRF Token:", csrfToken);
/*
  $(document).ready(function() {
    $('#studentTable').DataTable({
      dom: 'Bfrtip',
      buttons: ['excelHtml5', 'pdfHtml5', 'print'],
      responsive: true
    });
  });
*/
 
  $(document).ready(function() {
	  $('#studentTable').DataTable({
			dom: 'Bfrtip',
			buttons: [
				'excelHtml5',
				{
					extend: 'pdfHtml5',
					exportOptions: {
						columns: ':not(:last-child)'
					}
				},
				{
					extend: 'print',
					exportOptions: {
						columns: ':not(:last-child)'
					}
				}
			],
			responsive: true
		});
	});
  
  function editStudent(student_id) {
    console.log("Redirecting to student_edit.php?student_id=" + student_id);
    window.location.href = 'student_edit.php?student_id=' + student_id;
  }


function deleteStudent(student_id, btn) {

    showModal({
        title: 'Confirm Delete',
        message: 'Are you sure you want to delete student ID: ' + student_id + '?',
        showYesNo: true,
		showOk: false,

        onYes: function () {

            // Disable button
            btn.disabled = true;
            btn.innerText = "Deleting...";

            $.post('delete_student.php',
                { 
				delete_id: student_id,
				csrf_token: csrfToken 
				},

                function(response) {

                    if (response.trim() === "success") {

                        $(btn).closest('tr').fadeOut(300, function() {
                            $(this).remove();
                        });

                    } else {

                        showModal({
                            title: 'Delete Failed',
                            message: response,
                            showOk: true
                        });

                        btn.disabled = false;
                        btn.innerText = "🗑️";
                    }
                }

            ).fail(function(xhr) {
				if (
					xhr.status === 401 ||
					xhr.responseText === 'SESSION_EXPIRED'
				) {

					window.location.href = 'main.php?expired=1';
					return;
				}

				showModal({
					title: 'AJAX Error',
					message: xhr.responseText,
					showOk: true
				});

				btn.disabled = false;
				btn.innerText = "🗑️";
			});

        }

    });
}
</script>


<script>
function showModal(options) {

console.log("Modal Called");

    // Default values
    let settings = {
        title: "Message",
        message: "",
        showYesNo: false,
        showOk: true,
        onYes: null,
        onNo: null,
        onOk: null
    };

    // Merge options
    settings = { ...settings, ...options };

    // Elements
    const modal = document.getElementById("customModal");

    const title = document.getElementById("modalTitle");
    const message = document.getElementById("modalMessage");

    const yesBtn = document.getElementById("modalYesBtn");
    const noBtn = document.getElementById("modalNoBtn");
    const okBtn = document.getElementById("modalOkBtn");

    // Set content
    title.innerText = settings.title;
    message.innerHTML = settings.message;

    // Show/hide buttons
    yesBtn.style.display = settings.showYesNo ? "inline-block" : "none";
    noBtn.style.display = settings.showYesNo ? "inline-block" : "none";

    okBtn.style.display = settings.showOk ? "inline-block" : "none";

    // Remove old handlers
    yesBtn.onclick = null;
    noBtn.onclick = null;
    okBtn.onclick = null;

    // Yes button
    yesBtn.onclick = function () {
        closeMsgModal();
        if (settings.onYes) settings.onYes();
    };

    // No button
    noBtn.onclick = function () {
        closeMsgModal();
        if (settings.onNo) settings.onNo();
    };

    // OK button
    okBtn.onclick = function () {
        closeMsgModal();
        if (settings.onOk) settings.onOk();
    };

    // Show modal
    modal.style.display = "flex";
}

function closeMsgModal() {
    document.getElementById("customModal").style.display = "none";
}

function openApplications(studentId, studentName, email) {

    const url =
        "student_applications.php?student_id=" +
        encodeURIComponent(studentId) +
        "&student_name=" +
        encodeURIComponent(studentName) +
        "&email=" +
        encodeURIComponent(email);

    window.location.href = url;
}
</script>

<!-- Custom Modal -->
<?php include 'modal.php'; ?>
</body>
</html>
