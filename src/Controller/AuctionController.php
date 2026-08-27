<?php
namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Exception;
use Cake\Mailer\Email;

class AuctionController extends AuctionBaseController
{
    // デフォルトテーブルを使わない
    public $useTable = false;

    // 初期化処理
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        // 必要なモデルをすべてロード
        $this->loadModel('Users');
        $this->loadModel('Biditems');
        $this->loadModel('Bidrequests');
        $this->loadModel('Bidinfo');
        $this->loadModel('Bidmessages');
        $this->loadModel('Favorites');

        // ログインしているユーザー情報をauthuserに設定
        $this->set('authuser', $this->Auth->user());

        // レイアウトをauctionに変更
        $this->viewBuilder()->setLayout('auction');
    }

    // トップページ
    public function index()
{
    // 検索欄に入力された文字を取得
    $keyword = $this->request->getQuery('keyword');

    $category = $this->request->getQuery('category');

    //並べ替え
    $sort = $this->request->getQuery('sort');

    if(empty($sort)){
        $sort = 'newest';
    }

    switch ($sort) {
        case 'newest':
            $order = ['Biditems.created' => 'desc'];
            break;
        
        case 'oldest':
            $order = ['Biditems.created' => 'asc'];
            break;
        
        case 'endtime_asc':
            $order = [
                'Biditems.finished' => 'asc',
                'Biditems.endtime'=> 'asc'
                ];
            break;

        case 'endtime_desc':
            $order = ['Biditems.endtime' => 'desc'];
            break;

        case 'price_asc':
            $order = ['Biditems.minimum_price' => 'asc'];
            break;
        
        case 'price_desc':
            $order = ['Biditems.minimum_price' => 'desc'];
            break;
        
        case 'bid_count_desc':
            $order = [];
            break;

        default:
            $order = ['Biditems.id' => 'desc'];
            break;
    }


    // Biditemsの検索用Queryを作成
    $query = $this->Biditems->find();

    // キーワードが入力されている場合だけ曖昧検索
    if (!empty($keyword)) {
        $query->where([
            'Biditems.name LIKE' => '%' . $keyword . '%'
        ]);
    }

    //カテゴリが選択されている場合のみ絞り込み
    if(!empty($category)) {
        $query->where([
            'Biditems.category' => $category
        ]);
    }
    
    //入札数を取得
    $query
        ->leftJoinWith('Bidrequests')
        ->select($this->Biditems)
        ->select([
            'bid_count' => $query->func()->count('Bidrequests.id')
        ])
        ->group(['Biditems.id']);

    //入札数が多い順の処理
    if ($sort === 'bid_count_desc'){
            $query->order([
                'Biditems.finished'=> 'asc',
                'bid_count' => 'desc'
            ]);
    }

    // 検索結果をページネーション
    $auction = $this->paginate($query, [
        'order' => $order,
        'limit' => 10
    ]);

    $userId = $this->Auth->user('id');

    // お気に入り登録を取得
    $favorites = $this->Favorites->find()
        ->select(['biditem_id'])
        ->where(['user_id' => $userId])
        ->toArray();

    $favoriteBiditemIds = [];

    foreach ($favorites as $favorite) {
        $favoriteBiditemIds[] = $favorite->biditem_id;
    }

    $this->set(compact(
        'auction',
        'favoriteBiditemIds',
        'keyword',
        'sort'
    ));
}

    // 商品情報の表示
    public function view($id = null)
    {
        $biditem = $this->Biditems->get($id, [
            'contain' => ['Users', 'Bidinfo', 'Bidinfo.Users']
        ]);

        $bidrequests = $this->Bidrequests->find('all', [
            'conditions' => ['biditem_id' => $id],
            'contain' => ['Users'],
            'order' => ['price' => 'desc']
        ])->toArray();

        //オブジェクト類をテンプレート用に設定
        $this->set(compact('biditem', 'bidrequests'));
    }


    // 出品する処理
    public function add()
    {
        $biditem = $this->Biditems->newEntity();

        if ($this->request->is('post')) {

            //画像だけ取り出す
            $imageFile = $this->request->getData('image_file');

            $biditem = $this->Biditems->patchEntity(
                $biditem,
                $this->request->getData()
            );

            //最低価格が未入力なら300ベル
            if(empty($biditem->minimum_price)){
                $biditem->minimum_price = 300;
            }

            // 画像が選択されている場合
            if (!empty($imageFile['name'])) {

                $fileName = time() . '_' . $imageFile['name'];

                $uploadPath =
                    WWW_ROOT . 'img' . DS . 'biditems' . DS . $fileName;

                move_uploaded_file(
                    $imageFile['tmp_name'],
                    $uploadPath
                );

                $biditem->image = $fileName;

            }else{
                //画像がない場合は空文字を保存する
                $biditem -> image = '';
            }

            // $biditemを保存する
            if ($this->Biditems->save($biditem)) {

                $this->Flash->success(__('保存しました。'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('保存に失敗しました。もう一度入力下さい。'));
        }
        $this->set(compact('biditem'));
    }


    // 入札の処理
    public function bid($biditem_id = null)
    {
        $biditem = $this->Biditems->get($biditem_id,[
            'contain' => ['Users']
        ]);
        if ($biditem->user_id == $this->Auth->user('id')) {
            $this->Flash->error('自分が出品した商品には入札できません。');

            return $this->redirect([
                'action' => 'view',
                $biditem_id
            ]);
        }

        $bidrequest = $this->Bidrequests->newEntity();

        $bidrequest->biditem_id = $biditem_id;
        $bidrequest->user_id = $this->Auth->user('id');

        if ($this->request->is('post')) {

            // $bidrequestに送信フォームの内容を反映する
            $bidrequest = $this->Bidrequests->patchEntity(
                $bidrequest,
                $this->request->getData()
            );
            $minimumPrice = $biditem->minimum_price ?? 300;

            if ($this->Bidrequests->save($bidrequest)) {

                $to = $biditem->user->email;

                if(!empty($to)){
                    $email = new Email('default');

                    $email->setFrom([
                        'asuka2.2@icloud.com' => '島のオークションサイト'
                    ])
                    ->setTo($to)
                    ->setSubject('あなたの商品に入札がありました！')
                    ->send(
                        '出品中の「' . $biditem->name . "」に入札がありました！\n\n" .
                        '入札金額：' . $bidrequest->price . "ベル\n\n" .
                        'オークションの様子を確認してみましょう👀'
                    );
                }

                // 成功時のメッセージ
                $this->Flash->success(__('入札を送信しました。'));

                // トップページにリダイレクト
                return $this->redirect(['action' => 'view', $biditem_id]);
            }

            // 失敗時のメッセージ
            $this->Flash->error(__('入札に失敗しました。もう一度入力下さい。'));
        }

        $this->set(compact('bidrequest', 'biditem'));
    }


    // 落札者とのメッセージ
    public function msg($bidinfo_id = null)
    {
        // Bidmessageを新たに用意
        $bidmsg = $this->Bidmessages->newEntity();

        // POST送信時の処理
        if ($this->request->is('post')) {

            // 送信されたフォームで$bidmsgを更新
            $bidmsg = $this->Bidmessages->patchEntity($bidmsg, $this->request->getData());

            // Bidmessageを保存
            if ($this->Bidmessages->save($bidmsg)) {
                $this->Flash->success(__('保存しました。'));
            } else {
                $this->Flash->error(__('保存に失敗しました。もう一度入力下さい。'));
            }
        }

        try { // $bidinfo_idからBidinfoを取得する
            $bidinfo = $this->Bidinfo->get($bidinfo_id, ['contain' => ['Biditems']]);
        } catch(Exception $e){
            $bidinfo = null;
        }

        // Bidmessageをbidinfo_idとuser_idで検索
            $bidmsgs = $this->Bidmessages->find('all', [
            'conditions' => ['bidinfo_id' => $bidinfo_id],
            'contain' => ['Users'],
            'order' => ['created' => 'desc']]);

        $this->set(compact('bidmsgs', 'bidinfo', 'bidmsg'));
    }


    // 落札情報の表示
    public function home()
    {
        // 自分が落札したBidinfoをページネーションで取得
        $bidinfo = $this->paginate('Bidinfo', [
            'conditions' => ['Bidinfo.user_id' => $this->Auth->user('id')],
            'contain' => ['Users', 'Biditems'],
            'order' => ['created' => 'desc'],
            'limit' => 10])->toArray();

        $this->set(compact('bidinfo'));
    }


    // 出品情報の表示
    public function home2()
    {
        $biditems = $this->paginate('Biditems', [
            'conditions' => [
                'Biditems.user_id' => $this->Auth->user('id')
            ],
            'contain' => [
                'Bidinfo'
            ],
            'order' => [
                'Biditems.created' => 'DESC'
            ],
            'limit' => 10
        ]);

        foreach ($biditems as $biditem) {

            //入札件数
            $biditem->bid_count = $this->Bidrequests
                ->find()
                ->where([
                    'biditem_id' => $biditem->id
                ])
                ->count();
            
            //商品の状態
            if ($biditem->finished == 0) {
                $biditem->status_label = '出品中';

            }elseif (!empty($biditem->bidinfo)){
                $biditem->status_label = '落札済み';
            }else{
                $biditem->status_label = '終了（落札なし）';
            }
        }
        $this->set(compact('biditems'));
    }

    //マイページ
    public function mypage()
    {
        $userId = $this->Auth->user('id');
        $user = $this->Users->get($userId);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity(
                $user,
                $this->request->getData(),
                [
                    'fields' => [
                        'username',
                        'email',
                        'phone',
                        'address'
                    ]
                ]
            );

            if ($this->Users->save($user)) {
                $this->Auth->setUser($user->toArray());
                $this->Flash->success('ユーザー名を変更しました。');

                return $this->redirect([
                    'action' => 'mypage'
                ]);
            }

            $this->Flash->error('ユーザー名を変更できませんでした。');
        }
        $this->set(compact('user'));
    }

    public function deleteMessage($id)
    {
        $this->request->allowMethod(['post']);

        $message = $this->Bidmessages->get($id);

        if ($message->user_id != $this->Auth->user('id')) {
            throw new ForbiddenException();
        }

        $bidinfoId = $message->bidinfo_id;

        if ($this->Bidmessages->delete($message)) {
            $this->Flash->success('削除しました。');
        } else {
            $this->Flash->error('削除できませんでした。');
        }

        return $this->redirect([
            'action' => 'msg',
            $bidinfoId
        ]);
    }

    //お気に入り登録
    public function favorite($bidItemId)
    {
        $this->request->allowMethod(['post']);

        $userId = $this->Auth->user('id');

        $favorite = $this->Favorites->find()
            ->where([
                'user_id' =>$userId,
                'biditem_id' => $bidItemId
            ])
            ->first();

        if($favorite){
            //すでにお気に入り登録されていたら解除
            if($this->Favorites->delete($favorite)){
                $this->Flash->success('お気に入りを解除しました');
            }else{
                $this->Flash->error('お気に入りを解除できませんでした');
            } 
        }else {
            //お気に入り未登録なら新しく登録
            $favorite = $this->Favorites->newEntity([
                'user_id' => $userId,
                'biditem_id' => $bidItemId
            ]);

            $result = $this->Favorites->save($favorite);

            if($result){
                $this->Flash->success('お気に入りに登録しました');
            }else{
                $this->Flash->error('お気に入りに登録できませんでした');
            }
        }
        return $this->redirect($this->referer());
    }

    //お気に入り登録一覧
    public function favorites()
    {
        $userId = $this->Auth->user('id');

        $favorites = $this->Favorites->find()
            ->contain(['Biditems'])
            ->where([
                'Favorites.user_id' => $userId
            ]);
        $this->set(compact('favorites'));
    }

    //商品の編集
    public function edit($id = null)
    {
        //すでにあるデータを取得
        $biditem = $this->Biditems->get($id);

        if ($biditem->user_id != $this->Auth->user('id')) {
            throw new ForbiddenException();
        }

        if ($this->request->is(['patch', 'post', 'put'])) {

            $imageFile = $this->request->getData('image_file');

            $biditem = $this->Biditems->patchEntity(
                $biditem,
                $this->request->getData(),
                [
                    'fields' => [
                        'name',
                        'minimum_price',
                        'category'
                    ]
                ]
            );

            if (!empty($imageFile['name'])) {

                $fileName = time() . '_' . $imageFile['name'];

                $uploadPath =
                    WWW_ROOT . 'img' . DS . 'biditems' . DS . $fileName;

                move_uploaded_file(
                    $imageFile['tmp_name'],
                    $uploadPath
                );

                $biditem->image = $fileName;
            }

            // 更新内容を保存
            if ($this->Biditems->save($biditem)) {

                $this->Flash->success('商品情報を更新しました。');

                return $this->redirect([
                    'action' => 'home2'
                ]);
            }

            $this->Flash->error('商品情報を更新できませんでした。');
        }

        $this->set(compact('biditem'));
    }

    // 出品商品の削除
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $biditem = $this->Biditems->get($id);

        if ($biditem->user_id != $this->Auth->user('id')) {
            throw new ForbiddenException();
        }

        if ($this->Biditems->delete($biditem)) {
            $this->Flash->success('商品を削除しました。');
        } else {
            $this->Flash->error('商品を削除できませんでした。');
        }

        return $this->redirect([
            'action' => 'home2'
        ]);
    } 


    //入札一覧
    public function bidding()
    {
        $userId = $this->Auth->user('id');

        // 自分が入札した商品IDを取得
        $myBiditemIds = $this->Bidrequests->find()
            ->select(['biditem_id'])
            ->where([
                'Bidrequests.user_id' => $userId
            ])
            ->distinct(['biditem_id'])
            ->extract('biditem_id')
            ->toArray();

        // 入札した商品がない場合
        if (empty($myBiditemIds)) {
            $biditems = [];

            $this->set(compact('biditems'));
            return;
        }

        // 自分が入札した商品を取得
        $biditems = $this->Biditems->find()
            ->contain([
                'Bidrequests' => function ($q) {
                    return $q->order([
                        'Bidrequests.price' => 'DESC'
                    ]);
                },
                'Bidinfo'
            ])
            ->where([
                'Biditems.id IN' => $myBiditemIds
            ])
            ->order([
                'Biditems.finished' => 'ASC',
                'Biditems.endtime' => 'ASC'
            ])
            ->toArray();

        // 各商品の入札状況を整理
        foreach ($biditems as $biditem) {

            $myHighestPrice = 0;
            $highestPrice = 0;

            foreach ($biditem->bidrequests as $bidrequest) {

                // 全体の最高入札額
                if ($bidrequest->price > $highestPrice) {
                    $highestPrice = $bidrequest->price;
                }

                // 自分の最高入札額
                if (
                    $bidrequest->user_id == $userId &&
                    $bidrequest->price > $myHighestPrice
                ) {
                    $myHighestPrice = $bidrequest->price;
                }
            }

            $biditem->my_highest_price = $myHighestPrice;
            $biditem->highest_price = $highestPrice;

            // ステータス判定
            if ($biditem->finished) {

                if (
                    !empty($biditem->bidinfo) &&
                    $biditem->bidinfo->user_id == $userId
                ) {
                    $biditem->bid_status = '落札済み';
                } else {
                    $biditem->bid_status = '落札できませんでした';
                }

            } elseif ($myHighestPrice == $highestPrice) {

                $biditem->bid_status = '最高入札者';

            } else {

                $biditem->bid_status = '上位入札あり';
            }
        }

        $this->set(compact('biditems'));
    }
}