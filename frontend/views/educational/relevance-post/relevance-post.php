<?php

use yii\helpers\Url;
use yii\web\View;

/* @var $idAttribute */
/* @var $urlString */
/* @var $urlBackString */

$url = Url::toRoute($urlString);
$urlBack = Url::toRoute($urlBackString);

$this->registerJs(<<<JS
        $(document).ready(function () {
            $('{$idAttribute}').on('click', function () {
                let actual = [];
                let unactual = [];
                let checkboxes = document.getElementsByClassName('check');
        
                for (let index = 0; index < checkboxes.length; index++) {
                    if (checkboxes[index].checked) {
                        actual.push(checkboxes[index].value);
                    } else {
                        unactual.push(checkboxes[index].value);
                    }
                }
                
                if (actual.length > 0 || unactual.length > 0) {
                    // Отправляем POST-запрос на экшен контроллера
                    $.ajax({
                        type: 'POST',
                        url: "$url",
                        data: {
                            actual: actual,
                            unactual: unactual
                        },
                        success: function(response) {
                            let parsedResponse = JSON.parse(response);
                            if (parsedResponse.success) {
                                window.location.href = "$urlBack";
                            } else {
                                alert('Ошибка: ' + parsedResponse.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Произошла ошибка: ' + xhr.responseText);
                        }
                    });
                } else {
                    alert('Не выбрано ни одного элемента!');
                }
            });
        });
        JS
    , View::POS_END);
