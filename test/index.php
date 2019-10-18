<?php

require_once __DIR__ . '/../vendor/autoload.php';

use \Bdourado\SigepCorreios\SigepCorreios;

//Para utilizar os serviçõs do SIGEP WEB preencha as seguintes variáveis

$login          = 'seuLoginDosCorreios';
$senha          = 'suaSenhaDosCorreios';
$codAdm         = 'seuCodigoAdministrativoDosCorreios';
$numContrato    = 'seuNumeeroDoContratoDosCorreios';
$codPostagem    = 'seuCOdigoDePostagemDosCorreios';
$cnpj           = 'seuCNPJ';
$cepOrigem      = 'seuCepDeOrigem';


$sigep = new SigepCorreios($login, $senha, $codAdm, $numContrato, $codPostagem, $cnpj, $cepOrigem);

//consultar serviços disponíveis
$buscaCliente = $sigep->buscaCliente();


//Para utilizar o rastreio de objetos, basta chamar diretamente o método estático

$codObjeto = 'informeAquiOCodigoDoObjeto';
$sro = SigepCorreios::getRastro($codObjeto);
