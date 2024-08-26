
# Aplicação de Gerenciamento de vendas e estoque.

Esta aplicação foi desenvolvida para gerenciar produtos, categorias e vendas. Ela permite realizar operações de cadastro, consulta, atualização e exclusão (CRUD) para cada uma dessas entidades. O projeto foi desenvolvido utilizando Laravel e estilizado com Bootstrap e Livewire.

## Tecnologias Utilizadas

- PHP 8.3.6
- Laravel 8.83.27
- MySQL
- Bootstrap 4.x
- Composer version 2.7.4
- Livewire v2.12.8

## Instalando e configurando

1. **Clonar o repositório**

   ```sh
   git clone https://github.com/Eduardo-Co/vendas-app
2. **Criar e alterar a .env**
   ```sh
   cp .env.example .env
3. Instalar as dependências
   ```sh
   composer install
4. Rodando as migrations
   ```sh
   php artisan migrate
5. Gerando a APP_KEY
   ```sh
   php artisan key:generate
6. (Opicional) Alimente o banco de dados
    ```sh
    php artisan db:seed

Nota: Se os seeders forem executados, os seguintes logins serão criados:

Administrador: admin@example.com
Usuário: user@example.com
Senha padrão para ambos: password

Os Logins são feitos no mesmo formulário de login, caso o usuário seja adminsitrador será redirecionado para a parte de Admin. 

Caso opte por não rodar os seeders, será necessário criar um usuário através da página de registro. Para acessar a parte administrativa, será preciso 
alterar manualmente a permissão desse usuário para "administrator" na coluna profile da tabela correspondente no banco de dados.

7. Rode a aplicação
   ```sh
   php artisan serve
   
   
