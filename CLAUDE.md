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

- HTML, Bootstrap 5.3.8 (da CDN), CSS e JavaScript vanilla. Lenis (da CDN) per lo scroll morbido.
- Niente altri framework, librerie o strumenti di build: niente npm, Sass, Tailwind o React.

## File

- `index.html`: la home. Carica `css/style.css` e poi `css/home.css`.
- `aboutme.html`, `progetti.html`, `contatti.html`.
- Pagine progetto: `tesi.html`, `locandamagno.html`, `oodal-festival.html`, `eltoros.html`, `raw.html`, `golosandia.html`, `shampoococcole.html`. Usano le classi `progetto-…` di `style.css`.
- `css/style.css`: stili comuni a tutto il sito (variabili, font, navbar, footer, pagine interne, slider dei progetti).
- `css/home.css`: stili della sola home. Tutte le sue classi iniziano con `home-`.
- `assets/fonts/`: MonteCrysto (titoli) e Geist (testo), caricati con `@font-face` in `style.css`.
- `img/`: immagini, con una sottocartella per progetto.

## Regole del CSS

- Mobile first: regole base per il telefono, poi `@media (min-width: 768px)` per il tablet e `@media (min-width: 1200px)` per il desktop (breakpoint xl di Bootstrap). Nei media query metti solo quello che cambia.
- Le sezioni seguono lo stesso ordine nel blocco base, tablet e desktop, ognuna con un commento come titolo.
- Colori e font solo tramite le variabili di `:root` (`--terracotta`, `--verde`, `--base`, `--burgundy`, `--navy`, `--font-display`, `--font-body`). Niente colori esadecimali scritti a mano.
- La navbar è fissa: la prima sezione di ogni pagina ha `padding-top: calc(var(--nav-height) + …)`.
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
