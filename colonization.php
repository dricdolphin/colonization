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
include_once('includes/colonization_ajax.php');
include_once('includes/instalacao.php');
include_once('includes/instala_db.php');
include_once('includes/lista_usuarios.php');
include_once('includes/imperio.php');
include_once('includes/estrela.php');
include_once('includes/planeta.php');
include_once('includes/recurso.php');
include_once('includes/acoes.php');


//Classe "colonization"
//Classe principal do plugin
class colonization {
	public $html_header = "";
	
	/***********************
	function __construct()
	----------------------
	Inicializa o plugin
	***********************/
	function __construct() {
		//Adiciona os "shortcodes" que serão utilizados para exibir os dados do Império
		add_shortcode('colonization_exibe_imperio',array($this,'colonization_exibe_imperio')); //Exibe os dados do Império	
		$colonization_ajax = new colonization_ajax();
		
		//Inicializa os dados básicos, usados na maior parte das funções
		$this->html_header = "<link rel='stylesheet' type='text/css' href='../wp-content/plugins/colonization/colonization.css'>
		<script src='../wp-content/plugins/colonization/includes/edita_imperio.js?v=202002181953'></script>
		<script>";
		
		$lista_usuarios = new lista_usuarios();
		$this->html_header .= $lista_usuarios->html_lista_usuarios;
		
		$this->html_header .="</script>";

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
		add_submenu_page('colonization_admin_menu','Estrelas','Estrelas','manage_options','colonization_admin_estrelas',array($this,'colonization_admin_estrelas'));
		add_submenu_page('colonization_admin_menu','Roda Turno','Roda Turno','manage_options','colonization_admin_roda_turno',array($this,'colonization_admin_roda_turno'));
		
	}

	/******************
	function colonization_admin_menu()
	-----------
	Exibe a página principal do plugin, onde você pode criar e editar Impérios (dados básicos)
	******************/
	function colonization_admin_menu() {
		global $wpdb;
		
		$html = $this->html_header;
		
		$html .= "<div><h2>COLONIZATION - Impérios</h2></div>
		<div>
		<table class='wp-list-table widefat fixed striped users' id='tabela_imperios'>
		<thead>
		<tr><td>Usuário</td><td>Nome do Império</td><td>População</td><td>Pontuação</td></tr>
		</thead>
		<tbody>";
		
		//Pega a lista de impérios
		$lista_id_imperio = $wpdb->get_results("SELECT id_jogador FROM colonization_imperio");
		$html_lista_imperios = "";
		
		foreach ($lista_id_imperio as $id) {
			$user = get_user_by('ID',$id->id_jogador); //Pega todos os usuários
			$imperio = new imperio($id->id_jogador);
			$html_lista_imperios .= "<tr id='imperio_".$id->id_jogador."'><td><div>".$user->display_name."</div><div><a href='#' onclick='edita_imperio(".$id->id_jogador.");'>Editar</a> | <a href='#' onclick='excluir_imperio(".$id->id_jogador.");'>Excluir</a></div></td><td>".$imperio->imperio_nome."</td><td>999</td><td>999</td></tr>";
		}
		
		$html.= $html_lista_imperios;
		
		$html .= "\n</tbody>
		</table></div>
		<div><a href='#' class='page-title-action colonization_admin_botao' onclick='novo_imperio();'>Adicionar novo Império</a></div>";
		
		echo $html;
	}

	/******************
	function colonization_admin_estrelas()
	-----------
	Exibe a página de gestão de estrelas
	******************/
	function colonization_admin_estrelas() {
		global $wpdb;
		
		$html = $this->html_header;
		
		$html .= "<div><h2>COLONIZATION - Estrelas</h2></div>
		<div>
		<table class='wp-list-table widefat fixed striped users' id='tabela_estrelas'>
		<thead>
		<tr><td>Nome da estrela</td><td>Posição (X;Y;Z)</td><td>Tipo de estrela</td></tr>
		</thead>
		<tbody>";
		
		//Pega a lista de impérios
		$lista_id_estrelas = $wpdb->get_results("SELECT id FROM colonization_estrela ORDER BY X,Y,Z");
		$html_lista_estrelas = "";
		
		foreach ($lista_id_estrelas as $id) {
			$estrela = new estrela($id->id);
			$posicao_estrela = '('.$estrela->X.';'.$estrela->Y.';'.$estrela->Z.')';
			$html_lista_estrelas .= "<tr id='estrela_".$id->id."'><td><div>".$estrela->nome."</div><div><a href='#' onclick='edita_estrela(".$id->id.");'>Editar</a> | <a href='#' onclick='excluir_estrela(".$id->id.");'>Excluir</a></div></td><td>".$posicao_estrela."</td><td>".$estrela->tipo."</td></tr>";
		}
		
		$html.= $html_lista_estrelas;
		
		$html .= "\n</tbody>
		</table></div>
		<div><a href='#' class='page-title-action colonization_admin_botao' onclick='nova_estrela();'>Adicionar nova Estrela</a></div>";
		
		echo $html;
	}

	/******************
	function colonization_admin_roda_turno()
	-----------
	Exibe a opção de rodar turnos
	******************/
	function colonization_admin_roda_turno() {
		global $wpdb;
		
		$html = "<link rel='stylesheet' type='text/css' href='../wp-content/plugins/colonization/colonization.css'>
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
		
		//Pega a lista de impérios
		$lista_id_imperio = $wpdb->get_results("SELECT id_jogador FROM colonization_imperio");
		$html_lista_imperios = "";
		
		foreach ($lista_id_imperio as $id) {
			$imperio = new imperio($id->id_jogador);
			//TODO -- Pega a pontuação e a data da última ação
			$data_ultima_acao = "01/01/2020";
			$pontuacao = "999";
			$html_lista_imperios .= "<tr id='imperio_".$id->id_jogador."'><td><div>".$imperio->imperio_nome."</div><div><a href='#' onclick='edita_acoes(".$id->id_jogador.");'>Editar ações</a></div></td><td>{$data_ultima_acao}</td><td>{$pontuacao}</td></tr>";
		}

		$html .= $html_lista_imperios;
		
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