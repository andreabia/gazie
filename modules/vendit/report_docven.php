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
require("../../library/include/datlib.inc.php");
$admin_aziend=checkAdmin();
$anno = date("Y");
$cliente='';
$message = "";

function print_querytime($prev)
{
    list($usec, $sec) = explode(" ", microtime());
    $this_time= ((float)$usec + (float)$sec);
    echo round($this_time-$prev,8);
    return $this_time;
}

if (isset($_GET['auxil'])) {
   $seziva = $_GET['auxil'];
   $where = "tipdoc LIKE 'F%' AND ".$gTables['tesdoc'].".seziva = '$seziva' GROUP BY protoc, datfat";
} else {
   $seziva = "1";
   $where = "tipdoc LIKE 'F%' AND ".$gTables['tesdoc'].".seziva = '$seziva' GROUP BY protoc, datfat";
}

if (isset($_GET['protoc'])) {
   if ($_GET['protoc'] > 0) {
      $protocollo = $_GET['protoc'];
      $where = "tipdoc LIKE 'F%' AND ".$gTables['tesdoc'].".seziva = '$seziva' AND protoc = '$protocollo' GROUP BY protoc, datfat";
      $passo = 1;
   }
}  else {
   $protocollo ='';
}

if (isset($_GET['numerof'])) {
   if ($_GET['numerof'] > 0) {
      $numerof = $_GET['numerof'];
      $where = "tipdoc LIKE 'F%' AND ".$gTables['tesdoc'].".seziva = '$seziva' AND numfat = '$numerof' GROUP BY protoc, datfat";
      $passo = 1;
   }
}  else {
   $numerof ='';
}

if (isset($_GET['cliente'])) {
   if ($_GET['cliente'] <> '') {
      $cliente = $_GET['cliente'];
      $where = " tipdoc LIKE 'F%' AND ".$gTables['tesdoc'].".seziva = '$seziva' and ".$gTables['clfoco'].".descri like '%".addslashes($cliente)."%' GROUP BY protoc, datfat";
      $passo = 50;
      unset($protocollo);
      unset($numerof);
   }
}

if (isset($_GET['articolo'])) {
   if ($_GET['articolo'] <> '') {
      $articolo = $_GET['articolo'];
      $where = " tipdoc LIKE 'F%' AND ".$gTables['tesdoc'].".seziva = '$seziva' and ".$gTables['clfoco'].".descri like '%".addslashes($cliente)."%' and (".$gTables['rigdoc'].".descri like '%".$articolo."%' or ".$gTables['rigdoc'].".codart like '%".$articolo."%') GROUP BY protoc, datfat";
      $passo = 50;
      unset($protocollo);
      unset($numerof);
	  unset($cliente);
   }
}

if (isset($_GET['all'])) {
   gaz_set_time_limit (0);
   $where = "tipdoc LIKE 'F%' AND ".$gTables['tesdoc'].".seziva = '$seziva' GROUP BY protoc, datfat";
   $passo = 100000;
   unset($protocollo);
   unset($cliente);
   unset($numerof);
}

$titolo="Documenti di vendita a clienti";
require("../../library/include/header.php");
$script_transl=HeadMain(0,array('jquery/jquery-1.7.1.min',
                                  'jquery/ui/jquery.ui.core',
                                  'jquery/ui/jquery.ui.widget',
                                  'jquery/ui/jquery.ui.mouse',
                                  'jquery/ui/jquery.ui.button',
                                  'jquery/ui/jquery.ui.dialog',
                                  'jquery/ui/jquery.ui.position',
                                  'jquery/ui/jquery.ui.draggable',
                                  'jquery/ui/jquery.ui.resizable',
                                  'jquery/ui/jquery.effects.core',
                                  'jquery/ui/jquery.effects.scale',
                                  'jquery/modal_form'));
echo '<script>
$(function() {
   $( "#dialog" ).dialog({
      autoOpen: false
   });
   
   $( "#dialog1" ).dialog({
      autoOpen: false
   });

   $( "#dialog2" ).dialog({
      autoOpen: false
   });
   
});
function confirMail(link){
   tes_id = link.id.replace("doc", "");
   $.fx.speeds._default = 500;
   targetUrl = $("#doc"+tes_id).attr("url");
   //alert (targetUrl);
   $("p#mail_adrs").html($("#doc"+tes_id).attr("mail"));
   $("p#mail_attc").html($("#doc"+tes_id).attr("namedoc"));
   $( "#dialog" ).dialog({
         modal: "true",
      show: "blind",
      hide: "explode",
         buttons: {
                      " '.$script_transl['submit'].' ": function() {
                         window.location.href = targetUrl;
                      },
                      " '.$script_transl['cancel'].' ": function() {
                        $(this).dialog("close");
                      }
                  }
         });
   $("#dialog" ).dialog( "open" );
}



function confirFae(link){
   tes_id = link.id.replace("doc1", "");;
   $.fx.speeds._default = 500;
   $("p#fae1").html("numero: " + $("#doc1"+tes_id).attr("n_fatt"));
   $( "#dialog1" ).dialog({
      modal: "true",
      show: "blind",
      hide: "explode",
      buttons: {
                      " '.$script_transl['submit'].' ": function() {
                         window.location.href = link.href;
                          $(this).dialog("close");
                      },
                      " '.$script_transl['cancel'].' ": function() {
                        $(this).dialog("close");
                      }
               }
         });
   $("#dialog1" ).dialog( "open" );
}

function confirTutti(link){
   $.fx.speeds._default = 500;
   $( "#dialog2" ).dialog({
      modal: "true",
      show: "blind",
      hide: "explode",
      buttons: {
                      " '.$script_transl['submit'].' ": function() {
                          window.location.href = window.location.pathname + "?all=Mostra+tutti&auxil=1";
                          $(this).dialog("close");
                      },
                      " '.$script_transl['cancel'].' ": function() {
                        $(this).dialog("close");
                      }
               }
         });
   $("#dialog2" ).dialog( "open" );
}



</script>';
switch($admin_aziend['fatimm']) {
    case "1":
        $sezfatimm = 1;
    break;
    case "2":
        $sezfatimm = 2;
    break;
    case "3":
        $sezfatimm = 3;
    break;
    case "R":
        $sezfatimm = $seziva;
    break;
    case "U":
        $rs_ultimo = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "datemi LIKE '$anno%' AND tipdoc = 'FAI'","datfat desc",0,1);
        $ultimo = gaz_dbi_fetch_array($rs_ultimo);
        $sezfatimm = $ultimo['seziva'];
    break;
    default:
        $sezfatimm = $seziva;
}

?>
<form method="GET" >
<div id="dialog" title="<?php echo $script_transl['mail_alert0']; ?>">
      <p id="mail_alert1"><?php echo $script_transl['mail_alert1']; ?></p>
      <p class="ui-state-highlight" id="mail_adrs"></p>
      <p id="mail_alert2"><?php echo $script_transl['mail_alert2']; ?></p>
      <p class="ui-state-highlight" id="mail_attc"></p>
</div>

<div id="dialog1" title="<?php echo $script_transl['fae_alert0']; ?>">
      <p id="fae_alert1"><?php echo $script_transl['fae_alert1']; ?></p>
      <p class="ui-state-highlight" id="fae1"></p>
      <p id="fae_alert2"><?php echo $script_transl['fae_alert2']; ?></p>
      <p class="ui-state-highlight" id="fae2"></p>
</div>

<div id="dialog2" title="<?php echo $script_transl['report_alert0']; ?>">
      <p id="report_alert1"><?php echo $script_transl['report_alert1']; ?></p>
      <p class="ui-state-highlight" id="report1"></p>
</div>

<div align="center"><font class="FacetFormHeaderFont">Documenti di vendita della sezione
<select name="auxil" class="FacetSelect" onchange="this.form.submit()">
<?php
for ($sez = 1; $sez <= 3; $sez++) {
    $selected = "";
    if($seziva == $sez) {
        $selected = " selected ";
    }
    echo "<option value=\"".$sez."\"".$selected.">".$sez."</option>";
}
?>
</select></font></div>
<?php
if (!isset($_GET['field']) || ($_GET['field'] == 2) || (empty($_GET['field']))){
        $orderby = "datfat DESC, protoc DESC";
}
list($usec, $sec) = explode(' ',microtime());
$querytime = ((float)$usec + (float)$sec);
$querytime_before = $querytime;
if (isset($articolo) ) {
if ( $articolo!="" ) {
$recordnav = new recordnav($gTables['tesdoc'].' LEFT JOIN '.$gTables['clfoco'].' on '.$gTables['tesdoc'].'.clfoco = '.$gTables['clfoco'].'.codice inner join '.$gTables['rigdoc'].' on '.$gTables['rigdoc'].'.id_tes = '.$gTables['tesdoc'].'.id_tes', $where, $limit, $passo);	
} 
} else {
$recordnav = new recordnav($gTables['tesdoc'].' LEFT JOIN '.$gTables['clfoco'].' on '.$gTables['tesdoc'].'.clfoco = '.$gTables['clfoco'].'.codice', $where, $limit, $passo);
}

$recordnav -> output();
?>
<table class="Tlarge">
 <tr>
   <td class="FacetFieldCaptionTD">
		<input type="text" placeholder="Cerca Prot." class="input-xs form-control" name="protoc" value="<?php if (isset($protocollo)) echo $protocollo; ?>" tabindex="1" size="5" class="FacetInput">
   </td>
   <!--<td></td>-->
   <td class="FacetFieldCaptionTD">
		<input type="text" placeholder="Cerca Num." class="input-xs form-control" name="numerof" value="<?php if (isset($numerof)) { print $numerof;} ?>" tabindex="2" size="5">
   </td>
   <td class="FacetFieldCaptionTD"></td>
   <td colspan="1" class="FacetFieldCaptionTD">
		<input type="text" placeholder="Cerca Cliente" class="input-xs form-control" name="cliente" value="<?php if (isset($cliente)) { print $cliente;} ?>" tabindex="3" class="FacetInput">
   </td>
   <td colspan="1" class="FacetFieldCaptionTD">
		<input type="text" placeholder="Cerca Articolo" class="input-xs form-control" name="articolo" value="<?php if (isset($articolo)) { print $articolo;} ?>" tabindex="3" class="FacetInput">
   </td>
   <td class="FacetFieldCaptionTD" colspan="6">
     <input type="submit" class="btn btn-xs btn-default" name="search" value="Cerca" tabindex="1" onClick="javascript:document.report.all.value=1;">
     <input type="submit" class="btn btn-xs btn-default" name="all" value="Mostra tutti" onClick="confirTutti();return false;">
   </td>
 </tr>
<tr>
<?php
// creo l'array (header => campi) per l'ordinamento dei record
$headers_tesdoc = array  (
            "Prot." => "protoc",
            //"Tipo" => "tipdoc",
            "Numero" => "numfat",
            "Data" => "datfat",
            "Cliente" => "ragso1",
            "Status" => "",
            "Stampa" => "",
            "FAE" => "",
            "Mail" => "",
            "Origine" => "",
            "Cancella" => ""
            );
$linkHeaders = new linkHeaders($headers_tesdoc);
$linkHeaders -> output();
?>
</tr>
<?php
$rs_ultimo_documento = gaz_dbi_dyn_query("id_tes", $gTables['tesdoc'], "tipdoc LIKE 'F%' AND seziva = '$seziva'","datfat DESC, protoc DESC, id_tes",0,1);
$ultimo_documento = gaz_dbi_fetch_array($rs_ultimo_documento);
//recupero le testate in base alle scelte impostate
$result = gaz_dbi_dyn_query($gTables['rigdoc'].".*, ".$gTables['tesdoc'].".*, MAX(".$gTables['tesdoc'].".id_tes) AS reftes,".$gTables['anagra'].".fe_cod_univoco,".$gTables['anagra'].".ragso1,".$gTables['anagra'].".e_mail,".$gTables['clfoco'].".codice,".$gTables['pagame'].".tippag", $gTables['tesdoc']." LEFT JOIN ".$gTables['clfoco']." ON ".$gTables['tesdoc'].".clfoco = ".$gTables['clfoco'].".codice  LEFT JOIN ".$gTables['anagra']." ON ".$gTables['clfoco'].".id_anagra = ".$gTables['anagra'].".id  LEFT JOIN ".$gTables['pagame']." ON ".$gTables['tesdoc'].".pagame = ".$gTables['pagame'].".codice inner join ".$gTables['rigdoc']." on ".$gTables['rigdoc'].".id_tes = ".$gTables['tesdoc'].".id_tes", $where, $orderby,$limit, $passo);
$ctrl_doc = "";
$ctrl_eff = 999999;
while ($r = gaz_dbi_fetch_array($result)) {
    $modulo_fae="electronic_invoice.php?id_tes=".$r['id_tes'];
    $modulo_fae_report="report_fae_sdi.php?id_tes=".$r['id_tes'];
    $classe_btn = "btn-default";
	 if ($r["tipdoc"] == 'FAI') {
        $tipodoc="Fattura Immediata";
        $modulo="stampa_docven.php?id_tes=".$r['id_tes'];
        $modifi="admin_docven.php?Update&id_tes=".$r['id_tes'];
    } elseif ($r["tipdoc"] == 'FAD') {
        $tipodoc="Fattura Differita";
		  $classe_btn = "btn-inverse";
        $modulo="stampa_docven.php?td=2&si=".$r["seziva"]."&pi=".$r['protoc']."&pf=".$r['protoc']."&di=".$r['datfat']."&df=".$r['datfat'];
        $modulo_fae="electronic_invoice.php?seziva=".$r["seziva"]."&protoc=".$r['protoc']."&year=".substr($r['datfat'],0,4);
        $modifi="";
    } elseif ($r["tipdoc"] == 'FAP') {
        $tipodoc="Parcella";
		  $classe_btn = "btn-primary";
        $modulo="stampa_docven.php?id_tes=".$r['id_tes'];
        $modifi="admin_docven.php?Update&id_tes=".$r['id_tes'];
    } elseif ($r["tipdoc"] == 'FNC') {
        $tipodoc="Nota Credito";
		  $classe_btn = "btn-danger";
        $modulo="stampa_docven.php?id_tes=".$r['id_tes'];
        $modifi="admin_docven.php?Update&id_tes=".$r['id_tes'];
    } elseif ($r["tipdoc"] == 'FND') {
        $tipodoc="Nota Debito";
		  $classe_btn = "btn-success";
        $modulo="stampa_docven.php?id_tes=".$r['id_tes'];
        $modifi="admin_docven.php?Update&id_tes=".$r['id_tes'];
    } else {
        $tipodoc="DOC.SCONOSCIUTO";
		  $classe_btn = "btn-warning";
        $modulo="stampa_docven.php?id_tes=".$r['id_tes'];
        $modifi="admin_docven.php?Update&id_tes=".$r['id_tes'];
    }
    if (sprintf('%09d',$r['protoc']).$r['datfat'] <> $ctrl_doc)    {
        $n_e=0;
        echo "<tr>";
		// Colonna protocollo
        if (! empty ($modifi)) {
           echo "<td class=\"FacetDataTD\"><a href=\"".$modifi."\" class=\"btn btn-xs ".$classe_btn." btn-edit\" title=\"Modifica ".$tipodoc." \">".$r["protoc"]."&nbsp;".$r["tipdoc"]."&nbsp;<i class=\"glyphicon glyphicon-edit\"></i></a></td>";
        } else {
           echo "<td class=\"FacetDataTD\"><button class=\"btn btn-xs ".$classe_btn." btn-edit disabled\" title=\"Per poter modificare questa ".$tipodoc." devi modificare i DdT in essa contenuti!\">".$r["protoc"]."&nbsp;".$r["tipdoc"]." &nbsp;<i class=\"glyphicon glyphicon-edit\"></i></button></td>";
        }
		// Colonna tipo documento
        //echo "<td class=\"FacetDataTD\">".$tipodoc." &nbsp;</td>";
		// Colonna numero documento
        echo "<td class=\"FacetDataTD\" align=\"center\">".$r["numfat"]." &nbsp;</td>";
		// Colonna data documento
        echo "<td class=\"FacetDataTD\" align=\"center\">".gaz_format_date($r["datfat"])." &nbsp;</td>";
		// Colonna cliente
        echo "<td class=\"FacetDataTD\"><a title=\"Dettagli cliente\" href=\"report_client.php?auxil=".$r["ragso1"]."&search=Cerca\">".$r["ragso1"]."</a>&nbsp;</td>";
		// Colonna movimenti contabili
        echo "<td class=\"FacetDataTD\" align=\"center\">";
        if ($r["id_con"] > 0) {
           echo " <a class=\"btn btn-xs btn-default btn-default\" style=\"font-size:10px;\" title=\"Modifica il movimento contabile generato da questo documento\" href=\"../contab/admin_movcon.php?id_tes=".$r["id_con"]."&Update\">Cont.".$r["id_con"]."</a> ";
        } else {
           echo " <a class=\"btn btn-xs btn-default btn-cont\" href=\"accounting_documents.php?type=F&vat_section=".$seziva."&last=".$r["protoc"]."\">Contabilizza</a>";
        }
        $effett_result = gaz_dbi_dyn_query ('*',$gTables['effett'],"id_doc = ".$r["reftes"],'progre');
        while ($r_e = gaz_dbi_fetch_array ($effett_result)){
           // La fattura ha almeno un effetto emesso
           $n_e++;
           if ($r_e["tipeff"] == "B") {
                        echo " <a class=\"btn btn-xs btn-default btn-riba\" style=\"font-size:10px;\" title=\"Visualizza la ricevuta bancaria generata per il regolamento della fattura\" href=\"stampa_effett.php?id_tes=".$r_e["id_tes"]."\">";
                        echo "RiBa".$r_e["progre"];
                        echo "</a>";
           } elseif ($r_e["tipeff"] == "T")  {
                        echo " <a class=\"btn btn-xs btn-default btn-cambiale\" style=\"font-size:10px;\" title=\"Visualizza la cambiale tratta generata per il regolamento della fattura\" href=\"stampa_effett.php?id_tes=".$r_e["id_tes"]."\">";
                        echo "Tratta".$r_e["progre"];
                        echo "</a>";
           } elseif ($r_e["tipeff"] == "V")  {
                        echo " <a class=\"btn btn-xs btn-default btn-avviso\" style=\"font-size:10px;\" title=\"Visualizza il pagamento mediante avviso generato per il regolamento della fattura\" href=\"stampa_effett.php?id_tes=".$r_e["id_tes"]."\">";
                        echo "MAV".$r_e["progre"];
                        echo "</a>";
           }  else {
                        echo " <a class=\"btn btn-xs btn-default btn-effetto\" style=\"font-size:10px;\" title=\"Visualizza l'effetto\" href=\"stampa_effett.php?id_tes=".$r_e["id_tes"]."\">";
                        echo $r_e["tipeff"].$r_e["progre"];
                        echo "</a>";
           }
        }
        if ($n_e==0 && ($r["tippag"]=='B' || $r["tippag"]=='T' || $r["tippag"]=='V')) {
              echo " <a class=\"btn btn-xs btn-effetti\" title=\"Genera gli effetti previsti per il regolamento delle fatture\" href=\"genera_effett.php\"> Genera effetti</a>";
        }
        echo "</td>";
        // Colonna "Stampa"
        echo "<td class=\"FacetDataTD\" align=\"center\"><a class=\"btn btn-xs btn-default\" href=\"".$modulo."\"><i class=\"glyphicon glyphicon-print\"></i></a>";
        echo "</td>";
        
        // Colonna "Fattura elettronica"
        if (substr($r["tipdoc"],0,1)=='F'){
            if (strlen($r["fe_cod_univoco"])!=6) { // se il cliente non è un ufficio della PA tolgo il link
               $modulo_fae='';
			   echo "<td class=\"FacetDataTD\" align=\"center\"><button class=\"btn btn-xs btn-default btn-xml disabled\" title=\"Fattura elettronica non disponibile: codice ufficio univoco non presente\"><i class=\"glyphicon glyphicon-tag\"></i></button>";
               echo "</td>";
            } else {
              echo "<td class=\"FacetDataTD genera\" align=\"center\"><a class=\"btn btn-xs btn-default btn-xml\" onclick=\"confirFae(this);return false;\" id=\"doc1".$r["id_tes"]."\" n_fatt=\"".$r["numfat"]."\" target=\"_blank\" href=\"".$modulo_fae."\">xml</a>";
              //identifica le fatture inviate all'sdi           
              $where2 = " id_tes_ref = ".$r['id_tes'] . " AND (flux_status LIKE '@' OR flux_status LIKE '#' OR flux_status LIKE '@@')";
              $result2 = gaz_dbi_dyn_query ("*", $gTables['fae_flux'], $where2);
              $r2 = gaz_dbi_fetch_array($result2);   
              if ($r2 == false) {
              } elseif ($r2['flux_status']=="@" or $r2['flux_status']=="@@") {
                 echo " <a  title=\"Fattura elettronica inviata: VEDI REPORT\" class=\"FacetDataTDred\" target=\"_blank\" href=\"".$modulo_fae_report."\"> <img width=\"20px\" src=\"../../library/images/listed.png\" border=\"0\"></a>";
              } elseif ($r2['flux_status']=="#") {
                 echo " <a title=\"Fattura elettronica generata: VEDI REPORT\" target=\"_blank\" href=\"".$modulo_fae_report."\"> #<img width=\"20px\" src=\"../../library/images/listed.png\" border=\"0\"></a>";
              }   
              echo "</td>";
            }
         } else {
           echo "<td></td>";
         }
                 
        // Colonna "Mail"
        echo "<td class=\"FacetDataTD\" align=\"center\">";
			if (!empty($r["e_mail"])) {
				echo '<a class="btn btn-xs btn-default btn-email" onclick="confirMail(this);return false;" id="doc'.$r["id_tes"].'" url="'.$modulo.'&dest=E" href="#" title="Mailto: '.$r["e_mail"].'"
            mail="'.$r["e_mail"].'" namedoc="'.$tipodoc.' n.'.$r["numfat"].' del '.gaz_format_date($r["datfat"]).'"><i class="glyphicon glyphicon-envelope"></i></a>';
			} else {
				echo '<a title="Non hai memorizzato l\'email per questo cliente, inseriscila ora" href="admin_client.php?codice='.substr($r["codice"],3).'&Update#email"><i class="glyphicon glyphicon-edit"></i></a>';
			}		  
        echo "</td>";
        // Colonna "Origine"
        if ($r["tipdoc"]=='FAD'){
           $ddt_result = gaz_dbi_dyn_query ('*',$gTables['tesdoc'],"tipdoc = '".$r["tipdoc"]."' AND numfat = ".$r["numfat"]." AND datfat = '".$r["datfat"]."'",'datemi DESC');   
		   echo "<td class=\"FacetDataTD\" align=\"center\">";
           while ($r_d = gaz_dbi_fetch_array ($ddt_result)){
             echo " <a class=\"btn btn-xs btn-default btn-ddt\" title=\"Visualizza il DdT\" href=\"stampa_docven.php?id_tes=".$r_d['id_tes']."&template=DDT\" style=\"font-size:10px;\"><i class=\"glyphicon glyphicon-plane\"></i>&nbsp;DdT".$r_d['numdoc']."</a>\n";
           }		   
           echo "</td>";
        } elseif($r["id_contract"]>0) {
           $con_result = gaz_dbi_dyn_query ('*',$gTables['contract'],"id_contract = ".$r["id_contract"],'conclusion_date DESC');
           echo "<td class=\"FacetDataTD\" align=\"center\">";
           while ($r_d = gaz_dbi_fetch_array ($con_result)){
             echo " <a class=\"btn btn-xs btn-default btn-contr\" title=\"Visualizza il contratto\" href=\"print_contract.php?id_contract=".$r_d['id_contract']."\" style=\"font-size:10px;\"><i class=\"glyphicon glyphicon-list-alt\"></i>&nbsp;Contr.".$r_d['doc_number']."</a>\n";
           }
           echo "</td>";
        } else {
           echo "<td class=\"FacetDataTD\"></td>";
        }
        // Colonna "Cancella"
        echo "<td class=\"FacetDataTD\" align=\"center\">";
        if ($ultimo_documento['id_tes'] == $r["id_tes"] ) {
           // Permette di cancellare il documento.
           if ($r["id_con"] > 0) {
               echo "<a class=\"btn btn-xs btn-default btn-elimina\" title=\"Cancella il documento e la registrazione contabile relativa\" href=\"delete_docven.php?seziva=".$r["seziva"]."&protoc=".$r['protoc']."&anno=".substr($r["datfat"],0,4)."\"><i class=\"glyphicon glyphicon-remove\"></i></a>";
           } else {
               echo "<a class=\"btn btn-xs btn-default btn-elimina\" title=\"Cancella il documento\" href=\"delete_docven.php?seziva=".$r["seziva"]."&protoc=".$r['protoc']."&anno=".substr($r["datfat"],0,4)."\"><i class=\"glyphicon glyphicon-remove\"></i></a>";
           }
        } else {
           echo "<button title=\"Per garantire la sequenza corretta della numerazione, non &egrave; possibile cancellare un documento diverso dall'ultimo\" class=\"btn btn-xs btn-default btn-elimina disabled\"><i class=\"glyphicon glyphicon-remove\"></i></button>";
        }
        echo "</td>";
/*        echo "<td class=\"FacetDataTD\" align=\"right\">";
        $querytime=print_querytime($querytime);
        echo "</td>";*/
        echo "</tr>\n";
		if ( isset($articolo) ) 
			if ( $articolo!="" ) {
		echo "<tr>";
		echo "<td>&nbsp;</td>";
		echo "<td colspan='3' class='FacetDataTD'>".$r['codart']." ";
		echo $r['descri']." ";
		echo $r['unimis']." ".$r['quanti']."</td>";
		echo "</tr>";
		}
    }
    $ctrl_doc = sprintf('%09d',$r['protoc']).$r['datfat'];
}
echo '<tr><td class="FacetFieldCaptionTD" colspan="10" align="right">Querytime: ';
print_querytime($querytime);
echo ' sec.</td></tr>';
?>
</table>
</form>
</body>
</html>