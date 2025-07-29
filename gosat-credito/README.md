# GoSat Crédito - Sistema de Consulta de Ofertas de Crédito

Este projeto é uma implementação completa de um sistema para consultar ofertas de crédito via API externa, desenvolvido como teste prático para a empresa GoSat.

## 📋 Sobre o Projeto

O sistema permite consultar ofertas de crédito disponíveis para CPFs específicos, integrando-se com a API externa da GoSat para obter e processar informações de diferentes instituições financeiras. As ofertas são analisadas, classificadas e apresentadas ao usuário através de uma interface web moderna.

## 🚀 Funcionalidades

### API
- ✅ **Consulta de Ofertas de Crédito**: Endpoint para consultar ofertas disponíveis para um CPF
- ✅ **Histórico de Consultas**: Endpoint para consultar histórico de consultas por CPF
- ✅ **Documentação Swagger**: Documentação completa da API
- ✅ **Validação de Dados**: Validação rigorosa de entrada
- ✅ **Tratamento de Erros**: Respostas padronizadas para erros
- ✅ **Persistência de Dados**: Armazenamento de consultas e ofertas no banco

### Frontend Web
- ✅ **Dashboard Intuitivo**: Página inicial com estatísticas gerais
- ✅ **Consulta Interativa**: Interface para realizar consultas de crédito
- ✅ **Relatórios Gráficos**: Gráficos usando Chart.js para visualização de dados
- ✅ **Histórico Completo**: Visualização de todas as consultas realizadas
- ✅ **Design Responsivo**: Interface adaptável para desktop e mobile

### Funcionalidades Técnicas
- ✅ **Algoritmo de Ranking**: Sistema inteligente para ordenar ofertas por vantagem
- ✅ **Cache de Instituições**: Armazenamento local de dados das instituições
- ✅ **Logs Detalhados**: Sistema de logs para debugging e monitoramento
- ✅ **Banco de Dados Relacional**: Estrutura normalizada para dados

## 🛠️ Tecnologias Utilizadas

- **Backend**: Laravel 12.x
- **Frontend**: Bootstrap 5.3, Chart.js, Font Awesome
- **Banco de Dados**: SQLite (desenvolvimento) / MySQL (produção)
- **Documentação**: Swagger/OpenAPI
- **HTTP Client**: Guzzle HTTP
- **Outros**: Axios (frontend), PHP 8.1+

## 📦 Instalação

### Pré-requisitos
- PHP 8.1 ou superior
- Composer
- Node.js (opcional, para desenvolvimento frontend)
- Servidor web (Apache/Nginx) ou usar `php artisan serve`

### Passo a Passo

1. **Clone o repositório**
```bash
git clone <seu-repositorio>
cd gosat-credito
```

2. **Instale as dependências**
```bash
composer install
```

3. **Configure o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure o banco de dados**
Edite o arquivo `.env` com suas configurações de banco:
```env
DB_CONNECTION=sqlite
DB_DATABASE=/caminho/absoluto/para/database.sqlite
```

Ou para MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gosat_credito
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

5. **Execute as migrations**
```bash
php artisan migrate
```

6. **Gere a documentação Swagger**
```bash
php artisan l5-swagger:generate
```

7. **Inicie o servidor**
```bash
php artisan serve
```

O projeto estará disponível em `http://localhost:8000`

## 🔗 Endpoints da API

### Base URL
```
http://localhost:8000/api
```

### Consultar Ofertas de Crédito
```http
POST /consultar-credito
Content-Type: application/json

{
  "cpf": "11111111111"
}
```

**Resposta de Sucesso:**
```json
{
  "success": true,
  "data": {
    "cpf": "11111111111",
    "total_ofertas_encontradas": 5,
    "ofertas_selecionadas": 3,
    "ofertas": [
      {
        "instituicaoFinanceira": "Banco PingApp",
        "modalidadeCredito": "crédito pessoal",
        "valorAPagar": 7000.00,
        "valorSolicitado": 3000.00,
        "taxaJuros": 0.0365,
        "qntParcelas": 48,
        "scoreVantagem": 3.6510
      }
    ]
  },
  "message": "Ofertas consultadas com sucesso!"
}
```

### Consultar Histórico
```http
GET /historico/{cpf}
```

### Documentação Completa
Acesse: `http://localhost:8000/docs` para ver a documentação Swagger completa.

## 💾 CPFs de Teste

O sistema funciona com os seguintes CPFs de teste:
- `11111111111`
- `12312312312` 
- `22222222222`

## 🎯 Como Usar o Sistema

### Via Interface Web

1. **Acesse o Dashboard**: `http://localhost:8000`
2. **Navegue para "Consultar Crédito"**
3. **Digite um CPF de teste** ou clique em um dos botões de CPF disponível
4. **Clique em "Consultar"**
5. **Visualize os resultados** ordenados da melhor para a pior oferta

### Via API

Use qualquer cliente HTTP (Postman, Insomnia, curl) para fazer requisições para os endpoints documentados.

**Exemplo com curl:**
```bash
curl -X POST http://localhost:8000/api/consultar-credito \
  -H "Content-Type: application/json" \
  -d '{"cpf": "11111111111"}'
```

## 📊 Algoritmo de Classificação

O sistema utiliza um algoritmo proprietário para classificar as ofertas por vantagem:

### Score de Vantagem
```php
score = (taxa_juros * 100) + (qnt_parcelas * 0.1) + (1 / valor_max * 1000)
```

**Critérios (em ordem de importância):**
1. **Taxa de Juros** (peso maior): Menor taxa = melhor oferta
2. **Quantidade de Parcelas** (peso médio): Menos parcelas = melhor
3. **Valor Disponível** (peso menor): Maior valor = melhor

**Quanto menor o score, melhor a oferta.**

## 🗂️ Estrutura do Banco de Dados

### Tabelas Principais

#### `consultas`
- `id`: ID único da consulta
- `cpf`: CPF consultado
- `resposta_api`: Resposta completa da API (JSON)
- `total_ofertas`: Total de ofertas encontradas
- `created_at`, `updated_at`: Timestamps

#### `ofertas`
- `id`: ID único da oferta
- `consulta_id`: Referência para a consulta
- `cpf`: CPF da consulta
- `instituicao_id`: ID da instituição
- `instituicao_nome`: Nome da instituição
- `modalidade_credito`: Modalidade de crédito
- `cod_modalidade`: Código da modalidade
- `valor_pagar`: Valor total a ser pago
- `valor_solicitado`: Valor solicitado
- `taxa_juros`: Taxa de juros mensal
- `qnt_parcelas`: Quantidade de parcelas
- `score_vantagem`: Score calculado para ranking
- `created_at`, `updated_at`: Timestamps

#### `instituicoes`
- `id`: ID único
- `instituicao_id`: ID da instituição na API externa
- `nome`: Nome da instituição
- `modalidades`: Modalidades disponíveis (JSON)
- `created_at`, `updated_at`: Timestamps

## 📈 Relatórios e Gráficos

O sistema inclui uma seção completa de relatórios com:

- **Gráfico de Linhas**: Consultas por dia (últimos 30 dias)
- **Gráfico de Pizza**: Distribuição de ofertas por instituição
- **Gráfico de Barras**: Modalidades de crédito mais procuradas
- **Tabelas Detalhadas**: Resumos e estatísticas

## 🔒 Segurança

- Validação rigorosa de entrada de dados
- Sanitização de CPF (apenas números)
- Proteção CSRF em formulários web
- Logs de segurança para monitoramento
- Rate limiting implícito via timeouts

## 🚀 Deploy em Produção

### Requisitos
- Servidor Linux com PHP 8.1+
- Composer
- Banco de dados MySQL/PostgreSQL
- Nginx/Apache

### Passos Básicos
1. Configure o ambiente de produção no `.env`
2. Use um banco de dados robusto (MySQL/PostgreSQL)
3. Configure o servidor web
4. Execute as migrations
5. Gere a documentação Swagger
6. Configure SSL/HTTPS

### Variáveis de Ambiente Importantes
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com

DB_CONNECTION=mysql
DB_HOST=seu-host-db
DB_DATABASE=sua_base
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

## 🐛 Troubleshooting

### Problemas Comuns

**Erro de conexão com API externa:**
- Verifique sua conexão com internet
- Confirme se a API da GoSat está online
- Verifique os logs em `storage/logs/laravel.log`

**Erro de banco de dados:**
- Confirme as configurações no `.env`
- Execute `php artisan migrate` novamente
- Verifique permissões do arquivo SQLite

**Swagger não carrega:**
- Execute `php artisan l5-swagger:generate`
- Verifique se o diretório `storage/api-docs` tem permissões corretas

## 📝 Logs

Os logs do sistema ficam em:
- `storage/logs/laravel.log`: Logs gerais da aplicação
- `storage/logs/api.log`: Logs específicos de chamadas para API externa

## 🧪 Testes

### Testes Manuais
1. **Teste Básico**: Consulte um CPF válido via interface web
2. **Teste de API**: Use Postman/curl para testar endpoints
3. **Teste de Validação**: Tente usar CPF inválido
4. **Teste de Relatórios**: Verifique se gráficos carregam corretamente

### Testes Automatizados
```bash
php artisan test
```

## 🤝 Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto foi desenvolvido como teste prático para a GoSat.

## 🆘 Suporte

Para dúvidas ou problemas:
1. Verifique a documentação acima
2. Consulte os logs da aplicação
3. Verifique a documentação Swagger em `/docs`

---

**Desenvolvido para GoSat Telecom** 
Sistema de Consulta de Ofertas de Crédito - Teste Prático Dev Sr
