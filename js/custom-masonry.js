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
      
      // アイテムの幅を動的に設定
      var gridSizer = container.querySelector('.haru-grid-sizer');
      var normalItems = container.querySelectorAll('.haru-masonry-gallery-item');
      var wideItems = container.querySelectorAll('.haru-masonry-gallery-item--wide');
      
      // Grid Sizerの幅設定
      var columnWidth = 100 / columns;
      if (gridSizer) {
        gridSizer.style.width = columnWidth + '%';
      }
      
      // 通常アイテムの幅設定
      normalItems.forEach(function(item) {
        item.style.width = columnWidth + '%';
      });
      
      // 幅広アイテムの幅設定（2列分、ただし最大カラム数を超えない）
      var wideWidth = Math.min(2, columns) * columnWidth;
      wideItems.forEach(function(item) {
        item.style.width = wideWidth + '%';
      });
      
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