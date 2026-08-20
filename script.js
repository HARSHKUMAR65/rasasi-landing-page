(() => {
  "use strict";
  const WHATSAPP_NUMBER = "97143246000";
  const DEFAULT_MESSAGE = "Hello Rasasi, I am attending ASD Market Week and would like to discuss The World of Hawas for my buisness.";

  const toast = document.getElementById("toast");
  let toastTimer;

  const showToast = (message) => {
    if (!toast) return;
    toast.textContent = message;
    toast.classList.add("is-visible");
    window.clearTimeout(toastTimer);
    toastTimer = window.setTimeout(() => toast.classList.remove("is-visible"), 2600);
  };

  const openWhatsApp = (message) => {
    const text = encodeURIComponent(message || DEFAULT_MESSAGE);
    const url = `https://wa.me/${WHATSAPP_NUMBER}?text=${text}`;
    const opened = window.open(url, "_blank", "noopener,noreferrer");
    if (!opened) window.location.href = url;
  };

  document.querySelectorAll("[data-whatsapp-message]").forEach((button) => {
    button.addEventListener("click", () => openWhatsApp(button.dataset.whatsappMessage));
  });

  const leadForm = document.getElementById("leadForm");
  if (leadForm) {
    leadForm.addEventListener("submit", (event) => {
      event.preventDefault();
      if (!leadForm.checkValidity()) {
        leadForm.reportValidity();
        return;
      }

      const data = new FormData(leadForm);
      const message = [
        "Hello Rasasi, I am attending ASD Market Week and would like to discuss The World of Hawas.",
        `Name: ${data.get("name")}`,
        `Company: ${data.get("company")}`,
        `Phone: +91 ${data.get("phone")}`,
        `Email: ${data.get("email")}`,
        `Type of Service: ${data.get("service")}`,
        `Message: ${data.get("message") || "-"}`
      ].join("\n");

      openWhatsApp(message);
      showToast("Opening WhatsApp with your enquiry.");
    });
  }

  const consent = document.getElementById("consent");
  const teamWhatsApp = document.getElementById("teamWhatsApp");
  const consentError = document.getElementById("consentError");

  if (teamWhatsApp && consent) {
    teamWhatsApp.addEventListener("click", () => {
      if (!consent.checked) {
        consentError.textContent = "Please accept the WhatsApp communication consent first.";
        consent.focus();
        return;
      }
      consentError.textContent = "";
      openWhatsApp(DEFAULT_MESSAGE);
    });

    consent.addEventListener("change", () => {
      if (consent.checked) consentError.textContent = "";
    });
  }

  document.querySelectorAll(".faq-question").forEach((button) => {
    button.addEventListener("click", () => {
      const item = button.closest(".faq-item");
      const willOpen = !item.classList.contains("is-open");
      item.classList.toggle("is-open", willOpen);
      button.setAttribute("aria-expanded", String(willOpen));
    });
  });
})();
