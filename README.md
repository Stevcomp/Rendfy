# Rendify

Sistema web em PHP para gerenciamento de carteiras de investimentos e calculo de rendimentos.

## Organizacao

- `Index.php` e demais paginas PHP da raiz: telas do sistema (front-end).
- `frontend/css/app.css`: estilos consolidados das sete telas, isolados pelas classes `page-style1` a `page-style7`.
- `backend/`: processadores de formularios, autenticacao e operacoes de conta.
- `backend/database/`: conexao PDO, funcoes CRUD e script SQL.
- `uploads/`: imagens de perfil enviadas pelos usuarios.

## Executar localmente

1. Coloque o projeto no diretorio publico do Apache, por exemplo `htdocs/Rendify`.
2. Importe `backend/database/Rendify.sql` no MySQL.
3. Ajuste as credenciais em `backend/database/conexao.php`.
4. Inicie Apache e MySQL e acesse `http://localhost/Rendify`.
