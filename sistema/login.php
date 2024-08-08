<?php
    require_once 'operacoes.php';
    require_once 'conexao.php';
    session_start();

    $cpf = $_POST['cpf'];
    $password = $_POST['senha'];

    $sql = "SELECT nome_funcionario, cpf_funcionario FROM tb_funcionario WHERE cpf_funcionario = ? AND senha_funcionario = ?";

    $stmt = mysqli_prepare($conexao, $sql);
    
    mysqli_stmt_bind_param($stmt, "ss", $cpf, $password);

    mysqli_stmt_execute($stmt);

    mysqli_stmt_bind_result($stmt, $nome_funcionario, $cpf_funcionario);

    mysqli_stmt_store_result($stmt);

    $lista = array();

    if (mysqli_stmt_num_rows($stmt) > 0) {
        while (mysqli_stmt_fetch($stmt)) {
           $lista = [$nome_funcionario, $cpf_funcionario];
        }

        foreach($lista as $dados){
            $_SESSION['nomeFuncionario'] = $dados[0];
            $_SESSION['cpf_funcionario'] = $dados[1];
        }
        $_SESSION['lista'] = $lista;
        mysqli_stmt_close($stmt);

        $_SESSION['logado'] = true;
 
        header('Location: home.php');
        exit();
    }else {

        header('Location: index.html');
        exit();
    }

?>