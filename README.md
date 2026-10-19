# Rendify

Sistema web para gerenciar uma carteira de investimentos, cadastrar ativos e acompanhar rendimentos. Desenvolvido com PHP, MySQL, HTML e CSS.

## Funcionalidades

- Cadastro e autenticação de clientes.
- Cadastro, edição, listagem e remoção de ativos.
- Painel da carteira e resumo financeiro.
- Perfil do cliente e envio de foto.

## Estrutura do projeto

```text
Rendify/
├── app/
│   ├── config/conexao.php       # Conexão PHP com o MySQL
│   ├── controllers/             # Processamento de formulários e ações
│   ├── models/crud.php          # Operações de leitura e gravação
│   └── views/DY_calculo.php     # Trecho PHP reutilizado pelas páginas
├── database/schema.sql          # Referência ilustrativa para o repositório
├── public/
│   ├── assets/css/style.css     # Estilos consolidados
│   ├── pages/                   # Telas do sistema
│   ├── partials/Header.php      # Cabeçalho compartilhado
│   └── uploads/                 # Fotos de perfil
└── index.php                    # Entrada e redirecionamento para o login
```

Os redirecionamentos são feitos diretamente nas páginas e nos controladores usando `header("Location: ...")`. Não há um arquivo `bootstrap.php`.

## Requisitos

- PHP com PDO MySQL habilitado.
- MySQL ou MariaDB.
- Apache, por exemplo o incluído no XAMPP.

## Executar com XAMPP

1. Coloque a pasta `Rendify` em `htdocs`.
2. Inicie o Apache e o MySQL.
3. No phpMyAdmin, selecione o banco usado pelo sistema, atualmente `rendify_calc_invest`.
4. Confira host, porta, nome do banco, usuário e senha em `app/config/conexao.php`.
5. Acesse `http://localhost/Rendify/`.

A aplicação PHP conecta diretamente ao MySQL usando PDO. O phpMyAdmin é a interface para administrar esse banco. O arquivo `database/schema.sql` serve apenas como referência no repositório e não é carregado pela aplicação.

## Organização visual

As páginas usam a folha única `public/assets/css/style.css`. As regras de cada tela são delimitadas pela classe correspondente no elemento `body`. As fotos enviadas ficam em `public/uploads`.
