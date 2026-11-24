/* =====================================================
   HERO DELLA HOME: la parola in corsivo del titolo si "scrive" da sinistra a destra,
   resta ferma, sparisce e lascia il posto alla successiva, a ciclo.

   Nell'HTML il titolo resta "Il tuo brand merita di essere riconosciuto al primo sguardo":
   Google e i lettori di schermo leggono sempre "riconosciuto" (qui diventa visually-hidden),
   mentre le parole animate sono aria-hidden.
   Con "riduci movimento" attivo, o se JavaScript non parte, resta fissa "riconosciuto".
   L'animazione si ferma quando la scheda del browser non è visibile.
   Lo stile (maschera e tempi) è in home.css, sezione "hero"
   ===================================================== */

(function () {
  const parola = document.querySelector(".home-hero-parola");
  if (!parola) return;
  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

  const PAROLE = ["riconosciuto", "ricordato", "scelto", "memorabile", "capito"];
  const SCRIVI = 1000;     // ms per scrivere la parola (uguale all'animazione in home.css)
  const FERMA = 2500;      // ms in cui resta ferma
  const CANCELLA = 500;    // ms per sparire (uguale alla transizione in home.css)

  // "riconosciuto" resta nel titolo per Google e i lettori di schermo, nascosto alla vista
  const testoVero = parola.textContent;
  parola.textContent = "";
  const perLettori = document.createElement("span");
  perLettori.className = "visually-hidden";
  perLettori.textContent = testoVero;

  // le cinque parole sovrapposte nella stessa cella: lo spazio è quello della più lunga
  const gruppo = document.createElement("span");
  gruppo.className = "home-hero-parole-animate";
  gruppo.setAttribute("aria-hidden", "true");
  const parole = PAROLE.map((testo) => {
    const span = document.createElement("span");
    span.textContent = testo;
    gruppo.appendChild(span);
    return span;
  });

  parola.append(perLettori, gruppo);

  let indice = 0;
  let timer = null;

  function fase(nome) {
    parole.forEach((span) => span.classList.remove("scrive", "ferma", "cancella"));
    parole[indice].classList.add(nome);
  }

  // scrive la parola attuale, la tiene ferma, la fa sparire e passa alla successiva
  function ciclo() {
    fase("scrive");
    timer = setTimeout(() => {
      fase("ferma");
      timer = setTimeout(cancellaEPassa, FERMA);
    }, SCRIVI);
  }

  function cancellaEPassa() {
    fase("cancella");
    timer = setTimeout(() => {
      indice = (indice + 1) % parole.length;
      ciclo();
    }, CANCELLA);
  }

  // all'apertura si vede subito "riconosciuto" già scritto, poi parte il ciclo
  fase("ferma");
  timer = setTimeout(cancellaEPassa, FERMA);

  // scheda nascosta: si ferma; quando torna visibile, riparte dalla parola attuale
  document.addEventListener("visibilitychange", () => {
    if (document.hidden) {
      clearTimeout(timer);
      timer = null;
    } else if (timer === null) {
      ciclo();
    }
  });
})();
