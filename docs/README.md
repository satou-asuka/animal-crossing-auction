# 島のオークション：紹介サイト

既存のCakePHP製アプリを紹介する、ビルド不要の静的サイトです。
HTML・CSS・JavaScriptだけを使用し、外部フォント・CDN・解析ツール・追加ライブラリは使用していません。

## ローカルで確認する

リポジトリのルートで実行します。

```sh
python3 -m http.server 8000 --directory docs --bind 127.0.0.1
```

ブラウザーで `http://127.0.0.1:8000/` を開きます。終了は `Ctrl+C`。
HTMLを直接開いても本文は読めますが、動画の字幕確認などはHTTP配信で確認してください。
画像・動画・字幕が未追加の段階では、開発者ツールに素材の404が出ます。これは準備中表示に切り替えるための読み込み確認です。

## ファイル構成

```text
docs/
  index.html            紹介ページ
  css/style.css         配色、レイアウト、モバイル・印刷対応
  js/main.js            実画面・動画の読み込みと未追加表示
  images/README.md      実画面キャプチャの一覧・追加手順
  videos/README.md      動画・字幕の追加手順
  AI_DEVELOPMENT.md     今回のAI活用記録
  SOURCE_REVIEW.md      ソース調査の根拠・制約
  README.md             このファイル
  .nojekyll             Jekyllによる処理を省略
```

## 実画面・動画を追加する

- [画像の追加手順](images/README.md) の名前でPNGを配置します。ページ内のリンクや枠を編集する必要はありません。
- [動画の追加手順](videos/README.md) に従いMP4と任意の字幕を配置します。
- 架空の画面、AI生成した画面、HTMLで再現した画面を実アプリのキャプチャとして使わないでください。
- ファイルが存在しない、またはデコードできない場合は準備中表示を維持します。
- JavaScriptを無効にした場合も紹介本文・構成図・GitHubリンクは読めます。メディアは準備中表示となります。

### push後にGitHubの画面から追加・編集する

画像はクリップボードからHTMLへ貼り付けず、PNGファイルとして追加します。

1. 実際のCakePHPアプリをブラウザーで開き、スクリーンショットを撮影します。
2. 例として、商品一覧の画像は `auction-list.png` という名前で保存します。
3. GitHubでこのリポジトリの `docs/images/` を開き、**Add file → Upload files** で画像をアップロードします。
4. **Commit changes** で保存します。HTMLを変更せず、対応する画面枠へ表示されます。

文章はGitHubで `docs/index.html` を開き、鉛筆の編集ボタンから変更して **Commit changes** で保存できます。
READMEやAI活用記録も同様です。公開元のmain・docsに変更が入ると、設定済みのPagesの更新対象になります。
ブラウザーからのアップロードは1ファイル25 MiBまでです。動画が大きい場合は圧縮するかローカルからGitで追加してください。
GitHub上で編集した後にローカルでも作業を続ける場合は、先に変更を取り込んでから編集します。

手順の参考：[GitHub公式・ファイルの追加](https://docs.github.com/en/repositories/working-with-files/managing-files/adding-a-file-to-a-repository)、[ファイルの編集](https://docs.github.com/en/repositories/working-with-files/managing-files/editing-files)。

## 掲載内容の確認

ソース調査と実行確認を区別しています。[SOURCE_REVIEW.md](SOURCE_REVIEW.md) を参照してください。
制作の個人的な動機や開発期間は未確認です。必要な場合は、制作者本人の事実に沿って追記してください。

マイページは表示の誤記・Migrationの不足があるため、現在のギャラリーには掲載していません。
`mypage.png` を配置するだけでは表示されません。実動作確認後に、`index.html` の既存の `figure` と同じ形式で追加し、準備中・未確認に関する記載も見直してください。

## GitHub Pagesで公開する

初回のサイト制作ではcommit・pushを行わず、その後の制作者からの依頼で `docs/` のcommit・pushが許可されました。Pagesの設定変更とは別の操作です。以下は初回公開や更新のための手順です。

1. `docs/` の表示と文章を確認し、掲載する実画面・動画を追加します。
2. 素材内のアカウント情報、メールアドレス、住所、電話番号、通知・ブラウザー上の個人情報を確認します。
3. リポジトリにある個人メールアドレスの直接記載も、公開前に所有者が確認してください。今回のサイトには値を転載していません。
4. 確認済みの `docs/` を、自分でcommit・pushします。他の作業中ファイルを誤って含めないようにしてください。
5. リポジトリの **Settings → Pages → Build and deployment** で **Deploy from a branch** を選びます。
6. ブランチ `main`、フォルダー `/docs` を指定して保存します。GitHub側の利用条件・設定により表示名や選択肢が異なる場合があります。
7. GitHubが表示する公開URLにアクセスし、画像・動画・リンク・スマートフォン表示を確認します。

通常のプロジェクトURLの想定値：`https://satou-asuka.github.io/animal-crossing-auction/`
このURLの公開状態は確認していません。正式なURLはGitHub Pagesの設定画面で確認してください。

CSS・JavaScript・画像・動画のパスは相対指定なので、プロジェクト名のサブディレクトリでも配信できます。
サーバーにCakePHP・PHP・MySQLをインストールする必要はありません。
GitHub上のソースへのリンクは `main` を参照しています。ブランチ・所有者・リポジトリ名を変えた場合は更新してください。

## 検証の記録

今回実施した確認と未実施の確認は [AI_DEVELOPMENT.md](AI_DEVELOPMENT.md) に記録します。
メディア未追加でも本文は閲覧できますが、実画面・動画を含む完成版として公開する前に実物の確認が必要です。
