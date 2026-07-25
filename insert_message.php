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

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $student_id = $_POST['student_id'] ?? '';
    $university = $_POST['university'] ?? '';
    $message = $_POST['message'] ?? '';
    $payment_link = $_POST['payment_link'] ?? '';

    //$mail_from = $_SESSION['email'];
    $mail_from = "phinny@gmail.com";

    $your_docs = '';
    $ho_docs = '';

    /* Create folders if not exist */

    if (!is_dir("uploads/sop_docs")) {
        mkdir("uploads/sop_docs", 0777, true);
    }

    if (!is_dir("uploads/ho_docs")) {
        mkdir("uploads/ho_docs", 0777, true);
    }

    /* Upload SOP Document */

    if (
        isset($_FILES['your_docs']) &&
        $_FILES['your_docs']['error'] == 0
    )
    {
        $fileName =
            time() . "_SOP_" .
            preg_replace('/[^A-Za-z0-9._-]/', '_',
            $_FILES['your_docs']['name']);

        $target =
            "uploads/sop_docs/" . $fileName;

        if(move_uploaded_file(
            $_FILES['your_docs']['tmp_name'],
            $target))
        {
            $your_docs = $target;
        }
    }

    /* Upload HO Document */

    if (
        isset($_FILES['ho_docs']) &&
        $_FILES['ho_docs']['error'] == 0
    )
    {
        $fileName =
            time() . "_HO_" .
            preg_replace('/[^A-Za-z0-9._-]/', '_',
            $_FILES['ho_docs']['name']);

        $target =
            "uploads/ho_docs/" . $fileName;

        if(move_uploaded_file(
            $_FILES['ho_docs']['tmp_name'],
            $target))
        {
            $ho_docs = $target;
        }
    }

    $sql = "
        INSERT INTO student_messages
        (
            Mail_from,
            student_id,
            Mail_message,
            Message_date,
            University_Name,
            your_docs,
            ho_docs,
            payment_link
        )
        VALUES
        (
            ?,
            ?,
            ?,
            NOW(),
            ?,
            ?,
            ?,
            ?
        )
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sisssss",
        $mail_from,
        $student_id,
        $message,
        $university,
        $your_docs,
        $ho_docs,
        $payment_link
    );

    $stmt->execute();

    echo "success";
}
?>

