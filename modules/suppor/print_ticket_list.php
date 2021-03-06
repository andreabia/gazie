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
$vrag = "";

require("./lang.".$admin_aziend['lang'].".php");
//$script_transl = $strScript["select_partner_status.php"];

if ( isset($_GET['auxil']) ) {
   $auxil = $_GET['auxil'];
   $where = " ".$gTables['anagra'].".ragso1 like '%$auxil%'";
} else {
   $auxil = "";
}

if ( isset($_GET['flt_passo']) ) {
	$passo = $_GET['flt_passo'];
} else {
	$passo = "";
}
if ( isset($_GET['flt_cliente']) && $_GET['flt_cliente']!="tutti" ) {
	$where .= " and ".$gTables['assist'].".clfoco = '".$_GET['flt_cliente']."'";
}
if ( isset($_GET['flt_stato']) ) {
	if ( $_GET['flt_stato']!="tutti" ) {
		if ( $_GET['flt_stato']=="nochiusi" ) {
			$where .= " and stato != 'chiuso' and stato != 'contratto' ";
		} else {
			$where .= " and stato = '".$_GET['flt_stato']."'";
		}
	}
}
if (!ini_get('safe_mode')){ //se me lo posso permettere...
    ini_set('memory_limit','128M');
    gaz_set_time_limit (0);
}

require("../../config/templates/report_template.php");
$luogo_data=$admin_aziend['citspe'].", l� ".ucwords(strftime("%d %B %Y", mktime (0,0,0,date("m"),date("d"),date("Y"))));
$item_head = array('top'=>array(array('lun' => 80,'nam'=>'Descrizione'),
                                array('lun' => 25,'nam'=>'Numero Conto')
                               )
                   );
$acc=array();
/*foreach($script_transl['header'] as $k=>$v){
    $acc[]=$k;
}*/
$title = array('luogo_data'=>$luogo_data,
               'title'=>'RESOCONTO INTERVENTI DI ASSISTENZA TECNICA',
					'hile' => array()
              );
$pdf = new Report_template();
$pdf->setVars($admin_aziend,$title);
$pdf->setFooterMargin(22);
$pdf->setTopMargin(34);
$pdf->setRiporti('');
$pdf->AddPage();

$result = gaz_dbi_dyn_query($gTables['assist'].".*,
		".$gTables['anagra'].".ragso1, ".$gTables['anagra'].".ragso2,
		".$gTables['anagra'].".telefo, ".$gTables['anagra'].".fax,
		".$gTables['anagra'].".cell, ".$gTables['anagra'].".e_mail
		", $gTables['assist'].
		" LEFT JOIN ".$gTables['clfoco']." ON ".$gTables['assist'].".clfoco = ".$gTables['clfoco'].".codice". 
		" LEFT JOIN ".$gTables['anagra'].' ON '.$gTables['clfoco'].'.id_anagra = '.$gTables['anagra'].'.id',
		$where, "clfoco, DATA ASC", $limit, $passo);

$totale_ore = -1;
while ($row = gaz_dbi_fetch_array($result)) {
	if ( $row["ragso1"] != $vrag ) {	
		$pdf->SetFont('helvetica','B',10);
		$pdf->SetFillColor(255,255,255);
		if ( $totale_ore != -1 ) {	
			$pdf->Cell(158,5,'Totale Ore :','LTB',0,'R',1,'',1);
			$pdf->Cell(12,5,gaz_format_number($totale_ore),1,1,'R',1);
		}
		$totale_ore ++;
		
		//$pdf->SetFont('helvetica','B',10);
      //$pdf->SetFillColor(255,255,255);
      $pdf->Ln(2);		
	
		if ( $row['fax'] != "" ) $fax = "fax: ".$row['fax'];
		else $fax = "";
		if ( $row['cell'] != "" ) $mob = "mob:".$row['cell'];
		else $mob = "";
		if ( $row['telefo'] != "" ) $tel = "tel:".$row['telefo'];
		else $tel = "";
		if ( $row['e_mail'] != "" ) $email = $row['e_mail'];
		else $email = "";
      $pdf->Cell(188,6,$row['ragso1']." ".$row['ragso2']." ".$tel." ".$fax." ".$mob." ".$email,1,1,'',1,'',1);
		$vrag = $row["ragso1"];
		$totale_ore = 0;
	}
   $pdf->SetFont('helvetica','',9);

	$pdf->Cell(12,5,$row['id'],'LTB',0,'R',1,'',1);
	$pdf->Cell(20,5,gaz_format_date($row['data']),1,0,'C',1,'',1);
   $pdf->Cell(62,5,$row['oggetto'],1,0,'L',1);

   $pdf->Cell(64,5,substr($row['descrizione'],0,50),1,0,'L',1);
	$pdf->Cell(12,5,gaz_format_number($row['ore']),1,0,'R',1);
	$totale_ore += $row['ore'];	
   $pdf->Cell(18,5,$row['stato'],1,1,'R',1);
}
$pdf->SetFont('helvetica','B',10);
$pdf->SetFillColor(255,255,255);
if ( $totale_ore != -1 ) {	
	$pdf->Cell(158,5,'Totale Ore :','LTB',0,'R',1,'',1);
	$pdf->Cell(12,5,gaz_format_number($totale_ore),1,1,'R',1);
}
		
$pdf->setRiporti('');
$pdf->Output();
?>