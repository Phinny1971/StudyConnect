<?php
/******************************************************************************
 * StudyConnect
 *
 * File    : includes/db_connection.php
 * Purpose : Centralized Database Connection
 ******************************************************************************/

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/*
|--------------------------------------------------------------------------
| Load .env only if present (Local Development)
|--------------------------------------------------------------------------
*/

$autoload = dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists($autoload))
{
    require_once $autoload;

    if (class_exists(\Dotenv\Dotenv::class))
    {
        $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->safeLoad();
    }
}

/*
|--------------------------------------------------------------------------
| Database Configuration
|--------------------------------------------------------------------------
*/

$host     = getenv('MYSQLHOST')     ?: 'localhost';
$port     = (int)(getenv('MYSQLPORT') ?: 3306);
$dbname   = getenv('MYSQLDATABASE') ?: 'studyconnect';
$username = getenv('MYSQLUSER')     ?: 'StudyConnect';
$password = getenv('MYSQLPASSWORD') ?: 'Study@2025';

try
{
    $conn = new mysqli(
        $host,
        $username,
        $password,
        $dbname,
        $port
    );

    $conn->set_charset('utf8mb4');
}
catch (mysqli_sql_exception $e)
{
    die("Database connection failed: " . $e->getMessage());
}