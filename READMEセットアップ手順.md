# あつまれ どうぶつの森 オークション

CakePHP 3で作成したオークションサイトです。Dockerを使用してローカル環境で起動します。

## 主な機能

- ユーザー登録・ログイン
- 商品の出品・編集
- 商品検索・カテゴリ検索・並び替え
- 入札・落札
- お気に入り登録
- 落札者と出品者へのメール通知
- cronによる終了済みオークションの定期処理

## 必要なもの

- Git
- Docker
- Docker Compose
- メール通知を利用する場合は、利用者自身のSMTPアカウント

## セットアップ

### 1. リポジトリを取得する

```bash
git clone https://github.com/satou-asuka/animal-crossing-auction.git
cd animal-crossing-auction
```

### 2. ローカル設定ファイルを作成する

`config/app.php` にはDB接続情報やメール認証情報を設定するため、GitHubには含めていません。ひな形をコピーして作成します。

```bash
cp config/app.default.php config/app.php
```

### 3. DB接続を設定する

`config/app.php` の `Datasources.default` を、`docker-compose.yml` のMySQL設定に合わせて変更します。

Docker内のMySQLへ接続する場合、`host` には通常 `localhost` ではなく、Docker Composeで定義されているDBサービス名を指定します。

### 4. メール通知を設定する

メール通知にはSMTP認証が必要です。リポジトリ所有者のメールアドレスやパスワードはGitHubに含まれていないため、利用者自身のメールアカウントを設定してください。

iCloudメールを使用する場合は、Apple Accountで発行したアプリ用パスワードを使用します。通常のApple Accountパスワードは使用しません。

`config/app.php` の `EmailTransport.default` を次のように設定します。

```php
'EmailTransport' => [
    'default' => [
        'className' => SmtpTransport::class,
        'host' => 'smtp.mail.me.com',
        'port' => 587,
        'timeout' => 30,
        'username' => '自分のiCloudメールアドレス',
        'password' => '自分のアプリ用パスワード',
        'client' => null,
        'tls' => true,
    ],
],
```

送信元アドレスも、自分のメールアドレスに変更します。

```php
'Email' => [
    'default' => [
        'transport' => 'default',
        'from' => ['自分のiCloudメールアドレス' => '島のオークションサイト'],
    ],
],
```

> `config/app.php` はGitの管理対象外です。メールアドレスやアプリ用パスワードを、`app.default.php`、README、ソースコードへ直接記載しないでください。

メール通知を使用しない場合も画面機能は利用できますが、オークション終了時のバッチ処理ではメール送信に失敗する可能性があります。

### 5. Docker環境を起動する

```bash
docker compose up -d --build
```

### 6. Migrationを実行する

```bash
docker compose exec app bin/cake migrations migrate
```

### 7. アプリへアクセスする

- オークションサイト: http://localhost:8080
- phpMyAdmin: http://localhost:8081

## バッチ処理

終了日時を過ぎた商品を確認し、落札情報の登録とメール通知を行います。

手動で動作確認する場合は、次のコマンドを実行します。

```bash
docker compose exec app bin/cake auction
```

cronの状態を確認する場合は、次のコマンドを実行します。

```bash
docker compose exec app service cron status
```

## 終了方法

```bash
docker compose down
```

DBデータも削除して初期化する操作は通常の終了とは異なるため、必要な場合のみ実行してください。

## 注意事項

- GitHubから取得できるのはソースコードとMigrationです。開発者のローカルDBに保存されているユーザー、商品、入札履歴は含まれません。
- `config/app.php`、SMTP認証情報、個人のメールアドレスはGitHubに含めません。
- 初回起動時は、利用者自身でローカル設定とDBを準備する必要があります。
