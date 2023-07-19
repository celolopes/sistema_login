<?php
require('config/conexao.php');

//REQUERIMENTO DE PHPMAILER
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'config/PHPMailer/src/Exception.php';
require 'config/PHPMailer/src/PHPMailer.php';
require 'config/PHPMailer/src/SMTP.php';

if (isset($_GET['cod']) && !empty($_GET['cod'])) {
	//LIMPAR O GET
	$cod = limparPost($_GET['cod']);

	//VERIFICAR SE A POSTAGEM EXISTE DE ACORDO COM OS CAMPOS
	if (isset($_POST['senha']) && isset($_POST['repete_senha'])) {
		//VERIFICAR SE TODOS OS CAMPOS FORAM PREENCHIDOS
		if (empty($_POST['senha']) or empty($_POST['repete_senha'])) {
			$erro_geral = "Todos os campos são obrigatórios";
		} else {
			//RECEBER VALORES VINDOS DO POST E LIMPAR
			$senha = limparPost($_POST['senha']);
			$senha_cript = sha1($senha);
			$repete_senha = limparPost($_POST['repete_senha']);

			//VERIFICAR SE SENHA TEM MAIS DE 6 DÍGITOS
			if (strlen($senha) < 6) {
				$erro_senha = "Senha deve ter 6 caracteres!";
			}

			//VERIFICAR SE REPETE SENHA É IGUAL A SENHA
			if ($senha !== $repete_senha) {
				$erro_repete_senha = "Senha e repetição de senha devem ser iguais!";
			}

			if (!isset($erro_geral) && !isset($erro_senha) && !isset($erro_repete_senha)) {
				//VERIFICAR SE ESTE RECUPERAÇÃO DE SENHA EXISTE NO BANCO
				$sql = $pdo->prepare("SELECT * FROM usuarios WHERE recupera_senha=? LIMIT 1");
				$sql->execute(array($cod));
				$usuario = $sql->fetch();
				//SE NÃO EXISTE O USUÁRIO - ADICIONAR NO BANCO
				if (!$usuario) {
					echo "Recuperação de Senha Inválida!";
				} else {
					//JÁ EXISTE USUÁRIO - CÓDIGO DE RECUPERAÇÃO
					//ATUALIZAR O TOKEN DESDE USUÁRIO NO BANCO
					$sql = $pdo->prepare("UPDATE usuarios SET senha=? WHERE recupera_senha=?");
					if ($sql->execute(array($senha_cript, $cod))) {
						//REDIRECIONA PARA O LOGIN
						header('location: index.php?result=alterado');
					}
				}
			}
		}
	}
} else {
	header('location: index.php');
}



?>
<!DOCTYPE html>
<html lang="pt-br">

	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="css/estilo.css" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
		<title>Trocar a Senha</title>
	</head>

	<body>
		<form method="post" action="">
			<h1>Trocar a Senha</h1>
			<?php if (isset($erro_geral)) { ?>
				<div class="erro-geral animate__animated animate__rubberBand">
					<?php echo $erro_geral; ?>
				</div>
			<?php } ?>
			<div class="input-group">
				<img class="input-icon" src="img/lock.png" alt="" />
				<input <?php if (isset($erro_geral) or isset($erro_senha)) {
					echo 'class="erro-input"';
				} ?> type="password" name="senha" id="" placeholder="Nova senha de 6 digitos" required <?php if (isset($_POST['senha'])) {
					  echo "value='" . $_POST['senha'] . "'";
				  } ?> /> <?php if (isset($erro_senha)) { ?> <div class="erro">
					<?php echo $erro_senha; ?>
				</div>
				<?php } ?>
			</div>
			<div class="input-group">
				<img class="input-icon" src="img/unlock.png" alt="" />
				<input <?php if (isset($erro_geral) or isset($erro_repete_senha)) {
					echo 'class="erro-input"';
				} ?> type="password" name="repete_senha" id="" placeholder="Repita a senha " required <?php if (isset($_POST['repete_senha'])) {
					  echo "value='" . $_POST['repete_senha'] . "'";
				  } ?> /> <?php if (isset($erro_repete_senha)) { ?> <div class="erro">
					<?php echo $erro_repete_senha; ?>
				</div>
				<?php } ?>
			</div>
			<button class="btn-blue" type="submit">Alterar a Senha</button>
		</form>
	</body>

</html>