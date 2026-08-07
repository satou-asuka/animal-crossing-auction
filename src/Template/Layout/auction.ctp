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
<?= $this->Html->css('auction.css') ?>

<?= $this->fetch('meta') ?>
<?= $this->fetch('css') ?>
<?= $this->fetch('script') ?>

</head>

<body>

<header class="auction-header">

    <div class="auction-header-title">
        <?= $this->Html->link(
            'Auction! [' . h($authuser['username']) . ']',
            ['action' => 'index']
        ) ?>
    </div>

    <button
        type="button"
        id="menu-button"
        onclick="toggleMenu()"
        aria-label="メニューを開閉する"
    >
        ☰
    </button>

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
    } else if (hour < 18) {
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