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
    <link rel="stylesheet" href="../css/headbar.css">
    <script src="../js/headbar.js" defer></script>
    <script src="../js/profile.js" defer></script>
    <?php
}

function draw_home_header()
{
  ?>
  <link rel="stylesheet" href="../css/headbar.css">
  <link rel="stylesheet" href="../css/home.css">
  <link rel="stylesheet" href="../css/services_grid.css">
  <script src="../js/headbar.js" defer></script>
  <script src="../js/filters.js" defer></script>
  <script src="../js/home.js" defer></script>
  <?php
}

function draw_create_service_header()
{
  ?>
  <link rel="stylesheet" href="../css/headbar.css">
  <link rel="stylesheet" href="../css/create_service.css">
  <script src="../js/headbar.js" defer></script>
  <script src="../js/create_service.js" defer></script>
  <?php
}


function draw_change_password_header()
{
  ?>
  <link rel="stylesheet" href="../css/change_password.css">
  <link rel="stylesheet" href="../css/headbar.css">
  <script src="../js/headbar.js" defer></script>
  <?php
}

function draw_edit_profile_header()
{
  ?>
  <link rel="stylesheet" href="../css/edit_profile.css">
  <link rel="stylesheet" href="../css/headbar.css">
  <script src="../js/headbar.js" defer></script>
  <?php
}

function draw_service_header()
{
  ?>
  <link rel="stylesheet" href="../css/service.css">
  <link rel="stylesheet" href="../css/headbar.css">
  <script src="../js/headbar.js" defer></script>
  <script src="../js/service.js" defer></script>
  <?php
}

function draw_my_services_header()
{
  ?>
  <link rel="stylesheet" href="../css/my_services.css">
  <link rel="stylesheet" href="../css/headbar.css">
  <link rel="stylesheet" href="../css/services_grid.css">
  <script src="../js/headbar.js" defer></script>
  <script src="../js/filters.js" defer></script>
  <script src="../js/my_services.js" defer></script>
  <?php
}

function draw_favorites_header()
{
  ?>
  <link rel="stylesheet" href="../css/favorites.css">
  <link rel="stylesheet" href="../css/headbar.css">
  <link rel="stylesheet" href="../css/services_grid.css">
  <script src="../js/headbar.js" defer></script>
  <script src="../js/filters.js" defer></script>
  <script src="../js/favorites.js" defer></script>
  <?php
}

function draw_chat_header()
{
  ?>
  <link rel="stylesheet" href="../css/chat.css">
  <link rel="stylesheet" href="../css/headbar.css">
  <script src="../js/headbar.js" defer></script>
  <script src="../js/chat.js" defer></script>
  <?php
}

function draw_payment_header()
{
  ?>
  <link rel="stylesheet" href="../css/payment.css">
  <link rel="stylesheet" href="../css/headbar.css">
  <script src="../js/headbar.js" defer></script>
  <?php
}

function draw_edit_service_header()
{
  ?>
  <link rel="stylesheet" href="../css/edit_service.css">
  <link rel="stylesheet" href="../css/headbar.css">
  <script src="../js/headbar.js" defer></script>
  <script src="../js/edit_service.js" defer></script>
  <?php
}
?>

