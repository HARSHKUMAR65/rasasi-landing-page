(() => {
  "use strict";

  const toast = document.getElementById("toast");
  let toastTimer;

  const showToast = (message) => {
    if (!toast) return;
    toast.textContent = message;
    toast.classList.add("is-visible");
    window.clearTimeout(toastTimer);
    toastTimer = window.setTimeout(() => toast.classList.remove("is-visible"), 2600);
  };

  const serviceWrapper = document.getElementById("serviceSelectWrapper");
  if (serviceWrapper) {
    const trigger = document.getElementById("serviceSelectTrigger");
    const valueSpan = serviceWrapper.querySelector(".custom-select-value");
    const hiddenInput = document.getElementById("service");
    const options = serviceWrapper.querySelectorAll(".custom-select-option");

    trigger.addEventListener("click", (e) => {
      e.stopPropagation();
      const isOpen = serviceWrapper.classList.toggle("is-open");
      trigger.setAttribute("aria-expanded", String(isOpen));
    });

    options.forEach((option) => {
      option.addEventListener("click", () => {
        const val = option.dataset.value;
        valueSpan.textContent = val;
        hiddenInput.value = val;
        options.forEach((opt) => opt.classList.remove("is-selected"));
        option.classList.add("is-selected");
        serviceWrapper.classList.remove("is-open");
        trigger.setAttribute("aria-expanded", "false");
      });
    });

    document.addEventListener("click", (e) => {
      if (!serviceWrapper.contains(e.target)) {
        serviceWrapper.classList.remove("is-open");
        trigger.setAttribute("aria-expanded", "false");
      }
    });
  }

  const leadForm = document.getElementById("leadForm");
  if (leadForm) {
    leadForm.addEventListener("submit", (event) => {
      event.preventDefault();
      if (!leadForm.checkValidity()) {
        leadForm.reportValidity();
        return;
      }

      showToast("Thank you! Your enquiry has been received.");
      leadForm.reset();
    });
  }

  const consent = document.getElementById("consent");
  const teamWhatsApp = document.getElementById("teamWhatsApp");
  const consentError = document.getElementById("consentError");

  if (teamWhatsApp && consent) {
    teamWhatsApp.addEventListener("click", () => {
      if (!consent.checked) {
        consentError.textContent = "Please accept the communication consent first.";
        consent.focus();
        return;
      }
      consentError.textContent = "";
      showToast("Thank you! Our team will reach out to you.");
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
