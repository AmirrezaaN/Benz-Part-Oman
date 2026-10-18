import Swiper from 'swiper';
import { Autoplay, EffectFade, FreeMode, Navigation, Pagination, Thumbs, A11y } from 'swiper/modules';

export function initSliders() {
    // Hero banner
    document.querySelectorAll('.hero-swiper').forEach((el) => {
        const wrap = el.closest('[data-slider]') || el;
        new Swiper(el, {
            modules: [Autoplay, EffectFade, Navigation, Pagination, A11y],
            effect: 'fade',
            fadeEffect: { crossFade: true },
            loop: el.querySelectorAll('.swiper-slide').length > 1,
            speed: 700,
            autoplay: { delay: 6000, disableOnInteraction: false, pauseOnMouseEnter: true },
            pagination: { el: wrap.querySelector('.swiper-pagination'), clickable: true },
            navigation: { nextEl: wrap.querySelector('[data-next]'), prevEl: wrap.querySelector('[data-prev]') },
        });
    });

    // Endless brand marquee
    document.querySelectorAll('.brands-swiper').forEach((el) => {
        new Swiper(el, {
            modules: [Autoplay, FreeMode],
            slidesPerView: 'auto',
            spaceBetween: 14,
            loop: true,
            speed: 5000,
            freeMode: { enabled: true, momentum: false },
            autoplay: { delay: 0, disableOnInteraction: false, pauseOnMouseEnter: true },
            allowTouchMove: true,
        });
    });

    // Product carousels
    document.querySelectorAll('[data-product-slider]').forEach((wrap) => {
        const el = wrap.querySelector('.swiper');
        new Swiper(el, {
            modules: [Navigation, Autoplay, A11y],
            slidesPerView: 1.25,
            spaceBetween: 14,
            watchOverflow: true,
            navigation: { nextEl: wrap.querySelector('[data-next]'), prevEl: wrap.querySelector('[data-prev]') },
            autoplay: wrap.dataset.autoplay ? { delay: Number(wrap.dataset.autoplay), pauseOnMouseEnter: true, disableOnInteraction: false } : false,
            breakpoints: {
                480: { slidesPerView: 2.1 },
                768: { slidesPerView: 3.1 },
                1024: { slidesPerView: 4 },
                1280: { slidesPerView: 5 },
            },
        });
    });

    // Category chips carousel
    document.querySelectorAll('.categories-swiper').forEach((el) => {
        const wrap = el.closest('[data-slider]') || el;
        new Swiper(el, {
            modules: [Navigation, FreeMode],
            slidesPerView: 'auto',
            spaceBetween: 12,
            freeMode: true,
            navigation: { nextEl: wrap.querySelector('[data-next]'), prevEl: wrap.querySelector('[data-prev]') },
        });
    });

    // Product gallery with thumbnails
    document.querySelectorAll('[data-gallery]').forEach((wrap) => {
        const thumbsEl = wrap.querySelector('.gallery-thumbs');
        const thumbs = thumbsEl ? new Swiper(thumbsEl, {
            modules: [FreeMode],
            slidesPerView: 4.5,
            spaceBetween: 8,
            freeMode: true,
            watchSlidesProgress: true,
        }) : null;
        new Swiper(wrap.querySelector('.gallery-main'), {
            modules: [Navigation, Pagination, Thumbs, A11y],
            spaceBetween: 10,
            pagination: { el: wrap.querySelector('.swiper-pagination'), clickable: true },
            navigation: { nextEl: wrap.querySelector('[data-next]'), prevEl: wrap.querySelector('[data-prev]') },
            thumbs: thumbs ? { swiper: thumbs } : undefined,
        });
    });
}
