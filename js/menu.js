/* =====================================================
   MENU A TENDINA (mobile e tablet), uguale in tutte le pagine
   - il bottone dei puntini lo apre e lo chiude (onclick="toggleMenu()" nell'HTML)
   - ogni link del menu lo chiude: sulla home il form e "Come lavoro" sono
     nella stessa pagina e il menu aperto li coprirebbe
   - il tasto Esc lo chiude e riporta il focus sul bottone
   Il bottone dice ai lettori di schermo se il menu è aperto (aria-expanded)
   e cosa fa (aria-label). Due lineette da chiuso, X da aperto: lo scambio lo fa il CSS,
   guardando aria-expanded
   ===================================================== */

function toggleMenu() {
  const menu = document.getElementById("menu");
  const bottone = document.querySelector(".toggleMenu");
  const aperto = menu.classList.toggle("open");
  bottone.setAttribute("aria-expanded", aperto);
  bottone.setAttribute("aria-label", aperto ? "Chiudi il menu" : "Apri il menu");
}

document.querySelectorAll("#menu a").forEach((link) => {
  link.addEventListener("click", () => {
    if (document.getElementById("menu").classList.contains("open")) toggleMenu();
  });
});

document.addEventListener("keydown", (evento) => {
  if (evento.key === "Escape" && document.getElementById("menu").classList.contains("open")) {
    toggleMenu();
    document.querySelector(".toggleMenu").focus();
  }
});
