<?php
declare(strict_types=1);
session_start();


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {

    $search = urlencode(trim($_GET['search']));
    header("Location: ../pages/services.php?search=$search");
    exit();

} else {

    header("Location: ../pages/home.php");
    exit();
}