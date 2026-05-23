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

// Vidéos : play quand visible, pause hors écran, hover play (galerie) et clic pour son
(() => {
    const galleryVideos = Array.from(document.querySelectorAll('.galerie-item video'));
    const projectVideos = Array.from(document.querySelectorAll('.projet-media video'));

    // IntersectionObserver: play when at least half visible, pause otherwise
    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const v = entry.target;
                try {
                    if (entry.isIntersecting) {
                        v.muted = true; // keep autoplay muted
                        v.play().catch(() => { });
                    } else {
                        v.pause();
                    }
                } catch (e) { }
            });
        }, { threshold: 0.5 });

        projectVideos.forEach(v => io.observe(v));
    } else {
        projectVideos.forEach(v => { v.muted = true; try { v.play(); } catch (e) { } });
    }

    // Galerie: keep hover behaviour for quick preview
    galleryVideos.forEach((video) => {
        const parent = video.closest('.galerie-item');
        if (!parent) return;
        parent.addEventListener('mouseenter', () => { video.play().catch(() => { }); });
        parent.addEventListener('mouseleave', () => { video.pause(); video.currentTime = 0; });
    });

    // Click on project media toggles mute and pauses other videos
    document.querySelectorAll('.projet-media').forEach(media => {
        media.addEventListener('click', function (e) {
            const v = this.querySelector('video');
            if (!v) return;
            const willUnmute = v.muted;
            document.querySelectorAll('.projet-media video').forEach(other => {
                if (other !== v) {
                    other.muted = true;
                    try { other.pause(); } catch (e) { }
                }
            });
            v.muted = !willUnmute;
            try { if (!v.paused) v.play(); } catch (e) { }
        });
    });
})();
function getApiUrl(path) {
    const basePath = window.location.pathname.includes('/SITEECOFI/')
        ? '/SITEECOFI'
        : '';

    return window.location.origin + basePath + path;
}

async function handleInscription(event) {
    event.preventDefault();

    const form = event.target;
    const btn = form.querySelector('.inscription-submit');

    const payload = {
        name: document.getElementById('inscNom')?.value.trim() || '',
        phone: document.getElementById('inscTel')?.value.trim() || '',
        email: document.getElementById('inscEmail')?.value.trim() || '',
        interest: document.getElementById('inscInteret')?.value.trim() || 'programme'
    };

    if (!payload.name || !payload.phone || !payload.email) {
        afficherNotification('Merci de remplir tous les champs.', 'error');
        return;
    }

    const originalText = btn ? btn.innerHTML : '';

    if (btn) {
        btn.disabled = true;
        btn.classList.add('is-loading');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';
    }

    try {
        const response = await fetch(getApiUrl('/app/api/subscribe_newsletter.php'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'fetch'
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Impossible de confirmer l’inscription.');
        }

        form.reset();

        if (btn) {
            btn.innerHTML = '<i class="fas fa-check"></i> Inscrit avec succès !';
            btn.style.background = '#2e7d52';
        }

        afficherNotification(
            result.message || 'Inscription confirmée avec succès.',
            'success'
        );

    } catch (error) {
        console.error(error);

        afficherNotification(
            error.message || 'Une erreur est survenue pendant l’inscription.',
            'error'
        );

    } finally {
        setTimeout(() => {
            if (btn) {
                btn.disabled = false;
                btn.classList.remove('is-loading');
                btn.innerHTML = originalText || '<i class="fas fa-paper-plane"></i> M’inscrire aux alertes';
                btn.style.background = '';
            }
        }, 1400);
    }
}
// Formulaire d'inscription
/* async function handleInscription(e) {
    e.preventDefault();

    const form = e.target;
    const nom = document.getElementById('inscNom')?.value.trim() || '';
    const tel = document.getElementById('inscTel')?.value.trim() || '';
    const email = document.getElementById('inscEmail')?.value.trim() || '';
    const interet = document.getElementById('inscInteret')?.value.trim() || 'programme';

    if (!nom || !tel || !email) {
        afficherNotification('Merci de remplir tous les champs.', 'error');
        return;
    }

    const btn = form.querySelector('.inscription-submit');
    const originalText = btn ? btn.innerHTML : '';

    if (btn) {
        btn.disabled = true;
        btn.classList.add('is-loading');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';
    }

    const payload = {
        name: nom,
        phone: tel,
        email: email,
        interest: interet
    };

    try {
        const response = await fetch(getApiUrl('/app/api/subscribe_newsletter.php'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const result = await readJsonResponse(response);

        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Impossible de confirmer l’inscription.');
        }

        form.reset();

        if (btn) {
            btn.innerHTML = '<i class="fas fa-check"></i> Inscrit avec succès !';
            btn.style.background = '#2e7d52';
        }

        afficherNotification(
            result.message || 'Inscription confirmée ! Vous recevrez nos alertes en avant-première.',
            'success'
        );

    } catch (error) {
        console.error(error);
        afficherNotification(error.message, 'error');
    } finally {
        setTimeout(() => {
            if (btn) {
                btn.disabled = false;
                btn.classList.remove('is-loading');
                btn.innerHTML = originalText || '<i class="fas fa-paper-plane"></i> M’inscrire aux alertes';
                btn.style.background = '';
            }
        }, 1400);
    }
} */

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


const inscriptionForm = document.getElementById('inscriptionForm');

if (inscriptionForm) {
    inscriptionForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const payload = {
            name: document.getElementById('inscNom').value.trim(),
            phone: document.getElementById('inscTel').value.trim(),
            email: document.getElementById('inscEmail').value.trim(),
            interest: document.getElementById('inscInteret').value.trim() || 'programme'
        };

        console.log('Payload newsletter:', payload);

        const response = await fetch(getApiUrl('/app/api/subscribe_newsletter.php'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const result = await readJsonResponse(response);
        console.log('Réponse newsletter:', result);

        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Erreur inscription newsletter');
        }

        inscriptionForm.reset();
        afficherNotification('Inscription confirmée avec succès.', 'success');
    });
}