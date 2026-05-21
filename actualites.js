// Intersection Observer pour fade-up
const observer = new IntersectionObserver((entries) => {
    entries.forEach((e) => {
        if (e.isIntersecting) {
            e.target.classList.add('visible');
            observer.unobserve(e.target);
        }
    });
}, { threshold: 0.12 });

document.querySelectorAll('.fade-up').forEach((el) => observer.observe(el));

// Lecture auto des vidéos galerie au hover
document.querySelectorAll('.galerie-item video, .projet-media video').forEach((video) => {
    const parent = video.closest('.galerie-item, .projet-card');
    if (!parent) return;
    parent.addEventListener('mouseenter', () => {
        video.play().catch(() => {});
    });
    parent.addEventListener('mouseleave', () => {
        video.pause();
        video.currentTime = 0;
    });
});

// Formulaire d'inscription
function handleInscription(e) {
    e.preventDefault();
    const nom   = document.getElementById('inscNom').value.trim();
    const tel   = document.getElementById('inscTel').value.trim();
    const email = document.getElementById('inscEmail').value.trim();

    if (!nom || !tel || !email) {
        afficherNotification && afficherNotification('Merci de remplir tous les champs.', 'error');
        return;
    }

    // Simulation d'envoi (à brancher sur /app/api/submit_quote.php)
    const btn = e.target.querySelector('.inscription-submit');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi…';
    btn.disabled = true;

    setTimeout(() => {
        btn.innerHTML = '<i class="fas fa-check"></i> Inscrit avec succès !';
        btn.style.background = '#2e7d52';
        e.target.reset();
        typeof afficherNotification === 'function' &&
            afficherNotification('Inscription confirmée ! Vous recevrez nos alertes en avant-première.', 'success');
    }, 1400);
}

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach((a) => {
    a.addEventListener('click', (e) => {
        const target = document.querySelector(a.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

function openActualiteContact(trigger) {
    const modal = document.getElementById('actualiteContactModal');
    if (!modal || !trigger) return;

    const title = trigger.dataset.actualiteTitle || 'Actualité ECOFI';
    const type = trigger.dataset.actualiteType || 'Actualité';

    document.getElementById('selectedActualiteTitle').textContent = title;
    document.getElementById('selectedActualiteType').textContent = type;
    document.getElementById('actualiteTitleInput').value = title;
    document.getElementById('actualiteTypeInput').value = type;
    document.getElementById('actualiteContactTitle').textContent = title;
    document.getElementById('actualiteMessage').value = `Bonjour ECOFI, je souhaite recevoir plus de détails sur : ${type} - ${title}.`;

    const messageBox = document.getElementById('actualiteContactMessage');
    if (messageBox) {
        messageBox.style.display = 'none';
        messageBox.textContent = '';
    }

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    const firstInput = document.getElementById('actualiteNom');
    if (firstInput) firstInput.focus();
}

function closeActualiteContact() {
    const modal = document.getElementById('actualiteContactModal');
    if (!modal) return;

    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
}

function showActualiteContactMessage(message, type = 'success') {
    const box = document.getElementById('actualiteContactMessage');
    if (!box) return;

    box.className = `form-messages ${type}`;
    box.textContent = message;
    box.style.display = 'block';
}

const actualiteContactForm = document.getElementById('actualiteContactForm');
if (actualiteContactForm) {
    actualiteContactForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const submitButton = actualiteContactForm.querySelector('button[type="submit"]');
        const formData = new FormData(actualiteContactForm);
        const title = String(formData.get('actualite_title') || '').trim();
        const type = String(formData.get('actualite_type') || '').trim();

        const payload = {
            type: 'contact',
            nom: String(formData.get('nom') || '').trim(),
            email: String(formData.get('email') || '').trim(),
            telephone: String(formData.get('telephone') || '').trim(),
            service: `${type} - ${title}`,
            message: String(formData.get('message') || '').trim()
        };

        if (!payload.nom || !payload.email || !payload.telephone || !payload.message) {
            showActualiteContactMessage('Veuillez remplir tous les champs.', 'error');
            return;
        }

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.classList.add('is-loading');
            submitButton.dataset.originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';
        }

        try {
            const response = await fetch('app/api/submit_quote.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Impossible d’envoyer votre demande.');
            }

            showActualiteContactMessage('Votre demande a bien été envoyée. ECOFI vous recontacte rapidement.', 'success');
            typeof afficherNotification === 'function' && afficherNotification('Demande envoyée à ECOFI.', 'success');
            actualiteContactForm.reset();
            setTimeout(closeActualiteContact, 1400);
        } catch (error) {
            showActualiteContactMessage(error.message, 'error');
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.classList.remove('is-loading');
                submitButton.innerHTML = submitButton.dataset.originalText || 'Envoyer la demande';
            }
        }
    });
}

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closeActualiteContact();
    }
});

const actualiteContactModal = document.getElementById('actualiteContactModal');
if (actualiteContactModal) {
    actualiteContactModal.addEventListener('click', (event) => {
        if (event.target === actualiteContactModal) {
            closeActualiteContact();
        }
    });
}
