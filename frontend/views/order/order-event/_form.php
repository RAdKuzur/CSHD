<?php

use common\components\dictionaries\base\NomenclatureDictionary;
use common\helpers\DateFormatter;
use kartik\select2\Select2;
use kidzen\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model */
/* @var $people */
/* @var $scanFile */
/* @var $docFiles */
/* @var $teamList */
/* @var $awardList */
/* @var $foreignEventTable */
/* @var $teamTable */
/* @var $awardTable */
/* @var $modelActs */
/* @var $teams */
/* @var $nominations */
/* @var $actTable */
/* @var $participants */
/* @var $company */
/* @var $id */

?>

<style>
    div[role=radiogroup] > label {
        font-weight: normal;
    }

    .row {
        margin: 0;
    }

    /*.nomination-list-div, .team-list-div {
        border: 1px solid #ccc;
        border-radius: 7px;
        padding: 10px;
        overflow-y: scroll;
        width: 47%;
        margin: 10px;
        height: 250px;
        display: inline-block;
    }



    .nomination-add-input, .team-add-input {
        display: block;
        margin: 0;
        padding: 0;
        width: 100%;
    }



    .nomination-list-item, .team-list-item {
        display: inline-block;
    }

    .nomination-list-row, .team-list-row {
        display: block;
    }

    .nomination-list-item-delete,  .team-list-item-delete {
        display: inline-block;
        margin-right: 5px;
    }


    .nomination-add-input, .team-add-input {
        display: block;
        width: 97%;
        height: 30px;
        padding: 0.375rem 0.75rem;
        margin-top: 5px;
        margin-bottom: 5px;
        margin-right: 10px;
        margin-left: 0;
        font-family: inherit;
        font-size: 16px;
        font-weight: 400;
        line-height: 2;
        color: #212529;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #9f9f9f;
        border-radius: 0.25rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .nomination-add-input::placeholder, .team-add-input::placeholder {
        color: #212529;
        opacity: 0.4;
    }


    .delete-nomination-button, .delete-team-button {
        background-color: #b24848;
        font-weight: 400;
        color: white;
        border: 1px solid #962c2c;
        border-radius: 5px;
    }*/
</style>
<script>
    function displayDetails()
    {
        var elem = document.getElementById('documentorderwork-supplement-compliance_document').getElementsByTagName('input');
        var details = document.getElementById('details');

        if (elem[0].checked)
            details.style.display = "none";
        else
            details.style.display = "flex";

        let item = [1, 2, 3];
        item.forEach((element) => {
            if (elem[element].checked)
                details.childNodes[2*element-1].hidden = false;
            else
                details.childNodes[2*element-1].hidden = true;
        });
    }

    let listId = 'nomDdList'; //айди выпадающего списка, в который будут добавлены номинации
    let listId2 = 'teamDdList'; //айди выпадающего списка, в который будут добавлены команды

    //let nominations = [];
    //let team = [];
    let team = <?php echo json_encode($teams); ?>;
    let nominations = <?php echo json_encode($nominations); ?>;
    window.onload = function(){
        var actsDiv = document.getElementById('acts');
        var commandsDiv = document.getElementById('commands');
        actsDiv.style.pointerEvents = 'none'; // Блокируем ввод
        actsDiv.style.opacity = '0.5'; // Уменьшаем непрозрачность
        commandsDiv.style.pointerEvents = 'auto'; // Разблокируем ввод
        commandsDiv.style.opacity = '1'; // Восстанавливаем непрозрачность
        if (nominations != null) {
            FinishNom();
        }
        if (team != null) {
            FinishTeam();
        }
        if (document.getElementById('documentorderwork-order_date').value === '')
        {
            document.getElementById('documentorderwork-supplement-foreign_event_goals_id').childNodes[0].childNodes[0].checked = true;
            document.getElementById('documentorderwork-supplement-compliance_document').childNodes[0].childNodes[0].checked = true;
        }
        document.getElementsByClassName('form-group field-documentorderwork-foreign_event-is_minpros')[0].childNodes[4].style.color = 'white';
        displayDetails();
    }

    function AddElem(list_row, list_item, arr, list_name)
    {
        let item = document.getElementsByClassName(list_row)[0];
        let itemCopy = item.cloneNode(true)
        itemCopy.getElementsByClassName(list_item)[0].innerHTML = '<span>' + arr[i] + '</span>'
        itemCopy.style.display = 'flex';

        let list = document.getElementById(list_name);
        list.append(itemCopy);
    }

    function AddNom()
    {
        let elem = document.getElementById('nom-name');
        elem.value = elem.value.replace(/ +/g, ' ').trim();

        if (elem.value !== '' && nominations.indexOf(elem.value) === -1)
        {
            nominations.push(elem.value);

            let item = document.getElementsByClassName('nomination-list-row')[0];
            let itemCopy = item.cloneNode(true)
            itemCopy.getElementsByClassName('nomination-list-item')[0].innerHTML = '<span>' + elem.value + '</span>'
            itemCopy.style.display = 'flex';

            let list = document.getElementById('list');
            list.append(itemCopy);

            elem.value = '';
        }
        else
            alert('Вы ввели пустые или повторные данные!');
        FinishNom();
    }

    function AddTeam()
    {
        let elem = document.getElementById('team-name');
        elem.value = elem.value.replace(/ +/g, ' ').trim();

        if (elem.value !== '' && team.indexOf(elem.value) === -1)
        {
            team.push(elem.value);

            let item = document.getElementsByClassName('team-list-row')[0];
            let itemCopy = item.cloneNode(true)
            itemCopy.getElementsByClassName('team-list-item')[0].innerHTML = '<span>' + elem.value + '</span>'
            itemCopy.style.display = 'flex';

            let list = document.getElementById('list2');
            list.append(itemCopy);

            elem.value = '';
        }
        else
            alert('Вы ввели пустые или повторные данные!');

        FinishTeam();
    }

    function DelNom(elem)
    {
        let orig = elem.parentNode.parentNode;

        let name = elem.parentNode.parentNode.getElementsByClassName('nomination-list-item')[0].childNodes[0].textContent;
        nominations.splice(nominations.indexOf(name), 1);
        elem.parentNode.parentNode.parentNode.removeChild(orig);

        FinishNom();
    }

    function DelTeam(elem)
    {
        let orig = elem.parentNode.parentNode;

        let name = elem.parentNode.parentNode.getElementsByClassName('team-list-item')[0].childNodes[0].textContent;
        team.splice(team.indexOf(name), 1);
        elem.parentNode.parentNode.parentNode.removeChild(orig);

        FinishTeam();
    }

    function FinishNom()
    {
        let elem = document.getElementsByClassName(listId);
        for (let z = 0; z < elem.length; z++)
        {
            let value = null;
            if(elem[z].options.selectedIndex !== 0){
                value = elem[z].options[elem[z].selectedIndex].value;
            }
            while (elem[z].options.length) {
                elem[z].options[0] = null;
            }

            elem[z].appendChild(new Option("--", 'NULL'));

            for (let i = 0; i < nominations.length; i++)
            {
                var option = document.createElement('option');
                option.value = nominations[i];
                option.innerHTML = nominations[i];
                elem[z].appendChild(option);
            }
            if (nominations.includes(value)){
                elem[z].value = value;
            }
        }
    }

    function FinishTeam()
    {
        let elem = document.getElementsByClassName(listId2);

        for (let z = 0; z < elem.length; z++)
        {
            let value = null;
            if(elem[z].options.selectedIndex !== 0){
                value = elem[z].options[elem[z].selectedIndex].value;
            }
            while (elem[z].options.length) {
                elem[z].options[0] = null;
            }

            elem[z].appendChild(new Option("--", 'NULL'));

            for (let i = 0; i < team.length; i++)
            {
                var option = document.createElement('option');
                option.value = team[i];
                option.innerHTML = team[i];
                elem[z].appendChild(option);
            }
            if (team.includes(value)){
                elem[z].value = value;
            }
        }
    }

    function NextStep()
    {
        let foreign = document.getElementById('foreign-block');
        let nom = document.getElementById('nom-team-block');
        let btn = document.getElementById('nextBtn');

        foreign.disabled = !foreign.disabled;
        nom.disabled = !nom.disabled;
        if (foreign.disabled === false)
        {
            foreign.style.filter = 'blur(0px)';
            nom.style.filter = 'blur(1px)';
            btn.innerHTML = 'Вернуться к заполнению номинаций и команд';
        }
        else
        {
            nom.style.filter = 'blur(0px)';
            foreign.style.filter = 'blur(1px)';
            btn.innerHTML = 'Перейти к заполнению участников мероприятия';
        }
    }

    function NewPart()
    {
        let nom = document.getElementsByClassName(listId);
        let teams = document.getElementsByClassName(listId2);
        let item = teams.length - 1;    // добавляем только новым участникам команды и номинации

        while (teams[item].options.length) {
            teams[item].options[0] = null;
        }

        while (nom[item].options.length) {
            nom[item].options[0] = null;
        }

        teams[item].appendChild(new Option("--", 'NULL'));
        nom[item].appendChild(new Option("--", 'NULL'));

        for (let i = 0; i < team.length; i++)
        {
            var option = document.createElement('option');
            option.value = team[i];
            option.innerHTML = team[i];
            teams[item].appendChild(option);
        }

        for (let i = 0; i < nominations.length; i++)
        {
            var option = document.createElement('option');
            option.value = nominations[i];
            option.innerHTML = nominations[i];
            nom[item].appendChild(option);
        }
    }

    function ClickBranch(elem, index)
    {
        if (index === 4)
        {
            let parent = elem.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode;
            let childs = parent.querySelectorAll('.col-xs-4');
            let first_gen = childs[1].querySelectorAll('.form-group');
            let second_gen = first_gen[3].querySelectorAll('.form-control');
            if (second_gen[0].hasAttribute('disabled'))
                second_gen[0].removeAttribute('disabled');
            else
            {
                second_gen[0].value = 1;
                second_gen[0].setAttribute('disabled', 'disabled');
            }
        }
    }
</script>

<div class="order-event-form field-backing">
    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    <?= $form->field($model, 'order_date')->widget(DatePicker::class, [
        'dateFormat' => 'php:d.m.Y',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => DateFormatter::DEFAULT_STUDY_YEAR_RANGE,
        ]])->label('Дата приказа') ?>

    <div class="col-xs-4">
        <label class="control-label">Код и номенклатура приказа</label>
        <select class="form-control" disabled>
            <option selected><?= NomenclatureDictionary::ADMIN_ORDER ?> Приказы директора по основной деятельности</option>
        </select>
    </div>

    <div class="checkList">
        <div class="checkHeader">
            <h4 class="noPM">Информация для создания карточки учета достижений</h4>
        </div>

        <div class="checkBlock">
            <?= $form->field($model, 'eventName')->textInput()->label('Название мероприятия') ?>
            <div id="organizer">
                <?php
                $params = [
                    'id' => 'organizer',
                    'class' => 'form-control pos',
                    'prompt' => '---',
                ];
                echo $form
                    ->field($model, 'organizer_id')
                    ->dropDownList(ArrayHelper::map($company, 'id', 'name'), $params)
                    ->label('Организатор');
                ?>
            </div>
            <?= $form->field($model, 'dateBegin')->widget(DatePicker::class, [
                'dateFormat' => 'php:d.m.Y',
                'language' => 'ru',
                'options' => [
                    'placeholder' => 'Дата',
                    'class'=> 'form-control',
                    'autocomplete'=>'off'
                ],
                'clientOptions' => [
                    'changeMonth' => true,
                    'changeYear' => true,
                    'yearRange' => DateFormatter::DEFAULT_STUDY_YEAR_RANGE,
                ]])->label('Дата начала') ?>
            <?= $form->field($model, 'dateEnd')->widget(DatePicker::class, [
                'dateFormat' => 'php:d.m.Y',
                'language' => 'ru',
                'options' => [
                    'placeholder' => 'Дата',
                    'class'=> 'form-control',
                    'autocomplete'=>'off'
                ],
                'clientOptions' => [
                    'changeMonth' => true,
                    'changeYear' => true,
                    'yearRange' => DateFormatter::DEFAULT_STUDY_YEAR_RANGE,
                ]])->label('Дата окончания') ?>
            <?= $form->field($model, 'city')->textInput()->label('Город') ?>
            <?= $form->field($model, 'eventWay')->dropDownList(Yii::$app->eventWay->getList(), ['prompt' => '---'])
                ->label('Формат проведения') ?>
            <?= $form->field($model, 'eventLevel')->dropDownList(Yii::$app->eventLevel->getList(), ['prompt' => '---'])
                ->label('Уровень') ?>
            <?= $form->field($model, 'minAge')->textInput()->label('Мин. возраст участников (лет)') ?>
            <?= $form->field($model, 'maxAge')->textInput()->label('Макс. возраст участников (лет)') ?>
            <?= $form->field($model, 'keyEventWords')->textInput()->label('Ключевые слова') ?>
        </div>
    </div>

    <div class="col-xs-4">
        <label class="control-label">Наименование приказа</label>
        <select class="form-control" disabled>
            <option selected>Об участии в мероприятии</option>
        </select>
    </div>

    <div id="bring_id">
        <?php
        $params = [
            'id' => 'bring',
            'class' => 'form-control pos',
            'prompt' => '---',
        ];
        echo $form
            ->field($model, 'bring_id')
            ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
            ->label('Проект вносит');
        ?>
    </div>

    <div id="executor_id">
        <?php
        $params = [
            'id' => 'executor',
            'class' => 'form-control pos',
            'prompt' => '---',
        ];
        echo $form
            ->field($model, 'executor_id')
            ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
            ->label('Кто исполняет');
        ?>
    </div>

    <?= $form->field($model, "responsible_id")->widget(Select2::classname(), [
        'data' => ArrayHelper::map($people,'id','fullFio'),
        'size' => Select2::LARGE,
        'options' => [
            'prompt' => 'Выберите ответственного' ,
            'multiple' => true
        ],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ])->label('ФИО ответственного'); ?>

    <div class="checkList">
        <div class="checkHeader">
            <h4 class="noPM">Дополнительная информация для генерации приказа</h4>
        </div>

        <div class="checkBlock">
            <?= $form->field($model, 'purpose')->radioList([
                '0' => 'выявления, формирования, поддержки и развития способностей и талантов у детей и молодежи 
            на территории Астраханской области, оказания содействия в получении ими дополнительного образования,
             в том числе образования в области искусств, естественных наук, физической культуры и спорта,
              а также обеспечения организации их свободного времени (досуга) и отдыха',
                '1' => 'удовлетворения образовательных и профессиональных потребностей, профессионального развития человека,
             обеспечения соответствия его квалификации меняющимся условиям профессиональной деятельности и социальной среды,
              совершенствования и (или) получения новой компетенции, необходимой для профессиональной деятельности,
               и (или) повышения профессионального уровня в рамках имеющейся квалификации',
                '2' => ' участия в формировании образовательной политики Астраханской области в области выявления,
             сопровождения и дальнейшего развития проявивших выдающиеся способности детей и молодежи в соответствии 
             с задачами социально-экономического, научно-технологического, промышленного 
             и пространственного развития Астраханской области',
            ], ['itemOptions' => ['class' => 'radio-inline']])->label('Уставная цель') ?>
            <?= $form->field($model, 'docEvent')->radioList([
                '0' => 'Отсутствует',
                '1' => 'Регламент',
                '2' => 'Письмо',
                '3' => 'Положение',
            ],
                [
                    'itemOptions' => [
                        'class' => 'radio-inline'],
                    'id' => 'doc-event-radio'
                ])->label('Документ о мероприятии') ?>
            <div id = "document_details" style="display: none;">
                <?= $form->field($model, 'documentDetails')->textInput()->label('Описание документа для вставки в приказ') ?>
            </div>
            <div id="extra_resp_info_id">
                <?php
                $params = [
                    'id' => 'extra_resp_info',
                    'class' => 'form-control pos',
                    'prompt' => '---',
                ];
                echo $form
                    ->field($model, 'respPeopleInfo')
                    ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
                    ->label('Ответственный за сбор и предоставление информации');
                ?>
            </div>
            <?= $form->field($model, 'timeProvisionDay')->textInput()->label('Срок предоставления информации (в днях)') ?>
            <div id="extra_resp_insert_id">
                <?php
                $params = [
                    'id' => 'extra_resp_insert',
                    'class' => 'form-control pos',
                    'prompt' => '---',
                ];
                echo $form
                    ->field($model, 'extraRespInsert')
                    ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
                    ->label('Ответственный за внесение в ЦСХД');
                ?>
            </div>
            <?= $form->field($model, 'timeInsertDay')->textInput()->label('Срок внесения информации (в днях)') ?>
            <div id="extra_resp_method_id">
                <?php
                $params = [
                    'id' => 'extra_resp_method',
                    'class' => 'form-control pos',
                    'prompt' => '---',
                ];
                echo $form
                    ->field($model, 'extraRespMethod')
                    ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
                    ->label('Ответственный за методологический контроль');
                ?>
            </div>
            <div id="extra_resp_info_stuff_id">
                <?php
                $params = [
                    'id' => 'extra_resp_info_stuff',
                    'class' => 'form-control pos',
                    'prompt' => '---',
                ];
                echo $form
                    ->field($model, 'extraRespInfoStuff')
                    ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
                    ->label('Ответственный за информирование работников');
                ?>
            </div>
        </div>
    </div>

    <div id = "commands">
        <fieldset id="nom-team-block">
        <div class="main-div">
            <div class="nomination-heading"><h4><i class="glyphicon glyphicon-tower"></i>Номинации и команды</h4></div>

            <div class="flexx">
                <div class="nomination-div flx1 checkList">
                    <div class="flexx flx1 checkHeader">
                        <div class="flx1">
                            <label class="control-label">Номинация</label>
                            <input class="form-control" id="nom-name" placeholder="Введите номинацию" type="text"/>
                        </div>
                        <div class="nomination-add-button-div">
                            <button type="button" onclick="AddNom()" class="nomination-add-button btn btn-success add-item">+</button>
                        </div>
                    </div>
                    <div id="list" class="nomination-list-div checkBlock">
                        <?php
                        $flag = count($nominations) > 0;
                        $strDisplay = $flag ? 'flex' : 'none';
                        ?>
                        <div class="nomination-list-row" style="display: none">
                            <div class="nomination-list-item-delete">
                                <button type="button" onclick="DelNom(this)" class="delete-nomination-button btn btn-warning remove-item">X</button>
                            </div>
                            <div class="nomination-list-item">
                                DEFAULT_ITEM
                            </div>
                        </div>

                        <?php

                        if ($flag)
                            foreach ($nominations as $nomination)
                                echo '<div class="nomination-list-row">
                            <div class="nomination-list-item-delete">
                                <button type="button" onclick="DelNom(this)" class="delete-nomination-button btn btn-warning remove-item">X</button>
                            </div>
                            <div class="nomination-list-item">'.$nomination.'</div>
                        </div>';?>
                    </div>
                </div>
                <div class="team-div flx1 checkList">
                    <div class="flexx flx1 checkHeader">
                        <div class="flx1">
                            <label class="control-label">Команда</label>
                            <input class="form-control" id="team-name" placeholder="Введите название команды" type="text"/>
                        </div>
                        <div class="team-add-button-div">
                            <button type="button" onclick="AddTeam()" class="team-add-button btn btn-success add-item">+</button>
                        </div>
                    </div>
                    <div id="list2" class="team-list-div checkBlock">
                        <?php

                        $flag2 = count($nominations) > 0;
                        $strDisplay2 = $flag2 ? 'flex' : 'none';

                        ?>
                        <div class="team-list-row" style="display: none">
                            <div class="team-list-item-delete">
                                <button type="button" onclick="DelTeam(this)" class="delete-team-button btn btn-warning remove-item">X</button>
                            </div>
                            <div class="team-list-item">
                                DEFAULT_ITEM
                            </div>
                        </div>

                        <?php

                        if ($flag2)
                            foreach ($teams as $team)
                                echo '<div class="team-list-row">
                            <div class="team-list-item-delete">
                                <button type="button" onclick="DelTeam(this)" class="delete-team-button btn btn-warning remove-item">X</button>
                            </div>
                            <div class="team-list-item">'.$team.'</div>
                        </div>';?>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
    </div>

    <div style="text-align: center;">
        <?= Html::button('Перейти к заполнению участников мероприятия', [
            'class' => 'btn btn-success',
            'type' => 'button',
            'id' => 'toggle-button',
        ]) ?>
    </div>

    <div class="bordered-div" id="acts">
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper_act', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items-act', // required: css class selector
                'widgetItem' => '.item-act', // required: css class
                'limit' => 20, // the maximum times, an element can be cloned (default 999)
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '.add-item-act', // css class
                'deleteButton' => '.remove-item-act', // css class
                'model' => $modelActs[0],
                'formId' => 'dynamic-form',
                'formFields' => [
                    'full_name',
                ],
            ]); ?>
            <div class="container-items-act"><!-- widgetContainer -->
                <div class="panel-title">
                    <h5 class="panel-title pull-left">Акты участия</h5><!-- widgetBody -->
                    <div class="pull-right">
                        <button type="button" class="add-item-act btn btn-success btn-xs" onclick="updateOptions()"><span class="glyphicon glyphicon-plus">+</span></button>
                    </div>
                </div>
                <?php foreach ($modelActs as $i => $modelAct): ?>
                    <div class="item-act panel panel-default"><!-- widgetBody -->
                        <div class="panel-heading">
                            <div class="pull-right">
                                <button type="button" class="remove-item-act btn btn-warning btn-xs" onclick="updateOptions()"><span class="glyphicon glyphicon-minus">-</span></button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class = "form-label">
                            <div class="panel-body">
                                <label>
                                    <?=
                                    $form->field($modelAct, "[{$i}]type")->radioList([
                                        '0' => 'Личное участие',
                                        '1' => 'Командное участие',
                                    ], ['itemOptions' => ['class' => 'radio-inline', 'onclick' => 'handleParticipationChange(this)']])
                                        ->label('Выберите тип участия');
                                    ?>
                                </label>
                                <div class="row">
                                <div id = "form-<?=$i?>" hidden>
                                    <div class=" personal-dropdown-list">
                                        <?php
                                        $params = [
                                            'id' => 'personalParticipantDropdown',
                                            'class' => 'form-control pos personalDropDownList',
                                            'prompt' => '---Выберите участника---',
                                        ];
                                        echo $form->field($modelAct, "[{$i}]personalParticipants[]")->dropDownList(ArrayHelper::map($participants,'id','fullFio'), $params)->label('ФИО участника');
                                        ?>
                                    </div>

                                    <div class=" team-dropdown-list">
                                        <?= $form->field($modelAct, "[{$i}]participant")->widget(Select2::class, [
                                            'data' => ArrayHelper::map($participants,'id','fullFio'),
                                            'size' => Select2::LARGE,
                                            'options' => [
                                                'prompt' => 'Выберите участника' ,
                                                'multiple' => true
                                            ],
                                            'pluginOptions' => [
                                                'allowClear' => true
                                            ],
                                        ])->label('ФИО участников'); ?>
                                        <?php
                                        $params = [
                                            'id' => 'teamDropdown',
                                            'class' => 'form-control pos teamDropDownList teamDdList',
                                            'prompt' => '--- Выберите команду ---',
                                        ];
                                        echo $form->field($modelAct, "[{$i}]team")->dropDownList([], $params)->label('Выберите команду');
                                        ?>
                                    </div>
                                    <?php
                                    $params = [
                                        'id' => 'branch',
                                        'class' => 'form-control pos',
                                        'prompt' => '---',
                                    ];
                                    echo $form->field($modelAct, "[{$i}]branch")->widget(
                                            Select2::classname(), [
                                            'data' => Yii::$app->branches->getList() ,
                                            'size' => Select2::LARGE,
                                                'options' => [
                                                    'prompt' => 'Выберите участника' ,
                                                    'multiple' => true
                                                ],
                                                'pluginOptions' => [
                                                    'allowClear' => true
                                                ],
                                            ]
                                        )
                                        ->label('Отделы');
                                    ?>
                                    <?php
                                    $params = [
                                        'id' => 'teacher',
                                        'class' => 'form-control pos',
                                        'prompt' => '---',
                                    ];
                                    echo $form
                                        ->field($modelAct, "[{$i}]firstTeacher")
                                        ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
                                        ->label('ФИО первого учителя');
                                    echo $form
                                        ->field($modelAct, "[{$i}]secondTeacher")
                                        ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
                                        ->label('ФИО второго учителя (при необходмиости)');
                                    ?>
                                    <?php
                                    $params = [
                                        'id' => 'focus',
                                        'class' => 'form-control pos',
                                        'prompt' => '---',
                                    ];
                                    echo $form
                                        ->field($modelAct, "[{$i}]focus")
                                        ->dropDownList(Yii::$app->focus->getList(), $params)
                                        ->label('Направленность');
                                    ?>
                                    <?= $form->field($modelAct, "[{$i}]form")->dropDownList(Yii::$app->eventWay->getList(), ['prompt' => '---'])
                                        ->label('Форма реализации') ?>
                                    <?= $form->field($modelAct, "[{$i}]actFiles")->fileInput()->label('Представленные материалы') ?>
                                    <div class=" nomination-dropdown-list">
                                        <?php
                                        $params = [
                                            'id' => 'nominationDropdown',
                                            'class' => 'form-control pos nominationDropDownList nomDdList',
                                            'prompt' => '--- Выберите номинацию ---',
                                        ];
                                        echo $form->field($modelAct, "[{$i}]nomination")->dropDownList([], $params)->label('Выберите номинацию');
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php DynamicFormWidget::end(); ?>
        </div>
    </div>

    <?php if ($actTable != NULL): ?>
        <?= $actTable; ?>
    <?php endif; ?>

    <?= $form->field($model, 'key_words')->textInput()->label('Ключевые слова') ?>
    <?= $form->field($model, 'scanFile')->fileInput()->label('Скан документа') ?>
    <?php if (strlen($scanFile) > 10): ?>
        <?= $scanFile; ?>
    <?php endif; ?>

    <?= $form->field($model, 'docFiles[]')->fileInput(['multiple' => true])->label('Редактируемые документы') ?>

    <?php if (strlen($docFiles) > 10): ?>
        <?= $docFiles; ?>
    <?php endif; ?>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', [
            'class' => 'btn btn-primary',
            'onclick' => 'prepareAndSubmit();' // Подготовка скрытых полей перед отправкой
        ]) ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<script>
    function checkType(chkBoxName) {
        var participantNumber = chkBoxName.split('-')[1]; // Разделяем строку и берем номер
        var teamDiv = document.getElementById(`act-team-participant-${participantNumber}`); // Получаем соответствующий div по ID
        var personDiv = document.getElementById(`act-personal-participant-${participantNumber}`); // Получаем соответствующий div по ID
        var teamDropdownList = document.getElementById(`team-dropdown-list-${participantNumber}`);
        teamDiv.hidden = true;
        personDiv.hidden = false;
       // teamDropdownList.hidden = true;

    }
    function checkSecondType(chkBoxName) {
        var participantNumber = chkBoxName.split('-')[1]; // Разделяем строку и берем номер
        var teamDiv = document.getElementById(`act-team-participant-${participantNumber}`); // Получаем соответствующий div по ID
        var personDiv = document.getElementById(`act-personal-participant-${participantNumber}`); // Получаем соответствующий div по ID
        var teamDropdownList = document.getElementById(`team-dropdown-list-${participantNumber}`);
        teamDiv.hidden = false;
        personDiv.hidden = true;
       // teamDropdownList.hidden = false;
    }
</script>
<script>
    document.getElementById('toggle-button').addEventListener('click', function() {
        const actsDiv = document.getElementById('acts');
        const commandsDiv = document.getElementById('commands');

        // Переключаем текст кнопки
        if (this.innerText === 'Перейти к заполнению участников мероприятия') {
            this.innerText = 'Вернуться к заполнению команд и номинаций';
            commandsDiv.style.pointerEvents = 'none'; // Блокируем ввод
            commandsDiv.style.opacity = '0.5'; // Уменьшаем непрозрачность
            actsDiv.style.pointerEvents = 'auto'; // Разблокируем ввод
            actsDiv.style.opacity = '1';  // Восстанавливаем непрозрачность
        } else {
            this.innerText = 'Перейти к заполнению участников мероприятия';
            //
            actsDiv.style.pointerEvents = 'none'; // Блокируем ввод
            actsDiv.style.opacity = '0.5'; // Уменьшаем непрозрачность
            commandsDiv.style.pointerEvents = 'auto'; // Разблокируем ввод
            commandsDiv.style.opacity = '1';  // Восстанавливаем непрозрачность
        }
    });
</script>
<script>
    function updateDivNames() {
        const divs = document.querySelectorAll('div.act-team-participant');
        divs.forEach((div, index) => {
            div.id = `act-team-participant-${index + 1}`; // Уникальное имя с индексом
        });
        requestAnimationFrame(updateDivNames);
    }
    // Запускаем первую функцию вызова
    requestAnimationFrame(updateDivNames);
</script>
<script>
    function updateTeamDropdownList() {
        var divs = document.querySelectorAll('div.team-dropdown-list');
        divs.forEach((div, index) => {
            div.id = `team-dropdown-list-${index + 1}`; // Уникальное имя с индексом
        });
        divs = document.querySelectorAll('div.personal-dropdown-list');
        divs.forEach((div, index) => {
            div.id = `personal-dropdown-list-${index + 1}`; // Уникальное имя с индексом
        });
        requestAnimationFrame(updateTeamDropdownList);
    }
    // Запускаем первую функцию вызова
    requestAnimationFrame(updateTeamDropdownList);
</script>
<script>
    function updateNominationDropdownList() {
        const divs = document.querySelectorAll('div.nomination-dropdown-list');
        divs.forEach((div, index) => {
            div.id = `nomination-dropdown-list-${index + 1}`; // Уникальное имя с индексом
        });
        requestAnimationFrame(updateNominationDropdownList);
    }
    // Запускаем первую функцию вызова
    requestAnimationFrame(updateNominationDropdownList);
</script>
<script>
    function updateDivPersonalNames() {
        const divs = document.querySelectorAll('div.act-personal-participant');
        divs.forEach((div, index) => {
            div.id = `act-personal-participant-${index + 1}`; // Уникальное имя с индексом
        });
        requestAnimationFrame(updateDivPersonalNames);
    }
    // Запускаем первую функцию вызова
    requestAnimationFrame(updateDivPersonalNames);
</script>
<script>
    function handleParticipationChange(radio) {
        const name = radio.name;
        const index = name.match(/\[(\d+)\]/);
        if (index) {
            let extractedIndex = index[1];
            extractedIndex++;
            var teamDropdownList = document.getElementById(`team-dropdown-list-${extractedIndex}`);
            var personalDropDownList = document.getElementById(`personal-dropdown-list-${extractedIndex}`);
            var formList = document.getElementById(`form-${extractedIndex - 1}--0`);
            if(formList != null) {
                if (radio.value === '0') {
                    teamDropdownList.hidden = true;
                    personalDropDownList.hidden = false;
                    formList.hidden = false;
                } else if (radio.value === '1') {
                    formList.hidden = false;
                    teamDropdownList.hidden = false;
                    personalDropDownList.hidden = true;
                }
            }
            var firstList = document.getElementById(`form-${extractedIndex - 1}`);
            if(firstList != null) {
                if (radio.value === '0') {
                    teamDropdownList.hidden = true;
                    personalDropDownList.hidden = false;
                    firstList.hidden = false;
                } else if (radio.value === '1') {
                    teamDropdownList.hidden = false;
                    personalDropDownList.hidden = true;
                    firstList.hidden = false;
                }
            }
        }
    }
    function updateOptions() {
        setTimeout(() => {
            FinishNom();
            FinishTeam();
        }, 1000); // Пауза 1 секунда (1000 миллисекунд)
    }
    // Вызывает updateOptions каждые 2000 миллисекунд (2 секунды)
   // setInterval(updateOptions, 2000);
</script>
<?php
    $js = <<<JS
    $(document).ready(function() {
        // Обработчик изменения выбора радио-кнопок
        $('input[name="OrderEventForm[docEvent]"]').change(function() {
            if ($(this).val() === '1' || $(this).val() === '2' || $(this).val() === '3') {
                $('#document_details').show();
            } else {
                $('#document_details').hide();
                $('#document_details input').val(''); // Очищаем поле при скрытии
            }
        });
        // Проверяем при загрузке страницы, если уже что-то выбрано
        var selected = $('input[name="OrderEventForm[docEvent]"]:checked').val();
        if (selected === '1' || selected === '2' || selected === '3') {
            $('#document_details').show();
        }
    });
    JS;
    $this->registerJs($js);
?>