<?php
/**
 * Plugin Name: Colonization
 * Plugin URI: https://github.com/dricdolphin/colonization
 * Description: Plugin de WordPress com o sistema de jogo de Colonization.
 * Version: 1.2.2
 * Author: dricdolphin
 * Author URI: https://dricdolphin.com
 */

//Inclui os arquivos necessários para o sistema "Colonization"
//include_once(ABSPATH . 'wp-includes/pluggable.php'); //Arquivo do WordPress
include_once('includes/menu_admin.php');
include_once('includes/colonization_ajax.php');
include_once('includes/instalacao.php');
include_once('includes/recurso_instalacao.php');
include_once('includes/instala_db.php');
include_once('includes/listas.php');
include_once('includes/imperio.php');
include_once('includes/estrela.php');
include_once('includes/planeta.php');
include_once('includes/recurso.php');
include_once('includes/tech.php');
include_once('includes/colonia.php');
include_once('includes/colonia_instalacao.php');
include_once('includes/planeta_recurso.php');
include_once('includes/imperio_recursos.php');
include_once('includes/imperio_techs.php');
include_once('includes/imperio_instalacoes.php');
include_once('includes/techs_permitidas_imperio.php');
include_once('includes/transfere_tech.php');
include_once('includes/transfere_recurso.php');
include_once('includes/acoes.php');
include_once('includes/acoes_admin.php');
include_once('includes/turno.php');
include_once('includes/frota.php');
include_once('includes/roda_turno.php');
include_once('includes/reabastece_imperio.php');
include_once('includes/configuracao.php');
include_once('includes/missoes.php');
include_once('includes/estrelas_historico.php');

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
		add_shortcode('colonization_nome_imperio',array($this,'colonization_nome_imperio')); //Exibe o nome de um Império
		add_shortcode('colonization_exibe_colonias_imperio',array($this,'colonization_exibe_colonias_imperio')); //Exibe os dados do Império	
		add_shortcode('colonization_exibe_lista_imperios',array($this,'colonization_exibe_lista_imperios')); //Exibe a lista dos Impérios e suas pontuações
		add_shortcode('colonization_exibe_lista_imperios_pontuacao',array($this,'colonization_exibe_lista_imperios_pontuacao')); //Exibe a Pontuação dos Impérios
		add_shortcode('colonization_exibe_recursos_colonias_imperio',array($this,'colonization_exibe_recursos_colonias_imperio')); //Exibe os dados das Colônias do Império
		add_shortcode('colonization_exibe_acoes_imperio',array($this,'colonization_exibe_acoes_imperio')); //Exibe a lista de ações do Império
		add_shortcode('colonization_exibe_mapa_estelar',array($this,'colonization_exibe_mapa_estelar')); //Exibe o Mapa Estelar
		add_shortcode('colonization_exibe_missoes',array($this,'colonization_exibe_missoes')); //Exibe a Lista de Missões
		add_shortcode('colonization_exibe_frota_imperio',array($this,'colonization_exibe_frota_imperio')); //Exibe a Frota de um Império
		add_shortcode('colonization_exibe_techs_imperio',array($this,'colonization_exibe_techs_imperio')); //Exibe as Techs de um Império
		add_shortcode('colonization_exibe_reabastece_imperio',array($this,'colonization_exibe_reabastece_imperio')); //Exibe os pontos de Reabastecimento de um Império
		add_shortcode('colonization_exibe_autoriza_reabastece_imperio',array($this,'colonization_exibe_autoriza_reabastece_imperio')); //Exibe a tela de autorização de reabastecimento
		add_shortcode('colonization_exibe_constroi_naves',array($this,'colonization_exibe_constroi_naves')); //Exibe uma página de construção de naves
		add_shortcode('colonization_exibe_distancia_estrelas',array($this,'colonization_exibe_distancia_estrelas')); //Exibe uma página com a distância entre duas estrelas
		add_shortcode('colonization_exibe_hyperdrive',array($this,'colonization_exibe_hyperdrive')); //Exibe uma página com a distância entre duas estrelas via Hyperdrive
		add_shortcode('colonization_exibe_techtree',array($this,'colonization_exibe_techtree')); //Exibe a Tech Tree do Colonization
		add_shortcode('colonization_exibe_tech_transfere',array($this,'colonization_exibe_tech_transfere')); //Exibe a transferência de Techs e o histórico
		add_shortcode('colonization_exibe_recurso_transfere',array($this,'colonization_exibe_recurso_transfere')); //Exibe a transferência de Recursos e o histórico
		add_shortcode('colonization_exibe_mapa_naves',array($this,'colonization_exibe_mapa_naves')); //Exibe o mapa com a posição das naves
		add_shortcode('colonization_exibe_dados_estrelas',array($this,'colonization_exibe_dados_estrelas')); //Exibe os dados de uma estrela ou de todas as estrelas que um Jogador já visitou
		add_shortcode('colonization_exibe_diplomacia',array($this,'colonization_exibe_diplomacia')); //Exibe as condições diplomáticas do Império
		add_shortcode('colonization_gerencia_tech',array($this,'colonization_gerencia_tech')); //Exibe a lista de Techs do Império, e permite adicionar 
		add_shortcode('turno_atual',array($this,'colonization_turno_atual')); //Exibe o texto com o Turno Atual
		
		
		add_action('plugins_loaded', array($this,'carrega_actions') );
		//date_default_timezone_set('America/Sao_Paulo');
	}
	
	function carrega_actions() {
		include_once('js/listas_js.php');
		add_action('wp_head', array($this,'colonization_ajaxurl')); //Necessário para incluir o ajaxurl
		add_action('wp_body_open', array($this,'colonization_exibe_barra_recursos')); //Adiciona a barra de recursos de cada Império
		add_action('asgarosforum_after_post_author', array($this,'colonization_exibe_prestigio'), 10, 2);
		add_action('asgarosforum_wp_head', array($this,'colonization_exibe_tech_transfere_pendente')); //Adiciona as mensagens de transferência de Tech pendentes
		add_action('asgarosforum_wp_head', array($this,'colonization_exibe_viagem_frota')); //Adiciona as mensagens de viagens pendentes de Naves dos Impérios
		add_action('asgarosforum_wp_head', array($this,'colonization_exibe_missoes_pendente')); //Adiciona as mensagens de missões pendentes
		add_action('asgarosforum_custom_header_menu', array($this,'colonization_menu_asgaros'));
		add_action('asgarosforum_custom_topic_column', array($this,'colonization_muda_icone_topico'), 10, 2);
		add_action('asgarosforum_content_top', array($this,'colonization_mapa_naves_header'), 10, 2);
		
		add_filter('asgarosforum_filter_get_threads', array($this,'colonization_filtra_topicos_compartilhados'), 10, 2);
		add_filter('asgarosforum_filter_check_access', array($this,'colonization_check_access_compartilhado'), 10, 2);

		$colonization_ajax = new colonization_ajax();
	}
	
	
	function colonization_mapa_naves_header() {
		global $asgarosforum, $wpdb;

		$user = wp_get_current_user();
		$roles = "";
		$banido = false;
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
			$banido = get_user_meta($user->ID, 'asgarosforum_role', true);			
		}
		
		$html = "<div class='spoiler'>
			<div class='spoiler-head closed'><span style='font-weight: bold;'><i class='fas fa-radar'></i>RADAR DAS NAVES</span></div>
			<div class='spoiler-body'>";
		
		$exibe_mapa = false;
		if ($roles == "administrator") {
			$atts['mini_mapa'] = true;
			$html .= $this->colonization_exibe_mapa_naves($atts);
			$exibe_mapa = true;
		} elseif ($roles != "" && $banido != "banned") {
			$imperio = new imperio();

			if ($imperio->id != 0) {
				$atts['mini_mapa'] = true;
				$atts['id'] = $imperio->id;

				$html .= $this->colonization_exibe_mapa_naves($atts);
				$exibe_mapa = true;
			}
		}
		
		$html .= "</div></div><br>";
		if ($exibe_mapa) {
			echo $html;
		}
	}
	
	
	/******************
	function colonization_check_access_compartilhado()
	-----------
	Verifica se o usuário tem autorização para acessar um Tópico específico
	
	$arg1 -- sempre TRUE
	$categoria -- id da categoria sendo verificada
	******************/	
	function colonization_check_access_compartilhado($arg1, $categoria) {
		global $asgarosforum, $wpdb;

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}		
		
		$id_categoria_comunicacoes_globais = $wpdb->get_var("SELECT id FROM colonization_referencia_forum WHERE descricao='ID da Categoria de Comunicações Globais'");
		$id_categoria_comunicacoes_globais = new configuracao($id_categoria_comunicacoes_globais);
		
		if ($asgarosforum->current_view == "topic" && $categoria == $id_categoria_comunicacoes_globais->id_post && $roles != "administrator") {
			//Se estivermos vendo um Tópico na área de Comunicações Globais, só libera a exibição caso o usuário tenha seu nome ou nome do Império no título do post
			$imperio = new imperio();
			if ($imperio->id == 0) {
				$imperio->nome = $user->display_name;
			}
			
			$nome_topico = $asgarosforum->content->get_topic_title($asgarosforum->current_element);
			
			if (!str_contains($nome_topico,$imperio->nome)) {
				return false;
			}
		}
		
		return true;
	}
	
	/******************
	function colonization_filtra_topicos_compartilhados()
	-----------
	Filtra os tópicos a serem exibidos na Categoria de Comunicações Globais. Exibe somente Tópicos cujo título incluírem o nome do Jogador ou de seu Império
	
	$results -- lista de tópicos
	******************/	
	function colonization_filtra_topicos_compartilhados($results) {
		global $asgarosforum, $wpdb, $contador;
	
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}

		$id_categoria_comunicacoes_globais = $wpdb->get_var("SELECT id FROM colonization_referencia_forum WHERE descricao='ID da Categoria de Comunicações Globais'");
		$id_categoria_comunicacoes_globais = new configuracao($id_categoria_comunicacoes_globais);
		$id_forum_categoria_comunicacoes_globais = $wpdb->get_var("SELECT MAX(id) FROM wp_forum_forums WHERE parent_id={$id_categoria_comunicacoes_globais->id_post}");
		$nome_categoria = $wpdb->get_var("SELECT name FROM wp_terms WHERE term_id = {$asgarosforum->current_category}");
		
		$sticky = true;
		if (!empty($results)) {
			if ($results[0]->sticky == 0) {
				$sticky = false;
			}
		} else {
			return $results;
		}

		$imperio = new imperio();
		if ($imperio->id == 0) {
			$imperio->nome = $user->display_name;
		}

		// Build query-part for pagination.
		$limit_end = $asgarosforum->options['topics_per_page'];
		$limit_start = $asgarosforum->current_page * $limit_end;
		
		$results_temp = [];
		if (($asgarosforum->current_category == $id_categoria_comunicacoes_globais->id_post && $roles != "administrator")) {
			if (!$sticky) {
				// Build query-part for ordering.
				$order = "(SELECT MAX(id) FROM {$asgarosforum->tables->posts} WHERE parent_id = t.id) DESC";
				$order = apply_filters('asgarosforum_filter_get_threads_order', $order);

				// Build additional sub-queries.
				$query_answers = "SELECT (COUNT(*) - 1) FROM {$asgarosforum->tables->posts} WHERE parent_id = t.id";

				// Build final query and get results.
				$query = "SELECT t.id, t.name, t.views, t.sticky, t.closed, t.author_id, ({$query_answers}) AS answers 
				FROM {$asgarosforum->tables->topics} AS t 
				WHERE t.parent_id = %d AND t.sticky = 0 AND t.approved = 1 
				ORDER BY {$order};";
				$results = $asgarosforum->db->get_results($asgarosforum->db->prepare($query, $asgarosforum->current_forum));
			}

			foreach ($results as $chave => $resultado) {
				if ($resultado->sticky == 2) {//Sticky global é exibido normalmente
					return $results;
				} elseif ($resultado->sticky == 1) {
					//Exibe só os que tem o nome do Império
					if (str_contains($resultado->name,$imperio->nome)) {
						$results_temp[] = $resultado;	
					}
				} else {
					if (str_contains($resultado->name,$imperio->nome)) {
						$results_temp[] = $resultado;
					}
				}
			}

			//echo "current_forum: {$asgarosforum->current_forum}<br>";
			//echo "results:<br>";
			//var_dump($results);
		}  elseif ((str_contains($nome_categoria, $user->display_name) && !$sticky) || ($roles == "administrator" && !$sticky)) {
			$contem_display_name = false;
			if ($roles == "administrator") {
				$ids_jogadores = $wpdb->get_results("SELECT id, id_jogador FROM colonization_imperio");
				foreach ($ids_jogadores as $ids) {
					$user = get_user_by("ID",$ids->id_jogador);
					//echo "display_name: {$user->display_name}<br>";
					if (str_contains($nome_categoria, $user->display_name)) {
						$contem_display_name = true;
						$imperio = new imperio($ids->id);
						break;
					}
				}
				
				if (!$contem_display_name) {
					return $results;
				}
			}
			
			$ids_originais = [];
			foreach ($results as $chave => $resultado) {
				$ids_originais[] = $resultado->id;
			}
			$topic_ids = implode(",", $ids_originais);
			// Build query-part for ordering.
			$order = "(SELECT MAX(id) FROM {$asgarosforum->tables->posts} WHERE parent_id = t.id) DESC";
			$order = apply_filters('asgarosforum_filter_get_threads_order', $order);

			// Build additional sub-queries.
			$query_answers = "SELECT (COUNT(*) - 1) FROM {$asgarosforum->tables->posts} WHERE parent_id = t.id";

			// Build final query and get results.
			$query = "SELECT t.id, t.name, t.views, t.sticky, t.closed, t.author_id, ({$query_answers}) AS answers 
			FROM {$asgarosforum->tables->topics} AS t 
			WHERE (t.parent_id = {$id_forum_categoria_comunicacoes_globais} AND t.sticky = 0 AND t.approved = 1 AND (t.name LIKE '%{$imperio->nome}%' OR t.name LIKE '%{$user->display_name}%')) OR t.id IN ({$topic_ids})
			ORDER BY {$order};";
			
			$results_temp = $wpdb->get_results($query);
		} else {
			$results_temp = $results;
		}

		if (!$sticky) {
			$results_temp_temp = [];
			//echo "limit_start: {$limit_start}<br>";
			for ($i=$limit_start; $i < ($limit_start+$limit_end); $i++) {
				if (empty($results_temp[$i])) {
					break;
				}
				$results_temp_temp[] = $results_temp[$i];
			}
			$results_temp = $results_temp_temp;
		}

		$results = $results_temp;
		
		return $results;
	}
	
	function colonization_muda_icone_topico($topic_id) {
		global $asgarosforum, $wpdb;
		$page_id_forum = new configuracao(1);
		$id_missao = new configuracao(2);
		
		$id_lista_estrelas = $wpdb->get_var("SELECT id FROM colonization_referencia_forum WHERE descricao='ID do Tópico da Lista de Estrelas'");
		$id_lista_estrelas = new configuracao($id_lista_estrelas);
		
		$id_calculadora_distancias = $wpdb->get_var("SELECT id FROM colonization_referencia_forum WHERE descricao='ID do Tópico da Calculadora de Distâncias'");
		$id_calculadora_distancias = new configuracao($id_calculadora_distancias);

		//$id_radar = $wpdb->get_var("SELECT id FROM colonization_referencia_forum WHERE descricao='ID do Tópico de Radar de Naves'");
		//$id_radar = new configuracao($id_radar);		
		
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		//if ($roles == "administrator") {
			if ($topic_id == $id_missao->id_post) {
				echo "<div class='icone_topico'><i class='fas fa-trophy' style='font-size: 32px; color: #a2a2a2;'></i></div>";
			}
			
			if ($topic_id == $id_lista_estrelas->id_post) {
				echo "<div class='icone_topico'><i class='far fa-stars' style='font-size: 32px; color: #a2a2a2;'></i></div>";
			}
			
			if ($topic_id == $id_calculadora_distancias->id_post) {
				echo "<div class='icone_topico'><i class='fas fa-search-location' style='font-size: 32px; color: #a2a2a2;'></i></div>";
			}
		
			if ($asgarosforum->content->get_topic_title($topic_id) == "Radar de Naves") {
				echo "<div class='icone_topico'><i class='fad fa-radar' style='font-size: 32px; color: #a2a2a2;'></i></div>";
			}

			if ($asgarosforum->content->get_topic_title($topic_id) == "Diplomacia e Pontos de Reabastecimento") {
				echo "<div class='icone_topico'><i class='far fa-handshake' style='font-size: 32px; color: #a2a2a2;'></i></div>";
			}

			if ($asgarosforum->content->get_topic_title($topic_id) == "Dados do Império") {
				echo "<div class='icone_topico'><i class='fad fa-analytics' style='font-size: 32px; color: #a2a2a2;'></i></div>";
			}
			
			if ($asgarosforum->content->get_topic_title($topic_id) == "Transferência de Techs") {
				echo "<div class='icone_topico'><i class='fas fa-user-chart' style='font-size: 32px; color: #a2a2a2;'></i></div>";
			}

			if ($asgarosforum->content->get_topic_title($topic_id) == "Mapa de Recursos") {
				echo "<div class='icone_topico'><i class='fas fa-clipboard-list-check' style='font-size: 32px; color: #a2a2a2;'></i></div>";
			}		

			if ($asgarosforum->content->get_topic_title($topic_id) == "Gerenciamento das Techs") {
				echo "<div class='icone_topico'><i class='fas fa-flask' style='font-size: 32px; color: #a2a2a2;'></i></div>";
			}

			if ($asgarosforum->content->get_topic_title($topic_id) == "Simulador de Naves") {
				echo "<div class='icone_topico'><i class='fal fa-space-shuttle' style='font-size: 32px; color: #a2a2a2;'></i></div>";
			}

			if ($asgarosforum->content->get_topic_title($topic_id) == "Gerenciamento da Frota") {
				echo "<div class='icone_topico'><i class='fal fa-rocket' style='font-size: 32px; color: #a2a2a2;'></i></div>";
			}
			
			//Gerenciamento das Techs			
		//}
	}
	
	function colonization_menu_asgaros() {
		global $asgarosforum, $wpdb;
		$page_id_forum = new configuracao(1);
		$id_missao = new configuracao(2);
		
		$id_lista_estrelas = $wpdb->get_var("SELECT id FROM colonization_referencia_forum WHERE descricao='ID do Tópico da Lista de Estrelas'");
		$id_lista_estrelas = new configuracao($id_lista_estrelas);		

		echo "<a href='?page_id={$page_id_forum->id_post}&view=topic&id={$id_missao->id_post}'>Missões</a>";
		//echo "<a href='?page_id={$page_id_forum->id_post}&view=topic&id={$id_lista_estrelas->id_post}'>Estrelas Visitadas</a>";
	}

	function colonization_ajaxurl() {

		echo "<script type='text/javascript'>
           var ajaxurl = '" . admin_url('admin-ajax.php') . "';
         </script>";
	}

	/******************
	function colonization_gerencia_tech()
	-----------
	Mostra a lista com as missões do Império (atuais ou não)
	******************/	
	function colonization_gerencia_tech($atts = [], $content = null) {
		global $asgarosforum, $wpdb;

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
			$banido = get_user_meta($user->ID, 'asgarosforum_role', true);
			if ($banido === "banned") {
				return;
			} 
		}
		$turno = new turno();

		if (isset($atts['id'])) {
			$imperio = new imperio($atts['id'],false);
		} else {
			$imperio = new imperio();
		}		
			
		$html_techs_imperio = "";
		$tech = new tech();
		$resultados = $tech->query_tech("",$imperio->id);
		foreach ($resultados as $resultado) {
			$id_imperio_techs = $wpdb->get_var("SELECT id FROM colonization_imperio_techs WHERE id_imperio={$imperio->id} AND id_tech={$resultado->id}");
			if (!empty($id_imperio_techs)) {
				$imperio_techs = new imperio_techs($id_imperio_techs);
				$tech = new tech($imperio_techs->id_tech);
				if ($imperio_techs->custo_pago == 0) {
					$imperio_techs->custo_pago = $tech->custo;
				}
				$html_techs_imperio .= "<tr><td>{$tech->nome}</td><td>{$imperio_techs->custo_pago}</td><td>{$imperio_techs->turno}</td></tr>";
			}
		}
			
		$html = "<div><h4>Techs do Império '{$imperio->nome}'</h4></div>
		<div>
		<table class='lista_techs_imperio' data-tabela='colonization_imperio_techs' style='width: 700px;'>
		<thead>
		<tr><th style='width: 500px;'>Tech</th><th style='width: 150px;'>Custo</th><th style='width: 150px;'>Turno</th></tr>
		</thead>
		<tbody>";

		$html .= $html_techs_imperio;

		$html .= "\n</tbody>
		</table></div>";
		
		if (!$banido && !$turno->encerrado && $turno->bloqueado) {
			$html .= "\n<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return nova_tech_jogador(event, {$imperio->id});'>Adicionar nova Tech</a></div>";
		}
		
		return $html;
	}

	/******************
	function colonization_exibe_missoes()
	-----------
	Mostra a lista com as missões do Império (atuais ou não)
	******************/	
	function colonization_exibe_missoes($atts = [], $content = null) {
		global $asgarosforum, $wpdb;

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
			$banido = get_user_meta($user->ID, 'asgarosforum_role', true);
			if ($banido === "banned") {
				return;
			} 
		}

		if (isset($atts['id'])) {
			$imperio = new imperio($atts['id']);
		} else {
			$imperio = new imperio();
		}		
		
		$missao_ativa = 1;
		$html_lista = "<h3>Lista das Missões do Império</h3>";
		if ($imperio->id == 0 && $roles == "administrator") {
			$lista_id = $wpdb->get_results("
			SELECT id 
			FROM colonization_missao 
			ORDER BY ativo DESC, id_imperio, turno");

			
			foreach ($lista_id as $id) {
				$missao = new missoes($id->id);
				
				if ($missao_ativa == 1 && $missao->ativo == 0) {
					$html_lista .= "<div><b>=== Missões Encerradas ===</b></div>";
					$missao_ativa = 0;
				}
				
				$html_dados = $missao->exibe_missao();
				
				$html_lista .= "
				{$html_dados}<br>";
			}
		} elseif ($imperio->id != 0) {
			$lista_id = $wpdb->get_results("
			SELECT id 
			FROM colonization_missao 
			WHERE id_imperio={$imperio->id} 
			OR id_imperio=0
			ORDER BY ativo DESC, id_imperio, turno");

			foreach ($lista_id as $id) {
				$missao = new missoes($id->id);

				if ($missao_ativa == 1 && $missao->ativo == 0) {
					$html_lista .= "<div><b>=== Missões Encerradas ===</b></div>";
					$missao_ativa = 0;
				}
				
				$html_dados = $missao->exibe_missao($imperio->id);
				
				$html_lista .= "
				{$html_dados}<br>";
			}
		}
		
		return $html_lista;
	}

	/******************
	function colonization_turno_atual()
	-----------
	Mostra uma mensagem com o Turno atual
	******************/	
	function colonization_turno_atual() {
		$turno = new turno(); //Pega o turno atual

		if (empty($turno->turno)) {
			return "O jogo ainda não começou! Aguarde!";
		}
		
		$html = "O Turno {$turno->turno} já começou!";
		if (!$turno->encerrado && $turno->bloqueado) {
			$proximo_turno = $turno->turno + 1;
			$timezone = new DateTimeZone('America/Sao_Paulo');
			$proxima_semana = new DateTime($turno->data_turno, $timezone);
			$proxima_semana->modify('+7 days');
			
			$date = new DateTime($turno->data_turno);
			//echo $date->format('Y-m-d H:i:s');
			$proxima_semana_string = $proxima_semana->format('d/m/Y');
			$html .= "\n<br>O Turno {$proximo_turno} irá começar dia {$proxima_semana_string}!<br><br>Fique atento!";
		}
		
		return $html;
	}

	/******************
	function colonization_exibe_dados_estrelas()
	-----------
	Exibe os dados de uma estrela ou de todas as estrelas conhecidas por um Império
	******************/		
	function colonization_exibe_dados_estrelas($atts = [], $content = null) {
		global $asgarosforum, $wpdb;

		$turno = new turno();
		
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}

		if (isset($atts['id'])) {
			$imperio = new imperio($atts['id'],false);
		} else {
			$imperio = new imperio();
		}		
		
		if ($imperio->id == 0 && $roles == "administrator") {
			$id_estrelas = $wpdb->get_results("SELECT id AS id_estrela FROM colonization_estrela");
		} else {
			$id_estrelas = $wpdb->get_results("SELECT id_estrela FROM colonization_estrelas_historico WHERE id_imperio={$imperio->id}");
		}
		
		$lista_id_estrela = [];
		foreach ($id_estrelas as $id) {
			$lista_id_estrela[$id->id_estrela] = $id->id_estrela;
		}
		
		$lista_id_estrela = implode(",",$lista_id_estrela);
		
		$id_estrelas = [];
		if (!empty($lista_id_estrela)) {
			$id_estrelas = $wpdb->get_results("SELECT id AS id_estrela FROM colonization_estrela WHERE id IN ({$lista_id_estrela}) ORDER BY nome");
		}
		
		$html = "";
		foreach ($id_estrelas as $id) {
			$estrela = new estrela($id->id_estrela);
			
			$pesquisa_anterior = "";
			if (!empty($estrela->id)) {
				$pesquisa_anterior = $wpdb->get_var("SELECT id FROM colonization_imperio_historico_pesquisa WHERE id_imperio={$imperio->id} AND id_estrela={$estrela->id} AND sensores >= {$imperio->sensores}");
			}
			
			$html_pesquisa_nave = "";
			if (empty($pesquisa_anterior)) {
				$html_pesquisa_nave = "<div class='fas fa-search tooltip'><span class='tooltiptext'>Sistema ainda pode ser pesquisado</span></div>";	
			}

			if ($imperio->id == 0 && $roles == "administrator") {
				$turno_visita = $turno->turno;
				$html_pesquisa_nave = "";
			} else {
				$turno_visita = $wpdb->get_var("SELECT turno FROM colonization_estrelas_historico WHERE id_imperio={$imperio->id} AND id_estrela={$id->id_estrela}");
			}

			$ids_imperios = $wpdb->get_results("
			SELECT DISTINCT cic.id_imperio, cic.nome_npc
			FROM colonization_imperio_colonias AS cic
			JOIN colonization_planeta AS cp
			ON cp.id = cic.id_planeta
			JOIN colonization_estrela AS ce
			ON ce.id = cp.id_estrela
			WHERE cp.id_estrela = {$estrela->id}
			AND (cic.turno = {$turno_visita} OR (cic.id_imperio={$imperio->id} AND cic.turno={$turno->turno}))
			");
			
			$nomes_imperios = "";
			foreach ($ids_imperios as $id_imperio) {
				if ($id_imperio->id_imperio == 0) {
					$nomes_imperios .= "{$id_imperio->nome_npc}; ";
				} else {
					$imperio_estrela = new imperio($id_imperio->id_imperio,true);
					$nomes_imperios .= "{$imperio_estrela->nome}; ";
				}
			}
			if (!empty($nomes_imperios)) {
				$nomes_imperios = substr($nomes_imperios,0,-2);
				$nomes_imperios = "Colonizado por <span style='text-decoration: underline;'>{$nomes_imperios}</span>";
			}
			
			//descricao_html = str_ireplace("{$estrela->nome} ({$estrela->X};{$estrela->Y};{$estrela->Z})","",$estrela->descricao);
			$descricao_html = $estrela->descricao;
			$planetas_html = $estrela->pega_html_planetas_estrela(true, false, $turno_visita);
			$html .= "<div class='par_impar' style='margin-bottom: 5px;'>
			<div class='nome_estrela'>{$html_pesquisa_nave}<b>{$estrela->nome} ({$estrela->X};{$estrela->Y};{$estrela->Z})</b> {$nomes_imperios}</div>
			<div class='descricao_estrela'>{$descricao_html}</div>
			<div class='lista_planetas'>{$planetas_html}</div>
			</div>";
		}
		
		return $html;
	}


	/******************
	function colonization_exibe_mapa_naves()
	-----------
	Exibe a posição de todas as naves do Império e também nas Colônias
	******************/		
	function colonization_exibe_mapa_naves($atts = [], $content = null) {
		global $asgarosforum, $wpdb;
		$start_time = hrtime(true);
		
		$turno = new turno();
		
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}

		if (isset($atts['id'])) {
			$imperio = new imperio($atts['id'],false);
		} else {
			$imperio = new imperio();
		}		

		$apenas_recursos = false;
		$somente_pesquisa = "";
		if (isset($atts['apenas_recursos'])) {
			$apenas_recursos = true;
		}

		$mini_mapa = false;
		if (isset($atts['mini_mapa'])) {
			$mini_mapa = true;
		}

		$query_estrela = "";
		if (isset($atts['id_estrela'])) {//Caso seja para mostrar os dados de uma estrela específica
			$query_estrela = " AND ce.id={$atts['id_estrela']}";
		}		
		
		if ($imperio->id == 0 && $roles == "administrator") {
			$ids_naves = $wpdb->get_results("
			SELECT cif.id, {$turno->turno} AS turno
			FROM colonization_imperio_frota AS cif 
			LEFT JOIN colonization_estrela AS ce
			ON ce.X = cif.X AND ce.Y=cif.Y AND ce.Z=cif.Z
			WHERE cif.turno_destruido=0 AND (cif.id_estrela_destino = 0 OR cif.id_estrela_destino IS NULL)
			{$somente_pesquisa}{$query_estrela}
			");		
			
			$ids_estrelas = $wpdb->get_results("
			SELECT DISTINCT ce.id, {$turno->turno} AS turno
			FROM colonization_imperio_colonias AS cic
			JOIN colonization_planeta AS cp
			ON cp.id = cic.id_planeta
			JOIN colonization_estrela AS ce
			ON ce.id = cp.id_estrela
			WHERE cic.turno = {$turno->turno}{$query_estrela}
			ORDER BY ISNULL(cic.id_imperio), cic.id_imperio, cic.nome_npc, cic.capital DESC, ce.nome, ce.X, ce.Y, ce.Z
			");
		} else {
			if ($apenas_recursos) {//Somente naves de pesquisa conseguem ver os recursos dos planetas onde estão
				//$somente_pesquisa = " AND pesquisa=1";
				
				$ids_estrelas = $wpdb->get_results("
				SELECT DISTINCT ce.id,(CASE WHEN cic.id_imperio = {$imperio->id} THEN {$turno->turno} ELSE MAX(cihp.turno) END) AS turno
				FROM colonization_imperio_historico_pesquisa AS cihp
				JOIN colonization_estrela AS ce
				ON ce.id = cihp.id_estrela
				LEFT JOIN colonization_planeta AS cp
				ON cp.id_estrela = ce.id
				LEFT JOIN colonization_imperio_colonias AS cic
				ON cic.id_planeta = cp.id
				WHERE cihp.id_imperio = {$imperio->id}{$query_estrela}
				GROUP BY ce.id, cihp.turno
				ORDER BY ISNULL(cic.id_imperio), cic.id_imperio, cic.nome_npc, cic.capital DESC, ce.nome, ce.X, ce.Y, ce.Z
				");									
			} else {
				$ids_naves = $wpdb->get_results("
				SELECT cif.id , {$turno->turno} as turno
				FROM colonization_imperio_frota AS cif 
				LEFT JOIN colonization_estrela AS ce
				ON ce.X = cif.X AND ce.Y=cif.Y AND ce.Z=cif.Z
				WHERE cif.id_imperio = {$imperio->id} AND (cif.id_estrela_destino = 0 OR cif.id_estrela_destino IS NULL)
				AND cif.turno_destruido=0{$somente_pesquisa}{$query_estrela}
				");

				$ids_estrelas = $wpdb->get_results("
				SELECT DISTINCT ce.id, {$turno->turno} as turno
				FROM colonization_imperio_colonias AS cic
				JOIN colonization_planeta AS cp
				ON cp.id = cic.id_planeta
				JOIN colonization_estrela AS ce
				ON ce.id = cp.id_estrela
				WHERE cic.id_imperio = {$imperio->id}{$query_estrela}
				AND cic.turno = {$turno->turno} AND cic.vassalo = 0
				ORDER BY ISNULL(cic.id_imperio), cic.id_imperio, cic.nome_npc, cic.capital DESC, ce.nome, ce.X, ce.Y, ce.Z
				");						
			}
		}
		
		if (isset($atts['apenas_naves'])) {
			$ids_estrelas = [];
		}

		$html_estrela = [];
		$html_naves = [];
		$html_estrela_mini = [];
		$html_naves_mini = [];		
		$html_planetas_na_estrela = [];
		$exibiu_nave = [];
		$estrela = [];
		$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
		if ($roles == "administrator") {
			//echo "Loop das estrelas: {$diferenca}ms<br>";
		}
		foreach ($ids_estrelas as $id_estrela) {
			if (empty($estrela[$id_estrela->id])) {
				$estrela[$id_estrela->id] = new estrela($id_estrela->id);
			}
			
			$ids_imperios = $wpdb->get_results("
			SELECT DISTINCT cic.id_imperio, cic.nome_npc, {$turno->turno} as turno, ci.nome AS nome_imperio
			FROM colonization_imperio_colonias AS cic
			JOIN colonization_planeta AS cp
			ON cp.id = cic.id_planeta
			JOIN colonization_estrela AS ce
			ON ce.id = cp.id_estrela
			LEFT JOIN colonization_imperio AS ci
			ON ci.id = cic.id_imperio
			WHERE cp.id_estrela = {$estrela[$id_estrela->id]->id}
			AND cic.turno = {$turno->turno}
			");
			
			$nomes_imperios = "";
			foreach ($ids_imperios as $id_imperio) {
				if ($id_imperio->id_imperio == 0) {
					$nomes_imperios .= "{$id_imperio->nome_npc}; ";
				} else {
					$nomes_imperios .= "{$id_imperio->nome_imperio}; ";
				}
			}
			if (!empty($nomes_imperios)) {
				$nomes_imperios = substr($nomes_imperios,0,-2);
				$nomes_imperios = "Colonizado por <span style='text-decoration: underline;'>{$nomes_imperios}</span>";
			}
			
			if (empty($html_estrela[$estrela[$id_estrela->id]->id])) {
				$turno_visitado = "";
				if ($turno->turno != $id_estrela->turno) {
					$turno_visitado = " Vistado no Turno {$id_estrela->turno}";
				}
				$html_estrela[$estrela[$id_estrela->id]->id] = "<div class='nome_estrela'><b>{$estrela[$id_estrela->id]->nome}</b> ({$estrela[$id_estrela->id]->X};{$estrela[$id_estrela->id]->Y};{$estrela[$id_estrela->id]->Z}) {$nomes_imperios}{$turno_visitado}</div>";
				$html_estrela_mini[$estrela[$id_estrela->id]->id] = "<div class='nome_estrela_mini'><b>{$estrela[$id_estrela->id]->nome}</b> ({$estrela[$id_estrela->id]->X};{$estrela[$id_estrela->id]->Y};{$estrela[$id_estrela->id]->Z})<br>{$nomes_imperios}</div>";
				$html_naves[$estrela[$id_estrela->id]->id] = "<div class='naves_no_local'>";
				$html_naves_mini[$estrela[$id_estrela->id]->id] = "<div class='naves_no_local'>";
				$html_planetas_na_estrela[$estrela[$id_estrela->id]->id] = $estrela[$id_estrela->id]->pega_html_planetas_estrela($apenas_recursos, $apenas_recursos, $id_estrela->turno);
			}
			
			$imperio_nave = [];
			$ids_naves_na_estrela = $wpdb->get_results("SELECT id FROM colonization_imperio_frota WHERE X={$estrela[$id_estrela->id]->X} AND Y={$estrela[$id_estrela->id]->Y} AND Z={$estrela[$id_estrela->id]->Z} AND turno_destruido=0 AND (id_estrela_destino = 0 OR id_estrela_destino IS NULL)");
			foreach ($ids_naves_na_estrela AS $id_frota) {
				$nave = new frota($id_frota->id);
				if (empty($imperio_nave[$nave->id_imperio])) {
					$imperio_nave[$nave->id_imperio] = new imperio($nave->id_imperio,true);
				}
				if ($imperio_nave[$nave->id_imperio]->id == 0) {
					$imperio_nave[$nave->id_imperio]->nome = $nave->nome_npc;
				}
				
				if ($roles != "administrator") {
					if ((($imperio->sensores + $imperio->anti_camuflagem) > $nave->camuflagem) || $nave->visivel == 1 || $nave->camuflagem == 0 || $nave->id_imperio == $imperio->id) {
						$html_naves[$estrela[$id_estrela->id]->id] .= "<div class='naves'>{$nave->qtd} {$nave->tipo} '{$nave->nome}' ({$imperio_nave[$nave->id_imperio]->nome})</div>";
						$html_naves_mini[$estrela[$id_estrela->id]->id] .= "<div class='naves_mini'>{$nave->qtd} {$nave->tipo} '{$nave->nome}' ({$imperio_nave[$nave->id_imperio]->nome})</div>";
						$exibiu_nave[$nave->id] = true;
					}
				} else {
					if ($nave->camuflagem > 0 && $nave->visivel == 0) {
						$nave_visivel = "";
						if ($imperio->id != 0 && (($imperio->sensores + $imperio->anti_camuflagem) > $nave->camuflagem)) {
							$nave_visivel = "(Visível) ";	
						}
						$html_naves[$estrela[$id_estrela->id]->id] .= "<div class='naves'>{$nave_visivel}<i>{$nave->qtd} {$nave->tipo} '{$nave->nome}' ({$imperio_nave[$nave->id_imperio]->nome})</i></div>";
						$html_naves_mini[$estrela[$id_estrela->id]->id] .= "<div class='naves_mini'><i>{$nave->qtd} {$nave->tipo} '{$nave->nome}' ({$imperio_nave[$nave->id_imperio]->nome})</i></div>";
						$exibiu_nave[$nave->id] = true;
					} else {
						$html_naves[$estrela[$id_estrela->id]->id] .= "<div class='naves'>{$nave->qtd} {$nave->tipo} '{$nave->nome}' ({$imperio_nave[$nave->id_imperio]->nome})</div>";
						$html_naves_mini[$estrela[$id_estrela->id]->id] .= "<div class='naves_mini'>{$nave->qtd} {$nave->tipo} '{$nave->nome}' ({$imperio_nave[$nave->id_imperio]->nome})</div>";
						$exibiu_nave[$nave->id] = true;
					}
				}
			}
		}
		$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
		if ($roles == "administrator") {
			//echo "Término loop das estrelas: {$diferenca}ms<br>";
		}
		
		foreach ($ids_naves as $id_nave) {
			$nave = new frota($id_nave->id);
			if (empty($imperio_nave[$nave->id_imperio])) {
				$imperio_nave[$nave->id_imperio] = new imperio($nave->id_imperio,true);
			}
			if ($imperio_nave[$nave->id_imperio]->id == 0) {
				$imperio_nave[$nave->id_imperio]->nome = $nave->nome_npc;
			}
			
			$id_estrela = $wpdb->get_var("SELECT id FROM colonization_estrela WHERE X={$nave->X} AND Y={$nave->Y} AND Z={$nave->Z}");
			if (empty($estrela[$id_estrela])) {
				$estrela[$id_estrela] = new estrela($id_estrela);
			}
			
			$ids_imperios = $wpdb->get_results("
			SELECT DISTINCT cic.id_imperio, cic.nome_npc, ci.nome AS nome_imperio
			FROM colonization_imperio_colonias AS cic
			JOIN colonization_planeta AS cp
			ON cp.id = cic.id_planeta
			JOIN colonization_estrela AS ce
			ON ce.id = cp.id_estrela
			LEFT JOIN colonization_imperio AS ci
			ON ci.id = cic.id_imperio			
			WHERE cp.id_estrela = {$estrela[$id_estrela]->id}
			AND cic.turno = {$turno->turno}
			");
			
			$nomes_imperios = "";
			foreach ($ids_imperios as $id_imperio) {
				if ($id_imperio->id_imperio == 0) {
					$nomes_imperios .= "{$id_imperio->nome_npc}; ";
				} else {
					$nomes_imperios .= "{$id_imperio->nome_imperio}; ";
				}
			}
			if (!empty($nomes_imperios)) {
				$nomes_imperios = substr($nomes_imperios,0,-2);
				$nomes_imperios = "Colonizado por <span style='text-decoration: underline;'>{$nomes_imperios}</span>";
			}
			
			if (empty($html_estrela[$estrela[$id_estrela]->id])) {
				$html_estrela[$estrela[$id_estrela]->id] = "<div class='nome_estrela'><b>{$estrela[$id_estrela]->nome}</b> ({$estrela[$id_estrela]->X};{$estrela[$id_estrela]->Y};{$estrela[$id_estrela]->Z}) {$nomes_imperios}</div>";
				$html_estrela_mini[$estrela[$id_estrela]->id] = "<div class='nome_estrela_mini'><b>{$estrela[$id_estrela]->nome}</b> ({$estrela[$id_estrela]->X};{$estrela[$id_estrela]->Y};{$estrela[$id_estrela]->Z})<br>{$nomes_imperios}</div>";
				$html_naves[$estrela[$id_estrela]->id] = "<div class='naves_no_local'>";
				$html_naves_mini[$estrela[$id_estrela]->id] = "<div class='naves_no_local'>";
				$html_planetas_na_estrela[$estrela[$id_estrela]->id] = $estrela[$id_estrela]->pega_html_planetas_estrela($apenas_recursos, $apenas_recursos);
			}
			
			$ids_naves_na_estrela = $wpdb->get_results("SELECT id FROM colonization_imperio_frota WHERE X={$estrela[$id_estrela]->X} AND Y={$estrela[$id_estrela]->Y} AND Z={$estrela[$id_estrela]->Z} AND turno_destruido=0 AND (id_estrela_destino = 0 OR id_estrela_destino IS NULL) ORDER BY id_imperio");
			$imperio_nave_na_estrela = [];
			foreach ($ids_naves_na_estrela AS $id_frota) {
				$nave_na_estrela = new frota($id_frota->id);
				if (empty($imperio_nave_na_estrela[$nave_na_estrela->id_imperio])) {
					$imperio_nave_na_estrela[$nave_na_estrela->id_imperio] = new imperio($nave_na_estrela->id_imperio,true);
				}
				if ($imperio_nave_na_estrela[$nave_na_estrela->id_imperio]->id == 0) {
					$imperio_nave_na_estrela[$nave_na_estrela->id_imperio]->nome = $nave_na_estrela->nome_npc;
				}

				if (empty($exibiu_nave[$nave_na_estrela->id]) || $exibiu_nave[$nave_na_estrela->id] == false) {
					if ($roles != "administrator") {
						if ((($imperio->sensores + $imperio->anti_camuflagem) > $nave_na_estrela->camuflagem) || $nave_na_estrela->visivel == 1 || $nave_na_estrela->camuflagem == 0 || ($nave_na_estrela->id_imperio == $imperio->id)) {
							$html_naves[$estrela[$id_estrela]->id] .= "<div class='naves'>{$nave_na_estrela->qtd} {$nave_na_estrela->tipo} '{$nave_na_estrela->nome}' ({$imperio_nave_na_estrela[$nave_na_estrela->id_imperio]->nome})</div>";
							$html_naves_mini[$estrela[$id_estrela]->id] .= "<div class='naves_mini'>{$nave_na_estrela->qtd} {$nave_na_estrela->tipo} '{$nave_na_estrela->nome}' ({$imperio_nave_na_estrela[$nave_na_estrela->id_imperio]->nome})</div>";
							$exibiu_nave[$nave_na_estrela->id] = true;
						}
					} else {
						if ($nave_na_estrela->camuflagem > 0 && $nave_na_estrela->visivel == 0) {
							$html_naves[$estrela[$id_estrela]->id] .= "<div class='naves'><i>{$nave_na_estrela->qtd} {$nave_na_estrela->tipo} '{$nave_na_estrela->nome}' ({$imperio_nave_na_estrela[$nave_na_estrela->id_imperio]->nome})</i></div>";
							$html_naves_mini[$estrela[$id_estrela]->id] .= "<div class='naves_mini'><i>{$nave_na_estrela->qtd} {$nave_na_estrela->tipo} '{$nave_na_estrela->nome}' ({$imperio_nave_na_estrela[$nave_na_estrela->id_imperio]->nome})</i></div>";
							$exibiu_nave[$nave_na_estrela->id] = true;
						} else {
							$html_naves[$estrela[$id_estrela]->id] .= "<div class='naves'>{$nave_na_estrela->qtd} {$nave_na_estrela->tipo} '{$nave_na_estrela->nome}' ({$imperio_nave_na_estrela[$nave_na_estrela->id_imperio]->nome})</div>";
							$html_naves_mini[$estrela[$id_estrela]->id] .= "<div class='naves_mini'>{$nave_na_estrela->qtd} {$nave_na_estrela->tipo} '{$nave_na_estrela->nome}' ({$imperio_nave_na_estrela[$nave_na_estrela->id_imperio]->nome})</div>";
							$exibiu_nave[$nave_na_estrela->id] = true;
						}
					}
				}				
			}
		}
		$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
		if ($roles == "administrator") {
			//echo "Término loop das naves: {$diferenca}ms<br>";
		}
	
		$html_final = "";
		if ($apenas_recursos && $imperio->id != 0) {
			$id_recursos_conhecidos = $wpdb->get_results("
			SELECT DISTINCT cir.id_recurso 
			FROM colonization_imperio_recursos AS cir 
			JOIN colonization_recurso AS cr
			ON cr.id = cir.id_recurso
			WHERE cir.id_imperio={$imperio->id} AND cir.disponivel=true AND cr.extrativo = true
			ORDER by cr.nome");
			$html_options = "<option value=''></option>";
			foreach ($id_recursos_conhecidos as $id_recurso) {
				$recurso = new recurso($id_recurso->id_recurso);
				$html_options .= "<option value={$recurso->id}>{$recurso->nome}</option> \n";
			}
			$html_final = "<label>Destacar Recurso:</label><select class='destacar_recurso' data-atributo='destacar_recurso' onchange='return destacar_recurso(this);'>
			{$html_options}
			</select>";
		}
		
		$chaves = implode(",",array_keys($html_estrela));
		$ids_estrelas = [];
		if (!empty($chaves)) {
			$ids_estrelas = $wpdb->get_results("
			SELECT DISTINCT ce.id 
			FROM colonization_estrela AS ce
			LEFT JOIN colonization_planeta AS cp
			ON cp.id_estrela = ce.id
			LEFT JOIN colonization_imperio_colonias AS cic
			ON cic.id_planeta = cp.id
			AND cic.turno = {$turno->turno}
			WHERE ce.id IN ({$chaves})
			ORDER BY (CASE WHEN cic.id_imperio = {$imperio->id} THEN 0 ELSE 1 END), ISNULL(cic.id_imperio), cic.id_imperio, cic.nome_npc, cic.capital DESC, ce.nome, ce.X, ce.Y, ce.Z
			");
		}
		$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
		if ($roles == "administrator") {
			//echo "Query das Estrelas: {$diferenca}ms<br>";
		}

		foreach ($ids_estrelas as $chave) {
			if ($html_naves_mini[$chave->id] == "<div class='naves_no_local'>") {
				$html_naves_mini[$chave->id] .= "<i>Sem Naves</i>";
			}

			$html_naves[$chave->id] .= "</div>";
			$html_naves_mini[$chave->id] .= "</div>";
			
			if ($apenas_recursos) {
				$html_naves[$chave->id] = "";
			}
			if ($mini_mapa) {
				$html_final .= "<div class='mini_mapa' style='margin-bottom: 5px;'>{$html_estrela_mini[$chave->id]}{$html_naves_mini[$chave->id]}</div>";				
			} else {
				$html_final .= "<div class='par_impar' style='margin-bottom: 5px;'>{$html_estrela[$chave->id]}{$html_naves[$chave->id]}
				{$html_planetas_na_estrela[$chave->id]}
				</div>";
			}
		}
		$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
		if ($roles == "administrator") {
			//echo "Loop do HTML Final: {$diferenca}ms<br>";
		}		
		
		//$html_final = "Império {$imperio->id} // {$roles}";
		return $html_final;
	}
	
	/******************
	function colonization_exibe_viagem_frota()
	-----------
	Exibe no painel principal se existem viagens de Naves pendentes
	******************/	
	function colonization_exibe_viagem_frota() {
		global $asgarosforum, $wpdb;
		
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
	
		if ($roles == "administrator") {
			$naves_pendentes = $wpdb->get_results("SELECT cif.id 
			FROM colonization_imperio_frota AS cif 
			LEFT JOIN colonization_estrela AS ce
			ON ce.id=cif.id_estrela_destino
			WHERE cif.id_estrela_destino != 0 AND cif.turno_destruido=0 
			ORDER BY cif.id_imperio, ce.nome");
			
			foreach ($naves_pendentes as $id) {
				$nave = new frota($id->id);
				$notice = $nave->exibe_autoriza();
				$notice = apply_filters('asgarosforum_filter_login_message', $notice);
				$asgarosforum->add_notice($notice);
			}
		}
	
		return;
	}


	/******************
	function colonization_exibe_missoes_pendente()
	-----------
	Exibe no painel principal se existem transferências de Tech pendentes
	******************/	
	function colonization_exibe_missoes_pendente() {
		global $asgarosforum, $wpdb;
		
		$user = wp_get_current_user();
		$ids_pendentes = [];
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
			$banido = get_user_meta($user->ID, 'asgarosforum_role', true);
			if ($banido === "banned") {
				return;
			} 
		}
		
		if ($roles != "administrator") {
			$imperio = new imperio();
		} else {
			return;
		}
		
		if ($imperio->id == 0) {
			return;
		}
		
		if (!empty($imperio->id)) {
			$ids_pendentes = $wpdb->get_var("
			SELECT COUNT(id) FROM colonization_missao
			WHERE ativo=1 AND
			((id_imperio=0 AND id_imperios_aceitaram NOT LIKE '{$imperio->id};%' AND id_imperios_aceitaram NOT LIKE '%;{$imperio->id};%' AND id_imperios_aceitaram NOT LIKE '%;{$imperio->id}' AND id_imperios_aceitaram !='{$imperio->id}') AND 
			(id_imperio=0 AND id_imperios_rejeitaram NOT LIKE '{$imperio->id};%' AND id_imperios_rejeitaram NOT LIKE '%;{$imperio->id};%' AND id_imperios_rejeitaram NOT LIKE '%;{$imperio->id}' AND id_imperios_rejeitaram !='{$imperio->id}') OR
			(id_imperio={$imperio->id} AND id_imperios_aceitaram='' AND id_imperios_rejeitaram=''))
			");
		}
		
		if ($ids_pendentes == 0) {
			return;
		}
		
		$page_id_forum = new configuracao(1);
		$id_missao = new configuracao(2);
	  
		$notice = "<a href='?page_id={$page_id_forum->id_post}&view=topic&id={$id_missao->id_post}'>Existem Missões pendentes para seu Império</a>";
		$notice = apply_filters('asgarosforum_filter_login_message', $notice);
		$asgarosforum->add_notice($notice);
		
		return;
	}

	
	/******************
	function colonization_exibe_tech_transfere_pendente()
	-----------
	Exibe no painel principal se existem transferências de Tech pendentes
	* Também exibe os recebimentos de Recursos pendentes
	******************/	
	function colonization_exibe_tech_transfere_pendente() {
		global $asgarosforum, $wpdb;
		
		$user = wp_get_current_user();
		$ids_pendentes_techs = [];
		$ids_pendentes_recurso = [];
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
			$banido = get_user_meta($user->ID, 'asgarosforum_role', true);
			if ($banido === "banned") {
				return;
			} 
		}
		
		if ($roles != "administrator") {
			$imperio = new imperio();
		}
		
		if (!empty($imperio->id)) {
			$ids_pendentes_techs = $wpdb->get_results("SELECT id FROM colonization_imperio_transfere_techs WHERE id_imperio_destino={$imperio->id} AND processado=0");
			$ids_pendentes_recurso = $wpdb->get_results("SELECT id FROM colonization_imperio_transfere_recurso WHERE id_imperio_destino={$imperio->id} AND processado=0");
		}
		
		foreach ($ids_pendentes_techs as $id) {
			$transfere_tech = new transfere_tech($id->id);
			
			$notice = $transfere_tech->exibe_autoriza();
			$notice = apply_filters('asgarosforum_filter_login_message', $notice);
			$asgarosforum->add_notice($notice);
		}
		
		foreach ($ids_pendentes_recurso as $id) {
			$transfere_recurso = new transfere_recurso($id->id);
			
			$notice = $transfere_recurso->exibe_autoriza();
			$notice = apply_filters('asgarosforum_filter_login_message', $notice);
			$asgarosforum->add_notice($notice);
		}
		
		return;
	}

	/******************
	function colonization_exibe_tech_transfere()
	-----------
	Exibe a opção de transferir uma Tech para outro Império
	******************/	
	function colonization_exibe_tech_transfere($atts = [], $content = null) {
		global $wpdb;

		$turno = new turno();

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
			$banido = get_user_meta($user->ID, 'asgarosforum_role', true);
			if ($banido === "banned") {
				return;
			} 
		}
		
		if (isset($atts['id'])) {
			$imperio = new imperio($atts['id'],false);
			$roles = "";
		} else {
			$imperio = new imperio();
		}
		
		$html_lista_imperios = "<select data-atributo='id_imperio_destino' style='width: 100%'>";
		$resultados = $imperio->contatos_imperio();
		if ($roles == "administrator") {
			$resultados = $wpdb->get_results("SELECT id, nome FROM colonization_imperio");
		}
		
		foreach ($resultados as $resultado) {
			if (!empty($imperio->id)) {
				if ($resultado->id != $imperio->id) {
					$html_lista_imperios .= "<option value='{$resultado->id}'>{$resultado->nome}</option>";
				}
			} else {
				$html_lista_imperios .= "<option value='{$resultado->id}'>{$resultado->nome}</option>";
			}
		}
		$html_lista_imperios .= "</select>";

		if (!empty($imperio->id)) {
			$input_imperio_origem = "<input type='hidden' data-ajax='true' data-atributo='id_imperio_origem' data-valor-original='{$imperio->id}' value='{$imperio->id}'></input>
			<div data-atributo='id_imperio_origem' value='{$imperio->id}'>{$imperio->nome}</div>";
		}
		
		$estilo_npc = "style='display: none;'";
		if ($roles == 'administrator' && empty($atts['id'])) {
			$input_imperio_origem = "<div data-atributo='nome_imperio' data-id-selecionado='' data-valor-original=''><select data-atributo='id_imperio_origem' style='width: 100%; margin-bottom: 5px;' onchange='return libera_npc(event, this);'>";
			$resultados = $wpdb->get_results("SELECT id, nome FROM colonization_imperio");
				$input_imperio_origem .= "<option value='0'>NPC</option>";
			foreach ($resultados as $resultado) {
				$input_imperio_origem .= "<option value='{$resultado->id}'>{$resultado->nome}</option>";
			}
			$input_imperio_origem .= "</select></div>";
			$estilo_npc = "";
		}
		
		$techs = new tech();
		if ($roles == 'administrator') {
			$resultados = $techs->query_tech();
		} else {
			$resultados = $techs->query_tech("AND cit.custo_pago=0",$imperio->id);
		}
		
		$html_lista_techs = "<select data-atributo='id_tech' data-ajax='true' style='width: 100%'>";		
		foreach ($resultados as $resultado) {
			$tech = new tech($resultado->id);
			$html_lista_techs .= "<option value='{$tech->id}'>{$tech->nome}</option>";
		}
		$html_lista_techs .= "</select>";

		$html = "
		<h4>Transferência de Techs</h4>
		<table class='lista_transferencia_techs' data-tabela='colonization_imperio_transfere_techs' style='width: 700px;'>
		<thead>
		<tr><th style='width: 150px;'>Império Origem</th><th style='width: 200px;'>Império Destino</th><th>Tech à ser transferida</th><th style='width: 90px;'>&nbsp;</th></tr>
		</thead>
		<tr>
		<td>
			<input type='hidden' data-atributo='id' value=''></input>
			<input type='hidden' data-atributo='where_clause' value='id'></input>
			<input type='hidden' data-atributo='where_value' data-inalteravel='true' value=''></input>
			<input type='hidden' data-atributo='turno' data-ajax='true' data-valor-original='{$turno->turno}' value='{$turno->turno}'></input>
			<input type='hidden' data-atributo='funcao_validacao' value='valida_transfere_tech'></input>
			<input type='hidden' data-atributo='funcao_pos_processamento' value='atualiza_lista_techs'></input>
			{$input_imperio_origem}
			<div data-atributo='nome_npc' data-ajax='true' data-editavel='true' data-valor-original='' data-branco='true' {$estilo_npc} id='nome_npc' data-desabilita='false'><input type='text' data-atributo='nome_npc' data-ajax='true' data-editavel='true' data-valor-original='' data-branco='true'></input></div>
		</td>
		<td>
			<div data-atributo='nome_imperio' data-id-selecionado='' data-valor-original=''>{$html_lista_imperios}</div>
		</td>
		<td>
			<div data-atributo='nome_tech' data-id-selecionado='' data-valor-original=''>{$html_lista_techs}</div>
		</td>
		<td><div><a href='#' id='envia_tech' onclick='return salva_objeto(event, this);'>Enviar Tech</a></div></td>
		</tr>
		</table>";
		
		if ($roles == "administrator") {
			$id_techs_envio = $wpdb->get_results("SELECT id FROM colonization_imperio_transfere_techs");
			$id_techs_recebidas = [];
		} else {
			$id_techs_envio = $wpdb->get_results("SELECT id FROM colonization_imperio_transfere_techs WHERE id_imperio_origem = {$imperio->id}");
			$id_techs_recebidas = $wpdb->get_results("
			SELECT id 
			FROM colonization_imperio_transfere_techs 
			WHERE id_imperio_destino = {$imperio->id}
			AND processado = true
			AND autorizado = true");
		}
		
		$transfere_tech = new transfere_tech();
		$listas = $transfere_tech->exibe_listas($imperio->id);
		
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
		
		$html .= "<hr>
		<div><b>Techs Transferidas</b></div>
		<table>
		<thead>
		<tr><td>Tech</td><td>Origem</td><td>Destino</td><td>Turno</td><td>Status</td></tr>
		</thead>
		<tbody id='techs_enviadas'>{$lista_techs_enviadas}</tbody>
		</table>
		<br><div><b>Techs Recebidas</b></div>
		<table>
		<thead>
		<tr><td>Tech</td><td>Origem</td><td>Destino</td><td>Turno</td><td>Status</td></tr>
		</thead>
		<tbody id='techs_recebidas'>{$lista_techs_recebidas}</tbody>
		</table>
		";
		
		return $html;
	}
	
	/******************
	function colonization_exibe_recurso_transfere()
	-----------
	Exibe a opção de transferir Recursos para outro Império
	******************/	
	function colonization_exibe_recurso_transfere($atts = [], $content = null) {
		global $wpdb;

		$turno = new turno();

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
			$banido = get_user_meta($user->ID, 'asgarosforum_role', true);
			if ($banido === "banned") {
				return;
			} 
		}
		
		if (isset($atts['id'])) {
			$imperio = new imperio($atts['id'],false);
			$roles = "";
		} else {
			$imperio = new imperio();
		}
		
		$html_lista_imperios = "<select data-atributo='id_imperio_destino' style='width: 100%'>";
		$resultados = $imperio->contatos_imperio();
		if ($roles == "administrator") {
			$resultados = $wpdb->get_results("SELECT id, nome FROM colonization_imperio");
		}
		
		foreach ($resultados as $resultado) {
			if (!empty($imperio->id)) {
				if ($resultado->id != $imperio->id) {
					$html_lista_imperios .= "<option value='{$resultado->id}'>{$resultado->nome}</option>";
				}
			} else {
				$html_lista_imperios .= "<option value='{$resultado->id}'>{$resultado->nome}</option>";
			}
		}
		$html_lista_imperios .= "</select>";

		if (!empty($imperio->id)) {
			$input_imperio_origem = "<input type='hidden' data-ajax='true' data-atributo='id_imperio_origem' data-valor-original='{$imperio->id}' value='{$imperio->id}'></input>
			<div data-atributo='id_imperio_origem' value='{$imperio->id}'>{$imperio->nome}</div>";
		}
		
		$estilo_npc = "style='display: none;'";
		
		$resultados = $wpdb->get_results("SELECT id_recurso FROM colonization_imperio_recursos WHERE turno={$turno->turno} AND disponivel=true AND id_imperio={$imperio->id}");
		if ($roles == 'administrator' && empty($atts['id'])) {
			$input_imperio_origem = "<div data-atributo='nome_imperio' data-id-selecionado='' data-valor-original=''>
			<select data-atributo='id_imperio_origem' style='width: 100%; margin-bottom: 5px;' onchange='return libera_npc(event, this);'>";
			$resultados = $wpdb->get_results("SELECT id, nome FROM colonization_imperio");
			$input_imperio_origem .= "<option value='0'>NPC</option>";
			foreach ($resultados as $resultado) {
				$input_imperio_origem .= "<option value='{$resultado->id}'>{$resultado->nome}</option>";
			}
			$input_imperio_origem .= "</select></div>";
			$estilo_npc = "";
			$resultados = $wpdb->get_results("SELECT id AS id_recurso FROM colonization_recurso WHERE acumulavel=true");
		}
		
		
		$html_lista_techs = "<select data-atributo='id_recurso' data-ajax='true' style='width: 100%'>";		
		foreach ($resultados as $resultado) {
			$recurso = new recurso($resultado->id_recurso);
			$html_lista_techs .= "<option value='{$recurso->id}'>{$recurso->nome}</option>";
		}
		$html_lista_techs .= "</select>";

		$html = "
		<h4>Transferência de Recursos</h4>
		<table class='lista_transferencia_techs' data-tabela='colonization_imperio_transfere_recurso' style='width: 700px;'>
		<thead>
		<tr><th style='width: 20%;'>Império Origem</th><th style='width: 20%;'>Império Destino</th><th style='width: 20%;'>Recurso</th><th style='width: 10%;'>Qtd</th><th style='width: 15%;'>&nbsp;</th></tr>
		</thead>
		<tr>
		<td>
			<input type='hidden' data-atributo='id' value=''></input>
			<input type='hidden' data-atributo='where_clause' value='id'></input>
			<input type='hidden' data-atributo='where_value' data-inalteravel='true' value=''></input>
			<input type='hidden' data-atributo='turno' data-ajax='true' data-valor-original='{$turno->turno}' value='{$turno->turno}'></input>
			<input type='hidden' data-atributo='funcao_validacao' value='valida_transfere_recurso'></input>
			<input type='hidden' data-atributo='funcao_pos_processamento' value='atualiza_lista_recursos'></input>
			{$input_imperio_origem}
			<div data-atributo='nome_npc' data-ajax='true' data-editavel='true' data-valor-original='' data-branco='true' {$estilo_npc} id='nome_npc' data-desabilita='false'>
			<input type='text' data-atributo='nome_npc' data-ajax='true' data-editavel='true' data-valor-original='' data-branco='true'></input>
			</div>
		</td>
		<td>
			<div data-atributo='nome_imperio' data-id-selecionado='' data-valor-original=''>{$html_lista_imperios}</div>
		</td>
		<td>
			<div data-atributo='nome_recurso' data-id-selecionado='' data-valor-original=''>{$html_lista_techs}</div>
		</td>
		<td>
			<div data-atributo='qtd' data-valor-original=''><input type='number' data-atributo='qtd' data-ajax='true' min=0 value=0 style='width: 50px;'></input></div>
		</td>		
		<td><div><a href='#' id='envia_recurso' onclick='return salva_objeto(event, this);'>Enviar Recursos</a></div></td>
		</tr>
		</table>";
		
		$transfere_recurso = new transfere_recurso();
		$listas = $transfere_recurso->exibe_listas($imperio->id);
		
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
		
		$html .= "<hr>
		<div><b>Recursos Transferidos</b></div>
		<table>
		<thead>
		<tr><td>Recurso</td><td>Qtd</td><td>Origem</td><td>Destino</td><td>Turno</td><td>Status</td></tr>
		</thead>
		<tbody id='techs_enviadas'>{$lista_techs_enviadas}</tbody>
		</table>
		<br><div><b>Recursos Recebidos</b></div>
		<table>
		<thead>
		<tr><td>Recurso</td><td>Qtd</td><td>Origem</td><td>Destino</td><td>Turno</td><td>Status</td></tr>
		</thead>
		<tbody id='techs_recebidas'>{$lista_techs_recebidas}</tbody>
		</table>
		";
		
		return $html;
	}	
	
	
	/******************
	function colonization_exibe_techtree()
	-----------
	Exibe a Árvore de Technologia
	******************/	
	function colonization_exibe_techtree($atts = [], $content = null) {
		global $wpdb;
		$tech = new tech();

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
			$banido = get_user_meta($user->ID, 'asgarosforum_role', true);
			if ($banido === "banned") {
				return;
			} 
		}
		
		if (!empty($atts['super'])) {
			$tech = new tech();
			$techs = $tech->query_tech();
		} elseif (!empty($atts['id'])) {
			$imperio = new imperio($atts['id']);
			$techs = $tech->query_tech(" AND ct.publica = 1", $imperio->id);
		} else {
			$techs = $tech->query_tech(" AND ct.publica = 1");
		}

		if (!empty($atts['id'])) {
			$imperio = new imperio($atts['id']);
		}
		
		$html_tech = [];
		$html_custo_pago = [];
		$style_pago = [];
		$cor_borda = [];
		$tech = [];
		$tech_parent = [];
		
		foreach ($techs AS $id) {//Primeiro popula os custos pagos e também as cores das Techs
			$tech[$id->id] = new tech($id->id);
			if (!empty($atts['id'])) {
				$id->custo_pago = $wpdb->get_var("SELECT custo_pago FROM colonization_imperio_techs WHERE id_tech={$tech[$id->id]->id} AND id_imperio={$imperio->id}");
			}
			
			if ($id->custo_pago > 0) {
				$html_custo_pago[$id->id] = " [{$id->custo_pago}/{$tech[$id->id]->custo}]";
				$style_pago[$id->id] = "style='font-style: italic;'";
				$cor_borda[$id->id] = "borda_amarela";
			} elseif ($id->custo_pago == "0") {
				$html_custo_pago[$id->id] = "";
				$style_pago[$id->id] = "";
				$cor_borda[$id->id] = "borda_preta";
			} else {
				$html_custo_pago[$id->id] = "";
				$style_pago[$id->id] = "";
				$cor_borda[$id->id] = "borda_vermelha";
			}
		}
		
		foreach ($techs AS $id) {
			if ($tech[$id->id]->nivel == 1) {
				$html_tech[$id->id] = "
				<div class='wrapper_principal'>
					<div class='wrapper_nivel'>
						<div class='wrapper_tech'>
							<div class='tech tooltip {$cor_borda[$id->id]}' {$style_pago[$id->id]}>{$tech[$id->id]->nome}{$html_custo_pago[$id->id]}
							<span class='tooltiptext'>{$tech[$id->id]->descricao}</span>
							</div>
						</div>";
			}
			
			if (!empty($tech[$id->id]->id_tech_parent)) {
				$ids_tech_parent = [];
				$nivel = $tech[$id->id]->nivel-1;
				$ids_tech_parent[$nivel] = explode(";",$tech[$id->id]->id_tech_parent);
				
				while ($nivel > 1) {
					foreach ($ids_tech_parent[$nivel] as $chave => $id_tech_parent) {
						if (empty($tech[$id_tech_parent])) {
							$tech_parent[$id_tech_parent] = new tech($id_tech_parent);	
						} else {
							$tech_parent[$id_tech_parent] = $tech[$id_tech_parent];
						}
						$nivel_anterior = $tech_parent[$id_tech_parent]->nivel-1;
						if (!empty($tech_parent[$id_tech_parent]->id_tech_parent)) {//Tem um pré-requisito, que é de nível inferior
							if (empty($ids_tech_parent[$nivel_anterior])) {
								$ids_tech_parent[$nivel_anterior] = explode(";",$tech_parent[$id_tech_parent]->id_tech_parent);
							} else {
								$ids_tech_parent[$nivel_anterior] = array_merge($ids_tech_parent[$nivel_anterior],explode(";",$tech_parent[$id_tech_parent]->id_tech_parent));
							}
						}
						$nivel = $nivel_anterior;
					}
				}

				$nivel = 1;
				if (empty($ids_tech_parent[1])) {
					echo "id_tech:".$tech[$id->id]->id."<br>";
				}
					$ids_tech_parent = $ids_tech_parent[1];
				
				foreach ($ids_tech_parent as $chave => $id_tech_parent) {
					if (empty($tech[$id_tech_parent])) {
						$tech_parent[$id_tech_parent] = new tech($id_tech_parent);	
					} else {
						$tech_parent[$id_tech_parent] = $tech[$id_tech_parent];
					}
					
					if (!empty($html_tech[$tech_parent[$id_tech_parent]->id])) {
						$wrapper_nivel = "<div class='wrapper_nivel'>";
						if (!empty($nivel_preenchido[$tech_parent[$id_tech_parent]->id][$tech[$id->id]->nivel]) && $tech[$id->id]->nivel > 1) {
							$wrapper_nivel = "";
							$html_tech[$tech_parent[$id_tech_parent]->id] = substr($html_tech[$tech_parent[$id_tech_parent]->id],0,-6); //Reabre o DIV do "wrapper_nivel"
						}
						$html_tech[$tech_parent[$id_tech_parent]->id] .= "
					{$wrapper_nivel}
					<div class='fas fa-long-arrow-alt-right wrapper_tech' style='padding-top: 12px;'>&nbsp;</div>
						<div class='wrapper_tech'>
							<div class='tech tooltip {$cor_borda[$tech[$id->id]->id]}' {$style_pago[$tech[$id->id]->id]}>{$tech[$id->id]->nome}{$html_custo_pago[$tech[$id->id]->id]}
							<span class='tooltiptext'>{$tech[$id->id]->descricao}</span>
							</div>
						</div>";
					
					$nivel_preenchido[$tech_parent[$id_tech_parent]->id][$tech[$id->id]->nivel] = true;
					}
				}
			}
			
			if ($tech[$id->id]->lista_requisitos != '') {
				foreach ($tech[$id->id]->id_tech_requisito AS $chave => $id_tech_requisito) {
					$tech_requisito = new tech($id_tech_requisito);
					if (empty($cor_borda[$tech_requisito->id])) {
						$cor_borda[$tech_requisito->id] = "borda_vermelha";
					}
					if ($tech[$id->id]->nivel == 1) {
						$html_tech[$tech[$id->id]->id] .= "
						<div class='fas fa-ellipsis-v tech tech_requisito_ellipsis' >&nbsp;</div>
						<div class='tech tech_requisito tooltip {$cor_borda[$tech_requisito->id]}'>{$tech_requisito->nome}
							<span class='tooltiptext'>{$tech_requisito->descricao}</span>
						</div>";
					} else {
						foreach ($ids_tech_parent as $chave => $id_tech_parent) {
							if (empty($tech[$id_tech_parent])) {
								$tech_parent[$id_tech_parent] = new tech($id_tech_parent);	
							} else {
								$tech_parent[$id_tech_parent] = $tech[$id_tech_parent];
							}
							if (!empty($html_tech[$tech_parent[$id_tech_parent]->id])) {						
								$html_tech[$tech_parent[$id_tech_parent]->id] .= "
						<div class='fas fa-ellipsis-v tech tech_requisito_ellipsis' >&nbsp;</div>
						<div class='tech tech_requisito tooltip {$cor_borda[$tech_requisito->id]}'>{$tech_requisito->nome}
							<span class='tooltiptext'>{$tech_requisito->descricao}</span>
						</div>";
							}
						}
					}
				}
			}
			
			if ($tech[$id->id]->nivel == 1) {
				$html_tech[$tech[$id->id]->id] .= "</div>";
			} else {
				foreach ($ids_tech_parent as $chave => $id_tech_parent) {
					if (empty($tech[$id_tech_parent])) {
						$tech_parent[$id_tech_parent] = new tech($id_tech_parent);	
					} else {
						$tech_parent[$id_tech_parent] = $tech[$id_tech_parent];
					}
					if (!empty($html_tech[$tech_parent[$id_tech_parent]->id])) {						
						
						$html_tech[$tech_parent[$id_tech_parent]->id] .= "</div>";
					}
				}
			}
		}

		$html = "<div style='background-color: #FFFFFF; overflow-x: visible; max-width: 5000px; margin-right: -50%;'>";
		$belica = 0;
		foreach ($html_tech as $chave => $html_valor) {
			if (empty($tech[$chave])) {
				$tech[$chave] = new tech($chave);
			}
			
			if ($belica == 0 and $tech[$chave]->belica == 1) {
				$html .= "<br><div><span style='font-weight: bold'>Tecnologias Bélicas</span></div>";
				$belica = 1;
			} 
			$html .= $html_valor . "
				</div><!-- Fecha wrapper_principal -->";
		}
		$html .= "</div>";
		return $html;
	}



	/******************
	function colonization_exibe_distancia_estrelas()
	-----------
	Exibe a distância entre duas estrelas
	******************/	
	function colonization_exibe_distancia_estrelas() {
		global $wpdb;
		$wpdb->hide_errors();
		
		$user = wp_get_current_user();
		$id_estrela_capital = "";
		$turno = new turno();
		$div_imperios = "";
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}

		if ($roles != "administrator") {
			$imperios[0] = new imperio();
			if (!empty($imperios[0])) {
				$colonias = $wpdb->get_results("SELECT id FROM colonization_imperio_colonias WHERE id_imperio={$imperios[0]->id} AND turno={$turno->turno} ORDER BY ID asc");
			}
			if (!empty($colonias)) {
				$colonia = new colonia($colonias[0]->id);
				$planeta = new planeta($colonia->id_planeta);
				$id_estrela_capital = $planeta->id_estrela;
			}
		} else {
			$id_imperios = $wpdb->get_results("SELECT id FROM colonization_imperio ORDER BY nome");
			$imperios = [];
			$div_imperios = "
			<div id='div_imperios' style='width: 300px;'>&nbsp;</div>
			";
			foreach ($id_imperios as $chave => $id) {
				$imperios[$chave] = new imperio ($id->id);
			}
			if (!empty($imperios[0])) {
				$colonias = $wpdb->get_results("SELECT id FROM colonization_imperio_colonias WHERE id_imperio={$imperios[0]->id} AND turno={$turno->turno} ORDER BY ID asc");
			}
			if (!empty($colonias)) {
				$colonia = new colonia($colonias[0]->id);
				$planeta = new planeta($colonia->id_planeta);
				$id_estrela_capital = $planeta->id_estrela;
			}
		}

		
		//Popula o JavaScript
		$html_javascript = "
var lista_estrelas_colonia=[];
var lista_estrelas_reabastece=[];
var estrela_capital=[]; \n";
if (!empty($imperios[0])) {
	$html_javascript .= "var id_imperio_atual = {$imperios[0]->id}; \n";
}
				
		foreach ($imperios as $imperio) {
			$colonias = $wpdb->get_results("
			SELECT cic.id, cp.id_estrela, cic.vassalo, cic.capital
			FROM colonization_imperio_colonias AS cic
			JOIN colonization_planeta AS cp
			ON cp.id = cic.id_planeta
			WHERE cic.id_imperio={$imperio->id} AND cic.turno={$turno->turno}
			ORDER BY cic.id asc");

			$html_javascript .= "
			lista_estrelas_colonia[{$imperio->id}]=[];
			lista_estrelas_reabastece[{$imperio->id}]=[];
			";

			foreach ($colonias as $id_colonia) {
				//$colonia = new colonia($id_colonia->id);
				//$planeta = new planeta($colonia->id_planeta);
				
				if ($id_colonia->vassalo == 0) {
					$html_javascript .= "lista_estrelas_colonia[{$imperio->id}][{$id_colonia->id_estrela}]={$id_colonia->id_estrela}; \n";
					
					if ($id_colonia->capital == 1) {
						$html_javascript .= "estrela_capital[{$imperio->id}]={$id_colonia->id_estrela}; \n";
					}
				} else {
					$html_javascript .= "lista_estrelas_reabastece[{$imperio->id}][{$id_colonia->id_estrela}]={$id_colonia->id_estrela}; \n";
				}
			}
			
			$reabastece = $wpdb->get_results("SELECT id_estrela FROM colonization_imperio_abastecimento WHERE id_imperio={$imperio->id}");
			foreach ($reabastece as $id_colonia) {
				$html_javascript .= "lista_estrelas_reabastece[{$imperio->id}][{$id_colonia->id_estrela}]={$id_colonia->id_estrela}; \n";
			}
		}

		$html = "
		<h3>Distância entre as Estrelas</h3>
		{$div_imperios}
		<div><label>Alcance da Tech Logística:</label> <input id='tech_logistica' type='checkbox' value='1' onchange='return lista_distancia();'></input></div>
		<div>
			<div style='clear:both;'>
				<div style='display: inline-block; width: 100px;'>Origem:</div>
				<div id='estrela_origem' style='display: inline-block; width: 300px;'>&nbsp;</div>
			</div>
			<div style='clear:both;'>
				<div style='display: inline-block; width: 100px;'>Origem:</div>
				<div id='estrela_destino' style='display: inline-block; width: 300px;'>&nbsp;</div>
			</div>
			<div id='distancia'><b>Distância:</b> 0.0</div><br>
			<div id='div_alcance_nave'><label>Alcance da Nave: </label><input type='number' min=0 max=30 value=0 id='alcance_nave' onchange='return lista_distancia();'></input></div><br>
			<div><b>Estrelas que podem ser visitadas à partir da Capital:</b></div>
			<div id='lista_distancia'>&nbsp;</div>
		</div>
		<script>
		{$html_javascript}
		
		function carrega_distancia() {
		let estrela_origem = document.getElementById('estrela_origem');
		let estrela_destino = document.getElementById('estrela_destino');
		let div_imperios = document.getElementById('div_imperios');
		
		estrela_origem.innerHTML = lista_estrelas_html({$id_estrela_capital});
		estrela_destino.innerHTML = lista_estrelas_html({$id_estrela_capital});
		if (div_imperios !== undefined && div_imperios !== null) {
			div_imperios.innerHTML = lista_imperios_html();
			
			var select_imperios = div_imperios.childNodes[1]
			select_imperios.addEventListener('change', function () {
				id_imperio_atual = this.value;
				let id_estrela = estrela_capital[id_imperio_atual];
				
				for (let index=0; index<select_estrela_origem.options.length; index++) {
					if (select_estrela_origem.options[index].value == id_estrela) {
						select_estrela_origem.selectedIndex = index;
						calcula_distancia();
						lista_distancia();
						break;
					}
					
				}
			});
		}
		
		let select_estrela_origem = estrela_origem.childNodes[1];
		let select_estrela_destino = estrela_destino.childNodes[1];
		
		select_estrela_origem.addEventListener('change', function () {calcula_distancia();});
		select_estrela_destino.addEventListener('change', function () {calcula_distancia();});
		}
		
		carrega_distancia();
		</script>
		";
		
		return $html;
	}
	
	/******************
	function colonization_exibe_distancia_estrelas()
	-----------
	Exibe a distância entre duas estrelas
	******************/	
	function colonization_exibe_hyperdrive() {
		$html = "
		<h3>Distância entre as Estrelas - Hyperdrive</h3>
		<div>
			<div style='clear:both;'>
				<div style='display: inline-block; width: 100px;'>Origem:</div>
				<div id='estrela_origem_h' style='display: inline-block; width: 300px;'>&nbsp;</div>
			</div>
			<div style='clear:both;'>
				<div style='display: inline-block; width: 100px;'>Origem:</div>
				<div id='estrela_destino_h' style='display: inline-block; width: 300px;'>&nbsp;</div>
			</div>
			<div id='distancia_h'><b>Caminho do Hyperdrive:</b></div>
		</div>
		<script>
		function carrega_distancia_h () {
		let estrela_origem = document.getElementById('estrela_origem_h');
		let estrela_destino = document.getElementById('estrela_destino_h');
		
		estrela_origem.innerHTML = lista_estrelas_html();
		estrela_destino.innerHTML = lista_estrelas_html();
		
		let select_estrela_origem = estrela_origem.childNodes[1];
		let select_estrela_destino = estrela_destino.childNodes[1];
		
		select_estrela_origem.addEventListener('change', function () {calcula_pulos_hyperdrive();});
		select_estrela_destino.addEventListener('change', function () {calcula_pulos_hyperdrive();});
		}
		
		carrega_distancia_h();
		</script>
		";
		
		return $html;
	}
	
	/******************
	function colonization_exibe_barra_recursos()
	-----------
	Exibe a barra de Recursos do Império
	******************/	
	function colonization_exibe_barra_recursos() {
		global $asgarosforum, $wpdb;

		$page_id_forum = new configuracao(1);
		//Só mostra a barra de recursos se for o Fórum
		if (empty($_GET['page_id'])) {
			return;
		} elseif ($_GET['page_id'] != $page_id_forum->id_post) {
			return;
		}

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		$html = "<div id='barra_recursos' class='nojq barra_recursos'>";
		$html_recursos = "";
		if ($roles == "administrator") {
			$resultados = $wpdb->get_results("SELECT id FROM colonization_imperio");
		} else {
			$id_jogador = get_current_user_id();
			$resultados = $wpdb->get_results("SELECT id FROM colonization_imperio WHERE id_jogador={$id_jogador}");
		}
		
		if (empty($resultados)) {
			return; 
		}
		
		foreach ($resultados as $resultado) {
			$imperio = new imperio($resultado->id);
			$html_recursos .= "<div class='linha_barra_recursos'>
			<div class='celula_barra_recursos'><b>{$imperio->nome}</b></div>
			<div class='celula_barra_recursos'>{$imperio->icones_html}</div>";
			
			$recursos_atuais = $imperio->exibe_recursos_atuais(true);
			$recursos_atuais = substr($recursos_atuais,19); //Remove o cabeçalho
			$html_recursos .= "<div class='celula_barra_recursos'>{$recursos_atuais}</div>
			</div>";
		}
		
		//$html_recursos = substr($html_recursos,0,-4);
		
		$html .= $html_recursos."</div>
		<div style='position: static; top: 0px; left: 0px; height: 20px;'>&nbsp;</div>";

		/***DEBUG!
		if ($roles == "administrator") {
			echo $html;
		}
		//****/
		echo $html;
	}

	
	/******************
	function colonization_exibe_prestigio()
	-----------
	Exibe o Prestígio do Usuário
	******************/	
	function colonization_exibe_prestigio($author_id, $author_posts) {
		global $asgarosforum, $wpdb;

	
		$user_meta=get_userdata($author_id);
		$user_roles="";
		if (!empty($user_meta->roles[0])) {
			$user_roles=$user_meta->roles[0];
			$banido = get_user_meta($user_meta->ID, 'asgarosforum_role', true);
		}
		
		if ($user_roles == "administrator") {
			$prestigio = "&infin;";
		} elseif ($banido === "banned") {
			$prestigio = "<span style='color: #DD0000; font-weight: bold;'>BANIDO!</span>";
		} else {
			$prestigio = $wpdb->get_var("SELECT prestigio FROM colonization_imperio WHERE id_jogador={$author_id}");
			$prestigio_up = $wpdb->get_var("SELECT COUNT(*) FROM wp_forum_reactions WHERE author_id={$author_id} AND reaction='up'");
			if (empty($prestigio)) {
				$prestigio = 0;
			}
			$prestigio = $prestigio + $prestigio_up;
		}
		
		echo "<div>Prestígio: {$prestigio}</div>";
	}
	
	/******************
	function colonization_install()
	-----------
	Instala o plugin e cria os objetos necessários para rodar o sistema "Colonization"
	******************/
	function colonization_install() {
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
	function colonization_nome_imperio($atts = [], $content = null)
	----------------------
	Chamado pelo shortcode [colonization_exibe_imperio]
	$atts = [] - lista de atributos dentro do shortcode 
	(por exemplo, o shortcode [colonization_exibe_imperio id_imperio="1"] poderia exibir
	os dados do Império com id="1"
	***********************/	
	function colonization_nome_imperio($atts = [], $content = null) {
		if (isset($atts['id'])) {
			$imperio = new imperio($atts['id']);
		} else {
			$imperio = new imperio();
		}
		
		if (!empty($imperio->id)) {
			return "Império '{$imperio->nome}'";
		}
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
		if (isset($atts['id'])) {
			$imperio = new imperio($atts['id']);
		} else {
			$imperio = new imperio();
		}
		
		return $imperio->imperio_exibe_imperio();
	}

	/***********************
	function colonization_exibe_colonias_imperio($atts = [], $content = null)
	----------------------
	Chamado pelo shortcode [colonization_exibe_colonias_imperio]
	$atts = [] - lista de atributos dentro do shortcode 
	***********************/	
	function colonization_exibe_colonias_imperio($atts = [], $content = null) {
		if (isset($atts['id'])) {
			$imperio = new imperio($atts['id']);
		} else {
			$imperio = new imperio();
		}
		
		return $imperio->imperio_exibe_colonias_imperio();
	}

	/***********************
	function colonization_exibe_recursos_colonias_imperio($atts = [], $content = null)
	----------------------
	Chamado pelo shortcode [colonization_exibe_recursos_colonias_imperio]
	$atts = [] - lista de atributos dentro do shortcode 
	***********************/	
	function colonization_exibe_recursos_colonias_imperio($atts = [], $content = null) {
		global $wpdb;
		
		if (isset($atts['id'])) {
			$imperio = new imperio($atts['id']);
		} else {
			$imperio = new imperio();
		}

		if (isset($atts['turno'])) {
			$turno = new turno($atts['turno']);
		} else {
			$turno = new turno();
		}
		
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		$resultados = $wpdb->get_results("SELECT id FROM colonization_imperio_colonias WHERE id_imperio={$imperio->id} AND turno={$turno->turno}");
		
		$html = "
		<table class='lista_recursos_colonias' data-tabela='colonization_acoes_turno'>
		<thead>
		<tr><th style='width: 30%;'>Planeta (X;Y;Z)</th><th>Recursos</th></tr>
		</thead>
		<tbody>";
		
		foreach ($resultados as $resultado) {
			$colonia = new colonia($resultado->id, $turno->turno);
			if ($colonia->num_instalacoes == 0) {//Se não tiver Instalações na colônia, pula a mesma
				continue;
			}

			$colonia->planeta = new planeta($colonia->id_planeta);
			$colonia->estrela = new estrela($colonia->id_estrela);

			$lista_recursos = $colonia->exibe_recursos_colonia();
			
			$html .= "<tr><td><a href='#' onclick='return muda_nome_colonia({$colonia->id_planeta}, event);'>{$colonia->planeta->nome}</a> - {$colonia->estrela->X};{$colonia->estrela->Y};{$colonia->estrela->Z} / {$colonia->planeta->posicao}</td>
			<td>{$lista_recursos}</td>
			</tr>\n";				
		}
		
		$html .= "</tbody></table>";
		
		return $html;
	}

	/***********************
	function colonization_exibe_lista_imperios($atts = [], $content = null)
	----------------------
	Chamado pelo shortcode [colonization_exibe_lista_imperios]
	$atts = [] - lista de atributos dentro do shortcode 
	***********************/	
	function colonization_exibe_lista_imperios($atts = [], $content = null) {
		global $wpdb;
		
		//Pega a lista de impérios
		$lista_id_imperio = $wpdb->get_results("SELECT id FROM colonization_imperio");
		$html_lista_imperios = "";
		
		$html = "
		<table class='lista_imperios' data-tabela='colonization_acoes_turno' style='width: 500px;'>
		<thead>
		<tr><td style='width: 300px;'><b>Império</b></td><td><b>Pontuação</b></td></tr>
		</thead>
		<tbody>";
		
		$lista_pontuacao = [];
		$lista_nome = [];
		$imperio = [];
		foreach ($lista_id_imperio as $id) {
			$imperio[$id->id] = new imperio($id->id, true);
			$lista_pontuacao[$id->id] = $imperio[$id->id]->pontuacao;
			$lista_nome[$id->id] = $imperio[$id->id]->nome;
		}
		
		arsort($lista_pontuacao, SORT_NUMERIC);
		
		foreach ($lista_pontuacao as $id_imperio => $pontuacao) {
			//$imperio = new imperio($id_imperio, true);
			$html_pontuacao = "<div class='fas fa-search tooltip'>{$imperio[$id_imperio]->pontuacao}
			<span class='tooltiptext'>
			Desenvolvimento: {$imperio[$id_imperio]->pontuacao_desenvolvimento}<br>			
			Colônias: {$imperio[$id_imperio]->pontuacao_colonia}<br>
			Techs: {$imperio[$id_imperio]->pontuacao_tech}<br>
			Bélica: {$imperio[$id_imperio]->pontuacao_belica}<br>
			</span></div>";	
			
			$html .= "<tr><td>{$lista_nome[$id_imperio]}</td>
			<td>{$html_pontuacao}</td>
			</tr>";

		}
		
		$html .= "</tbody></table>";
		
		return $html;
	}
	
	
	
	/***********************
	function colonization_exibe_acoes_imperio($atts = [], $content = null)
	----------------------
	Chamado pelo shortcode [colonization_exibe_acoes_imperio]
	$atts = [] - lista de atributos dentro do shortcode 
	(por exemplo, o shortcode [colonization_exibe_acoes_imperio id_imperio="1"] poderia exibir
	os dados do Império com id="1"
	***********************/	
	function colonization_exibe_acoes_imperio($atts = [], $content = null) {
		global $wpdb;
		
		if (isset($atts['turno'])) {
			//$turno = new turno ($atts['turno']);
			$turno = $atts['turno'];
		} else {
			$turno = new turno();
			$turno = $turno->turno;
		}
		
		$turno_atual = new turno();
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}

		if (isset($atts['id'])) {
			$imperio = new imperio($atts['id'],false,$turno);
		} else {
			$imperio = new imperio(0,false,$turno);
		}
		
		if ($imperio->id == 0) {
			return;
		}
		
		$imperio->acoes = new acoes($imperio->id, $turno);
		
		$recursos_atuais = $imperio->exibe_recursos_atuais();
		$recursos_produzidos = $imperio->acoes->exibe_recursos_produzidos();
		$recursos_consumidos = $imperio->acoes->exibe_recursos_consumidos();
		$balanco_recursos = $imperio->acoes->exibe_recursos_balanco();
		$imperio->acoes->lista_dados(false); //Mostra somente o Turno atual
		$lista_colonias = $imperio->exibe_lista_colonias();
		$html_frota = "";
	
		
		//***
		$ids_frota = $wpdb->get_results("
		SELECT cif.id, MAX(cfhm.id) AS id_historico
		FROM colonization_imperio_frota AS cif
		LEFT JOIN colonization_frota_historico_movimentacao AS cfhm
		ON cfhm.id_nave = cif.id AND cfhm.turno = {$turno}
		WHERE (cif.turno_destruido = 0 OR cif.turno_destruido > {$turno}) AND cif.id_imperio = {$imperio->id} AND cif.turno <= {$turno}
		AND (cfhm.turno = {$turno} OR cfhm.turno IS NULL)
		GROUP BY cif.id
		ORDER BY cif.nivel_estacao_orbital DESC	
		");
		//***/
		
		/***
		$ids_frota = $wpdb->get_results("
		SELECT cif.id, 0 AS id_estrela_destino
		FROM colonization_imperio_frota AS cif
		WHERE cif.id_imperio = {$imperio->id} AND cif.turno <= {$turno} AND (cif.turno_destruido=0 OR cif.turno_destruido > {$turno})
		ORDER BY cif.nivel_estacao_orbital DESC");
		//***/
		
		foreach ($ids_frota as $ids) {
			$nave = new frota($ids->id);
			$html_qtd = "";
			
			if ($nave->qtd > 1) {
				$html_qtd = "{$nave->qtd} ";
			}
			
			if (empty($ids->id_historico)) {
				$id_estrela = $wpdb->get_var("SELECT id FROM colonization_estrela WHERE X={$nave->X} AND Y={$nave->Y} AND Z={$nave->Z}");
			} else {
				$id_estrela = $wpdb->get_var("SELECT id_estrela_destino FROM colonization_frota_historico_movimentacao WHERE id={$ids->id_historico}");
			}
			
			$pesquisa_anterior = "";
			if (!empty($id_estrela)) {
				$pesquisa_anterior = $wpdb->get_var("SELECT id FROM colonization_imperio_historico_pesquisa  WHERE id_imperio={$imperio->id} AND id_estrela={$id_estrela}");
				$nave->estrela = new estrela($id_estrela);
			}
			
			$html_pesquisa_nave = "";
			if (empty($pesquisa_anterior) && $nave->pesquisa==1) {
				$html_pesquisa_nave = "<div class='fas fa-search tooltip'><span class='tooltiptext'>Sistema sendo pesquisado</span></div>";	
			}
			
			$html_estacao_orbital = "";
			$html_danos = "";
			if ($nave->nivel_estacao_orbital > 0) {
				$html_mk = $this->html_mk($nave->nivel_estacao_orbital);
				
				$html_estacao_orbital = "<div class='fas fa-drone tooltip'><span class='tooltiptext'>Estação Orbital{$html_mk}</span></div>";	
			}
			
			$link_visivel = "";
			if ($nave->visivel == 0 && $nave->camuflagem > 0) {
				$link_visivel = "<a href='#' onclick='return nave_visivel(this,event,{$nave->id});'><div class='fad fa-hood-cloak tooltip'><span class='tooltiptext'>Desativar Camuflagem</span></div></a>";
			}
			
			if ($nave->HP < $nave->HP_max && $turno == $turno_atual->turno) {
				$nivel_dano = round((($nave->HP)/($nave->HP_max))*5,0);
				$icone_dano = "fal fa-claw-marks";
				switch ($nivel_dano) {
					case 5:
					$estilo_dano = "style='color: #124612;'";
					$nivel_dano = "Danos Mínimos";
					break;
					case 4:
					$estilo_dano = "style='color: #A47200;'";
					$nivel_dano = "Danos Moderados";
					break;
					case 3:
					$estilo_dano = "style='color: #FFBF00;'";
					$nivel_dano = "Danos Severos";
					break;
					case 2:
					$estilo_dano = "style='color: #D2222D;'";
					$nivel_dano = "Danos Críticos";
					break;
					case 1:
					$estilo_dano = "style='color: #4A0808;'";
					$nivel_dano = "Incapacitada!";
					break;
					default:
					$estilo_dano = "";
					$icone_dano = "fas fa-skull-crossbones";
					$nivel_dano = "DESTRUÍDA!!!";
				}
				
				$html_danos = "<div class='{$icone_dano} tooltip' {$estilo_dano}><span class='tooltiptext'>{$nivel_dano}</span></div>";
			}
			
			if (!empty($nave->id_estrela_destino) && $turno == $turno_atual->turno) {
				$html_nave_estrela_atual = "<span class='nave_em_transito'>Nave em trânsito</span>";
			} else {
				$html_nave_estrela_atual = "{$nave->estrela->nome} ({$nave->estrela->X};{$nave->estrela->Y};{$nave->estrela->Z})";
			}
			
			$html_nome_nave = $nave->nome;
			if ($nave->id_imperio != 0) {
				$html_nave = $nave->html_nave($imperio);
				$html_nome_nave = "<span class='tooltip'><span class='tooltiptext'>{$html_nave}</span>{$nave->nome}</span>";
			}
			
			$html_frota .= "<div style='background-color: #EFEFEF; padding: 2px; margin: 2px; display: inline-table;'>
			{$html_estacao_orbital}<b>{$html_qtd}{$html_nome_nave}</b>&nbsp;{$html_danos} {$link_visivel} {$html_pesquisa_nave}{$html_nave_estrela_atual}
			</div>";
		}
		
		$html_lista	= "
		<div><h4>COLONIZATION - Ações do Império '{$imperio->nome}' - Turno {$turno}</h4></div>
		<div id='lista_colonias_imperio_{$imperio->id}'>{$lista_colonias}</div><br>
		<div id='recursos_atuais_imperio_{$imperio->id}' >{$recursos_atuais}</div><br>
		<div id='recursos_produzidos_imperio_{$imperio->id}' style='display: none;'>{$recursos_produzidos}</div>
		<div id='recursos_consumidos_imperio_{$imperio->id}' style='display: none;'>{$recursos_consumidos}</div>
		<div id='recursos_balanco_imperio_{$imperio->id}'>{$balanco_recursos}</div><br>
		<div><b>Frota do Império</b></div>
		<div><span style='text-decoration: underline;'>Legenda:</span> <i class='fas fa-heart'></i>HP <i class='far fa-tachometer-alt'></i>Velocidade <i class='fas fa-hard-hat'></i>Blindagem <i class='fas fa-shield'></i>Escudos 
		<i class='far fa-sword-laser'></i>PdF Laser <i class='far fa-bahai'></i>PdF Torpedos <i class='far fa-asterisk'></i>PdF Projéteis <i class='fas fa-users'></i>Poder de Invasão</div>
		<div id='frota_imperio_{$imperio->id}'>{$html_frota}</div><br>
		<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_acoes_turno'>
		<thead>
		<tr style='background-color: #E5E5E5; font-weight: 700;'><td style='width: 40%;'>Slots | Colônia (X;Y;Z;P) | 
		(<div class='fas fa-user-circle	tooltip'><span class='tooltiptext'>MdO Disponível Sistema</span></div>)
		<div class='fas fa-user-clock tooltip'><span class='tooltiptext'>MdO</span></div>
		/<div class='fas fa-user tooltip'><span class='tooltiptext'>Pop</span></div>
		</td>
		
		<td style='width: 35%;'>Instalação</td><td style='width: 35%;'>Utilização (0-10)</td><td style='width: 2%;'>&nbsp;</td></tr>
		</thead>
		<tbody>";
		
		$html_lista .= $imperio->acoes->lista_dados(false); //Mostra somente o Turno atual
		
		$html_lista .= "</tbody>
		</table>";		
		
		return $html_lista;
	}

	function colonization_exibe_lista_imperios_pontuacao($atts = [], $content = null) {
		global $wpdb;
		$turno = new turno();
		
		$html_lista = "    <script type='text/javascript'>
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(drawChart);

		function drawChart() {
			var data = google.visualization.arrayToDataTable([";
		
		$imperios = $wpdb->get_results("SELECT id FROM colonization_imperio ORDER BY nome");
		
		$html_lista .= "['Turno'";
		foreach ($imperios as $id) {
			$imperio = new imperio($id->id, true);
			$html_lista .= ", '{$imperio->nome}'";
		}
		
		$html_lista .= "],
		";

		//$html_linha_turno = [];
		
		for ($turno_linha = 1; $turno_linha <= $turno->turno; $turno_linha++) {
			$html_lista .= "[{$turno_linha}";
			foreach ($imperios as $id) {
				$imperio = new imperio($id->id, true, $turno_linha);
				$html_lista .= ", {$imperio->pontuacao}";
			}
			$html_lista .= "],
			";
		}
		
		$html_lista .= "
		]);

			var options = {
				title: 'Pontuação',
				hAxis: {title: 'Turno',  titleTextStyle: {color: '#333'}}
			};
		
			var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
			chart.draw(data, options);
      }
    </script>
	<div id='chart_div' style='width: 800px; height: 500px;'></div>";
	
		return $html_lista;
		
		
	}


	/***********************
	function colonization_exibe_mapa_estelar($atts = [], $content = null)
	----------------------
	Chamado pelo shortcode [colonization_exibe_mapa_estelar]
	
	Exibe o mapa de estrelas
	***********************/	
	function colonization_exibe_mapa_estelar($atts = [], $content = null) {
		global $wpdb;
		
		$estrelas = $wpdb->get_results("SELECT nome, tipo, X, Y, Z, ROUND(X+Z*(SQRT(2)/2),2) AS X3D, ROUND(Y+Z*(SQRT(2)/2),2) AS Y3D FROM colonization_estrela");

		
		$html_lista = "
	<script type='text/javascript'>
    var data_externa = [];
	google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(pega_estrelas_ajax);
	
	function desenha_mapa_estelar() {
		let data_array = [
			['', '', {role: 'annotation'}, {role: 'style'}, {role: 'tooltip'}]
		];

		data_externa.forEach(element => {
			data_array.push(element);
		});

		
		var data = google.visualization.arrayToDataTable(data_array);
		
		
		var options = {
			title: 'Posição das estrelas',
			titleTextStyle: { color: '#DDD' },
			hAxis: {minValue: 0, maxValue: 100, textStyle:{color: '#DDD'}, titleTextStyle:{color: '#DDD'}},
			vAxis: {direction: -1, minValue: 0, maxValue: 100, textStyle:{color: '#DDD'}, titleTextStyle:{color: '#DDD'}},
			chartArea: {'width': '90%', 'height': '90%'},
			backgroundColor: '#000',
			explorer: { actions: ['dragToZoom', 'rightClickToReset'] }
		};

		var chart = new google.visualization.ScatterChart(document.getElementById('div_mapa_estelar'));
			chart.draw(data, options);
	}
    </script>
	<div id='div_mapa_estelar' style='width: 900px; height: 900px;'></div>";
	
		return $html_lista;
	}

	/***********************
	function colonization_exibe_frota_imperio($atts = [], $content = null)
	----------------------
	Chamado pelo shortcode [colonization_exibe_frota_imperio]
	$atts = [] - lista de atributos dentro do shortcode 
	***********************/	
	function colonization_exibe_frota_imperio($atts = [], $content = null) {
		global $wpdb;
		
		if (isset($atts['id'])) {
			$imperio = new imperio($atts['id']);
		} else {
			$imperio = new imperio();
		}

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
			$banido = get_user_meta($user->ID, 'asgarosforum_role', true);
			if ($banido === "banned") {
				return;
			} 
		}		

		$turno = new turno();
		
		$html = "
		<div style='width: auto; height: auto;'>
		<table class='lista_frota_imperio' data-tabela='colonization_imperio_frota'>
		<thead>
		<tr><th style='width: 40%;'>Nome</th><th style='width: 22%;'>Posição</th><th>Despachar Nave</th></tr>
		</thead>
		";
		
		$lista_frota_imperio = $wpdb->get_results("SELECT DISTINCT cif.id 
		FROM colonization_imperio_frota AS cif 
		LEFT JOIN colonization_estrela AS ce
		ON ce.X=cif.X AND ce.Y=cif.Y AND ce.Z=cif.Z
		WHERE cif.id_imperio={$imperio->id} AND cif.turno_destruido=0
		ORDER BY (cif.nivel_estacao_orbital>0) DESC, ce.nome");
		$html_id_estrela_destino = "";
		$html_buracos_de_minhoca = "";
		$index = 0;		
		
		foreach ($lista_frota_imperio as $id) {
			$frota = new frota($id->id);

			$html .= "<tr>". $frota->exibe_frota() . "</tr>";
			
			$html_id_estrela_destino .= "
			id_estrela_destino[{$index}] = {$frota->id_estrela_destino};";
			
			if ($frota->estrela->id == 0) {
				$html_id_estrela_destino .= "id_estrela_atual[{$index}] = 'nave_{$frota->id}';
			lista_x_estrela['nave_{$frota->id}'] = {$frota->X};
			lista_y_estrela['nave_{$frota->id}'] = {$frota->Y};
			lista_z_estrela['nave_{$frota->id}'] = {$frota->Z}; \n";

			$html_buracos_de_minhoca .= "			buracos_de_minhoca[{$index}] = []; \n";
			} else {
				$html_id_estrela_destino .= "			id_estrela_atual[{$index}] = {$frota->estrela->id}; \n";
				
				$html_buracos_de_minhoca .= "			buracos_de_minhoca[{$index}] = []; \n";

				if (!empty($frota->destinos_buracos_minhoca)) {
					foreach ($frota->destinos_buracos_minhoca as $chave_destinos_buracos_minhoca => $id_estrela_destino_buraco_minhoca) {
						$html_buracos_de_minhoca .= "buracos_de_minhoca[{$index}][{$chave_destinos_buracos_minhoca}] = {$id_estrela_destino_buraco_minhoca};";
					}
				}
			}
			
			
			$index++;
		}

		$lista_estrelas_sem_pesquisa = $wpdb->get_results("
		SELECT DISTINCT ce.id, cihp.id_estrela
		FROM colonization_estrela AS ce
		LEFT JOIN
		(SELECT id_estrela FROM 
		colonization_imperio_historico_pesquisa 
		WHERE id_imperio={$imperio->id} AND sensores >= {$imperio->sensores}
		) AS cihp
		ON cihp.id_estrela = ce.id
		WHERE cihp.id_estrela IS NULL");
		$html_id_estrela_pesquisa = "";
		
		foreach ($lista_estrelas_sem_pesquisa as $ids_estrelas_sem_pesquisa) {
			$html_id_estrela_pesquisa .= "id_estrela_pesquisa[{$ids_estrelas_sem_pesquisa->id}] = 1; \n";
		}
		
		$html .= "</table>
		</div>";

		//Popula o JavaScript
		$html_javascript = "
var lista_estrelas_colonia=[];
var lista_estrelas_reabastece=[];
var estrela_capital=[];
var id_estrela_atual = [];
var id_imperio_atual = {$imperio->id};
				";
				
		$colonias = $wpdb->get_results("
		SELECT cic.id, cic.vassalo, cic.capital, cp.id_estrela
		FROM colonization_imperio_colonias AS cic 
		JOIN colonization_planeta AS cp
		ON cp.id = cic.id_planeta
		WHERE cic.id_imperio={$imperio->id} AND cic.turno={$turno->turno} 
		ORDER BY cic.id asc");
		$html_javascript .= "
		lista_estrelas_colonia[{$imperio->id}]=[];
		lista_estrelas_reabastece[{$imperio->id}]=[];
		";
		foreach ($colonias as $id_colonia) {
			//$colonia = new colonia($id_colonia->id);
			//$planeta = new planeta($colonia->id_planeta);
			$html_javascript .= "lista_estrelas_colonia[{$imperio->id}][{$id_colonia->id_estrela}]={$id_colonia->id_estrela}; \n";
			if ($id_colonia->capital == 1) {
				$html_javascript .= "estrela_capital[{$imperio->id}]={$id_colonia->id_estrela}; \n";
			}
		}

		$reabastece = $wpdb->get_results("SELECT id_estrela FROM colonization_imperio_abastecimento WHERE id_imperio={$imperio->id}");
		foreach ($reabastece as $id_estrela) {
			$html_javascript .= "lista_estrelas_reabastece[{$imperio->id}][{$id_estrela->id_estrela}]={$id_estrela->id_estrela};\n
			";
		}
		
		$html .= "
		<script>
		{$html_javascript}
		var id_estrela_destino = [];
		{$html_id_estrela_destino}
		var id_estrela_pesquisa = [];
		{$html_id_estrela_pesquisa}
		var buracos_de_minhoca = [];
		{$html_buracos_de_minhoca}
		var html_lista_estrelas = lista_estrelas_html();
		
		let popula = popula_selects_estrelas_frotas(); //Depois de carregar os selects das naves, pode popular
		</script>
		";

		return $html;
	}

	/***********************
	function colonization_exibe_techs_imperio($atts = [], $content = null)
	----------------------
	Chamado pelo shortcode [colonization_exibe_techs_imperio]
	$atts = [] - lista de atributos dentro do shortcode 
	***********************/	
	function colonization_exibe_techs_imperio($atts = [], $content = null) {
		global $wpdb;
		
		if (isset($atts['id'])) {
			$imperio = new imperio($atts['id']);
		} else {
			$imperio = new imperio();
			$atts['id'] = $imperio->id;
		}
		
		$turno = new turno();
		
		//***
		$html = "<div>";
		
		$html .= $this->colonization_exibe_techtree($atts);
		
		$html .= "</div>";
		
		return $html;
	}

	/***********************
	function colonization_exibe_reabastece_imperio($atts = [], $content = null)
	----------------------
	Chamado pelo shortcode [colonization_exibe_reabastece_imperio]
	$atts = [] - lista de atributos dentro do shortcode 
	***********************/	
	function colonization_exibe_reabastece_imperio($atts = [], $content = null) {
		global $wpdb;
		
		if (isset($atts['id'])) {
			$imperio = new imperio($atts['id']);
		} else {
			$imperio = new imperio();
		}
		
		$turno = new turno();
		
		$html = "<div>";
		
		$lista_frota_imperio = $wpdb->get_results("SELECT id FROM colonization_imperio_abastecimento WHERE id_imperio={$imperio->id}");
		
		foreach ($lista_frota_imperio as $id) {
			$reabastece_imperio = new reabastece_imperio($id->id);
			$estrela = new estrela($reabastece_imperio->id_estrela);
			$html .= "{$estrela->nome} ({$estrela->X};{$estrela->Y};{$estrela->Z})<br>";
		}
		$html .= "</div>";

		return $html;
	}

	/***********************
	function colonization_exibe_autoriza_reabastece_imperio($atts = [], $content = null)
	----------------------
	Chamado pelo shortcode [colonization_exibe_autoriza_reabastece_imperio]
	$atts = [] - lista de atributos dentro do shortcode 
	***********************/	
	function colonization_exibe_autoriza_reabastece_imperio($atts = [], $content = null) {
		global $wpdb;

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
			$banido = get_user_meta($user->ID, 'asgarosforum_role', true);
			if ($banido === "banned") {
				return;
			} 
		}

		$turno = new turno();

		if (isset($atts['id'])) {
			$imperio = new imperio($atts['id'],false);
			$roles = "";
		} else {
			$imperio = new imperio();
		}		
		
		$where_id = "";
		if ($roles == "administrator" && $imperio->id == 0) {
			$lista_id = $wpdb->get_results("
			SELECT cic.id 
			FROM colonization_imperio_colonias AS cic
			WHERE cic.turno={$turno->turno}
			ORDER BY id_imperio, nome_npc");
		} else {
			$lista_id = $wpdb->get_results("
			SELECT cic.id 
			FROM colonization_imperio_colonias AS cic
			WHERE cic.id_imperio={$imperio->id}
			AND cic.turno={$turno->turno}
			AND cic.vassalo=0
			");
			
			$where_id = "WHERE id != {$imperio->id} AND id IN (SELECT id_imperio_contato FROM colonization_diplomacia WHERE id_imperio={$imperio->id})";
		}
		
		$lista_ids_imperios = $wpdb->get_results("SELECT id, nome FROM colonization_imperio {$where_id}");
		
		$html_lista = "";
		
		$lista_id_estrela = [];
		foreach ($lista_id as $id) {
			$colonia = new colonia($id->id);
			$planeta = new planeta($colonia->id_planeta);
			$estrela = new estrela($planeta->id_estrela);
			
			$lista_id_estrela[$estrela->id] = $estrela->id;
			if ($colonia->id_imperio == 0) {
				if (empty($nome_npc[$estrela->id])) {
					$nome_npc[$estrela->id] = $colonia->nome_npc;
				} else {
					$nome_npc[$estrela->id] .= " / " . $colonia->nome_npc;
				}
			} else {
				$imperio_colonia = new imperio($colonia->id_imperio);
				$nome_npc[$estrela->id] = $imperio_colonia->nome;
			}
		}

		$coluna = 1;
		foreach ($lista_id_estrela as $id_estrela => $valor) {
			$estrela = new estrela($id_estrela);
			
			$html_lista_imperios = "";
			$html_nome_npc = "";
			foreach ($lista_ids_imperios as $id_imperio) {
				$ponto_abastece = $wpdb->get_var("SELECT id FROM colonization_imperio_abastecimento WHERE id_imperio={$id_imperio->id} AND id_estrela={$estrela->id}");
				$abastece_checked = "";
				if (!empty($ponto_abastece)) {
					$abastece_checked = "checked";
				}
				
				$html_lista_imperios .= "<input type='checkbox' onchange='return salva_reabastece(event, this,{$id_imperio->id},{$estrela->id});' {$abastece_checked}></input><label>{$id_imperio->nome}</label><br>";
			}
			
			if ($roles == "administrator") {
				$html_nome_npc = "<i>{$nome_npc[$estrela->id]}</i><br>";
			}
			
			$html_lista .= "<div style='display: inline-block; width: 160px; padding: 2px; margin: 5px;'>
			<b>{$estrela->nome} ({$estrela->X};{$estrela->Y};{$estrela->Z})</b><br>
			{$html_nome_npc}
			{$html_lista_imperios}
			</div>";
		
			$coluna++;
			if ($coluna == 5) {
				$coluna = 1;
				$html_lista .= "<br>";
			}
		
		}
		
		$html = $html_lista;

		return $html;
	}

	/***********************
	function colonization_exibe_diplomacia($atts = [], $content = null)
	----------------------
	Chamado pelo shortcode [colonization_exibe_diplomacia]
	$atts = [] - lista de atributos dentro do shortcode 
	***********************/	
	function colonization_exibe_diplomacia($atts = [], $content = null) {
		global $wpdb;

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
			$banido = get_user_meta($user->ID, 'asgarosforum_role', true);
			if ($banido === "banned") {
				return;
			} 
		}

		if (isset($atts['id'])) {
			$imperio = new imperio($atts['id']);
		} else {
			$imperio = new imperio();
		}

		$ids_diplomacia = $wpdb->get_results("SELECT id_imperio, id_imperio_contato, nome_npc, acordo_comercial FROM colonization_diplomacia WHERE id_imperio={$imperio->id}");
		
		if ($roles == "administrator" && $imperio->id == 0) {
			$ids_diplomacia = $wpdb->get_results("SELECT id_imperio, id_imperio_contato, nome_npc, acordo_comercial FROM colonization_diplomacia ORDER BY id_imperio");
		}
		
		$html = "<div class='titulo_diplomacia'>Situação Diplomática</div>";
		$id_imperio_anterior = $imperio->id;
		//$html .= "\n<div class='titulo_imperio'>{$imperio->nome}</div>";
		foreach ($ids_diplomacia as $id_diplomacia) {
			if ($id_imperio_anterior != $id_diplomacia->id_imperio && $roles == "administrator") {
				$id_imperio_anterior = $id_diplomacia->id_imperio;
				$imperio = new imperio($id_diplomacia->id_imperio);
				$html .= "\n<div class='titulo_imperio'>{$imperio->nome}</div>";
			}
			
			$imperio_contato = new imperio($id_diplomacia->id_imperio_contato, true);
			$acordo_comercial = "";
			if ($id_diplomacia->id_imperio_contato == 0) {
				$imperio_contato->nome = $id_diplomacia->nome_npc;
			}
			
			if ($id_diplomacia->acordo_comercial == 1) {
				$acordo_comercial = "; Acordo Comercial";
			}
			
			$html .= "\n<div class='imperio_contato'><span style='subtitulo'>{$imperio_contato->nome}</span>: Primeiro Contato{$acordo_comercial}</div>";
		}
		
		return $html;
	}
	
	/***********************
	function colonization_exibe_constroi_naves($atts = [], $content = null)
	----------------------
	Chamado pelo shortcode [colonization_exibe_constroi_naves]
	$atts = [] - lista de atributos dentro do shortcode 
	***********************/	
	function colonization_exibe_constroi_naves($atts = [], $content = null) {
		$user = wp_get_current_user();
		
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}

		$estilo = "";
		if ($roles != "administrator") {
			$estilo = "style='visibility: hidden;'";
		}
		
		$html = "<h3>Construção de Naves</h3>
		<div id='string_construcao' {$estilo}><label>String da Nave: </label><input type='text' value='' id='input_string_construcao' style='width: 50%; display: inline-block; margin: 5px;'></input><a href='#' onclick='return processa_string(event, this);' style='width: 20%; display: inline-block; margin: 5px;'>Processa a String</a></div>
		<div id='dados'>Tamanho: 2; Velocidade: 5; Alcance: 10; <br>
		PdF Laser: 0/ PdF Torpedo: 0/ PdF Projétil: 0; Blindagem: 0/ Escudos: 0; HP: 20</div>
		<h4>Custos</h4>
		<div id='custos'>Industrializáveis: 2 | Enérgium: 0 | Dillithium: 1 | Duranium: 0</div><br>
		<div id='chassi'>Chassi: 2 - Categoria: Corveta</div>
		<div id='armas'>
		<div id='laser'>Laser: <input type='number' id='qtd_laser' onchange='return calcula_custos(event, this);' value='0' min='0' style='width: 50px;'></input> Mk: <input type='number' id='mk_laser' onchange='return calcula_custos(event, this);' value='1' min='1' max='6' style='width: 50px;'></input></div>
		<div id='torpedo'>Torpedo: <input type='number' id='qtd_torpedo' onchange='return calcula_custos(event, this);' value='0' min='0' style='width: 50px;'></input> Mk: <input type='number' id='mk_torpedo' onchange='return calcula_custos(event, this);' value='1' min='1' max='6' style='width: 50px;'></input> <label>Tricobalto: </label><input type='checkbox' onchange='return calcula_custos(event, this);' id='tricobalto_torpedo' value='1'></input></div>
		<div id='projetil'>Projétil: <input type='number' id='qtd_projetil' onchange='return calcula_custos(event, this);' value='0' min='0' style='width: 50px;'></input> Mk: <input type='number' id='mk_projetil' onchange='return calcula_custos(event, this);' value='1' min='1' max='6' style='width: 50px;'></input></div>
		</div>
		<div>---------------------------------------------------</div>
		<div id='defesas'>
		<div id='blindagem'>Blindagem: Mk: <input type='number' id='mk_blindagem' onchange='return calcula_custos(event, this);' value='0' min='0' max='5' style='width: 50px;'></input> <label>Tritânium: </label><input type='checkbox' onchange='return calcula_custos(event, this);' id='tritanium_blindagem' value='1'></input><label>Neutrônium: </label><input type='checkbox' onchange='return calcula_custos(event, this);' id='neutronium_blindagem' value='1'></input></div>
		<div id='escudos'>Escudos: Mk: <input type='number' id='mk_escudos' onchange='return calcula_custos(event, this);' value='0' min='0' max='5' style='width: 50px;'></input></div>
		</div>
		<div>---------------------------------------------------</div>
		<div id='motores'>
		<div id='impulso'>Motor de Impulso: Mk: <input type='number' id='mk_impulso' onchange='return calcula_custos(event, this);' value='1' max='3' min='1' style='width: 50px;'></input></div>
		<div id='dobra'>Motor de Dobra: Mk: <input type='number' id='mk_dobra' onchange='return calcula_custos(event, this);' value='1' max='5' min='1' style='width: 50px;'></input></div>
		<div id='combustivel'>Células de Combustível: <input type='number' id='qtd_combustivel' onchange='return calcula_custos(event, this);' value='0' min='0' style='width: 50px;'></input></div>
		</div>
		<div>---------------------------------------------------</div>
		<div id='especiais'>
		<div><label>Pesquisa: </label><input type='checkbox' onchange='return calcula_custos(event, this);' id='qtd_pesquisa' value='1'></input><br></div>
		<div><label>Nível da Estação Orbital: </label><input type='number' onchange='return calcula_custos(event, this);' id='nivel_estacao_orbital' value='0' min='0' max='5' style='width: 50px;'></input></div>
		<div><label>Transporte de Tropas: </label><input type='number' onchange='return calcula_custos(event, this);' id='qtd_tropas' value='0' min='0' style='width: 50px;'></input></div>
		<div><label>Compartimento de Bombardeamento Orbital: </label><input type='number' onchange='return calcula_custos(event, this);' id='qtd_bombardeamento' value='0' min='0' style='width: 50px;'></input>Mk: <input type='number' id='mk_bombardeamento' onchange='return calcula_custos(event, this);' value='1' max='3' min='1' style='width: 50px;'></input></div>
		<div><label>Camuflagem: </label><input type='number' id='camuflagem' onchange='return calcula_custos(event, this);' value='0' max='3' min='0' style='width: 50px;'></input></div>
		<div {$estilo}><label>Slots Extra: </label><input type='number' onchange='return calcula_custos(event, this);' id='qtd_slots_extra' value='0' min='0' style='width: 50px;'></input></div>
		<div {$estilo}><label>HP Extra: </label><input type='number' onchange='return calcula_custos(event, this);' id='qtd_hp_extra' value='0' min='0' style='width: 50px;'></input></div>
		</div>
		<div id='texto_especiais'>Especiais: &nbsp;</div>
		<div id='texto_partes_nave' {$estilo}>{\"mk_impulso\":\"1\",\"mk_dobra\":\"1\"}</div>
		";
		
		return $html;
	}

	
	function html_mk($nivel) {
		switch ($nivel) {
			case 1:
			$html_mk = " Mk I";
			break;
			case 2:
			$html_mk = " Mk II";
			break;
			case 3:
			$html_mk = " Mk III";
			break;
			case 4:
			$html_mk = " Mk IV";
			break;
			case 5:
			$html_mk = " Mk V";
			break;
			case 6:
			$html_mk = " Mk VI";
			break;
			case 7:
			$html_mk = " Mk VII";
			break;
			case 8:
			$html_mk = " Mk VIII";
			break;
			default:
			$html_mk = "";
		}
		
		return $html_mk;
	}

}
//Cria o plugin
$plugin_colonization = new colonization();
$menu_admin_colonization = new menu_admin();

//Ganchos de instalação e desinstalação do plugin "Colonization"
register_activation_hook( __FILE__, array($plugin_colonization,'colonization_install'));
register_deactivation_hook( __FILE__, array($plugin_colonization,'colonization_deactivate'));

//Cria o menu do plugin na área administrativa do WordPress
add_action('admin_menu', array($menu_admin_colonization, 'colonization_setup_menu'));
?>