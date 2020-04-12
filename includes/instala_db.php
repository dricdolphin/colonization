<?php
//****************************
//INSTALA_DB.PHP
//***************************
// Instalação do Banco de Dados do Colonization

//Classe "instala_db"
//Realiza a instalação do banco de dados
class instala_db {

	/***********************
	function __construct()
	----------------------
	Cria as tabelas do banco de dados
	***********************/
	function __construct() {
		global $wpdb; //Objeto WordPress de banco de dados
		
		//Tabela com os dados do Império
		$wpdb->query("CREATE TABLE IF NOT EXISTS colonization_imperio (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		nome VARCHAR(30) NOT NULL,
		id_jogador INT(30) NOT NULL,
		prestigio INT(30) DEFAULT 0
		)");

		//Tabela com os dados das estrelas
		$wpdb->query("CREATE TABLE IF NOT EXISTS colonization_estrela (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		nome VARCHAR(255) NOT NULL,
		X INT(6) NOT NULL,
		Y INT(6) NOT NULL,
		Z INT(6) NOT NULL,
		tipo VARCHAR(255) NOT NULL,
		UNIQUE KEY (X, Y, Z)
		)");

		//Tabela com os dados dos planetas e corpos celestes (luas e asteróides)
		//OBS: Caso o planeta tenha luas, elas serão definidas com CLASSE="lua" e seu atributo "posicao" será
		//o mesmo do planeta que orbitam. A subclasse define o tipo de biosfera que é capaz de sustentar
		$wpdb->query("CREATE TABLE IF NOT EXISTS colonization_planeta (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		id_estrela INT(6) NOT NULL,
		nome VARCHAR(255) NOT NULL,
		posicao INT(3) NOT NULL,
		classe VARCHAR(255) NOT NULL,
		subclasse VARCHAR(255) DEFAULT NULL,
		tamanho INT(2) NOT NULL,
		inospito BOOLEAN DEFAULT TRUE
		)");

		//Tabela com os tipos de instalações
		$wpdb->query("CREATE TABLE IF NOT EXISTS colonization_instalacao (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		nome VARCHAR(255) NOT NULL,
		descricao TEXT NOT NULL,
		slots INT(6) DEFAULT 1,
		autonoma BOOLEAN DEFAULT FALSE,
		desguarnecida BOOLEAN DEFAULT FALSE
		)");

		//Tabela com os tipos de recursos existentes
		$wpdb->query("CREATE TABLE IF NOT EXISTS colonization_recurso (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		nome VARCHAR(255) NOT NULL,
		descricao TEXT NOT NULL,
		acumulavel BOOLEAN DEFAULT TRUE,
		extrativo BOOLEAN DEFAULT TRUE,
		local BOOLEAN DEFAULT FALSE
		)");

		//Tabela com as Techs existentes
		$wpdb->query("CREATE TABLE IF NOT EXISTS colonization_tech (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		nome VARCHAR(255) NOT NULL,
		descricao TEXT NOT NULL,
		nivel INT(6) NOT NULL,
		custo INT(6) NOT NULL,
		id_tech_parent INT(6),
		belica BOOLEAN DEFAULT FALSE
		)");


		//Tabela com os recursos consumidos ou produzidos por uma instalação
		$wpdb->query("CREATE TABLE IF NOT EXISTS colonization_instalacao_recursos (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		id_instalacao INT(6) NOT NULL,
		id_recurso INT(6) NOT NULL,
		qtd_por_nivel INT(6) NOT NULL,
		consome BOOLEAN DEFAULT TRUE
		)");

		//Tabela com os recursos disponíveis do planeta. Refere-se a recursos que podem ser explorados
		$wpdb->query("CREATE TABLE IF NOT EXISTS colonization_planeta_recursos (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		id_planeta INT(6) NOT NULL,
		id_recurso INT(6) NOT NULL,
		disponivel INT(6) NOT NULL,
		turno INT(6) NOT NULL
		)");

		//Tabela com as instalações de um planeta
		$wpdb->query("CREATE TABLE IF NOT EXISTS colonization_planeta_instalacoes (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		id_planeta INT(6) NOT NULL,
		id_instalacao INT(6) NOT NULL,
		nivel INT(6) NOT NULL,
		turno INT(6) NOT NULL,
		turno_destroi INT(6) DEFAULT NULL
		)");

		$wpdb->query("CREATE TABLE colonization_planeta_instalacoes_upgrade (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		id_planeta_instalacoes INT(6) NOT NULL,
		nivel_anterior INT(6) NOT NULL,
		turno INT(6) NOT NULL
		)");


		//Tabela com os recursos acumulados do Império (não locais)
		$wpdb->query("CREATE TABLE IF NOT EXISTS colonization_imperio_recursos (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		id_imperio INT(6) NOT NULL,
		id_recurso INT(6) NOT NULL,
		qtd INT(6) NOT NULL,
		turno INT(6) NOT NULL,
		disponivel BOOLEAN DEFAULT NOT NULL
		)");

		//Tabela com as Techs dos Impérios
		$wpdb->query("CREATE TABLE IF NOT EXISTS colonization_imperio_techs (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		id_imperio INT(6) NOT NULL,
		id_tech INT(6) NOT NULL,
		custo_pago INT(6) DEFAULT 0,
		turno INT(6) NOT NULL
		)");
		

		//Tabela com as colonias do Império
		$wpdb->query("CREATE TABLE IF NOT EXISTS colonization_imperio_colonias (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		id_imperio INT(6) NOT NULL,
		id_planeta INT(6) NOT NULL,
		pop INT(6) NOT NULL,
		poluicao INT(6) NOT NULL,
		turno INT(6) NOT NULL
		)");

		//Tabela com a frota do Império, incluindo os dados de cada nave individualmente
		$wpdb->query("CREATE TABLE IF NOT EXISTS colonization_imperio_frota (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		id_imperio INT(6) NOT NULL,
		nome VARCHAR(255) NOT NULL,
		tipo VARCHAR(255) NOT NULL,
		X INT(6) NOT NULL,
		Y INT(6) NOT NULL,
		Z INT(6) NOT NULL,
		tamanho INT(6) NOT NULL,
		velocidade INT(6) NOT NULL,
		alcance INT(6) NOT NULL,
		PDF_laser INT(6) NOT NULL,
		PDF_projetil INT(6) NOT NULL,
		PDF_torpedo INT(6) NOT NULL,
		blindagem INT(6) NOT NULL,
		escudos INT(6) NOT NULL,
		poder_invasao INT(6) NOT NULL,
		especiais TEXT DEFAULT NULL,
		HP INT(6) NOT NULL,
		qtd INT(6) NOT NULL,
		turno INT(6) NOT NULL
		)");

		//Tabela com os dados do turno atual
		$wpdb->query("CREATE TABLE IF NOT EXISTS colonization_turno_atual (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		data_turno TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
		bloqueado BOOLEAN DEFAULT TRUE
		)");

		//Tabela com as ações
		$wpdb->query("CREATE TABLE IF NOT EXISTS colonization_acoes_turno (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		id_imperio INT(6) NOT NULL,
		id_planeta INT(6) NOT NULL,
		id_instalacao INT(6) NOT NULL,
		id_planeta_instalacoes INT(6) NOT NULL,
		pop INT(6) NOT NULL,
		turno INT(6) NOT NULL,
		data_modifica TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
		)");
		
		//Tabela com os dados do turno atual
		$wpdb->query("CREATE TABLE IF NOT EXISTS colonization_acoes_admin (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		id_imperio INT(6) NOT NULL,
		lista_recurso VARCHAR(255) NOT NULL,
		qtd VARCHAR(255) NOT NULL,
		descricao TEXT DEFAULT NULL,
		turno INT(6) NOT NULL,
		data_modifica TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
		)");
	
		//Cria os "triggers"
		$wpdb->query("DELIMITER $$
		CREATE TRIGGER deleta_imperio
		AFTER DELETE
		ON colonization_imperio FOR EACH ROW
		BEGIN
		DELETE FROM colonization_imperio_recursos WHERE id_imperio = old.id;
		DELETE FROM colonization_imperio_colonias WHERE id_imperio = old.id;
		DELETE FROM colonization_imperio_frota WHERE id_imperio = old.id;
		DELETE FROM colonization_acoes_turno WHERE id_imperio = old.id;
		DELETE FROM colonization_imperio_techs WHERE id_imperio = old.id;
		END$$
		DELIMITER ;");
		
		$wpdb->query("DELIMITER $$
		CREATE TRIGGER deleta_planeta
		AFTER DELETE
		ON colonization_planeta FOR EACH ROW
		BEGIN
		DELETE FROM colonization_planeta_recursos WHERE id_planeta = old.id;
		DELETE FROM colonization_planeta_instalacoes WHERE id_planeta = old.id;
		DELETE FROM colonization_imperio_colonias WHERE id_planeta = old.id;
		DELETE FROM colonization_acoes_turno WHERE id_planeta = old.id;
		END$$
		DELIMITER ;");
	
		$wpdb->query("DELIMITER $$
		CREATE TRIGGER deleta_estrela
		AFTER DELETE
		ON colonization_estrela FOR EACH ROW
		BEGIN
		DELETE FROM colonization_planeta WHERE id_estrela = old.id;
		END$$
		DELIMITER ;");
		
		$wpdb->query("DELIMITER $$
		CREATE TRIGGER deleta_recurso
		AFTER DELETE
		ON colonization_recurso FOR EACH ROW
		BEGIN
		DELETE FROM colonization_instalacao_recursos WHERE id_recurso = old.id;
		DELETE FROM colonization_planeta_recursos WHERE id_recurso = old.id;
		DELETE FROM colonization_imperio_recursos WHERE id_recurso = old.id;
		END$$
		DELIMITER ;");
		
		$wpdb->query("		DELIMITER $$
		CREATE TRIGGER deleta_instalacao
		AFTER DELETE
		ON colonization_instalacao FOR EACH ROW
		BEGIN
		DELETE FROM colonization_instalacao_recursos WHERE id_instalacao = old.id;
		DELETE FROM colonization_planeta_instalacoes WHERE id_instalacao = old.id;
		DELETE FROM colonization_acoes_turno WHERE id_instalacao = old.id;
		END$$
		DELIMITER ;");

		$wpdb->query("		DELIMITER $$
		CREATE TRIGGER deleta_planeta_instalacao
		AFTER DELETE
		ON colonization_planeta_instalacoes FOR EACH ROW
		BEGIN
		DELETE FROM colonization_planeta_instalacoes_upgrade WHERE id_planeta_instalacoes = old.id;
		END$$
		DELIMITER ;");		

		$wpdb->query("		DELIMITER $$
		CREATE TRIGGER deleta_tech
		AFTER DELETE
		ON colonization_tech FOR EACH ROW
		BEGIN
		DELETE FROM colonization_imperio_techs WHERE id_tech = old.id;
		END$$
		DELIMITER ;");
	}
}
?>