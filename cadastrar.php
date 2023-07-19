<?php
require('config/conexao.php');

//REQUERIMENTO DE PHPMAILER
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'config/PHPMailer/src/Exception.php';
require 'config/PHPMailer/src/PHPMailer.php';
require 'config/PHPMailer/src/SMTP.php';

//VERIFICAR SE A POSTAGEM EXISTE DE ACORDO COM OS CAMPOS
if (isset($_POST['nome_completo']) && isset($_POST['email']) && isset($_POST['senha']) && isset($_POST['repete_senha'])) {
	//VERIFICAR SE TODOS OS CAMPOS FORAM PREENCHIDOS
	if (empty($_POST['nome_completo']) or empty($_POST['email']) or empty($_POST['senha']) or empty($_POST['repete_senha']) or empty($_POST['termos'])) {
		$erro_geral = "Todos os campos são obrigatórios";
	} else {
		//RECEBER VALORES VINDOS DO POST E LIMPAR
		$nome = limparPost($_POST['nome_completo']);
		$email = limparPost($_POST['email']);
		$senha = limparPost($_POST['senha']);
		$senha_cript = sha1($senha);
		$repete_senha = limparPost($_POST['repete_senha']);
		$checkbox = limparPost($_POST['termos']);

		//VERIFICAR SE NOME É APENAS LETRAS E ESPAÇO
		if (!preg_match("/^[a-zA-Z-' éíáàèìòùÁÉÍÓÚâêîôûÂÊÎÔÛãõÃÕçÇ]*$/", $nome)) {
			$erro_nome = "Apenas letras e espaços em branco são permitidos!";
		}
		//VERIFICAR SE EMAIL É VÁLIDO
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$erro_email = "Formato de email inválido!";
		}

		//VERIFICAR SE SENHA TEM MAIS DE 6 DÍGITOS
		if (strlen($senha) < 6) {
			$erro_senha = "Senha deve ter 6 caracteres!";
		}

		//VERIFICAR SE REPETE SENHA É IGUAL A SENHA
		if ($senha !== $repete_senha) {
			$erro_repete_senha = "Senha e repetição de senha devem ser iguais!";
		}

		//VERIFICAR SE CHECKBOX FOI MARCADO
		if ($checkbox !== 'ok') {
			$erro_checkbox = "Desativado";
		}

		if (!isset($erro_geral) && !isset($erro_nome) && !isset($erro_email) && !isset($erro_senha) && !isset($erro_repete_senha) && !isset($erro_checkbox)) {
			//VERIFICAR SE ESTE EMAIL JÁ ESTÁ CADASTRADO NO BANCO
			$sql = $pdo->prepare("SELECT * FROM usuarios WHERE email=? LIMIT 1");
			$sql->execute(array($email));
			$usuario = $sql->fetch();
			//SE NÃO EXISTE O USUÁRIO - ADICIONAR NO BANCO
			if (!$usuario) {
				$recupera_senha = "";
				$token = "";
				$cod_confirmacao = uniqid();
				$status = "novo";
				$data_cadastro = date('d/m/Y');
				$sql = $pdo->prepare("INSERT INTO usuarios VALUES (null,?,?,?,?,?,?,?,?)");
				if ($sql->execute(array($nome, $email, $senha_cript, $recupera_senha, $token, $status, $data_cadastro, $cod_confirmacao))) {
					//SE O MODO FOR LOCAL
					if ($modo == "local") {
						header('location: index.php?result=ok');
					}
					//SE O MODO FOR PRODUÇÃO
					if ($modo == "producao") {

						//ENVIAR EMAIL PARA O USUÁRIO
						$mail = new PHPMailer(true);

						try {
							//Recipients
							$mail->setFrom('sistema@emailsistema.com', 'Sistema de Login'); //QUEM ESTÁ MANDANDO O EMAIL
							$mail->addAddress($email, $nome);

							//Content
							$mail->isHTML(true); //CORPO DO EMAIL COMO HTML
							$mail->Subject = 'Confirme seu cadastro'; //TITULO DO EMAIL
							$mail->Body = '<h1>Por favor confirme seu e-mail abaixo:</h1><br><br><a style="background:green; color:white; text-decoration:none; padding:20px;" href="https://seusistema.com.br/confirmacao.php?cod_confirm=' . $cod_confirmacao . '">Confirmar E-mail</a><br><p>Equipe do Login</p>';

							$mail->send();
							header('location: obrigado.php');

						} catch (Exception $e) {
							echo "Houve um problema ao enviar e-mail de confirmação: {$mail->ErrorInfo}";
						}
					}

				}
			} else {
				//JÁ EXISTE USUÁRIO - APRESENTAR ERRO
				$erro_geral = "Usuário já cadastrado";
			}
		}
	}


}

?>
<!DOCTYPE html>
<html lang="pt-br">

	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="css/estilo.css" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
		<title>Cadastrar</title>
	</head>

	<body>
		<form method="post" action="">
			<h1>Cadastrar</h1>
			<?php if (isset($erro_geral)) { ?>
				<div class="erro-geral animate__animated animate__rubberBand">
					<?php echo $erro_geral; ?>
				</div>
			<?php } ?>
			<div class="input-group">
				<img class="input-icon" src="img/card.png" alt="" />
				<input <?php if (isset($erro_geral) or isset($erro_nome)) {
					echo 'class="erro-input"';
				} ?> type="text" name="nome_completo" id="" placeholder="Nome Completo" <?php if (isset($_POST['nome_completo'])) {
					  echo "value='" . $_POST['nome_completo'] . "'";
				  } ?> required /> <?php if (isset($erro_nome)) { ?> <div class="erro">
					<?php echo $erro_nome; ?>
				</div>
				<?php } ?>
			</div>
			<div class="input-group">
				<img class="input-icon" src="img/user.png" alt="" />
				<input <?php if (isset($erro_geral) or isset($erro_email)) {
					echo 'class="erro-input"';
				} ?> type="email" name="email" id="" placeholder="Seu melhor email" required <?php if (isset($_POST['email'])) {
					  echo "value='" . $_POST['email'] . "'";
				  } ?> /> <?php if (isset($erro_email)) { ?> <div class="erro">
					<?php echo $erro_email; ?>
				</div>
				<?php } ?>
			</div>
			<div class="input-group">
				<img class="input-icon" src="img/lock.png" alt="" />
				<input <?php if (isset($erro_geral) or isset($erro_senha)) {
					echo 'class="erro-input"';
				} ?> type="password" name="senha" id="" placeholder="Senha de pelo menos 6 digitos" required <?php if (isset($_POST['senha'])) {
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
				} ?> type="password" name="repete_senha" id="" placeholder="Repita a senha criada" required <?php if (isset($_POST['repete_senha'])) {
					  echo "value='" . $_POST['repete_senha'] . "'";
				  } ?> /> <?php if (isset($erro_repete_senha)) { ?> <div class="erro">
					<?php echo $erro_repete_senha; ?>
				</div>
				<?php } ?>
			</div>
			<div <?php if (isset($erro_geral) or isset($erro_checkbox)) {
				echo 'class="input-group erro-input"';
			} else {
				'class="input-group"';
			} ?>>
				<input type="checkbox" name="termos" id="termos" value="ok" required />
				<label for="termos">Ao se cadastrar você concorda com a nossa <a class="link" href="#">Política de Privacidade</a> e os <a class="link" href="#">Termos de Uso</a></label>
			</div>
			<button class="btn-blue" type="submit">Cadastrar</button>
			<a href="index.php">Já tenho uma conta</a>
		</form>
	</body>

</html>