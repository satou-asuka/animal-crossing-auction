<h3 class="page-title-home"><?= h($authuser['username']) ?> さんの出品した商品一覧</h3>

<div class="item-count">
    🍇 出品数 <?= count($biditems) ?>件
</div>

<table  class="exhibit-table" cellpadding="0" cellspacing="0">
<thead>
    <tr>
        <th scope="col"><?= $this->Paginator->sort('id') ?></th>
        <th class="main" scope="col"><?= $this->Paginator->sort('name', '商品名') ?></th>
        <th scope="col">状態</th>
        <th scope="col">入札件数</th>
        <th scope="col">メッセージ</th>
    </tr>
</thead>

<tbody>
    <?php foreach ($biditems as $biditem): ?>
    <tr>
        <td><?= h($biditem->id) ?></td>

        <td>
            <?= $this->Html->link(
                h($biditem->name),
                ['action' => 'edit', $biditem->id],
                ['class' => 'item-name-link']
            ) ?>
        </td>

        <td>
            <?php if ($biditem->status_label === '出品中'): ?>

                <span class="status-badge status-selling">
                    🌱 出品中
                </span>

            <?php elseif ($biditem->status_label === '落札済み'): ?>

                <span class="status-badge status-sold">
                    🎉 落札済み
                </span>

            <?php else: ?>

                <span class="status-ended">
                    終了（落札なし）
                </span>

            <?php endif; ?>
        </td>

        <td>
            <?= h($biditem->bid_count) ?>件
        </td>

        <td class="actions">
            <?php if (!empty($biditem->bidinfo) && !empty($biditem->bidinfo->id)): ?>
                <div class="message-icon-wrapper">
                    <?= $this->Html->link(
                        '✉️ メッセージを見る',
                        ['action' => 'msg', $biditem->bidinfo->id],
                        ['class' => 'message-icon']
                    ) ?>
                </div>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>
</table>

<div class="paginator">
    <ul class="pagination">
        <?= $this->Paginator->first('<< ' . __('first')) ?>
        <?= $this->Paginator->prev('< ' . __('previous')) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next(__('next') . ' >') ?>
        <?= $this->Paginator->last(__('last') . ' >>') ?>
    </ul>
</div>

<h6><?= $this->Html->link(__('<< 落札情報に戻る'), ['action' => 'home']) ?></h6>