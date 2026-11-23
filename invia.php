<?php
/*
 * Riceve il modulo di contatto della home e lo invia a info@laurapasquali.it
 * tramite SMTP autenticato (PHPMailer).
 *
 * Esiti:
 * - invio riuscito, oppure bot riconosciuto  -> grazie.html
 * - dati non validi, limite superato, errore -> la pagina del modulo con ?errore=1
 *   (home o contatti, in base al campo nascosto "pagina")
 *
 * Nei log scrivo solo il tipo di errore, mai i dati di chi scrive.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

const PAGINA_GRAZIE = 'grazie.html';

// pagine di errore ammesse: il campo "pagina" sceglie solo tra queste due,
// non viene mai usato direttamente come indirizzo
const PAGINE_ERRORE = [
    'home'     => 'index.html?errore=1#contatti',
    'contatti' => 'contatti.html?errore=1',
];

const MAX_INVII_ORA = 5;
const SECONDI_MINIMI = 3;

// un livello sopra public_html, anche se invia.php sta in una sottocartella di prova
$cartella_privata = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\') . '/../';
define('FILE_TENTATIVI', $cartella_privata . 'modulo-tentativi.json');
define('FILE_CONFIG', $cartella_privata . 'config-mail.php');

// qualsiasi valore diverso da "contatti" vale come "home"
define('PAGINA_ERRORE', PAGINE_ERRORE[($_POST['pagina'] ?? '') === 'contatti' ? 'contatti' : 'home']);

function vai_a(string $pagina): void
{
    header('Location: ' . $pagina, true, 303);
    exit;
}

// toglie a capo e caratteri di controllo dai campi brevi (evita l'iniezione di intestazioni)
function pulisci_breve(string $testo): string
{
    return trim(preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $testo) ?? '');
}

// nel messaggio lascio gli a capo ma tolgo gli altri caratteri di controllo
function pulisci_messaggio(string $testo): string
{
    $testo = str_replace(["\r\n", "\r"], "\n", $testo);
    return trim(preg_replace('/[\x00-\x08\x0B-\x1F\x7F]+/u', '', $testo) ?? '');
}

function campo(string $nome): string
{
    $valore = $_POST[$nome] ?? '';
    return is_string($valore) ? $valore : '';
}

/*
 * Limite: massimo MAX_INVII_ORA invii all'ora dallo stesso IP.
 * Salvo un'impronta (hash) dell'IP, non l'IP in chiaro, e cancello i dati più vecchi di un'ora.
 * Restituisce true se l'invio è consentito (e lo registra).
 */
function invio_consentito(): bool
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'sconosciuto';
    $chiave = hash('sha256', $ip . '|' . __DIR__);
    $adesso = time();

    $file = @fopen(FILE_TENTATIVI, 'c+');
    if ($file === false) {
        // se il file non si apre non blocco i messaggi veri: lo segnalo nel log
        error_log('Modulo contatti: impossibile aprire il file dei tentativi');
        return true;
    }

    flock($file, LOCK_EX);
    $dati = json_decode(stream_get_contents($file) ?: '[]', true);
    if (!is_array($dati)) {
        $dati = [];
    }

    // tengo solo i tentativi dell'ultima ora
    foreach ($dati as $ip_hash => $momenti) {
        $recenti = array_values(array_filter((array) $momenti, fn($t) => is_int($t) && $t > $adesso - 3600));
        if ($recenti) {
            $dati[$ip_hash] = $recenti;
        } else {
            unset($dati[$ip_hash]);
        }
    }

    $consentito = count($dati[$chiave] ?? []) < MAX_INVII_ORA;
    if ($consentito) {
        $dati[$chiave][] = $adesso;
    }

    ftruncate($file, 0);
    rewind($file);
    fwrite($file, json_encode($dati));
    fflush($file);
    flock($file, LOCK_UN);
    fclose($file);

    return $consentito;
}


// ---- 1. solo richieste POST ----
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    vai_a('index.html');
}

// ---- 2. antispam: honeypot e tempo di compilazione ----
// il campo sito_web è nascosto: una persona non lo vede e lo lascia vuoto
if (campo('sito_web') !== '') {
    vai_a(PAGINA_GRAZIE);
}

// momento di caricamento della pagina, scritto da uno script (millisecondi)
$caricamento = campo('caricato_il');
if (!ctype_digit($caricamento)) {
    vai_a(PAGINA_GRAZIE);
}
$secondi = time() - intdiv((int) $caricamento, 1000);
// sotto zero = orologio di chi scrive avanti rispetto al server: non lo considero un bot
if ($secondi >= 0 && $secondi < SECONDI_MINIMI) {
    vai_a(PAGINA_GRAZIE);
}

// ---- 3. limite di invii per IP ----
if (!invio_consentito()) {
    vai_a(PAGINA_ERRORE);
}

// ---- 4. validazione ----
$nome      = pulisci_breve(campo('nome'));
$email     = pulisci_breve(campo('email'));
$attivita  = pulisci_breve(campo('attivita'));
$sito      = pulisci_breve(campo('sito'));
$messaggio = pulisci_messaggio(campo('messaggio'));
$privacy   = campo('privacy') !== '';

$valido = $nome !== ''
    && $email !== ''
    && $messaggio !== ''
    && $privacy
    && mb_strlen($nome) <= 100
    && mb_strlen($email) <= 150
    && mb_strlen($messaggio) <= 5000
    && mb_strlen($attivita) <= 150
    && mb_strlen($sito) <= 200
    && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;

if (!$valido) {
    vai_a(PAGINA_ERRORE);
}

// ---- 5. configurazione e PHPMailer ----
if (!is_file(FILE_CONFIG)) {
    error_log('Modulo contatti: config-mail.php non trovato sopra la document root');
    vai_a(PAGINA_ERRORE);
}
$config = require FILE_CONFIG;

require __DIR__ . '/phpmailer/Exception.php';
require __DIR__ . '/phpmailer/PHPMailer.php';
require __DIR__ . '/phpmailer/SMTP.php';

// ---- 6. invio ----
date_default_timezone_set('Europe/Rome');

$corpo = "Nuova richiesta dal modulo di contatto di laurapasquali.it\n\n"
    . "Nome: {$nome}\n"
    . "Email: {$email}\n"
    . "Attività: " . ($attivita !== '' ? $attivita : '-') . "\n"
    . "Sito web: " . ($sito !== '' ? $sito : '-') . "\n"
    . "Privacy: accettata\n"
    . "Pagina del modulo: " . (PAGINA_ERRORE === PAGINE_ERRORE['contatti'] ? 'contatti' : 'home') . "\n"
    . "Data e ora: " . date('d/m/Y H:i') . "\n\n"
    . "Messaggio:\n{$messaggio}\n";

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = $config['smtp_host'];
    $mail->Port       = (int) $config['smtp_port'];
    $mail->SMTPSecure = $config['smtp_secure'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $config['smtp_user'];
    $mail->Password   = $config['smtp_pass'];
    $mail->SMTPDebug  = 0;
    $mail->CharSet    = PHPMailer::CHARSET_UTF8;

    $mail->setFrom($config['smtp_user'], $config['mittente_nome']);
    // destinatario fisso, mai preso dal modulo
    $mail->addAddress($config['destinatario']);
    $mail->addReplyTo($email, $nome);

    $mail->isHTML(false);
    $mail->Subject = 'Nuova richiesta dal sito: ' . $nome;
    $mail->Body    = $corpo;

    $mail->send();
} catch (Exception $e) {
    // solo il tipo di errore: il messaggio di PHPMailer può contenere indirizzi
    error_log('Modulo contatti: invio SMTP non riuscito (' . get_class($e) . ')');
    vai_a(PAGINA_ERRORE);
}

vai_a(PAGINA_GRAZIE);
