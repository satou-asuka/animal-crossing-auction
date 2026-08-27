<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Mailer\Email;

class AuctionShell extends Shell
{
    public function initialize()
    {
        parent::initialize();

        $this->loadModel('Biditems');
        $this->loadModel('Bidrequests');
        $this->loadModel('Bidinfo');
        $this->loadModel('Users');
    }

    public function main()
    {
        $now = new \DateTime('now');

        //終了時刻を過ぎていて、まだ終了処理されていない商品を取得（indexとviewを開いていない）
        $finishedItems = $this->Biditems->find()
            ->where([
                'finished' => 0,
                'endtime <=' => $now
            ])
            ->toArray();

        $this->out('オークションチェック開始');

        foreach ($finishedItems as $biditem) {

            //最高額の入札を取得
            $bidrequest = $this->Bidrequests->find()
                ->where([
                    'biditem_id' => $biditem->id
                ])
                ->contain(['Users'])
                ->order([
                    'price' => 'DESC'
                ])
                ->first();
            
            //入札がある場合
            if(!empty($bidrequest)){

                // 落札情報を作成
                $bidinfo = $this->Bidinfo->newEntity();

                $bidinfo->biditem_id = $biditem->id;
                $bidinfo->user_id = $bidrequest->user->id;
                $bidinfo->price = $bidrequest->price;

                if ($this->Bidinfo->save($bidinfo)) {

                    $this->out(
                        '落札確定：' .
                        $biditem->name .
                        ' / ' .
                        $bidrequest->user->username .
                        ' / ' .
                        $bidrequest->price . 'ベル'
                    );

                    //落札者にメールを送信
                    $to = $bidrequest->user->email;

                    if (!empty($to)) {

                        $email = new Email('default');

                        $email->setFrom([
                            'asuka2.2@icloud.com' => '島のオークションサイト'
                        ])
                        ->setTo($to)
                        ->setSubject('商品を落札しました！')
                        ->send(
                            $biditem->name . 'を' .
                            $bidrequest->price . "ベルで落札しました！\n\n" .
                            'おめでとうございます！取引メッセージを送りましょう😊'
                        );
                    }

                    //出品者にメールを送信
                    $seller = $this->Users->get($biditem->user_id);

                    $sellerEmail = $seller->email;

                    if (!empty($sellerEmail)) {

                        $email = new Email('default');

                        $email->setFrom([
                            'asuka2.2@icloud.com' => '島のオークションサイト'
                        ])
                        ->setTo($sellerEmail)
                        ->setSubject('オークションが終了しました！')
                        ->send(
                            '出品していた「' . $biditem->name . "」のオークションが終了しました！\n\n" .
                            '落札価格：' . $bidrequest->price . "ベル\n" .
                            '落札者：' . $bidrequest->user->username . "\n\n" .
                            'おめでとうございます！取引メッセージを送りましょう🎉'
                        );
                    }

                    // 商品を終了状態にする
                    $biditem->finished = 1;
                    $this->Biditems->save($biditem);
                }

                }else {
                    $this->out(
                        '商品：' . $biditem->name .
                        ' / 入札なし'
                    );

                    $seller = $this->Users->get($biditem->user_id);

                    if (!empty($seller->email)) {

                        $email = new Email('default');

                        $email->setFrom([
                            'asuka2.2@icloud.com' => '島のオークションサイト'
                        ])
                        ->setTo($seller->email)
                        ->setSubject('オークションが終了しました')
                        ->send(
                            '出品していた「' . $biditem->name . "」のオークションが終了しました。\n\n" .
                            "今回は入札がありませんでした。\n" .
                            "最低価格を下げて出品してみませんか？"
                        );
                    }

                    // 入札がなくてもオークション自体は終了
                    $biditem->finished = 1;
                    $this->Biditems->save($biditem);
                }

        }

        $this->out('対象件数：' . count($finishedItems));
    }
}