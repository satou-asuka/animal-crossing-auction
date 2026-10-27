<!DOCTYPE html>
<html>
<head>

<?= $this->Html->charset() ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>
    <?= $this->name . '/' . $this->request->action ?>
</title>

<?= $this->Html->meta('icon') ?>

<?= $this->Html->css('base.css') ?>
<?= $this->Html->css('common.css') ?>
<?= $this->Html->css('header.css') ?>
<?= $this->Html->css('home.css') ?>
<?= $this->Html->css('forms.css') ?>
<?= $this->Html->css('mypage.css') ?>
<?= $this->Html->css('auction.css') ?>

<?= $this->fetch('meta') ?>
<?= $this->fetch('css') ?>
<?= $this->fetch('script') ?>

</head>

<body>

<?php
$controller = $this->request->getParam('controller');
$action = $this->request->getParam('action');

//デフォルト
$bgm = 'トップページ.mp3';

if($controller === 'Auction' && 
    in_array($action, ['index', 'view', 'favorites'])){
    $bgm = 'トップページ.mp3';
}elseif ($controller === 'Auction' && 
    in_array($action, ['home', 'home2', 'add', 'edit', 'bid'])){
    $bgm = '出品＆落札.mp3';
}else{
    $bgm = 'マイページ.mp3';

}
?>

<audio id="bgm" loop>
    <source
        src="<?= $this->Url->build('/audio/' . $bgm) ?>"
        type="audio/mpeg"
    >
</audio>

<header class="auction-header">

    <div class="auction-header-title">
        <?= $this->Html->link(
            'Auction! [' . h($authuser['username']) . ']',
            ['action' => 'index']
        ) ?>
    </div>

    <div class="header-buttons">
        <button
            type="button"
            id="bgm-button"
            aria-label="BGMを切り替える"
        >
            BGM <span id="bgm-status">●</span>
        </button>

        <button
            type="button"
            id="menu-button"
            onclick="toggleMenu()"
            aria-label="メニューを開閉する"
        >
            ☰
        </button>
</div>

</header>

<?= $this->Flash->render() ?>

<div class="container clearfix">

    <div class="actions index medium-9 columns content">
        <?= $this->fetch('content') ?>
    </div>

    <nav
        id="actions-sidebar"
        class="auction-sidebar"
        style="display: none;"
    >
        <style>
            .auction-sidebar .side-nav li {
                margin-bottom: 0;
            }

            .auction-sidebar .side-nav li a {
                padding-top: 3px;
                padding-bottom: 3px;
            }
        </style>

        <ul class="side-nav">

            <li class="heading">
                <?= __('MENU') ?>
            </li>

            <li>
                <?= $this->Html->link(
                    __('商品を出品する'),
                    ['action' => 'add']
                ) ?>
            </li>

            <li>
                <?= $this->Html->link(
                    __('商品リストを見る'),
                    ['action' => 'index']
                ) ?>
            </li>

            <li>
                <?= $this->Html->link(
                    __('あなたの入札情報'),
                    ['action' => 'bidding']
                ) ?>
            </li>

            <li>
                <?= $this->Html->link(
                    __('あなたの落札情報'),
                    ['action' => 'home']
                ) ?>
            </li>

            <li>
                <?= $this->Html->link(
                    __('あなたの出品情報'),
                    ['action' => 'home2']
                ) ?>
            </li>

            <li>
                <?=$this->Html->link(
                    __('お気に入り一覧'),
                    ['action' => 'favorites']
                )?>
            </li>

            <li>
                <?=$this->Html->link(
                    __('マイページ'),
                    ['action' => 'mypage']
                )?>
            </li>

        </ul>

        <div class="tanukichi-area">
            <div class="tanukichi-speech" id="tanukichi-speech"></div>

            <?= $this->Html->image(
                'たぬきち.png',
                [
                    'class' => 'tanukichi-image',
                    'alt' => 'たぬきち'
                ]
            ) ?>
        </div>
    </nav>

</div>

<footer>
</footer>

<script>
function toggleMenu() {
    const menu = document.getElementById('actions-sidebar');

    if (menu.style.display === 'none') {
        menu.style.display = 'block';
    } else {
        menu.style.display = 'none';
    }
}

/* =========================
   BGM
========================= */

document.addEventListener('DOMContentLoaded', function () {

    const bgm = document.getElementById('bgm');
    const bgmButton = document.getElementById('bgm-button');
    const bgmStatus = document.getElementById('bgm-status');

    const currentBgm = bgm.querySelector('source').src;

    const bgmEnabled = localStorage.getItem('bgmEnabled');
    const savedBgm = localStorage.getItem('bgmFile');
    const savedTime = parseFloat(localStorage.getItem('bgmTime')) || 0;

     bgm.addEventListener('loadedmetadata', function () {

        if (savedBgm === currentBgm) {
            bgm.currentTime = savedTime;
        } else {
            bgm.currentTime = 0;
        }

        localStorage.setItem('bgmFile', currentBgm);

        if (bgmEnabled === 'true') {

            bgm.play()
                .then(function () {
                    bgmStatus.className = 'playing';
                })
                .catch(function () {
                    bgmStatus.className = 'stopped';
                });

        } else {
            bgmStatus.className = 'stopped';
        }
    });


    // 再生中の時間を保存
    bgm.addEventListener('timeupdate', function () {
        localStorage.setItem('bgmTime', bgm.currentTime);
        localStorage.setItem('bgmFile', currentBgm);
    });


    // BGMボタン
    bgmButton.addEventListener('click', function () {

        if (bgm.paused) {

            bgm.play();

            localStorage.setItem('bgmEnabled', 'true');
            bgmStatus.className = 'playing';

        } else {

            bgm.pause();

            localStorage.setItem('bgmEnabled', 'false');
            bgmStatus.className = 'stopped';
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const speech = document.getElementById('tanukichi-speech');

    if (!speech) {
        return;
    }

    const hour = new Date().getHours();
    let messages = [];

    if (hour < 11) {
        messages = [
            'おはようだなも！',
            '今日もいい朝だなも！',
            '早起きは三文の得だなも！',
        ];
    } else if (hour < 17) {
        messages = [
            'こんにちはだなも！',
            'お昼のぽかぽか陽気だなも！',
            'ベルの使いすぎには注意だなも！'
        ];
    } else {
        messages = [
            'こんばんはだなも！',
            '今日もお疲れさまだなも！',
            '夜更かしには気をつけるだなも！'
        ];
    }

    const randomIndex = Math.floor(Math.random() * messages.length);
    speech.textContent = messages[randomIndex];
});

</script>
</body>
</html>