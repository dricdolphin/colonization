<?php
/**
 * Plugin Name: Colonization
 * Plugin URI: https://github.com/dricdolphin/colonization
 * Description: Plugin de WordPress com o sistema de jogo de Colonization.
 * Version: 1.1.7
 * Author: dricdolphin
 * Author URI: https://dricdolphin.com
 */

//Inclui os arquivos necessários para o sistema "Colonization"
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
include_once('js/listas_js.php');

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
		add_shortcode('colonization_exibe_recursos_colonias_imperio',array($this,'colonization_exibe_recursos_colonias_imperio')); //Exibe os dados das Colônias do Império
		add_shortcode('colonization_exibe_acoes_imperio',array($this,'colonization_exibe_acoes_imperio')); //Exibe a lista de ações do Império
		add_shortcode('colonization_exibe_mapa_estelar',array($this,'colonization_exibe_mapa_estelar')); //Exibe o Mapa Estelar
		add_shortcode('colonization_exibe_frota_imperio',array($this,'colonization_exibe_frota_imperio')); //Exibe a Frota de um Império
		add_shortcode('colonization_exibe_techs_imperio',array($this,'colonization_exibe_techs_imperio')); //Exibe as Techs de um Império
		add_shortcode('colonization_exibe_constroi_naves',array($this,'colonization_exibe_constroi_naves')); //Exibe uma página de construção de naves
		add_shortcode('colonization_exibe_distancia_estrelas',array($this,'colonization_exibe_distancia_estrelas')); //Exibe uma página com a distância entre duas estrelas
		add_shortcode('colonization_exibe_hyperdrive',array($this,'colonization_exibe_hyperdrive')); //Exibe uma página com a distância entre duas estrelas via Hyperdrive
		add_shortcode('colonization_exibe_techtree',array($this,'colonization_exibe_techtree')); //Exibe a Tech Tree do Colonization
		add_shortcode('colonization_exibe_tech_transfere',array($this,'colonization_exibe_tech_transfere')); //Exibe a transferência de Techs e o histórico

		add_action('asgarosforum_after_post_author', array($this,'colonization_exibe_prestigio'), 10, 2);
		add_action('wp_body_open', array($this,'colonization_exibe_barra_recursos')); //Adiciona a barra de recursos de cada Império
		add_action('asgarosforum_wp_head', array($this,'colonization_exibe_tech_transfere_pendente')); //Adiciona a barra de recursos de cada Império
	}
	
	/******************
	function colonization_exibe_tech_transfere_pendente()
	-----------
	Exibe no painel principal se existem transferências de Tech pendentes
	******************/	
	function colonization_exibe_tech_transfere_pendente() {
		global $asgarosforum, $wpdb;
		
		$user = wp_get_current_user();
		if (!empty($user->ID)) {
			$id_imperio = $wpdb->get_var("SELECT id FROM colonization_imperio WHERE id_jogador={$user->ID}");
			$imperio = new imperio($id_imperio, true);
			$roles = $user->roles[0];
			$ids_pendentes = [];
			
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
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		} else {
			$roles = "";
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
		if ($roles == 'administrator') {
		
			$input_imperio_origem = "<select data-atributo='id_imperio_origem' data-ajax='true' style='width: 100%'>";
			$resultados = $wpdb->get_results("SELECT id, nome FROM colonization_imperio");
			foreach ($resultados as $resultado) {
				$input_imperio_origem .= "<option value='{$resultado->id}'>{$resultado->nome}</option>";
			}
			$input_imperio_origem .= "</select>";
		}
		
		$techs = new tech();
		if ($roles == 'administrator') {
			$resultados = $techs->query_tech();
			/***
			$resultados = $wpdb->get_results("SELECT ct.id, ct.nome 
			FROM colonization_tech AS ct
			ORDER BY ct.nivel, ct.belica, ct.lista_requisitos, ct.nome");
			//***/
		} else {
			$resultados = $techs->query_tech("AND cit.custo_pago=0",$imperio->id);
			/***
			$resultados = $wpdb->get_results("SELECT ct.id, ct.nome 
			FROM colonization_imperio_techs AS cit
			JOIN colonization_tech AS ct
			ON ct.id = cit.id_tech
			WHERE cit.id_imperio={$imperio->id} AND custo_pago=0
			ORDER BY ct.nivel, ct.belica, ct.lista_requisitos, ct.nome
			");
			//***/
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
			
			/***
			$techs = $wpdb->get_results("SELECT ct.id, cit.custo_pago
			FROM colonization_imperio_techs AS cit
			JOIN colonization_tech AS ct
			ON ct.id = cit.id_tech
			WHERE cit.id_imperio = {$imperio->id}
			ORDER BY ct.nivel, ct.belica, ct.lista_requisitos, ct.nome");
			//***/
		} else {
			
			$techs = $tech->query_tech(" AND publica = 1");			
			
			/***
			$techs = $wpdb->get_results("
			SELECT id FROM colonization_tech 
			WHERE publica = 1 
			ORDER BY nivel, belica, lista_requisitos, nome");
			//***/
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
					<div class='wrapper_tech'>
						<div class='tech tooltip' $style_pago>{$tech->nome}{$html_custo_pago}
							<span class='tooltiptext'>{$tech->descricao}</span>
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
				
				$ids_tech_parent = $ids_tech_parent[1];

				foreach ($ids_tech_parent as $chave => $id_tech_parent) {
					$tech_parent = new tech($id_tech_parent);
					if (!empty($html_tech[$tech_parent->id])) {
						$html_tech[$tech_parent->id] .= "					
					<div class='fas fa-long-arrow-alt-right wrapper_tech' style='padding-top: 12px;'>&nbsp;</div>
					<div class='wrapper_tech'>
						<div class='tech tooltip'>{$tech->nome}
							<span class='tooltiptext'>{$tech->descricao}</span>
						</div>";
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
				$html_tech[$tech->id] .= "
					</div> <!-- Fecha wrapper_tech -->";				
			} else {
				foreach ($ids_tech_parent as $chave => $id_tech_parent) {
					$tech_parent = new tech($id_tech_parent);
					if (!empty($html_tech[$tech_parent->id])) {						
						$html_tech[$tech_parent->id] .= "
					</div> <!-- Fecha wrapper_tech -->";
					}
				}
			}
	
		}

		$html = "<div style='overflow-x: visible; max-width: 5000px;'>";
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
		$html = "
		<h3>Distância entre as Estrelas</h3>
		<div>
			<div style='clear:both;'>
				<div style='display: inline-block; width: 100px;'>Origem:</div>
				<div id='estrela_origem' style='display: inline-block; width: 300px;'>&nbsp;</div>
			</div>
			<div style='clear:both;'>
				<div style='display: inline-block; width: 100px;'>Origem:</div>
				<div id='estrela_destino' style='display: inline-block; width: 300px;'>&nbsp;</div>
			</div>
			<div id='distancia'><b>Distância:</b> 0.0</div>
		</div>
		<script>
		function carrega_distancia() {
		let estrela_origem = document.getElementById('estrela_origem');
		let estrela_destino = document.getElementById('estrela_destino');
		
		estrela_origem.innerHTML = lista_estrelas_html();
		estrela_destino.innerHTML = lista_estrelas_html();
		
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

		if (empty($_GET['page_id'])) {//Só mostra a barra de recursos se for o Fórum
			return;
		} elseif ($_GET['page_id'] != "357") {
			return;
		}

		$user = wp_get_current_user();
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		} else {
			$roles = "";
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
			$recursos_atuais = substr($recursos_atuais,19);
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
		$user_roles=$user_meta->roles[0];
			
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
		<tr><td style='width: 300px;'>Planeta (X;Y;Z)</td><td>Recursos</td></tr>
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
		foreach ($lista_id_imperio as $id) {
			$imperio = new imperio($id->id, true);
			$lista_pontuacao[$id->id] = $imperio->pontuacao;
			$lista_nome[$id->id] = $imperio->nome;
		}
		
		arsort($lista_pontuacao, SORT_NUMERIC);
		
		foreach ($lista_pontuacao as $id_imperio => $pontuacao) {
			$html .= "<tr><td>{$lista_nome[$id_imperio]}</td>
			<td>{$pontuacao}</td>
			</tr>
			";

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
		
		//TODO -- Pega a data da última ação
		$html_lista	= "
		<div><h4>COLONIZATION - Ações do Império '{$imperio->nome}' - Turno {$turno}</h4></div>
		<div id='lista_colonias_imperio_{$imperio->id}'>{$lista_colonias}</div><br>
		<div id='recursos_atuais_imperio_{$imperio->id}'>{$recursos_atuais}</div><br>
		<div id='recursos_produzidos_imperio_{$imperio->id}'>{$recursos_produzidos}</div>
		<div id='recursos_consumidos_imperio_{$imperio->id}'>{$recursos_consumidos}</div><br>
		<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_acoes_turno'>
		<thead>
		<tr style='background-color: #E5E5E5; font-weight: 700;'><td>Colônia (X;Y;Z;P) | Slots</td><td>Instalação</td><td>Utilização (0-10)</td><td>&nbsp;</td></tr>
		</thead>
		<tbody>";
		
		$html_lista .= $imperio_acoes->lista_dados(false); //Mostra somente o Turno atual
		
		$html_lista .= "</tbody>
		</table>";		
		
		return $html_lista;
	}

	/***********************
	function colonization_exibe_acoes_imperio($atts = [], $content = null)
	----------------------
	Chamado pelo shortcode [colonization_exibe_acoes_imperio]
	$atts = [] - lista de atributos dentro do shortcode 
	(por exemplo, o shortcode [colonization_exibe_acoes_imperio id_imperio="1"] poderia exibir
	os dados do Império com id="1"
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
		
		$html = "<div>";
		
		$lista_frota_imperio = $wpdb->get_results("SELECT id FROM colonization_imperio_frota WHERE id_imperio={$imperio->id}");
		
		foreach ($lista_frota_imperio as $id) {
			$frota = new frota($id->id);
			$html .= $frota->exibe_frota() . "<br>";
		}
		$html .= "</div>";

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
$plugin = new colonization();
$menu_admin = new menu_admin();

//Ganchos de instalação e desinstalação do plugin "Colonization"
register_activation_hook( __FILE__, array($plugin,'colonization_install'));
register_deactivation_hook( __FILE__, array($plugin,'colonization_deactivate'));

//Cria o menu do plugin na área administrativa do WordPress
add_action('admin_menu', array($menu_admin, 'colonization_setup_menu'));
?>