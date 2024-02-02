<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar se o usuÃ¡rio está logado
if (!isset($_SESSION['usuario']) || !isset($_SESSION['senha'])) {
    // Redireciona para a página de login
    header('Location: index.php');
    exit();
}

// Realiza a conexão com o banco de dados
require_once 'conexao.php';

// Exclui os dados de login ao clicar em "Sair"
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit(); 
}

// Função para validar a entrada do usuário
function validarEntrada($entrada) {
    return htmlspecialchars(trim($entrada), ENT_QUOTES, 'UTF-8');
}

// Variáveis para armazenar os valores do formulário
$registrationNumber = "";
$tributo = '1306';
// Variável para verificar se a tabela deve ser exibida
$showTable = isset($_GET['show-table']) && $_GET['show-table'] === 'true';
// Variáveis para controlar as classes CSS de ocultação
$tableClass = 'hidden';
$buttonsClass = 'hidden';
// VariÃ¡veis para armazenar nome e número de inscrição
$nome = "";
$numeroInscricao = "";
// Variável para verificar se a pesquisa foi realizada
$pesquisaRealizada = false;

// Consultar o banco de dados apenas se o tributo estiver selecionado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $registrationNumber = validarEntrada($_POST['registration-number']);
    // Verificar se o campo de Inscriçãoo Municipal está vazio
    if (empty($registrationNumber)) {
        echo '<script>alert("Por favor, insira a Inscrição Municipal.");</script>';
    } else {
        $sql = "SELECT AAEXERCICIO, CDINSCRICAOALT, NUBASE, VLLANCADODECLARADO, STCONTACONTRIBUINTE, SQCONTACONTRIBUINTE 
        FROM AUTONOMO AUT 
        INNER JOIN CONTACONTRIBUINTE CC ON CC.NUBASE = AUT.CDINSCRICAOAUTON
        WHERE AUT.CDINSCRICAOALT = :sqcontacontribuinte  
        AND CC.CDRECEITA = '1306'
        AND EXTRACT(YEAR FROM TO_DATE(AAEXERCICIO, 'YYYY')) <= EXTRACT(YEAR FROM SYSDATE) - 5";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':sqcontacontribuinte', $registrationNumber);
        oci_execute($stmt);
        // Verificar se há situações "A" para este tributo
        while ($row = oci_fetch_assoc($stmt)) {
            $situacao = $row['STCONTACONTRIBUINTE'];
            if ($situacao === 'A') {
                $showTable = true;
                $tableClass = ''; // Remove a classe 'hidden' para exibir a tabela
                $buttonsClass = ''; // Remove a classe 'hidden' para exibir os botões
                break;
            }
        }
        // A pesquisa foi realizada
        $pesquisaRealizada = true;
    }
}

// Obtém o nome e o número de inscrição
$sql_info = "SELECT NOMEAUTON(CDINSCRICAOAUTON) AS NOME_AUTON, FormataInscAlt(CDINSCRICAOALT) AS CDINSCRICAOALT FROM AUTONOMO WHERE CDINSCRICAOALT = :sqcontacontribuinte";;
$stmt_info = oci_parse($conn, $sql_info);
oci_bind_by_name($stmt_info, ':sqcontacontribuinte', $registrationNumber);
oci_execute($stmt_info);
if ($row_info = oci_fetch_assoc($stmt_info)) {
    $nome = $row_info['NOME_AUTON'];
    $numeroInscricao = $row_info['CDINSCRICAOALT'];
}
?>

<!DOCTYPE html>
<html lang="pt">
<meta charset="utf-8" />
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Prescrição Empresa</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Font Awesome icons (free version)-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Flex:opsz@8..144&family=Titillium+Web:wght@600&display=swap" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <!-- Simple line icons-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.5.5/css/simple-line-icons.min.css" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link href="style.css" rel="stylesheet" />
</head>
<body>
    <div class="head">
    <div class="sidebarToggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </div>
        <img src="assets/img/favicon6.png" alt="Logo">
    </div> 
    <div class="sidebar">        
        <ul class="sidebarlinks">
            <a href="painel.php"><i id="sidebaricon" class="fa-solid fa-pen-to-square"></i> TFF e TLP</a>     
            <br>      
            <a href="autonomo.php"><i id="sidebaricon" class="fa-solid fa-pen-to-square"></i> ISS Ofício</a>   
            <br> 
        </ul>
        <ul class= "sidebarout">        
            <a href="?logout=true"><i id="sidebaricon" class="fa-solid fa-right-from-bracket"></i> Sair</a>
        </ul>
    </div> 
    <div class="container" >
        <p class="text-center-descricao" > Prescrição de:</p>
        <h1 class="text-center-titulo"> ISS Ofício </h1>
        <form action="autonomo.php" method="post" class="form-row" >
        <div class ="row" style="align-items: center;"> 
            <div class="col-md-3" style="width: 25%;"></div>
            <div class="col-md-3" style="width: 35%;">
                <label for="registration-number" class="form-label"><strong>Inscrição municipal:</strong></label>
                <input type="text" id="registration-number" name="registration-number" class="form-control" style="width: 100%; " value="<?php echo $registrationNumber; ?>">
            </div>
            <div class="col-md-3" style="width: 15%;" >
                <button type="submit" class="custom-button btn-sm" name="submit-form" style="margin-top: 3.8vh;" >Pesquisar</button>
            </div>
            <div class="col-md-3" style="width: 25%;"></div>
        </div> 
        </form>
        <br>
        <?php
        // Exibe a tabela somente se há situações "A" para este tributo
        if ($showTable) {
        ?>
           <div id="Texto" class="rowTable">
                <div class="col-md-12">
                    <h2 class="tableinfo" >Informacoes:</h2>
                    <br>
                    <p id='nome' class="tableinfo" >Nome: <?= $nome ?> </p>
                    <p id='numero-inscricao' class="tableinfo">Número da Inscrição: <?= $numeroInscricao?></p>
                </div>                
           </div> 
           <form  id="form-exercicios" action="processar_autonomo.php" method="post"  > 
           <table class="table ' . $tableClass . '">
           <tr>
           <th class="text-center" style="width: 10%;"><strong>Prescrição</strong></th>
           <th class="text-center" style="width: 45%;"><strong>Exercí­cio</strong></th>
           <th class="text-center" style="width: 45%;"><strong>Valor</strong></th>
           </tr>
           
        <?php
            // Loop através dos resultados da consulta e preencha as linhas da tabela
            while ($row = oci_fetch_assoc($stmt)) {
                $data = $row['AAEXERCICIO'];
                $valor = $row['VLLANCADODECLARADO'];
                $situacao = $row['STCONTACONTRIBUINTE'];
                $sqcontacontribuinte = $row['SQCONTACONTRIBUINTE'];
                $prescricao = ''; // Preenche com o valor correto da prescrição se necessário 
                // Verifica se a situação "A" antes de exibir na tabela
                if ($situacao === 'A') {
                    // Mosta uma checkbox na coluna "Prescrição"
                    echo "<tr>";
                    echo "<td class='text-center'><input type='checkbox' name='prescricao[$sqcontacontribuinte]' ></td>";
                    echo "<td class='text-center'>$data</td>";
                    echo "<td class='text-center'> R$ $valor</td>";
                    echo "</tr>";
                }
            }
            echo '</table>';
            echo '<div class="' . $buttonsClass . '" style = "width: 69vh;">';
            echo '<button type="button" class="custom-button" id="marcarTodos">Marcar Todos</button>';
            echo '<button type="button" class="custom-button" id="desmarcarTodos">Desmarcar Todos</button>';
            echo '<div class="d-flex justify-content-end">';
            echo '<button type="button" onclick="validaCheckbox();" class="custom-button btn-sm" id="enviar-formulario">Enviar</button>';
            echo '</div>';
            echo '</div>';
            echo '</form>';
        } elseif ($pesquisaRealizada) {
            // Exibe mensagem quando a pesquisa foi realizada, mas nÃ£o hÃ¡ situaÃ§Ãµes "A" para este tributo
            if (!empty($nome) && !empty($numeroInscricao)){
                echo "<div class='data-box' >";
                echo "<h2>Informacoes:</h2>";
                echo "<br>";
                echo "<p id='nome'>Nome: $nome</p>";
                echo "<p id='numero-inscricao'>Número da Inscrição: $numeroInscricao</p>";
                echo '</div>';
                echo "<script>alert('Não consta nada em aberto no sistema.');</script>";
            } else {
                echo "<script>alert('Inscrição não encontrada.');</script>";
            }
        }
        ?>
        <br>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        document.getElementById('marcarTodos').addEventListener('click', function () {
            var checkboxes = document.querySelectorAll('input[type="checkbox"]');
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = true;
            }
        });
        document.getElementById('desmarcarTodos').addEventListener('click', function () {
            var checkboxes = document.querySelectorAll('input[type="checkbox"]');
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = false;
            }
        });
        function validaCheckbox() {        
            var has_checkbox = 0;
            jQuery('table tbody tr td input:checkbox').each(function(e) {
                if (jQuery(this).is(':checked')) { 
                    has_checkbox = 1;                  
                    $("#form-exercicios").submit();
                    return false; // Impede o envio do formulário se nenhuma checkbox estiver marcada
                }
            });
            if(!has_checkbox)
                alert('Por favor, marque pelo menos uma prescrição antes de enviar.');
            return false;
        }
        function toggleSidebar() {
            var sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('active');
        }
    </script>
</body>
</html>