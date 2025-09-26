# Sistema de Controle de Devoluções

## Descrição
Aplicação PHP MVC para gerenciar devoluções de produtos. Inclui dashboard, CRUD de devoluções e usuários, com controle de permissões de administrador.

## Funcionalidades
- Login obrigatório
- Controle de sessão
- Dashboard de devoluções com filtros e gráficos
- CRUD completo de devoluções
- Gestão de usuários (admin)
- Visualização de conta pelo próprio usuário
- Menu estático no header
- Footer com versão e copyright
- Segurança: senhas hashed, verificação de sessão

## Estrutura MVC
- /config: Configuração do banco de dados
- /controllers: Lógica de negócio
- /models: Representação das entidades
- /view: Telas e componentes
- /public: CSS, JS, imagens
- index.php: Front Controller

## Regras de acesso
- Admin: acesso total
- Usuário simples: acesso restrito (não pode deletar, não vê CRUD de usuários)


- Implementar CRUD completo de devoluções
- Filtros dinâmicos e gráficos no dashboard
- Página “Ver Conta” e edição de dados pessoais
- Logs de alterações para auditoria

Estrutura de PASTAS:

/consultadevolucao
│
├── /config
│   └── database.php        # Conexão com MySQL
│
├── /controllers
│   ├── AuthController.php  # Login / Logout / Sessão
│   ├── DashboardController.php
│   ├── UserController.php  # CRUD de usuários
│   └── DevolucaoController.php  # CRUD de devoluções
│
├── /models
│   ├── User.php            # Usuário (com boolean admin)
│   └── Devolucao.php       # Devoluções
│
├── /view
│   ├── /auth
│   │   └── login.php
│   │
│   ├── /dashboard
│   │   └── home.php
│   │
│   ├── /users
│   │   ├── index.php
│   │   ├── edit.php
│   │   └── details.php
│   │
│   ├── /devolucoes
│   │   ├── index.php
│   │   ├── create.php
│   │   ├── edit.php
│   │   └── delete.php
│   │
│   └── /components
│       ├── header.php
│       └── footer.php
│
├── /public
│   ├── style.css
│   ├── script.js
│   └── logo.png
│
├── index.php               # Front Controller, redireciona páginas
└── README.md