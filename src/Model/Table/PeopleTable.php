<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class PeopleTable extends Table{
    public function initialize(array $config){
        parent::initialize($config);

        $this->setTable('people');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
        $this->hasMany('Messages');
    }

    public function findMe(Query $query, array $options){
        $me = $options['me'];
        return $query->where(['name like' => '%' . $me . '%'])
            ->orWhere(['mail like' => '%' . $me . '%'])
            ->order(['age'=>'asc']);
    }

    public function findByAge(Query $query, array $options){
        return $query->order(['age' => 'asc'])->order(['name' => 'asc']);
    }

    public function validationDefault(Validator $validator){
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('name', 'テキストを入力してください')
            ->requirePresence('name', 'create')
            ->notEmpty('name', '名前は必ず入力してください');
        
        $validator
            ->scalar('mail', 'テキストを入力してください')
            ->allowEmpty('mail')
            ->email('mail', 'false', 'メールアドレスを記入してください');

        $validator
            ->integer('age', '整数を入力してください')
            ->requirePresence('age', 'create')
            ->notEmpty('age', '必ず値を入力してください')
            ->greaterThan('age', -1, 'ゼロ以上の値を記入してください');

        return $validator;
    }
}