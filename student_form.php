<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Prevent browser cache
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

/*Temporary
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
 */

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

// Fetch countries
$sql = "SELECT country_name FROM countries";
$result = $conn->query($sql);

// Create options HTML
$options = "";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $country = htmlspecialchars($row['country_name']);
        $options .= "<option value=\"$country\">$country</option>\n";
    }
} else {
    $options .= "<option disabled>No countries found</option>";
}

// Fetch Branches
$sql = "SELECT Branch_name FROM branches";
$result = $conn->query($sql);

// Create Branchoptions HTML
$Branchoptions = "";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $Branch_name = htmlspecialchars($row['Branch_name']);
        $Branchoptions .= "<option value=\"$Branch_name\">$Branch_name</option>\n";
    }
} else {
    $Branchoptions .= "<option disabled>No Branches found</option>";
}

// Fetch for display (if needed)
$res = $conn->prepare("SELECT * FROM coursechoice WHERE student_id=?");
$res->bind_param("i", $student_id);
$res->execute();
$records = $res->get_result()->fetch_all(MYSQLI_ASSOC);
$res->close();


$conn->close();
?>


<html lang="en">
<head>
<link rel="stylesheet" href="css/style.css">

  <meta charset="UTF-8">
  <title>Student Registration</title>
  
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

td {
    vertical-align: middle;
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
  

 
  
</head>
<body >

<!--
<table width="100%">
<TR><td><img fetchpriority="high" decoding="async" width="200" height="57" src="images/SCLogo.png" class="attachment-large size-large wp-image-4513" alt="" sizes="(max-width: 800px) 100vw, 800px"></td>
<td style="text-align: right; vertical-align: bottom;"> <h1> Student Tracking System</h1></td></tr>
</table>
<hr>
-->

  <h2>Student Registration Form</h2>

  <div class="tab-buttons">
    <button type="button" class="active" onclick="showTab(0)">Student Details</button>
    <button type="button" onclick="showTab(1)">Education Details</button>
	 <button type="button" onclick="showTab(2)">Work Experience</button>
	  <button type="button" onclick="showTab(3)">Language & Aptitude</button>
	  <button type="button" onclick="showTab(4)">Course Choice</button>
  </div>

  <form action="submit_student.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm();">

    <!-- Tab 1 -->
    <div class="tab active">
	<h3>Personal Details</h3>
	 <div class="edu-section">
	<table>
     <tr> 
	 <td ><label>Name: </label></td><td > <input  type="text" name="name" id="name" class="required" maxlength="50"> </td>
	  <td style="width:50px;"></td> <td style="width:200px;" ><label>Passport No.: </label></td>	 <td > <input type="text" name="Passport_no" maxlength="50" class="required"> </td>
	 <td ><input type="file" name="Passport_Upload" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_passport')" class="required"></td>
	 <td ><div id="preview_passport" class="preview-text"></div></td>
	 </tr>
     
	 <tr> <td><label>Email: </label></td><td><input type="email" name="email" id="email" class="required" maxlength="50"></td>
	<td style="width:50px;"></td><td>  <label>Date of Issue: </label></td>  <td> <input type="date" name="Passport_issue" class="required"> </td>
	 </tr>

     <tr> 
	 <td><label>Address:</label></td><td><input type="text" name="address" id="address" class="required"></td>
	 <td style="width:50px;"></td><td><label>Date of Expiry: </label></td>  <td> <input type="date" name="Passport_Expiry" class="required"> </td>
	 </tr>
	 
     <tr> <td><label>Phone: </label></td><td><input type="text" name="phone" id="phone" class="required" pattern="[0-9]{10}" title="Enter 10 digit phone number"></td>

      <td style="width:50px;"></td><td><label>Branch of Registration: </label></td>
	 <td> 
		<select name="Branch_name" id="Branch_name" class="required" >
          <option value="">Select Branch</option>
           <?php echo $Branchoptions; ?>
        </select>
	 </td>

	 
	 </tr>

      <tr>
      <td><label>Date of Birth: </label></td>  <td> <input type="date" name="DateOfBirth" class="required"> </td>
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
	   <td><label>Gender:</label></td>
        <td>
        <select name="gender" id="gender"   >
          <option value="">Select Gender</option>
 			<option value="Male">Male </option>
			<option value="Female">Female </option>
			<option value="Transgender"> Transgender </option>
			<option value="Non-binary/non-conforming">Non-binary/non-conforming </option>
			<option value="Prefer not to respond">Prefer not to respond </option>
        </select>
	  </td>
        <td style="width:50px;"></td>
	     <td><label>Marital Status:</label></td>
        <td>
        <select name="maritalstatus" id="maritalstatus"   >
          <option value="">Select Status</option>
 			<option value="Single">Single </option>
			<option value="Married">Married </option>
			<option value="Divorced"> Divorced </option>
			<option value="Widowed">Widowed </option>
			<option value="Separated">Separated </option>
			<option value="Prefer not to say">Prefer not to say </option>
        </select>
	  </td>
	  
     </tr>
	 
	
	  
	  <tr>
	  <td></td>
      <td></td>
      <td></td>
	 
	  </tr>
	 
	  </table>
    </div>
	
	
	  <h3>Emergency Contacts</h3>
		 <div class="edu-section">
		<table>
		 <tr> 
		 <td><label>Name: </label></td><td style="width:50px;"></td><td > <input  type="text" name="emergency_name" id="emergency_name"  maxlength="50"> </td>
		  <td style="width:50px;"></td> <td style="width:200px;" ><label>Emergency Phone: </label></td>	 
		  <td > <input type="text" name="emergency_phone" id="emergency_phone"  pattern="[0-9]{10}" title="Enter 10 digit phone number"> </td>
		 <td ></td>
		 <td ></td>
		 </tr>
		 
		  <tr> 
		 <td><label>Email: </label></td><td style="width:50px;"></td><td > <input  type="text" name="emergency_email" id="emergency_email"  maxlength="50"> </td>
		  <td style="width:50px;"></td> <td style="width:200px;" ><label>Relationship: </label></td>	 
		  <td > <input  type="text" name="emergency_relation" id="emergency_relation"  maxlength="50"> </td>
		 <td ></td>
		 <td ></td>
		 </tr>
		 
		 </table>
		 </div>

	 	  <h3>Background Information</h3>
		 <div class="edu-section">
		<table>
		 <tr> 
		 <td><label>Has the applicant applied for any type of immigration into any country? (If Yes, Please provide details): </label></td>
		 </tr>
		 <tr>
		 <td > <input  type="text" name="immi_country" id="immi_country"  maxlength="500"> </td>
		 <td><input type="file" name="immi_country_file" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this, 'preview_immi_country')" ></td>
			   <td><div id="preview_immi_country" class="preview-text"></div></td>
			   
		 </tr>
		 
		<tr> 
		 <td><label>Does applicant suffer from any serious medical condition? (If Yes, Please provide details): </label></td>
		 </tr>
		 <tr>
		 <td > <input  type="text" name="medical_cond" id="medical_cond"  maxlength="500"> </td>
		 <td><input type="file" name="medical_cond_file" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this, 'preview_medical_cond')" ></td>
			   <td><div id="preview_medical_cond" class="preview-text"></div></td>
		 </tr>

		<tr> 
		 <td><label>Has applicant Visa refusal for any country? (If Yes, Please provide details): </label></td>
		 </tr>
		 <tr>
		 <td > <input  type="text" name="visa_refusal" id="visa_refusal"  maxlength="500"> </td>
		 <td><input type="file" name="visa_refusal_file" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this, 'preview_visa_refusal')" ></td>
			   <td><div id="preview_visa_refusal" class="preview-text"></div></td>
		 </tr>
		 
		<tr> 
		 <td><label>Has applicant ever been convicted of a criminal offence? (If Yes, Please provide details): </label></td>
		 </tr>
		 <tr>
		 <td > <input  type="text" name="convicted_offence" id="convicted_offence"  maxlength="500"> </td>
		 <td><input type="file" name="convicted_offence_file" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this, 'preview_convicted_offence')" ></td>
			   <td><div id="preview_convicted_offence" class="preview-text"></div></td>
		 </tr>
		 
		 </table>
		 </div>
		 
	  
	</div>

    <!-- Tab 2 -->
    <div class="tab">
      <h3>Education Details</h3>
      <div class="edu-section">
	  
		  <table>
			  <tr><td><label>10th Marks (%): </label></td><td><input type="number" name="marks_10th" step="0.01" min="0" max="100" ></td>
			  <td><label>Certificate: </label></td>
			  <td><input type="file" name="cert_10th" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_10th')"></td>
			  <td><div id="preview_10th" class="preview-text"></div></td>
			  </tr>
			  
			  <tr><td><label>Intermediate Marks (%):</label></td><td><input type="number" name="marks_intermediate" step="0.01" min="0" max="100" ></td>
			  <td><label>Certificate: </label></td>
			  <td><input type="file" name="cert_intermediate" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this, 'preview_intermediate')" ></td>
			   <td><div id="preview_intermediate" class="preview-text"></div></td>
			  </tr>
			  
			  <tr><td><label>Degree Marks (%): </label></td><td><input type="number" name="marks_degree" step="0.01" min="0" max="100" ></td>
			  <td><label>Certificate: </label></td>
			  <td><input type="file" name="cert_degree" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this, 'preview_Degree')" ></td>
			   <td><div id="preview_Degree" class="preview-text"></div></td>
			  </tr>
			  
			  <tr><td><label>Post Graduation Marks (%):</label> </td><td><input type="number" name="marks_pg" step="0.01" min="0" max="100"></td>
			  <td><label>Certificate: </label></td>
			  <td><input type="file" name="cert_pg" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this, 'preview_PG')"></td>
			  <td><div id="preview_PG" class="preview-text"></div></td>
			  </tr>
			  
			  <tr><td><label>Diploma Marks (%):</label> </td><td><input type="number" name="marks_diploma" step="0.01" min="0" max="100"></td>
			  <td><label>Certificate:</label></td>
			  <td> <input type="file" name="cert_diploma" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this, 'preview_Diploma')"></td>
			  <td><div id="preview_Diploma" class="preview-text"></div></td>
			  </tr>
			  
			  <tr><td><label>Other Marks (%):</label></td><td><input type="number" name="marks_other" step="0.01" min="0" max="100"></td>
			  <td><label>Certificate: </label></td>
			  <td><input type="file" name="cert_other" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this, 'preview_Other')"></td>
			   <td><div id="preview_Other" class="preview-text"></div></td>
			  </tr>
		
			<tr>
				<td><label>Medium of Instruction (MOI):</label></td>
				<td></td>
				<td></td>
				<td><input type="file" name="moi" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this, 'preview_moi')"></td>
				<td><div id="preview_moi" class="preview-text"></div></td>
			</tr>	

			<tr>
				<td><label>CV / Resume:</label></td>
				<td></td>
				<td></td>
				<td><input type="file" name="resume" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this, 'preview_resume')"></td>
				<td><div id="preview_resume" class="preview-text"></div></td>
			</tr>	

			<tr>
				<td><label>Other documents (if any):</label></td>
				<td></td>
				<td></td>
				<td><input type="file" name="otherdoc" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this, 'preview_otherdoc')"></td>
				<td><div id="preview_otherdoc" class="preview-text"></div></td>
			</tr>	
			
			<tr>
				<td><label>Letter of Recommendation 1 (LOR 1):</label></td>
				<td></td>
				<td></td>
				<td><input type="file" name="lor1" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this, 'preview_lor1')"></td>
				<td><div id="preview_lor1" class="preview-text"></div></td>
				
				<td ><label>Ref.Name: </label></td><td > <input  type="text" name="lor1name" id="lor1name"  maxlength="50"> </td>
				<td><label>Email: </label></td><td><input type="email" name="lor1email" id="lor1email"  maxlength="50"></td>
				<td><label>Phone: </label></td><td><input type="text" name="lor1phone" id="lor1phone"  pattern="[0-9]{10}" 
				 maxlength="10" title="Enter 10 digit phone number"></td>
			</tr>

			<tr>
				<td><label>Letter of Recommendation 2 (LOR 2):</label></td>
				<td></td>
				<td></td>
				<td><input type="file" name="lor2" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this, 'preview_lor2')"></td>
				<td><div id="preview_lor2" class="preview-text"></div></td>
				
				<td ><label>Ref.Name: </label></td><td > <input  type="text" name="lor2name" id="lor2name"  maxlength="50"> </td>
				<td><label>Email: </label></td><td><input type="email" name="lor2email" id="lor2email"  maxlength="50"></td>
				<td><label>Phone: </label></td><td><input type="text" name="lor2phone" id="lor2phone"  pattern="[0-9]{10}" 
				 maxlength="10" title="Enter 10 digit phone number"></td>
			</tr>
			
			<tr>
				<td><label>Letter of Recommendation 3 (LOR 3):</label></td>
				<td></td>
				<td></td>
				<td><input type="file" name="lor3" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this, 'preview_lor3')"></td>
				<td><div id="preview_lor3" class="preview-text"></div></td>
				
				<td ><label>Ref.Name: </label></td><td > <input  type="text" name="lor3name" id="lor3name"  maxlength="50"> </td>
				<td><label>Email: </label></td><td><input type="email" name="lor3email" id="lor3email"  maxlength="50"></td>
				<td><label>Phone: </label></td><td><input type="text" name="lor3phone" id="lor3phone"  pattern="[0-9]{10}" 
				 maxlength="10" title="Enter 10 digit phone number"></td>
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
			  <td ><label>Experience 1. Date From: </label></td><td ><input type="date" name="Exp1From_date"  ></td>
			  <td ><label>Date To: </label></td><td ><input type="date" name="Exp1To_date"  ></td>
			  <td ><input type="file" name="Exp1_Cert" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_Exp1')"></td>
			  <td ><div  id="preview_Exp1" class="preview-text"></div></td>
			  </tr>
			  
			 <tr>
			  <td><label>Experience 2. Date From: </label></td><td><input type="date" name="Exp2From_date"  ></td>
			  <td><label>Date To: </label></td><td><input type="date" name="Exp2To_date"  ></td>
			  <td><input type="file" name="Exp2_Cert" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_Exp2')"></td>
			  <td align="bottom"><div id="preview_Exp2" class="preview-text"></div></td>
			  </tr>
			  
			  <tr>
			  <td><label>Experience 3. Date From: </label></td><td><input type="date" name="Exp3From_date"  ></td>
			  <td><label>Date To: </label></td><td><input type="date" name="Exp3To_date"  ></td>
			  <td><input type="file" name="Exp3_Cert" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_Exp3')"></td>
			  <td align="bottom"><div id="preview_Exp3" class="preview-text"></div></td>
			  </tr>
			  
			<tr>
				<td><label>Letter of Recommendation 1 (LOR 1):</label></td>
				
				<td><input type="file" name="explor1" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this, 'preview_explor1')"></td>
				<td><div id="preview_explor1" class="preview-text"></div></td>
				
				<td ><label>Ref.Name: </label></td><td > <input  type="text" name="explor1name" id="explor1name"  maxlength="50"> </td>
				<td><label>Email: </label></td><td><input type="email" name="explor1email" id="explor1email"  maxlength="50"></td>
				<td><label>Phone: </label></td><td><input type="text" name="explor1phone" id="explor1phone"  pattern="[0-9]{10}" 
				 maxlength="10" title="Enter 10 digit phone number"></td>
			</tr>

			<tr>
				<td><label>Letter of Recommendation 2 (LOR 2):</label></td>
				
				<td><input type="file" name="explor2" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this, 'preview_explor2')"></td>
				<td><div id="preview_explor2" class="preview-text"></div></td>
				
				<td ><label>Ref.Name: </label></td><td > <input  type="text" name="explor2name" id="explor2name"  maxlength="50"> </td>
				<td><label>Email: </label></td><td><input type="email" name="explor2email" id="explor2email"  maxlength="50"></td>
				<td><label>Phone: </label></td><td><input type="text" name="explor2phone" id="explor2phone"  pattern="[0-9]{10}" 
				 maxlength="10" title="Enter 10 digit phone number"></td>
			</tr>
			
			<tr>
				<td><label>Letter of Recommendation 3 (LOR 3):</label></td>
				
				<td><input type="file" name="explor3" accept=".jpg,.jpeg,.png,.pdf" onchange="previewFile(this, 'preview_explor3')"></td>
				<td><div id="preview_explor3" class="preview-text"></div></td>
				
				<td ><label>Ref.Name: </label></td><td > <input  type="text" name="explor3name" id="explor3name"  maxlength="50"> </td>
				<td><label>Email: </label></td><td><input type="email" name="explor3email" id="explor3email"  maxlength="50"></td>
				<td><label>Phone: </label></td><td><input type="text" name="explor3phone" id="explor3phone"  pattern="[0-9]{10}" 
				 maxlength="10" title="Enter 10 digit phone number"></td>
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
			  <td ><input step="0.01" min="0" max="100" type="number" name="IELTS_OA"></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="IELTS_READ"></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="IELTS_WRITE"></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="IELTS_SPEAK"></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="IELTS_LISTEN"></td>
			  <td> <input type="file" name="IELTS_UPLOAD" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_IELTS')"></td>
			  <td align="bottom"><div id="preview_IELTS" class="preview-text"></div></td>
			</tr>
			  
			<tr>
			  <td ><label>PTE: </label></td>
			  <td></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="PTE_OA"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="PTE_READ"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="PTE_WRITE"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="PTE_SPEAK"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="PTE_LISTEN"  ></td>
			  <td> <input step="0.01" min="0" max="100" type="file" name="PTE_UPLOAD" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_PTE')"></td>
			  <td align="bottom"><div id="preview_PTE" class="preview-text"></div></td>
			</tr>
			  
			<tr>
			  <td ><label>TOEFL: </label></td>
			  <td></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="TOEFL_OA"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="TOEFL_READ"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="TOEFL_WRITE"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="TOEFL_SPEAK"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="TOEFL_LISTEN"  ></td>
			  <td> <input step="0.01" min="0" max="100" type="file" name="TOEFL_UPLOAD" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_TOEFL')"></td>
			  <td align="bottom"><div id="preview_TOEFL" class="preview-text"></div></td>
			</tr>
			
			<tr>
			  <td ><label>LANGUAGE CERT: </label></td>
			  <td></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="LANGCERT_OA"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="LANGCERT_READ"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="LANGCERT_WRITE"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="LANGCERT_SPEAK"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="LANGCERT_LISTEN"  ></td>
			  <td> <input step="0.01" min="0" max="100" type="file" name="LANGCERT_UPLOAD" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_LANGCERT')"></td>
			  <td align="bottom"><div id="preview_LANGCERT" class="preview-text"></div></td>
			</tr>
			
			<tr>
			  <td ><label>DULINGO: </label></td>
			  <td></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="DULINGO_OA"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="DULINGO_READ"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="DULINGO_WRITE"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="DULINGO_SPEAK"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="DULINGO_LISTEN"  ></td>
			  <td> <input step="0.01" min="0" max="100" type="file" name="DULINGO_UPLOAD" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_DULINGO')"></td>
			  <td align="bottom"><div id="preview_DULINGO" class="preview-text"></div></td>
			</tr>
			
			<tr>
			  <td ><label>OTHER: </label></td>
			  <td><input type="text" name="ENGOTHER_NAME" id="ENGOTHER_NAME" maxlength="50" placeholder="Enter Test Name" ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="ENGOTHER_OA"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="ENGOTHER_READ"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="ENGOTHER_WRITE"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="ENGOTHER_SPEAK"  ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="ENGOTHER_LISTEN"  ></td>
			  <td> <input step="0.01" min="0" max="100" type="file" name="ENGOTHER_UPLOAD" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_ENGOTHER')"></td>
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
			  <td ><input step="0.01" min="0" max="100" type="number" name="GRE_OA"  ></td>
			  <td></td>
			  <td ></td>
			  <td ></td>
			  <td ></td>
			  <td> <input step="0.01" min="0" max="100" type="file" name="GRE_UPLOAD" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_GRE')"></td>
			  <td align="bottom"><div id="preview_GRE" class="preview-text"></div></td>
			</tr>
			
			
			<tr>
			  <td ><label>SAT: </label></td>
			  <td></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="SAT_OA"  ></td>
			  <td></td>
			  <td ></td>
			  <td ></td>
			  <td ></td>
			  <td> <input step="0.01" min="0" max="100" type="file" name="SAT_UPLOAD" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_SAT')"></td>
			  <td align="bottom"><div id="preview_SAT" class="preview-text"></div></td>
			</tr>
			
			<tr>
			 <td ><label>GMAT: </label></td>
			  <td></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="GMAT_OA"  ></td>
			  <td></td>
			  <td ></td>
			  <td ></td>
			  <td ></td>
			  <td> <input step="0.01" min="0" max="100" type="file" name="GMAT_UPLOAD" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_GMAT')"></td>
			  <td align="bottom"><div id="preview_GMAT" class="preview-text"></div></td>
			</tr>
			<tr>
			  <td ><label>OTHER: </label></td>
			  <td><input type="text" name="APTOTHER_NAME" id="APTOTHER_NAME" maxlength="50" placeholder="Enter Test Name" ></td>
			  <td ><input step="0.01" min="0" max="100" type="number" name="APTOTHER_OA"  ></td>
			  <td ></td>
			  <td ></td>
			  <td ></td>
			  <td ></td>
			  <td> <input step="0.01" min="0" max="100" type="file" name="APTOTHER_UPLOAD" accept=".jpg,.jpeg,.png,.pdf"  onchange="previewFile(this, 'preview_APTOTHER')"></td>
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
				<th style='height:25px; padding: 1px; border: 1px solid #ccc; text-align: left; position: relative;'>Actions</th>
				</tr>
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
   /* function showTab(index) {
      const tabs = document.querySelectorAll('.tab');
      const buttons = document.querySelectorAll('.tab-buttons button');
      tabs.forEach((tab, i) => {
        tab.classList.toggle('active', i === index);
        buttons[i].classList.toggle('active', i === index);
      });
    }
*/


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


 
  <script>
  function previewFile(input, previewId) {
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
        preview.appendChild(img);
      };
      reader.readAsDataURL(file);
    } else if (ext === 'pdf') {
      preview.textContent = "Click to Open: " + file.name;
    } else {
      preview.textContent = "Unsupported file type: " + file.name;
    }
  }
</script>

<!-- Modal for Preview -->
<div id="previewModal" class="modal">
  <span class="close" onclick="closeModal()">&times;</span>
  <div id="modalContentContainer"></div>
</div>

<script>
        let courses = <?= json_encode($records) ?>;
		
		/*
        function renderTable() {
            const tbody = document.getElementById("courseBody");
            tbody.innerHTML = "";
            courses.forEach((c, idx) => {
                tbody.innerHTML += `<tr style='height:25px; padding: 1px; border: 1px solid #ccc; text-align: left; position: relative;'>
                    <td style='height:25px; padding: 1px; border: 1px solid #ccc; text-align: left; position: relative;'>${c.University_Name}</td>
                    <td style='height:25px; padding: 1px; border: 1px solid #ccc; text-align: left; position: relative;'>${c.Course_Name}</td>
                    <td style='height:25px; padding: 1px; border: 1px solid #ccc; text-align: left; position: relative;'><a href="${c.Course_URL}" target="_blank">${c.Course_URL}</a></td>
                    <td style='height:25px; padding: 1px; border: 1px solid #ccc; text-align: left; position: relative;'>
                        <button type="button" onclick="editRow(${idx})">Edit</buttonstyle='padding: 12px; border: 1px solid #ccc; text-align: left; position: relative;'>
                        <button type="button" onclick="deleteRow(${idx})">Delete</button>
                    </td>
                  </tr>`;
            });
            document.getElementById("coursesInput").value = JSON.stringify(courses);
        }
		*/
		
		function renderTable() {

			const tbody = document.getElementById("courseBody");
			tbody.innerHTML = "";
			courses.forEach((c, idx) => {
				tbody.innerHTML += `
				<tr style='height:25px; padding:1px; border:1px solid #ccc; text-align:left;'>
					<td style='padding:1px; border:1px solid #ccc;'>
						${c.University_Name}
					</td>
					<td style='padding:1px; border:1px solid #ccc;'>
						${c.Course_Name}
					</td>
					<td style='padding:1px; border:1px solid #ccc;'>
						<a href="${c.Course_URL}" target="_blank">
							${c.Course_URL}
						</a>
					</td>
					<td style='padding:1px; border:1px solid #ccc;'>
						${c.Intake_Month}
					</td>
					<td style='padding:1px; border:1px solid #ccc;'>
						${c.Intake_Year}
					</td>
					<td style='padding:1px; border:1px solid #ccc;'>
						<button type="button" onclick="editRow(${idx})">
							Edit
						</button>
						<button type="button" onclick="deleteRow(${idx})">
							Delete
						</button>
					</td>
				</tr>`;
			});

			document.getElementById("coursesInput").value =
				JSON.stringify(courses);
		}

        window.onload = renderTable;
		
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

		/*
        function editRow(i) {
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

/*
==========================================
*/

  function previewFile(input, previewId) {
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
  }

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

</body>
</html>
