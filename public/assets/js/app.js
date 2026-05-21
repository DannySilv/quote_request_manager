document.addEventListener("DOMContentLoaded", () => {
    setupPasswordToggles();
    setupSubmitLock();
    setupConfirmActions();
    setupQuoteRequestValidation();
    setupTableSearch();
});

const setupPasswordToggles = () => {
    const passwordFields = document.querySelectorAll('input[type="password"]');

    passwordFields.forEach((field) => {
        const wrapper = document.createElement("div");
        wrapper.classList.add("password-wrapper");

        field.parentNode.insertBefore(wrapper, field);
        wrapper.appendChild(field);

        const button = document.createElement("button");
        button.type = "button";
        button.classList.add("password-toggle");
        button.innerHTML = '👁️';

        button.addEventListener("click", () => {
            const isPassword = field.type === "password";

            field.type = isPassword ? "text" : "password";
            button.innerHTML = isPassword ? '🙈' : '👁️';
        });

        wrapper.appendChild(button);
    });
};

const setupSubmitLock = () => {
    const forms = document.querySelectorAll("form");

    forms.forEach((form) => {
        form.addEventListener("submit", () => {
            const submitButton = form.querySelector("button[type='submit']");

            if (!submitButton) {
                return;
            }

            submitButton.disabled = true;
            submitButton.dataset.originalText = submitButton.textContent;
            submitButton.textContent = "Attendere...";
        });
    });
};

const setupConfirmActions = () => {
    const confirmForms = document.querySelectorAll("[data-confirm]");

    confirmForms.forEach((form) => {
        form.addEventListener("submit", (event) => {
            const message = form.dataset.confirm || "Confermi questa operazione?";

            if (!window.confirm(message)) {
                event.preventDefault();

                const submitButton = form.querySelector("button[type='submit']");

                if (submitButton && submitButton.dataset.originalText) {
                    submitButton.disabled = false;
                    submitButton.textContent = submitButton.dataset.originalText;
                }
            }
        });
    });
};

const setupQuoteRequestValidation = () => {
    const forms = document.querySelectorAll("[data-validate='quote-request']");

    forms.forEach((form) => {
        form.addEventListener("submit", (event) => {
            clearFormErrors(form);

            const company = form.querySelector("[name='company']");
            const sector = form.querySelector("[name='sector']");
            const quantity = form.querySelector("[name='quantity']");
            const message = form.querySelector("[name='message']");

            const errors = [];

            if (company && company.value.trim().length < 2) {
                errors.push({
                    field: company,
                    message: "Il nome dell'azienda deve contenere almeno 2 caratteri."
                });
            }

            if (sector && sector.value.trim() === "") {
                errors.push({
                    field: sector,
                    message: "Seleziona un settore."
                });
            }

            if (quantity && Number(quantity.value) <= 0) {
                errors.push({
                    field: quantity,
                    message: "La quantità deve essere maggiore di zero."
                });
            }

            if (message && (message.value.trim().length < 10 || message.value.trim().length > 1000)) {
                errors.push({
                    field: message,
                    message: "Il messaggio deve contenere almeno 10 e non più di 1000 caratteri."
                });
            }

            if (errors.length > 0) {
                event.preventDefault();

                errors.forEach((error) => {
                    showFieldError(error.field, error.message);
                });

                const submitButton = form.querySelector("button[type='submit']");

                if (submitButton && submitButton.dataset.originalText) {
                    submitButton.disabled = false;
                    submitButton.textContent = submitButton.dataset.originalText;
                }
            }
        });
    });
};

const clearFormErrors = (form) => {
    const oldErrors = form.querySelectorAll(".js-field-error");
    const invalidFields = form.querySelectorAll(".input-error");

    oldErrors.forEach((error) => error.remove());
    invalidFields.forEach((field) => field.classList.remove("input-error"));
};

const showFieldError = (field, message) => {
    field.classList.add("input-error");

    const error = document.createElement("p");
    error.classList.add("field-error", "js-field-error");
    error.textContent = message;

    field.insertAdjacentElement("afterend", error);
};

const setupTableSearch = () => {
    const searchInput = document.querySelector("[data-table-search]");
    const table = document.querySelector("[data-searchable-table]");

    if (!searchInput || !table) {
        return;
    }

    const rows = table.querySelectorAll("tbody tr");

    searchInput.addEventListener("input", () => {
        const query = searchInput.value.trim().toLowerCase();

        rows.forEach((row) => {
            const text = row.textContent.toLowerCase();
            const matches = text.includes(query);

            row.style.display = matches ? "" : "none";
        });
    });
};