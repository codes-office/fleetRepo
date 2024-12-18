
// Start Window Load Function
(function ($, window, document, undefined) {

    'use strict';

    //*********************************************
    //  REVOLUTION SLIDER FOR HOME
    //*********************************************

    // var tpj=jQuery;
    // var revapi2;
    // if(tpj("#home_slider").revolution == undefined){
    //     revslider_showDoubleJqueryError("#home_slider");
    // }else{
    //     revapi2 = tpj("#home_slider").show().revolution({
    //         sliderType:"standard",
    //         jsFileLocation:"js/revolutionslider/",
    //         sliderLayout:"fullscreen",
    //         delay:7000,
    //         navigation: {
    //             arrows: {
    //                 style: "uranus",
    //                 enable: true,
    //                 hide_onmobile: true,
    //                 hide_onleave: true,
    //                 tmp: '',
    //                 left: {
    //                     h_align: "left",
    //                     v_align: "center",
    //                     h_offset: 0,
    //                     v_offset: 0
    //                 },
    //                 right: {
    //                     h_align: "right",
    //                     v_align: "center",
    //                     h_offset: 0,
    //                     v_offset: 0
    //                 }
    //             },
    //             bullets: {
    //                 enable: true,
    //                 hide_onmobile: true,
    //                 style: "hades",
    //                 hide_onleave: false,
    //                 direction: "horizontal",
    //                 h_align: "right",
    //                 v_align: "bottom",
    //                 h_offset: 90,
    //                 v_offset: 27,
    //                 space: 8,
    //                 tmp: '<span class="tp-bullet-image"></span>'
    //             },
    //             touch: {
    //                 touchenabled: "on",
    //                 swipe_threshold: 75,
    //                 swipe_min_touches: 1,
    //                 swipe_direction: "horizontal",
    //                 drag_block_vertical: false
    //             }
    //         },
    //         responsiveLevels:[1170,860,640,480],
    //         visibilityLevels:[1170,860,640,480],
    //         gridwidth:[1170,860,640,480],
    //         gridheight:1000,
    //         parallax: {
    //             type:"scroll",
    //             origo:"slidercenter",
    //             speed:2000,
    //             levels:[2,3,4,5,8,10,12,16,30],
    //         },
    //         shadow:0,
    //         spinner:"off",
    //         stopLoop:"off",
    //         stopAfterLoops:-1,
    //         stopAtSlide:-1,
    //         disableProgressBar:"on",
    //         shuffle:"off",
    //     });
    // }

    //*********************************************
    //  PORTFOLIO SECTION
    //*********************************************

    // init cubeportfolio
    $('#projects').cubeportfolio({
        filters: '#project-filters',
        search: '#project-search',
        loadMore: '#more-projects',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'foldLeft',
        gapHorizontal: 25,
        gapVertical: 25,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1500,
            cols: 3,
        }, {
            width: 1100,
            cols: 3,
        }, {
            width: 700,
            cols: 2
        }, {
            width: 480,
            cols: 1,
            options: {
                caption: '',
                gapHorizontal: 25,
                gapVertical: 10,
            }
        }],
        caption: 'zoom',
        displayType: 'fadeIn',
        displayTypeSpeed: 100,

        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',

        // singlePage popup
        singlePageDelegate: '.cbp-singlePage',
        singlePageDeeplinking: true,
        singlePageStickyNavigation: true,
        singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>',
        singlePageCallback: function (url, element) {
            // to update singlePage content use the following method: this.updateSinglePage(yourContent)
            var t = this;

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'html',
                timeout: 30000
            })
                .done(function (result) {
                    t.updateSinglePage(result);
                })
                .fail(function () {
                    t.updateSinglePage('AJAX Error! Please refresh the page!');
                });
        },
    });

    //*********************************************
    //  CLIENTS
    //*********************************************
    // init cubeportfolio
    $('#js-grid-clients').cubeportfolio({
        layoutMode: 'slider',
        drag: true,
        auto: true,
        autoTimeout: 3000,
        autoPauseOnHover: true,
        showNavigation: false,
        showPagination: false,
        rewindNav: true,
        scrollByPage: false,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1500,
            cols: 5,
        }, {
            width: 1100,
            cols: 5,
        }, {
            width: 800,
            cols: 4,
        }, {
            width: 480,
            cols: 2,
        }],
        gapHorizontal: 10,
        gapVertical: 5,
        caption: 'opacity',
        displayType: 'fadeIn',
        displayTypeSpeed: 100,
    });




    // End Function
})(jQuery, window, document);
