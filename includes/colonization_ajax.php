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
		add_action('wp_ajax_valida_colonia_recurso', array ($this, 'valida_colonia_recurso'));
		add_action('wp_ajax_valida_colonia_instalacao', array ($this, 'valida_colonia_instalacao'));
		add_action('wp_ajax_destruir_instalacao', array ($this, 'destruir_instalacao'));
		add_action('wp_ajax_dados_imperio', array ($this, 'dados_imperio'));
		add_action('wp_ajax_produtos_acao', array ($this, 'produtos_acao'));
		add_action('wp_ajax_valida_acao', array ($this, 'valida_acao'));
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
	function valida_colonia_recurso ()
	----------------------
	Valida o objeto desejado
	***********************/	
	function valida_colonia_recurso() {
		global $wpdb; 
		$wpdb->hide_errors();

		if ($_POST['id'] == "") {//Se o valor estiver em branco, é um novo objeto.
			$query = "SELECT id FROM colonization_planeta_recursos WHERE id_recurso={$_POST['id_recurso']} AND id_planeta={$_POST['id_planeta']}";
		} else {
			$query = "SELECT id FROM colonization_planeta_recursos WHERE id_recurso={$_POST['id_recurso']} AND id_planeta={$_POST['id_planeta']} AND id != {$_POST['id']}";
		}
		
		$resposta = $wpdb->query($query);

		if ($resposta === 0) {
			$dados_salvos['resposta_ajax'] = "OK!";
		} else {
			$dados_salvos['resposta_ajax'] .= "Você não pode cadastrar o mesmo recurso para a mesma colônia duas vezes!";
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
			SELECT COUNT(cpi.id) as instalacoes FROM 
			(
			SELECT id 
			FROM colonization_planeta_instalacoes WHERE id_planeta={$_POST['id_planeta']}  AND turno_destroi IS NULL
			) AS cpi";
		} else {
			$dados_salvos['resposta_ajax'] = "OK!";
		}
		
		$resposta = $wpdb->get_results($query);
		$planeta = new planeta($_POST['id_planeta']);
		if ($resposta[0]->instalacoes < $planeta->tamanho) {
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
			
			//TODO -- Criar a função para pegar a pontuação do Império
			$dados_salvos[0]->pontuacao = 999;
			
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
		
		$resultados = $wpdb->get_results("SELECT mdo FROM (SELECT (cimc.pop - cat.pop) AS mdo FROM
			(SELECT turno, id_imperio, id_instalacao, id_planeta, SUM(pop) AS pop
			FROM colonization_acoes_turno 
			WHERE id_imperio={$_POST['id_imperio']} AND turno={$_POST['turno']} AND id_planeta={$_POST['id_planeta']}
			GROUP BY id_planeta
			) AS cat
			JOIN colonization_imperio_colonias AS cimc
			ON cimc.id_imperio = cat.id_imperio AND cimc.id_planeta = cat.id_planeta) AS tabela_balanco
			WHERE mdo < 0
			");
		
		foreach ($resultados as $resultado) {
			$dados_salvos['balanco_acao'] = "Mão-de-Obra, ";
		}
		
		$resultados = $wpdb->get_results("
		SELECT nome, (producao-consumo+estoque) AS balanco FROM (
		SELECT tabela_produz.nome, tabela_produz.producao, (CASE WHEN tabela_consome.producao IS NULL THEN 0 ELSE tabela_consome.producao END) AS consumo, cimr.qtd AS estoque FROM (
		SELECT cir.id_recurso, cat.turno, cat.id_imperio, cr.nome, (CASE WHEN SUM(FLOOR((cir.qtd_por_nivel * cpi.nivel * cat.pop)/10)) IS NULL THEN 0 ELSE SUM(FLOOR((cir.qtd_por_nivel * cpi.nivel * cat.pop)/10)) END) AS producao
			FROM 
			(SELECT turno, id_imperio, id_instalacao, id_planeta, pop
			FROM colonization_acoes_turno 
			UNION ALL
			SELECT {$_POST['turno']} AS turno, {$_POST['id_imperio']} AS id_imperio, {$_POST['id_instalacao']} AS id_instalacao, {$_POST['id_planeta']} AS id_planeta, {$_POST['pop']} AS pop
			) AS cat
			JOIN colonization_planeta_instalacoes AS cpi
			ON cpi.id_instalacao = cat.id_instalacao AND cpi.id_planeta = cat.id_planeta
			JOIN colonization_instalacao_recursos AS cir
			ON cir.id_instalacao = cat.id_instalacao
			JOIN colonization_recurso AS cr
			ON cir.id_recurso = cr.id
			WHERE cat.id_imperio={$_POST['id_imperio']} AND cat.turno={$_POST['turno']} AND cir.consome=false AND cpi.turno_destroi IS NULL
			GROUP BY cr.nome) AS tabela_produz
		LEFT JOIN (
		SELECT cir.id_recurso, cat.turno, cat.id_imperio, cr.nome, (CASE WHEN SUM(FLOOR((cir.qtd_por_nivel * cpi.nivel * cat.pop)/10)) IS NULL THEN 0 ELSE SUM(FLOOR((cir.qtd_por_nivel * cpi.nivel * cat.pop)/10)) END) AS producao
			FROM 
			(SELECT turno, id_imperio, id_instalacao, id_planeta, pop
			FROM colonization_acoes_turno 
			UNION ALL
			SELECT {$_POST['turno']} AS turno, {$_POST['id_imperio']} AS id_imperio, {$_POST['id_instalacao']} AS id_instalacao, {$_POST['id_planeta']} AS id_planeta, {$_POST['pop']} AS pop
			) AS cat
			JOIN colonization_planeta_instalacoes AS cpi
			ON cpi.id_instalacao = cat.id_instalacao AND cpi.id_planeta = cat.id_planeta
			JOIN colonization_instalacao_recursos AS cir
			ON cir.id_instalacao = cat.id_instalacao
			JOIN colonization_recurso AS cr
			ON cir.id_recurso = cr.id
			WHERE cat.id_imperio={$_POST['id_imperio']} AND cat.turno={$_POST['turno']} AND cir.consome=true AND cpi.turno_destroi IS NULL
			GROUP BY cr.nome) AS tabela_consome
		ON tabela_consome.id_recurso = tabela_produz.id_recurso AND tabela_consome.turno = tabela_produz.turno AND tabela_consome.id_imperio = tabela_produz.id_imperio
		LEFT JOIN colonization_imperio_recursos AS cimr
		ON cimr.id_imperio = tabela_produz.id_imperio AND cimr.id_recurso = tabela_produz.id_recurso AND cimr.turno = tabela_produz.turno) AS tabela_balanco
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
}
?>