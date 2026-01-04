<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "local_cultural".
 *
 * @property int $id
 * @property string $nome
 * @property int $tipo_id
 * @property string $morada
 * @property int $distrito_id
 * @property string $descricao
 * @property string|null $contacto_telefone
 * @property string|null $contacto_email
 * @property string|null $website
 * @property string|null $imagem_principal
 * @property int $ativo
 * @property float $latitude
 * @property float $longitude
 *
 * @property Avaliacao[] $avaliacaos
 * @property Distrito $distrito
 * @property Evento[] $eventos
 * @property Favorito[] $favoritos
 * @property Horario[] $horario
 * @property Noticia[] $noticias
 * @property Reserva[] $reservas
 * @property TipoLocal $tipo
 * @property TipoBilhete[] $tipoBilhetes
 * @property User[] $utilizadors
 * @property User[] $utilizadors0
 */
class LocalCultural extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'local_cultural';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['contacto_telefone', 'contacto_email', 'website', 'imagem_principal', 'horario_id'], 'default', 'value' => null],
            [['ativo'], 'default', 'value' => 1],
            [['nome', 'tipo_id', 'morada', 'distrito_id', 'descricao', 'latitude', 'longitude'], 'required'],
            [['tipo_id', 'distrito_id', 'ativo', 'horario_id'], 'integer'],
            [['descricao'], 'string'],
            [['latitude', 'longitude'], 'number'],
            [['nome'], 'string', 'max' => 200],
            [['morada', 'website', 'imagem_principal'], 'string', 'max' => 255],
            [['contacto_telefone'], 'string', 'max' => 20],
            [['contacto_email'], 'string', 'max' => 100],
            [['horario_id'], 'unique'],
            [['horario_id'], 'exist', 'skipOnError' => true, 'targetClass' => Horario::class, 'targetAttribute' => ['horario_id' => 'id']],
            [['tipo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoLocal::class, 'targetAttribute' => ['tipo_id' => 'id']],
            [['distrito_id'], 'exist', 'skipOnError' => true, 'targetClass' => Distrito::class, 'targetAttribute' => ['distrito_id' => 'id']],
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
            'tipo_id' => 'Tipo Local',
            'morada' => 'Morada',
            'distrito_id' => 'Distrito',
            'descricao' => 'Descricao',
            'contacto_telefone' => 'Contacto Telefone',
            'contacto_email' => 'Contacto Email',
            'website' => 'Website',
            'imagem_principal' => 'Imagem Principal',
            'ativo' => 'Ativo',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'horario_id' => 'Horario',
        ];
    }

    /**
     * Gets query for [[Avaliacaos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAvaliacaos()
    {
        return $this->hasMany(Avaliacao::class, ['local_id' => 'id']);
    }

    /**
     * Gets query for [[Distrito]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDistrito()
    {
        return $this->hasOne(Distrito::class, ['id' => 'distrito_id']);
    }

    /**
     * Gets query for [[Eventos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEventos()
    {
        return $this->hasMany(Evento::class, ['local_id' => 'id']);
    }

    /**
     * Gets query for [[Favoritos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFavoritos()
    {
        return $this->hasMany(Favorito::class, ['local_id' => 'id']);
    }

    /**
     * Gets query for [[Horario]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHorario()
    {
        return $this->hasOne(Horario::class, ['id' => 'horario_id']);
    }

    /**
     * Gets query for [[Noticias]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNoticias()
    {
        return $this->hasMany(Noticia::class, ['local_id' => 'id']);
    }

    /**
     * Gets query for [[Reservas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReservas()
    {
        return $this->hasMany(Reserva::class, ['local_id' => 'id']);
    }

    /**
     * Gets query for [[Tipo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTipoLocal()
    {
        return $this->hasOne(TipoLocal::class, ['id' => 'tipo_id']);
    }
    /**
     * Gets query for [[TipoBilhetes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTipoBilhetes()
    {
        return $this->hasMany(TipoBilhete::class, ['local_id' => 'id']);
    }

    /**
     * Gets query for [[Utilizadors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUtilizadors()
    {
        return $this->hasMany(User::class, ['id' => 'utilizador_id'])->viaTable('avaliacao', ['local_id' => 'id']);
    }

    /**
     * Gets query for [[Utilizadors0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUtilizadors0()
    {
        return $this->hasMany(User::class, ['id' => 'utilizador_id'])->viaTable('favorito', ['local_id' => 'id']);
    }

    public function getAvaliacoes()
    {
        return $this->hasMany(Avaliacao::class, ['local_id' => 'id']);
    }

    public function getAverageRating()
    {
        $query = $this->getAvaliacoes()->where(['ativo' => 1]);
        $count = $query->count();

        if ($count == 0) {
            return 0;
        }

        $sum = $query->sum('classificacao');
        return round($sum / $count, 1);
    }

    public function getRatingCount()
    {
        return (int) $this->getAvaliacoes()
            ->where(['ativo' => 1])
            ->count();
    }
    public function isFavoritedByUser($userId)
    {
        return $this->getFavoritos()
            ->where(['utilizador_id' => $userId])
            ->exists();
    }

    public function getFavoritoIdByUser($userId)
    {
        $favorito = Favorito::find()
            ->where(['utilizador_id' => $userId, 'local_id' => $this->id]) // Filtra pelo utilizador e local cultural
            ->one();
        
        return $favorito ? $favorito->id : null; // Retorna o ID do favorito ou null se não existir
    }

    public function getImage()
    {
        if (empty($this->imagem_principal)) {
            return null;
        }

        return '/uploads/' . $this->imagem_principal; 
    }

    public function getImageAPI()
    {
        if (empty($this->imagem_principal)) {
            return null;
        }
        
        /* 
        Retorna a URL completa da imagem com o seguinte formato:
        - hostInfo: Obtém o esquema (http/https) e o host (domínio) da aplicação atual.
        - /projetopsi/maislusitania/frontend/web/uploads/: Caminho
        - $this->imagem_principal: Nome do arquivo da imagem armazenado no banco de dados.
        */
        return Yii::$app->request->hostInfo . '/projetopsi/maislusitania/frontend/web/uploads/' . $this->imagem_principal; 
    }

    public function beforeDelete()
    {
        // Remover imagem principal se existir
        $currentImage = $this->imagem_principal;
        if (!empty($currentImage)) {
            $imagePath = Yii::getAlias('@uploadPath') . '/' . $currentImage;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Remover tipos de bilhete associados
        foreach ($this->tipoBilhetes as $tipoBilhete) {
            $tipoBilhete->delete();
        }

        return parent::beforeDelete();
    }

    public function afterDelete()
    {
        // Remover horário associado
        $horario = $this->horario;
        if ($horario) {
            $horario->delete();
        }

        parent::afterDelete();
    }
}
