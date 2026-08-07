<?php
namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Exception;

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

    // Biditemsの検索用Queryを作成
    $query = $this->Biditems->find();

    // キーワードが入力されている場合だけ曖昧検索
    if (!empty($keyword)) {
        $query->where([
            'Biditems.name LIKE' => '%' . $keyword . '%'
        ]);
    }

    // 検索結果をページネーション
    $auction = $this->paginate($query, [
        'order' => ['Biditems.endtime' => 'desc'],
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
        'keyword'
    ));
}

    // 商品情報の表示
    public function view($id = null)
    {
        // $idのBiditemを取得
        $biditem = $this->Biditems->get($id, [
            'contain' => ['Users', 'Bidinfo', 'Bidinfo.Users']
        ]);

        // オークション終了時の処理
        if ($biditem->endtime < new \DateTime('now') and $biditem->finished == 0) {

            // finishedを1に変更して保存
            $biditem->finished = 1;
            $this->Biditems->save($biditem);

            // Bidinfoを作成する
            $bidinfo = $this->Bidinfo->newEntity();

            // Bidinfoのbiditem_idに$idを設定
            $bidinfo->biditem_id = $id;

            // 最高金額のBidrequestを検索
            $bidrequest = $this->Bidrequests->find('all', [
                'conditions' => ['biditem_id' => $id],
                'contain' => ['Users'],
                'order' => ['price' => 'desc']])->first();

        // Bidrequestが得られた時の処理
        if (!empty($bidrequest)) {
            // Bidinfoの各種プロパティを設定して保存する
            $bidinfo->user_id = $bidrequest->user->id;
            $bidinfo->user = $bidrequest->user;
            $bidinfo->price = $bidrequest->price;
            $this->Bidinfo->save($bidinfo);
        }

        // Biditemのbidinfoに$bidinfoを設定
        $biditem->bidinfo = $bidinfo;
    }

        // Bidrequestsからbiditem_idが$idのものを取得
        $bidrequests = $this->Bidrequests->find('all', [
            'conditions' => ['biditem_id' => $id],
            'contain' => ['Users'],
            'order' => ['price' => 'desc']])->toArray();

        // オブジェクト類をテンプレート用に設定
        $this->set(compact('biditem', 'bidrequests'));
    }


    // 出品する処理
    public function add()
    {
        // Biditemインスタンスを用意
        $biditem = $this->Biditems->newEntity();

        // POST送信時の処理
        if ($this->request->is('post')) {

            // $biditemにフォームの送信内容を反映
            $biditem = $this->Biditems->patchEntity($biditem, $this->request->getData());

            // $biditemを保存する
            if ($this->Biditems->save($biditem)) {

                // 成功時のメッセージ
                $this->Flash->success(__('保存しました。'));

                // トップページ(index)に移動
                return $this->redirect(['action' => 'index']);
            }

            // 失敗時のメッセージ
            $this->Flash->error(__('保存に失敗しました。もう一度入力下さい。'));
        }

        // 値を保管
        $this->set(compact('biditem'));
    }


    // 入札の処理
    public function bid($biditem_id = null)
    {
        // 入札用のBidrequestインスタンスを用意
        $bidrequest = $this->Bidrequests->newEntity();

    // $bidrequestにbiditem_idとuser_idを設定
    $bidrequest->biditem_id = $biditem_id;
    $bidrequest->user_id = $this->Auth->user('id');

    // POST送信時の処理
    if ($this->request->is('post')) {

        // $bidrequestに送信フォームの内容を反映する
        $bidrequest = $this->Bidrequests->patchEntity($bidrequest, $this->request->getData());

        // Bidrequestを保存
        if ($this->Bidrequests->save($bidrequest)) {

            // 成功時のメッセージ
            $this->Flash->success(__('入札を送信しました。'));

            // トップページにリダイレクト
            return $this->redirect(['action' => 'view', $biditem_id]);
        }

        // 失敗時のメッセージ
        $this->Flash->error(__('入札に失敗しました。もう一度入力下さい。'));
    }

        // $biditem_idの$biditemを取得する
        $biditem = $this->Biditems->get($biditem_id);

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

        $this->set(compact('biditems'));
    }

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

}