<h2>「<?= $biditem->name ?>」の情報</h2>

<?= $this->Form->create($bidrequest) ?>

<fieldset>
    <legend><?= __('※入札を行う') ?></legend>

    <?php
        echo $this->Form->hidden('biditem_id', ['value' => $bidrequest->biditems_id]);
        echo $this->Form->hidden('user_id', ['value' => $bidrequest->user_id]);
        echo $this->Form->control('price', [
            'label' => '入札金額',
            'type' => 'number',
            'min' => $biditem->minimum_price ?? 300,
            'value' => $biditem->minimum_price ?? 300,
            'step' => 10
        ]);
    ?>
    <p class="minimum-price-note">
        ※この商品の最低入札価格は
        <strong><?= h($biditem->minimum_price ?? 300) ?>ベル</strong>
        です。
</fieldset>

<?= $this->Form->button(__('入札'),[
    'class' => 'bid-submit-button'
]) ?>
<?= $this->Form->end() ?>