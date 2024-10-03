// Variables globales pour les diaporamas
const slideInterval = 7000; // Temps d'affichage pour chaque diapositive (en millisecondes)

// Initialisation du diaporama de la page d'accueil
function initHomeSlideshow() {
    let slideIndex = 0;
    const slidesWrapper = document.getElementById('slidesWrapper');

    if (!slidesWrapper) {
        console.error("L'élément #slidesWrapper est introuvable.");
        return;
    }

    const slides = document.getElementsByClassName('slides');
    const dots = document.getElementsByClassName('dot');
    const totalSlides = slides.length;

    if (totalSlides === 0) {
        console.error("Aucune diapositive trouvée.");
        return;
    }

    function showSlides() {
        slideIndex = (slideIndex + 1) % totalSlides;
        slidesWrapper.style.transform = `translateX(-${slideIndex * 100}vw)`;

        // Gestion des points indicateurs
        for (let i = 0; i < dots.length; i++) {
            dots[i].classList.remove('active');
        }
        dots[slideIndex].classList.add('active');
    }

    setInterval(showSlides, slideInterval); // Défilement automatique
}

// Initialisation des diaporamas des habitats
function initHabitatSlideshows() {
    const slideshows = ['slideshowsavane', 'slideshowjungle', 'slideshowmarais'];

    slideshows.forEach(slideshowId => {
        let slideIndex = 0;
        const slidesWrapper = document.querySelector(`#${slideshowId} .slides-wrapper2`);

        if (!slidesWrapper) {
            console.error(`L'élément .slides-wrapper2 pour ${slideshowId} est introuvable.`);
            return;
        }

        const slides = slidesWrapper.getElementsByClassName('slides2');
        const dots = document.querySelectorAll(`#${slideshowId} .dot2`);
        const nextButton = document.querySelector(`#${slideshowId} .next2`);
        const prevButton = document.querySelector(`#${slideshowId} .prev2`);

        if (slides.length === 0) {
            console.error(`Aucune diapositive trouvée dans ${slideshowId}.`);
            return;
        }

        function showSlides() {
            slideIndex = (slideIndex + 1) % slides.length;
            updateSlides();
        }

        function updateSlides() {
            const offset = -(slideIndex * 100);
            slidesWrapper.style.transform = `translateX(${offset}%)`;

            dots.forEach(dot => dot.classList.remove("active"));
            dots[slideIndex].classList.add("active");
        }

        function changeSlide(n) {
            slideIndex = (slideIndex + n + slides.length) % slides.length;
            updateSlides();
        }

        // Événements pour les boutons de navigation
        if (nextButton) nextButton.addEventListener('click', () => changeSlide(1));
        if (prevButton) prevButton.addEventListener('click', () => changeSlide(-1));

        let interval = setInterval(showSlides, slideInterval);

        // Assurez-vous que la navigation manuelle arrête le diaporama automatique
        function resetInterval() {
            clearInterval(interval);
            interval = setInterval(showSlides, slideInterval);
        }

        if (nextButton) nextButton.addEventListener('click', resetInterval);
        if (prevButton) prevButton.addEventListener('click', resetInterval);

        showSlides(); // Appel initial
    });
}

// Gestion du menu burger
function toggleMenu() {
    const menu = document.getElementById('menu');
    if (menu) menu.classList.toggle('active');
}

// Gestion de la fenêtre modale de connexion
function openModal() {
    document.getElementById('loginModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('loginModal').style.display = 'none';
}

window.onclick = function(event) {
    if (event.target === document.getElementById('loginModal')) closeModal();
}

// Gestion du carousel des avis
document.addEventListener('DOMContentLoaded', () => {
    const prevButton = document.querySelector('.carousel-button.prev');
    const nextButton = document.querySelector('.carousel-button.next');
    let currentIndex = 0;
    const items = document.querySelectorAll('.avis-item');
    const totalItems = items.length;

    if (totalItems === 0) {
        console.error("Aucun élément d'avis trouvé.");
        return;
    }

    function moveSlide(n) {
        currentIndex = (currentIndex + n + totalItems) % totalItems;
        const container = document.querySelector('.avis-grid');
        const itemWidth = items[0].offsetWidth;
        container.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
    }

    prevButton.addEventListener('click', () => moveSlide(-1));
    nextButton.addEventListener('click', () => moveSlide(1));
});

// Gestion de la notation par étoiles
document.addEventListener('DOMContentLoaded', () => {
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('rating');

    stars.forEach(star => {
        star.addEventListener('click', () => {
            const value = star.getAttribute('data-value');
            ratingInput.value = value;
            stars.forEach(s => s.classList.remove('selected'));
            for (let i = 0; i < value; i++) stars[i].classList.add('selected');
        });

        star.addEventListener('mouseover', () => {
            const value = star.getAttribute('data-value');
            stars.forEach(s => s.classList.toggle('hover', s.getAttribute('data-value') <= value));
        });

        star.addEventListener('mouseout', () => stars.forEach(s => s.classList.remove('hover')));
    });
});

// Gestion des boutons pour afficher/cacher les images
function toggleImages(buttonId, imagesId) {
    const button = document.getElementById(buttonId);
    const images = document.getElementById(imagesId);

    if (button) {
        button.addEventListener('click', function () {
            const isVisible = images.style.display === "flex";
            images.style.display = isVisible ? "none" : "flex";
            this.textContent = isVisible ? 'Afficher les animaux !' : 'Cacher les animaux !';
        });
    }
}

// Initialisation des éléments après le chargement de la page
document.addEventListener("DOMContentLoaded", () => {
    initHomeSlideshow();  // Page d'accueil
    initHabitatSlideshows(); // Page des habitats

    toggleImages('button1', 'images1');
    toggleImages('button2', 'images2');
    toggleImages('button3', 'images3');

    // Gestion du zoom en fonction de l'état "ouvert" des sections
    document.querySelectorAll('.show-more').forEach(button => {
        button.addEventListener('click', function() {
            this.classList.toggle('active');

            const serviceItem = this.closest('.service-item');
            if (this.classList.contains('active')) {
                serviceItem.classList.add('open');
            } else {
                serviceItem.classList.remove('open');
            }
        });
    });
});
