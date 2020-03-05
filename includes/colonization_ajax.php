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
}
?>