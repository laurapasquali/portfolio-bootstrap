<?php
/*
 * MODELLO della configurazione del modulo di contatto.
 *
 * Come usarlo:
 * 1. fai una copia di questo file e chiamala config-mail.php
 * 2. scrivi la password della casella modulo@laurapasquali.it in smtp_pass
 * 3. carica config-mail.php sul server UN LIVELLO SOPRA public_html
 *    (non dentro public_html: lì sarebbe raggiungibile dal browser)
 *
 * Questo file di esempio non contiene password: non scriverla qui.
 */

return [
    // server SMTP di Serverplan: controlla il nome esatto nel pannello,
    // nelle impostazioni della casella email (di solito mail.tuodominio.it)
    'smtp_host'     => 'mail.laurapasquali.it',
    'smtp_port'     => 465,
    'smtp_secure'   => 'ssl',

    // casella dedicata solo al modulo: è il mittente di ogni email
    'smtp_user'     => 'modulo@laurapasquali.it',
    'smtp_pass'     => '',

    'mittente_nome' => 'Sito laurapasquali.it',

    // dove arrivano le richieste
    'destinatario'  => 'info@laurapasquali.it',
];
