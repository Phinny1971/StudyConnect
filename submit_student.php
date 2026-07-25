<?php
require_once 'session_check.php';
requirePermission('student.create');
require_once 'includes/db_connection.php';

if (
    !isset($_POST['csrf_token']) ||
    !isset($_SESSION['csrf_token']) ||
    !hash_equals(
        $_SESSION['csrf_token'],
        $_POST['csrf_token']
    )
) {
    die("Invalid request");
}

?>

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
    //message.innerHTML = settings.message;
	message.textContent = settings.message;

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
/*if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
*/
if (!$conn) {

   error_log(
      mysqli_connect_error()
   );

   die(
      "Database unavailable"
   );
}


mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


function nullIfEmpty($value) {
    return ($value === '' || $value === null)
        ? null
        : trim($value);
}

function decimalOrNull($value) {
    return ($value === '' || $value === null)
        ? null
        : floatval($value);
}

function intOrNull($value) {
    return ($value === '' || $value === null)
        ? null
        : intval($value);
}

function dateOrNull($value) {
    return ($value === '' || $value === null)
        ? null
        : $value;
}


// Create uploads directory if not exists
$uploadDir = "uploads/";
if (!file_exists($uploadDir)) {
  //mkdir($uploadDir, 0777, true);
  mkdir($uploadDir, 0750, true);
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

function uploadFile($fieldName)
{
	
    global $uploadDir;
	
    if (
        !isset($_FILES[$fieldName]) ||
        $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK ||
        empty($_FILES[$fieldName]['tmp_name'])
    ) {
        return null;
    }

    if (!is_uploaded_file($_FILES[$fieldName]['tmp_name'])) {
        throw new Exception(
            "Uploaded file is missing or invalid for field: " . $fieldName
        );
    }

    if (!file_exists($_FILES[$fieldName]['tmp_name'])) {
        throw new Exception(
            "Temporary upload file not found for field: " . $fieldName
        );
    }

    if (
        !isset($_FILES[$fieldName]) ||
        $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK
    ) {
        return null;
    }

    $maxSize = 5 * 1024 * 1024;

    if ($_FILES[$fieldName]['size'] > $maxSize) {
        throw new Exception(
            "File too large: " . $fieldName
        );
    }

    $allowedMimeTypes = [
        'application/pdf' => 'pdf',
        'image/jpeg'      => 'jpg',
        'image/png'       => 'png'
    ];

    $finfo = finfo_open(
        FILEINFO_MIME_TYPE
    );

    $mime = finfo_file(
        $finfo,
        $_FILES[$fieldName]['tmp_name']
    );

    finfo_close($finfo);

    if (!isset($allowedMimeTypes[$mime])) {
        throw new Exception(
            "Invalid file type"
        );
    }

    $extension =
        $allowedMimeTypes[$mime];

    $newFileName =
        bin2hex(random_bytes(16))
        . "."
        . $extension;

    $target =
        $uploadDir
        . DIRECTORY_SEPARATOR
        . $newFileName;

    if (
        !move_uploaded_file(
            $_FILES[$fieldName]['tmp_name'],
            $target
        )
    ) {
        throw new Exception(
            "Upload failed"
        );
    }

    return $target;
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

//$other_country= $_POST['other_country'];
$other_country = nullIfEmpty($_POST['other_country'] ?? null);
$Branch_name= $_POST['Branch_name'];
$DateOfBirth= $_POST['DateOfBirth'];

$Passport_no=$_POST['Passport_no'];
$Passport_issue = dateOrNull($_POST['Passport_issue'] ?? null);
$Passport_Expiry = dateOrNull($_POST['Passport_Expiry'] ?? null);
$Passport_Upload=uploadFile('Passport_Upload');

// Marks
$marks = [
  '10th' => decimalOrNull($_POST['marks_10th'] ?? null),
  'intermediate' => decimalOrNull($_POST['marks_intermediate'] ?? null),
  'degree' => decimalOrNull($_POST['marks_degree'] ?? null),
  'pg' => decimalOrNull($_POST['marks_pg'] ?? null),
  'diploma' => decimalOrNull($_POST['marks_diploma'] ?? null),
  'other' => decimalOrNull($_POST['marks_other'] ?? null)
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
  'Exp1From_date' => dateOrNull($_POST['Exp1From_date'] ?? null),
  'Exp1To_date' => dateOrNull($_POST['Exp1To_date'] ?? null),
  'Exp2From_date' => dateOrNull($_POST['Exp2From_date'] ?? null),
  'Exp2To_date' => dateOrNull($_POST['Exp2To_date'] ?? null),
  'Exp3From_date' => dateOrNull($_POST['Exp3From_date'] ?? null),
  'Exp3To_date' => dateOrNull($_POST['Exp3To_date'] ?? null)
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

$conn->begin_transaction();

try {

   // all inserts 
	if ($stmt->execute()) {
		$last_id = mysqli_insert_id($conn);
	  //echo "Student details saved successfully!";
	  saveLangAptTest($conn, $last_id, $Country_code);
	  saveOtherDetails($conn, $last_id, $Country_code);
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
	  //echo "Error: " . $stmt->error;
		error_log($stmt->error);

		throw new Exception(
			"Database operation failed"
		);
	}
 $conn->commit();

} catch(Exception $e){

   $conn->rollback();

   throw $e;
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
$IELTS_OA=decimalOrNull( $_POST['IELTS_OA'] ?? null);
$IELTS_READ=decimalOrNull( $_POST['IELTS_READ'] ?? null);
$IELTS_WRITE=decimalOrNull( $_POST['IELTS_WRITE'] ?? null);
$IELTS_SPEAK=decimalOrNull( $_POST['IELTS_SPEAK'] ?? null);
$IELTS_LISTEN=decimalOrNull( $_POST['IELTS_LISTEN'] ?? null);
$PTE_OA=decimalOrNull( $_POST['PTE_OA'] ?? null);
$PTE_READ=decimalOrNull( $_POST['PTE_READ'] ?? null);
$PTE_WRITE=decimalOrNull( $_POST['PTE_WRITE'] ?? null);
$PTE_SPEAK=decimalOrNull( $_POST['PTE_SPEAK'] ?? null);
$PTE_LISTEN=decimalOrNull( $_POST['PTE_LISTEN'] ?? null);
$TOEFL_OA=decimalOrNull( $_POST['TOEFL_OA'] ?? null);
$TOEFL_READ=decimalOrNull( $_POST['TOEFL_READ'] ?? null);
$TOEFL_WRITE=decimalOrNull( $_POST['TOEFL_WRITE'] ?? null);
$TOEFL_SPEAK=decimalOrNull( $_POST['TOEFL_SPEAK'] ?? null);
$TOEFL_LISTEN=decimalOrNull( $_POST['TOEFL_LISTEN'] ?? null);
$LANGCERT_OA=decimalOrNull( $_POST['LANGCERT_OA'] ?? null);
$LANGCERT_READ=decimalOrNull( $_POST['LANGCERT_READ'] ?? null);
$LANGCERT_WRITE=decimalOrNull( $_POST['LANGCERT_WRITE'] ?? null);
$LANGCERT_SPEAK=decimalOrNull( $_POST['LANGCERT_SPEAK'] ?? null);
$LANGCERT_LISTEN=decimalOrNull( $_POST['LANGCERT_LISTEN'] ?? null);
$DULINGO_OA=decimalOrNull( $_POST['DULINGO_OA'] ?? null);
$DULINGO_READ=decimalOrNull( $_POST['DULINGO_READ'] ?? null);
$DULINGO_WRITE=decimalOrNull( $_POST['DULINGO_WRITE'] ?? null);
$DULINGO_SPEAK=decimalOrNull( $_POST['DULINGO_SPEAK'] ?? null);
$DULINGO_LISTEN=decimalOrNull( $_POST['DULINGO_LISTEN'] ?? null);
$ENGOTHER_OA=decimalOrNull( $_POST['ENGOTHER_OA'] ?? null);
$ENGOTHER_READ=decimalOrNull( $_POST['ENGOTHER_READ'] ?? null);
$ENGOTHER_WRITE=decimalOrNull( $_POST['ENGOTHER_WRITE'] ?? null);
$ENGOTHER_SPEAK=decimalOrNull( $_POST['ENGOTHER_SPEAK'] ?? null);
$ENGOTHER_LISTEN=decimalOrNull( $_POST['ENGOTHER_LISTEN'] ?? null);
//$ENGOTHER_NAME=decimalOrNull( $_POST['ENGOTHER_NAME'] ?? null);
$ENGOTHER_NAME = nullIfEmpty($_POST['ENGOTHER_NAME'] ?? null);
	
$GRE_OA=decimalOrNull( $_POST['GRE_OA'] ?? null);
$SAT_OA=decimalOrNull( $_POST['SAT_OA'] ?? null);
$GMAT_OA=decimalOrNull( $_POST['GMAT_OA'] ?? null);

$APTOTHER_OA=decimalOrNull( $_POST['APTOTHER_OA'] ?? null);
//$APTOTHER_OA=decimalOrNull( $_POST['APTOTHER_OA'] ?? null);

$APTOTHER_NAME = nullIfEmpty($_POST['APTOTHER_NAME'] ?? null);
	
$ENGOTHER_UPLOAD= nullIfEmpty(uploadFile('ENGOTHER_UPLOAD') ?? null);
$IELTS_UPLOAD= nullIfEmpty(uploadFile('IELTS_UPLOAD') ?? null);
$APTOTHER_UPLOAD= nullIfEmpty(uploadFile('APTOTHER_UPLOAD') ?? null);
$GMAT_UPLOAD= nullIfEmpty(uploadFile('GMAT_UPLOAD') ?? null);
$SAT_UPLOAD= nullIfEmpty(uploadFile('SAT_UPLOAD') ?? null);
$GRE_UPLOAD= nullIfEmpty(uploadFile('GRE_UPLOAD') ?? null);
$DULINGO_UPLOAD= nullIfEmpty(uploadFile('DULINGO_UPLOAD') ?? null);
$LANGCERT_UPLOAD= nullIfEmpty(uploadFile('LANGCERT_UPLOAD') ?? null);
$PTE_UPLOAD= nullIfEmpty(uploadFile('PTE_UPLOAD') ?? null);
$TOEFL_UPLOAD= nullIfEmpty(uploadFile('TOEFL_UPLOAD') ?? null);

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
	  //echo "Error: " . $stmt->error;
		error_log($stmt->error);
		throw new Exception(
			"Database operation failed"
		);
	}
	
$stmt->close();	

}


//


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
        //$conn->commit();

        return true;

    } catch (Exception $e) {

        // Rollback on error
        $conn->rollback();

        // Optional: log error
        // error_log($e->getMessage());

        return false;
    }
}

function saveOtherDetails($conn, $student_id, $Country_code)
{
    $immi_country       = nullIfEmpty($_POST['immi_country'] ?? null);
    $medical_cond       = nullIfEmpty($_POST['medical_cond'] ?? null);
    $visa_refusal       = nullIfEmpty($_POST['visa_refusal'] ?? null);
    $convicted_offence  = nullIfEmpty($_POST['convicted_offence'] ?? null);

    $emergency_name      = nullIfEmpty($_POST['emergency_name'] ?? null);
    $emergency_phone     = nullIfEmpty($_POST['emergency_phone'] ?? null);
    $emergency_email     = nullIfEmpty($_POST['emergency_email'] ?? null);
    $emergency_relation  = nullIfEmpty($_POST['emergency_relation'] ?? null);

    $gender          = nullIfEmpty($_POST['gender'] ?? null);
    $maritalstatus   = nullIfEmpty($_POST['maritalstatus'] ?? null);

    $lor1      = nullIfEmpty(uploadFile('lor1'));
    $lor2      = nullIfEmpty(uploadFile('lor2'));
    $lor3      = nullIfEmpty(uploadFile('lor3'));
    $moi       = nullIfEmpty(uploadFile('moi'));
    $resume    = nullIfEmpty(uploadFile('resume'));
    $otherdoc  = nullIfEmpty(uploadFile('otherdoc'));
	
	$immi_country_file       = nullIfEmpty(uploadFile('immi_country_file'));
	$medical_cond_file       = nullIfEmpty(uploadFile('medical_cond_file'));
	$visa_refusal_file       = nullIfEmpty(uploadFile('visa_refusal_file'));
	$convicted_offence_file  = nullIfEmpty(uploadFile('convicted_offence_file'));
	
	$lor1name 			= nullIfEmpty($_POST['lor1name'] ?? null);
	$lor1email			= nullIfEmpty($_POST['lor1email'] ?? null);
	$lor1phone			= nullIfEmpty($_POST['lor1phone'] ?? null);
	$lor2name 			= nullIfEmpty($_POST['lor2name'] ?? null);
	$lor2email			= nullIfEmpty($_POST['lor2email'] ?? null);
	$lor2phone			= nullIfEmpty($_POST['lor2phone'] ?? null);
	$lor3name 			= nullIfEmpty($_POST['lor3name'] ?? null);
	$lor3email			= nullIfEmpty($_POST['lor3email'] ?? null);
	$lor3phone			= nullIfEmpty($_POST['lor3phone'] ?? null);
					
	$explor1name 		= nullIfEmpty($_POST['explor1name'] ?? null);	
	$explor1email		= nullIfEmpty($_POST['explor1email'] ?? null);	
	$explor1phone		= nullIfEmpty($_POST['explor1phone'] ?? null);	
	$explor2name 		= nullIfEmpty($_POST['explor2name'] ?? null);	
	$explor2email		= nullIfEmpty($_POST['explor2email'] ?? null);	
	$explor2phone		= nullIfEmpty($_POST['explor2phone'] ?? null);	
	$explor3name 		= nullIfEmpty($_POST['explor3name'] ?? null);	
	$explor3email		= nullIfEmpty($_POST['explor3email'] ?? null);	
	$explor3phone		= nullIfEmpty($_POST['explor3phone'] ?? null);	
	
	$explor1 		=nullIfEmpty(uploadFile('explor1'));
	$explor2 		=nullIfEmpty(uploadFile('explor2'));
	$explor3 		=nullIfEmpty(uploadFile('explor3'));

    $sql = "
    INSERT INTO studentotherdetails
    (
        student_id,
        Country_code,
        immi_country,
        medical_cond,
        visa_refusal,
        convicted_offence,
        emergency_name,
        emergency_phone,
        emergency_email,
        emergency_relation,
        lor1,
        lor2,
        lor3,
        moi,
        resume,
        otherdoc,
        gender,
        maritalstatus,
		immi_country_file,     
		medical_cond_file,     
		visa_refusal_file,     
		convicted_offence_file,
		lor1name, 		
		lor1email,		
		lor1phone,		
		lor2name, 		
		lor2email,		
		lor2phone,		
		lor3name, 		
		lor3email,		
		lor3phone,		
		explor1name, 	
		explor1email,	
		explor1phone,	
		explor2name, 	
		explor2email,	
		explor2phone,	
		explor3name, 	
		explor3email,	
		explor3phone,	
		explor1, 		
		explor2, 		
		explor3 		
		
    )
    VALUES
    (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
    )";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "issssssssssssssssssssssssssssssssssssssssss",
        $student_id,
        $Country_code,
        $immi_country,
        $medical_cond,
        $visa_refusal,
        $convicted_offence,
        $emergency_name,
        $emergency_phone,
        $emergency_email,
        $emergency_relation,
        $lor1,
        $lor2,
        $lor3,
        $moi,
        $resume,
        $otherdoc,
        $gender,
        $maritalstatus,
		$immi_country_file,     
		$medical_cond_file,     
		$visa_refusal_file,     
		$convicted_offence_file,
		$lor1name, 		
		$lor1email,		
		$lor1phone,		
		$lor2name, 		
		$lor2email,		
		$lor2phone,		
		$lor3name, 		
		$lor3email,		
		$lor3phone,		
		$explor1name, 	
		$explor1email,	
		$explor1phone,	
		$explor2name, 	
		$explor2email,	
		$explor2phone,	
		$explor3name, 	
		$explor3email,	
		$explor3phone,	
		$explor1, 		
		$explor2, 		
		$explor3
    );

    if (!$stmt->execute()) {
        echo "Error saving studentotherdetails: " . $stmt->error;
    }

    $stmt->close();
}


?>


