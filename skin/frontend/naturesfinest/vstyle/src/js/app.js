//=include foundation.js
//=include foundation.dropdown.js
//=include foundation.equalizer.js
//=include foundation.offcanvas.js
//=include slick.min.js
//=include instafeed.min.js

jQuery.noConflict();

jQuery(document).foundation();

jQuery(document).ready(function($) {
	// $('.live-chat-link').on('click', function(event){
 //    event.preventDefault();
 //    // $zopim.livechat.say('');

 //  })

  $('.search-toggle').on('click', function(event){
    event.preventDefault();
    // $(this).children('span').animate('opacity', 0);
    $('.top-bar .search-form').fadeIn();
  })

  jQuery('.home-slider').slick({
    arrows: false,
    autoplay: true,
    dots: true,
    autoplaySpeed: 5000
  });

  var userFeed = new Instafeed({
        get: 'user',
        userId: 2696283323,
        accessToken: '487728087.467ede5.b14a9c5bdfef4495849de00abb3d56ad',
        template: '<div class="medium-2 small-4 columns"><div class="gram"><a href="{{link}}" target="_blank"> <img src="{{image}}"></a></div></div>',
        limit: 6,
        resolution: 'low_resolution'
    });
    userFeed.run();

    $('a.faq-link').on('click', function(event) {
    var target = $(this.href);
    if( target.length ) {
        event.preventDefault();
        $('html, body').animate({
            scrollTop: target.offset().top
        }, 1000);
    }
});

    // ProductMediaManager is outside document.read scope
if (typeof ProductMediaManager !== 'undefined') {

  // Override image zoom in /skin/frontend/rwd/default/js/app.js
  // and prevent the zooming of images on hover
  ProductMediaManager.createZoom = function(image) { return; }

}

  // $('.dropdown-toggle').mouseover(function (){
  //     dropDownFixPosition($(this), $(this).children('ul'));
  // });
  // function dropDownFixPosition(button,dropdown){
  //       var dropDownTop = button.offset().top + button.outerHeight();
  //         dropdown.css('top', dropDownTop + "px");
  //         dropdown.css('left', button.offset().left + "px");
  // }
});