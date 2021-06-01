----------------------
--ESTRUTURA.SQL
----------------------
-- Definições das tabelas do Colonization

--Tabela com os dados do Império
CREATE TABLE colonization_imperio (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nome VARCHAR(30) NOT NULL,
id_jogador INT(30) NOT NULL,
prestigio INT(30) DEFAULT 0
)

--Tabela com os dados das estrelas
CREATE TABLE colonization_estrela (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nome VARCHAR(255) NOT NULL,
descricao TEXT DEFAULT '',
comentarios TEXT DEFAULT NULL,
X INT(6) NOT NULL,
Y INT(6) NOT NULL,
Z INT(6) NOT NULL,
tipo VARCHAR(255) NOT NULL,
cerco BOOLEAN DEFAULT FALSE,
ids_estrelas_destino TEXT DEFAULT '',
UNIQUE KEY (X, Y, Z)
)

--Tabela com os dados dos planetas e corpos celestes (luas e asteróides)
--OBS: Caso o planeta tenha luas, elas serão definidas com CLASSE="lua" e seu atributo "posicao" será
--o mesmo do planeta que orbitam. A subclasse define o tipo de biosfera que é capaz de sustentar
CREATE TABLE colonization_planeta (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_estrela INT(6) NOT NULL,
nome VARCHAR(255) NOT NULL,
posicao INT(3) NOT NULL,
classe VARCHAR(255) NOT NULL,
subclasse VARCHAR(255) DEFAULT NULL,
tamanho INT(2) NOT NULL,
inospito BOOLEAN DEFAULT TRUE
)

--Tabela com os tipos de instalações
CREATE TABLE IF NOT EXISTS colonization_instalacao (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nome VARCHAR(255) NOT NULL,
descricao TEXT NOT NULL,
id_tech INT(6) DEFAULT 0,
slots INT(6) DEFAULT 1,
autonoma BOOLEAN DEFAULT FALSE,
desguarnecida BOOLEAN DEFAULT FALSE,
sempre_ativa BOOLEAN DEFAULT TRUE,
oculta BOOLEAN DEFAULT FALSE,
icone VARCHAR(255) DEFAULT '',
especiais VARCHAR(255) DEFAULT '',
custos VARCHAR(255) DEFAULT ''
)

--Tabela com os tipos de recursos existentes
CREATE TABLE colonization_recurso (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nome VARCHAR(255) NOT NULL,
descricao TEXT NOT NULL,
icone VARCHAR(255) DEFAULT '',
nivel INT(6) DEFAULT 1,
acumulavel BOOLEAN DEFAULT TRUE,
extrativo BOOLEAN DEFAULT TRUE,
local BOOLEAN DEFAULT FALSE
)

--Tabela com as Techs existentes
CREATE TABLE IF NOT EXISTS colonization_tech (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nome VARCHAR(255) NOT NULL,
descricao TEXT NOT NULL,
nivel INT(6) NOT NULL,
custo INT(6) NOT NULL,
id_tech_parent VARCHAR(255) DEFAULT 0,
lista_requisitos VARCHAR(255) DEFAULT '',
belica BOOLEAN DEFAULT FALSE,
parte_nave BOOLEAN DEFAULT FALSE,
publica BOOLEAN DEFAULT TRUE,
especiais VARCHAR(255) DEFAULT '',
icone VARCHAR(255) DEFAULT ''
)

--Tabela com os recursos consumidos ou produzidos por uma instalação
CREATE TABLE colonization_instalacao_recursos (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_instalacao INT(6) NOT NULL,
id_recurso INT(6) NOT NULL,
qtd_por_nivel INT(6) NOT NULL,
consome BOOLEAN DEFAULT TRUE
)

--Tabela com os recursos disponíveis do planeta. Refere-se a recursos que podem ser explorados
CREATE TABLE colonization_planeta_recursos (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_planeta INT(6) NOT NULL,
id_recurso INT(6) NOT NULL,
disponivel INT(6) NOT NULL,
turno INT(6) NOT NULL
)

--Tabela com as instalações de um planeta
CREATE TABLE colonization_planeta_instalacoes (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_planeta INT(6) NOT NULL,
id_instalacao INT(6) NOT NULL,
nivel INT(6) NOT NULL,
turno INT(6) NOT NULL,
turno_destroi INT(6) DEFAULT NULL,
turno_desmonta INT(6) DEFAULT NULL,
instalacao_inicial BOOLEAN DEFAULT FALSE
)

--Tabela populada quando uma Instalação de um planeta tem um upgrades
CREATE TABLE colonization_planeta_instalacoes_upgrade (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_planeta_instalacoes INT(6) NOT NULL,
nivel_anterior INT(6) NOT NULL,
id_instalacao_anterior INT(6) NOT NULL,
turno INT(6) NOT NULL
)

--Tabela com os recursos acumulados do Império (não locais)
CREATE TABLE IF NOT EXISTS colonization_imperio_recursos (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_imperio INT(6) NOT NULL,
id_recurso INT(6) NOT NULL,
qtd INT(6) NOT NULL,
turno INT(6) NOT NULL,
disponivel BOOLEAN DEFAULT NOT NULL
)

--Tabela com as Instalações não-Públicas que o Império pode construir
CREATE TABLE IF NOT EXISTS colonization_imperio_instalacoes (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_imperio INT(6) NOT NULL,
id_instalacao INT(6) NOT NULL
)

--Tabela com as Techs não-Públicas que o Império pode pesquisar
CREATE TABLE IF NOT EXISTS colonization_imperio_techs_permitidas (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_imperio INT(6) NOT NULL,
id_tech INT(6) NOT NULL
)


--Tabela com as Techs do Império
CREATE TABLE IF NOT EXISTS colonization_imperio_techs (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_imperio INT(6) NOT NULL,
id_tech INT(6) NOT NULL,
custo_pago INT(6) DEFAULT 0,
turno INT(6) NOT NULL,
tech_inicial BOOLEAN DEFAULT FALSE
)

--Tabela com as Techs transferidas
CREATE TABLE IF NOT EXISTS colonization_imperio_transfere_techs (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_imperio_origem INT(6) NOT NULL,
nome_npc VARCHAR(255) DEFAULT '',
id_imperio_destino INT(6) NOT NULL,
id_tech INT(6) NOT NULL,
autorizado BOOLEAN DEFAULT FALSE,
processado BOOLEAN DEFAULT FALSE,
turno INT(6) NOT NULL
)

--Tabela com Transferência de Recursos
CREATE TABLE IF NOT EXISTS colonization_imperio_transfere_recurso (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_imperio_origem INT(6) NOT NULL,
nome_npc VARCHAR(255) DEFAULT '',
id_imperio_destino INT(6) NOT NULL,
id_recurso INT(6) NOT NULL,
qtd INT(6) NOT NULL,
processado BOOLEAN DEFAULT FALSE,
turno INT(6) NOT NULL
)

--Tabela com as colonias do Império
CREATE TABLE colonization_imperio_colonias (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_imperio INT(6) NOT NULL,
nome_npc VARCHAR(255) DEFAULT '',
id_planeta INT(6) NOT NULL,
capital BOOLEAN DEFAULT FALSE,
vassalo BOOLEAN DEFAULT FALSE,
pop INT(6) NOT NULL,
pop_robotica INT(6) DEFAULT 0,
satisfacao INT(6) NOT NULL DEFAULT 100,
poluicao INT(6) NOT NULL,
turno INT(6) NOT NULL
)

--Tabela com a frota do Império, incluindo os dados de cada nave individualmente
CREATE TABLE colonization_imperio_frota (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_imperio INT(6) NOT NULL,
nome_npc VARCHAR(255) DEFAULT '',
nome VARCHAR(255) NOT NULL,
tipo VARCHAR(255) NOT NULL,
X INT(6) NOT NULL,
Y INT(6) NOT NULL,
Z INT(6) NOT NULL,
string_nave TEXT NOT NULL,
custo TEXT DEFAULT '',
tamanho INT(6) NOT NULL,
velocidade INT(6) NOT NULL,
alcance INT(6) NOT NULL,
PDF_laser INT(6) NOT NULL,
PDF_projetil INT(6) NOT NULL,
PDF_torpedo INT(6) NOT NULL,
blindagem INT(6) NOT NULL,
escudos INT(6) NOT NULL,
PDF_bombardeamento INT(6) NOT NULL,
poder_invasao INT(6) NOT NULL,
pesquisa BOOLEAN DEFAULT FALSE,
camuflagem INT(6) DEFAULT 0,
nivel_estacao_orbital INT(6) DEFAULT 0,
especiais TEXT DEFAULT NULL,
HP INT(6) NOT NULL,
qtd INT(6) NOT NULL,
turno INT(6) NOT NULL,
turno_destruido INT(6) DEFAULT 0,
id_estrela_destino INT(6) DEFAULT 0,
anti_dobra BOOLEAN DEFAULT FALSE,
visivel BOOLEAN DEFAULT FALSE
)

--Tabela com o histórico de Pesquisas das naves do Império
CREATE TABLE colonization_imperio_historico_pesquisa (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_imperio INT(6) NOT NULL,
id_estrela INT(6) NOT NULL,
sensores INT(6) DEFAULT 0,
turno INT(6) NOT NULL
)

--Tabela com o histórico de Pesquisas das naves do Império
CREATE TABLE colonization_frota_historico_movimentacao (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_nave INT(6) NOT NULL,
id_imperio INT(6) NOT NULL,
id_estrela_origem INT(6) NOT NULL,
id_estrela_destino INT(6) DEFAULT 0,
turno INT(6) NOT NULL
)

--Tabela com os pontos de reabastecimento do Império
CREATE TABLE colonization_imperio_abastecimento (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_imperio INT(6) NOT NULL,
id_estrela INT(6) NOT NULL
)

--Tabela com as estrelas visitadas por um Império
CREATE TABLE colonization_estrelas_historico (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_imperio INT(6) NOT NULL,
id_estrela INT(6) NOT NULL,
turno INT(6) NOT NULL
)

--Tabela com os dados do turno atual
CREATE TABLE colonization_turno_atual (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
data_turno TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
bloqueado BOOLEAN DEFAULT TRUE
)

--Tabela com as ações
CREATE TABLE colonization_acoes_turno (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_imperio INT(6) NOT NULL,
id_planeta INT(6) NOT NULL,
id_instalacao INT(6) NOT NULL,
id_planeta_instalacoes INT(6) NOT NULL,
pop INT(6) NOT NULL,
desativado BOOLEAN DEFAULT FALSE,
turno INT(6) NOT NULL,
data_modifica TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
)

--Tabela com os balanços do turno
CREATE TABLE colonization_balancos_turno (
id_imperio INT(6) NOT NULL,
json_balancos TEXT NOT NULL,
turno INT(6) NOT NULL
)

--Tabela com as listas das colônias
CREATE TABLE colonization_lista_colonias_turno (
id_imperio INT(6) NOT NULL,
json_balancos TEXT NOT NULL,
turno INT(6) NOT NULL
)

--Tabela com os contatos diplomáticos
CREATE TABLE colonization_diplomacia (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_imperio INT(6) NOT NULL,
id_imperio_contato INT(6) NOT NULL,
nome_npc VARCHAR(255) DEFAULT '',
acordo_comercial BOOLEAN DEFAULT FALSE,
turno INT(6) NOT NULL
)

--Tabela com as ações do Admin
CREATE TABLE IF NOT EXISTS colonization_acoes_admin (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_imperio INT(6) NOT NULL,
lista_recurso VARCHAR(255) NOT NULL,
qtd VARCHAR(255) NOT NULL,
descricao TEXT DEFAULT NULL,
turno INT(6) NOT NULL,
data_modifica TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
)

--Tabela com os dados das Missões
CREATE TABLE IF NOT EXISTS colonization_missao (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
descricao TEXT DEFAULT NULL,
texto_sucesso TEXT DEFAULT NULL,
texto_fracasso TEXT DEFAULT NULL,
lista_recurso VARCHAR(255) NOT NULL,
qtd VARCHAR(255) NOT NULL,
id_imperio INT(6) DEFAULT 0,
id_imperios_aceitaram VARCHAR(255) DEFAULT '',
id_imperios_rejeitaram VARCHAR(255) DEFAULT '',
turno INT(6) NOT NULL,
ativo BOOLEAN DEFAULT TRUE,
turno_validade INT(6) NOT NULL,
id_imperios_sucesso TEXT DEFAULT NULL,
sucesso BOOLEAN DEFAULT FALSE,
obrigatoria BOOLEAN DEFAULT FALSE
)

--Tabela com as referências do Fórum
CREATE TABLE IF NOT EXISTS colonization_referencia_forum (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
descricao TEXT DEFAULT '',
id_post INT(6) DEFAULT NULL,
page_id BOOLEAN DEFAULT FALSE,
deletavel BOOLEAN DEFAULT TRUE
)

--Tabela com os logs de alteração de recursos
CREATE TABLE IF NOT EXISTS colonization_log_recursos_imperio (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_imperio INT(6) DEFAULT 0,
id_recurso INT(6) NOT NULL,
qtd INT(6) NOT NULL,
turno INT(6) NOT NULL,
data_modifica TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
)

--Tabela com os modelos de naves
CREATE TABLE IF NOT EXISTS colonization_modelo_naves (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_imperio INT(6) DEFAULT 0,
nome_modelo VARCHAR(255) NOT NULL,
string_nave TEXT DEFAULT '',
texto_nave TEXT DEFAULT '',
texto_custo TEXT DEFAULT ''
)