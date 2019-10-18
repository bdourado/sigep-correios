# SigepCorreios
Classe para trabalhar com os serviços dos Correios

### Serviços disponíveis

* Verificar disponibilidade do serviço
* Consultar Serviços disponíveis
* Consultar CEP
* Consultar situação do cartão de postagem
* Solicitar faixa de etiquetas para postagem
* Gerar dígito verificar de etiquetas
* Fechar pré-lista de postagem de objetos
* Consultar valor do frete
* Rastrear objeto

### Instalação

<code>composer require bdourado/sigep-correios</code>


### Como Usar

```php
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


//Para utilizar o rastreio de objetos, basta chamar diretamente o método

$codObjeto = 'informeAquiOCodigoDoObjeto';
$sro = SigepCorreios::getRastro($codObjeto);

```

