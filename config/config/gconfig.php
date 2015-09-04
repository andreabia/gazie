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
if (isset($_SERVER['SCRIPT_FILENAME']) && (str_replace('\\','/',__FILE__) == $_SERVER['SCRIPT_FILENAME'])) {
    exit('Accesso diretto non consentito') ;
}
//versione software
$versSw = '6.4';

//nome DBMS usato per la libreria specifica (MySQL=mysql.lib, SQLite=sqlite.lib, ecc)
//per il momento disponibile solo la libreria mysql.lib
$NomeDB = "mysqli";

////////////////////////////////////////////////////////////////////////
//
// Parametri di accesso: server, db, utente, passwd
//
////////////////////////////////////////////////////////////////////////
//
// Server MySQL. Si può specificare anche la porta per connettersi a
// MySQL, per esempio:
//
// $Host = "mysql.2freehosting.com:3306";
//
$Host     = "localhost";
//
// Nome della base di dati a cui ci si connette.
//
$Database = "gaziedev";
//
// Utente della base di dati che ha il permesso di accedervi con tutti
// i privilegi necessari.
//
$User     = "root";
//
// Parola d'ordine necessaria per accedere alla base di dati
// in qualità di utente $User.
//
$Password = "";
//
// Prefisso delle tabelle di Gazie.
//
// ATTENZIONE: il prefisso delle tabelle predefinito è "gaz". Eventualmente, si
// possono usare altri prefissi, ma composti sempre dai primi tre caratteri
// "gaz" e seguiti da un massimo di nove caratteri, costituiti da lettere
// minuscole e cifre numeriche. Per esempio, "gaz123" è valido, mentre "gaga1"
// o "gaz_123" non sono validi.
//
$table_prefix = "gaz";
//
// Utente proposto inizialmente per l'accesso a Gazie. Se non si vuole
// suggerire alcunché, è sufficiente assegnare la stringa vuota.
//
$default_user = "amministratore";
//
// Fuso orario, per la rappresentazione corretta delle date, indipendentemente
// dalla collocazione del server HTTP+PHP. MA NON FUNZIONA, perché MySQL aggiorna
// in modo indipendente le date di accesso alle tabelle.
//
$Timezone = "Europe/Rome";
//
// È ammesso inviare messaggi di posta elettronica?
//
$email_enabled = TRUE;
//
// Testo da aggiungere eventualmente ai messaggi di posta elettronica, sistematicamente,
// per qualche motivo.
//
$email_disclaimer = "";
//
// Gazie utilizza la funzione PHP set_time_limit() per consentire il completamento
// di elaborazioni che richiedono più tempo del normale.
// In condizioni normali, la variabile $disable_set_time_limit deve corrispondere
// a FALSE. La modifica del valore a TRUE serve solo in situazioni eccezionali,
// per esempio quando si vuole installare GAzie presso un servizio che vieta
// l'uso della funzione set_time_limit(), sapendo però che ciò pregiudica il funzionamento
// corretto di GAzie.
//
$disable_set_time_limit = FALSE;
//
// Se il servente HTTP-PHP non ha una configurazione locale corretta,
// questa può essere impostata qui, espressamente.
//
$gazie_locale = "";
//
// Le seguenti definizioni assegnano il percorso delle directory che devono essere scrivibili
// dal web server.
//
// Directory usata da modules/root/retrieve.php
//
define('DATA_DIR', '../../data/');
//
// Directory usata dal modulo tcpdf
//
define('K_PATH_CACHE', '../../library/tcpdf/cache/');

////////////////////////////////////////////////////////////////////////


// definisce il nome della sessione ma solo in caso di uso dei domini di livello superiore al secondo, in
// caso di installazione su domini di secondo livello viene attribuito automaticamente
// il nome del direttorio di installazione che normalmente e', appunto:  gazie
define('_SESSION_NAME','gazie');

//url di default per l'aggiornamento di GAzie
$update_URI_files= "http://sourceforge.net/projects/gazie";


?>