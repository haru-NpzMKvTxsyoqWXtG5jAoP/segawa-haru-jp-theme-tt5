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
      // 画像が全部読み込まれたら、ここからMasonryの処理を始める！
      var msnry = new Masonry( elem, {
        // オプション
        itemSelector: '.haru-masonry-gallery-item, .haru-masonry-gallery-item--wide',
        columnWidth: '.haru-grid-sizer',
        gutter: 4, // CSS変数 --gallery-gutter と同期
        percentPosition: true,
        transitionDuration: 0
      });
      
      console.log('Masonry initialized:', msnry);

      // ResizeObserver を使用して要素のサイズ変更を監視
      if (window.ResizeObserver) {
        const resizeObserver = new ResizeObserver(function() {
          clearTimeout(elem.resizeTimeoutId); // デバウンス処理用のタイマーIDを要素に紐付ける
          elem.resizeTimeoutId = setTimeout(function() {
            // レスポンシブ対応：リサイズ時にgutter値を再取得
            const screenWidth = window.innerWidth;
            let gutterVar;
            
            console.log('ResizeObserver triggered layout - Screen width:', screenWidth);
            
            // レイアウト更新（gutterは常に0）
            msnry.layout();
          }, 250); // 250msのデバウンス
        });
        resizeObserver.observe(elem);
      }
    });
  }
}); 