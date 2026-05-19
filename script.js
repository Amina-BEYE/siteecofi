document.addEventListener('DOMContentLoaded', function () {
    const adhesionForm = document.getElementById('adhesionForm');
    const messageElement = document.getElementById('adhesionFormMessage');

    if (!adhesionForm || !messageElement) {
        return;
    }

    adhesionForm.addEventListener('submit', function (event) {
        if (adhesionForm.dataset.isSubmitting === 'true') {
            event.preventDefault();
            return;
        }

        const requiredFields = [
            'nom',
            'prenom',
            'date_naissance',
            'lieu_naissance',
            'adresse',
            'telephone',
            'cni',
            'email',
            'mode_paiement'
        ];

        const errors = [];

        requiredFields.forEach(function (name) {
            const input = adhesionForm.querySelector('[name="' + name + '"]');
            if (!input || !input.value.trim()) {
                const label = input ? input.previousElementSibling?.textContent || name : name;
                errors.push("Le champ '" + label + "' est requis.");
            }
        });

        const emailInput = adhesionForm.querySelector('[name="email"]');
        if (emailInput && emailInput.value.trim()) {
            const emailValue = emailInput.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailValue)) {
                errors.push('Veuillez renseigner une adresse email valide.');
            }
        }

        const phoneInput = adhesionForm.querySelector('[name="telephone"]');
        if (phoneInput && phoneInput.value.trim()) {
            const phoneValue = phoneInput.value.trim();
            const phoneRegex = /^[0-9 +()\-]{7,20}$/;
            if (!phoneRegex.test(phoneValue)) {
                errors.push('Veuillez renseigner un numéro de téléphone valide.');
            }
        }

        if (errors.length) {
            event.preventDefault();
            messageElement.innerHTML = errors.map(function (error) {
                return '<div>' + error + '</div>';
            }).join('');
            messageElement.style.display = 'block';
            messageElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        adhesionForm.dataset.isSubmitting = 'true';

        const submitButton = adhesionForm.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.classList.add('is-loading');
            submitButton.dataset.originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<span class="button-spinner"></span> Envoi en cours...';
        }

        if (typeof showQuoteLoader === 'function') {
            showQuoteLoader('Envoi de votre adhésion en cours...');
        } else {
            const loader = document.getElementById('quoteLoader');
            const loaderText = document.getElementById('quoteLoaderText');
            if (loaderText) loaderText.textContent = 'Envoi de votre adhésion en cours...';
            if (loader) loader.style.display = 'flex';
        }
    });
});
