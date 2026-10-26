
document.addEventListener("DOMContentLoaded", () => {

  // 左側のメニューボタンをすべて取得
  const menuItems = document.querySelectorAll(".menu-item");

  // 右側の画面をすべて取得
  const screens = document.querySelectorAll(".screen");

  // メニューボタンをクリックしたときの処理
  menuItems.forEach((menuItem) => {

    menuItem.addEventListener("click", () => {

      // クリックされたメニューの画面IDを取得
      const screenId = menuItem.dataset.screen;

      // 表示対象の画面を取得
      const targetScreen = document.getElementById(
        `screen-${screenId}`
      );

      // 対象画面が存在しない場合は何もしない
      if (!targetScreen) {
        return;
      }

      // すべてのメニューからactiveを削除
      menuItems.forEach((item) => {
        item.classList.remove("active");
      });

      // クリックしたメニューにactiveを追加
      menuItem.classList.add("active");

      // すべての画面を非表示にする
      screens.forEach((screen) => {

        screen.classList.remove("active");

        // 非表示にする画面の動画を停止
        const videos = screen.querySelectorAll("video");

        videos.forEach((video) => {
          video.pause();
        });

      });

      // 選択した画面を表示
      targetScreen.classList.add("active");

      // 右側コンテンツのスクロール位置を先頭に戻す
      const mainContent = document.querySelector(".main-content");

      if (mainContent) {
        mainContent.scrollTop = 0;
      }

    });

  });

});
