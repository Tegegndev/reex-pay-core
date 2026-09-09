/*-----------------------------------------------------------------

Template Name: Digikash & Deshboard Laravel Script
Author:  Coevs
Author URI: https://codecanyon.net/user/coevs
Version: 1.0.0
Description: Digikash & Deshboard Laravel Script

-------------------------------------------------------------------
CSS TABLE OF CONTENTS
-------------------------------------------------------------------

01. header
02. magnificPopup
03. counter up
04. wow animation
05. nice select
06. swiper slider
07. preloader


------------------------------------------------------------------*/

(function($) {
    "use strict";

    $(document).ready( function() {

        // Sidebar Open
        $(".sidebar__toggle").on("click", function () {
            $(".offcanvas__info").addClass("info-open");
            $(".offcanvas__overlay").addClass("overlay-open");
        });

        // Sidebar Close (when click cross button or overlay)
        $(".offcanvas__close, .offcanvas__overlay").on("click", function () {
            $(".offcanvas__info").removeClass("info-open");
            $(".offcanvas__overlay").removeClass("overlay-open");
        });

        //>> Sticky Header Js Start <<//

        $(window).scroll(function() {
            if ($(this).scrollTop() > 250) {
                $("#header-sticky").addClass("sticky");
            } else {
                $("#header-sticky").removeClass("sticky");
            }
        });

        //>> Video Popup Start <<//
        $(".img-popup").magnificPopup({
            type: "image",
            gallery: {
                enabled: true,
            },
        });

        $('.video-popup').magnificPopup({
            type: 'iframe',
            callbacks: {
            }
        });

        //>> Counterup Start <<//
        $(".count").counterUp({
            delay: 15,
            time: 4000,
        });

        //>> Wow Animation Start <<//
        new WOW().init();

        //>> Nice Select Start <<//
        $('select').niceSelect();

       //>> service Start <<//
      if($('.service-slider').length > 0) {
        const serviceSlider = new Swiper(".service-slider", {
            spaceBetween: 20,
            speed: 2000,
            loop: true,
            autoplay: {
                delay: 1000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: ".array-prev",
                prevEl: ".array-next",
            },
            breakpoints: {
                1199: {
                    slidesPerView: 4,
                },
                991: {
                    slidesPerView: 3,
                },
                767: {
                    slidesPerView: 2,
                },
                575: {
                    slidesPerView: 1,
                },
                0: {
                    slidesPerView: 1,
                },
            },
        });
      }

      //>> brand Start <<//
      if($('.brand-slider').length > 0) {
        const brandSlider = new Swiper(".brand-slider", {
            spaceBetween: 20,
            speed: 2000,
            loop: true,
            autoplay: {
                delay: 1000,
                disableOnInteraction: false,
            },
            breakpoints: {
                1199: {
                    slidesPerView: 8,
                },
                991: {
                    slidesPerView: 5,
                },
                767: {
                    slidesPerView: 4,
                },
                575: {
                    slidesPerView: 3,
                },
                400: {
                    slidesPerView: 2,
                },
                0: {
                    slidesPerView: 1,
                },
            },
        });
      }

      //>> testimonial Start <<//
      if($('.testimonial-swiper').length > 0) {
        const testimonialSwiper = new Swiper(".testimonial-swiper", {
            spaceBetween: 30,
            speed: 2000,
            loop: true,

            autoplay: {
                delay: 1000,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".dot",
                clickable: true,
            },
            breakpoints: {
                1199: {
                    slidesPerView: 3,
                },
                991: {
                    slidesPerView: 3,
                },
                767: {
                    slidesPerView: 2,
                },
                575: {
                    slidesPerView: 1,
                },
                0: {
                    slidesPerView: 1,
                },
            },
        });
      }




    }); // End Document Ready Function

    function loader() {
        $(window).on('load', function() {
            // Animate loader off screen
            $(".preloader").addClass('loaded');
            $(".preloader").delay(600).fadeOut();
        });
    }

    loader();


    // Key for localStorage
    var consentKey = 'digikash_cookie_consent_v1';

    function showConsent() {
        $('#cookieConsent').fadeIn(250); // nice fade in
    }
    function hideConsent() {
        $('#cookieConsent').fadeOut(200);
    }
    function acceptConsent() {
        localStorage.setItem(consentKey, 'accepted');
        hideConsent();
    }
    function rejectConsent() {
        localStorage.setItem(consentKey, 'rejected');
        hideConsent();
    }

    // Run on page ready
    $(function() {
        // Only show if not previously accepted/rejected
        if (!localStorage.getItem(consentKey)) {
            setTimeout(showConsent, 900); // slight delay for UX
        }
        // Button bindings (check existence to avoid JS errors)
        $('#cookieAccept').on('click', acceptConsent);
        $('#cookieReject').on('click', rejectConsent);
    });


})(jQuery); // End jQuery

