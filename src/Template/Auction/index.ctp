<h2 class="page-title">🍃 島のフリーマーケット</h2>
<h3 class="subtitle">ただいま出品中のアイテムはこちらだなも！</h3>


<div class="search-area">
    <?= $this->Form->create(null, [
        'type' => 'get',
        'url' => ['action' => 'index'],
        'class' => 'search-form'
    ]) ?>

    <div class="search-input-wrap">
        <span class="search-leaf">🍃</span>

        <?= $this->Form->control('keyword', [
            'label' => false,
            'value' => $keyword,
            'placeholder' => '商品名を入力してください',
            'class' => 'search-input',
            'templates' => [
                'inputContainer' => '{{content}}'
            ]
        ]) ?>
    </div>

    <div class="sort-area">
        <label for="sort">並び替え</label>

        <?= $this->Form->select('sort', [
            "newest" => "新しい順",
            "oldest" => "古い順",
            "endtime_asc" => "終了が近い順",
            "endtime_desc" => "終了が遠い順"
        ],[
            "id" => "sort",
            "value" => $sort,
            "class" => "sort-select",
            "empty" => false
        ]) ?>
    </div>

    <?= $this->Form->button('さがす', [
        'type' => 'submit',
        'class' => 'search-button'
    ]) ?>

    <?= $this->Form->end() ?>
</div>

<table class="auction-table" cellpadding="0" cellspacing="0">
<thead>
    <tr>
        <th class="main" scope="col">
            <?= $this->Paginator->sort('name', '🍃 商品') ?>
        </th>

        <th class="small" scope="col">
            <?= $this->Paginator->sort('finished', '🌸 状態') ?>
        </th>

        <th class="small" scope="col">
            <?= $this->Paginator->sort('endtime', '🕒 終了日時') ?>
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
    <?php foreach ($auction as $biditem): ?>
    <tr>
        <td>
            <?= $this->Html->link(
                h($biditem->name),
                ['action' => 'view', $biditem->id]
            ) ?></td>
        <td><?= h($biditem->finished ? '🌸 終了' : '🍃 出品中' ) ?></td>
        <td><?= h($biditem->endtime) ?></td>
        <td class="favorite">
            <?= $this->Form->postLink(
                //biditem->idがfavoriteBiditemIdsの配列の中にあるか
                in_array($biditem->id, $favoriteBiditemIds)? '★' : '☆',
                ['action' => 'favorite', $biditem->id],
                [
                    'class' => 'favorite-button'
                ]
            ) ?>
        </td>

        <td class="actions">
            <?= $this->Html->link(
                '📦 商品を見る',
                ['action' => 'view', $biditem->id],
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
