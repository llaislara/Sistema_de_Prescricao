<?php
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica se a variável 'prescricao' existe e é um array
    if (isset($_POST['prescricao']) && is_array($_POST['prescricao'])) {
        try {
            $todosPrescritos = true;

            // Loop através dos valores marcados como 'on' em 'prescricao' e executa o update
            foreach (array_keys($_POST['prescricao']) as $sqcontacontribuinte) {
                $sql = "UPDATE CONTACONTRIBUINTE SET STCONTACONTRIBUINTE = 'P' WHERE SQCONTACONTRIBUINTE = :sqcontacontribuinte";
                $stmt = oci_parse($conn, $sql);

                // Limpa e vincula o valor para a consulta
                oci_bind_by_name($stmt, ':sqcontacontribuinte', $sqcontacontribuinte);

                // Execute a consulta
                $resultado = oci_execute($stmt);

                // Verifica se o resultado foi bem-sucedido
                if (!$resultado) {
                    $todosPrescritos = false;
                    // indica que nem todos os dados foram prescritos com sucesso
                }
            }

            // Exibe uma mensagem com base no valor de $todosPrescritos
            if ($todosPrescritos) {
                echo "<script>alert('Todos os dados foram prescritos com sucesso.');</script>";
            } else {
                echo "<script>alert('Alguns dados não foram prescritos.');</script>";
            }

            echo "<script>window.location = 'painel.php';</script>";
        } catch (Exception $e) {
            // Em caso de erro, exibe uma mensagem de erro
            echo 'Erro: ' . $e->getMessage();
        }
    } else {
        // Se 'prescricao' não é um array válido, redireciona de volta para a página de painel
        header('Location: painel.php');
        exit();
    }
} else {
    // Se o formulário não foi enviado corretamente, redireciona de volta para a página de painel
    header('Location: painel.php');
    exit();
}
?>
