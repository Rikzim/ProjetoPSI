<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "evento".
 *
 * @property int $id
 * @property int $local_id
 * @property string $titulo
 * @property string|null $descricao
 * @property string $data_inicio
 * @property string|null $data_fim
 * @property string|null $imagem
 * @property int $ativo
 *
 * @property LocalCultural $local
 */
class Evento extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'evento';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descricao', 'data_fim', 'imagem'], 'default', 'value' => null],
            [['ativo'], 'default', 'value' => 1],
            [['local_id', 'titulo', 'data_inicio'], 'required'],
            [['local_id', 'ativo'], 'integer'],
            [['descricao'], 'string'],
            [['data_inicio', 'data_fim'], 'safe'],
            [['titulo'], 'string', 'max' => 200],
            [['imagem'], 'string', 'max' => 255],
            [['local_id'], 'exist', 'skipOnError' => true, 'targetClass' => LocalCultural::class, 'targetAttribute' => ['local_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'local_id' => 'Local',
            'titulo' => 'Titulo',
            'descricao' => 'Descricao',
            'data_inicio' => 'Data Inicio',
            'data_fim' => 'Data Fim',
            'imagem' => 'Imagem',
            'ativo' => 'Ativo',
        ];
    }

    /**
     * Gets query for [[Local]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLocal()
    {
        return $this->hasOne(LocalCultural::class, ['id' => 'local_id']);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token, 'status' => self::STATUS_ACTIVE]);
    }


    /**
     * Obter a imagem atual do evento
     * @return string|null
     */
    public function getImage()
    {
        if (empty($this->imagem)) {
            return null;
        }

        return '/uploads/' . $this->imagem;
    }
    public function getImageAPI()
    {
        if (empty($this->imagem)) {
            return null;
        }
        
        /* 
        Retorna a URL completa da imagem com o seguinte formato:
        - hostInfo: Obtém o esquema (http/https) e o host (domínio) da aplicação atual.
        - /projetopsi/maislusitania/frontend/web/uploads/: Caminho
        - $this->imagem_principal: Nome do arquivo da imagem armazenado no banco de dados.
        */
        return Yii::$app->request->hostInfo . '/projetopsi/maislusitania/frontend/web/uploads/' . $this->imagem; 
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        //Obter dados do registo em causa
        $myObj = new \stdClass();
        $myObj->id = $this->id;
        $myObj->local_id = $this->local_id;
        $myObj->local_nome = $this->local->nome;
        $myObj->titulo = $this->titulo;
        $myObj->data_inicio = $this->data_inicio;
        // Converter para JSON
        $myJSON = json_encode($myObj);
        if($insert)
            $this->FazPublishNoMosquitto("EVENTOS {$myObj->local_nome}",$myJSON);
        else
            $this->FazPublishNoMosquitto("EVENTOS",$myJSON);
    }

    public function afterDelete()
    {
        parent::afterDelete();

        $myObj = new \stdClass();
        // ID do evento eliminado
        $myObj->id = $this->id;
        // Converter para JSON
        $myJSON = json_encode($myObj);
        $this->FazPublishNoMosquitto("DELETE",$myJSON);
    }

    public function FazPublishNoMosquitto($canal, $msg)
    {
        $server = "172.22.21.218"; 
        $port = 1883;
        $username = ""; // set your username
        $password = ""; // set your password
        $client_id = "phpMQTT-publisher"; // unique!
        $mqtt = new \Bluerhinos\phpMQTT($server, $port, $client_id);

        if ($mqtt->connect(true, NULL, $username, $password)){
            $mqtt->publish($canal, $msg, 0);
            $mqtt->close();
        }else { 
            file_put_contents("debug.output","Time out!");
        }
    }
}
