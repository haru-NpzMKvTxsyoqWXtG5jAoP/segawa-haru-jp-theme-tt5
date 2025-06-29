document.addEventListener('DOMContentLoaded', function () {
  console.log('Masonry script loaded');
  // メイソンリーレイアウトを適用するコンテナ要素を取得
  var elem = document.querySelector('.haru-masonry-gallery');
  console.log('Found masonry container:', elem);
  if (elem) {
    // まず、elem（.haru-masonry-gallery）の中の画像が全部読み込まれるのを待つ
    imagesLoaded( elem, function() {
      // 画像が全部読み込まれたら、ここからMasonryの処理を始める！
      var msnry = new Masonry( elem, {
        // オプション
        itemSelector: '.haru-masonry-gallery-item, .haru-masonry-gallery-item--wide',
        columnWidth: '.haru-grid-sizer',
        gutter: parseInt(getComputedStyle(document.documentElement).getPropertyValue('--gallery-gutter').trim()) || 12,
        percentPosition: true,
        transitionDuration: 0
      });

      // ResizeObserver を使用して要素のサイズ変更を監視
      if (window.ResizeObserver) {
        const resizeObserver = new ResizeObserver(function() {
          clearTimeout(elem.resizeTimeoutId); // デバウンス処理用のタイマーIDを要素に紐付ける
          elem.resizeTimeoutId = setTimeout(function() {
            // console.log('ResizeObserver triggered layout'); // デバッグ用
            msnry.layout();
          }, 250); // 250msのデバウンス
        });
        resizeObserver.observe(elem);
      }
    });
  }
}); 