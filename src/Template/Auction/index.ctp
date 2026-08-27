<h2 class="page-title">🍃 島のフリーマーケット</h2>
<h3 class="subtitle">ただいま出品中のアイテムはこちらだなも！</h3>


<div class="search-area">
    <?= $this->Form->create(null, [
        'type' => 'get',
        'url' => ['action' => 'index'],
        'class' => 'search-form'
    ]) ?>

    <!-- 1段目 -->
    <div class="search-row-top">

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

    </div>

    <!-- 2段目 -->
    <div class="search-row-bottom">

        <div class="category-area">
            <label for="category">カテゴリ</label>
            <?= $this->Form->select('category', [
                '料理' => '料理',
                '魚' => '魚',
                '虫' => '虫',
                '海の幸' => '海の幸',
                '家具' => '家具',
                '服' => '服',
                '素材' => '素材',
                '道具' => '道具'
            ], [
                'id' => 'category',
                'value' => $this->request->getQuery('category'),
                'class' => 'sort-select',
                'empty' => 'すべて'
            ])?>
    </div>

        <div class="sort-area">
            <label for="sort">並び替え</label>

            <?= $this->Form->select('sort', [
                'newest' => '新しい順',
                'oldest' => '古い順',
                'endtime_asc' => '終了が近い順',
                'endtime_desc' => '終了が遠い順',
                'price_asc' => '価格が安い順',
                'price_desc' => '価格が高い順',
                'bid_count_desc' => '入札数が多い順'
            ], [
                'id' => 'sort',
                'value' => $sort,
                'class' => 'sort-select',
                'empty' => false
            ]) ?>
        </div>

        <?= $this->Form->button('さがす', [
            'type' => 'submit',
            'class' => 'search-button'
        ]) ?>

    </div>

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
            🔔入札数        
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
            ) ?>
        </td>

        <td>
            <?= h($biditem->finished ? '🌸 終了' : '🍃 出品中' ) ?>
        </td>

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

        <td>
            <?= h(
                $biditem->endtime->format('m月d日') .
                '（' . $weekday . '）' .
                $biditem->endtime->format('H：i')
            ) ?>
        </td>
        
        <td class="bid-count">
            <?= h($biditem->bid_count) ?>件
        </td>

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