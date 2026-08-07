<table>
    <?= $this->Html->tableHeaders(['title', 'name', 'mail'],
        ['style'=>['background:#006;color:white']]) ?>
    <?=$this->Html->tableCells([
        ["this is sample", "taro", "taro@yamada"],
        ["Hello!", "hanako", "hanako@flower"],
        ["test, test.", "sachiko", "sachi@co.jp"],
        ["last!.", "jiro" , "jiro@change"],
    ],
    ['style'=>['background:#ccf']],
    ['style'=>['background:#dff']]) ?>
</table>

