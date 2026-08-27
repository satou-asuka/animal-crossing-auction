<h3 class="page-title-home">
    <?= h($authuser['username']) ?> さんの入札中の商品一覧
</h3>

<div class="item-count">
    🍊 入札中 <?= count($biditems) ?>件
</div>

<table class="bidding-table" cellpadding="0" cellspacing="0">
<thead>
    <tr>
        <th scope="col">🏷️ ID</th>
        <th scope="col">🍃 商品名</th>
        <th scope="col">🌸 ステータス</th>
        <th scope="col">🕒 終了日時</th>
        <th scope="col">📦 詳細</th>
    </tr>
</thead>

<tbody>

<?php foreach ($biditems as $biditem): ?>

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

    <tr>

        <td>
            <?= h($biditem->id) ?>
        </td>

        <td>
            <?= $this->Html->link(
                h($biditem->name),
                ['action' => 'view', $biditem->id],
                ['class' => 'product-link']
            ) ?>
        </td>

        <td>
            <?php if ($biditem->bid_status === '最高入札者') : ?>

                <span class="bid-status highest">
                    🍃 最高入札者
                </span>

            <?php elseif ($biditem->bid_status === '上位入札あり') : ?>

                <span class="bid-status overtaken">
                    ⚠ 上位入札あり
                </span>

            <?php elseif ($biditem->bid_status === '落札済み') : ?>

                <span class="bid-status won">
                    🌸 落札済み
                </span>

            <?php else : ?>

                <span class="bid-status lost">
                    落札できませんでした
                </span>

            <?php endif; ?>
        </td>

        <td>
            <?= h(
                $biditem->endtime->format('m月d日') .
                '（' . $weekday . '） ' .
                $biditem->endtime->format('H：i')
            ) ?>
        </td>

        <td class="actions">
            <?= $this->Html->link(
                ' 📦 商品を見る ',
                ['action' => 'view', $biditem->id],
                ['class' => 'view-button']
            ) ?>
        </td>

    </tr>

<?php endforeach; ?>

</tbody>
</table>

<p>
    <?= $this->Html->link(
        '🌸 落札した商品を見る',
        ['action' => 'home'],
        ['class' => 'view-button']
    ) ?>
</p>