<h3 class="page-title-home"><?= h($user->username) ?> のマイページ</h3>


<div class="mypage-content">
<?= $this->Form->create($user) ?>

<table class="vertical-table profile-table">
    <tr>
        <th>ユーザーID</th>
        <td><?= h($user->id) ?></td>
    </tr>

    <tr>
        <th>ユーザー名</th>
        <td>
            <div class="username-edit-area">
                <?= $this->Form->control('username', [
                    'label' => false,
                    'id' => 'username-input',
                    'value' => $user->username,
                    'disabled' => true
                ]) ?>
            </div>
        </td>
    </tr>

    <tr>
        <th>メールアドレス</th>
        <td>
            <?=$this->Form->controle('email', [
                'label' => false,
                'id' => 'email-input',
                'type' => 'email',
                'placeholder' => '例：tanukichi@pompoko.jp',
                'disabled' => true
            ])?>
        </td>
    </tr>

    <tr>
        <th>電話番号</th>
        <td>
            <?= $this->Form->control('phone', [
                'label' => false,
                'id' => 'phone-input',
                'type' => 'text',
                'placeholder' => '例：090-1234-5678',
                'disabled' => true
            ]) ?>
        </td>
    </tr>

    <tr>
        <th>住所</th>
        <td>
            <?= $this->Form->control('address', [
                'label' => false,
                'id' => 'address-input',
                'type' => 'text',
                'placeholder' => '例：にこにこ島 たぬき商店前',
                'disabled' => true
            ]) ?>
        </td>
    </tr>

    <tr>
        <?php if ($authuser['role'] === 'admin'): ?>
        <th>権限</th>
        <td><?= h($user->role) ?></td>
    </tr>
    <?php endif; ?>
</table>

<div class="profile-buttons">
    <?= $this->Form->button('編集', [
        'type' => 'button',
        'id' => 'edit-button',
        'class' => 'button'
    ]) ?>

    <?= $this->Form->button('保存', [
        'type' => 'submit',
        'id' => 'save-button',
        'class' => 'button',
        'disabled' => true
    ]) ?>
</div>

<?= $this->Form->end() ?>

<div class="mypage-links">
    <p>
        <?= $this->Html->link(
            '自分の落札情報を見る',
            ['action' => 'home']
        ) ?>
    </p>

    <p>
        <?= $this->Html->link(
            '自分の出品情報を見る',
            ['action' => 'home2']
        ) ?>
    </p>

    <p>
        <?= $this->Html->link(
            'ログアウト',
            [
                'controller' => 'Users',
                'action' => 'logout'
            ],
            [
                'confirm' => 'ログアウトしますか？'
            ]
        ) ?>
    </p>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editButton = document.getElementById('edit-button');
    const saveButton = document.getElementById('save-button');

    const inputs = [
        document.getElementById('username-input'),
        document.getElementById('email-input'),
        document.getElementById('phone-input'),
        document.getElementById('address-input')
    ];

    editButton.addEventListener('click', function () {
        inputs.forEach(function (input) {
            input.disabled = false;
        });

        saveButton.disabled = false;
        editButton.disabled = true;
        inputs[0].focus();
    });
});
</script>