<?php
namespace common\fixtures;

use yii\test\ActiveFixture;

class TipoBilheteFixture extends ActiveFixture
{
    public $modelClass = 'common\models\TipoBilhete';
    public $depends = [LocalCulturalFixture::class];
}