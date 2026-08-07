<p>This is People Table records.</p>
<?=$this->Form->create($entity,
    [
        'type'=>'post',
        'url'=>['controller'=>'Messages',
            'action'=>'index']
    ])?>

    <fieldset class="form">
        person id
        <?= $this->Form->error('Messsages.person_id'); ?>
        <?=$this->Form->text('Messages.person_id') ?>
        Message
        <?= $this->Form->error('Messsages.message'); ?>
        <?=$this->Form->text('Messages.message') ?>
        <?=$this->Form->submit('投稿') ?>
    </fieldset>

    <hr>
    <table>
    <thead><tr>
        <th>ID</th><th>Message</th><th>name</th><th>created at</th>
    </tr></thead>

    <?php foreach($data->toArray() as $obj): ?>

    <tr>
        <td><?= h($obj->id) ?></td>
        <td><?= h($obj->message) ?></td>
        <td><?= h($obj->person->name) ?></td>
    <td><?= h($obj->created_at) ?></td>
    </tr>

    <?php endforeach; ?>

    </table>
