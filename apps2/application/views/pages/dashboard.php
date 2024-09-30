<div class="gap">&nbsp;</div>

<?php
switch ($category) {

     case 'sales':
          if ($salesbymonth) {
               echo "<iframe src ='" . base_url() . "index.php/Grids/salesbymonth' class='graphicFrame'></iframe>";
          }
          if ($salesprojbymonth) {
               echo "<iframe src ='" . base_url() . "index.php/Grids/salesprojbymonth' class='graphicFrame'></iframe>";
          }
          if ($salesbyyear) {
               echo "<iframe src ='" . base_url() . "index.php/Grids/salesbyyear' class='graphicFrame'></iframe>";
          }
          if ($salesbystore) {
               echo "<iframe src ='" . base_url() . "index.php/Grids/salesbystore' class='graphicFrame'></iframe>";
          }
          if ($toptensku) {
               echo "<iframe src ='" . base_url() . "index.php/Grids/topten' class='graphicFrame'></iframe>";
          }

          break;

     case 'dropship':
          if ($dropshiporders) {
               echo "<iframe src ='" . base_url() . "index.php/Grids/dropshiporders' class='graphicFrame'></iframe>";
          }
          if ($dropshipordersbytopten) {
               echo "<iframe src ='" . base_url() . "index.php/Grids/dropshipordersbytopten' class='graphicFrame'></iframe>";
          }

          break;

     case 'csr':
          if ($salesbyday) {
               echo "<iframe src ='" . base_url() . "index.php/Grids/salesbyday' class='graphicFrame'></iframe>";
          }
          if ($salesbycsrmx) {
               echo "<iframe src ='" . base_url() . "index.php/Grids/salesbycsrmx' class='graphicFrame'></iframe>";
          }
          if ($itemsordersbycsrmx) {
               echo "<iframe src ='" . base_url() . "index.php/Grids/toptenitemsordersbycsr' class='graphicFrame'></iframe>";
          }

          if ($itemsorderbystore) {
               echo "<iframe src ='" . base_url() . "index.php/Grids/itemsorders' class='graphicFrame'></iframe>";
          }

          if ($salesbysalesperson) {
               echo "<iframe src ='" . base_url() . "index.php/Grids/salesbycsr' class='graphicFrame'></iframe>";
          }

          break;
}
?>
