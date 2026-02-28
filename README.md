# 📦 LogiBox – Backend (PHP + Apache + MySQL + Docker)

Este repositório contém o backend do projeto **LogiBox**, uma aplicação de gestão de estoque desenvolvida com **PHP**, **MySQL** e **Apache**, totalmente containerizada utilizando **Docker**.

O backend expõe endpoints responsáveis por funcionalidades como **cadastro de usuários**, **autenticação** e operações relacionadas aos dados da aplicação.

---

## 🎬 Demonstração do Projeto

[![Assista ao vídeo](https://img.youtube.com/vi/CQKah2Mv-3o/maxresdefault.jpg)](https://youtube.com/watch?v=CQKah2Mv-3o)


## 🚀 Tecnologias Utilizadas

- PHP 8 + Apache
- MySQL 8
- phpMyAdmin
- Docker e Docker Compose
- Estrutura em `/www`

---

## 📁 Estrutura do Projeto

/
├── docker-compose.yml
├── Dockerfile
├── www/
│   ├── usuario.php
│   ├── conexao.php
│   └── (outros arquivos PHP)
└── README.md

---

## 🐳 Docker

1. Serviços disponíveis
ServiçoURLBackend PHP/Apachehttp://IP-OU-LOCALHOST/phpMyAdminhttp://SEU-IP-OU-LOCALHOST:8080/MySQLPorta 3306 (internamente: db)

🔧 Configurações do Banco de Dados
As credenciais utilizadas:
Host: db
Database: Estoque
User: CJJPW
Password: CJJPW

O arquivo conexao.php utiliza essas informações para conectar ao MySQL.

## 📌 Endpoints Principais
POST – /usuario.php
Usado para cadastro de novos usuários.
Exemplo de payload:
{
  "nome": "João",
  "email": "joao@gmail.com",
  "senha": "12345"
}

## 🌐 Frontend Separado
O backend funciona independentemente do frontend.
Você pode consumir os endpoints a partir de:

GitHub Pages
localhost
outra VM
qualquer frontend externo
Basta alterar a URL da API no JavaScript do frontend.

## 📤 Deploy
A aplicação pode ser executada em:
Google Cloud VM
AWS EC2
Azure
VPS própria


Clone o repositório e execute:
docker compose up -d --build


📝 Como contribuir

Faça um fork do projeto

Crie sua branch:
git checkout -b minha-feature

Faça commit:
git commit -m "Add new feature"

Envie para sua branch:
git push origin minha-feature

Abra um Pull Request

## 📜 Licença
Este projeto está licenciado sob a MIT License.

## 👨‍💻 Autor

**Carlos Eduardo de Oliveira Bucazio**
- 💻 Estudante de **Análise e Desenvolvimento de Sistemas** | Foco em **DevOps e Infraestrutura**
- 📧 **Email**: [cbucazio@gmail.com](mailto:cbucazio@gmail.com)
- 🌐 **GitHub**: [Krl0sEd](https://github.com/Krl0sEd)
- 🧑‍💼 **Linkedin**: [linkedin.com/in/carlos-eduardo-de-oliveira-bucazio-516a7937a](https://www.linkedin.com/in/carlos-eduardo-de-oliveira-bucazio-516a7937a)

---


