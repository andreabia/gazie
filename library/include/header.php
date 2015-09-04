<?php
/*
 --------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2015 - Antonio De Vincentiis Montesilvano (PE)
         (http://www.devincentiis.it)
           <http://gazie.devincentiis.it>
 --------------------------------------------------------------------------
    Questo programma e` free software;   e` lecito redistribuirlo  e/o
    modificarlo secondo i  termini della Licenza Pubblica Generica GNU
    come e` pubblicata dalla Free Software Foundation; o la versione 2
    della licenza o (a propria scelta) una versione successiva.

    Questo programma  e` distribuito nella speranza  che sia utile, ma
    SENZA   ALCUNA GARANZIA; senza  neppure  la  garanzia implicita di
    NEGOZIABILITA` o di  APPLICABILITA` PER UN  PARTICOLARE SCOPO.  Si
    veda la Licenza Pubblica Generica GNU per avere maggiori dettagli.

    Ognuno dovrebbe avere   ricevuto una copia  della Licenza Pubblica
    Generica GNU insieme a   questo programma; in caso  contrario,  si
    scriva   alla   Free  Software Foundation,  Inc.,   59
    Temple Place, Suite 330, Boston, MA 02111-1307 USA Stati Uniti.
 --------------------------------------------------------------------------
*/
if (!strstr($_SERVER["REQUEST_URI"], "login_admin") == "login_admin.php") {
    $_SESSION['lastpage'] = $_SERVER["REQUEST_URI"];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="author" content="Antonio De Vincentiis http://www.devincentiis.it">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<?php
$menuclass = ' class="FacetMainMenu" ';
$style = 'default.css';
if (!empty($admin_aziend['style']) && file_exists( "../../library/style/".$admin_aziend['style'] ) ){
    $style = $admin_aziend['style'];
}

?>
<link href="../../library/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../../library/bootstrap/css/ml_dropdown.css" rel="stylesheet" type="text/css" >
<link href="../../library/style/<?php echo $style;?>" rel="stylesheet" type="text/css" >
<script src="../../library/bootstrap/js/jquery.min.js"></script>
<script src="../../library/bootstrap/js/bootstrap.min.js"></script>
<link rel="shortcut icon" href="../../library/images/favicon.ico">
<?php

function HeadMain ($idScript='',$jsArray='',$alternative_transl=false,$cssArray='')
{
  /* - In $idScript si deve passare l'id dell'array submenu (m2) in menu.language.php (per mettere nel tag <TITLE> )
             oltre che il nome del modulo anche quello dello script tradotto
     - In $jsArray di devono passare i nomi dei file javascript che si vogliono caricare e presenti nella directory 'js'
     */
  if (is_array($jsArray)){
      foreach ($jsArray as $v){
              echo "\n<script type=\"text/javascript\" src=\"../../js/".$v.".js\"></script>";
      }
  }
  if (is_array($cssArray)){
    foreach ($cssArray as $v){
      echo "\n<link rel=\"stylesheet\" type=\"text/css\" href=\"../../modules/".$v."\">";
    }
  }
  global $module,$admin_aziend,$radix,$scriptname;
  $result = getAccessRights($_SESSION['Login'],$_SESSION['enterprise_id']);
  if (gaz_dbi_num_rows($result) > 0) {
     // creo l'array associativo per la generazione del menu con JSCookMenu
     $ctrl_m1=0;
     $ctrl_m2=0;
     $ctrl_m3=0;
     $menuArray = array();
     $transl = array();
     while ($row = gaz_dbi_fetch_array($result)) {
        if ($row['access'] == 3 ){
             if ($ctrl_m1!=$row['m1_id']) {
                require("../../modules/".$row['name']."/menu.".$admin_aziend['lang'].".php");
             }
             if ($row['name'] == $module) {
               $row['weight']=0;
                 if ($row['m3_link'] == $scriptname) {
                     $title_from_menu = $transl[$row['name']]['m3'][$row['m3_trkey']][0];
                 }
                 if ($ctrl_m2 != $row['m2_id'] and $ctrl_m1 != $row['m1_id']) {
                    require("../../modules/".$row['name']."/lang.".$admin_aziend['lang'].".php");
                    if (isset($strScript[$scriptname])){ // se è stato tradotto lo script lo ritorno al chiamante
                          $translated_script=$strScript[$scriptname];
                          if (isset($translated_script['title'])) {
                             $title_from_menu = $translated_script['title'];
                          }
                    }
                }
             }
             if (isset($row['m3_id']) and $row['m3_id']>0) { // è un menu3
                if ($ctrl_m2 != $row['m2_id'] and $ctrl_m1 != $row['m1_id']) { // è pure il primo di menu2 e menu1
                    $menuArray[$row['weight']] = array('link'=>'../'.$row['name'].'/'.$row['link'],'icon'=>'../'.$row['name'].'/'.$row['icon'],'name'=>$transl[$row['name']]['name'],'title'=>$transl[$row['name']]['title'],'class'=>$row['class']);
                    $menuArray[$row['weight']][$row['m2_weight']] = array('link'=>'../'.$row['name'].'/'.$row['m2_link'],'icon'=>'../'.$row['name'].'/'.$row['m2_icon'],'name'=>$transl[$row['name']]['m2'][$row['m2_trkey']][1],'title'=>$transl[$row['name']]['m2'][$row['m2_trkey']][0],'class'=>$row['m2_class']);
                } elseif ($ctrl_m2 != $row['m2_id']) { // è solo il primo di menu2
                    $menuArray[$row['weight']][$row['m2_weight']] = array('link'=>'../'.$row['name'].'/'.$row['m2_link'],'icon'=>'../'.$row['name'].'/'.$row['m2_icon'],'name'=>$transl[$row['name']]['m2'][$row['m2_trkey']][1],'title'=>$transl[$row['name']]['m2'][$row['m2_trkey']][0],'class'=>$row['m2_class']);
                }
                $menuArray[$row['weight']][$row['m2_weight']][$row['m3_weight']] = array('link'=>'../'.$row['name'].'/'.$row['m3_link'],'icon'=>'../'.$row['name'].'/'.$row['m3_icon'],'name'=>$transl[$row['name']]['m3'][$row['m3_trkey']][1],'title'=>$transl[$row['name']]['m3'][$row['m3_trkey']][0],'class'=>$row['m3_class']);
             } elseif ($ctrl_m1!=$row['m1_id']) { // è il primo di menu2
                $menuArray[$row['weight']] = array('link'=>'../'.$row['name'].'/'.$row['link'],'icon'=>'../'.$row['name'].'/'.$row['icon'],'name'=>$transl[$row['name']]['name'],'title'=>$transl[$row['name']]['title'],'class'=>$row['class']);
                $menuArray[$row['weight']][$row['m2_weight']] = array('link'=>'../'.$row['name'].'/'.$row['m2_link'],'icon'=>'../'.$row['name'].'/'.$row['m2_icon'],'name'=>$transl[$row['name']]['m2'][$row['m2_trkey']][1],'title'=>$transl[$row['name']]['m2'][$row['m2_trkey']][0],'class'=>$row['m2_class']);
             } else { // non è il primo di menu2
                $menuArray[$row['weight']][$row['m2_weight']] = array('link'=>'../'.$row['name'].'/'.$row['m2_link'],'icon'=>'../'.$row['name'].'/'.$row['m2_icon'],'name'=>$transl[$row['name']]['m2'][$row['m2_trkey']][1],'title'=>$transl[$row['name']]['m2'][$row['m2_trkey']][0],'class'=>$row['m2_class']);
             }
        }
        $ctrl_m1=$row['m1_id'];
        $ctrl_m2=$row['m2_id'];
        $ctrl_m3=$row['m3_id'];
     }
     ksort($menuArray);
     
	 /*   Fine creazione array per JSCookMenu.
     In $menuArray c'e' la lista del menu
     con index '0' il modulo corrente,
     è una matrice a 3 dimensioni ,
     questo serve per poter creare un array in JS
     compatibile con le specifiche di JSCookMenu,
     la funzione createGazieJSCM serve per creare un
     array con il menu corrente orizzontale , si potrebbero creare
     altre forme di menu modificando questa funzione. */
     echo "\n<title>".$menuArray[0]['title']."&raquo;".$admin_aziend['ragso1'];
     if (!empty($idScript)) {
        if (is_array($idScript)) { // $idScript dev'essere un array con index [0] per il numero di menu e index[1] per l'id dello script
            if ($idScript[0] == 2) {
                echo "&raquo;".$transl[$module]['m2'][$idScript[1]][0];
            } elseif ($idScript[0] == 3){
                echo "&raquo;".$transl[$module]['m3'][$idScript[1]][0];
            }
        } elseif ($idScript > 0) {
            echo "&raquo;".$transl[$module]['m3'][$idScript][0];
        }
     } elseif (isset($title_from_menu)) {
            echo "&raquo;".$title_from_menu;
     }
     echo "</title>\n";
     echo "</head>\n";
     echo "<body>\n";

    // cambia il tipo di menu
    $tipomenu = substr ( $admin_aziend['style'], 0, -4 );
	if ( file_exists( "../../library/style/header_menu_".$tipomenu.".php" ) ) {
		require("../../library/style/header_menu_".$tipomenu.".php");
	} else {
		require("../../library/style/header_menu_default.php");
	}
  }
  if (!isset($translated_script)){
     if ($alternative_transl){ // se e' stato passato il nome dello script sul quale mi devo basare per la traduzione
        $translated_script=$strScript[$alternative_transl.'.php'];
     } else {
        $translated_script=array($module);
     }
  }
  require("../../language/".$admin_aziend['lang']."/menu.inc.php");
  echo '<script type="text/javascript">
     countclick = 0;
     function chkSubmit() {
        if(countclick > 0) {
            alert("'.$strCommon['wait_al'].'");
            document.getElementById(\'preventDuplicate\').disabled=true;
            return false;
        } else {
            var alPre = document.getElementById(\'confirmSubmit\').value.toString();
            if (alPre) {
                var conf = confirm (alPre);
                if (!conf) {
                    document.getElementById(\'preventDuplicate\').disabled=true;
                    return true;
                }
            }
            countclick++;
            document.getElementById(\'preventDuplicate\').hidden=true;
            return true;
        }
     }
     </script>';
  return ($strCommon+$translated_script);
}
?>