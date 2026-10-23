# ソース調査記録

調査日：2026-10-23（Asia/Tokyo）。Codexによる静的調査です。
アプリの実行、実データベースへの接続、メール配送、Cronの稼働確認は行っていません。
READMEの記載だけで実装済みと判断せず、Controller・Model・Template・Migrationを照合しました。

## 主な機能と根拠

| 機能 | 根拠ファイル・メソッド | 確認した内容 |
| --- | --- | --- |
| 認証 | [AuctionBaseController](../src/Controller/AuctionBaseController.php)、[UsersController](../src/Controller/UsersController.php) | AuthComponentのForm認証、セッション認証、roleによるコントローラー単位の認可 |
| パスワード | [User](../src/Model/Entity/User.php) | DefaultPasswordHasherを使うセッター、JSON等の出力からpasswordを隠す設定 |
| 商品検索・一覧 | [AuctionController](../src/Controller/AuctionController.php) `index()`、[一覧テンプレート](../src/Template/Auction/index.ctp) | 商品名LIKE、カテゴリ条件、7種類の並び替え、10件単位のページネーション |
| 件数集計 | 同 `index()` | BidrequestsをLEFT JOINし、商品ごとに入札件数をCOUNT。最低価格順と最高入札額順は区別 |
| 商品詳細 | 同 `view()`、[詳細テンプレート](../src/Template/Auction/view.ctp) | 商品・出品者・落札・入札履歴を取得、画像・日時・金額表示 |
| 出品・編集・削除 | 同 `add()`・`edit()`・`delete()`、[出品](../src/Template/Auction/add.ctp)、[編集](../src/Template/Auction/edit.ctp) | ファイル保存、最低価格未入力時300ベル、終了日時プリセット、編集項目の限定、所有者比較 |
| 入札 | 同 `bid()`、[入札テンプレート](../src/Template/Auction/bid.ctp)、[BidrequestsTable](../src/Model/Table/BidrequestsTable.php) | 入札情報保存、自分の商品への入札拒否、出品者へのメール送信処理、金額の整数・必須検証 |
| 入札状況 | 同 `bidding()`、[入札一覧](../src/Template/Auction/bidding.ctp) | 自分・全体の最高額比較、終了状態・落札者による状態表示 |
| お気に入り | 同 `favorite()`・`favorites()`、[FavoritesTable](../src/Model/Table/FavoritesTable.php)、[一覧](../src/Template/Auction/favorites.ctp) | POSTで登録・解除、利用者ごとの取得 |
| 出品・落札一覧 | 同 `home()`・`home2()`、[落札一覧](../src/Template/Auction/home.ctp)、[出品一覧](../src/Template/Auction/home2.ctp) | 自分の履歴取得、出品状態・入札件数表示、メッセージ画面への導線 |
| 取引メッセージ | 同 `msg()`・`deleteMessage()`、[msg.ctp](../src/Template/Auction/msg.ctp) | 落札情報に関連づく投稿保存と履歴、自分の投稿の削除処理 |
| マイページ | 同 `mypage()`、[mypage.ctp](../src/Template/Auction/mypage.ctp) | 更新項目をusername・email・phone・addressに限定、認証情報も更新。ただし下記の不整合あり |
| 終了バッチ | [AuctionShell](../src/Shell/AuctionShell.php) `main()` | finished=0かつendtimeが現在以前の商品を抽出し、最高入札から落札情報作成・メール送信・終了状態更新。入札なしの終了処理もあり |
| 画面演出 | [auctionレイアウト](../src/Template/Layout/auction.ctp) | メニュー開閉、画面別BGM、再生状態・位置をlocalStorageへ保存、時間帯別メッセージ |

商品一覧のカテゴリ選択肢は8種類ですが、出品フォームの選択肢は6種類です。紹介ページは統一された8カテゴリで出品できるとは説明していません。
一覧には終了商品も含むため、「出品中の商品だけを表示」とも説明していません。

## その他の画面

Users・Biditems・Bidrequests・Bidinfo・Bidmessagesには一覧・詳細・作成・編集のCRUD画面があります。
UsersとBidinfoでは認証基底クラス・認可が設定されていますが、他のCRUD Controllerすべてに同等の認証設定があるわけではありません。
Movies・People・Messages・Helloの画面、CakePHPの標準トップ・エラー画面もあります。
これらをオークションの追加機能や完成した管理システムとして紹介していません。
`src/Template/auction/index.ctp` と `src/Template/Auction/index.ctp` の大小文字が異なるパスも存在します。

## バージョンと構成

[composer.json](../composer.json) はPHP `>=5.6`、CakePHP `3.8.*` を指定しています。
[composer.lock](../composer.lock) の直接依存のロック値は以下です。インストール済みバージョンとは限りません。

| 依存 | ロック値 |
| --- | --- |
| cakephp/cakephp | 3.8.13 |
| cakephp/migrations | 2.4.2 |
| cakephp/plugin-installer | 1.3.1 |
| mobiledetect/mobiledetectlib | 2.8.47 |
| cakephp/bake | 1.12.0 |
| cakephp/cakephp-codesniffer | 3.3.0 |
| cakephp/debug_kit | 3.23.0 |
| josegonzalez/dotenv | 3.2.0 |
| phpunit/phpunit | 6.5.14 |
| psy/psysh | v0.12.24 |

PHPUnitやPsySHなど、開発依存にはPHP 7以上等の条件があります。「PHP 5.6で全構成を動作確認済み」とはしていません。
Mobile Detect等は依存定義への掲載と、独自機能での利用実績を区別しています。
[app.default.php](../config/app.default.php) にはMySQLドライバーのひな形があります。DBの実バージョンは不明です。
CSS・JavaScript・CTPによる画面実装を確認しました。標準スケルトンにはFoundation由来のCSSもありますが、今回の静的サイトには移植・導入していません。

## データモデル

[Migration群](../config/Migrations) と [Table群](../src/Model/Table) から確認しました。

| テーブル | 主な列・関連 |
| --- | --- |
| users | id, username, password, role。出品・入札・落札・メッセージ・お気に入りの利用者 |
| biditems | id, user_id, name, finished, endtime, created, image, minimum_price, category |
| bidrequests | id, biditem_id, user_id, price, created |
| bidinfo | id, biditem_id, user_id, price, created。ORMで商品にhasOne |
| bidmessages | id, bidinfo_id, user_id, message, created |
| favorites | id, user_id, biditem_id, created |

追加でmoviesのMigrationが存在します。People・Messagesはモデルがありますが、対応Migrationは見当たりません。
ORMの関連と `existsIn()` による参照整合性ルールはありますが、DBレベルの外部キー・一意制約の保証とは区別します。
Usersのemail・phone・addressや、TimestampBehaviorが通常更新するmodifiedなど、コードと提供Migrationに不一致があります。
この記録は実DBのスキーマを確認した結果ではありません。

## 掲載を制限した点・未確認事項

- 一般ユーザーの自己登録：UsersControllerの未認証許可はlogin・logoutのみ。作成処理があっても一般公開の登録機能とは扱いません。
- マイページ：`Form->controle()` の誤記があり、プロフィール追加列のMigrationも見当たりません。現状で正常動作する画面とは断定せず、ギャラリーから除外しました。
- 入札：最低価格はフォームのminにありますが、Controllerの `$minimumPrice` は検証に使われていません。サーバー側の最低価格・現在最高額・期限確認が見当たりません。フォームには `biditems_id` というプロパティ参照もあり、実動作の確認が必要です。
- 認可：所有者比較はありますが、AuctionControllerで使う `ForbiddenException` の名前空間指定がありません。取引参加者だけに閲覧・投稿を制限する処理、hidden項目の改変防止を含む厳密な本人確認は保証できません。
- CSRF：routes.php内の登録・適用はコメントアウトされています。SecurityComponentも有効化されていません。
- 画像：サーバー側のファイル種別・サイズ検証、保存結果確認やファイル名の厳格な制御は確認できません。「安全なアップロード」とは表現しません。
- 終了処理：排他制御・全体のトランザクション・同額入札の明示的な優先規則・メール送信例外からの復旧は見当たりません。メール失敗後の再実行や重複落札を防ぐ保証とは扱いません。
- メール：送信コードが存在することだけを確認しています。実配送・SMTP設定は確認していません。個人メールの直接記載を見つけましたが、値は紹介サイトやこの記録へ転載していません。
- Cron・Docker：[セットアップ文書](../READMEセットアップ手順.md) に記載がありますが、Cronの登録設定・Dockerfile・docker-compose.ymlは見当たりません。実運用実績・実行間隔は掲載していません。
- テスト：tests配下に未実装の `markTestIncomplete()` が多くあります。既存アプリのテストは実行していません。
- 起動環境：vendorとconfig/app.phpがなく、PHP・Dockerも `command -v` で見つかりませんでした。環境構築や依存インストールは行っていません。
- 素材：webrootには商品画像・キャラクター画像・音声がありますが、実画面全体のキャプチャや操作動画は見つかりませんでした。紹介サイトには転用していません。

## 今回の変更範囲

紹介サイトと文書を `docs/` にのみ作成しました。既存CakePHPアプリの問題はこの作業で修正していません。
作業開始時点に存在した `webroot/audio/マイページ.mp3` の変更は保持しています。
秘密情報を含みうる設定ファイル・実データ・ログをサイトへコピーしていません。初回の調査・制作ではcommit・push・公開を行わず、その後の依頼でdocsのcommit・pushが許可されました。公開操作の結果とは区別します。
