<h3 class="page-title">商品を出品する</h3>

<?= $this->Form->create($biditem, [
    'class' => 'item-form'
]) ?>

<div class="form-card">

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
        <?= $this->Form->control('name', [
            'label' => '商品名',
            'placeholder' => '例：木の枝5束',
            'class' => 'item-input'
        ]) ?>
    </div>

    <div class="form-group">
        <?= $this->Form->control('endtime', [
            'label' => '終了日時',
            'class' => 'endtime-input'
        ]) ?>
    </div>

</div>

<div class="form-actions">
    <?= $this->Form->button('出品する', [
        'class' => 'submit-item-button'
    ]) ?>
</div>

<?= $this->Form->end() ?>