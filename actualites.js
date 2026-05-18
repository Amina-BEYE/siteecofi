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