<h3 class="page-title-home"><?= h($authuser['username']) ?> さんのお気に入り一覧</h3>

<?php if ($favorites->isEmpty()): ?>

    <p>お気に入りに登録している商品はありません。</p>

<?php else: ?>

<table class="auction-table" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th class="main" scope="col">
                🍃 商品
            </th>

            <th class="small" scope="col">
                🌸 状態
            </th>

            <th class="small" scope="col">
                🕒 終了日時
            </th>

            <th class="small" scope="col">
                ⭐ お気に入り
            </th>

            <th class="small actions" scope="col">
                📦 詳細
            </th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($favorites as $favorite): ?>
            <?php $biditem = $favorite->biditem; ?>

            <tr>
                <td>
                    <?= $this->Html->link(
                        h($biditem->name),
                        [
                            'action' => 'view',
                            $biditem->id
                        ]
                    ) ?>
                </td>

                <td>
                    <?= h(
                        $biditem->finished
                            ? '🌸 終了'
                            : '🍃 出品中'
                    ) ?>
                </td>

                <td>
                    <?= h($biditem->endtime) ?>
                </td>

                <td class="favorite">
                    <?= $this->Form->postLink(
                        '★',
                        [
                            'action' => 'favorite',
                            $biditem->id
                        ],
                        [
                            'class' => 'favorite-button active'
                        ]
                    ) ?>
                </td>

                <td class="actions">
                    <?= $this->Html->link(
                        '📦 商品を見る',
                        [
                            'action' => 'view',
                            $biditem->id
                        ],
                        [
                            'class' => 'view-button'
                        ]
                    ) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php endif; ?>