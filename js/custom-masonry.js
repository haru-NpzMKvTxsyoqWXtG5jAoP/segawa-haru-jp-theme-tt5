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
          // シンプル設定で確実に動作
          itemSelector: '.haru-masonry-gallery-item, .haru-masonry-gallery-item--wide',
          columnWidth: '.haru-grid-sizer',
          gutter: 4,
          percentPosition: true,
          transitionDuration: 0
        });
        
        console.log('Masonry initialized:', msnry);
        
        // グローバルに保存してデバッグ用にアクセス可能にする
        window.masonryInstance = msnry;
        
        // 初期化直後に強制レイアウト更新
        setTimeout(function() {
          console.log('Force layout update after initialization');
          msnry.layout();
        }, 100);

        // ResizeObserver を使用して要素のサイズ変更を監視
        if (window.ResizeObserver) {
          const resizeObserver = new ResizeObserver(function() {
            clearTimeout(elem.resizeTimeoutId); // デバウンス処理用のタイマーIDを要素に紐付ける
            elem.resizeTimeoutId = setTimeout(function() {
              // レスポンシブ対応：リサイズ時にgutter値を再取得
              const screenWidth = window.innerWidth;
              
              console.log('ResizeObserver triggered layout - Screen width:', screenWidth);
              
              // レイアウト更新（gutterは常に0）
              msnry.layout();
            }, 250); // 250msのデバウンス
          });
          resizeObserver.observe(elem);
        }
        
        // ウィンドウリサイズ時の追加対応
        window.addEventListener('resize', function() {
          clearTimeout(window.masonryResizeTimer);
          window.masonryResizeTimer = setTimeout(function() {
            console.log('Window resize - force layout update');
            msnry.layout();
          }, 300);
        });
      }, 150); // CSS適用確保のための遅延
    });
  }
}); 