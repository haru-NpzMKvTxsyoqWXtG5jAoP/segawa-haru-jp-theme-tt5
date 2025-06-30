document.addEventListener('DOMContentLoaded', function () {
  console.log('Masonry script loaded - Service Card Aligned approach');
  
  var container = document.querySelector('.haru-masonry-gallery');
  var masonry;
  
  if (container) {
    console.log('Found masonry container');
    
    function initMasonry() {
      var screenWidth = window.innerWidth;
      var columns = 1;
      
      // サービスカードと同じブレイクポイント
      if (screenWidth >= 1800) {
        columns = 4;
      } else if (screenWidth >= 1131) {
        columns = 3;
      } else if (screenWidth >= 718) {
        columns = 2;
      }
      
      console.log('Screen width:', screenWidth, 'Columns:', columns);
      
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
      
      console.log('Masonry initialized with', columns, 'columns');
      return masonry;
    }
    
    imagesLoaded(container, function() {
      console.log('Images loaded, initializing service-card-aligned Masonry...');
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
      
      // デバッグ確認
      setTimeout(function() {
        console.log('=== SERVICE CARD ALIGNED DEBUG ===');
        var items = container.querySelectorAll('.haru-masonry-gallery-item, .haru-masonry-gallery-item--wide');
        var gridSizer = container.querySelector('.haru-grid-sizer');
        
        if (gridSizer) {
          console.log('Grid Sizer width:', window.getComputedStyle(gridSizer).width);
        }
        
        items.forEach(function(item, index) {
          var style = window.getComputedStyle(item);
          console.log('Item', index + 1, ':', {
            position: style.position,
            left: style.left,
            top: style.top,
            width: style.width
          });
        });
        console.log('=== END DEBUG ===');
      }, 1000);
    });
  }
});