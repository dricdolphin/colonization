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
X INT(6) NOT NULL,
Y INT(6) NOT NULL,
Z INT(6) NOT NULL,
tipo VARCHAR(255) NOT NULL,
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
slots INT(6) DEFAULT 1,
autonoma BOOLEAN DEFAULT FALSE,
desguarnecida BOOLEAN DEFAULT FALSE,
oculta BOOLEAN DEFAULT FALSE,
icone VARCHAR(255) DEFAULT '',
especiais VARCHAR(255) DEFAULT ''
)

--Tabela com os tipos de recursos existentes
CREATE TABLE colonization_recurso (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nome VARCHAR(255) NOT NULL,
descricao TEXT NOT NULL,
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
turno_destroi INT(6) DEFAULT NULL
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

--Tabela com as Techs do Império
CREATE TABLE IF NOT EXISTS colonization_imperio_techs (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_imperio INT(6) NOT NULL,
id_tech INT(6) NOT NULL,
custo_pago INT(6) DEFAULT 0,
turno INT(6) NOT NULL
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

--Tabela com as colonias do Império
CREATE TABLE colonization_imperio_colonias (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_imperio INT(6) NOT NULL,
id_planeta INT(6) NOT NULL,
pop INT(6) NOT NULL,
poluicao INT(6) NOT NULL,
turno INT(6) NOT NULL
)

--Tabela com a frota do Império, incluindo os dados de cada nave individualmente
CREATE TABLE colonization_imperio_frota (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_imperio INT(6) NOT NULL,
nome VARCHAR(255) NOT NULL,
tipo VARCHAR(255) NOT NULL,
X INT(6) NOT NULL,
Y INT(6) NOT NULL,
Z INT(6) NOT NULL,
string_nave TEXT NOT NULL,
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
nivel_estacao_orbital INT(6) DEFAULT 0,
especiais TEXT DEFAULT NULL,
HP INT(6) NOT NULL,
qtd INT(6) NOT NULL,
turno INT(6) NOT NULL
)

--Tabela com o histórico de Pesquisas das naves do Império
CREATE TABLE colonization_imperio_historico_pesquisa (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_imperio INT(6) NOT NULL,
id_estrela INT(6) NOT NULL,
turno INT(6) NOT NULL
)

--Tabela com os pontos de reabastecimento do Império
CREATE TABLE colonization_imperio_abastecimento (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_imperio INT(6) NOT NULL,
id_estrela INT(6) NOT NULL
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
turno INT(6) NOT NULL,
data_modifica TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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