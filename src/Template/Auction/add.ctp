<h3 class="page-title-home">商品を出品する</h3>

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
            画像が選択されていません
        </span>

    </div>
</div>

    <div class="seller-info">
        <span class="seller-label">出品者</span>
        <span class="seller-name"><?= h($authuser['username']) ?></span>
    </div>

    <?= $this->Form->hidden('user_id', [
        'value' => $authuser['id']
    ]) ?>

    <?= $this->Form->hidden('finished', [
        'value' => 0
    ]) ?>

    <div class="form-group">
        <label>
            商品名
            <span class="required-label">※必須</span>
        </label>

        <?= $this->Form->control('name', [
            'label' => false,
            'placeholder' => '例：木の枝5束',
            'class' => 'item-input',
            'required' => 'true'
        ]) ?>

    </div>

    <div class="form-group">
        <label>
            カテゴリ
            <span class="required-label">※必須</span>
        </label>

        <?= $this->Form->control('category', [
            'label' => false,
            'type' => 'select',
            'options' => [
                '料理' => '料理',
                '魚' => '魚',
                '虫' => '虫',
                '海の幸' => '海の幸',
                '家具' => '家具',
                '服' => '服'
            ],
            'empty' => 'カテゴリを選択してください',
            'class' => 'category-select',
            'required' => true
        ]) ?>

    </div>

    <div class="form-group">
        <?= $this->Form->control('minimum_price', [
            'label' => '最低価格',
            'type' => 'number',
            'min' => 300, 
            'step' => 10,
            'placeholder' => '例：500',
            'class' => 'item-input'
        ])?>

        <p class="form-note">
            ※未入力の場合は300ベルで出品されます。
        </p>
    </div>

    <div class="form-group endtime-group">
        <div class= "endtime-area">
            <label>終了日時</label>

            <div class="endtime-options">
                <button type="button" class="endtime-button" data-days="1">
                    1日後
                </button>

                <button type="button" class="endtime-button" data-days="2">
                    2日後
                </button>

                <button type="button" class="endtime-button" data-days="3">
                    3日後
                </button>

                <button type="button" class="endtime-button" data-days="5">
                    5日後
                </button>

                <button type="button" class="endtime-button" data-days="7">
                    7日後
                </button>

                <button type="button" class="endtime-button" id="custom-endtime">
                    自分で設定
                </button>
            </div>
        </div>

        <div id="custom-endtime-area" style="display:none;">
            <?= $this->Form->control('endtime', [
                'label' => false,
                'type' => 'datetime-local',
                'class' => 'endtime-input',
                'id' => 'endtime',
                'min' => date('Y-m-d\TH:i'),
            ]) ?>
    </div>
</div>

<div class="form-actions">
    <?= $this->Form->button('出品する', [
        'class' => 'submit-item-button'
    ]) ?>
</div>

</div>

<?= $this->Form->end() ?>

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

    const buttons = document.querySelectorAll('.endtime-button');
    const customButton = document.getElementById('custom-endtime');
    const customArea = document.getElementById('custom-endtime-area');
    const endtimeInput = document.getElementById('endtime');

    buttons.forEach(function (button) {

        button.addEventListener('click', function () {

            buttons.forEach(function (btn) {
                btn.classList.remove('selected');
            });

            button.classList.add('selected');

            if (button === customButton) {
                customArea.style.display = 'block';
                return;
            }

            customArea.style.display = 'none';

            const days = Number(button.dataset.days);
            const date = new Date();

            date.setDate(date.getDate() + days);

            const year = date.getFullYear();

            const month = String(
                date.getMonth() + 1
            ).padStart(2, '0');

            const day = String(
                date.getDate()
            ).padStart(2, '0');

            const hour = String(
                date.getHours()
            ).padStart(2, '0');

            const minute = String(
                date.getMinutes()
            ).padStart(2, '0');

            endtimeInput.value =
                year + '-' +
                month + '-' +
                day + 'T' +
                hour + ':' +
                minute;
        });
    });
});
</script>