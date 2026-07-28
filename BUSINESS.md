# Modelo de Negócios & Visão de Produto

## 🎯 1. Visão Geral & Proposta de Valor

O **App de Jogos e Quadras** é uma plataforma marketplace/comunitária pensada para resolver a dor de encontrar partidas esportivas informais ("babas") e quadras disponíveis em tempo real em uma determinada região.

### A Dor
* Atletas e entusiastas que viajam ou mudam de bairro não encontram facilmente partidas abertas do seu esporte preferido (ex: Vôlei de Praia, Futsal, Futvôlei).
* Organizadores de partidas têm dificuldade para preencher as últimas vagas de uma sessão de jogo em cima da hora.
* Arenas e quadras privadas possuem horários ociosos na grade.

### A Solução
* **Para o Atleta:** Encontrar partidas abertas ordenadas por proximidade via GPS ou filtro de cidade, solicitar vaga e entrar no chat do jogo.
* **Para o Organizador:** Criar partidas rapidamente com aviso de sobreposição de horários e impulsionar a busca de vagas via Pix.
* **Para a Arena/Quadra:** Divulgar sua estrutura e horários disponíveis com selos de verificação e destaque regional.

---

## 💰 2. Estratégia de Monetização Mínima (MVP)

O objetivo financeiro inicial da plataforma é a **auto-sustentabilidade** (cobrir os custos do servidor VPS/Gateway de $5 a $10/mês) sem criar barreiras de entrada para os usuários comuns.

### 2.1. Destaque de Partida / "Impulsionar Baba" (B2C)
* **Público:** Organizadores de partidas que precisam preencher vagas com urgência.
* **Modelo:** Pagamento avulso por evento (Microtransação via Pix ex: R$ 2,00 a R$ 5,00).
* **Benefício:**
  * Posição fixada no **topo da lista da cidade/região** (prioridade no algoritmo de busca).
  * Tag/Selo visual destacado (ex: 🔥 Urgente / Destaque).

### 2.2. Destaque de Arena / Quadra Parceira (B2B)
* **Público:** Donos de arenas e quadras privadas.
* **Modelo:** Assinatura recorrente ou taxa de verificação/destaque.
* **Benefício:**
  * Selo de **"Quadra Oficial / Verificada"** (`verified = true`).
  * Prioridade na exibição na busca geral de quadras da cidade.

---

## 🔍 3. Mecânica de Ordenação & Algoritmo de Busca

Para garantir que a monetização funcione sem prejudicar a experiência do usuário (UX), a ordenação do feed combina **Destaque Monetizado + Localização/Data**:

### 3.1. Ordenação do Feed de Partidas (`GameSession`)

1. **1º Critério:** Status de Destaque (`is_featured = true`) no topo.
2. **2º Critério:** Proximidade geográfica via GPS (`distance_in_km` ascendente) ou Horário de início (`start_time` ascendente).

*Lógica conceitual da Query no Eloquent:*
- `ORDER BY is_featured DESC, distance_in_km ASC, start_time ASC`

### 3.2. Ordenação da Busca de Quadras (`Venue`)

1. **1º Critério:** Quadra Verificada/Parceira (`verified = true`) no topo.
2. **2º Critério:** Proximidade geográfica via GPS (`distance_in_km` ascendente) ou Ordem Alfabética por Cidade.

---

## 🧭 4. Estratégia de Geolocalização & UX (Navegador e App)

A localização é o pilar de usabilidade da plataforma, tratada em 2 níveis de fallback:

* **Quando autoriza GPS:** Busca por raio de KM (Fórmula de Haversine via Lat/Lng).
* **Quando nega GPS:**
  1. Exibe filtro de Cidade / Estado escolhido manualmente pelo usuário.
  2. Se nada for escolhido, exibe destaques/recentes gerais da plataforma.

### Regra de Ouro da Geolocalização
* **Nunca travar a tela:** Se o usuário negar a localização, o app/site mostra um seletor de cidade para evitar telas em branco.
* **Cadastro Mapeado:** Todo cadastro de quadra feito pela comunidade exige marcar o pino direto no mapa, garantindo que o banco de dados **sempre armazene `latitude` e `longitude` exatas**.

---

## 🔁 5. Ciclo de Vida da Partida & Solicitações

Fluxo operacional:
1. **Criação:** Qualquer usuário pode criar um jogo. Se houver choque de horário na mesma quadra, o sistema emite um alerta de confirmação.
2. **Solicitação:** Jogadores pedem para entrar no jogo (`pending`).
3. **Aprovação/Recusa:** O organizador aprova (o que incrementa a vaga e adiciona no chat) ou recusa justificando o motivo.
4. **Finalização:** Ao atingir o número máximo de vagas, a partida assume o status `full` automaticamente.

### 5.1. Gestão de Lista Mista (Interna + Externa)
* O organizador define o limite total (`max_players`) e declara quantas vagas já foram ocupadas por fora (`external_players_count`).
* O app disponibiliza para solicitação pública apenas o saldo: `max_players - (external_players_count + approved_requests)`.
* Atualizações no saldo externo recalculam o status da partida (`open` vs `full`) em tempo real.

---

## 📊 6. Métricas Principais (KPIs para Acompanhamento)

* **Engajamento:** Total de partidas criadas vs. Total de solicitações aprovadas.
* **Taxa de Preenchimento:** % de vagas preenchidas por partida.
* **Monetização:** Quantidade de Pix de destaque gerados/pagos por mês.
* **Conversão de Feedbacks:** Tempo médio para análise e aprovação de solicitações de novos esportes e verificação de quadras.
