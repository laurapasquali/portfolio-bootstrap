# Sito di Laura Pasquali, graphic designer

Sito vetrina con portfolio (laurapasquali.it). Lo sviluppo io, Laura: sono una graphic designer, non una sviluppatrice, e voglio capire ogni scelta del codice. Uso VS Code con Live Server per l'anteprima.

## Come lavorare con me

- Rispondi in italiano.
- Prima di modificare, spiega cosa cambi e perché; poi applica la modifica.
- Per i bug segui questo ordine: sintomo, regola o struttura che lo causa, correzione.
- Fai modifiche mirate: non riscrivere file interi e non rinominare classi senza chiedermelo.
- Prima di togliere una regola CSS o un file, cerca in tutti gli `.html` se qualche pagina li usa ancora.
- Navbar e footer sono copiati in ogni pagina: una modifica lì va ripetuta in tutti gli `.html`.

## Stack

- HTML, Bootstrap 5.3.8 (in locale: `css/bootstrap.min.css`), CSS e JavaScript vanilla. Lenis 1.3.21 (in locale: `js/lenis.min.js`) per lo scroll morbido.
- Nessuna risorsa da domini esterni (GDPR): font, stili e script stanno tutti nella cartella del sito.
- PHP solo per il modulo di contatto (`invia.php` con PHPMailer, hosting Serverplan).
- Niente altri framework, librerie o strumenti di build: niente npm, Sass, Tailwind o React.

## File

- `index.html`: la home. Carica `css/style.css` e poi `css/home.css`. Contiene il modulo di contatto (sezione `#form`).
- `aboutme.html`, `progetti.html`, `pacchetti.html`, `privacy.html`.
- `grazie.html` (dopo l'invio del modulo) e `404.html` (pagina di errore): entrambe con `noindex`. `404.html` usa percorsi dalla radice (`/css/…`, `/img/…`), perché il server la mostra anche dentro sottocartelle.
- Pagine progetto: `oodal-festival.html`, `raw.html`, `podere-i-cerri.html`, `locandamagno.html`, `premio-lunezia.html`, `golosandia.html`, `shampoococcole.html`. Usano le classi `progetto-…` di `style.css`.
- `_archivio/`: pagine tolte dal sito ma conservate (per ora `tesi.html` con `img/tesi/`). Resta nel repository, non va caricata sul server.
- `css/style.css`: stili comuni a tutto il sito (variabili, font, navbar, footer, pagine interne, slider dei progetti).
- `css/home.css`: stili della sola home. Tutte le sue classi iniziano con `home-`.
- `js/menu.js`: menu e navbar di tutte le pagine (apertura e chiusura, Esc, ombra quando si scorre). `js/hero.js`: parola animata nella hero della home.
- `invia.php`: invia il modulo con PHPMailer (cartella `phpmailer/`). La configurazione con la password è `config-mail.php`, un livello sopra `public_html`; nel repository c'è solo il modello `config-mail.example.php`.
- `.htaccess`: redirect a https senza www, blocco di `phpmailer/` e `config-mail*.php`, pagina 404, cache del browser.
- `assets/fonts/`: Lora (titoli) e Geist (testo), caricati con `@font-face` in `style.css`.
- `img/`: immagini, con una sottocartella per progetto. Il logo della navbar e del footer è `img/logo-footer.png`.
- Dopo una modifica a CSS o JS aumenta la versione nei link (`?v=`) in tutte le pagine; lo stesso per un'immagine sostituita con lo stesso nome. Il `.htaccess` tiene in cache CSS e JS una settimana, le immagini un mese.

## Regole del CSS

- Mobile first: regole base per il telefono, poi `@media (min-width: 768px)` per il tablet e `@media (min-width: 1200px)` per il desktop (breakpoint xl di Bootstrap). Nei media query metti solo quello che cambia.
- Le sezioni seguono lo stesso ordine nel blocco base, tablet e desktop, ognuna con un commento come titolo.
- Colori e font solo tramite le variabili di `:root` (`--terracotta`, `--verde`, `--base`, `--burgundy`, `--navy`, `--font-display`, `--font-body`). Niente colori esadecimali scritti a mano.
- La navbar è fissa: la prima sezione di ogni pagina ha `padding-top: calc(var(--nav-height) + …)`. `--nav-height` vale 120px su mobile (riquadro staccato di 12px dai bordi) e 165px su desktop. Le sezioni con un `id` hanno `scroll-margin-top: var(--nav-height)`, così le ancore si fermano sotto la navbar.
- Margini laterali uguali in tutte le sezioni: 1.25rem su mobile, 10% su desktop.
- I colori li decido io: se un testo ha un contrasto basso (sotto 4,5:1), segnalamelo invece di cambiarlo.

## Errori già capitati: controlla sempre

- Conflitti di specificità e regole doppie: prima di aggiungere una regola, cerca se lo stesso selettore esiste già, anche nei media query.
- Nomi di classe già usati altrove (esempio: `.progetto-cover` è il velo delle card in `progetti.html`).
- Refusi nei nomi di classe e valori CSS non validi: falliscono in silenzio.
- Tag HTML non chiusi, che spostano tutte le sezioni successive.
- Stili di default di Bootstrap su `p`, `ul`, `ol`, `figure`, `dt`, `dd`; le classi utility di Bootstrap usano `!important`.
- Variabili usate nel CSS ma non più definite in `:root`.

## Accessibilità

- Ogni pagina inizia con `<!DOCTYPE html>` e `<html lang="it">`.
- Ogni immagine ha un `alt` descrittivo; quelle decorative hanno `alt=""` o `aria-hidden="true"`.
- Ogni campo del form ha la sua `<label>` collegata.
