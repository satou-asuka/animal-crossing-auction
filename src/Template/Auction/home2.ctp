<h3 class="page-title-home"><?= h($authuser['username']) ?> さんの出品した商品一覧</h3>

<div class="item-count">
    🍇 出品数 <?= count($biditems) ?>件
</div>

<table cellpadding="0" cellspacing="0">
<thead>
    <tr>
        <th scope="col"><?= $this->Paginator->sort('id') ?></th>
        <th class="main" scope="col"><?= $this->Paginator->sort('name') ?></th>
        <th scope="col"><?= $this->Paginator->sort('created') ?></th>
        <th scope="col" class="actions"><?= __('Actions') ?></th>
    </tr>
</thead>

<tbody>
    <?php foreach ($biditems as $biditem): ?>
    <tr>
        <td><?= h($biditem->id) ?></td>

        <td><?= h($biditem->name) ?></td>

        <td><?= h($biditem->created) ?></td>
        
        <td class="actions">
            <?php if (!empty($biditem->bidinfo)): ?>
                <?= $this->Html->link(
                    'メッセージを見る',
                    ['action' => 'msg', $biditem->bidinfo->id]
                ) ?>
            <?php else: ?>
                メッセージなし
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