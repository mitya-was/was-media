module.exports = {
  default: {
    loop: true,
    slidesPerGroup: 1,
    spaceBetween: 0,
    autoHeight: true,
    grabCursor: true,
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
      modifierClass: 'pagination-thumb-',
      renderBullet(index, className) {
        return `<span class="${className}"><img class="wp-post-image" src="${
          this.slides[index + 1].children[0].dataset['thumb']
        }" alt=""/></span>`;
      }
    }
  },
  elastic: {
    slidesPerView: 2,
    spaceBetween: 15,
    roundLengths: true,
    grabCursor: true,
    slideClass: 'post-preview',
    pagination: {
      el: '.swiper-pagination'
    },
    breakpoints: {
      567: {
        slidesPerView: 1
      },
      791: {
        slidesPerView: 2
      },
    }
  },
  carousel: {
    speed: 5000,
    freeMode: true,
    freeModeMomentum: true,
    grabCursor: true,
    roundLengths: true,
    autoplay: {
      delay: 1000,
      disableOnInteraction: false
    },
    centeredSlides: true,
    spaceBetween: 15,
    loop: true,
    loopedSlides: 0,
    breakpoints: {
      567: {
        slidesPerView: 1
      },
      791: {
        slidesPerView: 2
      },
      1024: {
        slidesPerView: 3
      },
      1560: {
        slidesPerView: 4
      },
      3400: {
        slidesPerView: 5
      }
    }
  },
  popular: {
    init: false,
    speed: 5000,
    roundLengths: true,
    freeMode: true,
    grabCursor: true,
    lazy: {
      loadPrevNext: true,
      elementClass: 'wp-post-image'
    },
    autoplay: {
      delay: 1000,
      disableOnInteraction: false
    },
    centeredSlides: true,
    spaceBetween: 15,
    loop: true,
    loopedSlides: 0,
    width: 162,
    height: 267,
    slidesPerView: 1
  }
};
