document.addEventListener('DOMContentLoaded', function () {
  console.log('Masonry script loaded - Working Responsive approach');
  
  var container = document.querySelector('.haru-masonry-gallery');
  var masonry;
  
  if (container) {
    console.log('Found masonry container');
    
    function initMasonry() {
      var screenWidth = window.innerWidth;
      var columns = 1;
      
      // レスポンシブ列数設定
      if (screenWidth >= 1024) {
        columns = 3;
      } else if (screenWidth >= 768) {
        columns = 2;
      }
      
      console.log('Screen width:', screenWidth, 'Columns:', columns);
      
      // Masonryを再初期化
      if (masonry) {
        masonry.destroy();
      }
      
      masonry = new Masonry(container, {
        itemSelector: '.haru-masonry-gallery-item, .haru-masonry-gallery-item--wide',
        gutter: 15,
        fitWidth: true,
        percentPosition: false
      });
      
      console.log('Masonry initialized with', columns, 'columns');
      return masonry;
    }
    
    imagesLoaded(container, function() {
      console.log('Images loaded, initializing responsive Masonry...');
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
        console.log('=== RESPONSIVE DEBUG ===');
        var items = container.querySelectorAll('.haru-masonry-gallery-item, .haru-masonry-gallery-item--wide');
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