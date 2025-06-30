document.addEventListener('DOMContentLoaded', function () {
  console.log('Masonry script loaded - Fixed CSS + JS approach');
  
  var container = document.querySelector('.haru-masonry-gallery');
  var masonry;
  
  if (container) {
    console.log('Found masonry container');
    
    function initMasonry() {
      // Masonryを再初期化
      if (masonry) {
        masonry.destroy();
      }
      
      masonry = new Masonry(container, {
        itemSelector: '.haru-masonry-gallery-item, .haru-masonry-gallery-item--wide',
        columnWidth: '.haru-grid-sizer',
        gutter: 15,
        percentPosition: true
      });
      
      console.log('Masonry initialized');
      return masonry;
    }
    
    imagesLoaded(container, function() {
      console.log('Images loaded, initializing Masonry...');
      initMasonry();
      
      // リサイズイベント
      var resizeTimer;
      window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
          console.log('Resize triggered, reinitializing Masonry...');
          initMasonry();
        }, 250);
      });
    });
  }
});