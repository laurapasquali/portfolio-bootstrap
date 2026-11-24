/* =====================================================
   HERO DELLA HOME: la parola in corsivo del titolo si scrive come a macchina,
   una lettera alla volta, con un cursore che lampeggia; resta ferma, si cancella
   da destra a sinistra (come il tasto backspace) e lascia il posto alla successiva.

   Nell'HTML il titolo resta "Il tuo brand merita di essere riconosciuto al primo sguardo":
   Google e i lettori di schermo leggono sempre "riconosciuto" (qui diventa visually-hidden),
   mentre le lettere animate e il cursore sono aria-hidden.
   Con "riduci movimento" attivo, o se JavaScript non parte, resta fissa "riconosciuto", senza cursore.
   L'animazione si ferma quando la scheda del browser non è visibile.
   Lo stile (cursore e spazio stabile) è in home.css, sezione "hero"
   ===================================================== */

(function () {
  const parola = document.querySelector(".home-hero-parola");
  if (!parola) return;
  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

  const PAROLE = ["riconosciuto", "ricordato", "scelto", "memorabile", "capito"];
  const SCRIVI = 90;       // ms per ogni lettera scritta
  const CANCELLA = 50;     // ms per ogni lettera cancellata
  const FERMA = 2000;      // ms in cui la parola resta intera
  const PAUSA = 300;       // ms a parola cancellata, prima di scrivere la successiva

  // "riconosciuto" resta nel titolo per Google e i lettori di schermo, nascosto alla vista
  const testoVero = parola.textContent;
  parola.textContent = "";
  const perLettori = document.createElement("span");
  perLettori.className = "visually-hidden";
  perLettori.textContent = testoVero;

  // parte visibile, nascosta ai lettori di schermo:
  // - "misura": la parola più lunga, invisibile, tiene fermo lo spazio (niente salti)
  // - "scritta": le lettere che compaiono, seguite dal cursore
  const visibile = document.createElement("span");
  visibile.className = "home-hero-macchina";
  visibile.setAttribute("aria-hidden", "true");

  const misura = document.createElement("span");
  misura.className = "home-hero-misura";
  misura.textContent = PAROLE.reduce((a, b) => (b.length > a.length ? b : a));

  const scritta = document.createElement("span");
  scritta.className = "home-hero-scritta";
  const testo = document.createElement("span");
  const cursore = document.createElement("span");
  cursore.className = "home-hero-cursore";
  scritta.append(testo, cursore);

  visibile.append(misura, scritta);
  parola.append(perLettori, visibile);

  let indice = 0;                 // parola attuale
  let lettere = PAROLE[0].length; // lettere visibili: si parte con "riconosciuto" già scritta
  let fase = "ferma";             // "scrive", "ferma", "cancella"
  let timer = null;

  testo.textContent = PAROLE[0];

  function passo() {
    const attuale = PAROLE[indice];

    if (fase === "scrive") {
      lettere++;
      testo.textContent = attuale.slice(0, lettere);
      if (lettere < attuale.length) {
        timer = setTimeout(passo, SCRIVI);
      } else {
        fase = "ferma";
        timer = setTimeout(passo, FERMA);
      }
    } else if (fase === "ferma") {
      fase = "cancella";
      timer = setTimeout(passo, CANCELLA);
    } else {
      lettere--;
      testo.textContent = attuale.slice(0, lettere);
      if (lettere > 0) {
        timer = setTimeout(passo, CANCELLA);
      } else {
        indice = (indice + 1) % PAROLE.length;
        fase = "scrive";
        timer = setTimeout(passo, PAUSA);
      }
    }
  }

  timer = setTimeout(passo, FERMA);

  // scheda nascosta: si ferma; quando torna visibile, riprende da dove era
  document.addEventListener("visibilitychange", () => {
    if (document.hidden) {
      clearTimeout(timer);
      timer = null;
    } else if (timer === null) {
      timer = setTimeout(passo, fase === "ferma" ? FERMA : SCRIVI);
    }
  });
})();
