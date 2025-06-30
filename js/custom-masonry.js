document.addEventListener('DOMContentLoaded', function () {
  console.log('Masonry script loaded - Clean CSS approach');
  
  var elem = document.querySelector('.haru-masonry-gallery');
  console.log('Found masonry container:', elem);
  
  if (elem) {
    // 画像読み込み完了を待つ
    imagesLoaded(elem, function() {
      console.log('All images loaded, applying clean Masonry setup...');
      
      // JavaScript側で完全なスタイリング制御
      function applyCleanMasonryStyles() {
        // コンテナスタイル
        elem.style.cssText = '';
        elem.style.position = 'relative';
        elem.style.width = '100%';
        elem.style.maxWidth = '1200px';
        elem.style.margin = '20px auto';
        elem.style.paddingLeft = '50px';
        elem.style.paddingRight = '50px';
        elem.style.paddingTop = '0';
        elem.style.paddingBottom = '0';
        
        // グリッドサイザー
        var gridSizer = elem.querySelector('.haru-grid-sizer');
        if (gridSizer) {
          gridSizer.style.cssText = '';
          gridSizer.style.width = '25%'; // 1900px = 4列対応
          gridSizer.style.height = '0';
          gridSizer.style.visibility = 'hidden';
        }
        
        // アイテムスタイリング
        var items = elem.querySelectorAll('.haru-masonry-gallery-item, .haru-masonry-gallery-item--wide');
        items.forEach(function(item) {
          item.style.cssText = '';
          item.style.width = '25%'; // 1900px = 4列対応
          item.style.marginBottom = '10px';
          item.style.padding = '10px';
          item.style.boxSizing = 'border-box';
          item.style.background = '#f8f8f8';
          item.style.border = '1px solid #ddd';
          item.style.borderRadius = '8px';
          item.style.overflow = 'hidden';
          item.style.cursor = 'pointer';
          item.style.transition = 'transform 0.2s ease';
          
          // 画像スタイリング
          var img = item.querySelector('img');
          if (img) {
            img.style.width = '100%';
            img.style.height = 'auto';
            img.style.display = 'block';
            img.style.borderRadius = '4px';
          }
          
          // テキストスタイリング
          var texts = item.querySelectorAll('h3, p');
          texts.forEach(function(text) {
            text.style.margin = '8px 0';
            text.style.padding = '0 5px';
          });
        });
        
        // ワイドアイテムは2列分
        var wideItems = elem.querySelectorAll('.haru-masonry-gallery-item--wide');
        wideItems.forEach(function(item) {
          item.style.width = '50%'; // 1900px = ワイドアイテムは2列分
        });
        
        console.log('Clean styles applied successfully');
      }
      
      // レスポンシブ対応関数
      function updateResponsiveWidths() {
        var screenWidth = window.innerWidth;
        var gridSizer = elem.querySelector('.haru-grid-sizer');
        var items = elem.querySelectorAll('.haru-masonry-gallery-item');
        var wideItems = elem.querySelectorAll('.haru-masonry-gallery-item--wide');
        
        if (screenWidth >= 1800) {
          // 4列
          if (gridSizer) gridSizer.style.width = '25%';
          items.forEach(function(item) { item.style.width = '25%'; });
          wideItems.forEach(function(item) { item.style.width = '50%'; });
        } else if (screenWidth >= 1131) {
          // 3列
          if (gridSizer) gridSizer.style.width = '33.333%';
          items.forEach(function(item) { item.style.width = '33.333%'; });
          wideItems.forEach(function(item) { item.style.width = '66.666%'; });
        } else if (screenWidth >= 718) {
          // 2列
          if (gridSizer) gridSizer.style.width = '50%';
          items.forEach(function(item) { item.style.width = '50%'; });
          wideItems.forEach(function(item) { item.style.width = '100%'; });
        } else {
          // 1列
          if (gridSizer) gridSizer.style.width = '100%';
          items.forEach(function(item) { item.style.width = '100%'; });
          wideItems.forEach(function(item) { item.style.width = '100%'; });
        }
        
        console.log('Responsive widths updated for screen:', screenWidth);
      }
      
      // スタイル適用
      applyCleanMasonryStyles();
      updateResponsiveWidths();
      
      // Masonry初期化
      setTimeout(function() {
        console.log('Initializing Masonry with clean setup...');
        
        var msnry = new Masonry(elem, {
          itemSelector: '.haru-masonry-gallery-item, .haru-masonry-gallery-item--wide',
          columnWidth: '.haru-grid-sizer',
          gutter: 10,
          percentPosition: false
        });
        
        console.log('Masonry initialized successfully:', msnry);
        
        // ホバーエフェクト
        var items = elem.querySelectorAll('.haru-masonry-gallery-item, .haru-masonry-gallery-item--wide');
        items.forEach(function(item) {
          item.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(0.95)';
          });
          item.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
          });
        });
        
        // レスポンシブ対応
        window.addEventListener('resize', function() {
          clearTimeout(window.masonryResizeTimer);
          window.masonryResizeTimer = setTimeout(function() {
            updateResponsiveWidths();
            msnry.layout();
            console.log('Layout updated after resize');
          }, 250);
        });
        
        // デバッグ情報
        setTimeout(function() {
          console.log('=== CLEAN MASONRY DEBUG ===');
          var items = elem.querySelectorAll('.haru-masonry-gallery-item, .haru-masonry-gallery-item--wide');
          items.forEach(function(item, index) {
            var style = window.getComputedStyle(item);
            console.log(`Item ${index + 1}:`, {
              position: style.position,
              left: style.left,
              top: style.top,
              width: style.width
            });
          });
          console.log('=== END CLEAN DEBUG ===');
        }, 1000);
        
        // グローバル変数として保存
        window.masonryInstance = msnry;
        
      }, 200);
    });
  }
});