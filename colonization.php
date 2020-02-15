<?php
/**
 * Plugin Name: Colonization
 * Plugin URI: https://github.com/dricdolphin/colonization
 * Description: Plugin de WordPress com o sistema de jogo de Colonization.
 * Version: 0.1
 * Author: dricdolphin
 * Author URI: https://dricdolphin.com
 */

//Inclui os arquivos necessários para o sistema "Colonization"
include_once('includes/geral.php');
include_once('includes/imperio.php');
include_once('includes/planeta.php');
include_once('includes/instalacao.php');
include_once('includes/recurso.php');
include_once('includes/acoes.php');
include_once('includes/instala_db.php');
include_once('includes/lista_usuarios.php');

//Classe "colonization"
//Classe principal do plugin
class colonization {

	
	/***********************
	function __construct()
	----------------------
	Inicializa o plugin
	***********************/
	function __construct() {
		//Adiciona os "shortcodes" que serão utilizados para exibir os dados do Império
		add_shortcode('colonization_exibe_imperio',array($this,'colonization_exibe_imperio')); //Exibe os dados do Império	
	}

	/******************
	function colonization_install()
	-----------
	Instala o plugin e cria os objetos necessários para rodar o sistema "Colonization"
	******************/
	function colonization_install() {
		//TODO - Sistema de instalação
		
		//Cria o banco de dados
		$instala_db = new instala_db();
	}

	/******************
	function colonization_deactivate()
	-----------
	Desinstala o plugin.
	******************/
	function colonization_deactivate() {
	//TODO - Rotinas de desativação

	}
	
	/***********************
	function colonization_exibe_imperio($atts = [], $content = null)
	----------------------
	Chamado pelo shortcode [colonization_exibe_imperio]
	$atts = [] - lista de atributos dentro do shortcode 
	(por exemplo, o shortcode [colonization_exibe_imperio id_imperio="1"] poderia exibir
	os dados do Império com id="1"
	***********************/	
	function colonization_exibe_imperio($atts = [], $content = null) {
		//Cria o Império
		//var_dump($atts);
		if (isset($atts['id'])) {
			$imperio = new imperio($atts['id']);
			//var_dump($atts);
		} else {
			$imperio = new imperio();
		}
		
		//Envia os dados do Império
		return $imperio->imperio_exibe_imperio();
	}

	/******************
	function colonization_setup_menu()
	-----------
	Adiciona o menu do plugin no menu de Admin do WordPress
	******************/
	function colonization_setup_menu() {
		//Adiciona o menu "Colonization", que pode ser acessador por quem tem a opção de Admin ('manage_options')
		add_menu_page('Gerenciar Impérios','Colonization','manage_options','colonization_admin_menu',array($this,'colonization_admin_menu'));
		add_submenu_page('colonization_admin_menu','Roda Turno','Roda Turno','manage_options','colonization_admin_roda_turno',array($this,'colonization_admin_roda_turno'));
		
	}

	/******************
	function colonization_admin_menu()
	-----------
	Exibe a página principal do plugin
	******************/
	function colonization_admin_menu() {
		global $html_lista_usuarios;
		$html = "
		<link rel='stylesheet' type='text/css' href='../wp-content/plugins/colonization/colonization.css'>
		<script src='../wp-content/plugins/colonization/includes/edita_imperio.js?v=202002151913'></script>
		<script>";
		
		$lista_usuarios = new lista_usuarios();
		$html .= $lista_usuarios->html_lista_usuarios;
		
		$html .="</script>
		<div><h2>COLONIZATION</h2></div>
		<div>
		<table class='wp-list-table widefat fixed striped users' id='tabela_imperios'>
		<thead>
		<tr><td>Usuário</td><td>Nome do Império</td><td>População</td><td>Pontuação</td></tr>
		</thead>
		<tbody>";
		
		//TODO - Chamar função para popular os Impérios
		$html .= 
		"<tr id='imperio_1'><td><div>XPTO 1</div><div><a href='#' onclick='edita_imperio(1);'>Editar</a> | <a href='#'>Excluir</a></div></td><td>Império XPTO</td><td>999</td><td>999</td></tr>
		<tr id='imperio_2'><td><div>XPTO 2</div><div><a href='#' onclick='edita_imperio(2);'>Editar</a> | <a href='#'>Excluir</a></div></td><td>Império XPTO</td><td>999</td><td>999</td></tr>
		<tr id='imperio_3'><td><div>XPTO 3</div><div><a href='#' onclick='edita_imperio(3);'>Editar</a> | <a href='#'>Excluir</a></div></td><td>Império XPTO</td><td>999</td><td>999</td></tr>";

		$html .= "\n</tbody>
		</table></div>
		<div><a href='#' class='page-title-action colonization_admin_botao' onclick='novo_imperio();'>Adicionar novo Império</a></div>";
		
		echo $html;
	}

	/******************
	function colonization_admin_menu()
	-----------
	Exibe a opção de rodar turnos
	******************/
	function colonization_admin_roda_turno() {
		$html = "
		<style>
		.colonization_admin_botao {
		padding: 4px 8px; 
		text-decoration: none; 
		background: #f3f5f6; 
		border: 1px solid #0071a1; 
		position: relative; 
		top: 10px;
		}
		</style>
		<div><h2>COLONIZATION - RODA TURNO</h2></div>
		<br>
		<div><b>TURNO ATUAL - XX</b><br>
		DATA DO ÚLTIMO TURNO - 01/01/2020</div>
		<br>
		<div>
		<table class='wp-list-table widefat fixed striped users'>
		<thead>
		<tr><td>Nome do Império</td><td>Última modificação das ações</td><td>Pontuação</td></tr>
		</thead>
		<tbody>";
		
		//TODO - Chamar função para popular os Impérios
		$html .= 
		"<tr><td><div>Império XPTO</div><div><a href='#'>Editar ações</a></div></td><td>01/01/2020 12:00</td><td>999</td></tr>
		<tr><td><div>Império XPTO</div><div><a href='#'>Editar ações</a></div></td><td>01/01/2020 12:00</td><td>999</td></tr>
		<tr><td><div>Império XPTO</div><div><a href='#'>Editar ações</a></div></td><td>01/01/2020 12:00</td><td>999</td></tr>";

		$html .= "\n</tbody>
		</table></div>
		<br>
		<div><a href='#' class='page-title-action colonization_admin_botao'>Rodar Turno</a></div>";
		
		//TODO - Criar sistema de rodar o turno

		echo $html;
	}

	
}


//Cria o plugin
$plugin = new colonization();

//Ganchos de instalação e desinstalação do plugin "Colonization"
register_activation_hook( __FILE__, array($plugin,'colonization_install'));
register_deactivation_hook( __FILE__, array($plugin,'colonization_deactivate'));

//Cria o menu do plugin na área administrativa do WordPress
add_action('admin_menu', array($plugin, 'colonization_setup_menu'));

?>