# Sistema de Login

<img width="574" alt="image" src="https://github.com/celolopes/sistema_login/assets/67067508/82f35615-1b3c-450f-95f2-98b70cb23961">

Este projeto é um simples sistema de login utilizando PHP, que permite que os usuários se autentiquem em um sistema.

# Como usar

1- Faça o clone do repositório.
2- Configure o acesso ao banco de dados no arquivo config/conexao.php.
3- Acesse a página index.php no seu servidor local para visualizar a página de login.

# Funcionalidades

* Autenticação de usuário: os usuários podem se autenticar inserindo seu email e senha. As senhas são encriptadas com SHA1 para segurança adicional.
* Verificação de email: os usuários precisam confirmar seu email antes de serem capazes de se autenticar.
* Redefinição de senha: se um usuário esquecer sua senha, ele pode redefini-la a partir de um link na página de login.
* Criação de sessão: uma vez que o usuário esteja autenticado, um token é gerado e armazenado na sessão do usuário para manter o usuário logado.

# Dependências

* PHP 7.4 ou superior.
* Servidor MySQL.

# Configuração do banco de dados

Este projeto utiliza o MySQL para armazenar os dados dos usuários. Para configurar o acesso ao banco de dados, edite o arquivo config/conexao.php com os detalhes do seu servidor MySQL.

# Contribuindo

Pull requests são bem-vindos. Para alterações importantes, por favor, abra um problema primeiro para discutir o que você gostaria de mudar.

# Licença

MIT

# Contato

Se você tiver alguma dúvida sobre este projeto, sinta-se à vontade para entrar em contato.
