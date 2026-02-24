AOS.init({
    duration: 1200,
    once: true,
});

if (document.querySelector('.hero')) {
    gsap.from('.hero h1', { duration: 2, y: 50, opacity: 0, ease: 'power3.out' });
    gsap.from('.hero p', { duration: 2, y: 50, opacity: 0, delay: 0.5, ease: 'power3.out' });
    gsap.from('.hero a', { duration: 2, y: 50, opacity: 0, delay: 1, ease: 'power3.out' });
}

const heroVideo = document.querySelector('.hero video');
if (heroVideo) {
    window.addEventListener('scroll', () => {
        const scrollTop = window.pageYOffset;
        heroVideo.style.transform = `translateY(${scrollTop * 0.5}px)`;
    });
}

// Карусель для фото
document.querySelectorAll('.carousel').forEach(carousel => {
    const images = carousel.querySelectorAll('img');
    if (images.length <= 1) return;

    let current = 0;
    setInterval(() => {
        images[current].classList.remove('active');
        current = (current + 1) % images.length;
        images[current].classList.add('active');
    }, 4000);
});