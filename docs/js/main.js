"use strict";

// Only genuine captures supplied at these paths replace the preparation notices.
let loadedScreens = 0;
const screenSlots = document.querySelectorAll("[data-image]");
const galleryStatus = document.getElementById("gallery-status");
const galleryIntro = document.querySelector("#screens .section-intro");

screenSlots.forEach((slot) => {
  const image = slot.querySelector("img");
  const placeholder = slot.querySelector(".media-placeholder");
  const link = slot.closest("figure").querySelector(".screen-expand");
  const path = slot.dataset.image;

  image.addEventListener("load", () => {
    if (!image.naturalWidth) return;
    image.hidden = false;
    placeholder.hidden = true;
    link.href = path;
    link.hidden = false;
    loadedScreens += 1;
    galleryStatus.textContent = `実画面 ${loadedScreens} / ${screenSlots.length} 枚`;
    galleryIntro.textContent = loadedScreens === screenSlots.length
      ? "実際のアプリで撮影した画面です。画像下のリンクから元の画像をご覧いただけます。"
      : "追加済みの実画面と、これから掲載する画面の役割を紹介しています。未追加の画像は準備中と表示しています。";
  }, { once: true });
  image.addEventListener("error", () => {
    // Missing media is expected until actual captures are added.
    image.hidden = true;
    image.removeAttribute("src");
  }, { once: true });
  // Hidden lazy images may never start loading; load eagerly before revealing.
  image.loading = "eager";
  image.src = path;
});

const video = document.getElementById("demo-video");
const videoPlaceholder = document.getElementById("video-placeholder");

// Leave the notice visible until the browser can read a real video.
video.addEventListener("loadedmetadata", () => {
  video.hidden = false;
  videoPlaceholder.hidden = true;
}, { once: true });
video.addEventListener("error", () => {
  video.hidden = true;
  videoPlaceholder.hidden = false;
  video.removeAttribute("src");
}, { once: true });
video.src = "videos/auction-demo.mp4";

// Captions are optional. Check first so an absent file is not shown as a track.
fetch("videos/auction-demo.vtt", { method: "HEAD" })
  .then((response) => {
    if (!response.ok) return;
    const track = document.createElement("track");
    track.kind = "captions";
    track.label = "日本語";
    track.srclang = "ja";
    track.src = "videos/auction-demo.vtt";
    track.default = true;
    video.appendChild(track);
  })
  .catch(() => { /* Preparation notices also work when opened as a local file. */ });
