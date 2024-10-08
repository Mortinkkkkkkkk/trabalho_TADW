<?php
    require_once 'testeLogin.php';
    require_once 'operacoes.php';
    require_once 'conexao.php';

    if ($_GET['origem'] == 1) {

        $nome_cliente = $_GET['nome_cliente'];
        $tipo_cliente = $_GET['tipo'];
        $id_veiculo = $_GET['idVeiculo'];
        $funcionario = $_SESSION['idFuncionario'];

        $_SESSION['dados'] = array($nome_cliente, $tipo_cliente, $id_veiculo, $funcionario);

        //futuramente adicionar uma verificação para que um cliente cadastrado não seja cadastrado de novo.
        //até lá vai ficar assim
        // Fazer com função

        // Direciona p/ o proximo formulário a ser preenchido
        if ($tipo_cliente == "p") {
            header('Location: pessoa_fisica.html');
            exit();
        } else {
            header('Location: pessoa_juridica.html');
            exit();
        }





        //Formulario pessoa física
    } elseif ($_GET['origem'] == 2) {

        $cpf = $_GET['cpf'];
        $cnh = $_GET['cnh'];
        $endereco = $_GET['endereco'];
        $data = date('Y-m-d');

        // a verificação de cadastro será realizada ensta parte
        // essa verificação será feita da seguinte forma: 
            //1º - um select buscando o cpf ou cnh no banco, para verificar se é igual ao digitado;
            //2º - caso o cpf ou cnh já esteja cadastrado, é necessário que os dados sigam para o cadstro nas tabelas
                //relacionadas ao aluguel;

        //separa os dados do array em variaveis
        list($nome, $tipo, $veiculo, $funcionario) = $_SESSION['dados'];

        //faz tudo que os dois trechos anteriores fazem.(os que estão comentados)
        $id_cliente = insereClienteVerificaID($conexao, $nome, $tipo);


        //cadastro dos dados do formulario pessoa fisica
        $sql2 = "INSERT INTO `tb_pessoa` (`cpf`, `cnh`, `tb_cliente_id_cliente`) VALUES (?, ?, ?)";
        
        $stmt2 = mysqli_prepare($conexao, $sql2);

        mysqli_stmt_bind_param($stmt2, "ssi", $cpf, $cnh, $id_cliente);

        mysqli_stmt_execute($stmt2);

        mysqli_stmt_close($stmt2);


        //cadastro na tabela de endereços
        $sql3 = "INSERT INTO `tb_enderecos` (`endereco`, `tb_cliente_id_cliente`) VALUES (?, ?)";

        $stmt3 = mysqli_prepare($conexao, $sql3);

        mysqli_stmt_bind_param($stmt3, "si", $endereco, $id_cliente);

        mysqli_stmt_execute($stmt3);

        mysqli_stmt_close($stmt3);

        //busca no cadastro aluguel
        $id_aluguel = insereAluguelVerificaID($conexao, $data, $funcionario, $id_cliente);

        //cadastro na veiculo_aluguel

        $sql_final = "INSERT INTO `tb_veiculo_aluguel` (`tb_veiculo_id_veiculo`, `tb_aluguel_id_aluguel`) 
                       VALUES (?, ?)";

        $stmt_final = mysqli_prepare($conexao, $sql_final);

        mysqli_stmt_bind_param($stmt_final, "ss", $veiculo, $id_aluguel);

        mysqli_stmt_execute($stmt_final);

        mysqli_stmt_close($stmt_final);
        
        //Update no estado do carro
        $update = "UPDATE `tb_veiculo` SET `estado_veiculo` = '2' WHERE `id_veiculo` = ? ";
        
        $stmtUpdate = mysqli_prepare($conexao, $update);
        
        mysqli_stmt_bind_param($stmtUpdate, "i", $veiculo);
        
        if (mysqli_stmt_execute($stmtUpdate)) {

            mysqli_stmt_close($stmtUpdate);
            
            
            //Desfaz o array no session
            if (isset($_SESSION['dados'])){unset($_SESSION['dados']);}
            
            //redireciona p/ prox. página
            header('Location: exibir_veiculos.php');
            exit();
        } else {
            header('Location: form_aluguel');
            exit();
        }

        //Formulário de empresa
    } elseif ($_GET['origem'] == 3) {

        $cnpj_cliente = $_GET['cnpj'];
        $responsavel = $_GET['func_resp'];
        $endereco = $_GET['endereco'];
        $data = date('Y-m-d');


        list($nome, $tipo, $veiculo, $funcionario) = $_SESSION['dados'];
    
        //insere os dados na tabela cliente e retorna o id do cadastro;
        $id_cliente = insereClienteVerificaID($conexao, $nome, $tipo);

        //insere os dado na tabela empresa;
        $sql2 = "INSERT INTO `tb_empresa` (`nome_empresa`, `cnpj`, `func_responsavel`, `tb_cliente_id_cliente`) VALUES
                    (?, ?, ?, ?)";

        $stmt2 = mysqli_prepare($conexao, $sql2);

        mysqli_stmt_bind_param($stmt2, "sssi", $nome, $cnpj_cliente, $responsavel, $id_cliente);

        mysqli_stmt_execute($stmt2);

        mysqli_stmt_close($stmt2);

        //cadastro na tabela de endereços
        $sql3 = "INSERT INTO `tb_enderecos` (`endereco`, `tb_cliente_id_cliente`) VALUES (?, ?)";
        
        $stmt3 = mysqli_prepare($conexao, $sql3);
        
        mysqli_stmt_bind_param($stmt3, "si", $endereco, $id_cliente);

        mysqli_stmt_execute($stmt3);

        mysqli_stmt_close($stmt3);

       
        //cadastro na tabela aluguel, e retorno do id;
        $id_aluguel = insereAluguelVerificaID($conexao, $data, $funcionario, $id_cliente);

            
        //cadastro na veiculo_aluguel
            
        $sql_final = "INSERT INTO `tb_veiculo_aluguel` (`tb_veiculo_id_veiculo`, `tb_aluguel_id_aluguel`) 
                    VALUES (?, ?)";

        $stmtFinal = mysqli_prepare($conexao, $sql_final);
        
        mysqli_stmt_bind_param($stmtFinal, "ii", $veiculo, $id_aluguel);

        mysqli_stmt_execute($stmtFinal);

        mysqli_stmt_close($stmtFinal);

         //Update no estado do carro
         $update = "UPDATE `tb_veiculo` SET `estado_veiculo` = 'a' WHERE `id_veiculo` = ? ";
        
         $stmtUpdate = mysqli_prepare($conexao, $update);
         
         mysqli_stmt_bind_param($stmtUpdate, "i", $veiculo);
         
        if (mysqli_stmt_execute($stmtUpdate)) {

            mysqli_stmt_close($stmtUpdate);

            //Desfaz o array no session
            if (isset($_SESSION['dados'])) {
                unset($_SESSION['dados']);
                }

            header('Location: exibir_veiculos.php');
            exit();
        } else {
            header('Location: form_aluguel');
            exit();
        }
    }
    ?>