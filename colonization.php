<?php
/**
 * Plugin Name: Colonization
 * Plugin URI: https://github.com/dricdolphin/colonization
 * Description: Plugin de WordPress com o sistema de jogo de Colonization.
 * Version: 1.1.20
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
include_once('includes/transfere_tech.php');
include_once('includes/acoes.php');
include_once('includes/acoes_admin.php');
include_once('includes/turno.php');
include_once('includes/frota.php');
include_once('includes/roda_turno.php');
include_once('includes/reabastece_imperio.php');
include_once('includes/configuracao.php');
include_once('includes/missoes.php');

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
		add_shortcode('colonization_exibe_mapa_naves',array($this,'colonization_exibe_mapa_naves')); //Exibe a transferência de Techs e o histórico
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
		
		$colonization_ajax = new colonization_ajax();
	}

	
	function colonization_muda_icone_topico($topic_id) {
		global $asgarosforum, $wpdb;
		$page_id_forum = new configuracao(1);
		$id_missao = new configuracao(2);
		
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		//if ($roles == "administrator") {
			if ($topic_id == $id_missao->id_post) {
				echo "<div class='icone_topico'><i class='fad fa-route' style='font-size: 32px;'></i></div>";
			}
		//}
	}
	
	function colonization_menu_asgaros() {
		global $asgarosforum, $wpdb;
		$page_id_forum = new configuracao(1);
		$id_missao = new configuracao(2);

		echo "<a href='?page_id={$page_id_forum->id_post}&view=topic&id={$id_missao->id_post}'>Missões</a>";
	}

	function colonization_ajaxurl() {

		echo "<script type='text/javascript'>
           var ajaxurl = '" . admin_url('admin-ajax.php') . "';
         </script>";
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
		}

		if (isset($atts['id'])) {
			$imperio = new imperio($atts['id'],false);
		} else {
			$id_imperio = $wpdb->get_var("SELECT id FROM colonization_imperio WHERE id_jogador={$user->ID}");
			if (empty($id_imperio)) {
				$id_imperio = 0;
			}
			$imperio = new imperio($id_imperio, false);
		}		
		
		$html_lista = "<h3>Lista das Missões do Império</h3>";
		if ($imperio->id == 0 && $roles == "administrator") {
			$lista_id = $wpdb->get_results("
			SELECT id 
			FROM colonization_missao 
			ORDER BY id_imperio, ativo DESC, turno");

			foreach ($lista_id as $id) {
				$missao = new missoes($id->id);
				
				$html_dados = $missao->exibe_missao();
				
				$html_lista .= "
				{$html_dados}<br>";
			}
		} else {
			$lista_id = $wpdb->get_results("
			SELECT id 
			FROM colonization_missao 
			WHERE id_imperio={$imperio->id} 
			OR id_imperio=0
			ORDER BY id_imperio, ativo DESC, turno");

			foreach ($lista_id as $id) {
				$missao = new missoes($id->id);
				
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

		$html = "O Turno {$turno->turno} já começou!";
		
		return $html;
	}


	/******************
	function colonization_exibe_mapa_naves()
	-----------
	Exibe a posição de todas as naves do Império e também nas Colônias
	******************/		
	function colonization_exibe_mapa_naves($atts = [], $content = null) {
		global $asgarosforum, $wpdb;

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}

		if (isset($atts['id'])) {
			$imperio = new imperio($atts['id'],false);
		} else {
			$id_imperio = $wpdb->get_var("SELECT id FROM colonization_imperio WHERE id_jogador={$user->ID}");
			if (empty($id_imperio)) {
				$id_imperio = 0;
			}
			$imperio = new imperio($id_imperio, true);
		}		
		
		if ($imperio->id == 0 && $roles == "administrator") {
			$ids_naves = $wpdb->get_results("SELECT id FROM colonization_imperio_frota ORDER BY id_imperio");
			$ids_estrelas = $wpdb->get_results("
			SELECT DISTINCT ce.id 
			FROM colonization_imperio_colonias AS cic
			JOIN colonization_planeta AS cp
			ON cp.id = cic.id_planeta
			JOIN colonization_estrela AS ce
			ON ce.id = cp.id_estrela
			ORDER BY ISNULL(cic.id_imperio), cic.id_imperio, cic.nome_npc, cic.capital DESC, ce.nome, ce.X, ce.Y, ce.Z
			");
		} else {
			$ids_naves = $wpdb->get_results("SELECT id FROM colonization_imperio_frota WHERE id_imperio = {$imperio->id}");
			$ids_estrelas = $wpdb->get_results("
			SELECT DISTINCT ce.id
			FROM colonization_imperio_colonias AS cic
			JOIN colonization_planeta AS cp
			ON cp.id = cic.id_planeta
			JOIN colonization_estrela AS ce
			ON ce.id = cp.id_estrela
			WHERE cic.id_imperio = {$imperio->id}
			ORDER BY ISNULL(cic.id_imperio), cic.id_imperio, cic.nome_npc, cic.capital DESC, ce.nome, ce.X, ce.Y, ce.Z
			");			
		}
		
		if (isset($atts['apenas_naves'])) {
			$ids_estrelas = [];
		}
		
		$html_estrela = [];
		$exibiu_nave = [];
		foreach ($ids_estrelas as $id_estrela) {
			$estrela = new estrela($id_estrela->id);
			
			$ids_imperios = $wpdb->get_results("
			SELECT DISTINCT cic.id_imperio, cic.nome_npc
			FROM colonization_imperio_colonias AS cic
			JOIN colonization_planeta AS cp
			ON cp.id = cic.id_planeta
			JOIN colonization_estrela AS ce
			ON ce.id = cp.id_estrela
			WHERE cp.id_estrela = {$estrela->id}
			");
			
			$nomes_imperios = "";
			foreach ($ids_imperios as $id_imperio) {
				if ($id_imperio->id_imperio == 0) {
					$nomes_imperios .= "{$id_imperio->nome_npc}; ";
				} else {
					$imperio_estrela = new imperio($id_imperio->id_imperio);
					$nomes_imperios .= "{$imperio_estrela->nome}; ";
				}
			}
			if (!empty($nomes_imperios)) {
				$nomes_imperios = substr($nomes_imperios,0,-2);
				$nomes_imperios = "Colonizado por <span style='text-decoration: underline;'>{$nomes_imperios}</span>";
			}
			
			if (empty($html_estrela[$estrela->id])) {
				$html_estrela[$estrela->id] = "<b>{$estrela->nome}</b> ({$estrela->X};{$estrela->Y};{$estrela->Z}) {$nomes_imperios}<br>";
			}
			
			$ids_naves_na_estrela = $wpdb->get_results("SELECT id FROM colonization_imperio_frota WHERE X={$estrela->X} AND Y={$estrela->Y} AND Z={$estrela->Z}");
			foreach ($ids_naves_na_estrela AS $id_frota) {
				$nave = new frota($id_frota->id);
				$imperio_nave = new imperio($nave->id_imperio);
				
				if ($roles != "administrator") {
					if (($imperio->sensores > $nave->camuflagem) || $nave->visivel == 1 || $nave->camuflagem == 0 || $nave->id_imperio == $imperio->id) {
						$html_estrela[$estrela->id] .= "{$nave->tipo} '{$nave->nome}' ({$imperio_nave->nome}); ";
						$exibiu_nave[$nave->id] = true;
					}
				} else {
					if ($nave->camuflagem > 0 && $nave_na_estrela->visivel == 0) {
						$html_estrela[$estrela->id] .= "<i>{$nave->tipo} '{$nave->nome}' ({$imperio_nave->nome});</i> ";
						$exibiu_nave[$nave->id] = true;
					} else {
						$html_estrela[$estrela->id] .= "{$nave->tipo} '{$nave->nome}' ({$imperio_nave->nome}); ";
						$exibiu_nave[$nave->id] = true;
					}
				}
			}
		}
		
		foreach ($ids_naves as $id_nave) {
			$nave = new frota($id_nave->id);
			$imperio_nave = new imperio($nave->id_imperio,true);
			
			$id_estrela = $wpdb->get_var("SELECT id FROM colonization_estrela WHERE X={$nave->X} AND Y={$nave->Y} AND Z={$nave->Z}");
			$estrela = new estrela($id_estrela);
			
			$ids_imperios = $wpdb->get_results("
			SELECT DISTINCT cic.id_imperio, cic.nome_npc
			FROM colonization_imperio_colonias AS cic
			JOIN colonization_planeta AS cp
			ON cp.id = cic.id_planeta
			JOIN colonization_estrela AS ce
			ON ce.id = cp.id_estrela
			WHERE cp.id_estrela = {$estrela->id}
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
			
			if (empty($html_estrela[$estrela->id])) {
				$html_estrela[$estrela->id] = "<b>{$estrela->nome}</b> ({$estrela->X};{$estrela->Y};{$estrela->Z}) {$nomes_imperios}<br>";
			}
			
			$ids_naves_na_estrela = $wpdb->get_results("SELECT id FROM colonization_imperio_frota WHERE X={$estrela->X} AND Y={$estrela->Y} AND Z={$estrela->Z}");
			foreach ($ids_naves_na_estrela AS $id_frota) {
				$nave_na_estrela = new frota($id_frota->id);
				$imperio_nave_na_estrela = new imperio($nave_na_estrela->id_imperio,true);

				if (empty($exibiu_nave[$nave_na_estrela->id]) || $exibiu_nave[$nave_na_estrela->id] == false) {
					if ($roles != "administrator") {
						if (($imperio->sensores > $nave_na_estrela->camuflagem) || $nave_na_estrela->visivel == 1 || $nave_na_estrela->camuflagem == 0 || ($nave_na_estrela->id_imperio == $imperio->id)) {
							$html_estrela[$estrela->id] .= "{$nave_na_estrela->tipo} '{$nave_na_estrela->nome}' ({$imperio_nave_na_estrela->nome}); ";
							$exibiu_nave[$nave_na_estrela->id] = true;
						}
					} else {
						if ($nave_na_estrela->camuflagem > 0 && $nave_na_estrela->visivel == 0) {
							$html_estrela[$estrela->id] .= "<i>{$nave_na_estrela->tipo} '{$nave_na_estrela->nome}' ({$imperio_nave_na_estrela->nome});</i> ";
							$exibiu_nave[$nave_na_estrela->id] = true;
						} else {
							$html_estrela[$estrela->id] .= "{$nave_na_estrela->tipo} '{$nave_na_estrela->nome}' ({$imperio_nave_na_estrela->nome}); ";
							$exibiu_nave[$nave_na_estrela->id] = true;
						}
					}
				}				
			}
		}
		
		$html_final = "";
		$chaves = implode(",",array_keys($html_estrela));
		$ids_estrelas = $wpdb->get_results("
		SELECT DISTINCT ce.id 
		FROM colonization_estrela AS ce
		LEFT JOIN colonization_planeta AS cp
		ON cp.id_estrela = ce.id
		LEFT JOIN colonization_imperio_colonias AS cic
		ON cic.id_planeta = cp.id
		WHERE ce.id IN ({$chaves})
		ORDER BY ISNULL(cic.id_imperio), cic.id_imperio, cic.nome_npc, cic.capital DESC, ce.nome, ce.X, ce.Y, ce.Z
		");

		$par_impar = "background-color: #DDD;";
		foreach ($ids_estrelas as $chave) {
			if ($par_impar == "background-color: #DDD;") {
				$par_impar = "background-color: #EEE;";
			} else {
				$par_impar = "background-color: #DDD;";
			}
			$html_final .= "<div style='margin-bottom: 5px;{$par_impar}'>".$html_estrela[$chave->id]."</div>";
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
			$naves_pendentes = $wpdb->get_results("SELECT id FROM colonization_imperio_frota WHERE id_estrela_destino != 0 ORDER BY id_imperio");
			
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
		}
		
		if ($roles != "administrator") {
			$id_imperio = $wpdb->get_var("SELECT id FROM colonization_imperio WHERE id_jogador={$user->ID}");
			$imperio = new imperio($id_imperio, true);
		} else {
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
	******************/	
	function colonization_exibe_tech_transfere_pendente() {
		global $asgarosforum, $wpdb;
		
		$user = wp_get_current_user();
		$ids_pendentes = [];
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		if ($roles != "administrator") {
			$id_imperio = $wpdb->get_var("SELECT id FROM colonization_imperio WHERE id_jogador={$user->ID}");
			$imperio = new imperio($id_imperio, true);
		}
		
		if (!empty($imperio->id)) {
			$ids_pendentes = $wpdb->get_results("SELECT id FROM colonization_imperio_transfere_techs WHERE id_imperio_destino={$imperio->id} AND processado=0");
		}
		
		foreach ($ids_pendentes as $id) {
			$transfere_tech = new transfere_tech($id->id);
			
			$notice = $transfere_tech->exibe_autoriza();
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
		}
		
		if (isset($atts['id'])) {
			$imperio = new imperio($atts['id'],false);
			$roles = "";
		} else {
			$imperio = new imperio(0,false);
		}
		
		$html_lista_imperios = "<select data-atributo='id_imperio_destino' style='width: 100%'>";
		$resultados = $wpdb->get_results("SELECT id, nome FROM colonization_imperio");
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
		if ($roles == 'administrator') {
			$input_imperio_origem = "<div data-atributo='nome_imperio' data-id-selecionado='' data-valor-original=''><select data-atributo='id_imperio_origem' style='width: 100%; margin-bottom: 5px;' onchange='return libera_npc(event, this);'></div>";
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
		<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_imperio_transfere_techs' style='width: 700px;'>
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
	function colonization_exibe_techtree()
	-----------
	Exibe a Árvore de Technologia
	******************/	
	function colonization_exibe_techtree($atts = [], $content = null) {
		global $wpdb;
		$tech = new tech();
		
		if (!empty($atts['super'])) {
			$tech = new tech();

			$techs = $tech->query_tech();
			
		} elseif (!empty($atts['id'])) {
			$imperio = new imperio($atts['id']);
			
			$techs = $tech->query_tech("",$imperio->id);
		} else {
			
			$techs = $tech->query_tech(" AND publica = 1");			
		}
		
		
		$html_tech = [];

		foreach ($techs AS $id) {
			$tech = new tech($id->id);
			if ($id->custo_pago != 0) {
				$html_custo_pago = " [{$id->custo_pago}/{$tech->custo}]";
				$style_pago = "style='font-style: italic;'";
			} else {
				$html_custo_pago = "";
				$style_pago = "";
			}
			if ($tech->nivel == 1) {
				$html_tech[$id->id] = "
				<div class='wrapper_principal'>
					<div class='wrapper_nivel'>
						<div class='wrapper_tech'>
							<div class='tech tooltip' $style_pago>{$tech->nome}{$html_custo_pago}
							<span class='tooltiptext'>{$tech->descricao}</span>
							</div>
						</div>";
			}
			
			if (!empty($tech->id_tech_parent)) {
				$ids_tech_parent = [];
				$nivel = $tech->nivel-1;
				$ids_tech_parent[$nivel] = explode(";",$tech->id_tech_parent);
				
				while ($nivel > 1) {
					foreach ($ids_tech_parent[$nivel] as $chave => $id_tech_parent) {
						$tech_parent = new tech($id_tech_parent);
						$nivel_anterior = $tech_parent->nivel-1;
						if (!empty($tech_parent->id_tech_parent)) {//Tem um pré-requisito, que é de nível inferior
							if (empty($ids_tech_parent[$nivel_anterior])) {
								$ids_tech_parent[$nivel_anterior] = explode(";",$tech_parent->id_tech_parent);
							} else {
								$ids_tech_parent[$nivel_anterior] = array_merge($ids_tech_parent[$nivel_anterior],explode(";",$tech_parent->id_tech_parent));
							}
						}
						$nivel = $nivel_anterior;
					}
				}

				$nivel = 1;
				if (empty($ids_tech_parent[1])) {
					echo "id_tech:".$tech->id."<br>";
				}
					$ids_tech_parent = $ids_tech_parent[1];
				
				foreach ($ids_tech_parent as $chave => $id_tech_parent) {
					$tech_parent = new tech($id_tech_parent);
					
					if (!empty($html_tech[$tech_parent->id])) {
						$wrapper_nivel = "<div class='wrapper_nivel'>";
						if (!empty($nivel_preenchido[$tech_parent->id][$tech->nivel]) && $tech->nivel > 1) {
							$wrapper_nivel = "";
							$html_tech[$tech_parent->id] = substr($html_tech[$tech_parent->id],0,-6); //Reabre o DIV do "wrapper_nivel"
						}
						$html_tech[$tech_parent->id] .= "
					{$wrapper_nivel}
					<div class='fas fa-long-arrow-alt-right wrapper_tech' style='padding-top: 12px;'>&nbsp;</div>
						<div class='wrapper_tech'>
							<div class='tech tooltip' $style_pago>{$tech->nome}{$html_custo_pago}
							<span class='tooltiptext'>{$tech->descricao}</span>
							</div>
						</div>";
					
					$nivel_preenchido[$tech_parent->id][$tech->nivel] = true;
					}
				}
			}
			
			if ($tech->lista_requisitos != '') {
				foreach ($tech->id_tech_requisito AS $chave => $id_tech_requisito) {
					$tech_requisito = new tech($id_tech_requisito);
					if ($tech->nivel == 1) {
						$html_tech[$tech->id] .= "
						<div class='fas fa-ellipsis-v tech tech_requisito_ellipsis' >&nbsp;</div>
						<div class='tech tech_requisito tooltip'>{$tech_requisito->nome}
							<span class='tooltiptext'>{$tech_requisito->descricao}</span>
						</div>";
					} else {
						foreach ($ids_tech_parent as $chave => $id_tech_parent) {
							$tech_parent = new tech($id_tech_parent);
							if (!empty($html_tech[$tech_parent->id])) {						
								$html_tech[$tech_parent->id] .= "
						<div class='fas fa-ellipsis-v tech tech_requisito_ellipsis' >&nbsp;</div>
						<div class='tech tech_requisito tooltip'>{$tech_requisito->nome}
							<span class='tooltiptext'>{$tech_requisito->descricao}</span>
						</div>";
							}
						}
					}
				}
			}
			
			if ($tech->nivel == 1) {
				$html_tech[$tech->id] .= "</div>";
			} else {
				foreach ($ids_tech_parent as $chave => $id_tech_parent) {
					$tech_parent = new tech($id_tech_parent);
					if (!empty($html_tech[$tech_parent->id])) {						
						
						$html_tech[$tech_parent->id] .= "</div>";
					}
				}
			}
	
		}

		$html = "<div style='background-color: #FFFFFF; overflow-x: visible; max-width: 5000px; margin-right: -50%;'>";
		$belica = 0;
		foreach ($html_tech as $chave => $html_valor) {
			$tech = new tech($chave);
			if ($belica == 0 and $tech->belica == 1) {
				$html .= "<br><div class='wrapper_principal'><span style='font-weight: bold'>Tecnologias Bélicas</span></div>";
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
		
		$user = wp_get_current_user();
		$id_estrela_capital = "";
		$turno = new turno();
		$div_imperios = "";
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}

		if ($roles != "administrator") {
			$id_imperio = $wpdb->get_var("SELECT id FROM colonization_imperio WHERE id_jogador={$user->ID}");
			$imperios[0] = new imperio($id_imperio, true);
			$colonias = $wpdb->get_results("SELECT id FROM colonization_imperio_colonias WHERE id_imperio={$imperios[0]->id} AND turno={$turno->turno} ORDER BY ID asc");
			$colonia = new colonia($colonias[0]->id);
			$planeta = new planeta($colonia->id_planeta);
			$id_estrela_capital = $planeta->id_estrela;
		} else {
			$id_imperios = $wpdb->get_results("SELECT id FROM colonization_imperio ORDER BY nome");
			$imperios = [];
			$div_imperios = "
			<div id='div_imperios' style='width: 300px;'>&nbsp;</div>
			";
			foreach ($id_imperios as $chave => $id) {
				$imperios[$chave] = new imperio ($id->id);
			}
			$colonias = $wpdb->get_results("SELECT id FROM colonization_imperio_colonias WHERE id_imperio={$imperios[0]->id} AND turno={$turno->turno} ORDER BY ID asc");
			$colonia = new colonia($colonias[0]->id);
			$planeta = new planeta($colonia->id_planeta);
			$id_estrela_capital = $planeta->id_estrela;
		}

		
		//Popula o JavaScript
		$html_javascript = "
var lista_estrelas_colonia=[];
var lista_estrelas_reabastece=[];
var estrela_capital=[];
var id_imperio_atual = {$imperios[0]->id};
				";
				
		foreach ($imperios as $imperio) {
			$colonias = $wpdb->get_results("SELECT id FROM colonization_imperio_colonias WHERE id_imperio={$imperio->id} AND turno={$turno->turno} ORDER BY ID asc");

			$html_javascript .= "
			lista_estrelas_colonia[{$imperio->id}]=[];
			lista_estrelas_reabastece[{$imperio->id}]=[];
			";

			foreach ($colonias as $id_colonia) {
				$colonia = new colonia($id_colonia->id);
				$planeta = new planeta($colonia->id_planeta);
				
				$html_javascript .= "
				lista_estrelas_colonia[{$imperio->id}][{$planeta->id_estrela}]={$planeta->id_estrela};";
				
				if ($colonia->capital == 1) {
					$html_javascript .= "
					estrela_capital[{$imperio->id}]={$planeta->id_estrela};";
				}
			}
			
			$reabastece = $wpdb->get_results("SELECT id_estrela FROM colonization_imperio_abastecimento WHERE id_imperio={$imperio->id}");
			foreach ($reabastece as $id_estrela) {
				$html_javascript .= "lista_estrelas_reabastece[{$imperio->id}][{$id_estrela->id_estrela}]={$id_estrela->id_estrela};\n
				";
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
		
		$html = "<div id='barra_recursos' class='nojq'>";
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
			if ($roles == "administrator") {
				$html_recursos .= "<b>{$imperio->nome}{$imperio->icones_html}</b> - ";
			} else {
				$html_recursos .= "<b>{$imperio->nome}{$imperio->icones_html}</b> - ";
			}
			
			$recursos_atuais = $imperio->exibe_recursos_atuais();
			$recursos_atuais = substr($recursos_atuais,19); //Remove o cabeçalho
			$html_recursos .= $recursos_atuais."<br>";
		}
		
		$html_recursos = substr($html_recursos,0,-4);
		
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
		}
		
			
		if ($user_roles == "administrator") {
			$prestigio = "&infin;";
		} else {
			$prestigio = $wpdb->get_var("SELECT prestigio FROM colonization_imperio WHERE id_jogador={$author_id}");
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
		
		$resultados = $wpdb->get_results("SELECT id FROM colonization_imperio_colonias WHERE id_imperio={$imperio->id} AND turno={$turno->turno}");
		
		$html = "
		<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_acoes_turno'>
		<thead>
		<tr><th style='width: 30%;'>Planeta (X;Y;Z)</th><th>Recursos</th></tr>
		</thead>
		<tbody>";
		
		foreach ($resultados as $resultado) {
			$colonia = new colonia($resultado->id, $turno->turno);
			
			$lista_recursos = $colonia->exibe_recursos_colonia();
			
			$html .= "<tr><td>{$colonia->planeta->nome} - {$colonia->estrela->X};{$colonia->estrela->Y};{$colonia->estrela->Z} / {$colonia->planeta->posicao}</td>
			<td>{$lista_recursos}</td>
			</tr>
			";
		
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
		<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_acoes_turno' style='width: 500px;'>
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

		if (isset($atts['id'])) {
			$imperio = new imperio($atts['id'],false,$turno);
		} else {
			$imperio = new imperio(0,false,$turno);
		}
		
		$imperio_acoes = new acoes($imperio->id,$turno);
		
		$lista_colonias = $imperio->exibe_lista_colonias();
		$recursos_atuais = $imperio->exibe_recursos_atuais();
		$recursos_produzidos = $imperio_acoes->exibe_recursos_produzidos();
		$recursos_consumidos = $imperio_acoes->exibe_recursos_consumidos();
		$balanco_recursos = $imperio_acoes->exibe_recursos_balanco();
		$html_frota = "";
	
		$ids_frota = $wpdb->get_results("SELECT id FROM colonization_imperio_frota WHERE id_imperio = {$imperio->id} ORDER BY nivel_estacao_orbital DESC");
		
		$naves_por_linha = 0;
		foreach ($ids_frota as $ids) {
			$nave = new frota($ids->id);
			
			$id_estrela = $wpdb->get_var("SELECT id FROM colonization_estrela WHERE X={$nave->X} AND Y={$nave->Y} AND Z={$nave->Z}");
			$pesquisa_anterior = $wpdb->get_var("SELECT id FROM colonization_imperio_historico_pesquisa  WHERE id_imperio={$imperio->id} AND id_estrela={$id_estrela}");
			
			$html_pesquisa_nave = "";
			if (empty($pesquisa_anterior) && $nave->pesquisa==1) {
				$html_pesquisa_nave = "<div class='fas fa-search tooltip'><span class='tooltiptext'>Sistema sendo pesquisado</span></div>";	
			}
			
			$html_estacao_orbital = "";
			if ($nave->nivel_estacao_orbital > 0) {
				$html_estacao_orbital = "<div class='fas fa-drone tooltip'><span class='tooltiptext'>Estação Orbital</span></div>";	
			}
			
			$naves_por_linha++;
			$link_visivel = "";
			if ($nave->visivel == 0 && $nave->camuflagem > 0) {
				$link_visivel = "<a href='#' onclick='return nave_visivel(this,event,{$nave->id});'><div class='fad fa-hood-cloak tooltip'><span class='tooltiptext'>Sistema sendo pesquisado</span></div></a>";
			}
			$html_frota .= "{$html_estacao_orbital}<b>{$nave->nome}</b> {$link_visivel} {$html_pesquisa_nave}{$nave->estrela->nome} ({$nave->X};{$nave->Y};{$nave->Z}); ";
			if ($naves_por_linha == 2) {
				$html_frota .= "<br>";
			}
		}
		
		$html_lista	= "
		<div><h4>COLONIZATION - Ações do Império '{$imperio->nome}' - Turno {$turno}</h4></div>
		<div id='lista_colonias_imperio_{$imperio->id}'>{$lista_colonias}</div><br>
		<div id='recursos_atuais_imperio_{$imperio->id}' >{$recursos_atuais}</div><br>
		<div id='recursos_produzidos_imperio_{$imperio->id}' style='display: none;'>{$recursos_produzidos}</div>
		<div id='recursos_consumidos_imperio_{$imperio->id}' style='display: none;'>{$recursos_consumidos}</div>
		<div id='recursos_balanco_imperio_{$imperio->id}'>{$balanco_recursos}</div><br>
		<div><b>Frota do Império</b></div>
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
		
		$html_lista .= $imperio_acoes->lista_dados(false); //Mostra somente o Turno atual
		
		$html_lista .= "</tbody>
		</table>";		
		
		return $html_lista;
	}

	function colonization_exibe_lista_imperios_pontuacao($atts = [], $content = null) {
		global $wpdb;
		
		$estrelas = $wpdb->get_results("SELECT nome, tipo, X, Y, Z, ROUND(X+Z*(SQRT(2)/2),2) AS X3D, ROUND(Y+Z*(SQRT(2)/2),2) AS Y3D FROM colonization_estrela");

		$html_lista = "    <script type='text/javascript'>
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        
		var data = new google.visualization.DataTable();
        data.addColumn('number', 'X');
        data.addColumn('number', 'Y');
		data.addColumn({type: 'string', role: 'tooltip'});
		data.addColumn({type: 'string', role: 'style'});

        data.addRows([";
		
		
		$html_estrela = "";
	    
		foreach ($estrelas as $estrela) {
			//,'{$estrela->nome} ({$estrela->X},{$estrela->Y},{$estrela->Z})'
			if (stripos($estrela->tipo,"amarela") !== false) {
				$estilo = 'point { size: 4; fill-color: #FFFF00; }';
			} elseif (stripos($estrela->tipo,"branca") !== false) {
				$estilo = 'point { size: 4; fill-color: #FFFFFF; }';
			} elseif (stripos($estrela->tipo,"vermelha") !== false) {
				$estilo = 'point { size: 4; fill-color: #FF0000; }';
			} elseif (stripos($estrela->tipo,"azul") !== false) {
				$estilo = 'point { size: 4; fill-color: #F0F0FF; }';
			} elseif (stripos($estrela->tipo,"laranja") !== false) {
				$estilo = 'point { size: 4; fill-color: #FFA500; }';				
			} else {
				$estilo = 'point { size: 4; fill-color: #DDDDDD; }';
			}
			
			$html_estrela	.= "[{$estrela->X3D},{$estrela->Y3D},'{$estrela->nome} ({$estrela->X},{$estrela->Y},{$estrela->Z})','{$estilo}'],";
		}
		
		$html_estrela = substr($html_estrela,0,-1);
		
		$html_lista .= $html_estrela;
		
		$html_lista	.= "]);
        var options = {
          title: 'Lista das Estrelas',
		  chartArea: {backgroundColor: '#111111'},
		  hAxis: {title: 'X', minValue: 0, maxValue: 35, minorGridlines: {count: 0}},
          vAxis: {title: 'Y', minValue: 0, maxValue: 35, minorGridlines: {count: 0}},
          legend: 'none'
        };

        var chart = new google.visualization.ScatterChart(document.getElementById('chart_div'));
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

		$html_lista = "    <script type='text/javascript'>
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        
		var data = new google.visualization.DataTable();
        data.addColumn('number', 'X');
        data.addColumn('number', 'Y');
		data.addColumn({type: 'string', role: 'tooltip'});
		data.addColumn({type: 'string', role: 'style'});

        data.addRows([";
		
		
		$html_estrela = "";
	    
		foreach ($estrelas as $estrela) {
			//,'{$estrela->nome} ({$estrela->X},{$estrela->Y},{$estrela->Z})'
			if (stripos($estrela->tipo,"amarela") !== false) {
				$estilo = 'point { size: 4; fill-color: #FFFF00; }';
			} elseif (stripos($estrela->tipo,"branca") !== false) {
				$estilo = 'point { size: 4; fill-color: #FFFFFF; }';
			} elseif (stripos($estrela->tipo,"vermelha") !== false) {
				$estilo = 'point { size: 4; fill-color: #FF0000; }';
			} elseif (stripos($estrela->tipo,"azul") !== false) {
				$estilo = 'point { size: 4; fill-color: #F0F0FF; }';
			} elseif (stripos($estrela->tipo,"laranja") !== false) {
				$estilo = 'point { size: 4; fill-color: #FFA500; }';				
			} else {
				$estilo = 'point { size: 4; fill-color: #DDDDDD; }';
			}
			
			$html_estrela	.= "[{$estrela->X3D},{$estrela->Y3D},'{$estrela->nome} ({$estrela->X},{$estrela->Y},{$estrela->Z})','{$estilo}'],";
		}
		
		$html_estrela = substr($html_estrela,0,-1);
		
		$html_lista .= $html_estrela;
		
		$html_lista	.= "]);
        var options = {
          title: 'Lista das Estrelas',
		  chartArea: {backgroundColor: '#111111'},
		  hAxis: {title: 'X', minValue: 0, maxValue: 35, minorGridlines: {count: 0}},
          vAxis: {title: 'Y', minValue: 0, maxValue: 35, minorGridlines: {count: 0}},
          legend: 'none'
        };

        var chart = new google.visualization.ScatterChart(document.getElementById('chart_div'));
	    chart.draw(data, options);
      }
    </script>
	<div id='chart_div' style='width: 800px; height: 500px;'></div>";
	
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
		
		$turno = new turno();
		
		$html = "
		<div style='width: auto; height: auto;'>
		<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_imperio_frota'>
		<thead>
		<tr><th style='width: 30%;'>Nome</th><th style='width: 22%;'>Posição</th><th style='width: 30%;'>Atributos</th><th style='width: 18%;'>Despachar Nave</th></tr>
		</thead>
		";
		
		$lista_frota_imperio = $wpdb->get_results("SELECT id FROM colonization_imperio_frota WHERE id_imperio={$imperio->id}");
		
		$index = 0;
		$html_id_estrela_destino = "";
		foreach ($lista_frota_imperio as $id) {
			$frota = new frota($id->id);
			
			$html_id_estrela_destino .= "
			id_estrela_destino[{$index}] = {$frota->id_estrela_destino};
			id_estrela_atual[{$index}] = {$frota->estrela->id};";
			$index++;
			$html .= "<tr>". $frota->exibe_frota() . "</tr>";
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
				
		$colonias = $wpdb->get_results("SELECT id FROM colonization_imperio_colonias WHERE id_imperio={$imperio->id} AND turno={$turno->turno} ORDER BY ID asc");
		$html_javascript .= "
		lista_estrelas_colonia[{$imperio->id}]=[];
		lista_estrelas_reabastece[{$imperio->id}]=[];
		";
		foreach ($colonias as $id_colonia) {
			$colonia = new colonia($id_colonia->id);
			$planeta = new planeta($colonia->id_planeta);

				$html_javascript .= "
				lista_estrelas_colonia[{$imperio->id}][{$planeta->id_estrela}]={$planeta->id_estrela};";
				
				if ($colonia->capital == 1) {
				$html_javascript .= "
				estrela_capital[{$imperio->id}]={$planeta->id_estrela};";
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
		var html_lista_estrelas = lista_estrelas_html();
		
		function popula_selects() {
			let selects = document.getElementsByTagName('SELECT');
			
			for (let index=0; index < selects.length; index++) {
				if (selects[index].getAttribute('data-atributo') == 'id_estrela') {
					let alcance_nave = selects[index].getAttribute('data-alcance');
					let alcance_estendido = 2;
					let reabastece = true;
					let estrela_atual = id_estrela_atual[index]
					let estrelas_destino = array_estrelas(alcance_nave,alcance_estendido,reabastece,estrela_atual);
					console.log('Index: '+index+'; '+estrela_atual);
					let alcance_local = selects[index].getAttribute('data-alcance-local');
					reabastece = false;
					alcance_estendido = 1;
					let estrelas_destino_local = array_estrelas(alcance_local,alcance_estendido,reabastece,estrela_atual);
					
					mapped_estrelas_destino_local = estrelas_destino_local.map(function(el, i) {
						return { index: i, value: el };
					});

					mapped_estrelas_destino_local.forEach(
					function(valor_estrelas_imperio, id_estrela_origem, mapa_estrelas_imperio) {
						estrelas_destino[id_estrela_origem] = true;
					});						
					
					var mapped_estrelas_destino = estrelas_destino.map(function(el, i) {
						return { index: i, value: el, id_estrela: i, nome_estrela: lista_nome_estrela[i], posicao_estrela: ' ('+lista_x_estrela[i]+';'+lista_y_estrela[i]+';'+lista_z_estrela[i]+')' };
					});	
					
					mapped_estrelas_destino.sort(function(firstEl, secondEl) {
						if (firstEl.nome_estrela.toLowerCase() < secondEl.nome_estrela.toLowerCase()) {
						return -1;
						}
						if (firstEl.nome_estrela.toLowerCase() > secondEl.nome_estrela.toLowerCase()) {
						return 1;
						}
						// a must be equal to b
						return 0;
					});
					
					html_lista = '';
					mapped_estrelas_destino.forEach(function(valor_destino, chave_destino, mapa_destino) {
						let selecionado = '';
						if (id_estrela_destino[index] == 0) {
							id_estrela_destino[index] = estrela_capital[id_imperio_atual];
						}

						if (valor_destino.id_estrela == id_estrela_destino[index]) {
							selecionado = 'selected';
						}
						html_lista = html_lista + '<option value=\"'+valor_destino.id_estrela+'\" '+selecionado+'>'+ valor_destino.nome_estrela +' '+ valor_destino.posicao_estrela +'</option>';
						
						//distancia[chave_destino] = true;
					});
					
					selects[index].innerHTML = html_lista;
				}
			}
		}
		
		let popula = popula_selects();
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
			AND cic.turno={$turno->turno}");
			
			$where_id = "WHERE id != {$imperio->id}";
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
		<div id='dados'>Tamanho: 1; Velocidade: 5; Alcance: 0; <br>
		PdF Laser: 0/ PdF Torpedo: 0/ PdF Projétil: 0; Blindagem: 0/ Escudos: 0; HP: 0</div>
		<h4>Custos</h4>
		<div id='custos'>Industrializáveis: 2 | Enérgium: 0 | Dillithium: 0 | Duranium: 0</div><br>
		<div id='chassi'>Chassi: 1 - Categoria: Corveta</div>
		<div id='laser'>Laser: <input type='number' id='qtd_laser' onchange='return calcula_custos(event, this);' value='0' min='0' style='width: 50px;'></input> Mk: <input type='number' id='mk_laser' onchange='return calcula_custos(event, this);' value='1' min='1' max='3' style='width: 50px;'></input></div>
		<div id='torpedo'>Torpedo: <input type='number' id='qtd_torpedo' onchange='return calcula_custos(event, this);' value='0' min='0' style='width: 50px;'></input> Mk: <input type='number' id='mk_torpedo' onchange='return calcula_custos(event, this);' value='1' min='1' max='3' style='width: 50px;'></input></div>
		<div id='projetil'>Projétil: <input type='number' id='qtd_projetil' onchange='return calcula_custos(event, this);' value='0' min='0' style='width: 50px;'></input> Mk: <input type='number' id='mk_projetil' onchange='return calcula_custos(event, this);' value='1' min='1' max='3' style='width: 50px;'></input></div>
		<div>---------------------------------------------------</div>
		<div id='blindagem'>Blindagem: <input type='number' id='qtd_blindagem' onchange='return calcula_custos(event, this);' value='0' min='0' style='width: 50px;'></input> Mk: <input type='number' id='mk_blindagem' onchange='return calcula_custos(event, this);' value='1' min='1' max='3' style='width: 50px;'></input></div>
		<div id='escudos'>Escudos: <input type='number' id='qtd_escudos' onchange='return calcula_custos(event, this);' value='0' min='0' style='width: 50px;'></input> Mk: <input type='number' id='mk_escudos' onchange='return calcula_custos(event, this);' value='1' min='1' max='3' style='width: 50px;'></input></div>
		<div>---------------------------------------------------</div>
		<div id='impulso'>Motor de Impulso: <input type='number' id='qtd_impulso' onchange='return calcula_custos(event, this);' value='1' min='1' style='width: 50px;'></input> Mk: <input type='number' id='mk_impulso' onchange='return calcula_custos(event, this);' value='1' max='3' min='1' style='width: 50px;'></input></div>
		<div id='dobra'>Motor de Dobra: <input type='number' id='qtd_dobra' onchange='return calcula_custos(event, this);' value='0' min='0' max='3' style='width: 50px;'></input> Mk: <input type='number' id='mk_dobra' onchange='return calcula_custos(event, this);' value='1' max='3' min='1' style='width: 50px;'></input></div>
		<div id='combustivel'>Células de Combustível: <input type='number' id='qtd_combustivel' onchange='return calcula_custos(event, this);' value='0' min='0' style='width: 50px;'></input></div>
		<div>---------------------------------------------------</div>
		<div id='especiais'>
		<label>Pesquisa: </label><input type='checkbox' onchange='return calcula_custos(event, this);' id='qtd_pesquisa' value='1'></input><br>
		<label>Nível da Estação Orbital: </label><input type='number' onchange='return calcula_custos(event, this);' id='qtd_estacao_orbital' value='0' min='0' max='5' style='width: 50px;'></input><br>
		<label>Transporte de Tropas: </label><input type='number' onchange='return calcula_custos(event, this);' id='qtd_tropas' value='0' min='0' style='width: 50px;'></input><br>
		<label>Compartimento de Bombardeamento Orbital: </label><input type='number' onchange='return calcula_custos(event, this);' id='qtd_bombas' value='0' min='0' style='width: 50px;'></input><br>
		<label>Slots Extra: </label><input type='number' onchange='return calcula_custos(event, this);' id='qtd_slots_extra' value='0' min='0' style='width: 50px;'></input><br>
		</div>
		<div id='texto_especiais'>Especiais: &nbsp;</div>
		<div id='texto_partes_nave' {$estilo}>0=1;0=1;0=1;0=1;0=1;1=1;0=1;0=1;0=1;0=1;0=1;0=1;0=1</div>
		";
		
		return $html;
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