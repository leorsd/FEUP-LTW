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