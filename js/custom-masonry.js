document.addEventListener('DOMContentLoaded', function () {
  console.log('Masonry script loaded - Ultra Simple approach');
  
  var container = document.querySelector('.haru-masonry-gallery');
  
  if (container) {
    console.log('Found masonry container');
    
    imagesLoaded(container, function() {
      console.log('Images loaded, initializing ultra simple Masonry...');
      
      // 完全にクリーンなMasonry設定
      var masonry = new Masonry(container, {
        itemSelector: '.haru-masonry-gallery-item, .haru-masonry-gallery-item--wide',
        gutter: 0,
        fitWidth: true,
        percentPosition: true
      });
      
      console.log('Ultra simple Masonry initialized:', masonry);
      
      // デバッグ確認
      setTimeout(function() {
        console.log('=== ULTRA SIMPLE DEBUG ===');
        var items = container.querySelectorAll('.haru-masonry-gallery-item, .haru-masonry-gallery-item--wide');
        items.forEach(function(item, index) {
          var style = window.getComputedStyle(item);
          console.log('Item', index + 1, ':', {
            position: style.position,
            left: style.left,
            top: style.top,
            width: style.width,
            height: style.height
          });
        });
        console.log('Container style:', {
          width: window.getComputedStyle(container).width,
          position: window.getComputedStyle(container).position
        });
        console.log('=== END DEBUG ===');
      }, 1000);
    });
  }
});