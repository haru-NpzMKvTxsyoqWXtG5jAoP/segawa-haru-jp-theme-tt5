/**
 * ロゴスクロールアニメーション
 * スクロール時にロゴを縮小して左上に固定
 */
document.addEventListener('DOMContentLoaded', function() {
  const logo = document.querySelector('.haru-main-logo');
  
  if (!logo) return;
  
  let isScrolled = false;
  
  function handleScroll() {
    const scrollY = window.scrollY;
    const shouldShrink = scrollY > 150; // 150px スクロールしたら縮小
    
    if (shouldShrink && !isScrolled) {
      logo.classList.add('scrolled');
      isScrolled = true;
    } else if (!shouldShrink && isScrolled) {
      logo.classList.remove('scrolled');
      isScrolled = false;
    }
  }
  
  // スクロールイベントリスナー（throttle付き）
  let ticking = false;
  window.addEventListener('scroll', function() {
    if (!ticking) {
      requestAnimationFrame(function() {
        handleScroll();
        ticking = false;
      });
      ticking = true;
    }
  });
});