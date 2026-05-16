<!-- header.php -->
<div class="header">

    <div class="header-left">

        <img src="images/SC-Logo.png.webp" class="logo">

        <div>
            <h1>Student Management System</h1>
            <p>Study Abroad CRM Platform</p>
        </div>

    </div>

    <div class="header-right">

        <div>
            <strong><?php echo $_SESSION['user_name']; ?></strong><br>
            <?php echo $_SESSION['email']; ?>
        </div>

        <a href="logout.php" class="logout-btn">Logout</a>

    </div>

</div>
