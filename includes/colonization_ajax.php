<?php 
/**************************
COLONIZATION_AJAX.PHP
----------------
Lida com os "request" de Ajax. 
Utilizado para operar o banco de dados (normalmente queries para salvar dados)
***************************/

class colonization_ajax {
	
	function __construct() {
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
		add_action('wp_ajax_dados_imperio', array ($this, 'dados_imperio'));
		//add_action('wp_ajax_produtos_acao', array ($this, 'produtos_acao'));
		add_action('wp_ajax_valida_acao', array ($this, 'valida_acao'));
		add_action('wp_ajax_roda_turno', array ($this, 'roda_turno'));
		add_action('wp_ajax_libera_turno', array ($this, 'libera_turno'));
		add_action('wp_ajax_encerra_turno', array ($this, 'encerra_turno'));
		add_action('wp_ajax_valida_acao_admin', array ($this, 'valida_acao_admin'));
		add_action('wp_ajax_valida_reabastecimento', array ($this, 'valida_reabastecimento'));
		add_action('wp_ajax_valida_tech_imperio', array ($this, 'valida_tech_imperio'));
		add_action('wp_ajax_valida_transfere_tech', array ($this, 'valida_transfere_tech'));//valida_transfere_tech
		add_action('wp_ajax_dados_transfere_tech', array ($this, 'dados_transfere_tech'));//dados_transfere_tech
		add_action('wp_ajax_processa_recebimento_tech', array ($this, 'processa_recebimento_tech'));//salva_transfere_tech
		add_action('wp_ajax_processa_viagem_nave', array ($this, 'processa_viagem_nave'));//processa_viagem_nave
		add_action('wp_ajax_envia_nave', array ($this, 'envia_nave'));//envia_nave
		add_action('wp_ajax_nave_visivel', array ($this, 'nave_visivel'));//nave_visivel
		add_action('wp_ajax_aceita_missao', array ($this, 'aceita_missao'));//aceita_missao
		add_action('wp_ajax_transfere_pop', array ($this, 'transfere_pop'));//transfere_pop
		add_action('wp_ajax_lista_estrelas', array ($this, 'lista_estrelas'));//Retorna com todas as estrelas disponíveis em formado JSON
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
		$dados_salvos['resposta_ajax'] = "Somente o jogador do {$imperio->nome} pode processar sua missão!";
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
	function processa_viagem_nave ()
	----------------------
	Processa uma viagem
	***********************/	
	function envia_nave() {
		global $wpdb;
		
		$nave = new frota($_POST['id']);
		
		$imperio = new imperio($nave->id_imperio);
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		$dados_salvos['resposta_ajax'] = "Somente o jogador do {$imperio->nome} pode despachar sua nave!";
		if ($imperio->id == $nave->id_imperio || $roles == "administrator") {
			if ($nave->id_estrela_destino == 0) {
				$resposta = $wpdb->query("UPDATE colonization_imperio_frota SET id_estrela_destino={$_POST['id_estrela']} WHERE id={$nave->id}");
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

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}

		$nave = new frota($_POST['id']);
		$turno = new turno();
		if ($roles == "administrator" && $nave->id_estrela_destino != 0) {
			$estrela = new estrela($nave->id_estrela_destino);
			$resposta = $wpdb->query("UPDATE colonization_imperio_frota SET X={$estrela->X}, Y={$estrela->Y}, Z={$estrela->Z}, id_estrela_destino=0, visivel=false WHERE id={$nave->id}");
			
			//Verifica se a Estrela já foi visitada, e se não foi marca como visitada
			$estrela_visitada = $wpdb->get_var("SELECT id FROM colonization_estrelas_historico WHERE id_imperio={$nave->id_imperio} AND id_estrela={$estrela->id}");
			if (empty($estrela_visitada)) {
				$wpdb->query("INSERT INTO colonization_estrelas_historico SET id_imperio={$nave->id_imperio}, id_estrela={$estrela->id}, turno={$turno->turno}");
			} else {
				$wpdb->query("UPDATE colonization_estrelas_historico SET turno={$turno->turno} WHERE id={$estrela_visitada}");
			}
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
			$imperio = new imperio($id_imperio_destino);
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
		if (empty($_POST['id'])) {
			$id_tech = $wpdb->get_var("SELECT id FROM colonization_imperio_techs WHERE id_imperio={$_POST['id_imperio']} AND id_tech={$_POST['id_tech']}");
			$dados_salvos['debug'] .= "SELECT id FROM colonization_imperio_techs WHERE id_imperio={$_POST['id_imperio']} AND id_tech={$_POST['id_tech']} AND id !={$_POST['id']}";
		} else {
			$tech_imperio = new imperio_techs($_POST['id']);
			$id_tech = $wpdb->get_var("SELECT id FROM colonization_imperio_techs WHERE id_imperio={$_POST['id_imperio']} AND id_tech={$_POST['id_tech']} AND id !={$_POST['id']}");
			$dados_salvos['debug'] .= "SELECT id FROM colonization_imperio_techs WHERE id_imperio={$_POST['id_imperio']} AND id_tech={$_POST['id_tech']} AND id !={$_POST['id']}";
		}
		
		$dados_salvos['confirma'] = "";
		if (!empty($id_tech)) {
			$dados_salvos['resposta_ajax'] = "O Império já possui essa Tech!";
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta
		}
		

		$tech = new tech($_POST['id_tech']);
		
		$dados_salvos['custo_pago'] = $_POST['custo_pago'];
		if ($_POST['custo_pago'] > $tech->custo) {
			$dados_salvos['resposta_ajax'] = "O custo pago por essa Tech é maior que o custo da Tech ({$tech->custo})! Favor revisar!";
			$dados_salvos['custo_pago'] = $tech->custo;
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta
		} elseif ($tech->custo == $_POST['custo_pago']) {
			$dados_salvos['custo_pago'] = 0;
		}

		//Verifica se o Império tem os pré-requisitos da Tech
		if (!empty($tech->id_tech_parent)) {
			$id_tech_parent = str_replace(";",",",$tech->id_tech_parent);
			$tech_parent = $wpdb->get_var("SELECT COUNT(id) FROM colonization_imperio_techs WHERE id_imperio={$_POST['id_imperio']} AND id_tech IN ({$id_tech_parent}) AND custo_pago = 0");
			if ($tech_parent == 0) {
				$id_tech_parent = explode(",",$id_tech_parent);
				$id_tech_parent = $id_tech_parent[0];
				$tech_parent = new tech($id_tech_parent);
				$dados_salvos['resposta_ajax'] = "O Império não tem os pré-requisitos necessários! É necessário ter a Tech '{$tech_parent->nome}'";
			}
		}

		if (!empty($tech->lista_requisitos)) {
			foreach ($tech->id_tech_requisito as $chave => $id_requisito) {
				$tech_requisito = new tech($id_requisito);

				$tech_requisito_query = $wpdb->get_var("SELECT COUNT(id) FROM colonization_imperio_techs WHERE id_imperio={$_POST['id_imperio']} AND (id_tech={$tech_requisito->id} OR id_tech={$tech_requisito->id_tech_alternativa})AND custo_pago = 0");
				if ($tech_requisito_query == 0) {
					if (empty($dados_salvos['resposta_ajax'])) {
						$dados_salvos['resposta_ajax'] = "O Império não tem os pré-requisitos necessários! É necessário ter a(s) Tech(s): ";	
					}
					$dados_salvos['resposta_ajax'] .= $tech_requisito->nome."; ";
				}
			}
		}
		
		//Verifica se o Império tem Pesquisa suficiente
		$id_recurso_pesquisa = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='Pesquisa'");
		
		if (!empty($_POST['id'])) {
			if ($_POST['custo_pago'] == 0 && $tech_imperio->custo_pago != 0) {
				$custo_a_pagar = $tech->custo - $tech_imperio->custo_pago;
			} else {
				$custo_a_pagar = $_POST['custo_pago'] - $tech_imperio->custo_pago;
			}
		} else {
			if ($_POST['custo_pago'] == 0) {
				$custo_a_pagar = $tech->custo;
			} else {
				$custo_a_pagar = $_POST['custo_pago'];
			}
		}
		
		$turno = new turno();
		$pesquisas_imperio = $wpdb->get_var("SELECT qtd FROM colonization_imperio_recursos WHERE id_imperio={$_POST['id_imperio']} AND turno={$turno->turno} AND id_recurso={$id_recurso_pesquisa}");
		
		if ($pesquisas_imperio < $custo_a_pagar && $_POST['tech_inicial'] != 1) {
			$imperio = new imperio($_POST['id_imperio']);
			if (empty($dados_salvos['resposta_ajax'])) {
				$dados_salvos['resposta_ajax'] = "O {$imperio->nome} precisa de {$custo_a_pagar} Pesquisa(s) para concluir essa ação, porém tem apenas {$pesquisas_imperio} Pesquisas(s). tech_inicial: {$_POST['tech_inicial']}";
				$dados_salvos['custo_pago'] = $pesquisas_imperio;
			}
		} 
		
		if (empty($dados_salvos['resposta_ajax'])) {
			$dados_salvos['resposta_ajax'] = "OK!";
			
			//Pode cobrar o custo da Tech, caso não seja uma Tech Inicial
			if ($_POST['tech_inicial'] != 1) {
				$consome_pesquisa = $wpdb->query("UPDATE colonization_imperio_recursos SET qtd=qtd-{$custo_a_pagar} WHERE id_imperio={$_POST['id_imperio']} AND turno={$turno->turno} AND id_recurso={$id_recurso_pesquisa}");
			}
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
		
		if (empty($resposta)) {//Não existe o ponto, pode adicionar
			$resposta = $wpdb->query("INSERT INTO {$_POST['tabela']} SET id_estrela={$_POST['id_estrela']}, id_imperio={$_POST['id_imperio']}");
		} else {//Existe o ponto, é para remover
			$resposta = $wpdb->query("DELETE FROM {$_POST['tabela']} WHERE id_estrela={$_POST['id_estrela']} AND id_imperio={$_POST['id_imperio']}");
		}

		$dados_salvos['resposta_ajax'] = "OK!";

		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}

	/***********************
	function valida_colonia ()
	----------------------
	Valida o objeto desejado
	***********************/	
	function valida_colonia() {
		global $wpdb; 
		$wpdb->hide_errors();
		
		$dados_salvos['resposta_ajax'] = "OK!";
		
		$id_existe = "";
		if ($_POST['id'] !== "") {//Se o valor estiver em branco, é um novo objeto.
			$id_existe = " AND id != {$_POST['id']}";			
		}
		
		$resposta = $wpdb->get_var("SELECT id FROM colonization_imperio_colonias WHERE id_planeta = {$_POST['id_planeta']} AND turno = {$_POST['turno']}{$id_existe}");

		if (!empty($resposta)) {
			$dados_salvos['resposta_ajax'] = "Este planeta já é a colônia de outro Império!";
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
		
		$planeta = new planeta($_POST['id_planeta']);
		$imperio = new imperio($_POST['id_imperio']);
		
		if (($planeta->inospito == 1 && $planeta->terraforma == 0) && $imperio->coloniza_inospito != 1) {
			if ($_POST['pop'] > $planeta->pop_inospito) {
				$dados_salvos['resposta_ajax'] = "Este planeta é inóspito! O máximo de Pop que ele suporta é {$planeta->pop_inospito} Pop";
			}
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
		$imperio = new imperio($_POST['id_imperio']);
		
		if (($planeta->inospito == 1 && $planeta->terraforma == 0) && $imperio->coloniza_inospito != 1) {
			if (($colonia_destino->pop + $_POST['pop']) > $planeta->pop_inospito) {
				$dados_salvos['resposta_ajax'] = "O planeta de destino é inóspito! O máximo de Pop que ele suporta é {$planeta->pop_inospito} Pop";
			}
		}

		if ($dados_salvos['resposta_ajax'] == "SALVO!") {
			$resultado = $wpdb->query("UPDATE colonization_imperio_colonias SET pop=pop-{$_POST['pop']} WHERE id={$colonia_origem->id}");
			$resultado = $wpdb->query("UPDATE colonization_imperio_colonias SET pop=pop+{$_POST['pop']} WHERE id={$colonia_destino->id}");
			$sem_balanco = true;
			$imperio->acoes = new acoes($_POST['id_imperio'],$_POST['turno'],$sem_balanco);
			
			$colonias = [];
			$colonias[0] = $colonia_origem->id;
			$colonias[1] = $colonia_destino->id;
			$produtos_acao = $this->produtos_acao($imperio, 0, $colonias);
			$debug .= $produtos_acao['debug'];
			$produtos_acao['debug'] = "";
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
			$query = "SELECT id FROM colonization_instalacao_recursos WHERE id_recurso={$_POST['id_recurso']} AND id_instalacao={$_POST['id_instalacao']}  AND consome={$_POST['consome']} AND id != {$_POST['id']}";
		}
		
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

		$query = "UPDATE colonization_planeta_recursos SET id_recurso={$_POST['id_recurso']} WHERE id_recurso={$_POST['id_recurso_original']} AND id_planeta={$_POST['id_planeta']}";
		
		$resposta = $wpdb->query($query);

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
	function valida_colonia_instalacao() {
		global $wpdb; 
		$wpdb->hide_errors();
		
		$dados_salvos['debug'] = "";
		
		$turno = new turno();
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

		if ($roles != "administrator" && ($id_imperio != $_POST['id_imperio'])) {
			$dados_salvos['resposta_ajax'] = "Você não está autorizado a realizar esta operação! {$id_imperio} {$_POST['id_imperio']}";
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta
		}
		
		if ($turno->encerrado == 1 && $roles != "administrator") {
			$dados_salvos['resposta_ajax'] = "O Turno {$turno->turno} foi ENCERRADO e não pode mais receber alterações!";
		
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta
		}
		
		$planeta = new planeta($_POST['id_planeta']);
		$id_colonia = $wpdb->get_var("SELECT id FROM colonization_imperio_colonias WHERE id_planeta={$planeta->id} AND turno={$turno->turno}");
		$colonia = new colonia($id_colonia);
		$imperio = new imperio($colonia->id_imperio);
		
		$instalacao = new instalacao($_POST['id_instalacao']);

		$nivel_original = 0;
		if ($_POST['id'] != "") {//Se o valor estiver em branco, é um novo objeto.
			//Realiza a atualização do histórico de upgrades
			$colonia_instalacao = new colonia_instalacao($_POST['id']);
			$nivel_original = $colonia_instalacao->nivel;
			
			if ($_POST['nivel'] != $colonia_instalacao->nivel || $_POST['id_instalacao'] != $colonia_instalacao->id_instalacao) {//É uma atualização! Pode ser um upgrade ou um downgrade
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

			//Atualiza a ação relativa à esta Instalação, reduzindo a Pop
			$fator = floor(($colonia_instalacao->nivel/$_POST['nivel'])*100)/100;
			$wpdb->query("UPDATE colonization_acoes_turno SET pop=floor(pop*{$fator}) WHERE id_planeta_instalacoes={$_POST['id']} AND turno={$turno->turno}");
			$dados_salvos['debug'] .= "UPDATE colonization_acoes_turno SET pop=floor(pop*{$fator}) WHERE id_planeta_instalacoes={$_POST['id']} AND turno={$turno->turno} \n";
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
					$dados_salvos['debug'] = "SELECT id FROM colonization_imperio_techs WHERE id_imperio={$imperio->id} AND (id_tech={$tech_requisito[$nivel_tech]->id} OR id_tech={$tech_requisito[$nivel_tech]->id_tech_alternativa}) AND custo_pago=0 \n";
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

			if ($_POST['id'] == "" && (($planeta->instalacoes + $instalacao->slots) > $planeta->tamanho)) {
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
				";
				
				if ($instalacao->limite <= $instalacoes_no_planeta) {
						$texto_limite = "{$instalacao->limite} Instalações";
					if ($instalacao->limite == 1) {
						$texto_limite = "uma Instalação";
					}
					$dados_salvos['resposta_ajax'] .= "Não é possível construir outro(a) {$instalacao->nome}. O limite é de {$texto_limite}.";
				}
			}

			//Verifica se o Império tem os Recursos para construir ou realizar o upgrade
			$niveis = $_POST['nivel'] - $nivel_original;
			$dados_salvos['debug'] .= "{$niveis} = {$_POST['nivel']} - {$nivel_original}; \n";
			
			//Se substituiu uma Instalação, DEVOLVE os recursos da Instalação antiga
			if (!empty($instalacao_antiga)) {
				$niveis = $_POST['nivel'];//Ajusta para cobrar o valor TOTAL da nova Instalação
				
				//Devolve os recursos
				$chave = 0;
				if ($instalacao_antiga->custos != "") {
					$custos = explode(";",$instalacao_antiga->custos);
					
					$recursos_devolve = [];
					
					foreach ($custos as $chave_recurso => $recurso) {
						$recursos = explode("=",$recurso);
						$id_recurso = $recursos[0];
						$qtd = $recursos[1];
					
						$custo_recursos = $qtd*$nivel_original;
						$query_update_recursos[$chave] = "UPDATE colonization_imperio_recursos SET qtd=qtd+{$custo_recursos} WHERE id_imperio={$imperio->id} AND id_recurso={$id_recurso} AND turno={$turno->turno}";
						$recursos_devolve[$id_recurso] = $custo_recursos;
						$chave++;
					}
				}				
			}

			if ($niveis > 0) {
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
						if ($_POST['id'] != "" && $_POST['instalacao_inicial'] != 1) {//Uma instalação inicial é gratuita, desde que esteja sendo criada.
							$qtd_imperio = $qtd_imperio + $recursos_devolve[$id_recurso];
							$custo_recursos = $qtd*$niveis;
							$query_update_recursos[$chave] = "UPDATE colonization_imperio_recursos SET qtd=qtd-{$custo_recursos} WHERE id_imperio={$imperio->id} AND id_recurso={$id_recurso} AND turno={$turno->turno}";
							$chave++;
							if ($qtd_imperio < $custo_recursos) {
								$dados_salvos['resposta_ajax'] = "O Império não tem Recursos suficientes para concluir essa operação.";	
							}
						}
					}
				}
			} 
		}
		
		if (empty($dados_salvos['resposta_ajax'])) {
			$dados_salvos['resposta_ajax'] = "OK!";

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
				
				$resposta = $this->salva_objeto(false); //Define que NÃO é pra responder com wp_die
				
				$dados_salvos['recursos_atuais'] = $imperio->exibe_recursos_atuais();
				$dados_salvos['resposta_ajax'] = "SALVO!";
			}
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

		//$resposta['debug'] = "";
		
		$resposta = $this->salva_objeto(false); //Define que NÃO é pra responder com wp_die
		//Como salvou uma ação, precisa REMOVER o antigo balanço dos recursos do banco de dados e salvar o novo
		$sem_balanco = true;
		$acoes = new acoes($_POST['id_imperio'],$_POST['turno'],$sem_balanco);
 		$chave_id_planeta_instalacoes = array_search($_POST['id_planeta_instalacoes'], $acoes->id_planeta_instalacoes);
		$acoes->pop[$chave_id_planeta_instalacoes] = $_POST['pop'];
		$acoes->desativado[$chave_id_planeta_instalacoes] = $_POST['desativado'];
		$acoes->pega_balanco_recursos($_POST['id_planeta_instalacoes'], true); //Recalcula os balanços
		$resposta['debug'] = $resposta['debug'] + "Salvando os Balanços... \n";
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
		$wpdb->hide_errors();
		
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
				$where .= " AND $chave='$valor'";
			}
			$where = substr($where,5);
			$dados_salvos = $wpdb->get_results("SELECT * FROM {$_POST['tabela']} WHERE {$where}");
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
		$wpdb->hide_errors();
		
		$where[$_POST['where_clause']]=$_POST['where_value'];
		
		$resposta = $wpdb->delete($_POST['tabela'],$where);

		if ($resposta !== false) {
			$dados_salvos['resposta_ajax'] = "DELETADO!";
		} else {
			$dados_salvos['resposta_ajax'] = "Ocorreu um erro desconhecido! Por favor, tente novamente!";
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
		$wpdb->hide_errors();
		$turno = new turno();
		$colonia_instalacao = new colonia_instalacao($_POST[id]);

		if ($colonia_instalacao->turno_destroi !== null) {
			$query = "UPDATE colonization_planeta_instalacoes SET turno_destroi = null WHERE id={$_POST[id]}";
		} else {
			$query = "UPDATE colonization_planeta_instalacoes SET turno_destroi = {$turno->turno} WHERE id={$_POST[id]}";
		}
		$resposta = $wpdb->query($query);
		
		if ($resposta !== false) {
			$dados_salvos = $wpdb->get_results("SELECT * FROM colonization_planeta_instalacoes WHERE id={$_POST[id]}");
			if ($dados_salvos[0]->turno_destroi === null) {
				$dados_salvos[0]->turno_destroi = "";
			}
			$dados_salvos['resposta_ajax'] = "OK!";
			
		} else {
			$dados_salvos['resposta_ajax'] = "Ocorreu um erro desconhecido! Por favor, tente novamente!";
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
		$wpdb->hide_errors();		
		$dados_salvos = [];
		$debug = "";
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
		if ($roles != "administrator" && $turno->encerrado == 1 ) {
			$dados_salvos['resposta_ajax'] = "O Turno {$turno->turno} foi ENCERRADO e não pode mais receber alterações!";
		
			echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
			wp_die(); //Termina o script e envia a resposta
		}
			
			$start_time = hrtime(true);
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
		$ajax_valida = true;
		$acoes->pega_balanco_recursos($_POST['id_planeta_instalacoes']); //Recalcula os balanços
			$debug .= $acoes->debug;
			$acoes->debug = "";
			$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
			$debug .= "valida_acao() -> \$acoes->pega_balanco_recursos() {$diferenca}ms \n";
		
		$mdo_planeta = $acoes->mdo_planeta($planeta->id);
		$pop_planeta = $colonia->pop;
		
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
			if (empty($recurso[$id_recurso])) {
				$recurso[$id_recurso] = new recurso($id_recurso);
			}
			if ($recurso[$id_recurso]->extrativo == 1 && $instalacao->nao_extrativo != true) {
				$id_planeta_recurso = $wpdb->get_var("SELECT id FROM colonization_planeta_recursos WHERE id_planeta={$planeta->id} AND id_recurso={$id_recurso_produz} AND turno={$_POST['turno']}");
				if (!empty($id_planeta_recurso)) {
					$planeta_recursos = new planeta_recurso($id_planeta_recurso);	
				} else {//Caso o planeta não tenha o recurso...
					$planeta_recursos = new recurso($id_recurso_produz);
					$dados_salvos['balanco_acao'] .= "Reservas Planetárias de {$recurso[$id_recurso]->nome}, ";
				}
	
				if ($acoes->recursos_produzidos_planeta[$id_recurso_produz][$planeta->id] > $planeta_recursos->qtd_disponivel) {
					$dados_salvos['balanco_acao'] .= "Reservas Planetárias de {$planeta_recursos->recurso->nome} (Extrai {$acoes->recursos_produzidos_planeta[$id_recurso_produz][$planeta->id]}, Reservas {$planeta_recursos->qtd_disponivel}), ";
				}
			}
		}
			$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
			$debug .= "valida_acao() -> Verifica Reservas Planetárias {$diferenca}ms \n";

		//Verifica se o balanço de recursos mais o estoque do Império são suficientes
		$imperio_recursos = new imperio_recursos($_POST['id_imperio'],$_POST['turno']);
		$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
		$debug .= "valida_acao() -> new Imperio_Recursos {$diferenca}ms \n";
		
		foreach ($acoes->recursos_balanco as $id_recurso => $valor) {
			if (empty($recurso[$id_recurso])) {
				$recurso[$id_recurso] = new recurso($id_recurso);
				$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
				$debug .= "valida_acao() -> Verifica Estoque Império: new Recurso {$diferenca}ms \n";
			}
			
			if ($recurso[$id_recurso]->acumulavel == 1) {

				$chave_id_recurso = array_search($id_recurso,$imperio_recursos->id_recurso);
				$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
				$debug .= "valida_acao() -> Verifica Estoque Império: array_search {$diferenca}ms \n";
				$balanco = $imperio_recursos->qtd[$chave_id_recurso] + $valor;
			} else {
				$balanco = $valor;
			}
			
			if ($balanco < 0 && $recurso[$id_recurso]->nome != "Poluição") {//A poluição pode ser negativa pois isso significa redução na poluição do planeta
				$dados_salvos['balanco_acao'] .= "{$recurso[$id_recurso]->nome} ({$balanco}), ";
			}
		}
			$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
			$debug .= "valida_acao() -> Verifica Estoque Império {$diferenca}ms \n";

		if ($dados_salvos['balanco_acao'] != "") {
			$dados_salvos['balanco_acao'] = substr($dados_salvos['balanco_acao'],0,-2);
		}


		if ($_POST['desativado'] == 1 || (!$instalacao->desguarnecida && $_POST['pop'] == 0)) {//Se for para DESATIVAR uma Instalação, não precisa fazer os balanços
			//EXCETO se estiver desativando uma produtora de Energia. Nesse caso, o Jogador é informado que só pode desativar a geradora se o balanço de energia for maior ou igual à zero.
			$chave_recurso = array_search($id_energia,$instalacao->recursos_produz);
			if ($chave_recurso !== false && $dados_salvos['balanco_acao'] != "") {
				$dados_salvos['balanco_acao'] .= ". \n\nUma geradora de Energia só pode ser desativada se o balanço de Energia estiver zerado ou positivo!";
			} else {
				$dados_salvos['balanco_acao'] = "";
			}
		}
		
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
	function produtos_acao()
	----------------------
	Pega os resultados da ação
	***********************/	
	function produtos_acao($imperio, $id_planeta_instalacoes, $colonias) {
		global $start_time;
		$dados_salvos = [];
		
		$dados_salvos['debug'] = "";
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
		$dados_salvos['pop_mdo_planeta'] = $imperio->acoes->exibe_pop_mdo_planeta($_POST['id_planeta']);
			$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
			$dados_salvos['debug'] .= "produtos_acao() -> \$imperio->acoes->exibe_pop_mdo_planeta() {$diferenca}ms \n";		
		$pop_sistema = $imperio->acoes->pop_mdo_sistema($planeta->id_estrela);
			$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
			$dados_salvos['debug'] .= "produtos_acao() -> \$imperio->acoes->pop_mdo_sistema() {$diferenca}ms \n";		
		
		//$id_planeta_instalacoes
		$dados_salvos['id_planeta_instalacoes_produz_consome'] = "";
		if (!empty($imperio->acoes->recursos_produzidos_id_planeta_instalacoes[$id_planeta_instalacoes])) {
			foreach ($imperio->acoes->recursos_produzidos_id_planeta_instalacoes[$id_planeta_instalacoes] as $id_recurso => $qtd) {
				$recurso = new recurso($id_recurso);
				$dados_salvos['id_planeta_instalacoes_produz_consome'] .= "{$recurso->nome}: {$qtd}; ";
			}
		}
		
		if (!empty($imperio->acoes->recursos_consumidos_id_planeta_instalacoes[$id_planeta_instalacoes])) {
			foreach ($imperio->acoes->recursos_consumidos_id_planeta_instalacoes[$id_planeta_instalacoes] as $id_recurso => $qtd) {
				$recurso = new recurso($id_recurso);
				$dados_salvos['id_planeta_instalacoes_produz_consome'] .= "{$recurso->nome}: <span style='color: #FF2222;'>-{$qtd}</span>; ";
			}
		}
		
		$pop_disponivel_sistema = $pop_sistema['pop'] - $pop_sistema['mdo'];
		$dados_salvos['mdo_sistema'] = "({$pop_disponivel_sistema})";
		
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