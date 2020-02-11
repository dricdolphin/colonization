----------------------
--ESTRUTURA.SQL
----------------------
-- Definições das tabelas do Colonization

--Tabela com os dados do Império
CREATE TABLE imperio (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nome VARCHAR(30) NOT NULL,
id_jogador INT(30) NOT NULL
)

--Tabela com os dados das estrelas
CREATE TABLE estrela {
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nome VARCHAR(255) NOT NULL,
X INT(6) NOT NULL,
Y INT(6) NOT NULL,
Z INT(6) NOT NULL,
tipo VARCHAR(255) NOT NULL
}

--Tabela com os dados dos planetas e corpos celestes (luas e asteróides)
--OBS: Caso o planeta tenha luas, elas serão definidas com CLASSE="lua" e seu atributo "posicao" será
--o mesmo do planeta que orbitam. A subclasse define o tipo de biosfera que é capaz de sustentar
CREATE TABLE planeta {
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_estrela INT(6) NOT NULL,
nome VARCHAR(255) NOT NULL,
posicao INT(3) NOT NULL,
classe VARCHAR(255) NOT NULL,
subclasse VARCHAR(255) DEFAULT NULL,
tamanho INT(2) NOT NULL
}

--Tabela com os tipos de instalações
CREATE TABLE instalacao {
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nome VARCHAR(255) NOT NULL,
descricacao TEXT NOT NULL
}

--Tabela com os tipos de recursos existentes
CREATE TABLE recurso {
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nome VARCHAR(255) NOT NULL,
descricao TEXT NOT NULL
}


--Tabela com os recursos produzidos por uma instalação
CREATE TABLE instalacao_produz_recursos {
id_instalacao INT(6) NOT NULL,
id_recurso INT(6) NOT NULL,
qtd_por_nivel INT(6) NOT NULL
}

--Tabela com os recursos consumidos por uma instalação
CREATE TABLE instalacao_consome_recursos {
id_instalacao INT(6) NOT NULL,
id_recurso INT(6) NOT NULL,
qtd_por_nivel INT(6) NOT NULL
}

--Tabela com os recursos disponíveis do planeta. Refere-se a recursos que podem ser explorados
CREATE TABLE planeta_recursos {
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_recurso INT(6) NOT NULL,
disponivel INT(6) NOT NULL,
turno INT(6)
}

--Tabela com as instalações de um planeta
CREATE TABLE planeta_instalacoes {
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
id_planeta INT(6) NOT NULL,
id_instalacao INT(6) NOT NULL,
nivel INT(6) NOT NULL,
turno INT(6) NOT NULL
}

--Tabela com os recursos acumulados do Império (não locais)
CREATE TABLE imperio_recursos {
id_imperio INT(6) NOT NULL,
id_recurso INT(6) NOT NULL,
qtd INT(6) NOT NULL,
turno INT(6)
}

--Tabela com as colonias do Império
CREATE TABLE imperio_colonias {
id_imperio INT(6) NOT NULL,
id_planeta INT(6) NOT NULL,
pop INT(6) NOT NULL,
poluicao INT(6) NOT NULL,
turno INT(6)
}

--Tabela com a frota do Império, incluindo os dados de cada nave individualmente
CREATE TABLE imperio_frota {
id_imperio INT(6) NOT NULL,
nome VARCHAR(255) NOT NULL,
tipo VARCHAR(255) NOT NULL,
X INT(6) NOT NULL,
Y INT(6) NOT NULL,
Z INT(6) NOT NULL,
tamanho INT(6) NOT NULL,
velocidade INT(6) NOT NULL,
PDF_laser INT(6) NOT NULL,
PDF_projetil INT(6) NOT NULL,
PDF_torpedo INT(6) NOT NULL,
blindagem INT(6) NOT NULL,
escudos INT(6) NOT NULL,
poder_invasao INT(6) NOT NULL,
especiais TEXT DEFAULT NULL,
HP INT(6) NOT NULL,
qtd INT(6) NOT NULL,
turno INT(6)
}

--Tabela com os dados do turno atual
CREATE TABLE turno_atual {
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
data_turno TIMESTAMP
}