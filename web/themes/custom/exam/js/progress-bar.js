(function ($) {
  $(document).ready(function() {
      $('.slick--view--banner--block-1').on('beforeChange', function(event, slick, currentSlide, nextSlide) {
          // Remove the 'slick-previous-active' class from all dots
          $('.banner-line li').removeClass('slick-previous-active');

          // Loop through each dot and add 'slick-previous-active' to those below the next slide
          $('.banner-line li').each(function(index) {
              if (index < nextSlide) {
                  $(this).addClass('slick-previous-active');
              }
          });
      });
  });
})(jQuery);
