document.addEventListener('DOMContentLoaded', function () {
  console.log('Masonry script loaded');
  // メイソンリーレイアウトを適用するコンテナ要素を取得
  var elem = document.querySelector('.haru-masonry-gallery');
  console.log('Found masonry container:', elem);
  
  if (elem) {
    // デバッグ：コンテナとアイテムの詳細を確認
    var items = elem.querySelectorAll('.haru-masonry-gallery-item, .haru-masonry-gallery-item--wide');
    var gridSizer = elem.querySelector('.haru-grid-sizer');
    console.log('Found items:', items.length);
    console.log('Found grid sizer:', gridSizer);
    console.log('Container classes:', elem.className);
    
    // まず、elem（.haru-masonry-gallery）の中の画像が全部読み込まれるのを待つ
    imagesLoaded( elem, function() {
      console.log('All images loaded, initializing Masonry...');
      
      // CSS適用を確実にするため少し遅延
      setTimeout(function() {
        // 画像が全部読み込まれたら、ここからMasonryの処理を始める！
        // Grid Sizerの実際の幅を取得
        var gridSizer = elem.querySelector('.haru-grid-sizer');
        var gridWidth = gridSizer.offsetWidth;
        
        console.log('Calculated grid width:', gridWidth);
        
        var msnry = new Masonry( elem, {
          // 縦詰め（テトリス効果）を確実にするための設定
          itemSelector: '.haru-masonry-gallery-item, .haru-masonry-gallery-item--wide',
          columnWidth: '.haru-grid-sizer',
          gutter: 4,
          percentPosition: false, // absolute配置で確実な縦詰め
          transitionDuration: 0,
          originTop: true, // 上から配置開始
          originLeft: true, // 左から配置開始
          initLayout: true // 初期レイアウト実行
        });
        
        console.log('Masonry initialized:', msnry);
        
        // WordPress環境でのCSS干渉を強制的に解除
        function forceResetStyles() {
          var items = elem.querySelectorAll('.haru-masonry-gallery-item, .haru-masonry-gallery-item--wide');
          items.forEach(function(item) {
            // position制約を完全除去
            item.style.position = '';
            item.style.transform = '';
            item.style.top = '';
            item.style.left = '';
            // WordPress独自の制約を除去
            item.style.maxWidth = '';
            item.style.width = '';
            item.style.flex = 'none';
            item.style.order = '';
            // 確実にblock要素にする
            item.style.display = 'block';
            item.style.float = 'left';
          });
          
          // コンテナスタイルも強制設定
          elem.style.position = 'relative';
          elem.style.overflow = '';
          
          console.log('Forced CSS reset completed');
        }
        
        // 初期リセット実行
        forceResetStyles();
        
        // グローバルに保存してデバッグ用にアクセス可能にする
        window.masonryInstance = msnry;
        
        // 初期化直後に段階的なレイアウト更新
        setTimeout(function() {
          forceResetStyles(); // スタイル再リセット
          msnry.layout();
          console.log('First layout update completed');
        }, 100);
        
        // さらに確実にするため追加のレイアウト更新
        setTimeout(function() {
          forceResetStyles(); // 再度スタイルリセット
          msnry.layout();
          console.log('Second layout update completed');
          
          // 最終デバッグ情報を出力
          debugMasonryStatus();
        }, 300);
        
        // デバッグ用関数：Masonryの動作状況を確認
        function debugMasonryStatus() {
          console.log('=== MASONRY DEBUG STATUS ===');
          var items = elem.querySelectorAll('.haru-masonry-gallery-item, .haru-masonry-gallery-item--wide');
          console.log('Total items:', items.length);
          
          items.forEach(function(item, index) {
            var style = window.getComputedStyle(item);
            var rect = item.getBoundingClientRect();
            console.log(`Item ${index + 1}:`, {
              position: style.position,
              left: style.left,
              top: style.top,
              width: style.width,
              height: style.height,
              display: style.display,
              float: style.float,
              rect: {
                x: Math.round(rect.x),
                y: Math.round(rect.y),
                width: Math.round(rect.width),
                height: Math.round(rect.height)
              }
            });
          });
          
          console.log('Container style:', {
            position: elem.style.position,
            height: elem.offsetHeight,
            width: elem.offsetWidth
          });
          console.log('=== END DEBUG ===');
        }

        // ResizeObserver を使用して要素のサイズ変更を監視
        if (window.ResizeObserver) {
          const resizeObserver = new ResizeObserver(function() {
            clearTimeout(elem.resizeTimeoutId); // デバウンス処理用のタイマーIDを要素に紐付ける
            elem.resizeTimeoutId = setTimeout(function() {
              // レスポンシブ対応：リサイズ時にgutter値を再取得
              const screenWidth = window.innerWidth;
              
              console.log('ResizeObserver triggered layout - Screen width:', screenWidth);
              
              // リサイズ時にもスタイルをリセットしてレイアウト更新
              forceResetStyles();
              msnry.layout();
            }, 250); // 250msのデバウンス
          });
          resizeObserver.observe(elem);
        }
        
        // ウィンドウリサイズ時の追加対応
        window.addEventListener('resize', function() {
          clearTimeout(window.masonryResizeTimer);
          window.masonryResizeTimer = setTimeout(function() {
            console.log('Window resize - force layout update with style reset');
            forceResetStyles(); // ウィンドウリサイズ時にもスタイルリセット
            msnry.layout();
          }, 300);
        });
      }, 150); // CSS適用確保のための遅延
    });
  }
}); 