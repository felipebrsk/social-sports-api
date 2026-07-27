1. Usuários, Autenticação e Perfis
users: Armazena as credenciais mínimas de acesso do sistema (e-mail, senha criptografada, verificação de e-mail e status de bloqueio).

profiles: Dados pessoais e de apresentação do usuário (nome completo, foto de perfil, bio, WhatsApp pessoal, etc.).

roles: Tabela referencial que centraliza todos os papéis/níveis de acesso do sistema (ex: Admin do Sistema, Dono de Quadra, Gerente de Quadra, Capitão de Time, Membro).

role_users: Associa usuários aos seus papéis globais no sistema (ex: definir quem é Admin da plataforma).

2. Locais, Quadras e Gestão
venues: As quadras e arenas cadastradas (nome, endereço e o par latitude/longitude para a geolocalização e raio de busca).

venue_managers: Gerencia quem administra cada quadra e qual a sua permissão local (conecta venues, users e a tabela roles para definir se o usuário é owner, manager, etc.).

venue_sport: Tabela pivô que define quais esportes uma quadra específica suporta (ex: a mesma quadra aceitando Vôlei de Praia e Futvôlei).

3. Esportes, Times e Níveis
sports: Tabela referencial dos esportes cadastrados na plataforma (Vôlei, Futsal, Basquete, Futvôlei).

skill_levels: Tabela referencial dos níveis de habilidade exigidos em cada jogo (Iniciante, Intermediário, Avançado, Livre).

teams: Cadastro de grupos/times de amigos (nome, logo, capitão/criador do time e esporte principal).

team_users: Vincula os jogadores a um time e define a função de cada um no grupo (usa a tabela roles para indicar quem é o Capitão ou apenas Membro).

4. Partidas (Jogos) e Solicitações
match_statuses: Tabela referencial para os status da partida (Aberto, Lotado, Cancelado, Concluído).

game_sessions: O coração do app (antiga matches). Armazena a partida agendada (quem criou, em qual quadra, esporte, nível de habilidade, horário de início/fim, limite de vagas, se é em destaque e se há times envolvidos).

request_statuses: Tabela referencial dos status do pedido para entrar no jogo (Pendente, Aprovado, Recusado).

game_session_requests: As solicitações dos jogadores para entrar em uma partida (relaciona o jogador, a sessão de jogo, o status da aprovação e a justificativa em caso de recusa).

5. Comunicação e Redes Sociais Dinâmicas
conversation_types: Tabela referencial dos tipos de bate-papo (Grupo do Jogo, Conversa Direta).

conversations: As salas de chat do sistema. Vinculadas ao conversation_type_id e opcionalmente a uma game_session_id.

conversation_users: Controla os participantes de cada conversa e registra a última vez que visualizaram o chat (notificação de mensagens não lidas).

messages: As mensagens enviadas nas conversas (remetente, conteúdo e data/hora).

social_networks: Tabela referencial dos tipos de redes/links (Instagram, Grupo do WhatsApp, TikTok, YouTube, Website).

social_links: Tabela polimórfica de links dinâmicos. Permite anexar múltiplos links/grupos a qualquer entidade do sistema (ao Perfil do Usuário, à Quadra, ao Time ou especificamente à Partida do dia).

6. Monetização e Transações
payment_statuses: Tabela referencial do status da transação (Aguardando Pagamento, Pago, Cancelado, Reembolsado).

payment_types: Tabela referencial do motivo da cobrança (Destaque de Partida no topo da cidade, Taxa de Reserva, Assinatura de Arena).

payments: Registra as movimentações financeiras da plataforma (valor, usuário que pagou, partida vinculada, gateway de pagamento e o código Pix gerado).

7. Suporte, Feedbacks e Solicitações
feedback_categories: Tabela referencial com os tipos de envio (Sugestão, Reclamação, Bug, Solicitar Esporte, Solicitar Quadra).

feedback_statuses: Tabela referencial com os status de atendimento (Pendente, Em Análise, Resolvido, Recusado).

feedbacks: Tabela polimórfica de registros enviados pelos usuários para a administração da plataforma (com título, descrição, notas do admin e vínculo opcional a quadras, partidas ou usuários).