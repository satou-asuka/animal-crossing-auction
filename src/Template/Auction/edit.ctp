<h3 class="page-title-home">商品情報を編集する</h3>

<?= $this->Form->create($biditem, [
    'class' => 'item-form',
    'type' => 'file'
]) ?>

<div class="form-card">

    <div class="form-group image-group">
        <label>商品画像</label>

        <div class="image-upload-area">

            <?= $this->Form->control('image_file', [
                'label' => false,
                'type' => 'file',
                'accept' => 'image/*',
                'class' => 'image-file-input',
                'id' => 'image-file'
            ]) ?>

            <label for="image-file" class="image-select-button">
                画像を選ぶ
            </label>

            <span class="file-name" id="file-name">
                <?= !empty($biditem->image)
                    ? h(preg_replace('/^\d+_/', '', $biditem->image))
                    :'画像が選択されていません' ?>
            </span>

        </div>
    </div>

    <div class="seller-info">
        <span class="seller-label">出品者</span>
        <span class="seller-name"><?= h($authuser['username']) ?></span>
    </div>

    <div class="form-group">
        <?= $this->Form->control('name', [
            'label' => '商品名',
            'class' => 'item-input'
        ]) ?>
    </div>

    <div class="form-group">
        <?= $this->Form->control('category', [
            'label' => 'カテゴリ',
            'type' => 'select',
            'class' => 'item-input category-select',
            'options' => [
                '料理' => '料理',
                '魚' => '魚',
                '虫' => '虫',
                '海の幸' => '海の幸',
                '家具' => '家具',
                '服' => '服',
                '素材' => '素材',
                '道具' => '道具'
            ],
            'empty' => 'カテゴリを選択してください'
        ]) ?>

        <?= $this->Form->control('minimum_price', [
            'label' => '最低価格',
            'type' => 'number',
            'min' => 300,
            'step' => 10,
            'class' => 'item-input'
        ]) ?>
    </div>

    <div class="edit-update-area">
    <?= $this->Form->button('更新する', [
        'class' => 'edit-update-button'
    ]) ?>
    </div>
    <?= $this->Form->end() ?>

    <div class="edit-delete-area">
        <?= $this->Form->postLink(
            'この商品を削除する',
            ['action' => 'delete', $biditem->id],
            [
                'confirm' => '本当にこの商品を削除しますか？',
                'class' => 'edit-delete-button'
            ]
        ) ?>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const imageFile = document.getElementById('image-file');
    const fileName = document.getElementById('file-name');

    imageFile.addEventListener('change', function () {

        if (imageFile.files.length > 0) {
            fileName.textContent = imageFile.files[0].name;
        } else {
            fileName.textContent = '画像が選択されていません';
        }

    });

});
</script>