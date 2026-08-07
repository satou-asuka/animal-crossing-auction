<h3 class="page-title-home"><?= h($authuser['username']) ?> さんの落札した商品一覧</h3>

<div class="item-count">
    🍎 落札数 <?= count($bidinfo) ?>件
</div>

<table cellpadding="0" cellspacing="0">
<thead>
    <tr>
        <th class="small" scope="col">
            <?= $this->Paginator->sort('id', '🏷️ ID') ?>
        </th>

        <th class="main" scope="col">
            <?= $this->Paginator->sort('name', '🍃 商品名') ?>
        </th>

        <th class="small" scope="col">
            <?= $this->Paginator->sort('created', '🕒 落札日時') ?>
        </th>

        <th class="small actions" scope="col">
            📩 取引メッセージ
        </th>
    </tr>
</thead>

<tbody>
<?php foreach ($bidinfo as $info): ?>
<tr>

    <td><?= h($info->id) ?></td>

    <td>
        <?= $this->Html->link(
            h($info->biditem->name),
            ['action' => 'msg', $info->id],
            ['class' => 'product-link']
        ) ?>
    </td>

    <td><?= h($info->created) ?></td>

    <td class="actions">
        <?= $this->Html->link(
            'メッセージを見る',
            ['action' => 'msg', $info->id],
            ['class' => 'view-button']
        ) ?>
    </td>

</tr>
<?php endforeach; ?>
</tbody>
</table>

<div class="paginator">
    <ul class="pagination">
        <?= $this->Paginator->first('<< 最初') ?>
        <?= $this->Paginator->prev('< 前へ') ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next('次へ >') ?>
        <?= $this->Paginator->last('最後 >>') ?>
    </ul>
</div>

<p>
<?= $this->Html->link(
    '📦 出品中のアイテムを見る',
    ['action' => 'home2'],
    ['class' => 'view-button']
) ?>
</p>