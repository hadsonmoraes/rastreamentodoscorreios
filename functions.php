<?php


function formatarDataHora($dataHora)
{
    $data = new DateTime($dataHora);
    return $data->format('d/m/Y H:i:s');
}


function exibirResultados($objeto)
{
    // echo '<h2>Informações Gerais do Objeto:</h2>';
    echo '<img src="images/correios.png" alt="Logo Correios" class="logo-correios">';
    echo '<p style="font-size: 20px;"><strong>'. $objeto['codObjeto'] .'</strong></p>';

    if (isset($objeto['eventos']) && is_array($objeto['eventos'])) {
    echo '<p>'. $objeto['eventos'][0]['descricao'] .'</p>';
    echo '<p><strong>DATA: </strong>'. formatarDataHora($objeto['eventos'][0]['dtHrCriado']) .'</p>';

    echo '<table>';
    echo '<tr><th>Data e Hora</th><th>Descrição</th><th>Detalhe</th><th>Local</th></tr>';
  
    foreach ($objeto['eventos'] as $evento) {
        echo '<tr>';
        echo '<td>' . formatarDataHora($evento['dtHrCriado']) . '</td>';
        echo '<td>' . $evento['descricao'] . '</td>';
        echo '<td>' . ($evento['detalhe'] ?? '-') . '</td>';

        // Verifica o código do evento
        if ($evento['codigo'] == 'PAR') {
            // Se o código for 'PAR', mostra cidade e UF
            echo '<td>' . $evento['unidade']['endereco']['cidade'] . ', ' . $evento['unidade']['endereco']['uf'] . '</td>';
        } elseif ($evento['codigo'] == 'PO') {
            // Se o código for 'PO', mostra o nome da unidade
            echo '<td>' . $evento['unidade']['nome'] . '</td>';
        } else {
            // Adicione aqui outras condições conforme necessário
            echo '<td>Dados de local indisponíveis</td>';
        }

        echo '</tr>';
      }
     } else {
      echo "<br>";
        echo '<p style="color: red; font-size: 20px">Código de rastreamento inválido.</p>';
    }

    echo '</table>';
}

function validarCodigoRastreamento($codigo)
{
    // Padrão: 2 letras, 9 números, 2 letras
    $padrao = '/^[A-Z]{2}\d{9}[A-Z]{2}$/';

    return preg_match($padrao, $codigo) === 1;
}



?>
