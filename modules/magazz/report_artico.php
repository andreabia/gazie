<?php
/*
 --------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2015 - Antonio De Vincentiis Montesilvano (PE)
         (http://www.devincentiis.it)
           <http://gazie.sourceforge.net>
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
if ($admin_aziend['decimal_quantity']>4){
	$admin_aziend['decimal_quantity']=4;
}

function getLastDoc($item_code)
   {
      global $gTables;
      $rs=false;
      $rs_last_doc = gaz_dbi_dyn_query("*", $gTables['files'], " item_ref ='".$item_code."'",'id_doc DESC',0,1);
      $last_doc = gaz_dbi_fetch_array($rs_last_doc);
      // se e' il primo documento dell'anno, resetto il contatore
      if ($last_doc) {
         $rs=$last_doc;
      }
      return $rs;
   }

$search_field_Array = array('C'=>array('codice','Codice'), 'D'=>array('descri','Descrizione'),'B'=>array('barcode','Codice a barre'));
//
require("../../library/include/header.php");
$script_transl=HeadMain();
if (isset($_GET['auxil'])) {
   $auxil = $_GET['auxil'];
}
if (isset($_GET['all'])) {
   $auxil = "&all=yes";
   $where = $search_field_Array[$admin_aziend['artsea']][0]." LIKE '%'";
   $passo = 100000;
} else {
   if (isset($_GET['auxil'])) {
		$where = $search_field_Array[$admin_aziend['artsea']][0]." LIKE '%".addslashes($_GET['auxil'])."%'";
		$where .= " or codice like '%".addslashes($_GET['auxil'])."%'";
		$where .= " or barcode = '".$_GET['auxil']."'";
   }
}

if (!isset($_GET['auxil'])) {
   $auxil = "";
   $where = $search_field_Array[$admin_aziend['artsea']][0]." LIKE '%$auxil%'";
}
?>
<div align="center" class="FacetFormHeaderFont">Articoli</div>
<form method="GET">
<table class="Tlarge">
<tr>
<td class="FacetFieldCaptionTD" colspan="2">
	<input class="input-xs form-control" placeholder="codice,descrizione,barcode" type="text" name="auxil" value="<?php if ($auxil != "&all=yes") echo $auxil; ?>" tabindex="1">
</td>
<td><input class="btn btn-xs btn-default" type="submit" name="search" value="Cerca" tabindex="1" onClick="javascript:document.report.all.value=1;"></td>
<td>
<input class="btn btn-xs btn-default" type="submit" name="all" value="Mostra tutti" onClick="javascript:document.report.all.value=1;">
</td>
</tr>
<?php
$result = gaz_dbi_dyn_query ('*', $gTables['artico'], $where, $orderby, $limit, $passo);
// creo l'array (header => campi) per l'ordinamento dei record
$headers_artico = array  (
              "Codice" => "codice",
              "Descrizione" => "descri",
              "Doc." => "",
              "Categoria<br>merceologica" => "catmer",
              "U.M." => "unimis",
              "Prezzo 1" => "preve1",
              "Prezzo<br>acquisto" => "preacq",
              "Giacenza" => "");
if ($admin_aziend['conmag']>0) {
   $headers_artico = array_merge($headers_artico,array(
              "Visualizza<br>e/o stampa"=>'',
              "Barcode" => "barcode",
              "Duplica" => "",
              "Cancella" => ""
              ));
} else {
   $headers_artico = array_merge ( $headers_artico,array(
              "Barcode" => "barcode",
              "Duplica" => "",
              "Cancella" => ""
              ));
}

$linkHeaders = new linkHeaders($headers_artico);
$linkHeaders -> output();
$recordnav = new recordnav($gTables['artico'], $where, $limit, $passo);
$recordnav -> output();
$gForm = new magazzForm();
while ($r = gaz_dbi_fetch_array($result)) {
       gaz_set_time_limit (30);
       $lastdoc=getLastDoc($r["codice"]);
       $mv=$gForm->getStockValue(false,$r['codice']); 
       $magval=array_pop($mv);
       $image_src = '';
       if((!empty($r["image"]) || (file_exists("../../data/files/fotoart/".$r["codice"].".gif" )))){
		if ( !empty( $r["image"] ) ) {
			$image_src = '<img border="1px" height="20" src="../root/view.php?table=artico&value='.$r['codice'].'" />';
			$boxover = "title=\"cssbody=[FacetInput] cssheader=[FacetButton] header=[".$r['annota']."] body=[<img src='../root/view.php?table=artico&value=".$r['codice']."'>] fade=[on] fadespeed=[0.03] \"";
		} elseif (file_exists("../../data/files/fotoart/".$r["codice"].".gif" )) {
			$image_src = '<img border="1px" height="20" src="../../data/files/fotoart/'.$r["codice"].'.gif" />';
			$boxover = "title=\"cssbody=[FacetInput] cssheader=[FacetButton] header=[".$r['annota']."] body=[<img width='50%' height='50%' src='../../data/files/fotoart/".$r['codice'].".gif'>] fade=[on] fadespeed=[0.03] \"";
		} else {
			$image_src = "";
		}
       } else {
            $boxover = "title=\"cssbody=[FacetInput] cssheader=[FacetButton] header=[".$r['annota']."]  fade=[on] fadespeed=[0.03] \"";
       }
       $iva = gaz_dbi_get_row($gTables['aliiva'],"codice",$r["aliiva"]);
       echo "<tr>";
       ?>
			<td class="FacetDataTD" style="min-width:80px">
				<div>		
					<div style="float:left;vertical-align: middle;">
						<a class="btn btn-xs btn-block btn-default " href="admin_artico.php?codice=<?php echo $r["codice"]; ?>&Update">
						<?php echo $r["codice"]; ?>
						</a>
					</div>
				</div>
			</td>
		 <?php
       echo "<td class=\"FacetDataTD\" $boxover>".$r["descri"]." </td>";
       echo "<td class=\"FacetDataTD\" align=\"center\" title=\"\">";
       if ($lastdoc){
         echo "<a href=\"../root/retrieve.php?id_doc=".$lastdoc["id_doc"]."\"><img src=\"../../library/images/doc.png\" title=\"Ultimo certificato e/o documentazione disponibile\" border=\"0\"></a>";
       }
       echo "</td>\n";
       echo "<td class=\"FacetDataTD\" align=\"center\">".$r["catmer"]." </td>";
       echo "<td class=\"FacetDataTD\" align=\"center\">".$r["unimis"]." </td>";
       echo "<td class=\"FacetDataTD\" align=\"right\">".number_format($r["preve1"],$admin_aziend['decimal_price'],',','.')." </td>";
       echo "<td class=\"FacetDataTD\" align=\"right\">".number_format($r["preacq"],$admin_aziend['decimal_price'],',','.')." </td>";
       echo "<td class=\"FacetDataTD\" align=\"right\" title=\"".$admin_aziend['symbol']." ".$magval['v_g']."\">".number_format($magval['q_g'],$admin_aziend['decimal_quantity'],',','.')." </td>";
       if ($admin_aziend['conmag']>0) {
          echo "<td class=\"FacetDataTD\" align=\"center\" title=\"Visualizza e/o stampa la scheda di magazzino\">
               <a class=\"btn btn-xs btn-default\" href=\"../magazz/select_schart.php?di=0101".date('Y')."&df=".date('dmY')."&id=".$r['codice']."\">
               <i class=\"glyphicon glyphicon-check\"></i><i class=\"glyphicon glyphicon-print\"></i></a></td>";
       }
       echo "<td class=\"FacetDataTD\" align=\"center\" title=\"Stampa Codici a Barre\"><a class=\"btn btn-xs btn-default\" href=\"stampa_barcode.php?code=".$r["codice"]."\"><i class=\"glyphicon glyphicon-barcode\"></i></a></td>"; //<img src=\"../../library/images/barcode.png\" border=\"0\"><br />".$r['barcode']."
       echo "<td class=\"FacetDataTD\" align=\"center\" title=\"Duplica articolo in (".$r["codice"]."_2)\"><a class=\"btn btn-xs btn-default\" href=\"clone_artico.php?codice=".$r["codice"]."\"><i class=\"glyphicon glyphicon-export\"></i></a></td>";
       echo "<td class=\"FacetDataTD\" align=\"center\"><a class=\"btn btn-xs btn-default btn-elimina\" href=\"delete_artico.php?codice=".$r["codice"]."\"><i class=\"glyphicon glyphicon-remove\"></i></a></td>";
       echo "</tr>";
}
?>
</form>
</table>
</body>
</html>
<script src="../../js/boxover/boxover.js"></script>