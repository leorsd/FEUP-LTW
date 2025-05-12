<?php
declare(strict_types=1);

//Cada página pode incluir apenas os css e js que usa.Isso melhora a performance e evita includes desnecessários.
function draw_login_header()
{
  ?>
  <link rel="stylesheet" href="../css/login.css">
  <?php
}

function draw_register_header()
{
  ?>
  <link rel="stylesheet" href="../css/register.css">
  <?php
}

function draw_welcome_header()
{
  ?>
  <link rel="stylesheet" href="../css/welcome.css">
  <script src="../js/welcome.js" defer></script>
  <?php
}

function draw_profile_header()
{
  ?>
    <link rel="stylesheet" href="../css/profile.css">
    <script src="../js/profile.js" defer></script>
    <?php
}

function draw_home_header()
{
  ?>
  <link rel="stylesheet" href="../css/headbar.css">
  <link rel="stylesheet" href="../css/home.css">
  <script src="../js/home.js" defer></script>
  <?php
}

function draw_create_service_header()
{
  ?>
  <link rel="stylesheet" href="../css/headbar.css">
  <link rel="stylesheet" href="../css/create_service.css">
  <script src="../js/create_service.js" defer></script>
  <?php
}

?>

