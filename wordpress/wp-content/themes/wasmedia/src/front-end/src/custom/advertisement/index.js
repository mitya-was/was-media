"use strict";
const styles = require("../../scss/custom/advertisement/slider.scss");
var swiper = new Swiper('.swiper-container', {
  slidesPerView: 5,
  grabCursor: true,
  spaceBetween: 0,
  initialSlide: 2,
  centeredSlides: true,
  preventClicks: false,
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },
  pagination: {
    el: '.swiper-pagination',
    type: "bullets",
    clickable: true,
  },
  breakpoints: {
    1817: {
      slidesPerView: 4
    },
    1100: {
      slidesPerView: 3
    },
    737: {
      slidesPerView: 2
    },
    568: {
      slidesPerView: 1
    }
  }
});
