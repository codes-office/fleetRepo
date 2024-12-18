// ==========================================================================
// Menus 
// ==========================================================================

$('[data-target]').click(function() {
    let dest = $(this).attr("data-target");
    let destEl = $('body').find('#' + dest);
    destEl.toggleClass('active');
    destEl.removeClass('fadeOutDown').addClass('fadeInUp');
    // destEl.toggleClass('fadeOutDown fadeInUp');
    // e.stopPropagation();
});

$('[data-toggle]').click(function() {
    let dest = $(this).attr("data-toggle");
    let destEl = $('body').find('#' + dest);
    destEl.toggleClass('active');
    // destEl.toggleClass('fadeOutDown fadeInUp');
    // e.stopPropagation();
});


$('[data-close]').click(function() {
    let dest = $(this).attr("data-close");
    let destEl = $('body').find('#' + dest);
    destEl.removeClass('fadeInUp').addClass('fadeOutDown');

    // remove active class after 1 second
    setTimeout(function() {
        destEl.removeClass('active');
    }, 500);

    // only if element has class active 
});

// $('body').not('[data-target]').click(function() {
//     $('.js-close-outside.active').removeClass('fadeInUp').addClass('fadeOutDown');
// });

// $(document).click(function() {
//     // $('.js-close-outside.active').removeClass('fadeInUp').addClass('fadeOutDown');
//     if ($(".login-popup").hasClass('active')) {
//         alert(1);
//         // $(".login-popup").hide();
//     }
// });

$(window).on('load', function() {
    if ($(".login-popup").hasClass('active')) {
        $('body').click(function() {
            $(".login-popup").show();
        });
    }
});

// jQuery(document).ready(function() {
$(document).click(function(e) {
    // console.log(e.target);
    if ($(e.target).is('.login-popup,.login-popup-trigger *, .login-popup *')) {} else {
        $('.js-close-outside.active').removeClass('fadeInUp').addClass('fadeOutDown');
    }
});
// });



// $(document).click(function(e) {
//     if (!$(e.target).is('[data-target]')) {
//         $(".js-close-outside").hide();
//     }
// });


// ==========================================================================
// IMAGE CHANGE
// ==========================================================================

// changable icon is parent class like list or link and image is being children of them.


$('.js-changable-icon').hover(function() {
    let light = $(this).find('img').attr('data-light');
    $(this).find('img').attr('src', light);
}, function() {
    let dark = $(this).find('img').attr('data-dark');
    $(this).find('img').attr('src', dark);
});

$('.js-iconChange').click(function() {
    var clicks = $(this).data('clicks');
    if (!clicks) {
        let one = $(this).attr('data-one');
        $(this).attr('src', one);
    } else {
        let two = $(this).attr('data-two');
        $(this).attr('src', two);
    }
    $(this).data("clicks", !clicks);
});



// ==========================================================================
// FORM THINGS
// ==========================================================================

$("label.label-animate").click(function() {
    $(this).next('input').focus();
});

$("input").focus(function() {
    // $(this).prev("label.label-animate").removeClass('zoomOut').addClass('zoomIn fastest');
    $(this).prev("label.label-animate").addClass('label-top stay');
});

$("input").focusout(function() {
    if ($(this).val().length == 0) {
        $(this).prev("label.label-animate").removeClass('label-top stay');
    } else {
        $(this).prev("label.label-animate").addClass('label-top stay');
    }
});

$('.form-group input').each(function(){
    if ($(this).val().length == 0) {
        $(this).prev("label.label-animate").removeClass('label-top stay');
    } else {
        $(this).prev("label.label-animate").addClass('label-top stay');
    }
});

if($("#book-now").prop('checked'))
{
    $(".hide-book-later").slideUp();
}
if($("#book-later").prop('checked'))
{
    $(".hide-book-later").slideDown();
}
// ==========================================================================
// book now / later
// ==========================================================================

$("#book-now").click(function() {
    $(".hide-book-later").slideUp();
});

$("#book-later").click(function() {
    $(".hide-book-later").slideDown();
});



/////////////////////////////////////////////////////////////////////////////

// slick slider

$('.js-vehicle-slider').slick({
    arrows: true,
    prevArrow: $(".slide-left"),
    nextArrow: $(".slide-right"),
    infinite: false,
    speed: 300,
    slidesToShow: 3,
    slidesToScroll: 1,
    responsive: [{
            breakpoint: 1025,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 1
            }
        }, {
            breakpoint: 769,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1
            }
        },
        {
            breakpoint: 480,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1
            }
        }
    ]
});

$('.js-service-slider').slick({
    arrows: true,
    prevArrow: $(".service-slide-prev"),
    nextArrow: $(".service-slide-next"),
    infinite: false,
    speed: 500,
    cssEase: 'ease-in-out',
    slidesToShow: 2,
    slidesToScroll: 2,
    responsive: [{
            breakpoint: 1025,
            settings: {
                slidesToShow: 1,
                infinite: true,
                slidesToScroll: 1
            }
        },
        // {
        //     breakpoint: 480,
        //     settings: {
        //         slidesToShow: 1,
        //         slidesToScroll: 1
        //     }
        // }
    ]
});

$('.vehicle-slider').slick({
    arrows: false,
    autoplay: false,
    speed: 1000,
    dots: true,
    centerMode: true,
    centerPadding: "0px",
    autoplay: true,
    focusOnSelect: true,
    infinite: true,
    slidesToShow: 3,
    slidesToScroll: 1,
    appendDots: '.custom-dots',
    asNavFor: '.vehicle-details-slider',
    responsive: [{
        breakpoint: 1025,
        settings: {
            centerMode: false,
            slidesToShow: 1,
            slidesToScroll: 1
        }
    }, ]
}).on('init reInit afterChange', function(event, slick, currentSlide, nextSlide) {
    var $current = $('.js-vehicle-slide-current');
    var $totalSlides = $('.js-vehicle-slide-total');
    // current slide and total slide
    var i = (currentSlide ? currentSlide : 0) + 1;
    $current.text(i);
    $totalSlides.text(slick.slideCount);
});


var slides = $('.vehicle-detail');
$('.vehicle-details-slider').slick({
    arrows: false,
    autoplay: false,
    speed: 200,
    dots: false,
    fade: true,
    infinite: true,
    slidesToShow: 1,
    slidesToScroll: 1,
    asNavFor: '.vehicle-slider'
}).on('beforeChange', function(event, slick, currentSlide, nextSlide) {
    slides.removeClass('fadeInUp');
    slides.eq(nextSlide).addClass('fadeInUp');
});





$('.js-testimonial-slider').slick({
    arrows: false,
    autoplaySpeed: 1000,
    fade: true,
    autoplay: true,
    speed: 1000,
    dots: true,
    infinite: false,
    slidesToShow: 1,
    slidesToScroll: 1,
    appendDots: '.testimonial-dots',
});

// ==========================================================================
// 
// ==========================================================================