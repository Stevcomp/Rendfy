# Rendify 💹

O **Rendify** é um sistema web responsivo e moderno desenvolvido para o gerenciamento de carteiras de investimentos e cálculo automatizado de rendimentos de ativos financeiros (como Ações e Fundos Imobiliários). O sistema conta com uma interface otimizada em *Dark Mode*, controle de autenticação de usuários, relatórios financeiros de dividendos e atualização de fotos de perfil.

## 🚀 Funcionalidades

- **Autenticação Segura:** Sistema de login e criação de contas com senhas criptografadas em hash.
- **Gestão de Ativos (CRUD):** Cadastro, leitura, edição e exclusão de papéis financeiros (Ações, FIIs, etc.).
- **Painel Financeiro Geral:** Monitoramento do valor total investido, soma absoluta de proventos recebidos no mês e cálculo automático da rentabilidade da carteira.
- **Upload de Perfil:** Upload dinâmico e seguro de imagens de avatar para os clientes.

## 📁 Estrutura do Projeto

```bash
├── Index.php                  # Tela de Login e ponto de entrada do sistema
├── Header.php                 # Cabeçalho global unificado do sistema
├── CriarCadastro_cliente.php  # Formulário de criação de novas contas
├── Perfil_cliente.php         # Área do investidor com avatar e informações pessoais
├── Editar_cliente.php         # Formulário de alteração de dados do perfil
├── Dashboard.php              # Painel principal de controle e listagem de ativos
├── Detalhes_financeiros.php   # Relatórios estatísticos e composição da carteira
├── DY_calculo.php             # Sistema do calculo do DY mensal para cada ativo 
├── crud.php                   # Funções centrais de persistência de dados (Banco de Dados)
├── RecebeCadastro_cliente.php # Processamento de login
├── RecebeNovoCadastro_cliente.php # Lógica de inserção de novos perfis
├── RecebeEdicao_cliente.php   # Processamento de atualizações do usuário
├── RecebeCadastro_ativo.php   # Lógica de inserção de novos ativos
├── RecebeEdicao_ativo.php     # Lógica de modificação de ativos existentes
├── RecebeExclusao_ativo.php   # Lógica de remoção de ativos da carteira
├── RecebeFoto_cliente.php     # Motor de upload e tratamento de imagens de perfil
├── Excluir_cliente.php        # Lógica de exclusão total da conta
├── Logout_cliente.php         # Encerramento seguro da sessão ativa
├── Rendify.sql                # Banco de Dados completo do sistema da Rendify
├── style1.css                 # Identidade visual da tela de login
├── style4.css                 # Identidade visual do perfil do usuário
├── style5.css                 # Identidade visual do formulário de edição
├── style7.css                 # Identidade visual dos relatórios financeiros
└── uploads/                   # Diretório de armazenamento das fotos de perfil

⚙️ Como Executar o Projeto
Certifique-se de ter um ambiente de servidor local instalado (ex: XAMPP, WAMP ou Laragon) com suporte a PHP e MySQL.

Clone este repositório para a pasta de arquivos públicos do seu servidor (htdocs ou www):

Bash
git clone [https://github.com/seu-usuario/rendify-calculadora.git](https://github.com/seu-usuario/rendify-calculadora.git)
Importe a estrutura do banco de dados MySQL contida no arquivo do projeto utilizando o phpMyAdmin ou ferramenta similar.

Inicie o Apache e o MySQL no painel de controle do seu servidor local.

Abra o seu navegador de preferência e digite:

Plaintext
http://localhost/Rendify
🎨 Interface Visual
O projeto utiliza um tema Dark Mode moderno, focado na sofisticação e redução da fadiga visual para o investidor durante o acompanhamento diário do mercado financeiro, contando com marcadores visuais dinâmicos para indicação de dividend yield e performance mensal.

👨‍💻 Desenvolvedor
Desenvolvido por Felipe Stevanato Ribeiro 📞 Entre em contato: +55 11 97756-9063
