<?php
require("../../library/include/datlib.inc.php");
$admin_aziend=checkAdmin();
// $img1px � una stringa equivalente ad una immagine jpg di 1 pixel
// e serve per evitare la visualizzazione dell'immagine di dafault del browser
// quando non la trova
$img1px= pack("c*",0xFF,0xD8,0xFF,0xE0,0x00,0x10,0x4A,0x46,0x49,0x46,0x00,0x01,0x02,
              0x00,0x00,0x64,0x00,0x64,0x00,0x00,0xFF,0xEC,0x00,0x11,0x44,0x75,
              0x63,0x6B,0x79,0x00,0x01,0x00,0x04,0x00,0x00,0x00,0x00,0x00,0x00,
              0xFF,0xEE,0x00,0x0E,0x41,0x64,0x6F,0x62,0x65,0x00,0x64,0xC0,0x00,
              0x00,0x00,0x01,0xFF,0xDB,0x00,0x84,0x00,0x1B,0x1A,0x1A,0x29,0x1D,
              0x29,0x41,0x26,0x26,0x41,0x42,0x2F,0x2F,0x2F,0x42,0x47,0x3F,0x3E,
              0x3E,0x3F,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,
              0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,
              0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,
              0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x01,0x1D,0x29,0x29,0x34,0x26,
              0x34,0x3F,0x28,0x28,0x3F,0x47,0x3F,0x35,0x3F,0x47,0x47,0x47,0x47,
              0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,
              0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,
              0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,0x47,
              0x47,0x47,0x47,0x47,0x47,0x47,0x47,0xFF,0xC0,0x00,0x11,0x08,0x00,
              0x01,0x00,0x01,0x03,0x01,0x22,0x00,0x02,0x11,0x01,0x03,0x11,0x01,
              0xFF,0xC4,0x00,0x4B,0x00,0x01,0x01,0x00,0x00,0x00,0x00,0x00,0x00,
              0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x06,0x01,0x01,0x00,
              0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,
              0x00,0x00,0x10,0x01,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,
              0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x11,0x01,0x00,0x00,0x00,0x00,
              0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0xFF,
              0xDA,0x00,0x0C,0x03,0x01,0x00,0x02,0x11,0x03,0x11,0x00,0x3F,0x00,
              0xA6,0x00,0x1F,0xFF,0xD9);
if (isset($_GET['table']) && isset($_GET['value'])){
     if (isset($_GET['field'])){
          $f=filter_var(substr($_GET['field'],0,30),FILTER_SANITIZE_MAGIC_QUOTES);
     } else {
          $f='codice';
     }
     $t=filter_var(substr($_GET['table'],0,30),FILTER_SANITIZE_MAGIC_QUOTES);
     $col = gaz_dbi_get_row($gTables[$t],$f,filter_var(substr($_GET['value'],0,30),FILTER_SANITIZE_MAGIC_QUOTES));
     header ('Content-type: image/pjpeg');
     if (empty($col['image'])) {
        echo $img1px;
     } else {
        echo $col['image'];
     }
}
?>