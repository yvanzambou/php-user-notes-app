document.addEventListener("DOMContentLoaded", () => {
  // Erfolgsmeldungen blenden sich nach 5 Sekunden aus (Fehler bleiben stehen).
  document.querySelectorAll(".flash--success[data-flash]").forEach((el) => {
    setTimeout(() => {
      el.classList.add("is-hidden");
      el.addEventListener("transitionend", () => el.remove(), { once: true });
    }, 5000);
  });

  // Sicherheitsabfrage vor dem Löschen.
  document.querySelectorAll("form[data-confirm]").forEach((form) => {
    form.addEventListener("submit", (event) => {
      if (!window.confirm(form.dataset.confirm)) {
        event.preventDefault();
      }
    });
  });

  // Strg/Cmd + Enter speichert die Notiz.
  document.querySelectorAll("textarea[name='note']").forEach((area) => {
    area.addEventListener("keydown", (event) => {
      if (event.key === "Enter" && (event.ctrlKey || event.metaKey)) {
        area.form.requestSubmit();
      }
    });
  });
});
