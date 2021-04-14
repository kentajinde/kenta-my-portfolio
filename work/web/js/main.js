"use strict"

{
  const mySwiper = new Swiper(".swiper-container", {
    pagination:{
      el: ".swiper-pagination",
      type: "bullets",
      clickable: true
    },

    navigation:{
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev"
    },

    slidesPerView: 1,

    breakpoints: {
      768: {
        slidesPerView: 2,
      },
      1100: {
        slidesPerView: 3,
      }
    },

    spaceBetween: 25
  });
}