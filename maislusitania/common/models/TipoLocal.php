<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tipo_local".
 *
 * @property int $id
 * @property string $nome
 * @property string $descricao
 * @property string|null $icone
 *
 * @property LocalCultural[] $localCulturals
 */
class TipoLocal extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipo_local';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['icone'], 'default', 'value' => null],
            [['nome', 'descricao'], 'required'],
            [['nome'], 'string', 'max' => 50],
            [['descricao', 'icone'], 'string', 'max' => 255],
            [['nome'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'descricao' => 'Descricao',
            'icone' => 'Icone',
        ];
    }

    /**
     * Gets query for [[LocalCulturals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLocalCulturals()
    {
        return $this->hasMany(LocalCultural::class, ['tipo_id' => 'id']);
    }
    public function getImage()
    {
        if (empty($this->icone)) {
            return null;
        }

        return '/uploads/' . $this->icone;
    }
    public function getImageAPI()
    {
        if (empty($this->icone)) {
            return null;
        }
        
        /* 
        Retorna a URL completa da imagem com o seguinte formato:
        - hostInfo: Obtém o esquema (http/https) e o host (domínio) da aplicação atual.
        - /projetopsi/maislusitania/frontend/web/uploads/: Caminho
        - $this->imagem_principal: Nome do arquivo da imagem armazenado no banco de dados.
        */
        return Yii::$app->request->hostInfo . '/projetopsi/maislusitania/frontend/web/uploads/' . $this->icone; 
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        // Apagar o ícone associado ao TipoLocal
        if (!empty($this->icone)) {
            $imagePath = Yii::getAlias('@uploadPath') . '/' . $this->icone;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        // Apagar todos os LocaisCulturais associados a este TipoLocal
        foreach ($this->localCulturals as $localCultural) {
            $localCultural->delete();
        }

        return true;
    }
}
