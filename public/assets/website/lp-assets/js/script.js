
// Swiper
var swiper = new Swiper(".mySwiper", {
  slidesPerView: 1,
  grabCursor: true,
  loop: true,
  speed: 800, // Transition speed in milliseconds
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
  navigation: {
    nextEl: ".swiper-button-prev",
    prevEl: ".swiper-button-next",
  },
  autoplay: {
    delay: 3000, // Auto-rotate every 3 seconds
    disableOnInteraction: false, // Keep autoplay active after user interaction
  },
});

// Menu
function toggleMenu() {
    document.getElementById('menu').classList.toggle('active');
}
