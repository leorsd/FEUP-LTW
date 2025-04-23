<?php
declare(strict_types=1);

function draw_initial_common_header(string $title)
{
  ?>
  <!DOCTYPE html>
  <html lang="en">
  
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
<?php }

function draw_final_common_header()
{
  ?>
  </head>
  <body>
<?php
}

function draw_common_footer()
{
  ?>
      <footer>
        <p>&copy; CarLink 2025 - LTWT2G2 - All Rights Reserved. Made by Arnaldo Lopes, António Braga e Leandro Resende</p>
      </footer>
    </body>
  </html>
  <?php
}
?>