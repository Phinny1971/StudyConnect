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
$host = "sql101.infinityfree.com"; 
$dbname = "if0_41864403_studyconnect";
$username = "if0_41864403"; 
$password = "Study2025";

/*
$host = "localhost";
$dbname = "studyconnect";
$username = "StudyConnect";
$password = "Study@2025";
*/

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$uploadDir = "uploads/";
if (!file_exists($uploadDir)) {
  mkdir($uploadDir, 0777, true);
}

// ================= FILE UPLOAD FUNCTION =================
function uploadFile($fieldName, $existingFile = "") {
  global $uploadDir;

  if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] != 0) {
    return $existingFile; // KEEP OLD FILE
  }

  $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
  $ext = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
  if (!in_array($ext, $allowed)) return $existingFile;

  $newFileName = uniqid() . '_' . basename($_FILES[$fieldName]["name"]);
  $targetPath = $uploadDir . $newFileName;

  if (move_uploaded_file($_FILES[$fieldName]["tmp_name"], $targetPath)) {
    return $targetPath;
  }

  return $existingFile;
}

// ================= START =================
$conn->begin_transaction();

try {

$student_id = $_POST['student_id'];

// ================= BASIC =================
$name = $_POST['name'];
$address = $_POST['address'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$preferred_country = $_POST['preferred_country'];
$other_country = $_POST['other_country'];
$Branch_name = $_POST['Branch_name'];
$DateOfBirth = $_POST['DateOfBirth'];

$Passport_no = $_POST['Passport_no'];
$Passport_issue = $_POST['Passport_issue'];
$Passport_Expiry = $_POST['Passport_Expiry'];

$Passport_Upload = uploadFile('Passport_Upload', $_POST['existing_Passport_Upload'] ?? "");

// ================= COUNTRY CODE =================
$stmt = $conn->prepare("SELECT country_code FROM countries WHERE country_name = ?");
$stmt->bind_param("s", $preferred_country);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$Country_code = trim($row['country_code']);
$stmt->close();

// ================= MARKS =================
$marks = [
  '10th' => $_POST['marks_10th'],
  'intermediate' => $_POST['marks_intermediate'],
  'degree' => $_POST['marks_degree'],
  'pg' => $_POST['marks_pg'] ?? null,
  'diploma' => $_POST['marks_diploma'] ?? null,
  'other' => $_POST['marks_other'] ?? null
];

// ================= CERT FILES =================
$certs = [
  '10th' => uploadFile('cert_10th', $_POST['existing_cert_10th'] ?? ""),
  'intermediate' => uploadFile('cert_intermediate', $_POST['existing_cert_intermediate'] ?? ""),
  'degree' => uploadFile('cert_degree', $_POST['existing_cert_degree'] ?? ""),
  'pg' => uploadFile('cert_pg', $_POST['existing_cert_pg'] ?? ""),
  'diploma' => uploadFile('cert_diploma', $_POST['existing_cert_diploma'] ?? ""),
  'other' => uploadFile('cert_other', $_POST['existing_cert_other'] ?? "")
];

// ================= EXPERIENCE =================
$experience = [
  'Exp1From_date' => $_POST['Exp1From_date'],
  'Exp1To_date' => $_POST['Exp1To_date'],
  'Exp2From_date' => $_POST['Exp2From_date'],
  'Exp2To_date' => $_POST['Exp2To_date'],
  'Exp3From_date' => $_POST['Exp3From_date'],
  'Exp3To_date' => $_POST['Exp3To_date']
];

// ================= EXPERIENCE FILES =================
$certsExp = [
  'Exp1_Cert' => uploadFile('Exp1_Cert', $_POST['existing_Exp1_Cert'] ?? ""),
  'Exp2_Cert' => uploadFile('Exp2_Cert', $_POST['existing_Exp2_Cert'] ?? ""),
  'Exp3_Cert' => uploadFile('Exp3_Cert', $_POST['existing_Exp3_Cert'] ?? "")
];

// ================= UPDATE studentdetails =================
$sql = "UPDATE studentdetails SET
name=?, address=?, email=?, phone=?, preferred_country=?,
marks_10th=?, cert_10th=?,
marks_intermediate=?, cert_intermediate=?,
marks_degree=?, cert_degree=?,
marks_pg=?, cert_pg=?,
marks_diploma=?, cert_diploma=?,
marks_other=?, cert_other=?,
Exp1From_date=?, Exp1To_date=?, Exp1_Cert=?,
Exp2From_date=?, Exp2To_date=?, Exp2_Cert=?,
Exp3From_date=?, Exp3To_date=?, Exp3_Cert=?,
Country_code=?, other_country=?, Branch_name=?,
DateOfBirth=?, Passport_no=?, Passport_issue=?, Passport_Expiry=?, Passport_Upload=?
WHERE student_id=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("sssssssssssssssssssssssssssssssssss",
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
$DateOfBirth, $Passport_no, $Passport_issue, $Passport_Expiry, $Passport_Upload,
$student_id
);

$stmt->execute();
$stmt->close();

// ================= UPDATE LANG TABLE =================
updateLangApt($conn, $student_id, $Country_code);

// ================= COURSE CHOICE =================
$courses = json_decode($_POST['courses'], true);
saveCourseChoices($conn, $student_id, $Country_code, $courses);

// ================= COMMIT =================
$conn->commit();

//echo "<script>alert('Student Updated Successfully'); window.location='student_list.php';</script>";

echo "<script type='text/javascript'>
showModal({
    title: 'Success',
    message: 'Student Updated Successfully Student Id: " . $Country_code . $student_id . "',
    showOk: true, onOk: function () {
        window.location.href = 'student_list.php';
    }
}); 
</script>";
				

} catch (Exception $e) {
  $conn->rollback();
  echo "Error: " . $e->getMessage();
}

$conn->close();


// ================= FUNCTION =================
function updateLangApt($conn, $student_id, $Country_code) {

function getFile($name) {
    return uploadFile($name, $_POST["existing_" . $name] ?? "");
}

$sql = "UPDATE studentlanguagetests SET
Country_code=?,
IELTS_OA=?,IELTS_READ=?,IELTS_WRITE=?,IELTS_SPEAK=?,IELTS_LISTEN=?,
PTE_OA=?,PTE_READ=?,PTE_WRITE=?,PTE_SPEAK=?,PTE_LISTEN=?,
TOEFL_OA=?,TOEFL_READ=?,TOEFL_WRITE=?,TOEFL_SPEAK=?,TOEFL_LISTEN=?,
LANGCERT_OA=?,LANGCERT_READ=?,LANGCERT_WRITE=?,LANGCERT_SPEAK=?,LANGCERT_LISTEN=?,
DULINGO_OA=?,DULINGO_READ=?,DULINGO_WRITE=?,DULINGO_SPEAK=?,DULINGO_LISTEN=?,
ENGOTHER_OA=?,ENGOTHER_READ=?,ENGOTHER_WRITE=?,ENGOTHER_SPEAK=?,ENGOTHER_LISTEN=?,ENGOTHER_NAME=?,
GRE_OA=?,SAT_OA=?,GMAT_OA=?,APTOTHER_NAME=?,APTOTHER_OA=?,
ENGOTHER_UPLOAD=?,APTOTHER_UPLOAD=?,GMAT_UPLOAD=?,SAT_UPLOAD=?,GRE_UPLOAD=?,
DULINGO_UPLOAD=?,LANGCERT_UPLOAD=?,IELTS_UPLOAD=?,PTE_UPLOAD=?,TOEFL_UPLOAD=?
WHERE student_id=?";

$stmt = $conn->prepare($sql);
/*
$stmt->bind_param("ssssssssssssssssssssssssssssssssssssssssssssssss",
$Country_code,
$_POST['IELTS_OA'],$_POST['IELTS_READ'],$_POST['IELTS_WRITE'],$_POST['IELTS_SPEAK'],$_POST['IELTS_LISTEN'],
$_POST['PTE_OA'],$_POST['PTE_READ'],$_POST['PTE_WRITE'],$_POST['PTE_SPEAK'],$_POST['PTE_LISTEN'],
$_POST['TOEFL_OA'],$_POST['TOEFL_READ'],$_POST['TOEFL_WRITE'],$_POST['TOEFL_SPEAK'],$_POST['TOEFL_LISTEN'],
$_POST['LANGCERT_OA'],$_POST['LANGCERT_READ'],$_POST['LANGCERT_WRITE'],$_POST['LANGCERT_SPEAK'],$_POST['LANGCERT_LISTEN'],
$_POST['DULINGO_OA'],$_POST['DULINGO_READ'],$_POST['DULINGO_WRITE'],$_POST['DULINGO_SPEAK'],$_POST['DULINGO_LISTEN'],
$_POST['ENGOTHER_OA'],$_POST['ENGOTHER_READ'],$_POST['ENGOTHER_WRITE'],$_POST['ENGOTHER_SPEAK'],$_POST['ENGOTHER_LISTEN'],$_POST['ENGOTHER_NAME'],
$_POST['GRE_OA'],$_POST['SAT_OA'],$_POST['GMAT_OA'],$_POST['APTOTHER_NAME'],$_POST['APTOTHER_OA'],
getFile('ENGOTHER_UPLOAD'),getFile('APTOTHER_UPLOAD'),getFile('GMAT_UPLOAD'),
getFile('SAT_UPLOAD'),getFile('GRE_UPLOAD'),
getFile('DULINGO_UPLOAD'),getFile('LANGCERT_UPLOAD'),
getFile('IELTS_UPLOAD'),getFile('PTE_UPLOAD'),getFile('TOEFL_UPLOAD'),
$student_id
);
*/

$ENGOTHER_UPLOAD  = getFile('ENGOTHER_UPLOAD');
$APTOTHER_UPLOAD  = getFile('APTOTHER_UPLOAD');
$GMAT_UPLOAD      = getFile('GMAT_UPLOAD');
$SAT_UPLOAD       = getFile('SAT_UPLOAD');
$GRE_UPLOAD       = getFile('GRE_UPLOAD');
$DULINGO_UPLOAD   = getFile('DULINGO_UPLOAD');
$LANGCERT_UPLOAD  = getFile('LANGCERT_UPLOAD');
$IELTS_UPLOAD     = getFile('IELTS_UPLOAD');
$PTE_UPLOAD       = getFile('PTE_UPLOAD');
$TOEFL_UPLOAD     = getFile('TOEFL_UPLOAD');

$stmt->bind_param("ssssssssssssssssssssssssssssssssssssssssssssssss",
$Country_code,
$_POST['IELTS_OA'],$_POST['IELTS_READ'],$_POST['IELTS_WRITE'],$_POST['IELTS_SPEAK'],$_POST['IELTS_LISTEN'],
$_POST['PTE_OA'],$_POST['PTE_READ'],$_POST['PTE_WRITE'],$_POST['PTE_SPEAK'],$_POST['PTE_LISTEN'],
$_POST['TOEFL_OA'],$_POST['TOEFL_READ'],$_POST['TOEFL_WRITE'],$_POST['TOEFL_SPEAK'],$_POST['TOEFL_LISTEN'],
$_POST['LANGCERT_OA'],$_POST['LANGCERT_READ'],$_POST['LANGCERT_WRITE'],$_POST['LANGCERT_SPEAK'],$_POST['LANGCERT_LISTEN'],
$_POST['DULINGO_OA'],$_POST['DULINGO_READ'],$_POST['DULINGO_WRITE'],$_POST['DULINGO_SPEAK'],$_POST['DULINGO_LISTEN'],
$_POST['ENGOTHER_OA'],$_POST['ENGOTHER_READ'],$_POST['ENGOTHER_WRITE'],$_POST['ENGOTHER_SPEAK'],$_POST['ENGOTHER_LISTEN'],$_POST['ENGOTHER_NAME'],
$_POST['GRE_OA'],$_POST['SAT_OA'],$_POST['GMAT_OA'],$_POST['APTOTHER_NAME'],$_POST['APTOTHER_OA'],

$ENGOTHER_UPLOAD,
$APTOTHER_UPLOAD,
$GMAT_UPLOAD,
$SAT_UPLOAD,
$GRE_UPLOAD,
$DULINGO_UPLOAD,
$LANGCERT_UPLOAD,
$IELTS_UPLOAD,
$PTE_UPLOAD,
$TOEFL_UPLOAD,

$student_id
);

$stmt->execute();
$stmt->close();
}


// ================= COURSE =================
/*
function saveCourseChoices($conn, $student_id, $Country_code, $courses) {

$stmt = $conn->prepare("DELETE FROM coursechoice WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();

$stmt = $conn->prepare("INSERT INTO coursechoice 
(student_id, COUNTRY_CODE, University_Name, Course_Name, Course_URL)
VALUES (?, ?, ?, ?, ?)");

foreach ($courses as $c) {
    $stmt->bind_param("issss",
        $student_id,
        $Country_code,
        $c['University_Name'],
        $c['Course_Name'],
        $c['Course_URL']
    );
    $stmt->execute();
}

}
*/

function saveCourseChoices($conn, $student_id, $Country_code, $courses) {

    // Delete existing records
    $stmtDelete = $conn->prepare(
        "DELETE FROM coursechoice WHERE student_id = ?"
    );

    $stmtDelete->bind_param("i", $student_id);
    $stmtDelete->execute();
    $stmtDelete->close();

    // Insert updated records
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

        $university  = $c['University_Name'] ?? '';
        $course      = $c['Course_Name'] ?? '';
        $url         = $c['Course_URL'] ?? '';
        $intakeMonth = $c['Intake_Month'] ?? '';
        $intakeYear  = $c['Intake_Year'] ?? '';

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
}
?>