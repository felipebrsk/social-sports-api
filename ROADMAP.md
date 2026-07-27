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
  - [x] `VerificationController` - `verify`: Processa a validação da URL assinada enviada por e-mail (`email_verified_at`).
    - [x] Endpoint `GET /authentication/email/verify/{id}/{hash}` (`authentication.email.verify`).
  - [x] `VerificationController` - `resend`: Reenvia a notificação de verificação de e-mail caso o usuário solicite.
    - [x] Endpoint `POST /authentication/email/resend` (`authentication.email.resend`).
- [x] **1.6. Cadastro de Usuário (`RegisterController`)**
  - [x] Request `RegisterRequest`, DTO `RegisterUser` e Service `RegisterUserService`.
  - [x] Disparo automático de e-mail com link assinado para verificação (`MustVerifyEmail`).
  - [x] Criação do registro padrão em `profiles`.
  - [x] Endpoint `POST /authentication/register` (`authentication.register`).
- [ ] **1.7. Gestão do Perfil Pessoal (`ProfileController`)**
  - [ ] Resource `ProfileResource`, DTO `UpdateProfile`.
  - [ ] Endpoint `GET /profile` (`profile.show`): Dados do perfil + redes sociais.
  - [ ] Endpoint `PUT /profile` (`profile.update`): Atualizar bio, foto e WhatsApp.

---

## 📌 Fase 2: Serviços de Apoio e Cadastros Base
> **Objetivo:** Implementar tabelas de referência e gestão de quadras/esportes.

- [ ] **2.1. Módulo de Esportes (`Sport`)**
  - [ ] `ListSportsController` (`GET /sports` -> `sports.index`)
  - [ ] `CreateSportController` (`POST /sports` -> `sports.store` - Admin)
  - [ ] `UpdateSportController` (`PUT /sports/{sport}` -> `sports.update` - Admin)
  - [ ] `DeleteSportController` (`DELETE /sports/{sport}` -> `sports.destroy` - Admin)
- [ ] **2.2. Módulo de Quadras / Locais (`Venue`)**
  - [ ] `CreateVenueController` (`POST /venues` -> `venues.store`): Cadastro com Lat/Long e vínculo inicial em `venue_managers`.
  - [ ] `ListVenuesController` (`GET /venues` -> `venues.index`): Busca com cálculo de distância (Haversine) por raio em KM.
  - [ ] `ShowVenueController` (`GET /venues/{venue}` -> `venues.show`): Detalhes da quadra e esportes suportados.
  - [ ] `UpdateVenueController` (`PUT /venues/{venue}` -> `venues.update`): Edição restrita aos gestores da quadra (`venue_managers`).
- [ ] **2.3. Módulo de Times / Equipes (`Team`) - *Opcional no MVP***
  - [ ] `CreateTeamController` (`POST /teams` -> `teams.store`): Define `leader_id`.
  - [ ] `AddTeamMemberController` (`POST /teams/{team}/members` -> `teams.members.store`).

---

## 📌 Fase 3: O Coração do App - Sessões de Jogo (`GameSession`)
> **Objetivo:** Agendamento de partidas com validações de horário e filtros geoespaciais.

- [ ] **3.1. Criação de Partidas com Validação de Conflito**
  - [ ] Service `GameSessionService` com verificação de sobreposição de horários (`start_time` / `end_time` no mesmo `venue_id`).
  - [ ] Tratar flag `force_create = true` em DTO caso o usuário aceite criar com conflito.
  - [ ] `CreateGameSessionController` (`POST /game-sessions` -> `game-sessions.store`).
- [ ] **3.2. Feed Principal e Detalhes da Partida**
  - [ ] `ListGameSessionsController` (`GET /game-sessions` -> `game-sessions.index`): Filtros por esporte, nível (`skill_level_id`), data e proximidade por GPS.
  - [ ] `ShowGameSessionController` (`GET /game-sessions/{gameSession}` -> `game-sessions.show`): Retorna organizador, quadra, participantes aprovados e links externos.
- [ ] **3.3. Cancelamento e Edição**
  - [ ] `UpdateGameSessionController` (`PUT /game-sessions/{gameSession}` -> `game-sessions.update`).
  - [ ] `CancelGameSessionController` (`PATCH /game-sessions/{gameSession}/cancel` -> `game-sessions.cancel`).

---

## 📌 Fase 4: Solicitações de Entrada e Vagas (`GameSessionRequest`)
> **Objetivo:** Fluxo de participação em partidas, aprovações e recusas.

- [ ] **4.1. Solicitação de Vaga pelo Jogador**
  - [ ] `CreateGameSessionRequestController` (`POST /game-sessions/{gameSession}/requests` -> `game-sessions.requests.store`).
  - [ ] Validações: partida aberta, usuário sem solicitação prévia e limites de vagas.
- [ ] **4.2. Gestão de Solicitações (Criador do Jogo)**
  - [ ] `ListGameSessionRequestsController` (`GET /game-sessions/{gameSession}/requests` -> `game-sessions.requests.index`).
  - [ ] `ApproveGameSessionRequestController` (`PATCH /requests/{request}/approve` -> `requests.approve`):
    - [ ] Atualiza status para `approved`.
    - [ ] Atualiza `game_sessions` para `full` se `max_players` for atingido.
    - [ ] Adiciona o usuário em `conversation_users` do bate-papo da partida.
  - [ ] `RejectGameSessionRequestController` (`PATCH /requests/{request}/reject` -> `requests.reject`):
    - [ ] Exige o campo `rejection_reason` na Request.
- [ ] **4.3. Minhas Solicitações**
  - [ ] `ListMyRequestsController` (`GET /my-requests` -> `requests.my-requests`).

---

## 📌 Fase 5: Comunicação e Mídias (`Conversations` e `SocialLinks`)
> **Objetivo:** Chat em grupo das partidas e anexação de links externos (WhatsApp/Instagram).

- [ ] **5.1. Bate-Papo Interno**
  - [ ] Criação automática da `Conversation` (`conversation_type_id` = Grupo do Jogo) ao criar uma `GameSession`.
  - [ ] `ListConversationsController` (`GET /conversations` -> `conversations.index`).
  - [ ] `ListMessagesController` (`GET /conversations/{conversation}/messages` -> `conversations.messages.index`).
  - [ ] `SendMessageController` (`POST /conversations/{conversation}/messages` -> `conversations.messages.store`).
- [ ] **5.2. Links e Redes Sociais Polimórficas**
  - [ ] `CreateSocialLinkController` (`POST /social-links` -> `social-links.store`): Anexar link do grupo do WhatsApp à partida ou Instagram à quadra/perfil.

---

## 📌 Fase 6: Monetização via Pix (Destaque de Jogos)
> **Objetivo:** Cobrança simbólica para destaque no topo das buscas da cidade.

- [ ] **6.1. Geração de Cobrança Pix**
  - [ ] `CreateFeaturedMatchPaymentController` (`POST /game-sessions/{gameSession}/feature` -> `payments.feature`).
  - [ ] Service de integração com Gateway (Mercado Pago / Asaas).
- [ ] **6.2. Processamento Assíncrono (Webhook)**
  - [ ] `PaymentWebhookController` (`POST /webhooks/payments/{gateway}` -> `webhooks.payments`).
  - [ ] Atualizar `payment_statuses` para `paid` e marcar `game_sessions.is_featured = true`.

---

## 📌 Fase 7: Central de Atendimento, Sugestões e Feedbacks (`Feedback`)
> **Objetivo:** Receber solicitações de novos esportes, quadras ou relatos de erros.

- [ ] **7.1. Canal do Usuário**
  - [ ] `CreateFeedbackController` (`POST /feedbacks` -> `feedbacks.store`).
  - [ ] `ListMyFeedbacksController` (`GET /my-feedbacks` -> `feedbacks.my-feedbacks`).
- [ ] **7.2. Painel de Atendimento (Admin)**
  - [ ] `ListAdminFeedbacksController` (`GET /admin/feedbacks` -> `admin.feedbacks.index`).
  - [ ] `UpdateAdminFeedbackController` (`PATCH /admin/feedbacks/{feedback}` -> `admin.feedbacks.update`).