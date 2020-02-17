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
		add_action( 'wp_ajax_salva_imperio', array ($this, 'salva_imperio') );	
		add_action( 'wp_ajax_deleta_imperio', array ($this, 'deleta_imperio') );	
	}
	
	
	/***********************
	function salva_imperio ()
	----------------------
	Salva o Império
	***********************/	
	function salva_imperio() {
		global $wpdb; 

		$resposta = $wpdb->query('SELECT ID FROM colonization_imperio WHERE id_jogador='. $_POST['id']);
		
		if ($resposta === 0) {//Se o Império não existir, cria
			$resposta = $wpdb->query('INSERT INTO colonization_imperio SET id_jogador = '.$_POST['id'].', nome="'.$_POST['nome_imperio'].'"');
		} elseif ($resposta === 1) {//Se existir, atualiza
			$resposta = $wpdb->query('UPDATE colonization_imperio SET nome="'.$_POST['nome_imperio'].'" WHERE id_jogador = '.$_POST['id']);
		} else {
			$html = "Erro! Dump dos dados: \$resposta = '$resposta' array('id_jogador' => ".$_POST['id'].", 'nome_imperio' => ".$_POST['nome_imperio'].")";
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
	function deleta_imperio ()
	----------------------
	Deleta o Império
	***********************/	
	function deleta_imperio() {
		global $wpdb; 
		
		$resposta = $wpdb->query('DELETE FROM colonization_imperio WHERE id_jogador='. $_POST['id']);

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