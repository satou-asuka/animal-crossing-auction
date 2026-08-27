<h2>「<?= $biditem->name ?>」の情報</h2>

<table class="vertical-table">
<tr>
    <th scope="row">商品画像</th>
    <td>
        <?php if (!empty($biditem->image)) : ?>
            <?= $this->Html->image(
                'biditems/' . $biditem->image,
                [
                    'alt' => $biditem->name,
                    'class' => 'item-image'
                ]
            ) ?>
        <?php else : ?>
            画像なし
        <?php endif; ?>
    </td>
</tr>

<tr>
    <th class="small" scope="row">出品者</th>
    <td><?= $biditem->has('user') ? $biditem->user->username : '' ?></td>
</tr>

<tr>
    <th scope="row">商品名</th>
    <td><?= h($biditem->name) ?></td>
</tr>

<tr>
    <th scope="row">カテゴリ</th>
    <td>
        <?php
        $categoryLabels = [
            '料理' => '料理',
            '魚' => '魚',
            '虫' => '虫',
            '海の幸' => '海の幸',
            '家具' => '家具',
            '服' => '服',
            '素材' => '素材',
            '道具' => '道具'
        ]
        ?>

        <?= h($categoryLabels[$biditem->category] ?? '未設定') ?>
    </td>
</tr>

<tr>
    <th scope="row">最低価格</th>
    <td><?= h($biditem->minimum_price ?? 300) ?>ベル</td>
</tr>

<tr>
    <th scope="row">商品ID</th>
    <td><?= $this->Number->format($biditem->id) ?></td>
</tr>
<tr>
    <?php
    $weekdays = [
        'Sun' => '日',
        'Mon' => '月',
        'Tue' => '火',
        'Wed' => '水',
        'Thu' => '木',
        'Fri' => '金',
        'Sat' => '土'
    ];

    $weekday = $weekdays[$biditem->endtime->format('D')];
    ?>

    <th scope="row">終了時間</th>
    <td>
        <?= h(
            $biditem->endtime->format('m月d日') .
            '（' . $weekday . '）'.
            $biditem->endtime->format('H：i')
            )?>
    </td>
</tr>

<tr>
    <?php
    $weekdays = [
        'Sun' => '日',
        'Mon' => '月',
        'Tue' => '火',
        'Wed' => '水',
        'Thu' => '木',
        'Fri' => '金',
        'Sat' => '土'
    ];

    $weekday = $weekdays[$biditem->created->format('D')];
    ?>

    <th scope="row">投稿時間</th>
    <td>
        <?= h(
            $biditem->created->format('m月d日') .
            '（' . $weekday . '）'.
            $biditem->created->format('H：i')
            )?>
    </td>
</tr>

<tr>
    <th scope="row"><?= __('終了した？') ?></th>
    <td><?= $biditem->finished ? __('Yes') : __('No'); ?></td>
</tr>
</table>

<div class="related">
    <h4><?= __('落札情報') ?></h4>

    <?php if (!empty($biditem->bidinfo)) : ?>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th scope="col">落札者</th>
            <th scope="col">落札金額</th>
            <th scope="col">落札日時</th>
        </tr>

        <tr>
            <td>
            <?= !empty($biditem->bidinfo->user)
                ? h($biditem->bidinfo->user->username)
                : '落札者なし' ?>
            </td>
            <td><?= h($biditem->bidinfo->price) ?>円</td>
            <td><?= h($biditem->endtime) ?></td>
        </tr>
    </table>

    <?php else: ?>
        <p><?= '※落札情報は、ありません。' ?></p>
    <?php endif; ?>
</div>

<div class="related">
    <h4><?= __('入札情報') ?></h4>

    <?php if ($biditem->finished) : ?>

        <p>※入札は、終了しました。</p>

    <?php elseif ($biditem->user_id != $authuser['id']) : ?>

        <div class="bid-button-area">

            <?= $this->Html->image('つぶきち.png', [
                'class' => 'bid-character tsubukichi'
            ]) ?>

            <a
                class="bid-link"
                href="<?= $this->Url->build([
                    'action' => 'bid',
                    $biditem->id
                ]) ?>">
                入札する
            </a>

            <?= $this->Html->image('まめきち.png', [
                'class' => 'bid-character mamekichi'
            ]) ?>

        </div>

    <?php endif; ?>


    <?php if (!empty($bidrequests)) : ?>

        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th scope="col">入札者</th>
                    <th scope="col">金額</th>
                    <th scope="col">入札日時</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($bidrequests as $bidrequest): ?>
                    <tr>
                        <td><?= h($bidrequest->user->username) ?></td>
                        <td><?= h($bidrequest->price) ?>ベル</td>

                        <?php
                        $bidWeekday =
                            $weekdays[$bidrequest->created->format('D')];
                        ?>

                        <td>
                            <?= h(
                                $bidrequest->created->format('m月d日') .
                                '（' . $bidWeekday . '）' .
                                $bidrequest->created->format('H：i')
                            ) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>

        <p>※入札は、まだありません。</p>

    <?php endif; ?>

</div>