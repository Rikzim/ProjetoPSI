<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "reserva".
 *
 * @property int $id
 * @property int $utilizador_id
 * @property int $local_id
 * @property string $data_visita
 * @property float $preco_total
 * @property string|null $estado
 * @property string|null $data_criacao
 *
 * @property LinhaReserva[] $linhaReservas
 * @property LocalCultural $local
 * @property User $utilizador
 */
class Reserva extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 10;

    /**
     * ENUM field values
     */
    const ESTADO_EXPIRADO = 'Expirado';
    const ESTADO_CONFIRMADA = 'Confirmada';
    const ESTADO_CANCELADA = 'Cancelada';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reserva';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['estado'], 'default', 'value' => '_utf8mb4\'Pendente\''],
            [['utilizador_id', 'local_id', 'data_visita', 'preco_total'], 'required'],
            [['utilizador_id', 'local_id'], 'integer'],
            [['data_visita', 'data_criacao'], 'safe'],
            [['preco_total'], 'number'],
            [['estado'], 'string'],
            ['estado', 'in', 'range' => array_keys(self::optsEstado())],
            [['utilizador_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['utilizador_id' => 'id']],
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
            'utilizador_id' => 'Utilizador',
            'local_id' => 'Local',
            'data_visita' => 'Data Visita',
            'preco_total' => 'Preco Total',
            'estado' => 'Estado',
            'data_criacao' => 'Data Criacao',
        ];
    }

    /**
     * Gets query for [[LinhaReservas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLinhaReservas()
    {
        return $this->hasMany(LinhaReserva::class, ['reserva_id' => 'id']);
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

    /**
     * Gets query for [[Utilizador]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUtilizador()
    {
        return $this->hasOne(User::class, ['id' => 'utilizador_id']);
    }


    /**
     * column estado ENUM value labels
     * @return string[]
     */
    public static function optsEstado()
    {
        return [
            self::ESTADO_EXPIRADO => 'Pendente',
            self::ESTADO_CONFIRMADA => 'Confirmada',
            self::ESTADO_CANCELADA => 'Cancelada',
        ];
    }

    /**
     * @return string
     */
    public function displayEstado()
    {
        return self::optsEstado()[$this->estado];
    }

    /**
     * @return bool
     */
    public function isEstadoExpirado()
    {
        return $this->estado === self::ESTADO_EXPIRADO;
    }

    public function setEstadoToExpirado()
    {
        $this->estado = self::ESTADO_EXPIRADO;
    }

    /**
     * @return bool
     */
    public function isEstadoConfirmada()
    {
        return $this->estado === self::ESTADO_CONFIRMADA;
    }

    public function setEstadoToConfirmada()
    {
        $this->estado = self::ESTADO_CONFIRMADA;
    }

    /**
     * @return bool
     */
    public function isEstadoCancelada()
    {
        return $this->estado === self::ESTADO_CANCELADA;
    }

    public function setEstadoToCancelada()
    {
        $this->estado = self::ESTADO_CANCELADA;
    }
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    //Metodos adicionados
    /**
     * Validar e preparar dados dos bilhetes
     * @param array $bilhetesPost
     * @return array ['bilhetes' => [], 'precoTotal' => 0]
     * @throws \Exception
     */
    private function prepararBilhetes($bilhetesPost)
    {
        $bilhetesParaGravar = [];
        $precoTotal = 0;

        foreach ($bilhetesPost as $tipoBilheteId => $bilheteData) {
            $quantidade = (int)($bilheteData['quantidade'] ?? 0);

            if ($quantidade > 0) {
                $tipoBilhete = TipoBilhete::findOne($tipoBilheteId);

                if ($tipoBilhete === null) {
                    throw new \Exception("Tipo de bilhete inválido: {$tipoBilheteId}");
                }

                $precoUnitario = floatval($tipoBilhete->preco);
                $subtotal = $quantidade * $precoUnitario;
                $precoTotal += $subtotal;

                $bilhetesParaGravar[] = [
                    'tipo_bilhete_id' => $tipoBilheteId,
                    'tipo' => $tipoBilhete->nome, // Para exibir na confirmação
                    'quantidade' => $quantidade,
                    'preco_unitario' => $precoUnitario,
                    'subtotal' => $subtotal,
                ];
            }
        }

        if (empty($bilhetesParaGravar)) {
            throw new \Exception('Selecione pelo menos 1 bilhete com quantidade maior que 0.');
        }

        return [
            'bilhetes' => $bilhetesParaGravar,
            'precoTotal' => $precoTotal,
        ];
    }

    /**
     * Simplificar GuardarReserva
     */

    public function GuardarReserva($postData)
    {
        // 1. Validações básicas
        if (empty($postData['local_id'])) {
            throw new \Exception('Local não especificado.');
        }

        if (empty($postData['bilhetes'])) {
            throw new \Exception('Nenhum bilhete selecionado.');
        }

        // 2. Preparar bilhetes (validação + cálculo)
        $dadosBilhetes = $this->prepararBilhetes($postData['bilhetes']);

        // 3. Validar data antes de gravar
        $this->data_visita = $postData['data_visita'] ?? date('Y-m-d');

        if (!$this->validateData($this->data_visita)) {
            throw new \Exception('Data de visita inválida. Não pode ser domingo ou uma data passada.');
        }

        // 4. Gravar reserva
        $this->utilizador_id = Yii::$app->user->id;
        $this->local_id = $postData['local_id'];
        $this->preco_total = $dadosBilhetes['precoTotal'];
        $this->setEstadoToConfirmada();
        $this->data_criacao = date('Y-m-d H:i:s');

        if (!$this->save()) {
            throw new \Exception('Erro ao gravar reserva: ' . json_encode($this->errors));
        }

        // 5. Gravar linhas
        foreach ($dadosBilhetes['bilhetes'] as $bilheteData) {
            $linhaReserva = new LinhaReserva();
            $linhaReserva->reserva_id = $this->id;
            $linhaReserva->tipo_bilhete_id = $bilheteData['tipo_bilhete_id'];
            $linhaReserva->quantidade = $bilheteData['quantidade'];

            if (!$linhaReserva->save()) {
                throw new \Exception('Erro ao gravar linha: ' . json_encode($linhaReserva->errors));
            }
        }

        return $this;
    }

    /**
     * Obter dados para página de create/confirmação
     */
    public static function obterDadosConfirmacao($postData)
    {
        if (empty($postData['local_id'])) {
            throw new \Exception('Local não especificado.');
        }

        $local = \common\models\LocalCultural::findOne($postData['local_id']);
        if (!$local) {
            throw new \Exception('Local não encontrado.');
        }

        // Reutilizar a lógica privada através de uma instância temporária
        $reservaTemp = new self();
        $dadosBilhetes = $reservaTemp->prepararBilhetes($postData['bilhetes'] ?? []);

        return [
            'local' => $local,
            'bilhetes' => $dadosBilhetes['bilhetes'],
            'precoTotal' => $dadosBilhetes['precoTotal'],
        ];
    }

    public static function validateData($data_visita)
    {
        // Verificar se a data é válida
        $timestamp = strtotime($data_visita);
        if ($timestamp === false) {
            return false;
        }

        // Verificar se é domingo (0 = domingo)
        $diaSemana = date('w', $timestamp);
        if ($diaSemana == 0) {
            return false;
        }

        // Verificar se a data é no passado
        $hoje = strtotime(date('Y-m-d'));
        if ($timestamp < $hoje) {
            return false;
        }

        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        //Obter dados do registo em causa
        $myObj = new \stdClass();
        // ID da reserva
        $myObj->id = $this->id;
        // ID e nome do utilizador
        $myObj->utilizador_id = $this->utilizador_id;
        $myObj->nome = $this->utilizador->username;
        // ID e nome do local
        $myObj->local_id = $this->local_id;
        $myObj->local_nome = $this->local->nome;
        // Data da visita
        $myObj->data_visita = $this->data_visita;
        // Preço total
        $myObj->preco_total = $this->preco_total;
        // Estado
        $myObj->estado = $this->estado;
        // Data de criação
        $myObj->data_criacao = $this->data_criacao;
        // Converter para JSON
        $myJSON = json_encode($myObj);
        if($insert)
            $this->FazPublishNoMosquitto("Reserva INSERT",$myJSON);
        else
            $this->FazPublishNoMosquitto("Reserva UPDATE",$myJSON);
    }

    public function afterDelete()
    {
        parent::afterDelete();

        $myObj = new \stdClass();
        // ID da reserva
        $myObj->id = $this->id;
        // Converter para JSON
        $myJSON = json_encode($myObj);
        $this->FazPublishNoMosquitto("DELETE",$myJSON);
    }

    public function FazPublishNoMosquitto($canal, $msg)
    {
        $server = "172.22.21.218"; 
        $port = 443;
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
