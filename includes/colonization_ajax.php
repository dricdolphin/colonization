<?php 
/**************************
COLONIZATION_AJAX.PHP
----------------
Lida com os "request" de Ajax. 
Utilizado para operar o banco de dados (normalmente queries para salvar dados)
***************************/

class colonization_ajax {
	
	function __construct() {
		global $wpdb, $debug, $start_time; 

		$start_time = hrtime(true);
		$script_time = time();
		$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
		$diferenca_server = round($script_time - $_SERVER['REQUEST_TIME']);
		$debug = "AJAX __construct() {$diferenca}ms \n => SERVER Request Time: {$diferenca_server}\n";
		
		//Adiciona as funções que serão utilizadas
		add_action('wp_ajax_salva_objeto', array ($this, 'salva_objeto'));
		add_action('wp_ajax_salva_acao', array ($this, 'salva_acao'));
		add_action('wp_ajax_deleta_objeto', array ($this, 'deleta_objeto'));
		add_action('wp_ajax_valida_estrela', array ($this, 'valida_estrela'));
		add_action('wp_ajax_valida_colonia', array ($this, 'valida_colonia'));
		add_action('wp_ajax_valida_instalacao_recurso', array ($this, 'valida_instalacao_recurso'));
		add_action('wp_ajax_valida_planeta_recurso', array ($this, 'valida_planeta_recurso'));
		add_action('wp_ajax_altera_recursos_planeta', array ($this, 'altera_recursos_planeta'));//altera_recursos_planeta
		add_action('wp_ajax_valida_colonia_instalacao', array ($this, 'valida_colonia_instalacao'));
		add_action('wp_ajax_destruir_instalacao', array ($this, 'destruir_instalacao'));
		add_action('wp_ajax_desmonta_instalacao', array ($this, 'desmonta_instalacao'));
		add_action('wp_ajax_dados_imperio', array ($this, 'dados_imperio'));
		add_action('wp_ajax_produtos_acao', array ($this, 'produtos_acao_ajax'));
		add_action('wp_ajax_valida_acao', array ($this, 'valida_acao'));
		add_action('wp_ajax_roda_turno', array ($this, 'roda_turno'));
		add_action('wp_ajax_libera_turno', array ($this, 'libera_turno'));
		add_action('wp_ajax_encerra_turno', array ($this, 'encerra_turno'));
		add_action('wp_ajax_valida_acao_admin', array ($this, 'valida_acao_admin'));
		add_action('wp_ajax_valida_reabastecimento', array ($this, 'valida_reabastecimento'));
		add_action('wp_ajax_valida_tech_imperio', array ($this, 'valida_tech_imperio'));
		add_action('wp_ajax_valida_transfere_tech', array ($this, 'valida_transfere_tech'));//valida_transfere_tech
		add_action('wp_ajax_valida_transfere_recurso', array ($this, 'valida_transfere_recurso'));//valida_transfere_recurso
		add_action('wp_ajax_dados_transfere_tech', array ($this, 'dados_transfere_tech'));//dados_transfere_tech
		add_action('wp_ajax_processa_recebimento_tech', array ($this, 'processa_recebimento_tech'));//salva_transfere_tech
		add_action('wp_ajax_processa_recebimento_recurso', array ($this, 'processa_recebimento_recurso'));
		add_action('wp_ajax_processa_viagem_nave', array ($this, 'processa_viagem_nave'));//processa_viagem_nave
		add_action('wp_ajax_envia_nave', array ($this, 'envia_nave'));//envia_nave
		add_action('wp_ajax_nave_visivel', array ($this, 'nave_visivel'));//nave_visivel
		add_action('wp_ajax_aceita_missao', array ($this, 'aceita_missao'));//aceita_missao
		add_action('wp_ajax_transfere_pop', array ($this, 'transfere_pop'));//transfere_pop
		add_action('wp_ajax_lista_estrelas', array ($this, 'lista_estrelas'));//Retorna com todas as estrelas disponíveis em formato JSON
		add_action('wp_ajax_ids_recursos_extrativos', array ($this, 'ids_recursos_extrativos'));//Retorna com todos os recursos extrativos formatados em JSON
		add_action('wp_ajax_lista_instalacoes_imperio', array ($this, 'lista_instalacoes_imperio'));//Retorna uma lista com todas as Instalações que o Império pode construir
		add_action('wp_ajax_lista_techs_imperio', array ($this, 'lista_techs_imperio'));//Retorna uma lista com todas as Techs que o Império pode pesquisar
		add_action('wp_ajax_recursos_atuais_imperio', array ($this, 'recursos_atuais_imperio'));//Retorna o HTML com os recursos atuais do Império
		add_action('wp_ajax_salva_diplomacia', array ($this, 'salva_diplomacia')); //Valida o evento de diplomacia
		add_action('wp_ajax_valida_nave', array ($this, 'valida_nave')); //Valida os dados de uma nave
		add_action('wp_ajax_valida_upgrade_nave', array ($this, 'valida_upgrade_nave')); //Valida um upgrade de nave
		add_action('wp_ajax_muda_nome_colonia', array ($this, 'muda_nome_colonia')); //Muda o nome de um planeta
		add_action('wp_ajax_muda_nome_nave', array ($this, 'muda_nome_nave')); //Muda o nome de uma nave
		add_action('wp_ajax_tirar_cerco', array ($this, 'tirar_cerco'));//Tira o status de Cerco de uma colônia
		add_action('wp_ajax_lista_techs_ocultas', array ($this, 'lista_techs_ocultas'));//Tira o status de Cerco de uma colônia
		add_action('wp_ajax_deleta_modelo_nave', array ($this, 'deleta_modelo_nave'));//Deleta uma nave MODELO
		add_action('wp_ajax_valida_modelo_nave', array ($this, 'valida_modelo_nave'));//Valida um modelo de nave (apenas os dados básicos)
		add_action('wp_ajax_carrega_nave', array ($this, 'carrega_nave'));//Puxa os dados de uma nave
		add_action('wp_ajax_coloniza_planeta', array ($this, 'coloniza_planeta'));//coloniza_planeta
		add_action('wp_ajax_repara_nave', array ($this, 'repara_nave'));//repara_nave
		add_action('wp_ajax_remove_aviso', array ($this, 'remove_aviso'));//remove_aviso
		add_action('wp_ajax_ativa_anti_dobra', array ($this, 'ativa_anti_dobra'));//ativa_anti_dobra
		add_action('wp_ajax_criar_pop', array ($this, 'criar_pop'));//criar_pop
		add_action('wp_ajax_usar_fumie', array ($this, 'usar_fumie'));//usar_fumie
	}

	/***********************
	function usar_fumie()
	----------------------
	Transforma 1 Fumiê em 100 Alimentos
	***********************/
	function usar_fumie() {
		global $wpdb;

		$imperio = new imperio($_POST['id_imperio']);
		if ($imperio->id != $_POST['id_imperio'] && $roles != "administrator") {
			$imperio = new imperio($_POST['id_imperio'],true);
			$dados_salvos['resposta_ajax'] = "Somente o Jogador do Império '{$imperio->nome}' pode realizar essa ação!";
			
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta	
		}
		
		$id_fumie = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='Fumiê'");
		$id_alimentos = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='Alimentos'");
		if ($imperio->pega_qtd_recurso_imperio($id_fumie) < $custo) {
			$dados_salvos['resposta_ajax'] = "O custo para efetuar essa ação é de 1 Fumiê mas o Império não tem os Recursos suficientes!";
			
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta							
		}

		$wpdb->query("UPDATE colonization_imperio_recursos SET qtd=qtd-1 WHERE id_recurso={$id_fumie} AND id_imperio={$imperio->id} AND turno={$imperio->turno->turno}");
		$dados_ajax['debug'] .= "UPDATE colonization_imperio_recursos SET qtd=qtd-1 WHERE id_recurso={$id_fumie} AND id_imperio={$imperio->id} AND turno={$imperio->turno->turno} \n";
		$wpdb->query("UPDATE colonization_imperio_recursos SET qtd=qtd+100 WHERE id_recurso={$id_alimentos} AND id_imperio={$imperio->id} AND turno={$imperio->turno->turno}");
		$dados_ajax['debug'] .= "UPDATE colonization_imperio_recursos SET qtd=qtd+100 WHERE id_recurso={$id_alimentos} AND id_imperio={$imperio->id} AND turno={$imperio->turno->turno} \n";

		$dados_salvos['resposta_ajax'] = "OK!";
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta						
	}

	/***********************
	function criar_pop()
	----------------------
	Cria Pop em uma colônia
	***********************/
	function criar_pop() {
		global $wpdb;

		$colonia = new colonia($_POST['id_colonia']);
		$imperio = new imperio($colonia->id_imperio);
		if ($imperio->id != $colonia->id_imperio && $roles != "administrator") {
			$imperio = new imperio($colonia->id_imperio,true);
			$dados_salvos['resposta_ajax'] = "Somente o Jogador do Império '{$imperio->nome}' pode realizar essa ação!";
			
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta	
		}
		
		$tipo_recurso = "Industrializáveis";
		$custo = $_POST['qtd']*10;
		if ($_POST['tipo_pop'] == "pop") {
			$tipo_recurso = "Alimentos";
			$custo = $_POST['qtd']*100;
		}
		
		$id_recurso = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='{$tipo_recurso}'");
		if ($imperio->pega_qtd_recurso_imperio($id_recurso) < $custo) {
			$dados_salvos['resposta_ajax'] = "O custo para efetuar essa ação é de {$custo} {$tipo_recurso} mas o Império não tem os Recursos suficientes!";
			
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta							
		}

		if ($_POST['tipo_pop'] != "pop") {
			$sem_balanco = true;
			$acoes = new acoes($imperio->id,$turno->turno,$sem_balanco);
			if (empty($acoes->recursos_balanco)) {
				$acoes->pega_balanco_recursos();
			}

			$id_energia = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome = 'Energia'");
			if ($_POST['qtd'] > $acoes->recursos_balanco[$id_energia]) {
				$dados_salvos['resposta_ajax'] = "Não é possível realizar esta ação! Esses Droids irão consumir {$_POST['qtd']} Energia mas o Balanço do Recurso não é suficiente!";					
				
				echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
				wp_die(); //Termina o script e envia a resposta										
			}
		}

		$wpdb->query("UPDATE colonization_imperio_recursos SET qtd=qtd-{$custo} WHERE id_recurso={$id_recurso} AND id_imperio={$imperio->id} AND turno={$imperio->turno->turno}");
		$dados_ajax['debug'] .= "UPDATE colonization_imperio_recursos SET qtd=qtd-{$custo} WHERE id_recurso={$id_recurso} AND id_imperio={$imperio->id} AND turno={$imperio->turno->turno} \n";
		if ($_POST['tipo_pop'] == "pop") {
			$wpdb->query("UPDATE colonization_imperio_colonias SET pop = pop+{$_POST['qtd']} WHERE id={$_POST['id_colonia']}");
			$dados_ajax['debug'] .= "UPDATE colonization_imperio_colonias SET pop = pop+{$_POST['qtd']} WHERE id={$_POST['id_colonia']}\n";				
		} else {
			$wpdb->query("UPDATE colonization_imperio_colonias SET pop_robotica = pop_robotica+{$_POST['qtd']} WHERE id={$_POST['id_colonia']}");
			$dados_ajax['debug'] .= "UPDATE colonization_imperio_colonias SET pop_robotica = pop_robotica+{$_POST['qtd']} WHERE id={$_POST['id_colonia']}\n";				
		}
		
		$dados_salvos['resposta_ajax'] = "OK!";
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta						
	}

	/***********************
	function ativa_anti_dobra()
	----------------------
	Ativa o sistema anti-dobra
	***********************/
	function ativa_anti_dobra() {
		global $wpdb;
		
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		$estrela = new estrela($_POST['id_estrela']);
		$slots = 0;
		if ($_POST['id_nave'] != 0) {
			$nave = new frota($_POST['id_nave']);
			$imperio = new imperio($nave->id_imperio);
			$slots = $nave->tamanho;
		}
		
		if ($slots == 0) {
			$wpdb->query("UPDATE colonization_imperio_frota SET anti_dobra=true WHERE X={$estrela->X} AND Y={$estrela->Y} AND Z={$estrela->Z}");
			$wpdb->query("UPDATE colonization_estrela SET anti_dobra=true WHERE id={$_POST['id_estrela']}");
		} else if ($nave->anti_dobra && $_POST['desativa'] == true) {//É pra DESATIVAR o anti-dobra DA NAVE
			$wpdb->query("UPDATE colonization_imperio_frota SET anti_dobra=false WHERE id={$nave->id}");
		} else {//Ativa o anti-dobra em todas as naves do sistema que forem iguais ou menores que a nave (inclusive a própria nave)
			$wpdb->query("UPDATE colonization_imperio_frota SET anti_dobra=true WHERE X={$estrela->X} AND Y={$estrela->Y} AND Z={$estrela->Z} AND tamanho <= {$slots}");
		}
		
		$dados_salvos['resposta_ajax'] = "OK!";
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta	
	}

	/***********************
	function remove_aviso()
	----------------------
	Remove um aviso
	***********************/
	function remove_aviso() {
		global $wpdb;
		// Report all PHP errors
		$wpdb->hide_errors();
		//error_reporting(E_ALL); 
		//ini_set("display_errors", 1);
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		$id_imperio = $wpdb->get_var("SELECT id_imperio FROM colonization_visita_nave WHERE id={$_POST['id']}");
		$imperio = new imperio($id_imperio);
		$dados_salvos['resposta_ajax'] = "OK!";
		if ($imperio->id != $id_imperio && $roles != "administrator") {
			$imperio = new imperio($id_imperio,true);
			$dados_salvos['resposta_ajax'] = "Somente o Jogador do Império '{$imperio->nome}' pode remover seus avisos!";
			
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta	
		}
		
		$wpdb->query("UPDATE colonization_visita_nave SET processado=true WHERE id={$_POST['id']}");
		
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta	
	}


	/***********************
	function repara_nave()
	----------------------
	Repara uma nave
	***********************/
	function repara_nave() {
		global $wpdb;
		// Report all PHP errors
		$wpdb->hide_errors();
		//error_reporting(E_ALL); 
		//ini_set("display_errors", 1);
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		$nave = new frota($_POST['id']);
		$imperio = new imperio($nave->id_imperio);
		$dados_ajax['debug'] = "";
		
		$dados_salvos['resposta_ajax'] = "OK!";
		if ($imperio->id != $nave->id_imperio && $roles != "administrator") {
			$imperio = new imperio($nave->id_imperio,true);
			$dados_salvos['resposta_ajax'] = "Somente o Jogador do Império '{$imperio->nome}' pode reparar suas naves!";
			
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta			
		}
		
		//Verifica se é uma colônia do Jogador (ou Ponto de Reabastecimento)
		//$estrela = new estrela ($nave->id_estrela);
		$ponto_reabastece = $wpdb->get_var("SELECT id FROM colonization_imperio_abastecimento WHERE id_imperio={$nave->id_imperio} AND id_estrela={$nave->id_estrela}");
		$estrela_colonia = $wpdb->get_var("SELECT ce.id 
		FROM colonization_estrela AS ce 
		JOIN colonization_planeta AS cp
		ON ce.id = cp.id_estrela
		JOIN colonization_imperio_colonias AS cic
		ON cic.id_planeta = cp.id
		WHERE cic.id_imperio={$nave->id_imperio} AND ce.id={$nave->id_estrela} AND cic.turno={$imperio->turno->turno}");
		
		if (empty($ponto_reabastece) && empty($estrela_colonia)) {
			$dados_salvos['resposta_ajax'] = "Só é possível reparar sua nave num sistema que tenha um Espaçoporto amigável!";
			
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta				
		}

		$sob_cerco = $wpdb->get_var("SELECT ce.cerco FROM colonization_estrela AS ce
		WHERE ce.id={$nave->id_estrela}");
		
		if ($sob_cerco == 1) {
			$dados_salvos['resposta_ajax'] = "Não é possível reparar naves em um sistema sob ataque!";
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta															
		}
		

		$id_industrializaveis = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='Industrializáveis'");
		if ($nave->HP_max > $nave->HP) {
			$qtd_html = "Industrializáveis";
			$custo_reparo = intval(($nave->HP_max - $nave->HP)/10) + 1;
			if ($custo_reparo == 1) {
				$qtd_html = "Industrializável";
			}
			if ($imperio->pega_qtd_recurso_imperio($id_industrializaveis) < $custo_reparo) {
				$dados_salvos['resposta_ajax'] = "O custo para reparar essa nave é de {$custo_reparo}{$qtd_html} mas o Império não tem os Recursos suficientes!";
				
				echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
				wp_die(); //Termina o script e envia a resposta							
			}
			$wpdb->query("UPDATE colonization_imperio_recursos SET qtd = qtd - {$custo_reparo} WHERE id_recurso={$id_industrializaveis} AND id_imperio={$imperio->id} AND turno={$imperio->turno->turno}");
			//$wpdb->query("UPDATE colonization_log_recursos_imperio SET comentario='ATUALIZADO POR REPARA_NAVE' WHERE id_imperio={$imperio->id} AND id=(SELECT MAX(id) FROM colonization_log_recursos_imperio WHERE id_imperio={$imperio->id})");
			$dados_ajax['debug'] .= "UPDATE colonization_imperio_recursos SET qtd = qtd - {$custo_reparo} WHERE id_recurso={$id_industrializaveis} AND id_imperio={$imperio->id} AND turno={$imperio->turno->turno}\n";
			$wpdb->query("UPDATE colonization_imperio_frota SET HP = {$nave->HP_max} WHERE id={$nave->id}");
			$dados_ajax['debug'] .= "UPDATE colonization_imperio_frota SET HP = {$nave->HP_max} WHERE id={$nave->id}\n";
		}

		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta				
	}


	/***********************
	function coloniza_planeta()
	----------------------
	Coloniza um Planeta e cria uma Base Colonial no mesmo
	***********************/
	function coloniza_planeta() {
		global $wpdb;
		// Report all PHP errors
		$wpdb->hide_errors();
		//error_reporting(E_ALL); 
		//ini_set("display_errors", 1);
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		$dados_salvos['resposta_ajax'] = "OK!";
		$planeta = new planeta($_POST['id_planeta']);
		$estrela = new estrela($planeta->id_estrela);
		$imperio = new imperio($_POST['id_imperio']);
		$turno = new turno();
		$id_base_colonial = $wpdb->get_var("SELECT id FROM colonization_instalacao WHERE nome LIKE 'Base Colonial%'");
		
		if ($imperio->id != $_POST['id_imperio'] && $roles != "administrator") {
			$imperio = new imperio($_POST['id_imperio'],true);
			$dados_salvos['resposta_ajax'] = "Somente o Jogador do Império '{$imperio->nome}' pode colonizar um planeta nesse sistema estelar!";
			
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta			
		}
		
		$estrela_capital = new estrela($imperio->id_estrela_capital);
		if ($estrela->cerco) {
			$dados_salvos['resposta_ajax'] = "O sistema está sitiado e não pode receber alterações nesse momento.";
		
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta
		}
	
		//Valida a colônia
		$_POST['id'] = "";
		$_POST['where_clause'] = "id";
		$_POST['where_value'] = "";
		$_POST['tabela'] = "colonization_imperio_colonias";		
		$_POST['capital'] = 0;
		$_POST['vassalo'] = 0;
		$_POST['pop'] = 0;
		$_POST['pop_robotica'] = 0;
		$_POST['poluicao'] = 0;
		$_POST['satisfacao'] = 100;
		$_POST['saude'] = 100;
		$_POST['seguranca'] = 100;
		$_POST['turno'] = $turno->turno;
		$dados_salvos['resposta_ajax'] = $this->valida_colonia(false);
		
		if ($dados_salvos['resposta_ajax'] == "OK!") {//Validou, agora verifica se a Colônia tem uma Base Colonial. 
			$base_colonial = $wpdb->get_var("
			SELECT DISTINCT cpi.id_planeta 
			FROM colonization_planeta_instalacoes AS cpi
			WHERE cpi.id_planeta = {$_POST['id_planeta']} AND cpi.id_instalacao = {$id_base_colonial}
			");

			$dados_salvos = $this->salva_objeto(false);
		}
		
		if ($dados_salvos['resposta_ajax'] == "SALVO!") {
			//Se chegamos até aqui, pode criar a Base Colonial na nova Colônia
			$id_colonia = $wpdb->get_var("
			SELECT cic.id
			FROM colonization_imperio_colonias AS cic
			WHERE cic.id_planeta = {$_POST['id_planeta']} AND cic.turno = {$_POST['turno']} AND cic.id_imperio = {$_POST['id_imperio']}
			");

			unset($_POST['capital']);
			unset($_POST['vassalo']);
			unset($_POST['pop']);
			unset($_POST['pop_robotica']);
			unset($_POST['poluicao']);
			unset($_POST['satisfacao']);
			unset($_POST['saude']);
			unset($_POST['seguranca']);

			$_POST['nivel'] = 1;
			$_POST['instalacao_inicial'] = 0;
			$_POST['turno_desmonta'] = 0;
			$_POST['turno_destroi'] = 0;
			$_POST['id'] = "";
			$_POST['id_instalacao'] = $id_base_colonial;
			$_POST['where_clause'] = "id";
			$_POST['where_value'] = "";
			$_POST['tabela'] = "colonization_planeta_instalacoes";
			
			$dados_salvos = $this->valida_colonia_instalacao(false);
			if ($dados_salvos['resposta_ajax'] == "OK!") {
				unset($_POST['id_imperio']);
				$dados_salvos = $this->salva_objeto();
			} else {
				//Remove a colônia que foi salva
				$wpdb->query("DELETE FROM colonization_imperio_colonias WHERE id = {$id_colonia}");
				echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
				wp_die(); //Termina o script e envia a resposta	
			}
		}
		
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta			
	}

	/***********************
	function valida_modelo_nave()
	----------------------
	Valida um Modelo de nave
	***********************/
	function valida_modelo_nave() {
		global $wpdb;
		// Report all PHP errors
		$wpdb->hide_errors();
		//error_reporting(E_ALL); 
		//ini_set("display_errors", 1);
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		//dados_ajax_valida = "post_type=POST&action=valida_modelo_nave&id_imperio="+dados['id_imperio']+"&nome_modelo="+dados['nome_modelo']
		//+"&string_nave="+dados['string_nave']+dados_ajax_where;		
		
		$id_modelo = 0;
		$dados_salvos['debug'] = "";
		if (isset($_POST['where_value'])) { //Está ATUALIZANDO um modelo. Deve verificar se o modelo está em uso
			$id_modelo = $_POST['where_value'];
			$modelo_nave = new modelo_naves($id_modelo);
			$dados_salvos['debug'] .= "#{$id_modelo}: id_imperio: {$modelo_nave->id_imperio} \n {$modelo_nave->string_nave}\n modelo_em_uso: ".$modelo_nave->modelo_em_uso()."\n";
			if ($modelo_nave->modelo_em_uso() && $roles != "administrator") {
				$dados_salvos['resposta_ajax'] = "Não é possível alterar um Modelo que esteja em uso!";
				
				echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
				wp_die(); //Termina o script e envia a resposta
			}
		}
		
		//Verifica se existe um modelo com o mesmo nome
		$modelo_nave = new modelo_naves($id_modelo);
		$modelo_nave->id_imperio = $_POST['id_imperio'];
		$modelo_nave->string_nave = stripslashes($_POST['string_nave']);
		$modelo_nave->nome_modelo = $_POST['nome_modelo'];
		$dados_salvos['debug'] .= "#{$id_modelo}: id_imperio: {$_POST['id_imperio']} \n {$modelo_nave->string_nave}\n";

		if ($modelo_nave->modelo_mesmo_nome()) {
			$dados_salvos['resposta_ajax'] = "Já existe um Modelo com esse nome!";
				
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta						
		}
		
		//Verifica se existe um modelo com as mesmas partes
		if ($modelo_nave->modelo_ja_existe()) {
			$dados_salvos['resposta_ajax'] = "Já existe um Modelo idêntico ao que está sendo salvo!";
				
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta									
		}
		
		$dados_salvos['resposta_ajax'] = "OK!";
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta			
	}

	/***********************
	function deleta_modelo_nave()
	----------------------
	Deleta uma nave
	***********************/
	function deleta_modelo_nave() {
		global $wpdb;
		// Report all PHP errors
		$wpdb->hide_errors();
		//error_reporting(E_ALL); 
		//ini_set("display_errors", 1);
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		$modelo_nave = new modelo_naves($_POST['id']);
		$imperio = new imperio($modelo_nave->id_imperio);
	
		if ($imperio->id != $modelo_nave->id_imperio && $roles != "administrator") {
			$imperio = new imperio($modelo_nave->id_imperio,true);
			$dados_salvos['resposta_ajax'] = "Somente o Jogador do Império '{$imperio->nome}' pode deletar um modelo de nave que ele criou!";
			
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta			
		}
		
		//Verifica se o modelo está em uso. Se estiver, NÃO libera para deletar
		if ($modelo_nave->modelo_em_uso() && $roles != "administrator") {
			$dados_salvos['resposta_ajax'] = "Não é possível deletar um Modelo que esteja em uso!";
			
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta			
		}
	
		$wpdb->query("DELETE FROM colonization_modelo_naves WHERE id={$_POST['id']}");
		$dados_salvos['resposta_ajax'] = "OK!";
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta			
	}

	/***********************
	function carrega_nave()
	----------------------
	Carrega os dados de uma nave
	***********************/
	function carrega_nave() {
		global $wpdb;
		// Report all PHP errors
		$wpdb->hide_errors();
		//error_reporting(E_ALL); 
		//ini_set("display_errors", 1);
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
	
		$frota = new frota($_POST['id']);
		$imperio = new imperio ($frota->id_imperio);
		if ($imperio->id != $frota->id_imperio && $roles != "administrator") {
			$imperio = new imperio($frota->id_imperio,true);
			$dados_salvos['resposta_ajax'] = "Somente o Jogador do Império '{$imperio->nome}' pode realizar essa ação!";
			
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta			
		}		
		
		$dados_salvos['resposta_ajax'] = "OK!";
		$dados_salvos['lista_dados'] = $frota->lista_dados();
		
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta					
	
	}

	/***********************
	function lista_techs_ocultas()
	----------------------
	Exibe a lista com as Techs ocultas
	***********************/
	function lista_techs_ocultas() {
		global $wpdb;
		
		$tech = new tech();
		$resultados = $tech->query_tech(" AND ct.publica = 0  AND ct.id NOT IN (SELECT id_tech FROM colonization_imperio_techs AS cit WHERE cit.id_imperio = {$_POST['id_imperio']}) AND ct.id NOT IN (SELECT id_tech FROM colonization_imperio_techs_permitidas AS citp WHERE citp.id_imperio = {$_POST['id_imperio']})"); //Pega TODAS as Techs, em ordem
		//AND ct.id NOT IN (SELECT id_tech FROM colonization_imperio_techs AS cit WHERE cit.id_imperio = {$_POST['id_imperio']})
		//AND ct.id NOT IN (SELECT id_tech FROM colonization_imperio_techs_permitidas AS citp WHERE citp.id_imperio = {$_POST['id_imperio']})
		/***
		$resultados = $wpdb->get_results("SELECT DISTINCT ct.id FROM
		(SELECT ct.nome, ct.id 
		FROM colonization_tech AS ct
		WHERE ct.id NOT IN (SELECT id_tech FROM colonization_imperio_techs AS cit WHERE cit.id_imperio = {$_POST['id_imperio']})
		AND ct.id NOT IN (SELECT id_tech FROM colonization_imperio_techs_permitidas AS citp WHERE citp.id_imperio = {$_POST['id_imperio']})
		AND ct.publica = 0) AS ct
		ORDER BY ct.nome");
		
		$dados_salvos['debug'] = "SELECT DISTINCT ct.id FROM
		(SELECT ct.nome, ct.id 
		FROM colonization_tech AS ct
		WHERE ct.id NOT IN (SELECT id_tech FROM colonization_imperio_techs AS cit WHERE cit.id_imperio = {$_POST['id_imperio']})
		AND ct.id NOT IN (SELECT id_tech FROM colonization_imperio_techs_permitidas AS citp WHERE citp.id_imperio = {$_POST['id_imperio']})
		AND ct.publica = 0) AS ct
		ORDER BY ct.nome";
		//***/
		
		$lista_nome = [];
		$lista_id = [];
		$index = 0;
		if ($_POST['id'] != 0) {
			$tech = new tech($_POST['id']);
			$lista_nome[] = $tech->nome;
			$lista_id[] = $tech->id;		
		}
		
		foreach ($resultados as $resultado) {
			$tech = new tech($resultado->id);
			$lista_nome[] = $tech->nome;
			$lista_id[] = $tech->id;
			$index++;
		}

		$dados_salvos['lista_nome'] = $lista_nome;
		$dados_salvos['lista_id'] = $lista_id;
		
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta			
	}

	/***********************
	function tirar_cerco()
	----------------------
	Tira uma estrela do cerco
	***********************/
	function tirar_cerco() {
		global $wpdb;
		// Report all PHP errors
		$wpdb->hide_errors();
		//error_reporting(E_ALL); 
		//ini_set("display_errors", 1);
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}

		
		$dados_salvos['resposta_ajax'] = "Somente o ADMIN pode tirar o Cerco de um sistema!";
		if ($roles == "administrator") {
			$wpdb->query("UPDATE colonization_estrela SET cerco={$_POST['coloca_cerco']} WHERE id={$_POST['id_estrela']}");
			$dados_salvos['resposta_ajax'] = "OK!";
		}
		
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta	
	}

	/***********************
	function muda_nome_colonia()
	----------------------
	Muda o nome de uma colônia
	***********************/
	function muda_nome_colonia() {
		global $wpdb;
		// Report all PHP errors
		$wpdb->hide_errors();
		//error_reporting(E_ALL); 
		//ini_set("display_errors", 1);
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}

		$turno = new turno();
		$id_colonia = $wpdb->get_var("SELECT cic.id FROM colonization_imperio_colonias AS cic WHERE cic.id_planeta={$_POST['id_planeta']} AND turno={$turno->turno}");
		
		if (empty($id_colonia)) {
			$dados_salvos['resposta_ajax'] = "Este Planeta não é Colônia de nenhum Império!";
		} else {
			$colonia = new colonia($id_colonia);
			$imperio = new imperio($colonia->id_imperio);
		}
		
		if ($imperio->id != $colonia->id_imperio && $roles != "administrator") {
			$dados_salvos['resposta_ajax'] = "Você precisa ser o proprietário dessa Colônia para poder alterar seu nome!";
		}
		
		if (empty($dados_salvos)) {
			$resposta = $wpdb->query("UPDATE colonization_planeta SET nome='{$_POST['novo_nome']}' WHERE id={$_POST['id_planeta']}");
			$dados_salvos['resposta_ajax'] = "OK!";
			//Reseta os dados do JSON
			$wpdb->query("DELETE FROM colonization_balancos_turno WHERE turno={$turno->turno} AND id_imperio={$imperio->id}");
			$wpdb->query("DELETE FROM colonization_lista_colonias_turno WHERE turno={$turno->turno} AND id_imperio={$imperio->id}");
		}
		
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta	
	}
	
	/***********************
	function muda_nome_nave()
	----------------------
	Muda o nome de uma nave
	***********************/
	function muda_nome_nave() {
		global $wpdb;
		// Report all PHP errors
		$wpdb->hide_errors();
		//error_reporting(E_ALL); 
		//ini_set("display_errors", 1);
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}

		$turno = new turno();
		$imperio = new imperio();
		$nave = new frota($_POST['id']);
		
		if ($imperio->id != $nave->id_imperio && $roles != "administrator") {
			$dados_salvos['resposta_ajax'] = "Você precisa ser o proprietário dessa Nave para poder alterar seu nome!";
		}
		
		if (empty($dados_salvos)) {
			$resposta = $wpdb->query("UPDATE colonization_imperio_frota SET nome='{$_POST['novo_nome']}' WHERE id={$_POST['id']}");
			$dados_salvos['resposta_ajax'] = "OK!";
		}
		
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta	
	}	


	/***********************
	function valida_upgrade_nave()
	----------------------
	Valida um Upgrade de nave
	***********************/
	function valida_upgrade_nave() {
		global $wpdb, $plugin_colonization;
		// Report all PHP errors
		$wpdb->hide_errors();
		//error_reporting(E_ALL); 
		//ini_set("display_errors", 1);

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}		

		$modelo_nave = new modelo_naves($_POST['id_modelo']);
		$string_nave = json_decode(stripslashes($modelo_nave->string_nave),true);
		$atributos_modelo_nave = $modelo_nave->JSON_atributos();
		
		$nave_upgrade = new frota($_POST['id_nave']);

		$imperio = new imperio($nave_upgrade->id_imperio);
		$dados_salvos['resposta_ajax'] = "OK!";
		$dados_salvos['debug'] = "";
		
		if ($imperio->id != $nave_upgrade->id_imperio && $roles != "administrator") {
			$imperio = new imperio($nave_upgrade->id_imperio,true);
			$dados_salvos['resposta_ajax'] = "Somente o Jogador do Império '{$imperio->nome}' pode realizar essa ação!";
			
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta				
		}		
		
		//Valida o modelo e a nave
		$valida_nave = new valida_nave($imperio);
		$dados_salvos['resposta_ajax'] = $valida_nave->valida_nave($atributos_modelo_nave['tamanho'], $nave_upgrade->X, $nave_upgrade->Y, $nave_upgrade->Z, $string_nave, true);		
		
		//Verifica a diferença de custo
		$JSON_custo_nave = json_decode($nave_upgrade->custo, true);
		$JSON_custo_modelo = $modelo_nave->JSON_custo();
		
		foreach ($JSON_custo_modelo as $nome_recurso => $qtd) {
			if (!isset($JSON_custo_nave[$nome_recurso])) {
				$JSON_custo_nave[$nome_recurso] = 0;
			}
			
			$diferenca_custo = $JSON_custo_modelo[$nome_recurso] - $JSON_custo_nave[$nome_recurso];
			if ($nome_recurso == "industrializaveis") {
				$nome_recurso = "Industrializáveis";
			} elseif ($nome_recurso == "energium") {
				$nome_recurso = "Enérgium";
			} elseif ($nome_recurso == "nor_duranium") {
				$nome_recurso = "Nor-Duranium";
			} elseif ($nome_recurso == "upgrade") {
				continue;
			}
			
			$id_recurso = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='{$nome_recurso}'");
			$qtd_recurso_imperio = $wpdb->get_var("SELECT qtd FROM colonization_imperio_recursos WHERE id_recurso={$id_recurso} AND id_imperio={$imperio->id} AND turno={$imperio->turno->turno}");
			if ($qtd_recurso_imperio < $diferenca_custo) {
				$qtd_faltante = $diferenca_custo - $qtd_recurso_imperio;
				$dados_salvos['resposta_ajax'] = "Os recursos do Império são insuficientes! Faltam {$qtd_faltante} unidade(s) de {$nome_recurso}";
				break;
			}
		}
		
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta		
	}

	/***********************
	function valida_nave()
	----------------------
	Valida uma nova nave
	***********************/
	function valida_nave() {
		global $wpdb, $plugin_colonization;
		// Report all PHP errors
		$wpdb->hide_errors();
		//error_reporting(E_ALL); 
		//ini_set("display_errors", 1);
		
		$custo = json_decode(stripslashes($_POST['custo']),true);
		$upgrade = array_key_exists("upgrade", $custo);
		
		if (!empty($_POST['id'])) {
			$HP = $wpdb->get_var("SELECT cif.HP FROM colonization_imperio_frota AS cif WHERE cif.id={$_POST['id']}");
		}
		
		if (!empty($_POST['id']) && !$upgrade) {//Só é necessário validar naves que sejam novas OU não seja um upgrade
			$dados_salvos['resposta_ajax'] = "OK!";
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta
		} elseif ($upgrade && $_POST['turno_destruido'] == 0) {
			$dados_salvos['resposta_ajax'] = "O atributo 'UPGRADE' é reservado somente para naves sendo atualizadas!";
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta	
		} elseif ($upgrade && $HP != ($_POST['tamanho'])*10) {
			$dados_salvos['resposta_ajax'] = "Uma nave só pode ser atualizada caso esteja totalmente reparada!";
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta					
		} elseif (!empty($_POST['id']) && $upgrade) {
			$nave = new frota($_POST['id']);
			if ($nave->turno_destruido != 0) {//Não é um upgrade, está apenas salvando
				$dados_salvos['resposta_ajax'] = "OK!";
				echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
				wp_die(); //Termina o script e envia a resposta
			}
		}

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		$imperio = new imperio($_POST['id_imperio']);
		$string_nave = json_decode(stripslashes($_POST['string_nave']),true);
		$dados_salvos['resposta_ajax'] = "OK!";
		$dados_salvos['debug'] = "";
		$queries = [];
		
		if ($imperio->id != $_POST['id_imperio'] && $roles != "administrator") {
			$imperio = new imperio($_POST['id_imperio'],true);
			$dados_salvos['resposta_ajax'] = "Somente o Jogador do Império '{$imperio->nome}' pode realizar essa ação!";
			
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta				
		}

		if ($_POST['qtd'] != "1" && $roles != "administrator") {
			$dados_salvos['resposta_ajax'] = "Só é possível construir UMA Nave por vez!";
			
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta				
		}

		//TODO -- Valida os ATRIBUTOS da nave caso não seja um Admin
		//if ($roles != "administrator") {
			$string_nave_com_slashes = addslashes($_POST['string_nave']);
			$frota_temp = new frota();
			$frota_temp->string_nave = stripcslashes($_POST['string_nave']);
			$frota_temp->id_imperio = $imperio->id;
			
			if ($frota_temp->pega_modelo_nave() == false) {
				$dados_salvos['resposta_ajax'] = "Não há nenhum modelo que corresponda aos dados dessa nave! Por favor, entre em contato com o Admin!";
				
				echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
				wp_die(); //Termina o script e envia a resposta						
			}
			
			$modelo_nave = new modelo_naves($frota_temp->pega_modelo_nave());
			$custo_modelo = $modelo_nave->JSON_custo;
			
			$custo_modificado = $custo;
			if (isset($custo['upgrade'])) {
				$custo_modificado = json_decode(stripslashes($_POST['custo']),true);
				unset($custo_modificado['upgrade']);
			}
			
			//Verifica se os Custos sendo passados pelo Jogador são iguais aos Custos do Modelo
			if ($custo_modificado != $custo_modelo) {
				$dados_salvos['resposta_ajax'] = "O custo da Nave NÃO está coerente com o custo do Modelo! Por favor, entre em contato com o Admin!";
				$dados_salvos['debug'] .= "Custos: " . json_encode($custo_modificado) . " || " . json_encode($custo_modelo) . "\n";
				
				echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
				wp_die(); //Termina o script e envia a resposta									
			}
			
			$atributos_modelo = $modelo_nave->JSON_atributos;
			$atributos_OK = true;

			//Verifica se os atributos sendo passados pelo Jogador são iguais aos atributos do Modelo
			foreach ($atributos_modelo as $chave_nome_atributo => $valor_atributo) {
				if ($_POST[$chave_nome_atributo] != intval($valor_atributo)) {
					$dados_salvos['debug'] .= "Inválido! {$chave_nome_atributo}: {$_POST[$chave_nome_atributo]} || {$valor_atributo}\n";
					$atributos_OK = false;
				}			
			}
			
			//pdf_bombardeamento, poder_invasao e mk_camuflagem não são salvos no objeto, mas são salvos no string_nave
			if ($_POST['mk_camuflagem'] != 0) {
				$dados_salvos['debug'] .= "mk_camuflagem: {$_POST['mk_camuflagem']} || {$string_nave['mk_camuflagem']}\n";
				if ($string_nave['mk_camuflagem'] != $_POST['mk_camuflagem']) {
					$dados_salvos['debug'] .= "Inválido! mk_camuflagem\n";
					$atributos_OK = false;
				}
			}
			if ($_POST['pdf_bombardeamento'] != 0) {
				$pdf_bombardeamento = intval($string_nave['qtd_bombardeamento'])*(intval($string_nave['mk_bombardeamento'])*2-1);
				$dados_salvos['debug'] .= "pdf_bombardeamento: {$_POST['pdf_bombardeamento']} || {$pdf_bombardeamento}\n";
				if ($pdf_bombardeamento != $_POST['pdf_bombardeamento'] ) {
					$dados_salvos['debug'] .= "Inválido! pdf_bombardeamento\n";
					$atributos_OK = false;
				}
			}
			if ($_POST['poder_invasao'] != 0) {
				$dados_salvos['debug'] .= "poder_invasao: {$_POST['poder_invasao']} || {$string_nave['qtd_tropas']}\n";
				if (intval($string_nave['qtd_tropas']) != $_POST['poder_invasao'] ) {
					$dados_salvos['debug'] .= "Inválido! qtd_tropas\n";
					$atributos_OK = false;
				}
			}		
			
			if (!$atributos_OK) {
				$dados_salvos['resposta_ajax'] = "Os atributos da Nave NÃO estão coerentes com os atributos do Modelo! Por favor, entre em contato com o Admin!";
			
				echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
				wp_die(); //Termina o script e envia a resposta							
			}
		//}

		//Valida os dados da Nave de acordo com a Tech do Império
		if ($_POST['id_imperio'] != 0) {//Só valida se for um Jogador
			$valida_nave = new valida_nave($imperio);
			$dados_salvos['resposta_ajax'] = $valida_nave->valida_nave($_POST['tamanho'], $_POST['X'], $_POST['Y'], $_POST['Z'], $string_nave, $upgrade);
		}
		
		foreach ($custo as $nome_recurso => $qtd) {
			if ($qtd == 0) {
				continue;
			}
			
			if ($nome_recurso == "industrializaveis") {
				$nome_recurso = "Industrializáveis";
			} elseif ($nome_recurso == "energium") {
				$nome_recurso = "Enérgium";
			} elseif ($nome_recurso == "nor_duranium") {
				$nome_recurso = "Nor-Duranium";
			} elseif ($nome_recurso == "upgrade") {
				continue;
			}
			
			$id_recurso = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='{$nome_recurso}'");
			$dados_salvos['debug'] .= "SELECT id FROM colonization_recurso WHERE nome='{$nome_recurso}' => id_recurso: {$id_recurso}\n";
			$qtd_recurso_imperio = $wpdb->get_var("SELECT qtd FROM colonization_imperio_recursos WHERE id_recurso={$id_recurso} AND id_imperio={$imperio->id} AND turno={$imperio->turno->turno}");
			$dados_salvos['debug'] .= "\n {$id_recurso}:{$qtd_recurso_imperio} || {$qtd} && upgrade:{$upgrade}\n";
			if ($qtd_recurso_imperio < $qtd && !$upgrade) {
				$qtd_faltante = $qtd - $qtd_recurso_imperio;
				$dados_salvos['resposta_ajax'] = "Os recursos do Império são insuficientes! Faltam {$qtd_faltante} unidade(s) de {$nome_recurso}";
				break;
			}
			
			$soma_ou_subtrai = "-";
			if ($upgrade) {//Devolve os recursos da nave
				$soma_ou_subtrai = "+";
			}
			$queries[] = "UPDATE colonization_imperio_recursos SET qtd=qtd{$soma_ou_subtrai}{$qtd} WHERE id_recurso={$id_recurso} AND id_imperio={$imperio->id} AND turno={$imperio->turno->turno}";
		}
		
		if ($dados_salvos['resposta_ajax'] == "OK!") {
			foreach ($queries as $chave => $query) {
				$wpdb->query($query);
				//$wpdb->query("UPDATE colonization_log_recursos_imperio SET comentario='ATUALIZADO POR REPARA_NAVE' WHERE id_imperio={$imperio->id} AND id=(SELECT MAX(id) FROM colonization_log_recursos_imperio WHERE id_imperio={$imperio->id})");
			}
		}
		
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}


	/***********************
	function recursos_atuais_imperio()
	----------------------
	Exibe os recursos atuais do Império
	***********************/
	function recursos_atuais_imperio() {
		global $wpdb;

		$imperio = new imperio($_POST['id_imperio']);
	
		$dados_salvos['recursos_atuais'] = $imperio->exibe_recursos_atuais();

		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}


	//lista_techs_imperio
	/***********************
	function lista_techs_imperio()
	----------------------
	Retorna uma lista com todas as Techs que o Império pode pesquisar
	***********************/
	function lista_techs_imperio() {
		global $wpdb;
		$wpdb->hide_errors();
		// Report all PHP errors
		$wpdb->hide_errors();
		//error_reporting(E_ALL);
		//ini_set("display_errors", 1);
		
		$imperio = new imperio($_POST['id_imperio']);
		
		/***
		$ids_techs = $wpdb->get_results("
		SELECT ct.id, ct.custo, cit.custo_pago, cit.id_imperio_techs
		FROM colonization_tech AS ct
		LEFT JOIN (SELECT cit.id AS id_imperio_techs, cit.id_tech, cit.custo_pago FROM colonization_imperio_techs AS cit WHERE cit.id_imperio={$imperio->id}) AS cit
		ON cit.id_tech = ct.id
		WHERE (ct.publica=1 OR ct.id IN (SELECT citp.id_tech FROM colonization_imperio_techs_permitidas AS citp WHERE citp.id_imperio={$imperio->id})
		OR ct.id IN (SELECT DISTINCT cit.id_tech FROM colonization_imperio_techs AS cit WHERE cit.id_imperio={$imperio->id})) 
		AND (cit.id_tech IS NULL OR (cit.custo_pago IS NOT NULL AND cit.custo_pago > 0))
		ORDER BY cit.custo_pago DESC, ct.nome");
		//***/
		$ids_techs = new tech();
		$ids_techs = $ids_techs->query_tech(" AND ct.publica = 1", $imperio->id, false, "ORDER BY ct.custo_pago DESC, ct.nome");
		
		$dados_salvos['debug'] = "";
		$dados_salvos['id_imperio_techs'] = "";
		$html_select = "<select data-atributo='id_tech' style='width: 100%' class='nome_instalacao' onchange='return atualiza_custo_tech(event, this);'>";
		
		foreach ($ids_techs as $id_tech) {
			$tech = new tech($id_tech->id);
			if ($tech->id_tech_parent != 0) {
				$id_tech_parents = explode(";",$tech->id_tech_parent);
				$id_tech_parents = implode(",",$id_tech_parents);
				
				$where_tech_alternativa = "";
				if (!empty($tech->tech_alternativa)) {
					$where_tech_alternativa	= " OR id_tech={$tech->tech_alternativa}";
				}
				$imperio_tem_tech = $wpdb->get_var("SELECT id FROM colonization_imperio_techs WHERE (id_tech IN ({$id_tech_parents}){$where_tech_alternativa}) AND custo_pago=0 AND id_imperio={$_POST['id_imperio']}");
				if (empty($imperio_tem_tech)) {//Se não tem a Tech Requisito, pula a Tech
					continue;
				}
			}
			
			if (!empty($tech->lista_requisitos)) {
				foreach ($tech->id_tech_requisito as $chave => $id_tech_requisito) {
					//$dados_salvos['debug'] .= "{$tech->id} => id_tech_requisito: {$id_tech_requisito} \n";
					$imperio_tem_tech = $wpdb->get_var("SELECT id FROM colonization_imperio_techs WHERE id_tech={$id_tech_requisito} AND custo_pago=0 AND id_imperio={$_POST['id_imperio']}");
					if (empty($imperio_tem_tech)) {//Se não tem a Tech Requisito, pula a Tech
						continue 2;
					}
				}
			}
			
			$tech_parcial = $wpdb->get_var("SELECT COUNT(cit.id) FROM colonization_imperio_techs AS cit WHERE cit.id_imperio={$_POST['id_imperio']} AND cit.id_tech={$id_tech->id} AND cit.custo_pago = 0");
			if ($tech_parcial == 1) {//Já tem essa Tech, pode pular
				continue;
			}
			
			if ($html_select == "<select data-atributo='id_tech' style='width: 100%' class='nome_instalacao' onchange='return atualiza_custo_tech(event, this);'>" && !empty($id_tech->custo_pago)) {
				$dados_salvos['id_imperio_techs'] = $id_tech->id_imperio_techs;
			}
			if (!empty($id_tech->custo_pago)) {
				$dados_salvos['custos_tech'][$tech->id] = $tech->custo - $id_tech->custo_pago;
				$html_select .= "<option value='{$tech->id}' data-id-imperio-tech='{$id_tech->id_imperio_techs}'>Concluir Pesquisa '{$tech->nome}'</option>";
			} else {
				$dados_salvos['custos_tech'][$tech->id] = $tech->custo;
				$html_select .= "<option value='{$tech->id}' data-id-imperio-tech=''>{$tech->nome}</option>";
			}
			$dados_salvos['descricao_tech'][$tech->id] = $tech->descricao;
			
			if (empty($dados_salvos['custo'])) {
				$dados_salvos['custo'] = $dados_salvos['custos_tech'][$tech->id];
				$dados_salvos['descricao'] = $dados_salvos['descricao_tech'][$tech->id];
				$dados_salvos['id_imperio_techs'] = $id_tech->id_imperio_techs;
			}
		}
		
		$html_select .= "</select>";
		
		$dados_salvos['resposta_ajax'] = "OK!";
		$dados_salvos['html'] = $html_select;
		
		
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die();
	}
	
	
	/***********************
	function lista_instalacoes_imperio()
	----------------------
	Retorna uma lista com todas as Instalações que o Império pode construir
	***********************/
	function lista_instalacoes_imperio() {
		global $wpdb;
		// Report all PHP errors
		$wpdb->hide_errors();
		//error_reporting(E_ALL);
		//ini_set("display_errors", 1);
		
		$turno = new turno();
		$id_imperio = $wpdb->get_var("
		SELECT cic.id_imperio 
		FROM colonization_imperio_colonias AS cic
		WHERE cic.id_planeta={$_POST['id_planeta']} AND cic.turno={$turno->turno}");
		
		$imperio = new imperio($id_imperio);
		
		$ids_instalacao = $wpdb->get_results("
		SELECT ci.id , ct.nome AS nome_tech
		FROM colonization_instalacao AS ci
		JOIN colonization_imperio_techs AS cit
		ON cit.id_tech = ci.id_tech
		JOIN colonization_tech AS ct
		ON ct.id = ci.id_tech
		WHERE cit.id_imperio={$imperio->id} AND cit.custo_pago=0 
		AND (ci.publica=1 OR ci.id IN (SELECT cii.id_instalacao FROM colonization_imperio_instalacoes AS cii WHERE cii.id_imperio={$imperio->id}))
		ORDER BY ci.nome");
		
		$dados_salvos['debug'] = "";
		$html_select_instalacoes = "<select data-atributo='id_instalacao' style='width: 100%' class='nome_instalacao' onchange='return atualiza_custo_instalacao(event, this);'>";
		
		foreach ($ids_instalacao as $id_instalacao) {
			$instalacao = new instalacao($id_instalacao->id);
			
			if ($instalacao->especiais != "") {
				$especiais = explode(";",$instalacao->especiais);
				
				//Especiais: tech_requisito=id_tech
				//Requer uma Tech especial como requisito

				$tech_requisito = array_values(array_filter($especiais, function($value) {
					return strpos($value, 'tech_requisito') !== false;
				}));
				
				if (!empty($tech_requisito)) {
					$valor_tech_requisito = explode("=",$tech_requisito[0]);
					$id_tech_requisito = $valor_tech_requisito[1];
					$tech_requisito = new tech ($id_tech_requisito);
					
					$id_tech_imperio = $wpdb->get_var("SELECT id FROM colonization_imperio_techs WHERE id_imperio={$imperio->id} AND (id_tech={$tech_requisito->id} OR id_tech={$tech_requisito->id_tech_alternativa}) AND custo_pago=0");
					//$dados_salvos['debug'] .= "SELECT id FROM colonization_imperio_techs WHERE id_imperio={$imperio->id} AND (id_tech={$tech_requisito->id} OR id_tech={$tech_requisito->id_tech_alternativa}) AND custo_pago=0 \n";
					if (empty($id_tech_imperio)) {//O Império não tem a Tech requisito -- vai para a próxima Instalação
						continue;
					}
				}
			}
			
			$html_select_instalacoes .= "<option value='{$instalacao->id}'>{$instalacao->nome}</option>";
			$dados_salvos['custos_instalacao'][$id_instalacao->id] = $instalacao->html_custo();
			$html_slots = "Ocupa {$instalacao->slots} slots.";
			if ($instalacao->slots == 0) {
				$html_slots = "Não ocupa slots.";
			} elseif ($instalacao->slots == 1) {
				$html_slots = "Ocupa um slot.";
			}
			$html_pode_desativar = "";
			if ($instalacao->pode_desativar == 0) {
				$html_pode_desativar = " <span style='font-style: italic;'>Essa instalação não pode ser desativada.</span>";
			}
			$dados_salvos['descricao_instalacao'][$id_instalacao->id] = "{$instalacao->descricao}. {$html_slots}{$html_pode_desativar}";
			$dados_salvos['produz_instalacao'][$id_instalacao->id] = $instalacao->html_producao_consumo_instalacao();
			$dados_salvos['tech_instalacao'][$id_instalacao->id] = $id_instalacao->nome_tech;
		}
		
		$html_select_instalacoes .= "</select>";
		
		$dados_salvos['resposta_ajax'] = "OK!";
		$dados_salvos['html'] = $html_select_instalacoes;
		
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die();
	}

	/***********************
	function lista_estrelas ()
	----------------------
	Retorna com todas as estrelas disponíveis em formado JSON
	***********************/
	function lista_estrelas() {
		global $wpdb;
		
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		$dados_estrela = [];
		if (!empty($roles)) {
			$query = $wpdb->get_results("SELECT id, nome, descricao, X, Y, Z, tipo, ids_estrelas_destino FROM colonization_estrela");
		
			foreach ($query as $chave => $resultado) {
				$dados_estrela[$chave]['nome'] = $resultado->nome;
				$dados_estrela[$chave]['tipo'] = $resultado->tipo;
				$dados_estrela[$chave]['X'] = $resultado->X;
				$dados_estrela[$chave]['Y'] = $resultado->Y;
				$dados_estrela[$chave]['Z'] = $resultado->Z;
			}
		} else {
			$dados_estrela[0]['roles'] = $roles;
		}
		
		echo json_encode($dados_estrela); //Envia a resposta via echo, codificado como JSON
		wp_die();
	}

	/***********************
	function ids_recursos_extrativos ()
	----------------------
	Retorna com todos os ids dos recursos extrativos em formado JSON
	***********************/	
	function ids_recursos_extrativos() {
		global $wpdb;
		
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		$ids_recursos = [];
		if (!empty($roles)) {
			$query = $wpdb->get_results("SELECT id FROM colonization_recurso WHERE extrativo = 1");
			foreach ($query as $chave => $ids) {
				$ids_recursos[$chave] = $ids->id;
			}
		}
		
		echo json_encode($ids_recursos); //Envia a resposta via echo, codificado como JSON
		wp_die();		
	}

	/***********************
	function aceita_missao ()
	----------------------
	Aceita ou Rejeita uma missão
	***********************/	
	function aceita_missao() {
		global $wpdb;
		
		$imperio = new imperio();
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		$dados_salvos['debug'] = "";
		$imperio_correto = new imperio($_POST['id_imperio']);
		$dados_salvos['resposta_ajax'] = "Somente o Jogador do '{$imperio_correto->nome}' pode processar sua missão!";
		if ($imperio->id == $_POST['id_imperio'] || $roles == "administrator") {
			$missao = new missoes($_POST['id']);
			//Verifica se o player já aceitou ou se rejeitou essa Missão. Uma missão REJEITADA pode ser aceita posteriormente, mas uma missão ACEITA não pode mais ser editada
			$id_imperios_aceitaram = explode(";",$missao->id_imperios_aceitaram);
			$id_imperios_rejeitaram = explode(";",$missao->id_imperios_rejeitaram);
			
			$aceitou = array_search($_POST['id_imperio'],$id_imperios_aceitaram);
			$rejeitou = array_search($_POST['id_imperio'],$id_imperios_rejeitaram);

			$dados_salvos['debug'] .= "\$_POST['aceita']: {$_POST['aceita']}";
			if ($_POST['aceita'] == "true") {
				//Verifica se está na lista de rejeitados
				if ($rejeitou !== false) {
					unset($id_imperios_rejeitaram[$rejeitou]);
				}
				
				$lista_imperios_rejeitaram = implode(";",$id_imperios_rejeitaram);
				if (empty($missao->id_imperios_aceitaram)) {//É o primeiro império a aceitar!
					$wpdb->query("UPDATE colonization_missao SET id_imperios_aceitaram='{$_POST['id_imperio']}', id_imperios_rejeitaram='{$lista_imperios_rejeitaram}' WHERE id={$_POST['id']}");
				} else {
					$lista_imperios_aceitaram = "{$missao->id_imperios_aceitaram};{$_POST['id_imperio']}";
					$wpdb->query("UPDATE colonization_missao SET id_imperios_aceitaram='{$lista_imperios_aceitaram}', id_imperios_rejeitaram='{$lista_imperios_rejeitaram}'  WHERE id={$_POST['id']}");
				}
			} else {//Rejeitou!
				if (empty($missao->id_imperios_rejeitaram)) {//É o primeiro império a rejeitar!
					$wpdb->query("UPDATE colonization_missao SET id_imperios_rejeitaram='{$_POST['id_imperio']}' WHERE id={$_POST['id']}");
				} else {
					$lista_imperios_rejeitaram = "{$missao->id_imperios_rejeitaram};{$_POST['id_imperio']}";
					$wpdb->query("UPDATE colonization_missao SET id_imperios_rejeitaram='{$lista_imperios_rejeitaram}' WHERE id={$_POST['id']}");
				}
			}
			$dados_salvos['resposta_ajax'] = "SALVO!";
		}
		
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die();
	}

	/***********************
	function envia_nave ()
	----------------------
	Despacha uma nave
	***********************/	
	function envia_nave() {
		global $wpdb;
		// Report all PHP errors
		$wpdb->hide_errors();
		//error_reporting(E_ALL);
		//ini_set("display_errors", 1);
		
		$nave = new frota($_POST['id']);
		if ($nave->alcance == 0 || $nave->nivel_estacao_orbital != 0) {
			$dados_salvos['resposta_ajax'] = "Não é possível despachar a nave até o destino desejado.";
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die();			
		}
		
		
		$imperio = new imperio($nave->id_imperio);
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		//Verificar se tem Combustível suficiente para mandar a nave.
		$id_plasma_dobra = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome = 'Plasma de Dobra'");
		$estrela_origem = new estrela($nave->id_estrela);
		
		$fator_tamanho = 1;
		if ($nave->tamanho > 30) {
			$fator_tamanho = ceil($nave->tamanho/30);
		}
		
		$distancia_origem_destino = $nave->qtd*$estrela_origem->distancia_estrela($_POST['id_estrela'])*$fator_tamanho;
		$plasma_disponivel = $imperio->pega_qtd_recurso_imperio($id_plasma_dobra);

		if ($plasma_disponivel < $distancia_origem_destino) {
			$dados_salvos['resposta_ajax'] = "Não é possível despachar a nave até o destino desejado. \n
			É necessário {$distancia_origem_destino} lotes de Plasma de Dobra, porém você tem apenas {$plasma_disponivel}.";
			
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die();
		}
		
		$imperio_correto = new imperio($nave->id_imperio, true);
		$dados_salvos['resposta_ajax'] = "Somente o Jogador do '{$imperio_correto->nome}' pode despachar sua nave!";
		if ($imperio->id == $nave->id_imperio || $roles == "administrator") {
			if ($nave->id_estrela_destino == 0) {
				$resposta = $wpdb->query("UPDATE colonization_imperio_frota SET id_estrela_destino={$_POST['id_estrela']} WHERE id={$nave->id}");
				$resposta = $wpdb->query("UPDATE colonization_imperio_recursos SET qtd=qtd-{$distancia_origem_destino} WHERE id_recurso={$id_plasma_dobra} AND id_imperio={$imperio->id} AND turno={$imperio->turno->turno}");
				
				$dados_salvos['resposta_ajax'] = "SALVO!";
			} else {
				$estrela = new estrela($nave->id_estrela_destino);
				$dados_salvos['resposta_ajax'] = "Essa nave já foi despachada para {$estrela->nome} ({$estrela->X};{$estrela->Y};{$estrela->Z})!";
			}
		}
		
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die();
	}


	/***********************
	function processa_viagem_nave ()
	----------------------
	Processa uma viagem
	***********************/	
	function processa_viagem_nave() {
		global $wpdb;


		$dados_salvos['debug'] = "";
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}

		$nave = new frota($_POST['id']);
		$imperio = new imperio($nave->id_imperio);
		$id_estrela_origem = $wpdb->get_var("SELECT id FROM colonization_estrela WHERE X={$nave->X} AND Y={$nave->Y} AND Z={$nave->Z}");
		$estrela_origem = new estrela($id_estrela_origem);
		$turno = new turno();
		if ($roles == "administrator" && $nave->id_estrela_destino != 0) {
			$estrela_destino = new estrela($nave->id_estrela_destino);
		
			//Verifica se a Estrela já foi visitada, e se não foi marca como visitada
			$estrela_visitada = $wpdb->get_var("SELECT id FROM colonization_estrelas_historico WHERE id_imperio={$nave->id_imperio} AND id_estrela={$estrela_destino->id}");
			$estrela_origem_visitada = $wpdb->get_var("SELECT id FROM colonization_estrelas_historico WHERE id_imperio={$nave->id_imperio} AND id_estrela={$id_estrela_origem}");
			if (empty($estrela_visitada)) {
				$wpdb->query("INSERT INTO colonization_estrelas_historico SET id_imperio={$nave->id_imperio}, id_estrela={$estrela_destino->id}, turno={$turno->turno}");
			} else {
				$wpdb->query("UPDATE colonization_estrelas_historico SET turno={$turno->turno} WHERE id={$estrela_visitada}");
			}

			if (empty($estrela_origem_visitada)) {
				$wpdb->query("INSERT INTO colonization_estrelas_historico SET id_imperio={$nave->id_imperio}, id_estrela={$id_estrela_origem}, turno={$turno->turno}");
			} else {
				$wpdb->query("UPDATE colonization_estrelas_historico SET turno={$turno->turno} WHERE id={$estrela_origem_visitada}");
			}			
			
			//Verifica se já pesquisou essa estrela. Se ainda não pesquisou, então fornece 5 pontos de pesquisa automaticamente
			if ($nave->pesquisa == 1) {
				//Pega os recursos desconhecidos
				$id_recursos_desconhecidos = $wpdb->get_results("
				SELECT DISTINCT cir.id_recurso FROM colonization_imperio_recursos AS cir
				JOIN colonization_planeta AS cp
				ON cp.id_estrela={$estrela_destino->id}
				JOIN colonization_planeta_recursos AS cpr
				ON cpr.id_planeta = cp.id
				AND cir.id_recurso = cpr.id_recurso
				WHERE cir.disponivel=0 AND cir.id_imperio={$nave->id_imperio} AND cir.turno={$turno->turno} AND cpr.turno={$turno->turno}
				");
				
				$dados_salvos['alerta'] = "";
				
				if ($estrela_destino->comentarios != "") {
					$dados_salvos['alerta'] .= "{$estrela_destino->comentarios}\n";
				}
				
				if ($estrela_destino->anti_dobra == 1) {
					$dados_salvos['alerta'] .= "Há um campo anti-dobra ativo no sistema!\n";
				}
				
				if ($nave->camuflagem > 0) {
					$dados_salvos['alerta'] .= "A nave está camuflada!\n";
				}
				
				if (!empty($id_recursos_desconhecidos)) {
					$dados_salvos['alerta'] .= "O Império descobriu os seguintes recursos novos: \n";
				}
				
				foreach ($id_recursos_desconhecidos as $id_recurso_novo) {
					$wpdb->query("UPDATE colonization_imperio_recursos AS cir SET cir.disponivel=1 WHERE cir.id_recurso={$id_recurso_novo->id_recurso} AND cir.disponivel=0 AND cir.id_imperio={$nave->id_imperio} AND cir.turno={$turno->turno}");
					$recurso = new recurso($id_recurso_novo->id_recurso);
					$dados_salvos['alerta'] .= "{$recurso->nome} \n";
				}

				$id_pesquisa = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='Pesquisa'");
				$id_pesquisa_estrela_destino = $wpdb->get_var("SELECT id FROM colonization_imperio_historico_pesquisa WHERE id_imperio={$nave->id_imperio} AND id_estrela={$estrela_destino->id}");
				$nivel_sensor_estrela_destino = $wpdb->get_var("SELECT sensores FROM colonization_imperio_historico_pesquisa WHERE id_imperio={$nave->id_imperio} AND id_estrela={$estrela_destino->id}");
				$id_pesquisa_estrela_origem = $wpdb->get_var("SELECT id FROM colonization_imperio_historico_pesquisa WHERE id_imperio={$nave->id_imperio} AND id_estrela={$id_estrela_origem}");
				$nivel_sensor_estrela_origem = $wpdb->get_var("SELECT sensores FROM colonization_imperio_historico_pesquisa WHERE id_imperio={$nave->id_imperio} AND id_estrela={$id_estrela_origem}");
				
				if (empty($id_pesquisa_estrela_destino)) {//O sistema ainda não foi pesquisado, pode adicionar o bônus de pesquisa!
					$wpdb->query("INSERT INTO colonization_imperio_historico_pesquisa SET id_imperio={$nave->id_imperio}, id_estrela={$estrela_destino->id}, turno={$turno->turno}, sensores={$imperio->sensores}");
					$qtd_pesquisa = ($imperio->sensores + 5);
					$wpdb->query("UPDATE colonization_imperio_recursos SET qtd=qtd+{$qtd_pesquisa} WHERE id_recurso={$id_pesquisa} AND id_imperio={$nave->id_imperio} AND turno={$turno->turno}");
					$dados_salvos['debug'] .= "\n nova_pesquisa ||UPDATE colonization_imperio_recursos SET qtd=qtd+{$qtd_pesquisa} WHERE id_recurso={$id_pesquisa} AND id_imperio={$nave->id_imperio} AND turno={$turno->turno}";
				} elseif ($nivel_sensor_estrela_destino < $imperio->sensores) {
					$wpdb->query("UPDATE colonization_imperio_historico_pesquisa SET sensores={$imperio->sensores}, turno={$turno->turno} WHERE id_estrela={$estrela_destino->id} AND id_imperio={$nave->id_imperio}");
					$qtd_pesquisa = ($imperio->sensores - $nivel_sensor_estrela_destino);
					$wpdb->query("UPDATE colonization_imperio_recursos SET qtd=qtd+{$qtd_pesquisa} WHERE id_recurso={$id_pesquisa} AND id_imperio={$nave->id_imperio} AND turno={$turno->turno}");
					$dados_salvos['debug'] .= "\n sensores_estrela_destino: {$nivel_sensor_estrela_destino} || UPDATE colonization_imperio_recursos SET qtd=qtd+{$qtd_pesquisa} WHERE id_recurso={$id_pesquisa} AND id_imperio={$nave->id_imperio} AND turno={$turno->turno}";
				}
				
				//Pesquisa a estrela_origem se for possível
				if (empty($id_pesquisa_estrela_origem)) {
					$wpdb->query("INSERT INTO colonization_imperio_historico_pesquisa SET id_imperio={$nave->id_imperio}, id_estrela={$id_estrela_origem}, turno={$turno->turno}, sensores={$imperio->sensores}");
					$qtd_pesquisa = ($imperio->sensores + 5);
					$wpdb->query("UPDATE colonization_imperio_recursos SET qtd=qtd+{$qtd_pesquisa} WHERE id_recurso={$id_pesquisa} AND id_imperio={$nave->id_imperio} AND turno={$turno->turno}");
					$dados_salvos['debug'] .= "\n nova_pesquisa ||UPDATE colonization_imperio_recursos SET qtd=qtd+{$qtd_pesquisa} WHERE id_recurso={$id_pesquisa} AND id_imperio={$nave->id_imperio} AND turno={$turno->turno}";					
				} elseif ($nivel_sensor_estrela_origem < $imperio->sensores) {
					$wpdb->query("UPDATE colonization_imperio_historico_pesquisa SET sensores={$imperio->sensores}, turno={$turno->turno} WHERE id_estrela={$id_estrela_origem} AND id_imperio={$nave->id_imperio}");
					$qtd_pesquisa = ($imperio->sensores - $nivel_sensor_estrela_origem);
					$wpdb->query("UPDATE colonization_imperio_recursos SET qtd=qtd+{$qtd_pesquisa} WHERE id_recurso={$id_pesquisa} AND id_imperio={$nave->id_imperio} AND turno={$turno->turno}");
					$dados_salvos['debug'] .= "\n sensores_estrela_origem: {$nivel_sensor_estrela_origem} || UPDATE colonization_imperio_recursos SET qtd=qtd+{$qtd_pesquisa} WHERE id_recurso={$id_pesquisa} AND id_imperio={$nave->id_imperio} AND turno={$turno->turno}";					
				}
			}

			$naves_no_local = $wpdb->get_results("
			SELECT DISTINCT id_imperio, nome_npc, 'Naves' as categoria
			FROM colonization_imperio_frota 
			WHERE X='{$estrela_destino->X}' AND Y='{$estrela_destino->Y}' AND Z='{$estrela_destino->Z}' AND (turno_destruido IS NULL OR turno_destruido = '') AND id_imperio != {$imperio->id}
			AND (camuflagem < ({$imperio->sensores} + {$imperio->anti_camuflagem}) OR visivel = 1)");
			
			//Também vale quando há uma COLÔNIA no local
			$ids_imperios_colonias = $wpdb->get_results("
			SELECT DISTINCT cic.id_imperio, cic.nome_npc, 'Colônia' as categoria
			FROM colonization_imperio_colonias AS cic
			JOIN colonization_planeta AS cp
			ON cp.id = cic.id_planeta
			JOIN colonization_estrela AS ce
			ON ce.id = cp.id_estrela
			WHERE ce.id={$estrela_destino->id}
			AND cic.turno={$turno->turno}");
				
			if (!empty($ids_imperios_colonias)) {
				$naves_no_local = array_merge($naves_no_local, $ids_imperios_colonias);
			}
				
			$nave_detectada = false;
			if (!empty($naves_no_local)) {
				$dados_salvos['alerta'] .= "Foram encontrados outros Impérios no local: \n";
				foreach ($naves_no_local as $nave_no_local) {
					//Atualiza a tabela de Diplomacia com Primeiro Contato, caso não tenha
					$id_diplomacia = $wpdb->get_var("SELECT id FROM colonization_diplomacia WHERE id_imperio={$nave->id_imperio} AND id_imperio_contato={$nave_no_local->id_imperio} and nome_npc='{$nave_no_local->nome_npc}'");
					if (empty($id_diplomacia)) {
						if ($nave_no_local->id_imperio != 0) {
							$wpdb->query("INSERT INTO colonization_diplomacia SET id_imperio={$nave_no_local->id_imperio}, id_imperio_contato={$nave->id_imperio}");
						}
						$imperio_tem_colonias = $wpdb->get_var("SELECT COUNT(id) FROM colonization_imperio_colonias WHERE id_imperio={$nave_no_local->id_imperio} OR (id_imperio=0 AND nome_npc='{$nave_no_local->nome_npc}')");
						$dados_salvos['debug'] .= "\nSELECT COUNT(id) FROM colonization_imperio_colonias WHERE id_imperio={$nave_no_local->id_imperio} OR (id_imperio=0 AND nome_npc ='{$nave_no_local->nome_npc}')
						imperio_tem_colonias: {$imperio_tem_colonias}";
						if ($imperio_tem_colonias != 0) {
							$wpdb->query("INSERT INTO colonization_diplomacia SET id_imperio={$nave->id_imperio}, id_imperio_contato={$nave_no_local->id_imperio}, nome_npc='{$nave_no_local->nome_npc}'");
						}
					}
				}
				//Também vale quando há uma COLÔNIA no local
				$ids_imperios_colonias = $wpdb->get_results("
				SELECT cic.id_imperio, cic.nome_npc 
				FROM colonization_imperio_colonias AS cic
				JOIN colonization_planeta AS cp
				ON cp.id = cic.id_planeta
				JOIN colonization_estrela AS ce
				ON ce.id = cp.id_estrela
				WHERE ce.id={$estrela_destino->id}
				AND cic.turno = {$turno->turno}");
					
				foreach ($ids_imperios_colonias as $imperios_colonia) {
					$id_diplomacia = $wpdb->get_var("SELECT id FROM colonization_diplomacia WHERE id_imperio={$nave->id_imperio} AND id_imperio_contato={$imperios_colonia->id_imperio} and nome_npc='{$imperios_colonia->nome_npc}'");	
					
					$imperio_colonia = new imperio($imperios_colonia->id_imperio);
					$dados_salvos['debug'] .= "\n imperio_colonia->sensores: {$imperio_colonia->sensores } > nave->camuflagem: {$nave->camuflagem}";
					if ($imperio_colonia->sensores > $nave->camuflagem && $imperio_colonia->id != $nave->id_imperio && $imperio_colonia->id != 0) {
						//Verifica se já tem um aviso
						$id_aviso = $wpdb->get_var("SELECT id FROM colonization_visita_nave WHERE id_imperio = {$imperio_colonia->id} AND id_estrela = {$estrela_destino->id} AND processado = FALSE");
						if (empty($id_aviso)) {
							$wpdb->query("INSERT INTO colonization_visita_nave SET id_imperio={$imperio_colonia->id}, id_estrela={$estrela_destino->id}, id_nave={$nave->id}");
						}
					}
				}
			}
			$dados_salvos['debug'] .= "\n 
			SELECT DISTINCT id_imperio, nome_npc, 'Naves' as categoria
			FROM colonization_imperio_frota 
			WHERE X='{$estrela_destino->X}' AND Y='{$estrela_destino->Y}' AND Z='{$estrela_destino->Z}' AND (turno_destruido IS NULL OR turno_destruido = '') AND id_imperio != {$imperio->id}
			AND (camuflagem < ({$imperio->sensores} + {$imperio->anti_camuflagem}) OR visivel = 1)\n		
			naves_no_local:" . count($naves_no_local);
				
			foreach ($naves_no_local as $ids_imperio) {
				$imperio = new imperio($ids_imperio->id_imperio);
				if ($nave->camuflagem > 0 && !$nave_detectada) {
					if (($imperio->sensores + $imperio->anti_camuflagem) > $nave->camuflagem) {
						$nave_detectada = true;
						$dados_salvos['alerta'] .= "A nave camuflada foi DETECTADA!!!\n";
					}
				}
				if ($imperio->id == 0) {
					$imperio->nome = $ids_imperio->nome_npc;
				}
				$dados_salvos['alerta'] .= "{$ids_imperio->categoria}: {$imperio->nome}\n";
			}
			
			$resposta = $wpdb->query("UPDATE colonization_imperio_frota SET X={$estrela_destino->X}, Y={$estrela_destino->Y}, Z={$estrela_destino->Z}, id_estrela_destino=0, visivel=false, anti_dobra={$estrela_destino->anti_dobra} WHERE id={$nave->id}"); //Atualiza a posição da nave
			$resposta = $wpdb->query("INSERT INTO colonization_frota_historico_movimentacao SET id_nave={$nave->id}, id_imperio={$nave->id_imperio}, id_estrela_origem={$id_estrela_origem}, id_estrela_destino={$estrela_destino->id}, turno={$turno->turno}"); //Adiciona o histórico da nave
		}
		
		$dados_salvos['resposta_ajax'] = "SALVO!";
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die();
	}
	
	/***********************
	function nave_visivel ()
	----------------------
	Faz uma nave ficar visível
	***********************/	
	function nave_visivel() {
		global $wpdb;
		
		$nave = new frota($_POST['id']);
		$imperio = new imperio($nave->id_imperio);

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}

		if ($roles == "administrator" || $user->ID == $imperio->id_jogador) {
			$resposta = $wpdb->query("UPDATE colonization_imperio_frota SET visivel=true WHERE id={$nave->id}");
		}
		
		$dados_salvos['resposta_ajax'] = "SALVO!";
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die();
	}


	//processa_recebimento_recurso
	/***********************
	function processa_recebimento_recurso ()
	----------------------
	Processa o recebimento de recursos
	***********************/		
	function processa_recebimento_recurso() {
		global $wpdb;
		
		$transfere_recurso = new transfere_recurso($_POST['id']);
		$imperio = new imperio($transfere_recurso->id_imperio_destino, true);
		
		$turno = new turno();
		$wpdb->query("UPDATE colonization_imperio_recursos SET qtd=qtd+{$transfere_recurso->qtd} WHERE turno={$turno->turno} AND id_imperio={$transfere_recurso->id_imperio_destino} AND id_recurso={$transfere_recurso->id_recurso}");
		$wpdb->query("UPDATE colonization_imperio_transfere_recurso SET processado=true, turno={$turno->turno} WHERE id={$_POST['id']}");
		
		$dados_salvos['resposta_ajax'] = "SALVO!";
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die();
	}
	
	/***********************
	function processa_recebimento_tech ()
	----------------------
	Processa o recebimento de uma Tech
	***********************/	
	function processa_recebimento_tech() {
		global $wpdb;
		
		$turno = new turno();
		$transfere_tech = new transfere_tech($_POST['id']);
		$tech = new tech($transfere_tech->id_tech);
		
		$imperio = new imperio($transfere_tech->id_imperio_destino);
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		if ($roles != "administrator" && $imperio->id != $transfere_tech->id_imperio_destino) {
			$dados_salvos['resposta_ajax'] = "Você não está autorizado a realizar esta operação!";
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die();
		}
		
		if ($_POST['autorizado'] == 1) {//Se aceitou, é para adicionar a Tech na lista de Techs
			$custo_pago = round($tech->custo*0.3, 0, PHP_ROUND_HALF_UP);
			$wpdb->query("INSERT INTO colonization_imperio_techs SET id_imperio={$transfere_tech->id_imperio_destino}, custo_pago={$custo_pago}, id_tech={$transfere_tech->id_tech}, turno={$turno->turno}");
		} else {
			$id_pesquisa = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='Pesquisa'");
			$id_tech_imperio = $wpdb->get_var("SELECT id FROM colonization_imperio_techs WHERE id_imperio={$transfere_tech->id_imperio_destino} AND id_tech={$transfere_tech->id_tech}");
			$bonus = round(0.3*$tech->custo, 0, PHP_ROUND_HALF_UP);
			$ressarce = round(0.1*$tech->custo, 0, PHP_ROUND_HALF_UP);
			$bonus_parcial = $ressarce;
		
			if (!empty($id_tech_imperio)) {
			$imperio_tech = new imperio_techs($id_tech_imperio);
			$bonus_parcial = $bonus - $imperio_tech->custo_pago;
				if ($bonus_parcial < $ressarce) {
					$bonus_parcial = $ressarce;
				}
			}
			
			$wpdb->query("UPDATE colonization_imperio_recursos SET qtd=qtd+{$bonus_parcial} WHERE turno={$turno->turno} AND id_imperio={$transfere_tech->id_imperio_destino} AND id_recurso={$id_pesquisa}");
		}
	
		//Depois de salvar, registra a transferência
		$this->salva_objeto();
		wp_die();
	}


	/***********************
	function valida_transfere_recurso ()
	----------------------
	Valida o objeto desejado
	***********************/	
	function valida_transfere_recurso() {
		global $wpdb; 

		$id_imperio_destino = $wpdb->get_var("SELECT citr.id_imperio_destino FROM
		colonization_imperio_transfere_recurso AS citr
		WHERE citr.id_imperio_origem = {$_POST['id_imperio_origem']}
		AND citr.id_imperio_destino = {$_POST['id_imperio_destino']}
		AND citr.processado = false
		");
		
		if(!empty($id_imperio_destino)) {
			$imperio = new imperio($id_imperio_destino, true);
			$dados_salvos['resposta_ajax'] = "Já existe uma operação pendente! Aguarde o aceite ou declínio do {$imperio->nome}";
			
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta				
		}
		
		$dados_salvos['debug'] = "";
		if ($_POST['id_imperio_origem'] != 0) { //Não é um NPC! Tem que validar!
			$imperio = new imperio($_POST['id_imperio_origem']);
			if ($imperio->id != $_POST['id_imperio_origem']) {
				$imperio = new imperio($_POST['id_imperio_origem'],true);
				$dados_salvos['resposta_ajax'] = "Somente o Jogador do Império '{$imperio->nome}' pode realizar essa ação!";
			} else {
				$turno = new turno();
				$recursos_disponivel = $wpdb->get_var("SELECT qtd FROM colonization_imperio_recursos WHERE id_imperio={$imperio->id} AND id_recurso={$_POST['id_recurso']} AND turno={$turno->turno}");
				if ($recursos_disponivel >= $_POST['qtd']) {
					$wpdb->query("UPDATE colonization_imperio_recursos SET qtd=qtd-{$_POST['qtd']} WHERE id_imperio={$imperio->id} AND id_recurso={$_POST['id_recurso']} AND turno={$turno->turno}");
					$dados_salvos['resposta_ajax'] = "OK!";
					$dados_salvos['mensagem'] = "Recursos transferidos!";					
				} else {
					$dados_salvos['resposta_ajax'] = "Não há recursos suficientes para realizar essa ação!";
				}
			}
		} else {//É um NPC!
			$dados_salvos['resposta_ajax'] = "OK!";
			$dados_salvos['mensagem'] = "Recursos transferidos!";
		}
		
		$dados_salvos['debug'] .= "imperio_origem: {$imperio->id} | imperio_destino: {$_POST['id_imperio_destino']} || {$recursos_disponivel} >= {$_POST['qtd']}";
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta		
	}


	/***********************
	function valida_transfere_tech ()
	----------------------
	Valida o objeto desejado
	***********************/	
	function valida_transfere_tech() {
		global $wpdb; 
	
		$id_imperio_destino = $wpdb->get_var("SELECT citt.id_imperio_destino FROM
		colonization_imperio_transfere_techs AS citt
		WHERE citt.id_imperio_origem = {$_POST['id_imperio_origem']}
		AND citt.id_imperio_destino = {$_POST['id_imperio_destino']}
		AND citt.processado = false
		");
		
		if(!empty($id_imperio_destino)) {
			$imperio = new imperio($id_imperio_destino, true);
			$dados_salvos['resposta_ajax'] = "Já existe uma operação pendente! Aguarde o aceite ou declínio do {$imperio->nome}";
		}
		
		if ($_POST['id_imperio_origem'] != 0) { //Não é um NPC! Tem que validar!
			$id_transfere = $wpdb->get_var("SELECT citt.id FROM
			colonization_imperio_transfere_techs AS citt
			WHERE citt.id_imperio_origem = {$_POST['id_imperio_origem']}
			AND citt.id_imperio_destino = {$_POST['id_imperio_destino']}
			AND citt.id_tech = {$_POST['id_tech']}
			");

			if(!empty($id_transfere)) {
				$dados_salvos['resposta_ajax'] = "Você já realizou uma operação deste tipo!";
			}
			
			//Validou a ação, agora verifica se tem Pesquisa suficiente para pagar pela transferência
			$id_recurso = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='Pesquisa'");
			$imperio_recursos = new imperio_recursos($_POST['id_imperio_origem']);
			$tech = new tech($_POST['id_tech']);
			
			$chave_recurso = array_search($id_recurso, $imperio_recursos->id_recurso);
			if ($imperio_recursos->qtd[$chave_recurso] < round(0.1*$tech->custo, 0, PHP_ROUND_HALF_UP)) {
				$dados_salvos['resposta_ajax'] = "Você não tem Pesquisas suficientes para realizar a transferência!";
			}
		
			if (empty($dados_salvos['resposta_ajax'])) {
				//Validou! Pode cobrar a Pesquisa
				$custo = round(0.1*$tech->custo, 0, PHP_ROUND_HALF_UP);
				$turno = new turno();
				$wpdb->query("UPDATE colonization_imperio_recursos SET qtd=qtd-$custo WHERE id_recurso={$id_recurso} AND id_imperio={$_POST['id_imperio_origem']} AND turno={$turno->turno}");
				$dados_salvos['resposta_ajax'] = "OK!";
				$dados_salvos['mensagem'] = "Foi cobrado {$custo} Pesquisa(s) para transferir a Tech desejada!";
			}
		} else {//É um NPC!
			$dados_salvos['resposta_ajax'] = "OK!";
			$dados_salvos['mensagem'] = "Tech transferida!";
		}
		
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}



	/***********************
	function valida_tech_imperio ()
	----------------------
	Valida o objeto desejado
	***********************/	
	function valida_tech_imperio() {
		global $wpdb; 
		
		//Verifica se o Império já tem essa Tech
		$dados_salvos['debug'] = "";
		$imperio = new imperio($_POST['id_imperio']);
		if (empty($_POST['id']) || $_POST['id'] == "null") {
			$id_tech = $wpdb->get_var("SELECT id FROM colonization_imperio_techs WHERE id_imperio={$_POST['id_imperio']} AND id_tech={$_POST['id_tech']}");
			$dados_salvos['debug'] .= "SELECT id FROM colonization_imperio_techs WHERE id_imperio={$_POST['id_imperio']} AND id_tech={$_POST['id_tech']} AND id !={$_POST['id']} \n";
		} else {
			$tech_imperio = new imperio_techs($_POST['id']);
			$dados_salvos['debug'] .= "new imperio_techs (POST['id']:{$_POST['id']}) \n";
			$id_tech = $wpdb->get_var("SELECT id FROM colonization_imperio_techs WHERE id_imperio={$_POST['id_imperio']} AND id_tech={$_POST['id_tech']} AND id !={$_POST['id']}");
			$dados_salvos['debug'] .= "SELECT id FROM colonization_imperio_techs WHERE id_imperio={$_POST['id_imperio']} AND id_tech={$_POST['id_tech']} AND id !={$_POST['id']} \n";
		}
		
		$dados_salvos['confirma'] = "";
		if (!empty($id_tech)) {
			$tech_imperio = new imperio_techs($id_tech);
			$dados_salvos['debug'] .= "new imperio_techs (id_tech:{$id_tech}) \n";
			if ($tech_imperio->custo_pago == 0) {
				$dados_salvos['resposta_ajax'] = "O Império já possui essa Tech!";
				echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
				wp_die(); //Termina o script e envia a resposta
			}
		}
		
		if ($_POST['tech_permitida'] == "true") {
			$id_tech = $wpdb->get_var("SELECT id FROM colonization_imperio_techs_permitidas WHERE id_imperio={$_POST['id_imperio']} AND id_tech={$_POST['id_tech']}");
			if (!empty($id_tech)) {
				$dados_salvos['resposta_ajax'] = "O Império já está autorizado à pesquisar essa Tech!";
				echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
				wp_die(); //Termina o script e envia a resposta
			}
		}

		$tech = new tech($_POST['id_tech']);
		$dados_salvos['debug'] .= "id_tech: {$_POST['id_tech']} || somente_valida: {$_POST['somente_valida']}\n";
		$tabela_techs = "colonization_imperio_techs";
		if ($_POST['somente_valida'] === "true") {
			$tabela_techs = "(SELECT DISTINCT 1 AS id, cit.id_tech, cit.custo_pago, cit.publica, cit.id_imperio
			FROM (
			SELECT id_tech, 0 AS custo_pago, 1 AS publica, id_imperio
			FROM colonization_imperio_techs_permitidas
			UNION
			SELECT ct.id AS id_tech, 0 AS custo_pago, ct.publica, {$_POST['id_imperio']} AS id_imperio
			FROM colonization_tech AS ct
			WHERE id NOT IN (SELECT citp.id_tech FROM colonization_imperio_techs_permitidas AS citp WHERE citp.id_imperio={$_POST['id_imperio']}) AND ct.publica = 1
			) AS cit) AS cit";
		}
		
		//Verifica se o Império tem os pré-requisitos da Tech
		if (!empty($tech->id_tech_parent)) {
			$id_tech_parent = str_replace(";",",",$tech->id_tech_parent);
			$tech_parent = $wpdb->get_var("SELECT COUNT(id) FROM {$tabela_techs} WHERE id_imperio={$_POST['id_imperio']} AND id_tech IN ({$id_tech_parent}) AND custo_pago = 0");
			$dados_salvos['debug'] .= "SELECT COUNT(id) FROM {$tabela_techs} WHERE id_imperio={$_POST['id_imperio']} AND id_tech IN ({$id_tech_parent}) AND custo_pago = 0 \n";
			
			if ($tech_parent == 0) {
				$id_tech_parent = explode(",",$id_tech_parent);
				$id_tech_parent = $id_tech_parent[0];
				$tech_parent = new tech($id_tech_parent);
				$dados_salvos['resposta_ajax'] = "O Império não tem os pré-requisitos necessários! É necessário ter a Tech '{$tech_parent->nome}'\n";
			}
		}

		if (!empty($tech->lista_requisitos)) {
			foreach ($tech->id_tech_requisito as $chave => $id_requisito) {
				$tech_requisito = new tech($id_requisito);

				$tech_requisito_query = $wpdb->get_var("SELECT COUNT(id) FROM {$tabela_techs} WHERE id_imperio={$_POST['id_imperio']} AND (id_tech={$tech_requisito->id} OR id_tech={$tech_requisito->id_tech_alternativa}) AND custo_pago = 0");
				$dados_salvos['debug'] .= "SELECT COUNT(id) FROM {$tabela_techs} WHERE id_imperio={$_POST['id_imperio']} AND (id_tech={$tech_requisito->id} OR id_tech={$tech_requisito->id_tech_alternativa}) AND custo_pago = 0 \n";
				
				if ($tech_requisito_query == 0) {
					if (empty($dados_salvos['resposta_ajax'])) {
						$dados_salvos['resposta_ajax'] = "O Império não tem os pré-requisitos necessários! É necessário ter a(s) Tech(s): ";	
					}
					$dados_salvos['resposta_ajax'] .= "'{$tech_requisito->nome}'\n";
				}
			}
		}
		
		if ($_POST['somente_valida'] === "true") {
			if (empty($dados_salvos['resposta_ajax'])) {
				$dados_salvos['resposta_ajax'] = "OK!";
			}
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta
		}

		$dados_salvos['custo_pago'] = $_POST['custo_pago'];
		if ($_POST['custo_pago'] > $tech->custo) {
			$dados_salvos['resposta_ajax'] = "O custo pago por essa Tech é maior que o custo da Tech ({$tech->custo})! Favor revisar!";
			$dados_salvos['custo_pago'] = $tech->custo;
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta
		} elseif ($tech->custo == $_POST['custo_pago']) {
			$dados_salvos['custo_pago'] = 0;
		}		
		
		//Verifica se o Império tem Pesquisa suficiente
		$id_recurso_pesquisa = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='Pesquisa'");
		
		if ($tech_imperio->id != 0) {
			if ($_POST['custo_pago'] == 0 && $tech_imperio->custo_pago != 0) {
				$custo_a_pagar = $tech->custo - $tech_imperio->custo_pago;
				$dados_salvos['debug'] .= "tech->custo - tech_imperio->custo \n";
			} else {
				$custo_a_pagar = $_POST['custo_pago'] - $tech_imperio->custo_pago;
				$dados_salvos['debug'] .= "POST_custo_pago - tech_imperio->custo \n";
			}
		} else {
			if ($_POST['custo_pago'] == 0) {
				$custo_a_pagar = $tech->custo;
				$dados_salvos['debug'] .= "tech->custo \n";
			} else {
				$custo_a_pagar = $_POST['custo_pago'];
				$dados_salvos['debug'] .= "POST_custo_pago \n";
			}
		}
		$dados_salvos['debug'] .= "\$_POST['custo_pago']: {$_POST['custo_pago']} || \$tech->custo: {$tech->custo} || \$custo_a_pagar: {$custo_a_pagar} || \$_POST['tech_inicial']:{$_POST['tech_inicial']} \n";
		
		$turno = new turno();
		$pesquisas_imperio = $wpdb->get_var("SELECT qtd FROM colonization_imperio_recursos WHERE id_imperio={$_POST['id_imperio']} AND turno={$turno->turno} AND id_recurso={$id_recurso_pesquisa}");
		
		if (($pesquisas_imperio < $custo_a_pagar) && $_POST['tech_inicial'] != 1) {
			if (empty($dados_salvos['resposta_ajax'])) {
				$dados_salvos['resposta_ajax'] = "O {$imperio->nome} precisa de {$custo_a_pagar} Pesquisa(s) para concluir essa ação, porém tem apenas {$pesquisas_imperio} Pesquisas(s). {$_POST['tech_inicial']}";
				$dados_salvos['custo_pago'] = $pesquisas_imperio;
			}
		} 
		
		if (empty($dados_salvos['resposta_ajax'])) {
			$dados_salvos['resposta_ajax'] = "OK!";
			
			//Pode cobrar o custo da Tech, caso não seja uma Tech Inicial
			if ($_POST['tech_inicial'] != 1) {
				$consome_pesquisa = $wpdb->query("UPDATE colonization_imperio_recursos SET qtd=qtd-{$custo_a_pagar} WHERE id_imperio={$_POST['id_imperio']} AND turno={$turno->turno} AND id_recurso={$id_recurso_pesquisa}");
				$dados_salvos['debug'] .= "UPDATE colonization_imperio_recursos SET qtd=qtd-{$custo_a_pagar} WHERE id_imperio={$_POST['id_imperio']} AND turno={$turno->turno} AND id_recurso={$id_recurso_pesquisa} \n";
			}
			//Reseta os dados do JSON
			$wpdb->query("DELETE FROM colonization_balancos_turno WHERE turno={$turno->turno} AND id_imperio={$imperio->id}");
			$wpdb->query("DELETE FROM colonization_lista_colonias_turno WHERE turno={$turno->turno} AND id_imperio={$imperio->id}");						
		} 

		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}
	
	/***********************
	function valida_acao_admin ()
	----------------------
	Valida o objeto desejado
	***********************/	
	function valida_acao_admin() {
		global $wpdb; 
		
		$recursos = explode(";",$_POST['lista_recursos']);
		$qtds = explode(";",$_POST['qtd']);
		$dados_salvos['html'] = "";
		
		if (count($recursos) != count($qtds)) {
			$dados_salvos['resposta_ajax'] = "É necessário que a lista de recursos e de quantidades seja do mesmo tamanho!";
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta
		}
		
		if (!empty($_POST['id'])) {//É uma edição! REVERTE a ação e depois atualiza, para não ficar em duplicidade
			$recursos_original = explode(";",$_POST['lista_recursos_original']);
			$qtds_original = explode(";",$_POST['qtd_original']);
		}
		
		foreach ($recursos as $chave=>$valor) {
			if (!empty($_POST['id'])) {//É uma edição! REVERTE a ação e depois atualiza, para não ficar em duplicidade
					$dados_salvos['html'] .= "UPDATE colonization_imperio_recursos SET qtd=qtd+{$qtds_original[$chave]} WHERE id_recurso={$recursos_original[$chave]} AND id_imperio={$_POST['id_imperio']} AND turno={$_POST['turno']}<br>";
					$wpdb->query("UPDATE colonization_imperio_recursos SET qtd=qtd+{$qtds_original[$chave]} WHERE id_recurso={$recursos_original[$chave]} AND id_imperio={$_POST['id_imperio']} AND turno={$_POST['turno']}");
			}
			$qtd_atual = $wpdb->get_var("SELECT qtd FROM colonization_imperio_recursos WHERE id_recurso={$recursos[$chave]} AND id_imperio={$_POST['id_imperio']} AND turno={$_POST['turno']}");
			if ($qtd_atual < $qtds[$chave]) {
				$dados_salvos['resposta_ajax'] = "Não foi possível salvar a ação! Os recursos do Império são insuficientes! Favor revisar e tentar novamente!";
				echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
				wp_die(); //Termina o script e envia a resposta
			}
		}
		
		foreach ($recursos as $chave=>$valor) {
				$dados_salvos['html'] .= "UPDATE colonization_imperio_recursos SET qtd=qtd-{$qtds[$chave]} WHERE id_recurso={$recursos[$chave]} AND id_imperio={$_POST['id_imperio']} AND turno={$_POST['turno']}<br>";
				$wpdb->query("UPDATE colonization_imperio_recursos SET qtd=qtd-{$qtds[$chave]} WHERE id_recurso={$recursos[$chave]} AND id_imperio={$_POST['id_imperio']} AND turno={$_POST['turno']}");					
		}
		
		$dados_salvos['resposta_ajax'] = "OK!";
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}


	/***********************
	function valida_diplomacia()
	----------------------
	Valida o objeto desejado
	***********************/	
	function salva_diplomacia() {
		global $wpdb; 
		$wpdb->hide_errors();

		$resposta = $wpdb->get_var("SELECT id FROM {$_POST['tabela']} WHERE id_imperio={$_POST['id_imperio']} AND id_imperio_contato={$_POST['id_imperio_contato']} AND nome_npc='{$_POST['nome_npc']}'");
		//$dados_salvos['debug'] = "SELECT id FROM {$_POST['tabela']} WHERE id_imperio={$_POST['id_imperio']} AND id_imperio_contato={$_POST['id_imperio_contato']} AND nome_npc='{$_POST['nome_npc']}' \n";
		//$dados_salvos['debug'] .= $resposta;
		
		$imperio = new imperio($_POST['id_imperio']);
		$turno = $imperio->turno;
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		$dados_salvos['resposta_ajax'] = "";
		
		if ($roles != "administrator" && $_POST['id_imperio'] != $imperio->id) {
			$dados_salvos['resposta_ajax'] = "Você não está autorizado a realizar esta operação!";
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die();
		}
		
		if (empty($resposta)) {//Não existe o ponto, pode adicionar
			if ($_POST['acordo_comercial'] == 1) {
				$dados_salvos['resposta_ajax'] = "É necessário realizar o PRIMEIRO CONTATO antes de subir o nível das relações diplomáticas!";
			} else {
				$resposta = $wpdb->query("INSERT INTO {$_POST['tabela']} SET id_imperio={$_POST['id_imperio']}, id_imperio_contato={$_POST['id_imperio_contato']}, nome_npc='{$_POST['nome_npc']}', acordo_comercial=false");
				//Reseta os dados do JSON
				$wpdb->query("DELETE FROM colonization_balancos_turno WHERE turno={$turno->turno} AND id_imperio={$_POST['id_imperio']}");
				$wpdb->query("DELETE FROM colonization_lista_colonias_turno WHERE turno={$turno->turno} AND id_imperio={$_POST['id_imperio']}");
			}
		} else {//Temos que atualizar, pois é uma mudança de acordo_comercial (ou outra variável futura)
			$resposta = $wpdb->query("UPDATE {$_POST['tabela']} SET acordo_comercial={$_POST['acordo_comercial']} WHERE id={$resposta}");
			//Reseta os dados do JSON
			$wpdb->query("DELETE FROM colonization_balancos_turno WHERE turno={$turno->turno} AND id_imperio={$_POST['id_imperio']}");
			$wpdb->query("DELETE FROM colonization_lista_colonias_turno WHERE turno={$turno->turno} AND id_imperio={$_POST['id_imperio']}");
		}

		if ($dados_salvos['resposta_ajax'] == "") {
			$dados_salvos['resposta_ajax'] = "OK!";
		}

		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}

	/***********************
	function valida_reabastecimento()
	----------------------
	Valida o objeto desejado
	***********************/	
	function valida_reabastecimento() {
		global $wpdb; 
		$wpdb->hide_errors();

		$resposta = $wpdb->get_var("SELECT id FROM {$_POST['tabela']} WHERE id_estrela={$_POST['id_estrela']} AND id_imperio={$_POST['id_imperio']}");
		$dados_salvos['debug'] = $resposta;
		
		$id_jogador = get_current_user_id();
		$id_imperio_autorizando = $wpdb->get_var("SELECT id FROM colonization_imperio WHERE id_jogador=".$id_jogador);

		$id_colonia = $wpdb->get_var("
		SELECT MAX(cic.id)
		FROM colonization_imperio_colonias AS cic
		JOIN colonization_planeta AS cp
		ON cp.id = cic.id_planeta
		WHERE cic.id_imperio = {$id_imperio_autorizando}
		AND cp.id_estrela = {$_POST['id_estrela']}
		");

		$imperio = new imperio($_POST['id_imperio'],true);
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		if ($roles != "administrator" && $id_colonia == null) {
			$dados_salvos['resposta_ajax'] = "Você não está autorizado a realizar esta operação!";
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die();
		}
		
		//Só é possível criar um Ponto de Reabastecimento em Impérios que já sejam conhecidos (ou seja, que tenha o Primeiro Contato)
		//Pega todos os planetas e colônias na estrela
		$ids_colonias = $wpdb->get_results("
		SELECT DISTINCT cic.id_imperio, cic.nome_npc
		FROM colonization_imperio_colonias AS cic
		JOIN colonization_planeta AS cp
		ON cp.id = cic.id_planeta
		WHERE cp.id_estrela = {$_POST['id_estrela']}
		");
		
		foreach ($ids_colonias as $ids_colonia_reabastece) {
			if ($ids_colonia_reabastece->id_imperio != 0) {
				 $ids_colonia_reabastece->nome_npc = "";
			}
			if ($_POST['id_imperio'] == $ids_colonia_reabastece->id_imperio) {
				break;
			}
			$contato_imperio = $wpdb->get_var("SELECT id FROM colonization_diplomacia WHERE id_imperio={$_POST['id_imperio']} AND nome_npc='{$ids_colonia_reabastece->nome_npc}' AND id_imperio_contato={$ids_colonia_reabastece->id_imperio}");
			$dados_salvos['debug'] = "\nSELECT id FROM colonization_diplomacia WHERE id_imperio={$_POST['id_imperio']} AND nome_npc='{$ids_colonia_reabastece->nome_npc}' AND id_imperio_contato={$ids_colonia_reabastece->id_imperio}";			
			if (!empty($contato_imperio)) {
				break;
			}
		}
		
		if (!empty($contato_imperio) || ($_POST['id_imperio'] == $ids_colonia_reabastece->id_imperio)) {
			if (empty($resposta)) {//Não existe o ponto, pode adicionar
				$resposta = $wpdb->query("INSERT INTO {$_POST['tabela']} SET id_estrela={$_POST['id_estrela']}, id_imperio={$_POST['id_imperio']}");
			} else {//Existe o ponto, é para remover
				$resposta = $wpdb->query("DELETE FROM {$_POST['tabela']} WHERE id_estrela={$_POST['id_estrela']} AND id_imperio={$_POST['id_imperio']}");
			}
		} else {
			$dados_salvos['resposta_ajax'] = "É necessário primeiro realizar o Primeiro Contato com o Império antes de criar um Ponto de Reabastecimento!";

			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a 			
		}

		//Reseta os dados do JSON
		$wpdb->query("DELETE FROM colonization_balancos_turno WHERE turno={$turno->turno} AND id_imperio={$imperio->id}");
		$wpdb->query("DELETE FROM colonization_lista_colonias_turno WHERE turno={$turno->turno} AND id_imperio={$imperio->id}");
		$dados_salvos['resposta_ajax'] = "OK!";

		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}

	/***********************
	function valida_colonia ()
	----------------------
	Valida o objeto desejado
	***********************/	
	function valida_colonia($resposta_ajax = true) {
		global $wpdb; 
		$wpdb->hide_errors();
		
		$dados_salvos['resposta_ajax'] = "OK!";
		$dados_salvos['debug'] = "";
		$planeta = new planeta($_POST['id_planeta']);
		$planeta->popula_instalacoes_planeta();
		$estrela = new estrela($planeta->id_estrela);
		$imperio = new imperio($_POST['id_imperio']);
		$id_existe = "";
		if ($_POST['id'] != "") {//Se o valor estiver em branco, é um novo objeto.
			$id_existe = " AND id != {$_POST['id']}";			
		}
		
		$resposta = $wpdb->get_var("SELECT id FROM colonization_imperio_colonias WHERE id_planeta = {$_POST['id_planeta']} AND turno = {$_POST['turno']}{$id_existe}");
		if (!empty($resposta)) {
			$dados_salvos['resposta_ajax'] = "Este planeta já é a colônia de outro Império!";
		}

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}

		if ($_POST['id_imperio'] != 0) {
			$estrelas_colonias_imperio = $wpdb->get_results("
			SELECT DISTINCT cp.id_estrela
			FROM colonization_imperio_colonias AS cic
			JOIN colonization_planeta AS cp
			ON cp.id=cic.id_planeta
			JOIN colonization_estrela AS ce
			ON ce.id=cp.id_estrela
			WHERE cic.id_imperio={$_POST['id_imperio']} AND cic.turno={$_POST['turno']}
			AND cic.vassalo=0
			");
			
			$dados_salvos['debug'] .= "
			SELECT DISTINCT cp.id_estrela
			FROM colonization_imperio_colonias
			JOIN colonization_planeta AS cp
			ON cp.id=cic.id_planeta
			JOIN colonization_estrela AS ce
			ON ce.id=cp.id_estrela
			WHERE cic.id_imperio={$_POST['id_imperio']} AND cic.turno={$_POST['turno']}
			AND cic.vassalo=0
			\n";
			
			$colonia_dentro_logistica = false;
			foreach ($estrelas_colonias_imperio as $dados_colonia) {
				$dados_salvos['debug'] .= "distância: {$estrela->distancia_estrela($dados_colonia->id_estrela)} || {$imperio->logistica}\n";
				if ($estrela->distancia_estrela($dados_colonia->id_estrela) <= $imperio->logistica) {
					$colonia_dentro_logistica = true;
					break;
				}
			}
			
			if (!$colonia_dentro_logistica && $_POST['vassalo'] != 1 && count($estrelas_colonias_imperio) != 0) {
				$dados_salvos['resposta_ajax'] = "Esta estrela está além do Alcance Logístico do Império!";
			}
			
			$dados_salvos['debug'] .= "estrelas->colonias_na_estrela: " . count($estrela->colonias_na_estrela()) . "\n";
			if (count($estrela->colonias_na_estrela()) == 0) {//É a primeira colônia no sistema!
				$nave_no_sistema = $wpdb->get_var("SELECT COUNT(id) FROM colonization_imperio_frota WHERE X={$estrela->X} AND Y={$estrela->Y} AND Z={$estrela->Z} AND id_imperio={$_POST['id_imperio']}");
				$dados_salvos['debug'] .= "SELECT COUNT(id) FROM colonization_imperio_frota WHERE X={$estrela->X} AND Y={$estrela->Y} AND Z={$estrela->Z} AND id_imperio={$_POST['id_imperio']}\n";
				if ($nave_no_sistema == 0 && $roles != "administrator") {
					$dados_salvos['resposta_ajax'] = "É necessário ter uma nave no sistema para permitir sua Colonização!";
				}
				
				if ($dados_salvos['resposta_ajax'] == "OK!") {
					$id_espacoporto = $wpdb->get_var("SELECT id FROM colonization_instalacao WHERE nome='Espaçoporto'");
					$espacoporto = new instalacao($id_espacoporto);
					$custos = explode(";",$espacoporto->custos);
					
					$html = "";
					foreach ($custos as $custo) {
						$dados_custo = explode("=",$custo);
						if ($imperio->pega_qtd_recurso_imperio($dados_custo[0]) < $dados_custo[1]) {
							$dados_salvos['resposta_ajax'] = "O Império não tem recursos suficientes para instalar um Espaçoporto nessa Colônia!";
						}
					}
				}
			}
		}

		if ($_POST['capital'] == 1) {
			if ($_POST['id_imperio'] == 0) {
				$resposta = $wpdb->get_var("SELECT id FROM colonization_imperio_colonias WHERE capital=1 AND nome_npc='{$_POST['nome_npc']}' AND turno={$_POST['turno']}{$id_existe}");
			} else {
				$resposta = $wpdb->get_var("SELECT id FROM colonization_imperio_colonias WHERE capital=1 AND id_imperio={$_POST['id_imperio']} AND turno={$_POST['turno']}{$id_existe}");
			}
			
			if (!empty($resposta)) {
				$dados_salvos['resposta_ajax'] = "Já existe uma Capital para este Império!";
			}		
		}
		
		if (($planeta->inospito == 1 && $planeta->terraforma == 0) && $imperio->coloniza_inospito != 1) {
			if ($_POST['pop'] > $planeta->pop_inospito) {
				$dados_salvos['resposta_ajax'] = "Este planeta é inóspito! O máximo de Pop que ele suporta é {$planeta->pop_inospito} Pop";
				$dados_salvos['debug'] .= $planeta->debug;
			}
		}
		
		if ($resposta_ajax === false) {
			return $dados_salvos['resposta_ajax'];		
		}

		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}

	/***********************
	function transfere_pop ()
	----------------------
	Valida e realiza a transferência de Pop de uma colônia para outra
	***********************/	
	function transfere_pop() {
		global $wpdb; 
		$wpdb->hide_errors();
		
		$dados_salvos['resposta_ajax'] = "SALVO!";
		
		$colonia_origem = new colonia($_POST['id_colonia_origem']);
		$colonia_destino = new colonia($_POST['id_colonia_destino']);
		$planeta = new planeta($colonia_destino->id_planeta);
		$planeta->popula_instalacoes_planeta();
		$estrela_destino = new estrela($planeta->id_estrela);
		$imperio = new imperio($_POST['id_imperio']);
		
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}		
		
		if ($estrela_destino->cerco) {
			$dados_salvos['resposta_ajax'] = "O sistema está sitiado e não pode receber Pop no momento.";
		}
		
		if (($planeta->inospito == 1 && $planeta->terraforma == 0) && $imperio->coloniza_inospito != 1) {
			if (($colonia_destino->pop + $_POST['pop']) > $planeta->pop_inospito) {
				$dados_salvos['resposta_ajax'] = "O planeta de destino é inóspito! O máximo de Pop que ele suporta é {$planeta->pop_inospito} Pop";
			}
		} elseif (($colonia_origem->vassalo == 1 || $colonia_destino->vassalo == 1) && $roles != "administrator") {
			$dados_salvos['resposta_ajax'] = "Somente o Admin pode transferir Pop entre Colônias de Vassalos.";
		}

		if ($dados_salvos['resposta_ajax'] == "SALVO!") {
			$sem_balanco = true;
			$imperio->acoes = new acoes($_POST['id_imperio'],$_POST['turno'],$sem_balanco);
			$id_instalacao = 0;
			$salva_balanco = false;
			$imperio->acoes->pega_balanco_recursos($id_instalacao, $salva_balanco, $imperio);
			if ($colonia_origem->pop >= $_POST['pop']) {
				$resultado = $wpdb->query("UPDATE colonization_imperio_colonias SET pop=pop-{$_POST['pop']} WHERE id={$colonia_origem->id}");
				$resultado = $wpdb->query("UPDATE colonization_imperio_colonias SET pop=pop+{$_POST['pop']} WHERE id={$colonia_destino->id}");
			} else {
				$dados_salvos['resposta_ajax'] = "Não há Pop suficiente na Colônia de origem para que essa transferência seja efetuada!";
				echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
				wp_die(); //Termina o script e envia a resposta
			}
			
			$colonias = [];
			$colonias[0] = $colonia_origem->id;
			$colonias[1] = $colonia_destino->id;
			$produtos_acao = $this->produtos_acao($imperio, 0, $colonias);
			//$debug .= $produtos_acao['debug'];
			$produtos_acao['debug'] = $imperio->acoes->debug . "\n" . $produtos_acao['debug'];
			$dados_salvos = array_merge($dados_salvos, $produtos_acao);
		}

		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}


	/***********************
	function valida_estrela ()
	----------------------
	Valida o objeto desejado
	***********************/	
	function valida_estrela() {
		global $wpdb; 
		$wpdb->hide_errors();

		if ($_POST['id'] == "") {//Se o valor estiver em branco, é um novo objeto.
			$query = "SELECT id FROM colonization_estrela WHERE X={$_POST['X']} AND Y={$_POST['Y']} AND Z={$_POST['Z']}";
		} else {
			$query = "SELECT id FROM colonization_estrela WHERE X={$_POST['X']} AND Y={$_POST['Y']} AND Z={$_POST['Z']} AND id != {$_POST['id']}";		
		}
		
		$resposta = $wpdb->query($query);

		if ($resposta === 0) {
			$dados_salvos['resposta_ajax'] = "OK!";
		} else {
			$dados_salvos['resposta_ajax'] = "Já existe uma estrela nestas coordenadas!";
		}

		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}

	/***********************
	function valida_instalacao_recurso ()
	----------------------
	Valida o objeto desejado
	***********************/	
	function valida_instalacao_recurso() {
		global $wpdb; 
		$wpdb->hide_errors();

		if ($_POST['id'] == "") {//Se o valor estiver em branco, é um novo objeto.
			$query = "SELECT id FROM colonization_instalacao_recursos WHERE id_recurso={$_POST['id_recurso']} AND id_instalacao={$_POST['id_instalacao']} AND consome={$_POST['consome']}";
		} else {
			$query = "SELECT id FROM colonization_instalacao_recursos WHERE id_recurso={$_POST['id_recurso']} AND id_instalacao={$_POST['id_instalacao']} AND consome={$_POST['consome']} AND id != {$_POST['id']}";
		}
		$dados_salvos['debug'] = $query;
		$resposta = $wpdb->query($query);

		if ($resposta === 0) {
			$dados_salvos['resposta_ajax'] = "OK!";
		} else {
			$dados_salvos['resposta_ajax'] .= "Você não pode cadastrar o mesmo recurso para a mesma instalação duas vezes!";
		}

		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}
	
	/***********************
	function altera_recursos_planeta ()
	----------------------
	Atualiza os recursos de um planeta quando houve alteração do id_recurso
	***********************/	
	function altera_recursos_planeta() {
		global $wpdb; 
		$wpdb->hide_errors();

		$resposta = 0;
		if (!empty($_POST['id_recurso_original'])) {
			$query = "UPDATE colonization_planeta_recursos SET id_recurso={$_POST['id_recurso']} WHERE id_recurso={$_POST['id_recurso_original']} AND id_planeta={$_POST['id_planeta']}";
			$resposta = $wpdb->query($query);
		}

		if ($resposta === 0) {
			$dados_salvos['resposta_ajax'] = "OK!";
		} else {
			$dados_salvos['resposta_ajax'] .= $query;
		}

		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}

	/***********************
	function valida_planeta_recurso ()
	----------------------
	Valida o objeto desejado
	***********************/	
	function valida_planeta_recurso() {
		global $wpdb; 
		$wpdb->hide_errors();

		if ($_POST['id'] == "") {//Se o valor estiver em branco, é um novo objeto.
			$query = "SELECT id FROM colonization_planeta_recursos WHERE id_recurso={$_POST['id_recurso']} AND id_planeta={$_POST['id_planeta']} AND turno={$_POST['turno']}";
		} else {
			$query = "SELECT id FROM colonization_planeta_recursos WHERE id_recurso={$_POST['id_recurso']} AND id_planeta={$_POST['id_planeta']} AND turno={$_POST['turno']} AND id != {$_POST['id']}";
		}
		
		$resposta = $wpdb->query($query);

		if ($resposta === 0) {
			$dados_salvos['resposta_ajax'] = "OK!";
		} else {
			$dados_salvos['resposta_ajax'] .= "Você não pode cadastrar o mesmo recurso para a mesma colônia duas vezes! -- SELECT id FROM colonization_planeta_recursos WHERE id_recurso={$_POST['id_recurso']} AND id_planeta={$_POST['id_planeta']} AND turno={$_POST['turno']} AND id != {$_POST['id']}";
		}

		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}

	/***********************
	function valida_colonia_instalacao ()
	----------------------
	Valida o objeto desejado
	***********************/	
	function valida_colonia_instalacao($resposta_ajax = true) {
		global $wpdb; 
		// Report all PHP errors
		$wpdb->hide_errors();
		//error_reporting(E_ALL); 
		//ini_set("display_errors", 1);
		
		$dados_salvos['debug'] = "";
		
		$turno = new turno();
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
			$imperio = new imperio();
		}

		$planeta = new planeta($_POST['id_planeta']);
		$planeta->popula_instalacoes_planeta();
		$estrela = new estrela($planeta->id_estrela);
		$id_colonia = $wpdb->get_var("SELECT id FROM colonization_imperio_colonias WHERE id_planeta={$planeta->id} AND turno={$turno->turno}");
		$colonia = new colonia($id_colonia);

		if ($roles != "administrator" && ($imperio->id != $colonia->id_imperio)) {
			$dados_salvos['resposta_ajax'] = "Você não está autorizado a realizar esta operação! {$imperio->id}:{$colonia->id_imperio}";
			
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta
		}
		
		if ($turno->encerrado == 1 && $roles != "administrator") {
			$dados_salvos['resposta_ajax'] = "O Turno {$turno->turno} foi ENCERRADO e não pode mais receber alterações!";
		
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta
		}
		
		if ($estrela->cerco && $roles != "administrator") {
			$dados_salvos['resposta_ajax'] = "O sistema está sitiado e não pode receber alterações nas Instalações nesse momento.";
			
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta
		}
		
		$imperio = new imperio($colonia->id_imperio);
		$instalacao = new instalacao($_POST['id_instalacao']);
		
		$nivel_original = 0;
		if ($_POST['id'] != "") {//Se o valor estiver em branco, é um novo objeto.
			//Realiza a atualização do histórico de upgrades
			$colonia_instalacao = new colonia_instalacao($_POST['id']);
			$nivel_original = $colonia_instalacao->nivel;
			$instalacao_original = new instalacao($colonia_instalacao->id_instalacao);

			if ($_POST['nivel'] != $colonia_instalacao->nivel || $_POST['id_instalacao'] != $colonia_instalacao->id_instalacao) {//É uma atualização! Pode ser um upgrade ou um downgrade
				if ($_POST['id_instalacao'] != $colonia_instalacao->id_instalacao && ($instalacao_original->espacoporto || $instalacao_original->base_colonial)) {
					$dados_salvos['resposta_ajax'] = "Não é possível substituir um Espaçoporto ou Base Colonial por outro tipo de Instalação!";
					
					echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
					wp_die(); //Termina o script e envia a resposta
				}
				
				$turno_upgrade = $wpdb->get_var("SELECT MAX(turno) FROM colonization_planeta_instalacoes_upgrade WHERE id_planeta_instalacoes = {$_POST['id']}");
				if ($turno->turno != $turno_upgrade) {
					//Já salvou um upgrade, mantém o valor antigo pois qualquer alteração nova será feita para o Turno ATUAL
					//$wpdb->query("UPDATE colonization_planeta_instalacoes_upgrade SET nivel_anterior={$colonia_instalacao->nivel}, id_instalacao_anterior={$colonia_instalacao->id_instalacao} WHERE id_planeta_instalacoes={$_POST['id']} AND turno={$turno->turno}");
					$wpdb->query("INSERT INTO colonization_planeta_instalacoes_upgrade SET nivel_anterior={$colonia_instalacao->nivel}, id_instalacao_anterior={$colonia_instalacao->id_instalacao}, id_planeta_instalacoes={$_POST['id']}, turno={$turno->turno}");
				}
				
				$aumento_de_slot = 0;
				if ($_POST['id_instalacao'] != $colonia_instalacao->id_instalacao) { //É uma ATUALIZAÇÃO da Instalação. Precisa verificar se tem espaço
					$instalacao_antiga = new instalacao($colonia_instalacao->id_instalacao);
					$instalacao_nova = new instalacao($_POST['id_instalacao']);
					if ($instalacao_nova->slots > $instalacao_antiga->slots) {
						$aumento_de_slot = $instalacao_nova->slots - $instalacao_antiga->slots;
					}
				}
				
				if ($instalacao->limite > 0) {
					$instalacoes_no_planeta = $wpdb->get_var("
					SELECT COUNT(cpi.id)
					FROM colonization_planeta_instalacoes AS cpi
					JOIN colonization_imperio_colonias AS cic
					ON cic.id_planeta = cpi.id_planeta
					AND cic.turno = {$turno->turno}
					WHERE cpi.id_instalacao = {$instalacao->id}
					AND cic.id_imperio = {$imperio->id}
					AND cpi.id_planeta = {$planeta->id}
					AND cpi.turno <= {$turno->turno}
					AND (cpi.turno_desmonta = 0 OR cpi.turno_desmonta IS NULL)
					");
					
					$instalacoes_no_planeta = $instalacoes_no_planeta + $aumento_de_slot;
					
					if ($instalacao->limite <= $instalacoes_no_planeta && empty($_POST['id'])) {
							$texto_limite = "{$instalacao->limite} Instalações";
						if ($instalacao->limite == 1) {
							$texto_limite = "uma Instalação";
						}
						$dados_salvos['resposta_ajax'] .= "Não é possível construir outro(a) {$instalacao->nome}. O limite é de {$texto_limite}.";
					}
				}
			}

			//Atualiza a ação relativa à esta Instalação, reduzindo a Pop ou desativando
			$fator = floor(($colonia_instalacao->nivel/$_POST['nivel'])*100)/100;
			$dados_salvos['debug'] .= "{$instalacao->desguarnecida} && {$instalacao->pode_desativar} \n";
			if ($instalacao->desguarnecida == 1 && $instalacao->pode_desativar == 1) {
				$id_energia = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome = 'Energia'");
				$chave_recurso = array_search($id_energia,$instalacao->recursos_produz);
				if ($chave_recurso === false && !(($planeta->inospito == 1 && ($instalacao->pop_inospito || $instalacao->terraforma) && $colonia->pop != 0))) {
					//Produtoras de Energia não podem ser desativadas, nem Instalações que suportam Pop em Planetas Inóspitos
					//$wpdb->query("UPDATE colonization_acoes_turno SET pop=0, desativado=1 WHERE id_planeta_instalacoes={$_POST['id']} AND turno={$turno->turno}");
					//$dados_salvos['debug'] .= "UPDATE colonization_acoes_turno SET pop=0, desativado=1 WHERE id_planeta_instalacoes={$_POST['id']} AND turno={$turno->turno}\n";
				}
			} else {
				$wpdb->query("UPDATE colonization_acoes_turno SET pop=floor(pop*{$fator}) WHERE id_planeta_instalacoes={$_POST['id']} AND turno={$turno->turno}");
				$dados_salvos['debug'] .= "UPDATE colonization_acoes_turno SET pop=floor(pop*{$fator}) WHERE id_planeta_instalacoes={$_POST['id']} AND turno={$turno->turno} \n";
			}
			
		} else {
			//Verifica se é a primeira Instalação da Colônia. Se for, TEM que ser um Espaçoporto ou uma Base Colonial
			if ($imperio->id != 0) {
				$id_espacoporto = $wpdb->get_var("SELECT id FROM colonization_instalacao WHERE nome LIKE 'Espaçoporto%'");
				$id_base_colonial = $wpdb->get_var("SELECT id FROM colonization_instalacao WHERE nome LIKE 'Base Colonial%'");
				
				$tem_espacoporto = $wpdb->get_var("
				SELECT cpi.id
				FROM colonization_planeta_instalacoes AS cpi
				WHERE cpi.id_planeta IN (SELECT cp.id FROM colonization_planeta AS cp WHERE cp.id_estrela = {$planeta->id_estrela}) 
				AND turno <={$turno->turno} AND (turno_destroi = 0 OR turno_destroi IS NULL) AND cpi.id_instalacao = {$id_espacoporto}
				");
				
				$dados_salvos['debug'] .= "Query tem_espacoporto ({$tem_espacoporto}): SELECT cpi.id
				FROM colonization_planeta_instalacoes AS cpi
				WHERE cpi.id_planeta IN (SELECT cp.id FROM colonization_planeta AS cp WHERE cp.id_estrela = {$planeta->id_estrela}) 
				AND turno <={$turno->turno} AND (turno_destroi = 0 OR turno_destroi IS NULL) AND cpi.id_instalacao = {$id_espacoporto}\n";
				
				$tem_espacoporto_na_colonia = $wpdb->get_var("
				SELECT cpi.id
				FROM colonization_planeta_instalacoes AS cpi
				WHERE cpi.id_planeta IN (SELECT cp.id FROM colonization_planeta AS cp WHERE cp.id_estrela = {$planeta->id_estrela}) 
				AND turno <={$turno->turno} AND (turno_destroi = 0 OR turno_destroi IS NULL) AND cpi.id_instalacao = {$id_espacoporto}
				AND cpi.id_planeta = {$colonia->id_planeta}
				");
				
				$tem_base_estelar_na_colonia = $wpdb->get_var("
				SELECT cpi.id
				FROM colonization_planeta_instalacoes AS cpi
				WHERE cpi.id_planeta IN (SELECT cp.id FROM colonization_planeta AS cp WHERE cp.id_estrela = {$planeta->id_estrela}) 
				AND turno <={$turno->turno} AND (turno_destroi = 0 OR turno_destroi IS NULL) AND cpi.id_instalacao = {$id_base_colonial}
				AND cpi.id_planeta = {$colonia->id_planeta}
				");

				if ($instalacao->espacoporto && !empty($tem_espacoporto)) {
					$dados_salvos['resposta_ajax'] .= "Não é possível construir outro Espaçoporto. Só é possível ter um por Sistema Estelar.";
				} elseif ($instalacao->base_colonial && empty($tem_espacoporto)) {
					$dados_salvos['resposta_ajax'] .= "É necessário ter um Espaçoporto no Sistema Estelar antes de poder construir uma Base Colonial.";
				} elseif (!empty($tem_espacoporto_na_colonia) && $instalacao->base_colonial) {
					$dados_salvos['resposta_ajax'] .= "Não é possível criar uma Base Colonial em um planeta que já tem um Espaçoporto.";
				} elseif (empty($tem_espacoporto) && !$instalacao->espacoporto && $colonia->vassalo == 0) {
					$dados_salvos['resposta_ajax'] .= "A primeira Instalação de um Sistema Estelar precisa ser, necessariamente, um Espaçoporto.";
				} elseif (!empty($tem_espacoporto) && empty($tem_base_estelar_na_colonia) && empty($tem_espacoporto_na_colonia) && !$instalacao->base_colonial && $colonia->vassalo == 0) {
					$dados_salvos['resposta_ajax'] .= "A primeira Instalação de uma Colônia num Sistema Estelar que tenha um Espaçoporto precisa ser, necessariamente, uma Base Colonial.";
				}
			}			
		}
		
		$nivel = 1;
		$tech_requisito[$nivel] = new tech($instalacao->id_tech); //Pega todos os níveis de Tech
		while ($tech_requisito[$nivel]->id != 0) {
			$id_tech_child = $wpdb->get_var("SELECT id FROM colonization_tech 
			WHERE id_tech_parent={$tech_requisito[$nivel]->id} 
			OR id_tech_parent LIKE '{$tech_requisito[$nivel]->id};%'
			OR id_tech_parent LIKE '%;{$tech_requisito[$nivel]->id};%'
			OR id_tech_parent LIKE '%;{$tech_requisito[$nivel]->id}'");
			
			$dados_salvos['debug'] .= "SELECT id FROM colonization_tech 
			WHERE id_tech_parent={$tech_requisito[$nivel]->id} 
			OR id_tech_parent LIKE '{$tech_requisito[$nivel]->id};%'
			OR id_tech_parent LIKE '%;{$tech_requisito[$nivel]->id};%'
			OR id_tech_parent LIKE '%;{$tech_requisito[$nivel]->id}' \n";
			
			$nivel++;
			if (!empty($id_tech_child)) {
				$tech_requisito[$nivel] = new tech($id_tech_child);
			} else {
				$tech_requisito[$nivel] = new tech(0);
			}
		}
		$tech_requisito[$nivel] = "";
		
		$dados_salvos['confirma'] = "";
		//Verifica se o Império tem os Pré-Requisitos
		if ($imperio->id != 0) {//É um jogador
			if (!empty($tech_requisito[$_POST['nivel']])) {
				for ($nivel_tech = 1; $nivel_tech <= $_POST['nivel']; $nivel_tech++) {
					$id_tech_imperio = $wpdb->get_var("SELECT id FROM colonization_imperio_techs WHERE id_imperio={$imperio->id} AND (id_tech={$tech_requisito[$nivel_tech]->id} OR id_tech={$tech_requisito[$nivel_tech]->id_tech_alternativa}) AND custo_pago=0");
					$dados_salvos['debug'] .= "SELECT id FROM colonization_imperio_techs WHERE id_imperio={$imperio->id} AND (id_tech={$tech_requisito[$nivel_tech]->id} OR id_tech={$tech_requisito[$nivel_tech]->id_tech_alternativa}) AND custo_pago=0 \n";
					if (empty($id_tech_imperio)) {
						$dados_salvos['resposta_ajax'] = "O {$imperio->nome} NÃO tem a Tech '{$tech_requisito[$nivel_tech]->nome}'.";
						break;
					}
				}
			} else {
				$dados_salvos['resposta_ajax'] = "Não existe Tech que permita esse nível de Instalação.";
			}

			if ($instalacao->especiais != "") {
				$especiais = explode(";",$instalacao->especiais);
				
				//Especiais: tech_requisito=id_tech
				//Requer uma Tech especial como requisito

				$tech_requisito = array_values(array_filter($especiais, function($value) {
					return strpos($value, 'tech_requisito') !== false;
				}));
				
				if (!empty($tech_requisito)) {
					$valor_tech_requisito = explode("=",$tech_requisito[0]);
					$id_tech_requisito = $valor_tech_requisito[1];
					$tech_requisito = new tech ($id_tech_requisito);
					
					$id_tech_imperio = $wpdb->get_var("SELECT id FROM colonization_imperio_techs WHERE id_imperio={$imperio->id} AND (id_tech={$tech_requisito->id} OR id_tech={$tech_requisito->id_tech_alternativa}) AND custo_pago=0");
					$dados_salvos['debug'] .= "SELECT id FROM colonization_imperio_techs WHERE id_imperio={$imperio->id} AND (id_tech={$tech_requisito->id} OR id_tech={$tech_requisito->id_tech_alternativa}) AND custo_pago=0 \n";
					if (empty($id_tech_imperio)) {
						$dados_salvos['resposta_ajax'] = "O {$imperio->nome} NÃO tem a Tech Requisito '{$tech_requisito->nome}'.";
					}
				}
			}

			if ($instalacao->autonoma == 0 && ($planeta->inospito == 1 && $planeta->terraforma == 0)) {
				$dados_salvos['resposta_ajax'] = "Este tipo de Instalação só pode ser instalado em planetas habitáveis!";
			}
			
			if ($instalacao->somente_gigante_gasoso && $planeta->classe != "Gigante Gasoso") {
				$dados_salvos['resposta_ajax'] = "Este tipo de Instalação só pode ser instalado em um Gigante Gasoso!";
			} 
			
			if ($instalacao->somente_ana_branca && $estrela->tipo != "Anã Branca") {
				$dados_salvos['resposta_ajax'] = "Este tipo de Instalação só pode ser instalado em um sistema que tenha uma Estrela Anã Branca!";
			} 			
			
			$dados_salvos['debug'] .= "{$planeta->classe} && {$instalacao->somente_gigante_gasoso} && {$instalacao->slots}\n";
			if ($planeta->classe == "Gigante Gasoso" && $instalacao->somente_gigante_gasoso == false && $instalacao->slots > 0) {
				$dados_salvos['resposta_ajax'] = "Este tipo de Instalação não pode ser construído num Gigante Gasoso!";
			}

			if ($_POST['id'] == "" && (($planeta->instalacoes + $instalacao->slots) > $planeta->tamanho())) {
				$dados_salvos['resposta_ajax'] .= "Este planeta já atingiu o número máximo de instalações! Destrua uma instalação antes de criar outra!";
			}

			if ($_POST['id'] == "" && $instalacao->limite > 0) {
				$instalacoes_no_planeta = $wpdb->get_var("
				SELECT COUNT(cpi.id)
				FROM colonization_planeta_instalacoes AS cpi
				JOIN colonization_imperio_colonias AS cic
				ON cic.id_planeta = cpi.id_planeta
				AND cic.turno = {$turno->turno}
				WHERE cpi.id_instalacao = {$instalacao->id}
				AND cic.id_imperio = {$imperio->id}
				AND cpi.id_planeta = {$planeta->id}
				AND cpi.turno <= {$turno->turno}
				AND (cpi.turno_desmonta = 0 OR cpi.turno_desmonta IS NULL)
				");
				
				$dados_salvos['debug'] .= "SELECT COUNT(cpi.id)
				FROM colonization_planeta_instalacoes AS cpi
				JOIN colonization_imperio_colonias AS cic
				ON cic.id_planeta = cpi.id_planeta
				AND cic.turno = {$turno->turno}
				WHERE cpi.id_instalacao = {$instalacao->id}
				AND cic.id_imperio = {$imperio->id}
				AND cpi.id_planeta = {$planeta->id}
				AND cpi.turno <= {$turno->turno}
				AND (cpi.turno_desmonta = 0 OR cpi.turno_desmonta IS NULL)
				";
				
				if ($instalacao->limite <= $instalacoes_no_planeta) {
						$texto_limite = "{$instalacao->limite} Instalações";
					if ($instalacao->limite == 1) {
						$texto_limite = "uma Instalação";
					}
					$dados_salvos['resposta_ajax'] .= "Não é possível construir outro(a) {$instalacao->nome}. O limite é de {$texto_limite} por Planeta.\n";
				}
			}

			if ($_POST['id'] == "" && $instalacao->limite_sistema > 0) {
				$instalacoes_no_sistema = $wpdb->get_var("
				SELECT COUNT(cpi.id)
				FROM colonization_planeta_instalacoes AS cpi
				JOIN colonization_imperio_colonias AS cic
				ON cic.id_planeta = cpi.id_planeta
				AND cic.turno = {$turno->turno}
				JOIN colonization_planeta AS cp
				ON cp.id = cic.id_planeta
				WHERE cpi.id_instalacao = {$instalacao->id}
				AND cic.id_imperio = {$imperio->id}
				AND cp.id_estrela = {$planeta->id_estrela}
				AND cpi.turno <= {$turno->turno}
				AND (cpi.turno_desmonta = 0 OR cpi.turno_desmonta IS NULL)
				");
				
				$dados_salvos['debug'] .= "SELECT COUNT(cpi.id)
				FROM colonization_planeta_instalacoes AS cpi
				JOIN colonization_imperio_colonias AS cic
				ON cic.id_planeta = cpi.id_planeta
				AND cic.turno = {$turno->turno}
				JOIN colonization_planeta AS cp
				ON cp.id = cic.id_planeta
				WHERE cpi.id_instalacao = {$instalacao->id}
				AND cic.id_imperio = {$imperio->id}
				AND cp.id_estrela = {$planeta->id_estrela}
				AND cpi.turno <= {$turno->turno}
				AND (cpi.turno_desmonta = 0 OR cpi.turno_desmonta IS NULL)\n";
				
				if ($instalacao->limite_sistema <= $instalacoes_no_sistema) {
						$texto_limite = "{$instalacao->limite_sistema} Instalações";
					if ($instalacao->limite_sistema == 1) {
						$texto_limite = "uma Instalação";
					}
					$dados_salvos['resposta_ajax'] .= "Não é possível construir outro(a) {$instalacao->nome}. O limite para esta Instalação é de {$texto_limite} em todo o Sistema Estelar.\n";
				}
			}			

			//Verifica se o Império tem os Recursos para construir ou realizar o upgrade
			$niveis = $_POST['nivel'] - $nivel_original;
			$dados_salvos['debug'] .= "\nDiferença de níveis: {$niveis} = {$_POST['nivel']} - {$nivel_original}; \n";
			
			//Se substituiu uma Instalação, DEVOLVE os recursos da Instalação antiga
			//Se DESMONTOU uma Instalação, DEVOLVE os recursos, mas cobra 1 Industrializáveis
			//Se realizou um DOWNGRADE, DEVOLVE a diferença dos recursos
			$devolve_recursos = false;
			if (!empty($instalacao_antiga)) {
				$niveis = $_POST['nivel'];//Ajusta para cobrar o valor TOTAL da nova Instalação
				$devolve_recursos = true;
			} elseif (!empty($_POST['turno_desmonta'])) {
				$niveis = 0;//Está DESMONTANDO uma instalação, então só DEVOLVE os recursos
				$instalacao_antiga = $instalacao;
				//Cobra 1 Industrializáveis
				$id_recurso = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='Industrializáveis'");
				$query_update_recursos[] = "UPDATE colonization_imperio_recursos SET qtd=qtd-1 WHERE id_imperio={$imperio->id} AND id_recurso={$id_recurso} AND turno={$turno->turno}";
				$devolve_recursos = true;
			} elseif ($niveis < 0) {
				$nivel_original = -$niveis;
				$instalacao_antiga = $instalacao;
				$devolve_recursos = true;
			}
				
			if ($devolve_recursos) {
				//Devolve os recursos
				if ($instalacao_antiga->custos != "") {
					$custos = explode(";",$instalacao_antiga->custos);
					
					$recursos_devolve = [];
					foreach ($custos as $chave_recurso => $recurso) {
						$recursos = explode("=",$recurso);
						$id_recurso = $recursos[0];
						$qtd = $recursos[1];
					
						$custo_recursos = $qtd*$nivel_original;
						$query_update_recursos[] = "UPDATE colonization_imperio_recursos SET qtd=qtd+{$custo_recursos} WHERE id_imperio={$imperio->id} AND id_recurso={$id_recurso} AND turno={$turno->turno}";
						$recursos_devolve[$id_recurso] = $custo_recursos;
					}
				}				
			}

			if ($niveis > 0) {//Só cobra se tiver diferença entre os níveis
				if ($instalacao->custos != "") {
					$custos = explode(";",$instalacao->custos);
					foreach ($custos as $chave_recurso => $recurso) {
						$recursos = explode("=",$recurso);
						$id_recurso = $recursos[0];
						$qtd = $recursos[1];
					
						$qtd_imperio = $wpdb->get_var("SELECT qtd FROM colonization_imperio_recursos WHERE id_imperio={$imperio->id} AND id_recurso={$id_recurso} AND turno={$turno->turno}");
						if (empty($recursos_devolve[$id_recurso])) {
							$recursos_devolve[$id_recurso] = 0;
						}
						$dados_salvos['debug'] .= "ID:{$_POST['id']} instalacao_inicial:{$_POST['instalacao_inicial']}\n";
						if (!($_POST['id'] == "" && $_POST['instalacao_inicial'] == 1)) {//Uma instalação inicial é gratuita, desde que esteja sendo criada.
							$qtd_imperio = $qtd_imperio + $recursos_devolve[$id_recurso];
							$custo_recursos = $qtd*$niveis;
							$query_update_recursos[] = "UPDATE colonization_imperio_recursos SET qtd=qtd-{$custo_recursos} WHERE id_imperio={$imperio->id} AND id_recurso={$id_recurso} AND turno={$turno->turno}";
							if ($qtd_imperio < $custo_recursos) {
								$dados_salvos['resposta_ajax'] = "O Império não tem Recursos suficientes para concluir essa operação.";	
							}
						}
					}
				}
			
				//Verifica no balanço do Império se é possível ativar a instalação (caso ela seja pode_desativar == 0)
				if ($instalacao->pode_desativar == 0 || (!empty($_POST['upgrade_acao']) && $instalacao->desguarnecida == 1)) {
					$sem_balanco = true;
					$acoes = new acoes($imperio->id,$turno->turno,$sem_balanco);
					
					if (empty($acoes->recursos_balanco)) {
						$acoes->pega_balanco_recursos();
					}
					
					foreach ($instalacao->consumo_fixo as $chave => $id_recurso) {//Verifica o custo fixo
						$dados_salvos['debug'] .= "Consumo e balanço: {$acoes->recursos_balanco_nome[$id_recurso]}: {$instalacao->consumo_fixo_qtd[$chave]} || {$acoes->recursos_balanco[$id_recurso]} \n";
						if ($instalacao->consumo_fixo_qtd[$chave] > ($acoes->recursos_balanco[$id_recurso] + $imperio->pega_qtd_recurso_imperio($id_recurso))) {
							if (!empty($_POST['upgrade_acao'])) {
								$dados_salvos['resposta_ajax'] = "Não é possível realizar esse upgrade! Essa ação irá consumir {$instalacao->consumo_fixo_qtd[$chave]} {$acoes->recursos_balanco_nome[$id_recurso]} mas o Balanço do Recurso não é suficiente!";
							} else {
								$dados_salvos['resposta_ajax'] = "Não é possível construir essa Instalação! Essa ação irá consumir {$instalacao->consumo_fixo_qtd[$chave]} {$acoes->recursos_balanco_nome[$id_recurso]} mas o Balanço do Recurso não é suficiente!";
							}
							break;
						}
					}
					
					if ($instalacao->desguarnecida == 1) {//Se for desguarnecida, verifica também o custo de ATIVAR ela no máximo
						foreach ($instalacao->recursos_consome as $chave => $id_recurso) {//Verifica o custo fixo
							$dados_salvos['debug'] .= "Consumo e balanço: {$acoes->recursos_balanco_nome[$id_recurso]}: {$instalacao->recursos_consome_qtd[$chave]} || {$acoes->recursos_balanco[$id_recurso]} \n";
							if ($instalacao->recursos_consome_qtd[$chave] > ($acoes->recursos_balanco[$id_recurso] + $imperio->pega_qtd_recurso_imperio($id_recurso))) {
								if (!empty($_POST['upgrade_acao'])) {
									$dados_salvos['resposta_ajax'] = "Não é possível realizar esse upgrade! Essa ação irá consumir {$instalacao->recursos_consome_qtd[$chave]} {$acoes->recursos_balanco_nome[$id_recurso]} mas o Balanço do Recurso não é suficiente!";
								} else {
									$dados_salvos['resposta_ajax'] = "Não é possível construir essa Instalação! Essa ação irá consumir {$instalacao->recursos_consome_qtd[$chave]} {$acoes->recursos_balanco_nome[$id_recurso]} mas o Balanço do Recurso não é suficiente!";
								}
								break;
							}
						}
					}
				}
			}
		}
		
		if (empty($dados_salvos['resposta_ajax'])) {
			$dados_salvos['resposta_ajax'] = "OK!";
			//Reseta os dados do JSON
			$wpdb->query("DELETE FROM colonization_balancos_turno WHERE turno={$turno->turno} AND id_imperio={$imperio->id}");
			$wpdb->query("DELETE FROM colonization_lista_colonias_turno WHERE turno={$turno->turno} AND id_imperio={$imperio->id}");

			//Se chegou até aqui pode atualizar os Recursos do Império
			if ($imperio->id != 0) {
				foreach ($query_update_recursos as $chave => $query) {
					$dados_salvos['debug'] .= "{$query} \n";
					$update_recursos = $wpdb->query($query);
				}
			}
		
			if (!empty($_POST['upgrade_acao'])) {
				unset($_POST['upgrade_acao']);
				unset($_POST['id_imperio']);				
				
				$debug = $dados_salvos['debug'];
				$dados_salvos = $this->salva_objeto(false); //Define que NÃO é pra responder com wp_die
				$dados_salvos['debug'] = $debug;
				$dados_salvos['recursos_atuais'] = $imperio->exibe_recursos_atuais();
				$dados_salvos['pos_processamento'] = $dados_salvos['recursos_atuais'];
				$dados_salvos['resposta_ajax'] = "SALVO!";
			}
		}
		
		if ($resposta_ajax === false) {
			return $dados_salvos;
		}
		
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}
	
	/***********************
	function salva_acao ()
	----------------------
	Salva uma Ação e devolve os dados 
	***********************/	
	function salva_acao() {
		global $wpdb; 
		$wpdb->hide_errors();

		if (empty($resposta['debug'])) {
			$resposta['debug'] = "";
		}
		
		if (empty($_POST['desativado'])) {
			$_POST['desativado'] = 0;
		}
		$resposta = $this->salva_objeto(false); //Define que NÃO é pra responder com wp_die
		//Como salvou uma ação, precisa REMOVER o antigo balanço dos recursos do banco de dados e salvar o novo
		$sem_balanco = true;
		$acoes = new acoes($_POST['id_imperio'],$_POST['turno'],$sem_balanco);
 		$chave_id_planeta_instalacoes = array_search($_POST['id_planeta_instalacoes'], $acoes->id_planeta_instalacoes);
		$acoes->pop[$chave_id_planeta_instalacoes] = $_POST['pop'];
		$acoes->desativado[$chave_id_planeta_instalacoes] = $_POST['desativado'];
		$acoes->pega_balanco_recursos($_POST['id_planeta_instalacoes'], true); //Recalcula os balanços
		$resposta['debug'] .= "\nSalvando os Balanços... \n";
		//$resposta['debug'] = "{$_POST['id_imperio']},{$_POST['turno']} \n";
		$resposta['resposta_ajax'] = "SALVO!";
		
		echo json_encode($resposta);
		wp_die();
	}


	/***********************
	function salva_objeto ()
	----------------------
	Salva o objeto desejado
	***********************/	
	function salva_objeto($resposta_ajax = true) {
		global $wpdb; 
		// Report all PHP errors
		$wpdb->hide_errors();
		//error_reporting(E_ALL); 
		//ini_set("display_errors", 1);		
		
		foreach ($_POST as $chave => $valor) {
			if ($chave!='tabela' && $chave!='where_clause' && $chave!='post_type' && $chave!='action' && $chave!='where_value') {
				$dados[$chave] = $valor;
			}
		}

		if ($_POST['where_value'] == "") {//Se o valor estiver em branco, é um novo objeto.
			$resposta = 0;
		} else {
			$query = "SELECT id FROM {$_POST['tabela']} WHERE {$_POST['where_clause']}={$_POST['where_value']}";
			$resposta = $wpdb->query($query);
		}
		
		if ($resposta === 0) {//Se o objeto não existe, cria
			$resposta = $wpdb->insert($_POST['tabela'],$dados);
			$dados['id'] = $wpdb->insert_id;
		} elseif ($resposta === 1) {//Se existir, atualiza
			$where[$_POST['where_clause']]=$_POST['where_value'];
			$resposta = $wpdb->update($_POST['tabela'],$dados,$where);
			$dados[$_POST['where_clause']] = $_POST['where_value'];
		} else {
			$dados_salvos['resposta_ajax']	= "ERRO!";
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta
		}
		
		if ($resposta !== false) {
			//Retorna os dados do objeto e uma variável "resposta" com "SALVO!"
			$where = "";
			foreach ($dados as $chave => $valor) {
				$semslashes = addslashes($valor);
				$where .= " AND {$chave}='{$semslashes}'";
			}
			$where = substr($where,5);
			$dados_salvos = $wpdb->get_results("SELECT * FROM {$_POST['tabela']} WHERE {$where}");
			foreach ($dados_salvos[0] as $chave => $valor) {
				if (str_contains($valor, "\\")) {
					$dados_salvos[0]->$chave = stripslashes($valor);
				}
			}
			
			$dados_salvos['debug'] = "SELECT * FROM {$_POST['tabela']} WHERE {$where}";
			$dados_salvos['resposta_ajax'] = "SALVO!";
		} else {
			$dados_salvos['resposta_ajax'] = $wpdb->last_error;
			$dados_salvos['resposta_ajax'] .= "Ocorreu um erro ao tentar salvar o objeto! Por favor, tente novamente!";
		}
		
		if ($resposta_ajax === false) {
			return $dados_salvos; 
		}

		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}

	/***********************
	function deleta_objeto ()
	----------------------
	Deleta o objeto desejado
	***********************/	
	function deleta_objeto() {
		global $wpdb; 
		//$wpdb->hide_errors();
		
		//$where = [];
		//$where[$_POST['where_clause']]=$_POST['where_value'];
		
		$resposta = $wpdb->query("DELETE FROM {$_POST['tabela']} WHERE {$_POST['where_clause']}={$_POST['where_value']}");

		if ($resposta === false) {
			$dados_salvos['debug'] = "DELETE FROM {$_POST['tabela']} WHERE {$_POST['where_clause']}={$_POST['where_value']}";
			$dados_salvos['resposta_ajax'] = "Ocorreu um erro desconhecido! Por favor, tente novamente! \nErro: resposta:{$resposta}\n{$_POST['tabela']}: {$_POST['where_clause']}={$_POST['where_value']}  \n{$wpdb->last_error}";			
		} else {
			$dados_salvos['resposta_ajax'] = "DELETADO!";
		}
	
		echo json_encode($dados_salvos); //Envia a resposta via echo
		wp_die(); //Termina o script e envia a resposta

	}
	
	/***********************
	function destruir_instalacao ()
	----------------------
	Destrói uma instalação
	***********************/	
	function destruir_instalacao() {
		global $wpdb; 
		// Report all PHP errors
		$wpdb->hide_errors();
		//error_reporting(E_ALL);
		//ini_set("display_errors", 1);
		
		$dados_salvos['debug'] = "";
		$dados_salvos['resposta_ajax'] = "";
		$query_consome_recursos = [];
		$turno = new turno();
		$colonia_instalacao = new colonia_instalacao($_POST['id']);
		$id_colonia = $wpdb->get_var("SELECT id FROM colonization_imperio_colonias WHERE id_planeta={$colonia_instalacao->id_planeta} AND turno={$turno->turno}");
		$id_imperio = 0;
		$dados_salvos['debug'] .= "id_colonia_instalacao{$_POST['id']} \n";
		
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		if (!empty($id_colonia)) {
			$colonia = new colonia($id_colonia);
			$planeta = new planeta($colonia->id_planeta);
			$estrela = new estrela($planeta->id_estrela);
			$id_imperio = $colonia->id_imperio;
			$imperio = new imperio($id_imperio);
		}
		
		if ($id_imperio != $imperio->id && $roles != "administrator") {
			$imperio = new imperio ($id_imperio , true);
			
			$dados_salvos['resposta_ajax'] = "Somente o Jogador do Império '{$imperio->nome}' ou o Administrador podem reparar/destruír Instalações!";
			echo json_encode($dados_salvos); //Envia a resposta via echo
			wp_die(); //Termina o script e envia a resposta
		}

		if ($estrela->cerco && $roles != "administrator") {
			$dados_salvos['resposta_ajax'] = "O sistema está sitiado e não pode receber alterações nas Instalações nesse momento.";
			
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta
		}		
		
		if (!empty($colonia_instalacao->turno_destroi)) {
			if (empty($colonia_instalacao->turno_desmonta)) {
				if (empty($_POST['desmantelar'])) {//Não está tentando desmantelar uma Instalação, então pode Reparar a Instalação
					//Reparar uma Instalação custa o valor de um nível da Instalação
					$instalacao = new instalacao($colonia_instalacao->id_instalacao);
					if ($instalacao->custos != "") {
						$custos = explode(";",$instalacao->custos);
						foreach ($custos as $chave_recurso => $recurso) {
							$recursos = explode("=",$recurso);
							$id_recurso = $recursos[0];
							$qtd = $recursos[1];
						
							$qtd_imperio = $wpdb->get_var("SELECT qtd FROM colonization_imperio_recursos WHERE id_imperio={$imperio->id} AND id_recurso={$id_recurso} AND turno={$turno->turno}");
							if ($qtd_imperio < $qtd) {
								$dados_salvos['resposta_ajax'] = "O Império não tem recursos suficientes para reparar essa Instalação!";
								break;
							} else {
								$query_consome_recursos[] = "UPDATE colonization_imperio_recursos SET qtd=qtd-{$qtd} WHERE id_imperio={$id_imperio} AND id_recurso={$id_recurso} AND turno={$turno->turno}";
							}
						}
					}

					//Verifica se o Império tem o suficiente (ou se é o Admin que está fazendo
					if ($id_imperio != 0) {
						if ($dados_salvos['resposta_ajax'] == "") {
							foreach ($query_consome_recursos as $chave => $query) {
								$wpdb->query($query);
							}
						} else {
							echo json_encode($dados_salvos); //Envia a resposta via echo
							wp_die(); //Termina o script e envia a resposta
						}
					}
					$query = "UPDATE colonization_planeta_instalacoes SET turno_destroi = null WHERE id={$_POST['id']}";
				} else {
					$dados_salvos['resposta_ajax'] = "OK!";
					echo json_encode($dados_salvos); //Envia a resposta via echo
					wp_die(); //Termina o script e envia a resposta					
				}
			} else {
				$dados_salvos['resposta_ajax'] = "Não é possível reparar uma Instalação que foi desmantelada!";
				echo json_encode($dados_salvos); //Envia a resposta via echo
				wp_die(); //Termina o script e envia a resposta
			}
		} else {
			$query = "UPDATE colonization_planeta_instalacoes SET turno_destroi = {$turno->turno} WHERE id={$_POST['id']}";
		}
		
		//Atualiza os balanços
		if ($id_imperio != 0) {
			$acoes = new acoes($id_imperio);
			$chave_id_planeta_instalacoes = array_search($colonia_instalacao->id, $acoes->id_planeta_instalacoes);
			$wpdb->query("UPDATE colonization_acoes_turno SET pop=0 WHERE id={$acoes->id[$chave_id_planeta_instalacoes]}");
			//Reseta os dados do JSON
			$wpdb->query("DELETE FROM colonization_balancos_turno WHERE turno={$turno->turno} AND id_imperio={$id_imperio}");
			$wpdb->query("DELETE FROM colonization_lista_colonias_turno WHERE turno={$turno->turno} AND id_imperio={$id_imperio}");
		}
		
		$resposta = $wpdb->query($query);
		
		if ($resposta !== false) {
			$dados_salvos = $wpdb->get_results("SELECT * FROM colonization_planeta_instalacoes WHERE id={$_POST['id']}");
			if ($dados_salvos[0]->turno_destroi === null) {
				$dados_salvos[0]->turno_destroi = "";
			}
			$dados_salvos['resposta_ajax'] = "OK!";
		} else {
			$dados_salvos['resposta_ajax'] = "Ocorreu um erro desconhecido! Por favor, tente novamente!";
		}
		
		$dados_salvos['debug'] .= "{$id_colonia}\n";
		echo json_encode($dados_salvos); //Envia a resposta via echo
		wp_die(); //Termina o script e envia a resposta
	}
	
	/***********************
	function desmonta_instalacao ()
	----------------------
	Destrói uma instalação
	***********************/	
	function desmonta_instalacao() {
		global $wpdb; 
		// Report all PHP errors
		$wpdb->hide_errors();
		//error_reporting(E_ALL);
		//ini_set("display_errors", 1);
		
		$turno = new turno();
		$colonia_instalacao = new colonia_instalacao($_POST['id']);
		$instalacao = new instalacao($colonia_instalacao->id_instalacao);
	
		$dados_salvos['resposta_ajax'] = "";
		
		if ($instalacao->pode_desativar == 0 && $instalacao->slots == 0) {
			$dados_salvos['resposta_ajax'] = "Não é possível desmantelar uma Instalação que não pode ser desativada.";
		}

		if (!empty($colonia_instalacao->turno_desmonta)) {
			$dados_salvos['resposta_ajax'] = "Não é possível reverter uma operação de desmantelamento. Construa uma nova instalação.";
		}

		if (empty($colonia_instalacao->turno_destroi)) {
			$dados_salvos['resposta_ajax'] = "A Instalação precisa ser destruída antes de poder ser desmantelada!";
		}
		
		if ($dados_salvos['resposta_ajax'] == "") {
			$this->valida_colonia_instalacao();
		}

		echo json_encode($dados_salvos); //Envia a resposta via echo
		wp_die(); //Termina o script e envia a resposta
	}


	/***********************
	function dados_imperio ()
	----------------------
	Pega os dados do Império
	***********************/	
	function dados_imperio() {
		global $wpdb; 
		$wpdb->hide_errors();
		$dados_salvos = [];
		
		$dados_salvos = $wpdb->get_results("SELECT * FROM colonization_imperio WHERE id={$_POST['id']}");
		
		if (isset($dados_salvos[0])) {
			$imperio = new imperio($_POST['id']);
			$dados_salvos['resposta_ajax'] = "OK!";
			
			$dados_salvos[0]->pop = $imperio->pop;
			
		} else {
			$dados_salvos['resposta_ajax'] = $wpdb->last_error;
			$dados_salvos['resposta_ajax'] .= "Ocorreu um erro ao tentar salvar o objeto! Por favor, tente novamente!";
		}
		
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}
	
	/***********************
	function dados_transfere_tech()
	----------------------
	Pega os resultados das listas de Techs transferidas
	***********************/	
	function dados_transfere_tech() {
		global $wpdb; 
		
		$transfere_tech = new transfere_tech();
		$listas = $transfere_tech->exibe_listas($_POST['id_imperio']);
		
		if (!empty($listas)) {
			$lista_techs_enviadas = $listas['lista_techs_enviadas'];
			$lista_techs_recebidas = $listas['lista_techs_recebidas'];			
		}
		
		if (empty($lista_techs_enviadas)) {
			$lista_techs_enviadas = "&nbsp;";
		}
		if (empty($lista_techs_recebidas)) {
			$lista_techs_recebidas = "&nbsp;";
		}		
		
		$dados_salvos['techs_enviadas'] = $lista_techs_enviadas;
		$dados_salvos['techs_recebidas'] = $lista_techs_recebidas;
		$dados_salvos['resposta_ajax'] = "OK!";
		
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta		
	}

	/***********************
	function valida_acao ()
	----------------------
	Valida o objeto desejado
	***********************/	
	function valida_acao() {
		global $wpdb, $debug, $start_time; 
		// Report all PHP errors
		$wpdb->hide_errors();
		//error_reporting(E_ALL);
		//ini_set("display_errors", 1);
		
		$dados_salvos = [];
		$dados_salvos['debug'] = "";
		
		$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
		$debug .= "valida_acao() => {$diferenca}ms \n";
		$dados_salvos['balanco_acao'] = "";		
		
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
			$id_imperio = $wpdb->get_var("SELECT id FROM colonization_imperio WHERE id_jogador={$user->ID}");
			if (empty($id_imperio)) {
				$id_imperio = 0;
			}
			$imperio = new imperio($id_imperio, true);
		}
		$turno = new turno($_POST['turno']);
		if ($turno->encerrado == 1 && $roles != "administrator" ) {
			$dados_salvos['resposta_ajax'] = "O Turno {$turno->turno} foi ENCERRADO e não pode mais receber alterações!";
		
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta
		}
			
		//$start_time = hrtime(true);
		$sem_balanco = true;
		$acoes = new acoes($_POST['id_imperio'],$_POST['turno'],$sem_balanco); //Como vamos alterar a ação, não calcula os balanços na criação da ação
			$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
			$debug .= "valida_acao() -> new Ações {$diferenca}ms \n";
		
		//Verifica se existe MdO suficiente na Colônia (ou no Sistema, para o caso de unidades Autônomas)
		$instalacao = new instalacao($_POST['id_instalacao']);
		$planeta = new planeta($_POST['id_planeta']);
		$id_colonia = $wpdb->get_var("
		SELECT id FROM colonization_imperio_colonias 
		WHERE id_imperio={$_POST['id_imperio']} AND id_planeta={$planeta->id} 
		AND turno={$_POST['turno']}
		");
		$colonia = new colonia($id_colonia);
		$chave_id_planeta_instalacoes = array_search($_POST['id_planeta_instalacoes'], $acoes->id_planeta_instalacoes);

		//Verifica se existe recurso suficiente no planeta para ser extraído (caso seja um recurso extrativo)
		//Para fazer isso, temos que RECALCULAR o objeto Ações, alterando o MdO para o MdO correto
 		$acoes->pop[$chave_id_planeta_instalacoes] = $_POST['pop'];
		$acoes->desativado[$chave_id_planeta_instalacoes] = $_POST['desativado'];
		if ($_POST['desativado'] == 1) {
			$acoes->pop[$chave_id_planeta_instalacoes] = 0;
		}
		$ajax_valida = true;
		$acoes->pega_balanco_recursos($_POST['id_planeta_instalacoes']); //Recalcula os balanços
		$debug .= $acoes->debug;
		$acoes->debug = "";
		$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
		$debug .= "valida_acao() -> \$acoes->pega_balanco_recursos() {$diferenca}ms \n";
		
		$mdo_planeta = $acoes->mdo_planeta($planeta->id);
		$pop_planeta = $colonia->pop + $colonia->pop_robotica;
		
		$mdo_sistema = 0;
		$pop_sistema = 0;
		$pop_mdo_sistema = $acoes->pop_mdo_sistema($planeta->id_estrela);
		$mdo_sistema = $pop_mdo_sistema['mdo'];
		$pop_sistema = $pop_mdo_sistema['pop'];

		if ($mdo_sistema > $pop_sistema) {//Verifica se tem MdO no sistema
			$dados_salvos['balanco_acao'] = "Mão-de-Obra no Sistema, ";
		} 
		if (!$instalacao->autonoma) {//Depois, se NÃO for instalação autônoma, verifica se tem MdO no planeta
			if ($mdo_planeta > $pop_planeta) {
				$dados_salvos['balanco_acao'] = "Mão-de-Obra no Planeta, ";
			}
		}

		//Verifica se tem limite suficiente de Reservas Planetárias
		$recurso = [];
		
		$id_energia = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome = 'Energia'");
		$recurso_energia = new recurso($id_energia);
		$recurso[$id_energia] = $recurso_energia;
		
		foreach ($instalacao->recursos_produz as $chave_recurso_produz => $id_recurso_produz) {
			if (empty($recurso[$id_recurso_produz])) {
				$recurso[$id_recurso_produz] = new recurso($id_recurso_produz);
			}
			if ($recurso[$id_recurso_produz]->extrativo == 1 && $instalacao->nao_extrativo != true) {
				$id_planeta_recurso = $wpdb->get_var("SELECT id FROM colonization_planeta_recursos WHERE id_planeta={$planeta->id} AND id_recurso={$id_recurso_produz} AND turno={$_POST['turno']}");
				if (!empty($id_planeta_recurso)) {
					$planeta_recursos = new planeta_recurso($id_planeta_recurso);	
					if ($acoes->recursos_produzidos_planeta[$id_recurso_produz][$planeta->id] > $planeta_recursos->qtd_disponivel) {
						$dados_salvos['balanco_acao'] .= "Reservas Planetárias de {$planeta_recursos->recurso->nome} (Extrai {$acoes->recursos_produzidos_planeta[$id_recurso_produz][$planeta->id]}, Reservas {$planeta_recursos->qtd_disponivel}), ";
					}
				} else {//Caso o planeta não tenha o recurso...
					$planeta_recursos = new recurso($id_recurso_produz);
					$dados_salvos['balanco_acao'] .= "Reservas Planetárias de {$recurso[$id_recurso_produz]->nome}, ";
				}
			}
		}
			$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
			$debug .= "valida_acao() -> Verifica Reservas Planetárias {$diferenca}ms \n";

		//Verifica se o balanço de recursos mais o estoque do Império são suficientes
		$imperio_recursos = new imperio_recursos($_POST['id_imperio'],$_POST['turno']);
		$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
		$debug .= "valida_acao() -> new Imperio_Recursos {$diferenca}ms \n";
		
		//Valida os recursos NÃO-LOCAIS (ou seja, GLOBAIS)
		foreach ($acoes->recursos_balanco as $id_recurso => $valor) {
			if (empty($recurso[$id_recurso])) {
				$recurso[$id_recurso] = new recurso($id_recurso);
				$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
				$debug .= "valida_acao() -> Verifica Estoque Império: new Recurso {$diferenca}ms \n";
			}
			
			/***
			if (empty($acoes->recursos_balanco[$id_recurso])) {
				$acoes->recursos_balanco[$id_recurso] = 0;
			}		
			$valor = $acoes->recursos_balanco[$id_recurso];
			***/

			if ($recurso[$id_recurso]->acumulavel == 1) {
				$chave_id_recurso = array_search($id_recurso,$imperio_recursos->id_recurso);
				$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
				$debug .= "valida_acao() -> Verifica Estoque Império: array_search {$diferenca}ms \n";
				$balanco = $imperio_recursos->qtd[$chave_id_recurso] + $valor;
			} else {
				$balanco = $valor;
			}
			
				$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
				$debug .= "valida_acao() -> Verifica Balanço do Recurso {$recurso[$id_recurso]->nome}: {$balanco} {$diferenca}ms \n";
			if ($balanco < 0) {
				$dados_salvos['balanco_acao'] .= "{$recurso[$id_recurso]->nome} ({$balanco}), ";
			}
		}
			$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
			$debug .= "valida_acao() -> Verifica Estoque Império {$diferenca}ms \n";

		//Precisa validar o balanço dos Recursos LOCAIS
		foreach ($acoes->recursos_balanco_nome as $id_recurso => $nome) {
			if ($recurso[$id_recurso]->local == 1) {
				foreach ($acoes->recursos_consumidos_planeta[$id_recurso] as $id_planeta => $valor) {
					
					$balanco = $acoes->recursos_produzidos_planeta[$id_recurso][$id_planeta] - $valor;
					$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
					$debug .= "valida_acao() -> Verifica Balanço do Recurso {$nome} no Planeta {$id_planeta}: {$balanco} {$diferenca}ms \n";
					
					if ($balanco < 0 && $nome != "Poluição") {//A Poluição pode ser negativa
						$dados_salvos['balanco_acao'] .= "{$recurso[$id_recurso]->nome} ({$balanco}) no Planeta '{$planeta->nome}', ";
					}
				}
			}
		}
		
		if ($dados_salvos['balanco_acao'] != "") {
			$dados_salvos['balanco_acao'] = substr($dados_salvos['balanco_acao'],0,-2);
		}


		//***
		if ($_POST['desativado'] == 1 || (!$instalacao->desguarnecida && $_POST['pop'] == 0)) {//Se for para DESATIVAR uma Instalação, não precisa fazer os balanços
			//EXCETO se estiver desativando uma produtora de Energia. Nesse caso, o Jogador é informado que só pode desativar a geradora se o balanço de energia for maior ou igual à zero.
			$chave_recurso = array_search($id_energia,$instalacao->recursos_produz);
			if ($chave_recurso !== false && $dados_salvos['balanco_acao'] != "") {
				$dados_salvos['balanco_acao'] .= ". \n\nUma geradora de Energia só pode ser desativada se o balanço de Energia estiver zerado ou positivo!";
			} elseif ($planeta->inospito == 1 && ($instalacao->pop_inospito || $instalacao->terraforma) && $colonia->pop != 0) {
				$dados_salvos['balanco_acao'] = "\n\nNão é possível desativar uma Instalação que serve de suporte de vida em Planetas Inóspitos enquanto houver habitantes no local!";
			} else {
				$dados_salvos['balanco_acao'] = "";
			}
		}
		//***/
		
		if ($dados_salvos['balanco_acao'] == "") {//Validou as ações! Pega os dados modificados e passa para o Jogador
			$imperio = new imperio($_POST['id_imperio']);
			$imperio->acoes = $acoes;
			
			$colonias = [];
			$colonias[0] = $id_colonia;
			$colonias[1] = 0;
			$produtos_acao = $this->produtos_acao($imperio, $_POST['id_planeta_instalacoes'], $colonias);
			$debug .= $produtos_acao['debug'];
			$produtos_acao['debug'] = "";
			$dados_salvos = array_merge($dados_salvos, $produtos_acao);
				$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
				$debug .= "valida_acao() -> \$this->produtos_acao {$diferenca}ms \n";			
		}
		
		$dados_salvos['debug'] .= $debug;
		$dados_salvos['resposta_ajax'] = "OK!";
		
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}
	
	/***********************
	function produtos_acao_ajax()
	----------------------
	Pega os resultados da ação, via AJAX
	***********************/	
	function produtos_acao_ajax() {
		
		$dados_salvos = $this->produtos_acao();
		
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}
	
	
	/***********************
	function produtos_acao()
	----------------------
	Pega os resultados da ação
	***********************/
	function produtos_acao($imperio=0, $id_planeta_instalacoes=0, $colonias = []) {
		global $wpdb, $start_time;
		// Report all PHP errors
		$wpdb->hide_errors();
		//error_reporting(E_ALL);
		//ini_set("display_errors", 1);		
		
		$dados_salvos = [];
		
		$dados_salvos['debug'] = "";
		if ($imperio == 0) {
			$imperio = new imperio($_POST['id_imperio']);
			$imperio->acoes = new acoes($imperio->id,$imperio->turno->turno,false);
			$id_planeta_instalacoes = $_POST['id_planeta_instalacoes'];
			$colonias[0] = $wpdb->get_var("SELECT cic.id FROM colonization_imperio_colonias AS cic WHERE cic.id_imperio={$imperio->id} AND cic.turno={$imperio->turno->turno} AND cic.id_planeta={$_POST['id_planeta']}");
			$colonias[1] = 0;	
			$imperio->acoes->pega_balanco_recursos();
		}
		
		$acoes = $imperio->acoes;

		//$start_time = hrtime(true);
		$planeta = new planeta($_POST['id_planeta']);
			$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
			$dados_salvos['debug'] .= "produtos_acao() -> new Planeta {$diferenca}ms \n";
		
		$dados_salvos['lista_colonias'] = $imperio->exibe_lista_colonias($colonias);
			$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
			$dados_salvos['debug'] .= $imperio->debug;
			$dados_salvos['debug'] .= "produtos_acao() -> \$imperio->exibe_lista_colonias() {$diferenca}ms \n";
		$dados_salvos['recursos_produzidos'] = $imperio->acoes->exibe_recursos_produzidos();
			$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
			$dados_salvos['debug'] .= "produtos_acao() -> \$imperio->acoes->exibe_recursos_produzidos() {$diferenca}ms \n";		
		$dados_salvos['recursos_consumidos'] = $imperio->acoes->exibe_recursos_consumidos();
			$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
			$dados_salvos['debug'] .= "produtos_acao() -> \$imperio->acoes->exibe_recursos_consumidos() {$diferenca}ms \n";
		$dados_salvos['recursos_balanco'] = $imperio->acoes->exibe_recursos_balanco();
			$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
			$dados_salvos['debug'] .= "produtos_acao() -> \$imperio->acoes->exibe_recursos_balanco(); {$diferenca}ms \n";		
		$dados_salvos['balanco_planeta'] = $imperio->acoes->exibe_balanco_planeta($_POST['id_planeta']);
			$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
			$dados_salvos['debug'] .= "produtos_acao() -> \$imperio->acoes->exibe_balanco_planeta() {$diferenca}ms \n";		
		$dados_salvos['pop_mdo_planeta'] = $imperio->acoes->exibe_pop_mdo_planeta($_POST['id_planeta'], $imperio);
			$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
			$dados_salvos['debug'] .= "produtos_acao() -> \$imperio->acoes->exibe_pop_mdo_planeta() {$diferenca}ms \n";		
		$pop_sistema = $imperio->acoes->pop_mdo_sistema($planeta->id_estrela);
			$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
			$dados_salvos['debug'] .= "produtos_acao() -> \$imperio->acoes->pop_mdo_sistema() {$diferenca}ms \n";		
		
		$chave_acao = array_search($id_planeta_instalacoes, $imperio->acoes->id_planeta_instalacoes);
		$dados_salvos['id_planeta_instalacoes_produz_consome'] = "<label>Balanço da produção:</label> {$imperio->acoes->html_producao_consumo_instalacao($chave_acao)}";
		$pop_disponivel_sistema = $pop_sistema['pop'] - $pop_sistema['mdo'];
		$dados_salvos['mdo_sistema'] = "({$pop_disponivel_sistema})";
		$dados_salvos['debug'] .= "debug_imperio: {$imperio->debug} \n";
		//$dados_salvos['resposta_ajax'] = "OK!";
		
		return $dados_salvos; //Envia a resposta via echo, codificado como JSON
		//wp_die(); //Termina o script e envia a resposta
	}	

	/***********************
	function roda_turno()
	----------------------
	Roda o Turno
	***********************/	
	function roda_turno() {
		global $wpdb;
		// Report all PHP errors
		$wpdb->hide_errors();
		//error_reporting(E_ALL); 
		//ini_set("display_errors", 1);		
		$html = [];

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		if ($roles != "administrator") {
			$dados_salvos['resposta_ajax'] = "Você não está autorizado a realizar esta operação!";
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta
		}

		$roda_turno = new roda_turno();
		$html['html'] = $roda_turno->executa_roda_turno();
		
		if ($roda_turno->concluido) {
			$turno = new turno();
			
			$proxima_semana = new DateTime($turno->data_turno);
			$proxima_semana->modify('+7 days');
			$proxima_semana = $proxima_semana->format('Y-m-d H:i:s');
			
			$html['turno_novo'] = "<div id='div_turno'><h2>COLONIZATION - RODA TURNO</h2>
			<h3>TURNO ATUAL - {$turno->turno}</h3>
			<div>DATA DO TURNO ATUAL - {$turno->data_turno}</div>
			<div>DATA DO PRÓXIMO TURNO - {$proxima_semana}</div></div>";
			
			$html['dados_acoes_imperios'] = "<thead>
			<tr><td style='width: 200px;'>Nome do Império</td><td style='width: 200px;'>Dt Última Modificação</td><td style='width: 80px;'>Pontuação</td><td style='width: 100%;'>Balanço dos Recursos</td></tr>
			</thead>
			<tbody>";
		
			//Pega a lista de impérios
			$lista_id_imperio = $wpdb->get_results("SELECT id FROM colonization_imperio");
			$html_lista_imperios = "";
		
			foreach ($lista_id_imperio as $id) {
				$imperio = new imperio($id->id);
				$acoes = new acoes($imperio->id,$turno->turno);
				$acoes->pega_balanco_recursos();
				//Reseta os dados do JSON
				$wpdb->query("DELETE FROM colonization_balancos_turno WHERE turno={$turno->turno} AND id_imperio={$imperio->id}");
				$wpdb->query("DELETE FROM colonization_lista_colonias_turno WHERE turno={$turno->turno} AND id_imperio={$imperio->id}");
				$acoes->pega_balanco_recursos();
				$balanco = $acoes->exibe_recursos_balanco();
			
				$html_lista_imperios .= "<tr><td><div>".$imperio->nome."</div></td><td>{$acoes->max_data_modifica}</td><td>{$imperio->pontuacao}</td><td>{$balanco}</td></tr>";
			}

			$html['dados_acoes_imperios'] .= $html_lista_imperios;
		
			$html['dados_acoes_imperios'] .= "\n</tbody>";

		} else {
			$html['turno_novo'] = "";
		}
		
		echo json_encode($html); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}

	/***********************
	function libera_turno()
	----------------------
	Libera o turno
	***********************/	
	function libera_turno() {
		global $wpdb;

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		if ($roles != "administrator") {
			$dados_salvos['resposta_ajax'] = "Você não está autorizado a realizar esta operação!";
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta
		}
		
		$wpdb->query("UPDATE colonization_turno_atual SET bloqueado=false, data_turno=data_turno");
		
		$dados_salvos['resposta_ajax'] = "OK!";
		
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}

	/***********************
	function encerra_turno()
	----------------------
	Encerra o Turno
	***********************/	
	function encerra_turno() {
		global $wpdb;
		
		$dados_salvos['resposta_ajax'] = "OK!";
		
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}

		if ($roles != "administrator") {
			$dados_salvos['resposta_ajax'] = "É necessário ser um Administrador para poder executar essa ação!";
		} else {
			$turno = new turno($_POST['turno']);
			$wpdb->query("UPDATE colonization_turno_atual SET encerrado=1, data_turno='{$turno->data_turno}' WHERE id={$turno->turno}");
			$wpdb->query("UPDATE wp_forum_topics SET closed = 1 WHERE name LIKE 'Turno {$turno->turno}%'");
		}
		
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}
}
?>