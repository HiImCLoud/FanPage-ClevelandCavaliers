const hamburger = document.querySelector(
    ".header .nav-bar .nav-list .hamburger"
  );
  const mobile_menu = document.querySelector(".header .nav-bar .nav-list ul");
  const menu_item = document.querySelectorAll(
    ".header .nav-bar .nav-list ul li a"
  );
  const header = document.querySelector(".header.container");
  
  hamburger.addEventListener("click", () => {
    hamburger.classList.toggle("active");
    mobile_menu.classList.toggle("active");
  });
  
  document.addEventListener("scroll", () => {
    var scroll_position = window.scrollY;
    if (scroll_position > 250) {
      header.style.backgroundColor = "#29323c";
    } else {
      header.style.backgroundColor = "transparent";
    }
  });
  
  menu_item.forEach((item) => {
    item.addEventListener("click", () => {
      hamburger.classList.toggle("active");
      mobile_menu.classList.toggle("active");
    });
  });
  


const track = document.querySelector('.carousel-track');
const slides = Array.from(track.children);
const nextButton = document.querySelector('.carousel-button-right');
const prevButton = document.querySelector('.carousel-button-left');
const slideWidth = slides[0].getBoundingClientRect().width;


slides.forEach((slide, index) => {
  slide.style.left = slideWidth * index + 'px';
});

const moveToSlide = (track, currentSlide, targetSlide) => {
  track.style.transform = 'translateX(-' + targetSlide.style.left + ')';
  currentSlide.classList.remove('current-slide');
  targetSlide.classList.add('current-slide');
};

prevButton.addEventListener('click', () => {
  const currentSlide = track.querySelector('.current-slide');
  const prevSlide = currentSlide.previousElementSibling;

  if (prevSlide) {
    moveToSlide(track, currentSlide, prevSlide);
  }
});

nextButton.addEventListener('click', () => {
  const currentSlide = track.querySelector('.current-slide');
  const nextSlide = currentSlide.nextElementSibling;

  if (nextSlide) {
    moveToSlide(track, currentSlide, nextSlide);
  }
});


