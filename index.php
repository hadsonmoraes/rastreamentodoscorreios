<?php
require 'vendor/autoload.php';
require 'functions.php';

use GuzzleHttp\Client;

$client = new GuzzleHttp\Client();

use GuzzleHttp\Exception\RequestException;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$tokenResponse = null;

$usuario = $_ENV['usuario'];
$codigoAcesso = $_ENV['codigoAcesso'];
$Vtoken = base64_encode("$usuario:$codigoAcesso");
$cartaoPostagem = $_ENV['cartaoPostagem'];
$correiosUrl = $_ENV['correiosUrl'];

if (isset($_GET['codigo'])) {
    $codigo = trim($_GET['codigo']);

    if (!empty($codigo)) {


        $tokenResponse = $client->post("$correiosUrl/token/v1/autentica/cartaopostagem", [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Basic $Vtoken",
                'Content-Type' => 'application/json',
            ],
            'json' => ['numero' => $cartaoPostagem],
        ]);

        $tokenData = json_decode($tokenResponse->getBody()->getContents(), true);

        // Imprime a resposta completa do token para depuração
        // echo json_encode(['tokenResponse' => $tokenData], JSON_PRETTY_PRINT);
        $accessToken = $tokenData['token'];
        // Coloque aqui o restante do código para obter o resultado do rastreamento

        $url = "$correiosUrl/srorastro/v1/objetos?codigosObjetos={$codigo}&resultado=T";

        // Substitua 'seu_token_aqui' pelo token de autenticação real
        $token = $accessToken;

        $options = [
            'http' => [
                'header' => "Authorization: Bearer {$token}\r\n"
            ]
        ];


        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            echo 'Erro na requisição para a API dos Correios. Por favor, tente novamente.';
            exit;
        }

        $data = json_decode($response, true);

        if ($data === null) {
            echo 'Erro ao decodificar a resposta da API dos Correios. Por favor, tente novamente.';
            exit;
        }

        echo '<html lang="en">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        echo '<title>Rastreamento de Encomenda</title>';
        echo '<link rel="stylesheet" href="styles.css">';
        echo '</head>';
        echo '<body>';
        echo '<div class="container">';
        echo '<h1>Resultado do Rastreamento</h1>';
        exibirResultados($data['objetos'][0]); // Exibe os resultados
        echo '</div>';
        echo '</body>';
        echo '</html>';
        exit;

        // echo '<h1>Resultado do Rastreamento</h1>';
        // exibirResultados($data['objetos'][0]); // Exibe os resultados

        // echo '<pre>' . htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT)) . '</pre>';
    }
}
