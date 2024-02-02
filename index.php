<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Prescrição Empresa</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon2.jpg" />
    <!-- Font Awesome icons (free version)-->
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <!-- Simple line icons-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.5.5/css/simple-line-icons.min.css" rel="stylesheet" />
    <!-- Google fonts-->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body id="page-top" style="background-color: #27343D;">
    <header class="d-flex flex-column flex-md-row justify-content-md-center align-items-center" style="background-color: #27343D; margin-top: 20vh;">
        <div class="container px-40 px-lg-50 text-center">
            <div class="row">
                <div class="col-lg-5">
                    <div class="p-3">
                        <img src="assets/img/logofsa.png" alt="Logo FSA" class="img-fluid">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="p-5" style="margin-top:15%">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-6 text-white" style="font-size: 40px;">Bem vindo(a)!</h1>
                        </div>
                        <form class="form-login" role="form" method="POST">
                            <div class="form-group">
                                <input id="usuario" name="usuario" type="text" class="form-control" placeholder="Usuário" required autofocus> <br>
                                <input id="senha" name="senha" type="password" class="form-control" placeholder="Senha" required> <br>
                                <?php  if (isset($mensagem)) { ?>
                                    <div class="error-message"><?php echo $mensagem; ?></div> 
                                <?php } ?>
                            </div>
                            <div class="form-group">
                                <button type="submit" name="login" class="btn btn-primary btn-user btn-block">Login</button>
                            </div>
                        </form>
                        <?php                
                        ini_set('display_errors', 1);
                        ini_set('display_startup_errors', 1);
                        error_reporting(E_ALL);

                        session_start();
                        require_once 'conexao.php';
                        // Verifica se o formulário foi enviado
                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                            
                            // Verifica se os campos foram preenchidos
                            if (empty($_POST['usuario']) || empty($_POST['senha'])) {
                                $mensagem = 'Preencha o campo de usuário e senha.';
                               
                            } else {
                                // Obtem as informações do formulário
                                $usuario = strtoupper($_POST['usuario']);
                                $senha = md5(strtoupper($_POST['senha']));


                                // Consulta o banco de dados para verificar as informações de login
                                $sql = "SELECT CDUSUARIO, PASSWORD FROM PMFSUSUARIO WHERE CDUSUARIO = :usuario AND PASSWORD = :senha AND PRESCRICAOEMP = 1"; 
                                $stmt = oci_parse($conn, $sql);

                                oci_bind_by_name($stmt, ':usuario', $usuario);
                                oci_bind_by_name($stmt, ':senha', $senha);

                                oci_execute($stmt);

                                $row = oci_fetch_assoc($stmt);

                                // Verifica se o usuário foi encontrado
                                if ($row) {
                                    // Definir os dados de login na sessão
                                    
                                    $_SESSION['usuario'] = $row['CDUSUARIO'];
                                    $_SESSION['senha'] = $row['PASSWORD'];

                                    // Redireciona para a página de painel
                                    header('Location: painel.php');
                              
                                } else {
                                    ?> <script>alert('Usuário e/ou senha incorretos.');</script><?php
                                }                           
                            }
                        }
                        ?>                                                  
                    </div>
                </div>
            </div>
        </div>
    </header>        

    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script> 
    <script src="js/scripts.js"></script>
</body>
</html>


