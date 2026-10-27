
<?php
$this->layout = 'login';
?>

<div class="login-page">
    <div class="login-card">

        <div class="login-header">
            <h1>📦 あつ森オークション</h1>
            <p>LOGIN</p>
        </div>

        <?= $this->Flash->render('auth') ?>

        <?= $this->Form->create() ?>

        <div class="login-field">
            <label for="username">ユーザー名</label>
            <?= $this->Form->input('username', [
                'label' => false,
                'placeholder' => 'ユーザー名を入力'
            ]) ?>
        </div>

        <div class="login-field">
            <label for="password">パスワード</label>
            <?= $this->Form->input('password', [
                'label' => false,
                'placeholder' => 'パスワードを入力'
            ]) ?>
        </div>

        <?= $this->Form->button(__('Login'), [
            'class' => 'login-button'
        ]) ?>

        <?= $this->Form->end() ?>

    </div>
</div>
