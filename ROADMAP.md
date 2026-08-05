# 🚀 Roadmap de Desenvolvimento: App de Jogos e Quadras (Clean Architecture + DDD)

## 📌 Fase 1: Base de Autenticação, Verificação e Perfis
> **Arquitetura Base:** Controllers Invocáveis (`__invoke`), DTOs, Form Requests, Services/Contracts e Scramble Attributes.

- [x] **1.1. Login de Usuário (`LoginController`)**
  - [x] DTO `LoginAttempt` e Service `AuthenticationService`.
  - [x] Endpoint `POST /authentication/login` (`authentication.login`).
- [x] **1.2. Esqueci a Senha (`ForgotPasswordController`)**
  - [x] DTO `ForgotPassword` e Service `ResetPasswordService`.
  - [x] Endpoint `POST /authentication/password/forgot` (`authentication.forgot-password`).
- [x] **1.3. Redefinição de Senha (`ResetPasswordController`)**
  - [x] DTO `ResetPassword` e Service `ResetPasswordService`.
  - [x] Endpoint `POST /authentication/password/reset` (`authentication.reset-password`).
- [x] **1.4. Detalhes do Usuário Autenticado (`MeController`)**
  - [x] `UserResource` e `AuthContextServiceInterface`.
  - [x] Endpoint `GET /me` (`user.me`).
- [x] **1.5. Verificação de E-mail (`EmailVerification`)**
  - [x] `VerificationController` - `verify`: Validação da URL assinada (`email_verified_at`).
    - [x] Endpoint `GET /authentication/email/verify/{id}/{hash}` (`authentication.email.verify`).
  - [x] `VerificationController` - `resend`: Reenvio de e-mail de verificação.
    - [x] Endpoint `POST /authentication/email/resend` (`authentication.email.resend`).
- [x] **1.6. Cadastro de Usuário (`RegisterController`)**
  - [x] Request `RegisterRequest`, DTO `RegisterUser` e Service `RegisterUserService`.
  - [x] Disparo automático de e-mail com link assinado para verificação (`MustVerifyEmail`).
  - [x] Criação do registro padrão em `profiles`.
  - [x] Endpoint `POST /authentication/register` (`authentication.register`).
- [x] **1.7. Gestão do Perfil Pessoal (`ProfileController`)**
  - [x] Resource `ProfileResource`, DTO `UpdateProfile`, Request `ProfileUpdateRequest`.
  - [x] Adicionar ao Endpoint `GET /me` (`user.me`): Dados do perfil + redes sociais.
  - [x] Endpoint `PUT /profiles` (`profiles.update`): Atualizar bio, foto, WhatsApp e Instagram.
- [x] **1.8. Login Social (`GoogleController`)**
  - [x] Resource `GoogleRequest`, DTO `GoogleAuthentication`.
  - [x] Adicionar ao Endpoint `POST /authentication/oauth/google` (`authentication.oauth.google`): Callback de Login Social - Devolução do Token.

---

## 📌 Fase 2: Área do Usuário / App (Público & Jogadores)
> **Objetivo:** Consultas, agendamentos, interações e solicitações do atleta.

- [x] **2.1. Consulta de Esportes (`Sport`)**
  - [x] `ListSportsController` (`GET /sports` -> `sports.index`): Lista simples de esportes para filtros e navegação.
- [x] **2.2. Consulta e Busca de Quadras (`Venue`)**
  - [x] `VenueController`, `index` (`GET /venues` -> `venues.index`): Busca com filtros por `sport_id`, GPS (Haversine) ou Cidade/Estado.
  - [x] `VenueController`, `show` (`GET /venues/{venue}` -> `venues.show`): Detalhes da quadra, endereço, fotos e esportes aceitos.
- [ ] **2.3. Sessões de Jogo (`GameSession`)**
  - [x] `GameSessionsController`, `index` (`GET /game-sessions` -> `game-sessions.index`): Feed principal por esporte, data, nível de habilidade e proximidade.
  - [x] `GameSessionController`, `show` (`GET /game-sessions/{gameSession}` -> `game-sessions.show`): Retorna organizador, quadra, lista de participantes e links do grupo de WhatsApp.
  - [ ] `GameSessionController`, `store` (`POST /game-sessions` -> `game-sessions.store`): Criar jogo com verificação de sobreposição de horários na mesma quadra. Tratar flag `force_create`.
  - [ ] `GameSessionController`, `update` (`PUT /game-sessions/{gameSession}` -> `game-sessions.update` - Organizador).
  - [ ] `GameSessionController`, `cancel` (`PATCH /game-sessions/{gameSession}/cancel` -> `game-sessions.cancel` - Organizador).
- [ ] **2.4. Participação e Vagas (`GameSessionRequest`)**
  - [ ] `CreateGameSessionRequestController` (`POST /game-sessions/{gameSession}/requests` -> `game-sessions.requests.store`): Pedir para entrar no jogo.
  - [ ] `ListGameSessionRequestsController` (`GET /game-sessions/{gameSession}/requests` -> `game-sessions.requests.index` - Organizador).
  - [ ] `ApproveGameSessionRequestController` (`PATCH /requests/{request}/approve` -> `requests.approve` - Organizador): Aprova atleta, atualiza vagas e adiciona no chat.
  - [ ] `RejectGameSessionRequestController` (`PATCH /requests/{request}/reject` -> `requests.reject` - Organizador): Exige `rejection_reason`.
  - [ ] `ListMyRequestsController` (`GET /my-requests` -> `requests.my-requests`): Acompanhar status dos meus pedidos.
- [ ] **2.5. Bate-Papo & Mídias (`Conversations` e `SocialLinks`)**
  - [ ] `ListConversationsController` (`GET /conversations` -> `conversations.index`).
  - [ ] `ListMessagesController` (`GET /conversations/{conversation}/messages` -> `conversations.messages.index`).
  - [ ] `SendMessageController` (`POST /conversations/{conversation}/messages` -> `conversations.messages.store`).
  - [ ] `CreateSocialLinkController` (`POST /social-links` -> `social-links.store`): Anexar link do grupo do WhatsApp à partida ou Instagram.
- [ ] **2.6. Central de Atendimento e Solicitação de Esportes (`Feedback`)**
  - [ ] `CreateFeedbackController` (`POST /feedbacks` -> `feedbacks.store`): Enviar chamado para:
    - [ ] *Solicitar novo esporte* (`category_id` = request_sport).
    - [ ] *Sugestões, reclamações gerais ou reporte de bugs*.
  - [ ] `ListMyFeedbacksController` (`GET /my-feedbacks` -> `feedbacks.my-feedbacks`): Jogador acompanha o andamento do suporte.

---

## 📌 Fase 3: Gestão de Quadras (Donos & Comunidade)
> **Objetivo:** Cadastro com geolocalização nativa e gestão por donos/gerentes (`venue_managers`).

- [ ] **3.1. Cadastro e Gestão de Arenas (`Venue`)**
  - [ ] `CreateVenueController` (`POST /venues` -> `venues.store`): 
    - Qualquer usuário ou dono cadastra uma quadra marcando o pino exato no mapa (salvando `latitude` e `longitude`).
    - Salva a quadra como não verificada (`verified = false`) e vincula o criador em `venue_managers`.
  - [ ] `UpdateVenueController` (`PUT /venues/{venue}` -> `venues.update`): Atualizar informações, fotos e esportes suportados (Apenas Gestores/Admin).
  - [ ] `ListMyVenuesController` (`GET /my-venues` -> `venues.my-venues`): Listar quadras que o usuário gerencia.

---

## 📌 Fase 4: Área Administrativa (Backoffice / Painel Admin)
> **Objetivo:** Gestão global, moderação de quadras e aprovação de solicitações (`/admin`).

- [ ] **4.1. Gestão Geral de Esportes (`Admin\Sport`)**
  - [ ] `Admin\ListSportsController` (`GET /admin/sports` -> `admin.sports.index`): Lista completa para o painel com estatísticas/counts.
  - [ ] `Admin\CreateSportController` (`POST /admin/sports` -> `admin.sports.store`).
  - [ ] `Admin\UpdateSportController` (`PUT /admin/sports/{sport}` -> `admin.sports.update`).
  - [ ] `Admin\DeleteSportController` (`DELETE /admin/sports/{sport}` -> `admin.sports.destroy`).
- [ ] **4.2. Moderação e Verificação de Quadras (`Admin\Venue`)**
  - [ ] `Admin\ListVenuesController` (`GET /admin/venues` -> `admin.venues.index`): Filtro por quadras pendentes (`verified = false`).
  - [ ] `Admin\VerifyVenueController` (`PATCH /admin/venues/{venue}/verify` -> `admin.venues.verify`): Concede o selo de quadra oficial/parceira.
  - [ ] `Admin\DeleteVenueController` (`DELETE /admin/venues/{venue}` -> `admin.venues.destroy`).
- [ ] **4.3. Moderação de Usuários (`Admin\User`)**
  - [ ] `Admin\ListUsersController` (`GET /admin/users` -> `admin.users.index`).
  - [ ] `Admin\ToggleBlockUserController` (`PATCH /admin/users/{user}/block` -> `admin.users.block`).
- [ ] **4.4. Atendimento de Chamados e Solicitação de Esportes (`Admin\Feedback`)**
  - [ ] `Admin\ListFeedbacksController` (`GET /admin/feedbacks` -> `admin.feedbacks.index`).
  - [ ] `Admin\ShowFeedbackController` (`GET /admin/feedbacks/{feedback}` -> `admin.feedbacks.show`).
  - [ ] `Admin\UpdateFeedbackController` (`PATCH /admin/feedbacks/{feedback}` -> `admin.feedbacks.update`):
    - [ ] Atualizar status para `resolved` ou `rejected` e responder no `admin_notes`.
    - [ ] *Fluxo do Esporte:* Se aceitar a solicitação de novo esporte, o Admin executa o `Admin\CreateSportController` e conclui o chamado.

---

## 📌 Fase 5: Monetização via Pix (Destaque de Jogos)
> **Objetivo:** Infraestrutura financeira simples para manutenção do servidor.

- [ ] **5.1. Cobrança e Gateway**
  - [ ] `CreateFeaturedMatchPaymentController` (`POST /game-sessions/{gameSession}/feature` -> `payments.feature`): Gera QR Code do Pix para destacar o jogo no topo da cidade.
  - [ ] Service de integração com Gateway (Mercado Pago / Asaas).
- [ ] **5.2. Processamento de Webhooks**
  - [ ] `PaymentWebhookController` (`POST /webhooks/payments/{gateway}` -> `webhooks.payments`): Confirma pagamento e atualiza `game_sessions.is_featured = true`.
