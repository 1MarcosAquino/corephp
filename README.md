# 🚀 CORE PHP

Um mini framework PHP criado do zero com foco em aprendizado, simplicidade e estrutura profissional.
Inspirado em conceitos de frameworks modernos como Laravel.

---

## 📌 Sobre o projeto

Este projeto implementa um ciclo completo de requisição HTTP:

```
Request → Router → Controller → Response → Client
```

Inclui:

- Sistema de rotas com parâmetros dinâmicos
- Abstração de Request e Response
- Camada de banco de dados com PDO
- Estrutura modular e extensível

---

## 🧠 Objetivo

Este projeto foi desenvolvido para:

- Entender como frameworks funcionam internamente
- Criar uma base reutilizável para APIs REST
- Demonstrar conhecimento técnico em PHP puro
- Servir como projeto de portfólio

---

## 🏗️ Estrutura do Projeto

```
/app
  /Core
    Request.php
    Response.php
    Router.php
    Database.php

/public
  index.php

.env
```

---

## ⚙️ Como funciona

### 1. Entrada da aplicação

```php
$router->run();
```

O Router é responsável por interpretar a URL e decidir qual código será executado.

---

### 2. Definição de rotas

```php
$router->get('/users', [UserController::class, 'index']);

$router->get('/users/{id}', function ($req, $res, $id) {
    return ['id' => $id];
});
```

---

### 3. Request

A classe `Request` encapsula:

- Query params (`$_GET`)
- Body (`$_POST` ou JSON)
- Headers
- Método HTTP

```php
$request->input('name');
$request->all();
$request->header('authorization');
```

---

### 4. Response

A classe `Response` facilita o retorno de dados:

```php
return ['message' => 'ok']; // JSON automático

return $response->json(['data' => []]);

return $response->status(204);
```

---

### 5. Database

Camada simples utilizando PDO:

```php
Database::insert('users', [
    'name' => 'John',
    'email' => 'john@email.com'
]);

Database::select('users', ['id' => 1]);
```

---

## 🔐 Segurança

- Uso de prepared statements (PDO)
- Proteção contra SQL Injection em valores
- Validação de identificadores (tabela/coluna)
- Bloqueio de UPDATE/DELETE sem WHERE

---

## 🚀 Como usar

### 1. Clonar o projeto

```bash
git clone https://github.com/seu-usuario/seu-repo.git
```

---

### 2. Configurar ambiente

Crie um arquivo `.env`:

```
DB_HOST=localhost
DB_NAME=database
DB_USER=root
DB_PASSWORD=secret
```

---

### 3. Rodar servidor

```bash
php -S localhost:8000 -t public
```

---

## 📦 Exemplo de Controller

```php
class UserController
{
    public function index($req, $res)
    {
        return Database::select('users');
    }

    public function show($req, $res, $id)
    {
        return Database::select('users', ['id' => $id]);
    }
}
```

---

## 📈 Próximos passos

- [ ] Response (Revisar o codigo da classe Response)
- [ ] Middleware (Auth, CORS, etc)
- [ ] Container / Dependency Injection
- [ ] ORM simples (Model layer)
- [ ] Validação de dados
- [ ] Sistema de autenticação (JWT)

---

## 🎯 Status

🟢 Em desenvolvimento ativo
🧪 Pronto para uso em projetos simples e APIs

---

## 👨‍💻 Autor

Desenvolvido por **Marcos Aquino**

---

## 📄 Licença

MIT License
