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
		add_action( 'wp_ajax_salva_objeto', array ($this, 'salva_objeto') );	
		add_action( 'wp_ajax_deleta_objeto', array ($this, 'deleta_objeto') );
		add_action( 'wp_ajax_salva_estrela', array ($this, 'salva_estrela') );	
		add_action( 'wp_ajax_deleta_estrela', array ($this, 'deleta_estrela') );			
	}
	
	
	/***********************
	function salva_objeto ()
	----------------------
	Salva o objeto desejado
	***********************/	
	function salva_objeto() {
		global $wpdb; 
		
		$query = "SELECT id FROM {$_POST['tabela']} WHERE {$_POST['where_clause']}={$_POST['where_value']}";
		$resposta = $wpdb->query($query);

		foreach ($_POST as $chave => $valor) {
			if ($chave!='tabela' && $chave!='where_clause' && $chave!='post_type' && $chave!='action' && $chave!='where_value') {
				$dados[$chave] = $valor;
			}
		}
		
		$where[$_POST['where_clause']]=$_POST['where_value'];
		
		if ($resposta === 0) {//Se o objeto não existe, cria
			$resposta = $wpdb->insert($_POST['tabela'],$dados);
		} elseif ($resposta === 1) {//Se existir, atualiza
			$resposta = $wpdb->update($_POST['tabela'],$dados,$where);
		} else {
			$html = "Erro!";
			echo $html; //Envia a resposta via echo
			wp_die(); //Termina o script e envia a resposta
		}
		
		if ($resposta !== false) {
			$html = "SALVO!";
		} else {
			$html = "Ocorreu um erro desconhecido! Por favor, tente novamente!";
		}
		
		echo $html; //Envia a resposta via echo
		wp_die(); //Termina o script e envia a resposta
	}

	/***********************
	function deleta_objeto ()
	----------------------
	Deleta o objeto desejado
	***********************/	
	function deleta_objeto() {
		global $wpdb; 
		
		$where[$_POST['where_clause']]=$_POST['where_value'];
		
		$resposta = $wpdb->delete($_POST['tabela'],$where);

		if ($resposta !== false) {
			$html = "DELETADO!";
		} else {
			$html = "Ocorreu um erro desconhecido! Por favor, tente novamente!";
		}
	
		echo $html; //Envia a resposta via echo
		wp_die(); //Termina o script e envia a resposta

	}
}
?>