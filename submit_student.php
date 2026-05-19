<link rel="stylesheet" href="css/style.css">

<!-- Custom Modal -->
<?php include 'modal.php'; ?>

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
</script>

<?php
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

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Create uploads directory if not exists
$uploadDir = "uploads/";
if (!file_exists($uploadDir)) {
  mkdir($uploadDir, 0777, true);
}

//Check for Record already exists in combo DOB and PassportNo
//-------------------------------
$date = DateTime::createFromFormat('Y-m-d', $_POST['DateOfBirth']);
$dob = $date->format('Y-m-d'); // convert to correct DB format
$pport= $_POST['Passport_no'];
$checkSql = "SELECT * FROM studentdetails WHERE Passport_no = ? AND DateOfBirth = ?";
$stmt = $conn->prepare($checkSql);
$stmt->bind_param("ss", $pport, $dob);
$stmt->execute();
$result = $stmt->get_result();
//-------------------------------

if ($result->num_rows === 0) {

function uploadFile($fieldName) {
  global $uploadDir;
  if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] != 0) return "";

  $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
  $ext = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
  if (!in_array($ext, $allowed)) return "";

  $newFileName = uniqid() . '_' . basename($_FILES[$fieldName]["name"]);
  $targetPath = $uploadDir . $newFileName;
  if (move_uploaded_file($_FILES[$fieldName]["tmp_name"], $targetPath)) {
    return $targetPath;
  }
  return "";
}


$name = $_POST['name'];
$address = $_POST['address'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$preferred_country = $_POST['preferred_country'];

$courses = json_decode($_POST['courses'], true);
//echo "<pre>";
//print_r($courses);
//if (isset($_POST['coursesInput'])) {
    
//}

//GET COUNTRY CODE 
//----------------
$sql = "SELECT country_code FROM countries WHERE country_name = ?";
$stmt = $conn->prepare($sql);
// Bind parameter and execute
$stmt->bind_param("s", $preferred_country);
$stmt->execute();
// Get the result
$result = $stmt->get_result();
// Fetch and display result
$row = $result->fetch_assoc();
$Country_code = trim(htmlspecialchars($row['country_code']));
// Close sql Statement
$stmt->close();
//----------------

$other_country= $_POST['other_country'];
$Branch_name= $_POST['Branch_name'];
$DateOfBirth= $_POST['DateOfBirth'];

$Passport_no=$_POST['Passport_no'];
$Passport_issue=$_POST['Passport_issue'];
$Passport_Expiry=$_POST['Passport_Expiry'];
$Passport_Upload=uploadFile('Passport_Upload');

// Marks
$marks = [
  '10th' => $_POST['marks_10th'],
  'intermediate' => $_POST['marks_intermediate'],
  'degree' => $_POST['marks_degree'],
  'pg' => $_POST['marks_pg'] ?? null,
  'diploma' => $_POST['marks_diploma'] ?? null,
  'other' => $_POST['marks_other'] ?? null
];

// Uploads Marks
$certs = [
  '10th' => uploadFile('cert_10th'),
  'intermediate' => uploadFile('cert_intermediate'),
  'degree' => uploadFile('cert_degree'),
  'pg' => uploadFile('cert_pg'),
  'diploma' => uploadFile('cert_diploma'),
  'other' => uploadFile('cert_other')
];

// Experience
$experience = [
  'Exp1From_date' => $_POST['Exp1From_date'],
  'Exp1To_date' => $_POST['Exp1To_date'],
  'Exp2From_date' => $_POST['Exp2From_date'],
  'Exp2To_date' => $_POST['Exp2To_date'] ?? null,
  'Exp3From_date' => $_POST['Exp3From_date'] ?? null,
  'Exp3To_date' => $_POST['Exp3To_date'] ?? null
];

// Uploads Experience
$certsExp = [
  'Exp1_Cert' => uploadFile('Exp1_Cert'),
  'Exp2_Cert' => uploadFile('Exp2_Cert'),
  'Exp3_Cert' => uploadFile('Exp3_Cert')
];



// Prepare SQL
$sql = "INSERT INTO studentdetails 
(name, address, email, phone, preferred_country, 
 marks_10th, cert_10th,
 marks_intermediate, cert_intermediate,
 marks_degree, cert_degree,
 marks_pg, cert_pg,
 marks_diploma, cert_diploma,
 marks_other, cert_other,
 Exp1From_date, Exp1To_date, Exp1_Cert,
 Exp2From_date, Exp2To_date, Exp2_Cert, 
 Exp3From_date, Exp3To_date, Exp3_Cert,
 Country_code, other_country, Branch_name, 
 DateOfBirth, Passport_no, Passport_issue, Passport_Expiry, Passport_Upload
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
	
$stmt->bind_param("ssssssssssssssssssssssssssssssssss",
  $name, $address, $email, $phone, $preferred_country,
  $marks['10th'], $certs['10th'],
  $marks['intermediate'], $certs['intermediate'],
  $marks['degree'], $certs['degree'],
  $marks['pg'], $certs['pg'],
  $marks['diploma'], $certs['diploma'],
  $marks['other'], $certs['other'],
  $experience['Exp1From_date'], $experience['Exp1To_date'], $certsExp['Exp1_Cert'],
  $experience['Exp2From_date'], $experience['Exp2To_date'], $certsExp['Exp2_Cert'],
  $experience['Exp3From_date'], $experience['Exp3To_date'], $certsExp['Exp3_Cert'],
  $Country_code, $other_country, $Branch_name,
  $DateOfBirth, $Passport_no, $Passport_issue, $Passport_Expiry, $Passport_Upload
);

 
if ($stmt->execute()) {
	$last_id = mysqli_insert_id($conn);
  //echo "Student details saved successfully!";
  saveLangAptTest($conn, $last_id, $Country_code);
  saveCourseChoices($conn, $last_id, $Country_code, $courses);
 //echo "<script type='text/javascript'>alert('Student details saved successfully!. Student Id : " . $Country_code . $last_id . "');  </script>";
 

echo "<script type='text/javascript'>
showModal({
    title: 'Success',
    message: 'Student details saved successfully. Student Id: " . $Country_code . $last_id . "',
    showOk: true, onOk: function () {
        window.location.href = 'student_list.php';
    }
}); 
</script>";



} else {
  echo "Error: " . $stmt->error;
}

}
else{      //else for record already exists if condition at tht top

 //echo "<script type='text/javascript'>alert('Student details Already exists for the Passport Number & Date of Birth..!! RECORD NOT SAVED.'); history.back(); </script>";
	//echo "Student details Already exists for the Passport Number & Date of Birth..!!";

echo "<script>

showModal({
    title: 'Attention..!!',
    message: 'Student details Already exists for the Passport Number & Date of Birth..!! RECORD NOT SAVED.',
    showOk: true,
    onOk: function () {
        history.back();
    }
});

</script>";

}

$conn->close();

function saveLangAptTest($conn, $last_id, $Country_code)
{
$IELTS_OA= $_POST['IELTS_OA'];
$IELTS_READ= $_POST['IELTS_READ'];
$IELTS_WRITE= $_POST['IELTS_WRITE'];
$IELTS_SPEAK= $_POST['IELTS_SPEAK'];
$IELTS_LISTEN= $_POST['IELTS_LISTEN'];
$PTE_OA= $_POST['PTE_OA'];
$PTE_READ= $_POST['PTE_READ'];
$PTE_WRITE= $_POST['PTE_WRITE'];
$PTE_SPEAK= $_POST['PTE_SPEAK'];
$PTE_LISTEN= $_POST['PTE_LISTEN'];
$TOEFL_OA= $_POST['TOEFL_OA'];
$TOEFL_READ= $_POST['TOEFL_READ'];
$TOEFL_WRITE= $_POST['TOEFL_WRITE'];
$TOEFL_SPEAK= $_POST['TOEFL_SPEAK'];
$TOEFL_LISTEN= $_POST['TOEFL_LISTEN'];
$LANGCERT_OA= $_POST['LANGCERT_OA'];
$LANGCERT_READ= $_POST['LANGCERT_READ'];
$LANGCERT_WRITE= $_POST['LANGCERT_WRITE'];
$LANGCERT_SPEAK= $_POST['LANGCERT_SPEAK'];
$LANGCERT_LISTEN= $_POST['LANGCERT_LISTEN'];
$DULINGO_OA= $_POST['DULINGO_OA'];
$DULINGO_READ= $_POST['DULINGO_READ'];
$DULINGO_WRITE= $_POST['DULINGO_WRITE'];
$DULINGO_SPEAK= $_POST['DULINGO_SPEAK'];
$DULINGO_LISTEN= $_POST['DULINGO_LISTEN'];
$ENGOTHER_OA= $_POST['ENGOTHER_OA'];
$ENGOTHER_READ= $_POST['ENGOTHER_READ'];
$ENGOTHER_WRITE= $_POST['ENGOTHER_WRITE'];
$ENGOTHER_SPEAK= $_POST['ENGOTHER_SPEAK'];
$ENGOTHER_LISTEN= $_POST['ENGOTHER_LISTEN'];
$ENGOTHER_NAME= $_POST['ENGOTHER_NAME'];
$GRE_OA= $_POST['GRE_OA'];
$SAT_OA= $_POST['SAT_OA'];
$GMAT_OA= $_POST['GMAT_OA'];
$APTOTHER_NAME= $_POST['APTOTHER_NAME'];
$APTOTHER_OA= $_POST['APTOTHER_OA'];

$ENGOTHER_UPLOAD= uploadFile('ENGOTHER_UPLOAD');
$IELTS_UPLOAD= uploadFile('IELTS_UPLOAD');
$APTOTHER_UPLOAD= uploadFile('APTOTHER_UPLOAD');
$GMAT_UPLOAD= uploadFile('GMAT_UPLOAD');
$SAT_UPLOAD= uploadFile('SAT_UPLOAD');
$GRE_UPLOAD= uploadFile('GRE_UPLOAD');
$DULINGO_UPLOAD= uploadFile('DULINGO_UPLOAD');
$LANGCERT_UPLOAD= uploadFile('LANGCERT_UPLOAD');
$IELTS_UPLOAD= uploadFile('IELTS_UPLOAD');
$PTE_UPLOAD= uploadFile('PTE_UPLOAD');
$TOEFL_UPLOAD= uploadFile('TOEFL_UPLOAD');

// Prepare SQL
$sql = "INSERT INTO studentlanguagetests(
student_id,Country_code,
IELTS_OA,IELTS_READ,IELTS_WRITE,IELTS_SPEAK,IELTS_LISTEN,PTE_OA,PTE_READ,PTE_WRITE,PTE_SPEAK,PTE_LISTEN,
TOEFL_OA,TOEFL_READ,TOEFL_WRITE,TOEFL_SPEAK,TOEFL_LISTEN,LANGCERT_OA,LANGCERT_READ,LANGCERT_WRITE,
LANGCERT_SPEAK,LANGCERT_LISTEN,DULINGO_OA,DULINGO_READ,DULINGO_WRITE,DULINGO_SPEAK,DULINGO_LISTEN,
ENGOTHER_OA,ENGOTHER_READ,ENGOTHER_WRITE,ENGOTHER_SPEAK,ENGOTHER_LISTEN,ENGOTHER_NAME,GRE_OA,
SAT_OA,GMAT_OA,APTOTHER_NAME,APTOTHER_OA,ENGOTHER_UPLOAD,APTOTHER_UPLOAD,GMAT_UPLOAD,SAT_UPLOAD,
GRE_UPLOAD,DULINGO_UPLOAD,LANGCERT_UPLOAD,IELTS_UPLOAD,PTE_UPLOAD,TOEFL_UPLOAD
)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param("ssssssssssssssssssssssssssssssssssssssssssssssss",
$last_id,$Country_code,
$IELTS_OA,$IELTS_READ,$IELTS_WRITE,$IELTS_SPEAK,$IELTS_LISTEN,$PTE_OA,$PTE_READ,
$PTE_WRITE,$PTE_SPEAK,$PTE_LISTEN,$TOEFL_OA,$TOEFL_READ,$TOEFL_WRITE,$TOEFL_SPEAK,
$TOEFL_LISTEN,$LANGCERT_OA,$LANGCERT_READ,$LANGCERT_WRITE,$LANGCERT_SPEAK,
$LANGCERT_LISTEN,$DULINGO_OA,$DULINGO_READ,$DULINGO_WRITE,$DULINGO_SPEAK,
$DULINGO_LISTEN,$ENGOTHER_OA,$ENGOTHER_READ,$ENGOTHER_WRITE,
$ENGOTHER_SPEAK,$ENGOTHER_LISTEN,$ENGOTHER_NAME,$GRE_OA,$SAT_OA,$GMAT_OA,$APTOTHER_NAME,
$APTOTHER_OA,$ENGOTHER_UPLOAD,$IELTS_UPLOAD,$APTOTHER_UPLOAD,$GMAT_UPLOAD,
$SAT_UPLOAD,$GRE_UPLOAD,$DULINGO_UPLOAD,$LANGCERT_UPLOAD,$PTE_UPLOAD,$TOEFL_UPLOAD
);

	if ($stmt->execute()) {
		//$last_id = mysqli_insert_id($conn);
	// echo "<script type='text/javascript'>alert('Language & Aptitude saved successfully!. Student Id : " . $Country_code . $last_id . "');  </script>";
	} else {
	  echo "Error: " . $stmt->error;
	}
	
$stmt->close();	

}


//

/*
function saveCourseChoices($conn, $student_id, $Country_code, $coursesJson) {

    // Decode JSON
    //$courses = json_decode($coursesJson, true);
	$courses = $coursesJson;

    if (!$courses || !is_array($courses)) {
        return false; // nothing to insert
    }

    // 🔹 Start transaction (important)
    //$conn->begin_transaction();

    try {
        // 🔹 Delete existing records
        $stmtDelete = $conn->prepare("DELETE FROM coursechoice WHERE student_id = ?");
        $stmtDelete->bind_param("i", $student_id);
        $stmtDelete->execute();

        // 🔹 Insert new records
        $stmtInsert = $conn->prepare("INSERT INTO coursechoice 
        (student_id, COUNTRY_CODE, University_Name, Course_Name, Course_URL) 
        VALUES (?, ?, ?, ?, ?)");

        foreach ($courses as $c) {
            $stmtInsert->bind_param(
                "issss",
                $student_id,
                $Country_code,
                $c['University_Name'],
                $c['Course_Name'],
                $c['Course_URL']
            );
            $stmtInsert->execute();
        }

        // 🔹 Commit
        $conn->commit();
        return true;

    } catch (Exception $e) {
        // 🔹 Rollback if anything fails
       // $conn->rollback();
        return false;
    }
}
*/

function saveCourseChoices($conn, $student_id, $Country_code, $coursesJson) {

    // Decode JSON if needed
   // if (is_string($coursesJson)) {
   //     $courses = json_decode($coursesJson, true);
   // } else {
   //     $courses = $coursesJson;
  //  }

	 $courses = $coursesJson;
    // Validate
    if (!$courses || !is_array($courses)) {
        return false;
    }

    try {

        // Start transaction
        //$conn->begin_transaction();

        // Delete existing records
        $stmtDelete = $conn->prepare(
            "DELETE FROM coursechoice WHERE student_id = ?"
        );

        $stmtDelete->bind_param("i", $student_id);
        $stmtDelete->execute();
        $stmtDelete->close();

        // Insert new records
        $stmtInsert = $conn->prepare("
            INSERT INTO coursechoice
            (
                student_id,
                COUNTRY_CODE,
                University_Name,
                Course_Name,
                Course_URL,
                Intake_Month,
                Intake_Year
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        foreach ($courses as $c) {

            $university   = $c['University_Name'] ?? '';
            $course       = $c['Course_Name'] ?? '';
            $url          = $c['Course_URL'] ?? '';
            $intakeMonth  = $c['Intake_Month'] ?? '';
            $intakeYear   = $c['Intake_Year'] ?? '';

            $stmtInsert->bind_param(
                "issssss",
                $student_id,
                $Country_code,
                $university,
                $course,
                $url,
                $intakeMonth,
                $intakeYear
            );

            $stmtInsert->execute();
        }

        $stmtInsert->close();

        // Commit transaction
        $conn->commit();

        return true;

    } catch (Exception $e) {

        // Rollback on error
        $conn->rollback();

        // Optional: log error
        // error_log($e->getMessage());

        return false;
    }
}

?>


