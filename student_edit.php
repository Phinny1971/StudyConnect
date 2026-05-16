 
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

// Get student ID from query string
$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;
if ($student_id <= 0) {
    die("Invalid student ID.");
}

// Fetch student details
$sql = "SELECT * FROM studentdetails WHERE student_id = $student_id";
$result = $conn->query($sql);
if (!$result || $result->num_rows === 0) {
    die("Student not found.");
}
$student = $result->fetch_assoc();

// Fetch studentlanguagetests
$sql = "SELECT * FROM studentlanguagetests WHERE student_id = $student_id";
$result = $conn->query($sql);
if (!$result || $result->num_rows === 0) {
    //die("Student not found.");
}
$studentlanguagetests = $result->fetch_assoc();

// Fetch coursechoice
$sql = "SELECT * FROM coursechoice WHERE student_id = $student_id";
$result = $conn->query($sql);
if (!$result || $result->num_rows === 0) {
    //die("Student not found.");
}
$coursechoice = $result->fetch_assoc();


// Fetch countries
$sql = "SELECT country_name FROM countries";
$result = $conn->query($sql);
$options = "";
while ($row = $result->fetch_assoc()) {
    $selected = ($student['preferred_country'] == $row['country_name']) ? "selected" : "";
    $country = htmlspecialchars($row['country_name']);
    $options .= "<option value=\"$country\" $selected>$country</option>\n";
}

// Fetch branches
$sql = "SELECT Branch_name FROM branches";
$result = $conn->query($sql);
$Branchoptions = "";
while ($row = $result->fetch_assoc()) {
    $selected = ($student['Branch_name'] == $row['Branch_name']) ? "selected" : "";
    $Branch = htmlspecialchars($row['Branch_name']);
    $Branchoptions .= "<option value=\"$Branch\" $selected>$Branch</option>\n";
}



// Fetch for display (if needed)
$res = $conn->prepare("SELECT * FROM coursechoice WHERE student_id=?");
$res->bind_param("i", $student_id);
$res->execute();
$records = $res->get_result()->fetch_all(MYSQLI_ASSOC);
$res->close();


$conn->close();
?>

<!-- HTML FORM CONTINUES BELOW - to be appended with actual tab content and JavaScript -->
<!--
<html>
<head>
  <title>Edit Student</title>
</head>
<body>
  <h2>Edit Student - <?php echo htmlspecialchars($student['name']); ?></h2>
 
  <form action="update_student.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">

    <label>Name:</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>">

    <label>Passport No:</label>
    <input type="text" name="Passport_no" value="<?php echo htmlspecialchars($student['Passport_no']); ?>">

	<label>Branch of Registration: </label>
	<select name="Branch_name" id="Branch_name" class="required" >
          <option value="">Select Branch</option>
           <?php echo $Branchoptions; ?>
    </select>
	
	<label>Preferred Country:</label>
	<select name="preferred_country" id="preferred_country" class="required"  onchange="checkCountry(this.value)">
          <option value="">Select Country</option>
           <?php echo $options; ?>
        </select>
   
    <button type="submit">Update</button>
  </form>
</body>
</html>
-->


<html lang="en">
<head>

  <link rel="stylesheet" href="css/style.css">

  <meta charset="UTF-8">
  <title>Student Record Edit</title>
  
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .tab { display: none; }
    .tab.active { display: block; }

    .tab-buttons {
      display: flex;
      margin-bottom: 20px;
    }

    .tab-buttons button {
      flex: 1;
      padding: 10px;
      cursor: pointer;
      background-color: #f1f1f1;
      border: 1px solid #ccc;
      border-bottom: none;
    }

    .tab-buttons button.active {
      background-color: #fff;
      font-weight: bold;
      border-bottom: 2px solid #007BFF;
    }

    form {
      border: 1px solid #ccc;
      padding: 20px;
      background: #fff;
    }

    label {
      display: block;
      margin: 10px 0 5px;
    }

    input[type="text"],
    input[type="email"],
    input[type="number"],
    select,
    input[type="file"] {
      width: 100%;
      padding: 8px;
      margin-bottom: 15px;
      box-sizing: border-box;
    }

    button[type="submit"] {
      margin-top: 20px;
      padding: 10px 20px;
      font-size: 16px;
    }

    .edu-section {
      border-top: 1px solid #ccc;
      margin-top: 20px;
      padding-top: 10px;
    }

.preview-img {
  max-width: 50px;
  max-height: 50px;
  display: block;
  margin-top: 5px;
}
.preview-text {
  font-style: italic;
  font-size: 14px;
  color: #555;
}

  </style>
  
  <style>
  .preview-img {
    max-width: 50px;
    max-height: 50px;
    display: block;
    margin-top: 5px;
    cursor: pointer;
    border: 1px solid #ccc;
  }

  .preview-text {
    font-style: italic;
    font-size: 14px;
    color: #555;
    margin-top: 5px;
    cursor: pointer;
  }

  .modal {
    display: none;
    position: fixed;
    z-index: 999;
    padding-top: 60px;
    left: 0; top: 0;
    width: 100%; height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.8);
  }

  .modal-content {
    margin: auto;
    display: block;
    max-width: 90%;
    max-height: 80%;
  }

  .modal-content.pdf-link {
    color: #fff;
    font-size: 20px;
    text-align: center;
    padding: 20px;
  }

  .close {
    position: absolute;
    top: 20px;
    right: 35px;
    color: #fff;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
  }
</style>
<style>
  .invalid {
    border: 2px solid red;
    background-color: #ffecec;
  }
</style>
  
<style>

/* Ultra-slim header */
.modal-header {
    background: #007bff;
    color: white;

    padding: 2px 10px;

    display: flex;
    justify-content: space-between;
    align-items: center;

    height: 28px;
}

/* Header title */
.modal-header h2 {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    line-height: 1;
}

/* Close button */
.close-btn {
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    line-height: 1;
}

</style>
  
</head>
<body>

  <h2>Student Record Edit</h2>

<hr>


  <div class="tab-buttons">
    <button type="button" class="active" onclick="showTab(0)">Student Details</button>
    <button type="button" onclick="showTab(1)">Education Details</button>
	 <button type="button" onclick="showTab(2)">Work Experience</button>
	  <button type="button" onclick="showTab(3)">Language & Aptitude</button>
	  <button type="button" onclick="showTab(4)">Course Choice</button>
  </div>

  <form action="update_student.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm();">
	
	<input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
    <!-- Tab 1 -->
    <div class="tab active">
	<h3>Personal Details</h3>
	 <div class="edu-section">
	<table>
     <tr> 
	 
	 <td><label>Name: </label></td><td> <input type="text" name="name" id="name" class="required" maxlength="50" 
	 value="<?php echo htmlspecialchars($student['name']); ?>"> </td>
	 
	 
	 <!-- 
	 <td><label>Passport No.: </label></td>	 <td> <input type="text" name="Passport_no" maxlength="50" class="required" value="<?php echo htmlspecialchars($student['Passport_no']); ?>"> </td>
	 <td><input type="file" name="Passport_Upload" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_passport')" class="required" value="<?php echo htmlspecialchars($student['Passport_Upload']); ?>"></td>
	 <td><div id="preview_passport" class="preview-text"></div></td>
	 -->
	 
	 
	 <td style="width:50px;"></td><td style="width:200px;"><label>Passport No.: </label></td><td> <input type="text" name="Passport_no" maxlength="50" class="required" value="<?php echo htmlspecialchars($student['Passport_no']); ?>"> </td>
	  <td>
	  <input type="file" name="Passport_Upload" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this, 'preview_passport')"  value="<?php echo htmlspecialchars($student['Passport_Upload']); ?>">
	  
	  <input type="hidden" name="existing_Passport_Upload"
       value="<?php echo htmlspecialchars($student['Passport_Upload'] ?? ''); ?>">
	   
	  </td>
	  <td>
		<?php if (!empty($student['Passport_Upload'])): ?>
		<script>
		  window.addEventListener('DOMContentLoaded', function() {
			previewFile(<?php echo json_encode($student['Passport_Upload']); ?>, 'preview_passport');
		  });
		</script>
		<?php endif; ?>
	  </td>
	 
	<td><div id="preview_passport" class="preview-text"></div></td>


	 </tr>
     
	 <tr> <td><label>Email: </label></td><td><input type="email" name="email" id="email" class="required" maxlength="50" value="<?php echo htmlspecialchars($student['email']); ?>"></td>
	<td style="width:50px;"></td><td><label>Date of Issue: </label></td>  <td> <input type="date" name="Passport_issue" class="required" value="<?php echo htmlspecialchars($student['Passport_issue']); ?>"> </td>
	 </tr>

     <tr> 
	 <td><label>Address:</label></td><td><input type="text" name="address" id="address" class="required" value="<?php echo htmlspecialchars($student['address']); ?>"></td>
	<td style="width:50px;"></td><td><label>Date of Expiry: </label></td>  <td> <input type="date" name="Passport_Expiry" class="required" value="<?php echo htmlspecialchars($student['Passport_Expiry']); ?>" > </td>
	 </tr>
	 
     <tr> <td><label>Phone: </label></td><td><input type="text" name="phone" id="phone" class="required" pattern="[0-9]{10}" title="Enter 10 digit phone number" value="<?php echo htmlspecialchars($student['phone']); ?>" ></td>

      <td style="width:50px;"></td><td><label>Branch of Registration: </label></td>
	 <td> 
		<select name="Branch_name" id="Branch_name" class="required" >
          <option value="">Select Branch</option>
           <?php echo $Branchoptions; ?>
        </select>
	 </td>

	 
	 </tr>

      <tr>
      <td><label>Date of Birth: </label></td>  <td> <input type="date" name="DateOfBirth" class="required" value="<?php echo htmlspecialchars($student['DateOfBirth']); ?>" > </td>
      <td style="width:50px;"></td><td><label>Preferred Country:</label></td>
      <td>
        <select name="preferred_country" id="preferred_country" class="required"  onchange="checkCountry(this.value)">
          <option value="">Select Country</option>
           <?php echo $options; ?>
        </select>
	  </td>
       <td>
		<div id="other_country_box" style="display: none; margin-top: 10px;">
		<input type="text" name="other_country" id="other_country" placeholder="Enter Country Name" maxlength="50"  ></td>
		 </div>
	  </td>
     </tr>
	  
	  <tr>
	  <td></td>
      <td></td>
      <td></td>
	 
	  </tr>
	 
	  </table>
    </div>
	</div>

    <!-- Tab 2 -->
    <div class="tab">
      <h3>Education Details</h3>
      <div class="edu-section">
	  
		  <table>
			  <tr><td><label>10th Marks (%): </label></td><td><input type="number" name="marks_10th" step="0.01" min="0" max="100" value="<?php echo htmlspecialchars($student['marks_10th']); ?>" ></td>
			  <td><label>Certificate: </label></td>
			  <td><input type="file" name="cert_10th" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_10th')">
			  <input type="hidden" name="existing_cert_10th" value="<?php echo htmlspecialchars($student['cert_10th'] ?? ''); ?>">
			  </td>
			  <td>
				<?php if (!empty($student['cert_10th'])): ?>
				<script>
				  window.addEventListener('DOMContentLoaded', function() {
					previewFile(<?php echo json_encode($student['cert_10th']); ?>, 'preview_10th');
				  });
				</script>
				<?php endif; ?>
			  </td>
			  <td><div id="preview_10th" class="preview-text"></div></td>
			  </tr>
			  
			  <tr><td><label>Intermediate Marks (%):</label></td><td><input type="number" name="marks_intermediate" step="0.01" min="0" max="100" value="<?php echo htmlspecialchars($student['marks_intermediate']); ?>" ></td>
			  <td><label>Certificate: </label></td>
			  <td><input type="file" name="cert_intermediate" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this, 'preview_intermediate')" >
			  <input type="hidden" name="existing_cert_intermediate" value="<?php echo htmlspecialchars($student['cert_intermediate'] ?? ''); ?>">
			  </td>
			   <td>
				<?php if (!empty($student['cert_intermediate'])): ?>
				<script>
				  window.addEventListener('DOMContentLoaded', function() {
					previewFile(<?php echo json_encode($student['cert_intermediate']); ?>, 'preview_intermediate');
				  });
				</script>
				<?php endif; ?>
			  </td>
			   <td><div id="preview_intermediate" class="preview-text"></div></td>
			  </tr>
			  
			  <tr><td><label>Degree Marks (%): </label></td><td><input type="number" name="marks_degree" step="0.01" min="0" max="100" value="<?php echo htmlspecialchars($student['marks_degree']); ?>"></td>
			  <td><label>Certificate: </label></td>
			  <td><input type="file" name="cert_degree" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this, 'preview_Degree')" >
			  <input type="hidden" name="existing_cert_degree" value="<?php echo htmlspecialchars($student['cert_degree'] ?? ''); ?>">
			  </td>
			   <td>
				<?php if (!empty($student['cert_degree'])): ?>
				<script>
				  window.addEventListener('DOMContentLoaded', function() {
					previewFile(<?php echo json_encode($student['cert_degree']); ?>, 'preview_Degree');
				  });
				</script>
				<?php endif; ?>
			  </td>
			   <td><div id="preview_Degree" class="preview-text"></div></td>
			  </tr>
			  
			  <tr><td><label>Post Graduation Marks (%):</label> </td><td><input type="number" name="marks_pg" step="0.01" min="0" max="100" value="<?php echo htmlspecialchars($student['marks_pg']); ?>"></td>
			  <td><label>Certificate: </label></td>
			  <td><input type="file" name="cert_pg" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this, 'preview_PG')">
			  <input type="hidden" name="existing_cert_pg" value="<?php echo htmlspecialchars($student['cert_pg'] ?? ''); ?>">
			  </td>
			  <td>
				<?php if (!empty($student['cert_pg'])): ?>
				<script>
				  window.addEventListener('DOMContentLoaded', function() {
					previewFile(<?php echo json_encode($student['cert_pg']); ?>, 'preview_PG');
				  });
				</script>
				<?php endif; ?>
			  </td>
			  <td><div id="preview_PG" class="preview-text"></div></td>
			  </tr>
			  
			  <tr><td><label>Diploma Marks (%):</label> </td><td><input type="number" name="marks_diploma" step="0.01" min="0" max="100" value="<?php echo htmlspecialchars($student['marks_diploma']); ?>"></td>
			  <td><label>Certificate:</label></td>
			  <td> <input type="file" name="cert_diploma" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this, 'preview_Diploma')">
			  <input type="hidden" name="existing_cert_diploma" value="<?php echo htmlspecialchars($student['cert_diploma'] ?? ''); ?>">
			  </td>
			   <td>
				<?php if (!empty($student['cert_diploma'])): ?>
				<script>
				  window.addEventListener('DOMContentLoaded', function() {
					previewFile(<?php echo json_encode($student['cert_diploma']); ?>, 'preview_Diploma');
				  });
				</script>
				<?php endif; ?>
			  </td>
			  <td><div id="preview_Diploma" class="preview-text"></div></td>
			  </tr>
			  
			  <tr><td><label>Other Marks (%):</label></td><td><input type="number" name="marks_other" step="0.01" min="0" max="100" value="<?php echo htmlspecialchars($student['marks_other']); ?>"></td>
			  <td><label>Certificate: </label></td>
			  <td><input type="file" name="cert_other" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this, 'preview_Other')">
			  <input type="hidden" name="existing_cert_other" value="<?php echo htmlspecialchars($student['cert_other'] ?? ''); ?>">
			  </td>
			  <td>
				<?php if (!empty($student['cert_other'])): ?>
				<script>
				  window.addEventListener('DOMContentLoaded', function() {
					previewFile(<?php echo json_encode($student['cert_other']); ?>, 'preview_Other');
				  });
				</script>
				<?php endif; ?>
			  </td>
			   <td><div id="preview_Other" class="preview-text"></div></td>
			  </tr>
		  </table>
	  
      </div>
    </div>

 <!-- Tab 3 -->
    <div class="tab">
      <h3>Work Experience</h3>
      <div class="edu-section">
	  
		  <table>
			  <tr>
			  <td ><label>Experience 1. Date From: </label></td><td ><input type="date" name="Exp1From_date" value="<?php echo htmlspecialchars($student['Exp1From_date']); ?>" ></td>
			  <td ><label>Date To: </label></td><td ><input type="date" name="Exp1To_date" value="<?php echo htmlspecialchars($student['Exp1To_date']); ?>" ></td>
			  <td ><input type="file" name="Exp1_Cert" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_Exp1')">
			  <input type="hidden" name="existing_Exp1_Cert" value="<?php echo htmlspecialchars($student['Exp1_Cert'] ?? ''); ?>">
			  </td>
			  <td>
				<?php if (!empty($student['Exp1_Cert'])): ?>
				<script>
				  window.addEventListener('DOMContentLoaded', function() {
					previewFile(<?php echo json_encode($student['Exp1_Cert']); ?>, 'preview_Exp1');
				  });
				</script>
				<?php endif; ?>
			  </td>
			  <td ><div  id="preview_Exp1" class="preview-text"></div></td>
			  </tr>
			  
			 <tr>
			  <td><label>Experience 2. Date From: </label></td><td><input type="date" name="Exp2From_date" value="<?php echo htmlspecialchars($student['Exp2From_date']); ?>" ></td>
			  <td><label>Date To: </label></td><td><input type="date" name="Exp2To_date" value="<?php echo htmlspecialchars($student['Exp2To_date']); ?>" ></td>
			  <td><input type="file" name="Exp2_Cert" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_Exp2')">
			  <input type="hidden" name="existing_Exp2_Cert" value="<?php echo htmlspecialchars($student['Exp2_Cert'] ?? ''); ?>">
			  </td>
			  <td>
				<?php if (!empty($student['Exp2_Cert'])): ?>
				<script>
				  window.addEventListener('DOMContentLoaded', function() {
					previewFile(<?php echo json_encode($student['Exp2_Cert']); ?>, 'preview_Exp2');
				  });
				</script>
				<?php endif; ?>
			  </td>
			  <td align="bottom"><div id="preview_Exp2" class="preview-text"></div></td>
			  </tr>
			  
			  <tr>
			  <td><label>Experience 3. Date From: </label></td><td><input type="date" name="Exp3From_date" value="<?php echo htmlspecialchars($student['Exp3From_date']); ?>" ></td>
			  <td><label>Date To: </label></td><td><input type="date" name="Exp3To_date" value="<?php echo htmlspecialchars($student['Exp3To_date']); ?>" ></td>
			  <td><input type="file" name="Exp3_Cert" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_Exp3')">
			  <input type="hidden" name="existing_Exp3_Cert" value="<?php echo htmlspecialchars($student['Exp3_Cert'] ?? ''); ?>">
			  </td>
			  <td>
				<?php if (!empty($student['Exp3_Cert'])): ?>
				<script>
				  window.addEventListener('DOMContentLoaded', function() {
					previewFile(<?php echo json_encode($student['Exp3_Cert']); ?>, 'preview_Exp3');
				  });
				</script>
				<?php endif; ?>
			  </td>
			  <td align="bottom"><div id="preview_Exp3" class="preview-text"></div></td>
			  </tr>
			  
			  </tr>
			  
		  </table>
	  
      </div>
    </div>
	
	<!-- Tab 4 -->
    <div class="tab">
      <h3>Language & Aptitude</h3>
      <div class="edu-section">
	  
		  <table>
		  <tr><td colspan=6><u><b>English :</b></u></td></tr>
		  
			<tr>
			  <td></td><td></td>
			  <td>Over all (%)</td>
			  <td >Read  </td>
			  <td >Write </td>
			  <td >Speak </td>
			  <td >Listen</td>
			  <td></td>
			  <td></td>
			</tr>
		  
			<tr>
			  <td ><label>IELTS: </label></td>
			  <td></td>
			  <td ><input step="0.01" min="0" max="100" type="number" value="<?php echo htmlspecialchars($studentlanguagetests['IELTS_OA'] ?? ''); ?>" name="IELTS_OA"></td>
			  <td ><input step="0.01" min="0" max="100" type="number" value="<?php echo htmlspecialchars($studentlanguagetests['IELTS_READ'] ?? ''); ?>" name="IELTS_READ"></td>
			  <td ><input step="0.01" min="0" max="100" type="number" value="<?php echo htmlspecialchars($studentlanguagetests['IELTS_WRITE'] ?? ''); ?>" name="IELTS_WRITE"></td>
			  <td ><input step="0.01" min="0" max="100" type="number" value="<?php echo htmlspecialchars($studentlanguagetests['IELTS_SPEAK'] ?? ''); ?>" name="IELTS_SPEAK"></td>
			  <td ><input step="0.01" min="0" max="100" type="number" value="<?php echo htmlspecialchars($studentlanguagetests['IELTS_LISTEN'] ?? ''); ?>" name="IELTS_LISTEN"></td>
			  <td> <input type="file" name="IELTS_UPLOAD" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_IELTS')">
			  <input type="hidden" name="existing_IELTS_UPLOAD" value="<?php echo htmlspecialchars($studentlanguagetests['IELTS_UPLOAD'] ?? ''); ?>">
			  </td>
			   <td>
				<?php if (!empty($studentlanguagetests['IELTS_UPLOAD'])): ?>
				<script>
				  window.addEventListener('DOMContentLoaded', function() {
					previewFile(<?php echo json_encode($studentlanguagetests['IELTS_UPLOAD']); ?>, 'preview_IELTS');
				  });
				</script>
				<?php endif; ?>
			  </td>
			  <td align="bottom"><div id="preview_IELTS" class="preview-text"></div></td>
			</tr>
			  
			<tr>
			  <td ><label>PTE: </label></td>
			  <td></td>
			  <td ><input step="0.01" min="0" max="100" type="number" value="<?php echo htmlspecialchars($studentlanguagetests['PTE_OA'] ?? ''); ?>" name="PTE_OA"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" value="<?php echo htmlspecialchars($studentlanguagetests['PTE_READ'] ?? ''); ?>" name="PTE_READ"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" value="<?php echo htmlspecialchars($studentlanguagetests['PTE_WRITE'] ?? ''); ?>" name="PTE_WRITE"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" value="<?php echo htmlspecialchars($studentlanguagetests['PTE_SPEAK'] ?? ''); ?>" name="PTE_SPEAK"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" value="<?php echo htmlspecialchars($studentlanguagetests['PTE_LISTEN'] ?? ''); ?>" name="PTE_LISTEN"  ></td>
			  <td> <input step="0.01" min="0" max="100" type="file" name="PTE_UPLOAD" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_PTE')">
			   <input type="hidden" name="existing_PTE_UPLOAD" value="<?php echo htmlspecialchars($studentlanguagetests['PTE_UPLOAD'] ?? ''); ?>">
			  </td>
			  <td>
				<?php if (!empty($studentlanguagetests['PTE_UPLOAD'])): ?>
				<script>
				  window.addEventListener('DOMContentLoaded', function() {
					previewFile(<?php echo json_encode($studentlanguagetests['PTE_UPLOAD']); ?>, 'preview_PTE');
				  });
				</script>
				<?php endif; ?>
			  </td>
			  <td align="bottom"><div id="preview_PTE" class="preview-text"></div></td>
			</tr>
			  
			<tr>
			  <td ><label>TOEFL: </label></td>
			  <td></td>
			  <td ><input step="0.01" min="0" max="100" type="number" value="<?php echo htmlspecialchars($studentlanguagetests['TOEFL_OA'] ?? ''); ?>" name="TOEFL_OA"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" value="<?php echo htmlspecialchars($studentlanguagetests['TOEFL_READ'] ?? ''); ?>" name="TOEFL_READ"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" value="<?php echo htmlspecialchars($studentlanguagetests['TOEFL_WRITE'] ?? ''); ?>" name="TOEFL_WRITE"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" value="<?php echo htmlspecialchars($studentlanguagetests['TOEFL_SPEAK'] ?? ''); ?>" name="TOEFL_SPEAK"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" value="<?php echo htmlspecialchars($studentlanguagetests['TOEFL_LISTEN'] ?? ''); ?>" name="TOEFL_LISTEN"  ></td>
			  <td> <input step="0.01" min="0" max="100" type="file" name="TOEFL_UPLOAD" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_TOEFL')">
			  <input type="hidden" name="existing_TOEFL_UPLOAD" value="<?php echo htmlspecialchars($studentlanguagetests['TOEFL_UPLOAD'] ?? ''); ?>">
			  </td>
			  <td>
				<?php if (!empty($studentlanguagetests['TOEFL_UPLOAD'])): ?>
				<script>
				  window.addEventListener('DOMContentLoaded', function() {
					previewFile(<?php echo json_encode($studentlanguagetests['TOEFL_UPLOAD']); ?>, 'preview_TOEFL');
				  });
				</script>
				<?php endif; ?>
			  </td>
			  <td align="bottom"><div id="preview_TOEFL" class="preview-text"></div></td>
			</tr>
			
			<tr>
			  <td ><label>LANGUAGE CERT: </label></td>
			  <td></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="LANGCERT_OA" value="<?php echo htmlspecialchars($studentlanguagetests['LANGCERT_OA'] ?? ''); ?>" ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="LANGCERT_READ" value="<?php echo htmlspecialchars($studentlanguagetests['LANGCERT_READ'] ?? ''); ?>"></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="LANGCERT_WRITE" value="<?php echo htmlspecialchars($studentlanguagetests['LANGCERT_WRITE'] ?? ''); ?>"></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="LANGCERT_SPEAK" value="<?php echo htmlspecialchars($studentlanguagetests['LANGCERT_SPEAK'] ?? ''); ?>"></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="LANGCERT_LISTEN" value="<?php echo htmlspecialchars($studentlanguagetests['LANGCERT_LISTEN'] ?? ''); ?>"  ></td>
			  <td> <input step="0.01" min="0" max="100" type="file" name="LANGCERT_UPLOAD" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_LANGCERT')">
			  <input type="hidden" name="existing_LANGCERT_UPLOAD" value="<?php echo htmlspecialchars($studentlanguagetests['LANGCERT_UPLOAD'] ?? ''); ?>">
			  </td>
			  <td>
				<?php if (!empty($studentlanguagetests['LANGCERT_UPLOAD'])): ?>
				<script>
				  window.addEventListener('DOMContentLoaded', function() {
					previewFile(<?php echo json_encode($studentlanguagetests['LANGCERT_UPLOAD']); ?>, 'preview_LANGCERT');
				  });
				</script>
				<?php endif; ?>
			  </td>
			  <td align="bottom"><div id="preview_LANGCERT" class="preview-text"></div></td>
			</tr>
			
			<tr>
			  <td ><label>DULINGO: </label></td>
			  <td></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="DULINGO_OA"     value="<?php echo htmlspecialchars($studentlanguagetests['DULINGO_OA'] ?? ''); ?>"></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="DULINGO_READ"   value="<?php echo htmlspecialchars($studentlanguagetests['DULINGO_READ'] ?? ''); ?>"></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="DULINGO_WRITE"  value="<?php echo htmlspecialchars($studentlanguagetests['DULINGO_WRITE'] ?? ''); ?>" ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="DULINGO_SPEAK"  value="<?php echo htmlspecialchars($studentlanguagetests['DULINGO_SPEAK'] ?? ''); ?>" ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="DULINGO_LISTEN" value="<?php echo htmlspecialchars($studentlanguagetests['DULINGO_LISTEN'] ?? ''); ?>"  ></td>
			  <td> <input step="0.01" min="0" max="100" type="file" name="DULINGO_UPLOAD" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_DULINGO')">
			  <input type="hidden" name="existing_DULINGO_UPLOAD" value="<?php echo htmlspecialchars($studentlanguagetests['DULINGO_UPLOAD'] ?? ''); ?>">
			  </td>
			  <td>
				<?php if (!empty($studentlanguagetests['DULINGO_UPLOAD'])): ?>
				<script>
				  window.addEventListener('DOMContentLoaded', function() {
					previewFile(<?php echo json_encode($studentlanguagetests['DULINGO_UPLOAD']); ?>, 'preview_DULINGO');
				  });
				</script>
				<?php endif; ?>
			  </td>
			  <td align="bottom"><div id="preview_DULINGO" class="preview-text"></div></td>
			</tr>
			
			<tr>
			  <td ><label>OTHER: </label></td>
			  <td><input type="text" name="ENGOTHER_NAME" id="ENGOTHER_NAME" maxlength="50" placeholder="Enter Test Name" value="<?php echo htmlspecialchars($studentlanguagetests['ENGOTHER_NAME'] ?? ''); ?>" ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="ENGOTHER_OA" value="<?php echo htmlspecialchars($studentlanguagetests['ENGOTHER_OA'] ?? ''); ?>" ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="ENGOTHER_READ" value="<?php echo htmlspecialchars($studentlanguagetests['ENGOTHER_READ'] ?? ''); ?>"></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="ENGOTHER_WRITE" value="<?php echo htmlspecialchars($studentlanguagetests['ENGOTHER_WRITE'] ?? ''); ?>" ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="ENGOTHER_SPEAK" value="<?php echo htmlspecialchars($studentlanguagetests['ENGOTHER_SPEAK'] ?? ''); ?>" ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="ENGOTHER_LISTEN" value="<?php echo htmlspecialchars($studentlanguagetests['ENGOTHER_LISTEN'] ?? ''); ?>"  ></td>
			  <td> <input step="0.01" min="0" max="100" type="file" name="ENGOTHER_UPLOAD" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_ENGOTHER')">
			  <input type="hidden" name="existing_ENGOTHER_UPLOAD" value="<?php echo htmlspecialchars($studentlanguagetests['ENGOTHER_UPLOAD'] ?? ''); ?>">
			  </td>
			  <td>
				<?php if (!empty($studentlanguagetests['ENGOTHER_UPLOAD'])): ?>
				<script>
				  window.addEventListener('DOMContentLoaded', function() {
					previewFile(<?php echo json_encode($studentlanguagetests['ENGOTHER_UPLOAD']); ?>, 'preview_ENGOTHER');
				  });
				</script>
				<?php endif; ?>
			  </td>
			  <td align="bottom"><div id="preview_ENGOTHER" class="preview-text"></div></td>
			</tr>
			
			
			<!--APTITUDE DETAILS-->
			
			<tr><td colspan=6><br><u><b>Aptitude :</b></u></td></tr>
		  
			<tr>
			  <td></td><td></td>
			  <td>Over all (%)</td>
			  <td >  </td>
			  <td > </td>
			  <td > </td>
			  <td ></td>
			  <td></td>
			  <td></td>
			</tr>
			
			<tr>
			  <td ><label>GRE: </label></td>
			  <td></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="GRE_OA" value="<?php echo htmlspecialchars($studentlanguagetests['GRE_OA'] ?? ''); ?>"  ></td>
			  <td></td>
			  <td ></td>
			  <td ></td>
			  <td ></td>
			  <td> <input step="0.01" min="0" max="100" type="file" name="GRE_UPLOAD" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_GRE')">
			  <input type="hidden" name="existing_GRE_UPLOAD" value="<?php echo htmlspecialchars($studentlanguagetests['GRE_UPLOAD'] ?? ''); ?>">
			  </td>
			  <td>
				<?php if (!empty($studentlanguagetests['GRE_UPLOAD'])): ?>
				<script>
				  window.addEventListener('DOMContentLoaded', function() {
					previewFile(<?php echo json_encode($studentlanguagetests['GRE_UPLOAD']); ?>, 'preview_GRE');
				  });
				</script>
				<?php endif; ?>
			  </td>
			  <td align="bottom"><div id="preview_GRE" class="preview-text"></div></td>
			</tr>
			
			
			<tr>
			  <td ><label>SAT: </label></td>
			  <td></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="SAT_OA" value="<?php echo htmlspecialchars($studentlanguagetests['SAT_OA'] ?? ''); ?>" ></td>
			  <td></td>
			  <td ></td>
			  <td ></td>
			  <td ></td>
			  <td> <input step="0.01" min="0" max="100" type="file" name="SAT_UPLOAD" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_SAT')">
			  <input type="hidden" name="existing_SAT_UPLOAD" value="<?php echo htmlspecialchars($studentlanguagetests['SAT_UPLOAD'] ?? ''); ?>">
			  </td>
			  <td>
				<?php if (!empty($studentlanguagetests['SAT_UPLOAD'])): ?>
				<script>
				  window.addEventListener('DOMContentLoaded', function() {
					previewFile(<?php echo json_encode($studentlanguagetests['SAT_UPLOAD']); ?>, 'preview_SAT');
				  });
				</script>
				<?php endif; ?>
			  </td>
			  <td align="bottom"><div id="preview_SAT" class="preview-text"></div></td>
			</tr>
			
			<tr>
			 <td ><label>GMAT: </label></td>
			  <td></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="GMAT_OA" value="<?php echo htmlspecialchars($studentlanguagetests['GMAT_OA'] ?? ''); ?>" ></td>
			  <td></td>
			  <td ></td>
			  <td ></td>
			  <td ></td>
			  <td> <input step="0.01" min="0" max="100" type="file" name="GMAT_UPLOAD" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_GMAT')">
			  <input type="hidden" name="existing_GMAT_UPLOAD" value="<?php echo htmlspecialchars($studentlanguagetests['GMAT_UPLOAD'] ?? ''); ?>">
			  </td>
			  <td>
				<?php if (!empty($studentlanguagetests['GMAT_UPLOAD'])): ?>
				<script>
				  window.addEventListener('DOMContentLoaded', function() {
					previewFile(<?php echo json_encode($studentlanguagetests['GMAT_UPLOAD']); ?>, 'preview_GMAT');
				  });
				</script>
				<?php endif; ?>
			  </td>
			  <td align="bottom"><div id="preview_GMAT" class="preview-text"></div></td>
			
			<tr>
			  <td ><label>OTHER: </label></td>
			  <td><input type="text" name="APTOTHER_NAME" id="APTOTHER_NAME" maxlength="50" placeholder="Enter Test Name" value="<?php echo htmlspecialchars($studentlanguagetests['APTOTHER_NAME'] ?? ''); ?>" ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="APTOTHER_OA" value="<?php echo htmlspecialchars($studentlanguagetests['APTOTHER_OA'] ?? ''); ?>" ></td>
			  <td ></td>
			  <td ></td>
			  <td ></td>
			  <td ></td>
			  <td> <input step="0.01" min="0" max="100" type="file" name="APTOTHER_UPLOAD" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_APTOTHER')">
			  <input type="hidden" name="existing_APTOTHER_UPLOAD" value="<?php echo htmlspecialchars($studentlanguagetests['APTOTHER_UPLOAD'] ?? ''); ?>">
			  </td>
			  <td>
				<?php if (!empty($studentlanguagetests['APTOTHER_UPLOAD'])): ?>
				<script>
				  window.addEventListener('DOMContentLoaded', function() {
					previewFile(<?php echo json_encode($studentlanguagetests['APTOTHER_UPLOAD']); ?>, 'preview_APTOTHER');
				  });
				</script>
				<?php endif; ?>
			  </td>
			  <td align="bottom"><div id="preview_APTOTHER" class="preview-text"></div></td>
			</tr>
			
		  </table>
	  
      </div>
    </div>



 <!-- Tab 5 -->
    <div class="tab">
      <h3>Course Choice</h3>
      <div class="edu-section">
	  
		<table>
			<tr style="height:30px;">
			<td style="vertical-align: top; height:30px;"><input type="text" id="university" placeholder="University Name" style="height:30px;"></td>
			<td style="vertical-align: top; height:30px;"><input type="text" id="course" placeholder="Course Name"  style="height:30px; "></td>
			<td style="vertical-align: top; height:30px;"><input type="url" id="url" placeholder="Course URL" style="height:30px; ">     </td>
			
			<!-- Intake Month Dropdown -->
			<td style="vertical-align: top; height:30px;">
				<select id="intakeMonth" style="height:30px;">
					<option value="">Month</option>
					<option value="January">January</option>
					<option value="February">February</option>
					<option value="March">March</option>
					<option value="April">April</option>
					<option value="May">May</option>
					<option value="June">June</option>
					<option value="July">July</option>
					<option value="August">August</option>
					<option value="September">September</option>
					<option value="October">October</option>
					<option value="November">November</option>
					<option value="December">December</option>
				</select>
			</td>

			<!-- Intake Year Dropdown -->
			<td style="vertical-align: top; height:30px;">
				<select id="intakeYear" style="height:30px;">
					<option value="">Year</option>
					<option value="2025">2025</option>
					<option value="2026">2026</option>
					<option value="2027">2027</option>
					<option value="2028">2028</option>
					<option value="2029">2029</option>
					<option value="2030">2030</option>
				</select>
			</td>
			
			<td style="vertical-align: top; height:25px;"><button type="button" onclick="addRow()" style="height:25px; ">Add</button>    </td>
			
			</tr>
		</table>


    <input type="hidden" name="courses" id="coursesInput">

		<table style=" border-collapse: collapse; width: 100%; margin-top: 20px;">
			<thead>
				<tr style='height:15px; padding: 1px; border: 1px solid #ccc; text-align: left; position: relative; background-color: lightgrey;'><th style='height:25px; padding: 1px; border: 1px solid #ccc; text-align: left; position: relative;'>University</th><th style='height:25px; padding: 1px; border: 1px solid #ccc; text-align: left; position: relative;'>Course</th><th style='height:25px; padding: 1px; border: 1px solid #ccc; text-align: left; position: relative;'>URL</th>
				
				<th style='height:25px; padding: 1px; border: 1px solid #ccc; text-align: left; position: relative;'>In-Take Month</th>
				<th style='height:25px; padding: 1px; border: 1px solid #ccc; text-align: left; position: relative;'>Year</th>
				
				<th style='height:25px; padding: 1px; border: 1px solid #ccc; text-align: left; position: relative;'>Actions</th></tr>
			</thead>
			<tbody id="courseBody"></tbody>
		</table>

			  
			 <style>
 </style> 
		  
      </div>
    </div>


  <center>  <button type="submit" onclick="goToTab0();">Save Details</button> </center>
  </form>

  <script>

function showTab(index) {
  const tabs = document.querySelectorAll('.tab');
  const buttons = document.querySelectorAll('.tab-buttons button');

  const currentTabIndex = [...tabs].findIndex(tab => tab.classList.contains('active'));
  const currentTab = tabs[currentTabIndex];

  const requiredFields = currentTab.querySelectorAll('.required');
  let isValid = true;
  let firstInvalid = null;

  requiredFields.forEach(field => field.classList.remove('invalid'));

  requiredFields.forEach(field => {
    let valueMissing = false;

    if (field.type === 'file') {
      valueMissing = field.files.length === 0;
    } else if (field.tagName.toLowerCase() === 'select') {
      valueMissing = !field.value;
    } else {
      valueMissing = !field.value.trim();
    }

    if (field.type === 'email' && !valueMissing) {
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailPattern.test(field.value.trim())) {
        valueMissing = true;
      }
    }

    if (valueMissing) {
      field.classList.add('invalid');
      if (!firstInvalid) firstInvalid = field;
      isValid = false;
    }
  });

  if (!isValid) {
    if (firstInvalid) firstInvalid.focus();
    return false;
  }

  tabs.forEach((tab, i) => {
    tab.classList.toggle('active', i === index);
    tab.style.display = i === index ? 'block' : 'none';
    buttons[i].classList.toggle('active', i === index);
  });
}


function validateForm() {
  const tabs = document.querySelectorAll('.tab');
  const tabButtons = document.querySelectorAll('.tab-buttons button');
  let firstInvalidField = null;
  let firstInvalidTabIndex = null;
  let formIsValid = true;

  document.querySelectorAll('.invalid').forEach(el => el.classList.remove('invalid'));

  const requiredFields = document.querySelectorAll('.required');

  requiredFields.forEach(field => {
    let valueMissing = false;

    if (field.type === 'file') {
      valueMissing = field.files.length === 0;
    } else if (field.tagName.toLowerCase() === 'select') {
      valueMissing = !field.value;
    } else {
      valueMissing = !field.value.trim();
    }

    if (field.type === 'email' && !valueMissing) {
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailPattern.test(field.value.trim())) {
        valueMissing = true;
      }
    }

    if (valueMissing) {
      field.classList.add('invalid');
      if (!firstInvalidField) {
        firstInvalidField = field;
        firstInvalidTabIndex = [...tabs].findIndex(tab => tab.contains(field));
      }
      formIsValid = false;
    }
  });

  if (!formIsValid && firstInvalidTabIndex !== null) {
    showTab(firstInvalidTabIndex);
    firstInvalidField.focus();
    return false;
  }
	
  return true;
}

function goToTab0() {
  const tabs = document.querySelectorAll('.tab');
  const buttons = document.querySelectorAll('.tab-buttons button');

  tabs.forEach((tab, i) => {
    tab.classList.toggle('active', i === 0);
    tab.style.display = i === 0 ? 'block' : 'none';
    buttons[i].classList.toggle('active', i === 0);
  });
}
</script>


 
  

<!-- Modal for Preview -->
<div id="previewModal" class="modal">
  <span class="close" onclick="closeModal()">&times;</span>
  <div id="modalContentContainer"></div>
</div>


<script>
/*
const studentId = <?= $student_id ?>;
let courses = <?= json_encode($records) ?>;
const studentName = <?= json_encode($student['name']) ?>;
const studentEmail = <?= json_encode($student['email']) ?>;

        function renderTable() {
            const tbody = document.getElementById("courseBody");
            tbody.innerHTML = "";
            courses.forEach((c, idx) => {
                tbody.innerHTML += `<tr style='height:25px; padding: 1px; border: 1px solid #ccc; text-align: left; position: relative;'>
                    <td style='height:25px; padding: 1px; border: 1px solid #ccc; text-align: left; position: relative;'>${c.University_Name}</td>
                    <td style='height:25px; padding: 1px; border: 1px solid #ccc; text-align: left; position: relative;'>${c.Course_Name}</td>
                    <td style='height:25px; padding: 1px; border: 1px solid #ccc; text-align: left; position: relative;'><a href="${c.Course_URL}" target="_blank">${c.Course_URL}</a></tdstyle='padding: 12px; border: 1px solid #ccc; text-align: left; position: relative;'>
                    <td style='height:25px; padding: 1px; border: 1px solid #ccc; text-align: left; position: relative;'>
                        <button type="button" onclick="editRow(${idx})">Edit</buttonstyle='padding: 12px; border: 1px solid #ccc; text-align: left; position: relative;'>
                        <button type="button" onclick="deleteRow(${idx})">Delete</button>
	
						 
					<button type="button"
					onclick="openMessages(studentId,
					'${encodeURIComponent(c.University_Name)}',
					'${encodeURIComponent(studentName)}',
					'${encodeURIComponent(studentEmail)}')">
					💬 Messages
					</button>
						
           
                    </td>
					
                  </tr>`;
            });
            document.getElementById("coursesInput").value = JSON.stringify(courses);
        }

        window.onload = renderTable;
*/		
		
const studentId = <?= $student_id ?>;
let courses = <?= json_encode($records) ?>;

const studentName  = <?= json_encode($student['name']) ?>;
const studentEmail = <?= json_encode($student['email']) ?>;

function renderTable() {

    const tbody = document.getElementById("courseBody");
    tbody.innerHTML = "";

    courses.forEach((c, idx) => {

        tbody.innerHTML += `
        <tr style='height:25px; padding:1px; border:1px solid #ccc; text-align:left;'>

            <td style='padding:1px; border:1px solid #ccc;'>
                ${c.University_Name || ''}
            </td>

            <td style='padding:1px; border:1px solid #ccc;'>
                ${c.Course_Name || ''}
            </td>

            <td style='padding:1px; border:1px solid #ccc;'>
                <a href="${c.Course_URL || '#'}" target="_blank">
                    ${c.Course_URL || ''}
                </a>
            </td>

            <td style='padding:1px; border:1px solid #ccc;'>
                ${c.Intake_Month || ''}
            </td>

            <td style='padding:1px; border:1px solid #ccc;'>
                ${c.Intake_Year || ''}
            </td>

            <td style='padding:1px; border:1px solid #ccc;'>

                <button type="button" onclick="editRow(${idx})">
                    Edit
                </button>

                <button type="button" onclick="deleteRow(${idx})">
                    Delete
                </button>

                <button type="button"
                    onclick="openMessages(
                        studentId,
                        '${encodeURIComponent(c.University_Name || '')}',
                        '${encodeURIComponent(studentName)}',
                        '${encodeURIComponent(studentEmail)}'
                    )">
                    💬 Messages
                </button>

            </td>

        </tr>`;
    });

    // Hidden field passed to update_student.php
    document.getElementById("coursesInput").value =
        JSON.stringify(courses);
}

window.onload = renderTable;		
		
 /*       function addRow() {
			const uni = document.getElementById("university").value.trim();
            const course = document.getElementById("course").value.trim();
            const url = document.getElementById("url").value.trim();

            if (uni && course && url) {
                courses.push({University_Name: uni, Course_Name: course, Course_URL: url});
                renderTable();
                document.getElementById("university").value="";
                document.getElementById("course").value="";
                document.getElementById("url").value="";
            } else {
                //alert("Please fill all fields.");
				showModal({
					title: "Warning",
					message: "Please fill all mandatory fields.",
					showOk: true
				});
            }
        }
*/
        function addRow() {
			const uni = document.getElementById("university").value.trim();
            const course = document.getElementById("course").value.trim();
            const url = document.getElementById("url").value.trim();
			
			const intakeMonth = document.getElementById("intakeMonth").value;
			const intakeYear = document.getElementById("intakeYear").value;

           /* if (uni && course && url ) {
                courses.push({University_Name: uni, Course_Name: course, Course_URL: url});
                renderTable();
                document.getElementById("university").value="";
                document.getElementById("course").value="";
                document.getElementById("url").value="";
            */
			if (uni && course && url && intakeMonth && intakeYear) {

				courses.push({
					University_Name: uni,
					Course_Name: course,
					Course_URL: url,
					Intake_Month: intakeMonth,
					Intake_Year: intakeYear
				});

				renderTable();

				// Clear fields
				document.getElementById("university").value = "";
				document.getElementById("course").value = "";
				document.getElementById("url").value = "";
				document.getElementById("intakeMonth").value = "";
				document.getElementById("intakeYear").value = "";
				
				} else {
                //alert("Please fill all fields.");
				showModal({
					title: "Warning",
					message: "Please fill all mandatory fields.",
					showOk: true
				});
            }
        }
		
		
        function deleteRow(i) {
            if (confirm("Delete this row?")) {
                courses.splice(i,1);
                renderTable();
            }
        }

 /*       function editRow(i) {
            const c = courses[i];
            document.getElementById("university").value = c.University_Name;
            document.getElementById("course").value = c.Course_Name;
            document.getElementById("url").value = c.Course_URL;
            courses.splice(i,1); // remove and re-add on submit
            renderTable();
        }
*/
		function editRow(i) {

			const c = courses[i];

			document.getElementById("university").value = c.University_Name;
			document.getElementById("course").value = c.Course_Name;
			document.getElementById("url").value = c.Course_URL;

			// Intake fields
			document.getElementById("intakeMonth").value = c.Intake_Month;
			document.getElementById("intakeYear").value = c.Intake_Year;

			// Remove existing row and re-add after editing
			courses.splice(i, 1);

			renderTable();
		}
		
		
	/*function openMessages(studentId, university, studentName, email) {
    window.location.href =
        "messages.php?student_id=" + studentId +
        "&university=" + university +
        "&student_name=" + studentName +
        "&email=" + email;
	}*/


	function openMessages(studentId, university, studentName, studentEmail) {

		const url =
			'messages.php?' +
			'student_id=' + encodeURIComponent(studentId) +
			'&university=' + university +
			'&student_name=' + studentName +
			'&student_email=' + studentEmail;

		document.getElementById("messageFrame").src = url;

		document.getElementById("messageModal").style.display = "block";
	}

	function closeMessageModal() {

		document.getElementById("messageModal").style.display = "none";

		document.getElementById("messageFrame").src = "";
	}

	// close when clicked outside
	window.onclick = function(event) {

		let modal = document.getElementById("messageModal");

		if (event.target == modal) {
			closeMessageModal();
		}
	}


/*
==========================================
*/


function previewFile(input, previewId) {
  const preview = document.getElementById(previewId);
  preview.innerHTML = '';

  let file;
  let ext;

  if (input instanceof HTMLInputElement) {
    file = input.files[0];
    if (!file) return;

    ext = file.name.split('.').pop().toLowerCase();
    const validImageTypes = ['jpg', 'jpeg', 'png'];

    if (validImageTypes.includes(ext)) {
      const reader = new FileReader();
      reader.onload = function(e) {
        const img = document.createElement('img');
        img.src = e.target.result;
        img.className = 'preview-img';
        img.onclick = function() { openModalImage(e.target.result); };
        preview.appendChild(img);
      };
      reader.readAsDataURL(file);
    } else if (ext === 'pdf') {
      const span = document.createElement('span');
      span.textContent = "Click to Open: " + file.name;
      span.className = 'preview-text';
      span.onclick = function() {
        const url = URL.createObjectURL(file);
        openModalPDFInline(url);
      };
      preview.appendChild(span);
    } else {
      preview.textContent = "Unsupported file type: " + file.name;
    }
  } 
  // Support for string filename (edit mode)
  else if (typeof input === 'string') {
    const fileUrl = '' + input;
    ext = input.split('.').pop().toLowerCase();

    if (['jpg', 'jpeg', 'png'].includes(ext)) {
      const img = document.createElement('img');
      img.src = fileUrl;
      img.className = 'preview-img';
      img.onclick = function() { openModalImage(fileUrl); };
      preview.appendChild(img);
    } else if (ext === 'pdf') {
      const span = document.createElement('span');
      span.textContent = "Click to Open: " + input;
      span.className = 'preview-text';
      span.onclick = function() { openModalPDFInline(fileUrl); };
      preview.appendChild(span);
    } else {
      preview.textContent = "Unsupported file type: " + input;
    }
  }
}


  /*function previewFile(input, previewId) {
    const file = input.files[0];
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';

    if (!file) return;

    const ext = file.name.split('.').pop().toLowerCase();
    const validImageTypes = ['jpg', 'jpeg', 'png'];

    if (validImageTypes.includes(ext)) {
      const reader = new FileReader();
      reader.onload = function(e) {
        const img = document.createElement('img');
        img.src = e.target.result;
        img.className = 'preview-img';
        img.onclick = function() { openModalImage(e.target.result); };
        preview.appendChild(img);
      };
      reader.readAsDataURL(file);
    } else if (ext === 'pdf') {
      const span = document.createElement('span');
      span.textContent = "Click to Open: " + file.name;
      span.className = 'preview-text';
      span.onclick = function() {
        const url = URL.createObjectURL(file);
        openModalPDFInline(url);
      };
      preview.appendChild(span);
    } else {
      preview.textContent = "Unsupported file type: " + file.name;
    }
  }*/

  function openModalImage(src) {
    const modal = document.getElementById("previewModal");
    const container = document.getElementById("modalContentContainer");
    container.innerHTML = `<img src="${src}" class="modal-content">`;
    modal.style.display = "block";
  }

  function openModalPDFInline(url) {
    const modal = document.getElementById("previewModal");
    const container = document.getElementById("modalContentContainer");
    container.innerHTML = `<embed src="${url}" type="application/pdf" class="modal-content" style="height:80vh;">`;
    modal.style.display = "block";
  }

  function closeModal() {
    const modal = document.getElementById("previewModal");
    const container = document.getElementById("modalContentContainer");
    container.innerHTML = '';
    modal.style.display = "none";
  }

  window.onclick = function(event) {
    const modal = document.getElementById("previewModal");
    if (event.target == modal) {
      closeModal();
    }
  }
  
  function checkCountry(value) {
  document.getElementById('other_country').value="";
  const otherBox = document.getElementById('other_country_box');
  if (value === 'Others') {
    otherBox.style.display = 'block';
  } else {
    otherBox.style.display = 'none';
  }
}

</script>

<script>
  document.addEventListener("DOMContentLoaded", () => showTab(0));
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const fields = document.querySelectorAll('.required');

  fields.forEach(field => {
    field.addEventListener('input', () => {
      if (field.classList.contains('invalid')) {
        if (field.type === 'file') {
          if (field.files.length > 0) field.classList.remove('invalid');
        } else if (field.tagName.toLowerCase() === 'select') {
          if (field.value) field.classList.remove('invalid');
        } else {
          if (field.value.trim() !== '') {
            if (field.type === 'email') {
              const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
              if (emailPattern.test(field.value.trim())) {
                field.classList.remove('invalid');
              }
            } else {
              field.classList.remove('invalid');
            }
          }
        }
      }
    });

    // For file inputs, also listen to "change"
    if (field.type === 'file') {
      field.addEventListener('change', () => {
        if (field.files.length > 0) {
          field.classList.remove('invalid');
        }
      });
    }
  });
});
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
</script>

<!-- Custom Modal -->
<?php include 'modal.php'; ?>

<!-- Message Modal -->
<div id="messageModal" class="modal">
    <div class="modal-content">
        <!-- Header -->
        <div class="modal-header">
            <h2>💬 Messages</h2>
            <span class="close-btn"
                  onclick="closeMessageModal()">
                  &times;
            </span>
        </div>
        <!-- Body -->
        <div class="modal-body">
            <iframe id="messageFrame"
                    width="100%"
                    height="500"
                    frameborder="0">
            </iframe>
        </div>
    </div>
</div>
  
</body>
</html>