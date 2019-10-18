<?php
/**
 * User: bruno dourado
 * Date: 07/01/19
 */

namespace Bdourado\SigepCorreios;

class SigepCorreios
{
    const SIGEP_URL     = 'https://apps.correios.com.br/SigepMasterJPA/AtendeClienteService/AtendeCliente?wsdl';
    const FRETE_URL     = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx?wsdl';
    const RASTRO_URL    = 'http://webservice.correios.com.br/service/rastro/Rastro.wsdl';

    private static $tipos = array(
        'sedex'          => '04014',
        'sedex_a_cobrar' => '40045',
        'sedex_10'       => '40215',
        'sedex_12'       => '40169',
        'sedex_hoje'     => '40290',
        'pac'            => '04510',
        'pac_contrato'   => '04669',
        'sedex_contrato' => '04162',
        'esedex'         => '81019',
    );

    private $login;
    private $senha;
    private $codAdm;
    private $cepOrigem;
    private $numeroContrato;
    private $codigoPostagem;
    private $cnpj;
    private $soapConnection;

    public function __construct(
        $login = '',
        $senha = '',
        $codAdm = '',
        $numeroContrato = '',
        $codigoPostagem = '',
        $cnpj = '',
        $cepOrigem = ''
    )
    {
        ini_set('default_socket_timeout', 1);
        if ( !empty($login) && !empty($senha) ) {
            $this->login = $login;
            $this->senha = $senha;
            $this->codAdm = $codAdm;
            $this->numeroContrato = $numeroContrato;
            $this->codigoPostagem = $codigoPostagem;
            $this->cnpj = $cnpj;
            $this->cepOrigem = $cepOrigem;

            $endpoint = SELF::SIGEP_URL;

            $params = array(
                'trace' => true,
                'exceptions' => true,
                'connection_timeout' => 10,
                'cache_wsdl' => 2,
                'encoding' => 'UTF-8',
                'compression'        => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
                'stream_context' => stream_context_create(
                    array(
                        'http' => array(
                            'protocol_version' => '1.0',
                            'header' => 'Connection: Close'
                        )
                    )
                )
            );

            try{
                $this->soapConnection = new \SoapClient($endpoint, $params);
            }catch (\Exception $exception){
                return $exception->getMessage();
            }

        }
    }

    public function verificaDisponibilidadeServico($numeroServico,$cepDestino)
    {
        $params = array(
            'codAdministrativo' => $this->codAdm,
            'numeroServico' => $numeroServico,
            'cepOrigem' => $this->cepOrigem,
            'cepDestino' => $cepDestino,
            'usuario' => $this->login,
            'senha' => $this->senha
        );

        try{
            $verificaDisponibilidadeServico = $this->soapConnection->verificaDisponibilidadeServico($params);
            $return = get_object_vars($verificaDisponibilidadeServico);
            return $return['return'];
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }

    public function buscaCliente()
    {
        $params = array(
            'idContrato' => $this->numeroContrato,
            'idCartaoPostagem' => $this->codigoPostagem,
            'usuario' => $this->login,
            'senha' => $this->senha
        );

        try {
            $buscaCliente = $this->soapConnection->buscaCliente($params);
            $return = get_object_vars($buscaCliente);
            return $return['return'];
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }

    public function consultaCep($cep)
    {
        $params = array('cep' => $cep);

        try{
            $consultaCEP = $this->soapConnection->consultaCEP($params);
            $return = get_object_vars($consultaCEP);
            return $return['return'];
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }

    public function getStatusCartaoPostagem()
    {
        $params = array(
            'numeroCartaoPostagem' => $this->codigoPostagem,
            'usuario' => $this->login,
            'senha' => $this->senha
        );

        try{
            $getStatusCartaoPostagem = $this->soapConnection->getStatusCartaoPostagem($params);
            $return = get_object_vars($getStatusCartaoPostagem);
            return $return['return'];
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }

    public function solicitaEtiquetas($idServico, $qtdEtiquetas)
    {
        $params = array(
            'tipoDestinatario' => 'C',
            'identificador' => $this->cnpj,
            'idServico' => $idServico,
            'qtdEtiquetas' => $qtdEtiquetas,
            'usuario' => $this->login,
            'senha' => $this->senha
        );

        try {
            $solicitaEtiquetas = $this->soapConnection->solicitaEtiquetas($params);
            $return = get_object_vars($solicitaEtiquetas);
            return $return['return'];
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }

    public function geraDigitoVerificadorEtiquetas($etiqueta)
    {
        $params = array(
            'etiquetas' => $etiqueta,
            'usuario' => $this->login,
            'senha' => $this->senha
        );

        try {
            $geraDigitoVerificadorEtiquetas = $this->soapConnection->geraDigitoVerificadorEtiquetas($params);
            $return = get_object_vars($geraDigitoVerificadorEtiquetas);
            return $return['return'];
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }

    public function fechaPLP($xmlPlp, $faixaEtiquetas)
    {
        $params = array(
            "xml" => $xmlPlp,
            "idPlpCliente" => '',
            "cartaoPostagem" => CORR_COD_POSTAGEM,
            "faixaEtiquetas" => $faixaEtiquetas,
            'usuario' => $this->login,
            'senha' => $this->senha
        );

        try{
            $fechaPLP = $this->soapConnection->fechaPLP($params);
            $return = get_object_vars($fechaPLP);
            return $return['return'];
        }catch (\Exception $exception){
            return $exception->getMessage();
        }

    }

    public function fechaPlpVariosServicos($xmlPlp, $listaEtiquetas)
    {
        $params = array(
            'xml' => $xmlPlp,
            'idPlpCliente' => '',
            'cartaoPostagem' => $this->codigoPostagem,
            'listaEtiquetas' => $listaEtiquetas,
            'usuario' => $this->login,
            'senha' => $this->senha
        );

        try{
            $fechaPlpVariosServicos = $this->soapConnection->fechaPlpVariosServicos($params);
            $return = get_object_vars($fechaPlpVariosServicos);
            return $return['return'];
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }

    /**
     * Função reutilizada da classe correios-consulta do Cagartner
     * https://github.com/cagartner/correios-consulta
     *
     * @param $dados
     * @param array $options
     * @return array|mixed
     */

    public function getValorFrete($dados, $options = array())
    {
        $endpoint = self::FRETE_URL;

        $tipos = self::getTipoInline($dados['tipo']);

        $formatos = array(
            'caixa'    => 1,
            'rolo'     => 2,
            'envelope' => 3,
        );

        $dados['tipo']    = $tipos;
        $dados['formato'] = $formatos[$dados['formato']];

        $dados['cep_destino'] = preg_replace("/[^0-9]/", '', $dados['cep_destino']);

        $options = array_merge(array(
            'trace'              => true,
            'exceptions'         => true,
            'compression'        => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
            'connection_timeout' => 20
        ), $options);

        $soap = new \SoapClient($endpoint, $options);

        $params = array(
            'nCdEmpresa'          => $this->codAdm,
            'sDsSenha'            => $this->senha,
            'nCdServico'          => $dados['tipo'],
            'sCepOrigem'          => $this->cepOrigem,
            'sCepDestino'         => $dados['cep_destino'],
            'nVlPeso'             => $dados['peso'],
            'nCdFormato'          => $dados['formato'],
            'nVlComprimento'      => $dados['comprimento'],
            'nVlAltura'           => $dados['altura'],
            'nVlLargura'          => $dados['largura'],
            'nVlDiametro'         => $dados['diametro'],
            'sCdMaoPropria'       => (isset($dados['mao_propria']) && $dados['mao_propria'] ? 'S' : 'N'),
            'nVlValorDeclarado'   => (isset($dados['valor_declarado']) ? $dados['valor_declarado'] : 0),
            'sCdAvisoRecebimento' => (isset($dados['aviso_recebimento']) && $dados['aviso_recebimento'] ? 'S' : 'N'),
            'sDtCalculo'          => date('d/m/Y'),
        );

        $CalcPrecoPrazoData = $soap->CalcPrecoPrazoData($params);
        
        if (empty($CalcPrecoPrazoData)){
            throw new \Exception('Erro ao consultar os preços');
        }
        
        $resultado          = $CalcPrecoPrazoData->CalcPrecoPrazoDataResult->Servicos->cServico;

        if (!is_array($resultado))
            $resultado = array($resultado);

        $dados = array();

        foreach ($resultado as $consulta)
        {
            $consulta = (array) $consulta;

            $dados[] = array(
                'codigo'             => $consulta['Codigo'],
                'valor'              => (float) str_replace(',', '.', $consulta['Valor']),
                'prazo'              => (int) str_replace(',', '.', $consulta['PrazoEntrega']),
                'mao_propria'        => (float) str_replace(',', '.', $consulta['ValorMaoPropria']),
                'aviso_recebimento'  => (float) str_replace(',', '.', $consulta['ValorAvisoRecebimento']),
                'valor_declarado'    => (float) str_replace(',', '.', $consulta['ValorValorDeclarado']),
                'entrega_domiciliar' => ($consulta['EntregaDomiciliar'] === 'S' ? true : false),
                'entrega_sabado'     => ($consulta['EntregaSabado'] === 'S' ? true : false),
                'erro'               => array('codigo' => (real) $consulta['Erro'], 'mensagem' => $consulta['MsgErro']),
            );
        }

        return $dados;
    }


    public static function getRastro($codObjeto, $loginSRO = 'ECT', $senhaSRO = 'SRO')
    {
        $params = array(
            'trace' => true,
            'exceptions' => true,
            'connection_timeout' => 10,
            'cache_wsdl' => 2,
            'encoding' => 'UTF-8',
            'compression'        => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
            'stream_context' => stream_context_create(
                array(
                    'http' => array(
                        'protocol_version' => '1.0',
                        'header' => 'Connection: Close'
                    )
                )
            )
        );

        $endpoint = self::RASTRO_URL;

        $soapConnection = new \SoapClient($endpoint,$params);

        $params = array(
            'usuario'   => $loginSRO,
            'senha'     => $senhaSRO,
            'tipo'      => 'L',
            'resultado' => 'T',
            'lingua'    => '101',
            'objetos'   => $codObjeto
        );

        try{
            $buscaEventos = $soapConnection->buscaEventos($params);
            $return = get_object_vars($buscaEventos);
            return $return['return'];
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }



    /**
     * Função reutilizada da classe correios-consulta do Cagartner
     * https://github.com/cagartner/correios-consulta
     *
     * Retorna todos os codigos em uma linha
     *
     * @param $valor string
     * @return string
     */
    public static function getTipoInline($valor)
    {
        $explode = explode(",", $valor);
        $tipos   = array();

        foreach ($explode as $value)
        {
            $tipos[] = self::$tipos[$value];
        }

        return implode(",", $tipos);
    }

}
