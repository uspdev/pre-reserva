# Pré-reserva
Aplicação para gerencimento de pré-reservas de salas informatizadas da STI.

## Funcionalidades

* Receber pré-reservas para as salas
* Visualizar as pré-reservas
* Gerenciar as pré-reservas (aceitar e designar uma sala ou não aceitar)
* Editar e excluir pré-reservas

## Requisitos

* PHP ^8.2
* Conexão com banco de dados
* Token oauth
* Acesso ao replicado

### Em produção

Para receber as últimas atualizações do sistema rode:

```sh
git pull
composer install --no-dev
php artisan migrate
```

## Instalação

```sh
git clone git@github.com:uspdev/pre-reserva
composer install
cp .env.example .env
php artisan key:generate
```

Configure o .env conforme a necessidade

### Apache ou nginx

Deve apontar para a <pasta do projeto>/public, assim como qualquer projeto laravel.

No Apache é possivel utilizar a extensão MPM-ITK (http://mpm-itk.sesse.net/) que permite rodar seu Servidor Virtual com usuário próprio. Isso facilita rodar o sistema como um usuário comum e não precisa ajustar as permissões da pasta storage/.

```bash
sudo apt install libapache2-mpm-itk
sudo a2enmod mpm_itk
sudo service apache2 restart
```

Dentro do seu virtualhost coloque

```apache
<IfModule mpm_itk_module>
AssignUserId nome_do_usuario nome_do_grupo
</IfModule>
```

### Senha única

Cadastre uma nova URL no configurador de senha única utilizando o caminho https://seu_app/callback. Guarde o callback_id para colocar no arquivo .env.

### Banco de dados

* DEV

    `php artisan migrate:fresh --seed`

* Produção

    `php artisan migrate`
