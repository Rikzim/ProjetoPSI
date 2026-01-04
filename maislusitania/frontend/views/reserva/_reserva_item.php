<?php
/** @var yii\web\View $this */
/** @var array $model - contains reserva, linha, ticketNumber, isExpirado */

use frontend\widgets\TicketCard;

echo TicketCard::widget([
    'reserva' => $model['reserva'],
    'linha' => $model['linha'],
    'ticketNumber' => $model['ticketNumber'],
    'isExpirado' => $model['isExpirado'],
]);
