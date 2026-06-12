<!-- dashboard.php -->

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="css/style.css">

		
</head>


<body>

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

$conn = mysqli_connect($host, $user, $password, $database, $port);
//$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
  http_response_code(500);
  die("Connection failed: " . $conn->connect_error);
}

require_once 'news/news_helper.php';

/* AUTO UPDATE */

updateEducationNews($conn);

?>


<div class="page-content">

<div class="dashboard">

    <div class="welcome-box">

        <div>
            <h1>Dashboard</h1>
		
	
            
          Monitor students, applications and communication.      
            
			
        </div>

        <img
            src="images/dashboard.jfif"
            class="dashboard-banner">
		
    </div>


    <div class="stats-grid">

        <div class="stat-card">
            <h3>Total Students</h3>
            <div class="stat-number">1,248</div>
        </div>

        <div class="stat-card">
            <h3>Applications</h3>
            <div class="stat-number">562</div>
        </div>

        <div class="stat-card">
            <h3>Universities</h3>
            <div class="stat-number">96</div>
        </div>

        <div class="stat-card">
            <h3>Messages</h3>
            <div class="stat-number">124</div>
        </div>

    </div>




    <div class="dashboard-sections">

        <div class="section-box">

            <h3>Recent Students</h3>

            <ul class="student-list">
                <li>John Carter</li>
                <li>Sarah Lee</li>
                <li>David Martin</li>
                <li>Priya Sharma</li>
            </ul>

        </div>


        <div class="section-box">

            <h3>Notifications</h3>

            <div class="notification-item">
                New student registered for UK intake.
            </div>

            <div class="notification-item">
                5 unread messages pending.
            </div>

            <div class="notification-item">
                Application deadline approaching.
            </div>

        </div>

    </div>


<?php

$newsQuery = $conn->query("
SELECT *
FROM education_news
ORDER BY published_at DESC
LIMIT 6
");

?>

<div class="news-wrapper">

    <div class="news-header">

        <h2>
            Latest Abroad Education News
        </h2>

        <span>
            Auto Updated
        </span>

    </div>

    <div class="news-grid">

        <?php while($news = $newsQuery->fetch_assoc()){ ?>

        <a
            href="<?= $news['article_url'] ?>"
            target="_blank"
            class="news-card"
        >

            <img
                src="<?= $news['image_url'] ?>"
                class="news-image"
            >

            <div class="news-content">

                <div class="news-tag">
                    <?= $news['category'] ?>
                </div>

                <h3>
                    <?= $news['title'] ?>
                </h3>

                <p>
                    <?= $news['summary'] ?>
                </p>

                <div class="news-footer">

                    <span>
                        <?= $news['source_name'] ?>
                    </span>

                    <span>
                        <?= date(
                            'd M Y',
                            strtotime($news['published_at'])
                        ) ?>
                    </span>

                </div>

            </div>

        </a>

        <?php } ?>

    </div>

</div>

</div>



</div>


</body>
</html>
