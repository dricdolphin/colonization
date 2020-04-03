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
		//TODO -- Adicionar as funções conforme necessário
		add_action('wp_ajax_salva_objeto', array ($this, 'salva_objeto'));
		add_action('wp_ajax_deleta_objeto', array ($this, 'deleta_objeto'));
		add_action('wp_ajax_valida_estrela', array ($this, 'valida_estrela'));
		add_action('wp_ajax_valida_colonia', array ($this, 'valida_colonia'));
		add_action('wp_ajax_valida_instalacao_recurso', array ($this, 'valida_instalacao_recurso'));
		add_action('wp_ajax_valida_planeta_recurso', array ($this, 'valida_planeta_recurso'));
		add_action('wp_ajax_valida_colonia_instalacao', array ($this, 'valida_colonia_instalacao'));
		add_action('wp_ajax_destruir_instalacao', array ($this, 'destruir_instalacao'));
		add_action('wp_ajax_dados_imperio', array ($this, 'dados_imperio'));
		add_action('wp_ajax_produtos_acao', array ($this, 'produtos_acao'));
		add_action('wp_ajax_valida_acao', array ($this, 'valida_acao'));
		add_action('wp_ajax_roda_turno', array ($this, 'roda_turno'));
		add_action('wp_ajax_libera_turno', array ($this, 'libera_turno'));
		add_action('wp_ajax_valida_acao_admin', array ($this, 'valida_acao_admin'));
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
		
		foreach ($recursos as $chave=>$valor) {
			if (!empty($_POST['id'])) {//É uma edição! REVERTE a ação e depois atualiza, para não ficar em duplicidade
					$dados_salvos['html'] .= "UPDATE colonization_imperio_recursos SET qtd=qtd+{$qtds[$chave]} WHERE id={$_POST['id']}<br>";
					$wpdb->query("UPDATE colonization_imperio_recursos SET qtd=qtd+{$qtds[$chave]} WHERE id={$_POST['id']}");
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
	function valida_colonia ()
	----------------------
	Valida o objeto desejado
	***********************/	
	function valida_colonia() {
		global $wpdb; 
		$wpdb->hide_errors();

		if ($_POST['id'] == "") {//Se o valor estiver em branco, é um novo objeto.
			$query = "SELECT id FROM colonization_imperio_colonias WHERE id_planeta = {$_POST['id_planeta']}";
		} else {
			$query = "SELECT id FROM colonization_imperio_colonias WHERE id_planeta = {$_POST['id_planeta']} AND id != {$_POST['id']}";
		}
		
		$resposta = $wpdb->query($query);

		if ($resposta === 0) {
			$dados_salvos['resposta_ajax'] = "OK!";
		} else {
			$dados_salvos['resposta_ajax'] = "Este planeta já é a colônia de outro Império!";
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

		if ($_POST['id'] == "") {//Se o valor estiver em branco, é um novo objeto.
			$query = "
			SELECT SUM(cpi.slots) as instalacoes FROM 
			(
			SELECT cpi.id, ci.slots
			FROM colonization_planeta_instalacoes AS cpi
			JOIN colonization_instalacao AS ci
			ON ci.id = cpi.id_instalacao
			WHERE cpi.id_planeta={$_POST['id_planeta']} AND cpi.turno_destroi IS NULL
			) AS cpi";
		} else {
			$dados_salvos['resposta_ajax'] = "OK!";
		}
		
		$resposta = $wpdb->get_results($query);
		$planeta = new planeta($_POST['id_planeta']);
		$instalacao = new instalacao($_POST['id_instalacao']);
		if ($resposta[0]->instalacoes + $instalacao->slots <= $planeta->tamanho) {
			$dados_salvos['resposta_ajax'] = "OK!";
		} else {
			$dados_salvos['resposta_ajax'] .= "Este planeta já atingiu o número máximo de instalações! Destrua uma instalação antes de criar outra!";
		}

		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}
	
	/***********************
	function salva_objeto ()
	----------------------
	Salva o objeto desejado
	***********************/	
	function salva_objeto() {
		global $wpdb; 
		//$wpdb->hide_errors();
		
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
			$dados_salvos['resposta_ajax'] = "SALVO!";
		} else {
			$dados_salvos['resposta_ajax'] = $wpdb->last_error;
			$dados_salvos['resposta_ajax'] .= "Ocorreu um erro ao tentar salvar o objeto! Por favor, tente novamente!";
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
		//$wpdb->hide_errors();
		$dados_salvos = [];
		
		$dados_salvos = $wpdb->get_results("SELECT * FROM colonization_imperio WHERE id={$_POST['id']}");
		
		if (isset($dados_salvos[0])) {
			$dados_salvos['resposta_ajax'] = "OK!";
			$dados_salvos[0]->pop = $wpdb->get_var("SELECT 
			(CASE 
			WHEN SUM(pop) IS NULL THEN 0
			ELSE SUM(pop)
			END) AS pop
			FROM colonization_imperio_colonias
			WHERE id_imperio={$_POST['id']}");
			
		} else {
			$dados_salvos['resposta_ajax'] = $wpdb->last_error;
			$dados_salvos['resposta_ajax'] .= "Ocorreu um erro ao tentar salvar o objeto! Por favor, tente novamente!";
		}
		
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}
	
	/***********************
	function dados_imperio ()
	----------------------
	Pega os dados do Império
	***********************/	
	function produtos_acao() {
		$dados_salvos = [];
		
		$imperio = new imperio($_POST['id_imperio']);
		$acoes = new acoes($imperio->id);
		
		$dados_salvos['lista_colonias'] = $imperio->exibe_lista_colonias();
		$dados_salvos['recursos_produzidos'] = $acoes->exibe_recursos_produzidos();
		$dados_salvos['recursos_consumidos'] = $acoes->exibe_recursos_consumidos();
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
		global $wpdb; 
		//$wpdb->hide_errors();		

		$dados_salvos = [];
		$dados_salvos['balanco_acao'] = "";
		
		//Verifica se existe MdO suficiente na Colônia (ou no Sistema, para o caso de unidades Autônomas)
		$instalacao = new instalacao($_POST['id_instalacao']);

		if ($instalacao->autonoma) {
			$planeta = new planeta($_POST['id_planeta']);
			
			$resultados = $wpdb->get_results("
				SELECT mdo 
				FROM 
					(SELECT (cimc.pop - SUM(cat.pop)) AS mdo FROM
						(SELECT turno, id_imperio, id_instalacao, id_planeta_instalacoes, id_planeta, pop AS pop
						FROM colonization_acoes_turno
						WHERE id_imperio={$_POST['id_imperio']} AND turno={$_POST['turno']} AND id_planeta IN (SELECT id FROM colonization_planeta WHERE id_estrela={$planeta->id_estrela})
						UNION ALL
						SELECT {$_POST['turno']} AS turno, {$_POST['id_imperio']} AS id_imperio, {$_POST['id_instalacao']} AS id_instalacao, {$_POST['id_planeta_instalacoes']} AS id_planeta_instalacoes, {$_POST['id_planeta']} AS id_planeta, {$_POST['pop']} AS pop FROM DUAL
						) AS cat
					JOIN colonization_imperio_colonias AS cimc
					ON cimc.id_imperio = cat.id_imperio 
					AND cimc.id_planeta = cat.id_planeta
					AND cimc.turno={$_POST['turno']}
					) AS tabela_balanco
					WHERE mdo < 0
					");
		} else {
			//Para construções locais, precisa verificar se tem MdO suficiente no planeta E também se o MdO do sistema não está sendo ultrapassado
			
			$resultados = $wpdb->get_results("
			SELECT mdo 
			FROM 
				(SELECT (cimc.pop - SUM(cat.pop)) AS mdo FROM
					(SELECT turno, id_imperio, id_instalacao, id_planeta_instalacoes, id_planeta, pop AS pop
					FROM colonization_acoes_turno
					WHERE id_imperio={$_POST['id_imperio']} AND turno={$_POST['turno']} AND id_planeta={$_POST['id_planeta']}
					UNION ALL
					SELECT {$_POST['turno']} AS turno, {$_POST['id_imperio']} AS id_imperio, {$_POST['id_instalacao']} AS id_instalacao, {$_POST['id_planeta_instalacoes']} AS id_planeta_instalacoes,{$_POST['id_planeta']} AS id_planeta, {$_POST['pop']} AS pop FROM DUAL
					) AS cat
				JOIN colonization_imperio_colonias AS cimc
				ON cimc.id_imperio = cat.id_imperio 
				AND cimc.id_planeta = cat.id_planeta
				AND cimc.turno={$_POST['turno']}
				) AS tabela_balanco
				WHERE mdo < 0
				");
			
			if (empty($resultados)) {//Passou no local, vamos verificar para o sistema
				$planeta = new planeta($_POST['id_planeta']);
				
				$resultados = $wpdb->get_results("
				SELECT mdo 
				FROM 
					(SELECT (cimc.pop - SUM(cat.pop)) AS mdo FROM
						(SELECT turno, id_imperio, id_instalacao, id_planeta_instalacoes, id_planeta, pop AS pop
						FROM colonization_acoes_turno
						WHERE id_imperio={$_POST['id_imperio']} AND turno={$_POST['turno']} AND id_planeta IN (SELECT id FROM colonization_planeta WHERE id_estrela={$planeta->id_estrela})
						UNION ALL
						SELECT {$_POST['turno']} AS turno, {$_POST['id_imperio']} AS id_imperio, {$_POST['id_instalacao']} AS id_instalacao, {$_POST['id_planeta_instalacoes']} AS id_planeta_instalacoes, {$_POST['id_planeta']} AS id_planeta, {$_POST['pop']} AS pop FROM DUAL
						) AS cat
					JOIN colonization_imperio_colonias AS cimc
					ON cimc.id_imperio = cat.id_imperio 
					AND cimc.id_planeta = cat.id_planeta
					AND cimc.turno={$_POST['turno']}
					) AS tabela_balanco
					WHERE mdo < 0
					");
				
				//***DEBUG!
				$user = wp_get_current_user();
				$roles = $user->roles[0];

				if ($roles == "administrator") {
					if (empty($resultados == "")) {
						$dados_salvos['debug'] = "
				SELECT mdo 
				FROM 
					(SELECT (cimc.pop - SUM(cat.pop)) AS mdo FROM
						(SELECT turno, id_imperio, id_instalacao, id_planeta_instalacoes, id_planeta, pop AS pop
						FROM colonization_acoes_turno
						WHERE id_imperio={$_POST['id_imperio']} AND turno={$_POST['turno']} AND id_planeta IN (SELECT id FROM colonization_planeta WHERE id_estrela={$planeta->id_estrela})
						UNION ALL
						SELECT {$_POST['turno']} AS turno, {$_POST['id_imperio']} AS id_imperio, {$_POST['id_instalacao']} AS id_instalacao, {$_POST['id_planeta_instalacoes']} AS id_planeta_instalacoes, {$_POST['id_planeta']} AS id_planeta, {$_POST['pop']} AS pop FROM DUAL
						) AS cat
					JOIN colonization_imperio_colonias AS cimc
					ON cimc.id_imperio = cat.id_imperio 
					AND cimc.id_planeta = cat.id_planeta
					AND cimc.turno={$_POST['turno']}
					) AS tabela_balanco
					WHERE mdo < 0
					";	
					}
				
				}
				//****/
					
			}
		}
		
		foreach ($resultados as $resultado) {
			$dados_salvos['balanco_acao'] = "Mão-de-Obra, ";
		}
		
		//Verifica se existe recurso suficiente no planeta para ser extraído (caso seja um recurso extrativo)
		$resultados = $wpdb->get_results("
			SELECT cr.nome, tabela_produz.producao, tabela_produz.id_planeta, cpr.id_planeta, cpr.disponivel
			FROM colonization_recurso AS cr
            LEFT JOIN (
				SELECT cir.id_recurso, cat.turno, cat.id_imperio, cat.id_planeta, SUM(FLOOR((cir.qtd_por_nivel * cpi.nivel * cat.pop)/10)) AS producao
				FROM 
				(SELECT turno, id_imperio, id_instalacao, id_planeta_instalacoes, id_planeta, pop
				FROM colonization_acoes_turno 
				WHERE id_imperio={$_POST['id_imperio']} AND turno={$_POST['turno']} AND id_planeta={$_POST['id_planeta']}
				UNION ALL
				SELECT {$_POST['turno']} AS turno, {$_POST['id_imperio']} AS id_imperio, {$_POST['id_instalacao']} AS id_instalacao, {$_POST['id_planeta_instalacoes']} AS id_planeta_instalacoes,{$_POST['id_planeta']} AS id_planeta, {$_POST['pop']} AS pop FROM DUAL
				) AS cat
				JOIN colonization_planeta_instalacoes AS cpi
				ON cpi.id = cat.id_planeta_instalacoes
				LEFT JOIN colonization_instalacao_recursos AS cir
				ON cir.id_instalacao = cat.id_instalacao
				WHERE cir.consome=false AND cpi.turno_destroi IS NULL
				GROUP BY cir.id_recurso
			) AS tabela_produz
			ON tabela_produz.id_recurso = cr.id
   			LEFT JOIN colonization_planeta_recursos AS cpr
			ON cpr.id_recurso = cr.id
            AND cpr.id_planeta={$_POST['id_planeta']}
			WHERE cr.extrativo=true AND cpr.disponivel < tabela_produz.producao
		");
		
		foreach ($resultados as $resultado) {
			$dados_salvos['balanco_acao'] .= "Reservas Planetárias de {$resultado->nome}, ";
		}
		
		//Faz o balanço dos recursos
		$resultados = $wpdb->get_results("
		SELECT nome, (producao-consumo+estoque) AS balanco
		FROM (
			SELECT cr.nome, (CASE WHEN tabela_produz.producao IS NULL THEN 0 ELSE tabela_produz.producao END) AS producao, 
			(CASE WHEN tabela_consome.producao IS NULL THEN 0 ELSE tabela_consome.producao END) AS consumo, 
			cimr.qtd AS estoque 
			FROM colonization_recurso AS cr
			LEFT JOIN (
				SELECT cir.id_recurso, cat.turno, cat.id_imperio, SUM(FLOOR((cir.qtd_por_nivel * cpi.nivel * cat.pop)/10)) AS producao
				FROM 
				(SELECT turno, id_imperio, id_instalacao, id_planeta_instalacoes, id_planeta, pop
				FROM colonization_acoes_turno 
				WHERE id_imperio={$_POST['id_imperio']} AND turno={$_POST['turno']}
				UNION ALL
				SELECT {$_POST['turno']} AS turno, {$_POST['id_imperio']} AS id_imperio, {$_POST['id_instalacao']} AS id_instalacao, {$_POST['id_planeta_instalacoes']} AS id_planeta_instalacoes,{$_POST['id_planeta']} AS id_planeta, {$_POST['pop']} AS pop FROM DUAL
				) AS cat
				JOIN colonization_planeta_instalacoes AS cpi
				ON cpi.id = cat.id_planeta_instalacoes
				JOIN colonization_instalacao_recursos AS cir
				ON cir.id_instalacao = cat.id_instalacao
				WHERE cir.consome=false AND cpi.turno_destroi IS NULL
				GROUP BY cir.id_recurso
			) AS tabela_produz
			ON tabela_produz.id_recurso = cr.id
			LEFT JOIN (
			SELECT cir.id_recurso, cat.turno, cat.id_imperio, SUM(FLOOR((cir.qtd_por_nivel * cpi.nivel * cat.pop)/10)) AS producao
				FROM 
				(SELECT turno, id_imperio, id_instalacao, id_planeta_instalacoes, id_planeta, pop
				FROM colonization_acoes_turno 
				WHERE id_imperio={$_POST['id_imperio']} AND turno={$_POST['turno']}
				UNION ALL
				SELECT {$_POST['turno']} AS turno, {$_POST['id_imperio']} AS id_imperio, {$_POST['id_instalacao']} AS id_instalacao, {$_POST['id_planeta_instalacoes']} AS id_planeta_instalacoes,{$_POST['id_planeta']} AS id_planeta, {$_POST['pop']} AS pop FROM DUAL
				) AS cat
				JOIN colonization_planeta_instalacoes AS cpi
				ON cpi.id = cat.id_planeta_instalacoes
				JOIN colonization_instalacao_recursos AS cir
				ON cir.id_instalacao = cat.id_instalacao
				WHERE cir.consome=true AND cpi.turno_destroi IS NULL
				GROUP BY cir.id_recurso
			) AS tabela_consome
			ON tabela_consome.id_recurso = cr.id
			LEFT JOIN colonization_imperio_recursos AS cimr
			ON cimr.id_imperio = tabela_produz.id_imperio 
			AND cimr.id_recurso = tabela_produz.id_recurso 
			AND cimr.turno = tabela_produz.turno
		) AS tabela_balanco
		WHERE (producao-consumo+estoque)<0
		");
		
		
		foreach ($resultados as $resultado) {
			$dados_salvos['balanco_acao'] .= "{$resultado->nome}, ";
		}
		
		if ($dados_salvos['balanco_acao'] != "") {
			$dados_salvos['balanco_acao'] = substr($dados_salvos['balanco_acao'],0,-2);
		}
		
		$dados_salvos['resposta_ajax'] = "OK!";
		
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}

	/***********************
	function roda_turno()
	----------------------
	Roda o Turno
	***********************/	
	function roda_turno() {
		global $wpdb;
		$html = [];
		
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
		
		$wpdb->query("UPDATE colonization_turno_atual SET bloqueado=false, data_turno=data_turno");
		
		$dados_salvos['resposta_ajax'] = "OK!";
		
		echo json_encode($dados_salvos); //Envia a resposta via echo, codificado como JSON
		wp_die(); //Termina o script e envia a resposta
	}


}
?>