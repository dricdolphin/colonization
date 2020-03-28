<?php
/**
 * Plugin Name: Colonization
 * Plugin URI: https://github.com/dricdolphin/colonization
 * Description: Plugin de WordPress com o sistema de jogo de Colonization.
 * Version: 1.0.3
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
		
		$resultados = $wpdb->get_results("SELECT id FROM colonization_imperio_colonias WHERE id_imperio={$imperio->id}");
		
		$html = "
		<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_acoes_turno'>
		<thead>
		<tr><td style='width: 300px;'>Planeta (X;Y;Z)</td><td>Recursos</td></tr>
		</thead>
		<tbody>";
		
		foreach ($resultados as $resultado) {
			$colonia = new colonia($resultado->id);
			
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
		
		foreach ($lista_id_imperio as $id) {
			$imperio = new imperio($id->id, true);
			
			$html .= "<tr><td>{$imperio->nome}</td>
			<td>{$imperio->pontuacao}</td>
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
		if (isset($atts['id'])) {
			$imperio = new imperio($atts['id']);
		} else {
			$imperio = new imperio();
		}
		
		if (isset($atts['turno'])) {
			$turno = new turno ($atts['turno']);
		} else {
			$turno = new turno();
		}
		
		$imperio_acoes = new acoes($imperio->id,$turno->turno);
		
		$lista_colonias = $imperio->exibe_lista_colonias();
		$recursos_atuais = $imperio->exibe_recursos_atuais($atts['turno']);
		$recursos_produzidos = $imperio_acoes->exibe_recursos_produzidos();
		$recursos_consumidos = $imperio_acoes->exibe_recursos_consumidos();
		
		//TODO -- Pega a data da última ação
		$html_lista	= "
		<div><h4>COLONIZATION - Ações do Império '{$imperio->nome}' - Turno {$turno->turno}</h4></div>
		<div><b>Lista de Colônias:</b> {$lista_colonias}</div>
		<div id='recursos_atuais_imperio_{$imperio->id}'>$recursos_atuais</div>
		<div id='recursos_produzidos_imperio_{$imperio->id}'>$recursos_produzidos</div>
		<div id='recursos_consumidos_imperio_{$imperio->id}'>$recursos_consumidos</div>
		<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_acoes_turno'>
		<thead>
		<tr><td>Colônia (X;Y;Z) | P</td><td>Instalação</td><td>Utilização (0-10)</td><td>&nbsp;</td></tr>
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
		}
		
		$turno = new turno();
		
		$html = "<div>";
		
		$lista_techs_imperio = $wpdb->get_results("SELECT cit.id, cit.id_tech, cit.custo_pago 
		FROM colonization_imperio_techs AS cit
		JOIN colonization_tech AS ct
		ON ct.id=cit.id_tech
		WHERE id_imperio={$imperio->id}
		ORDER BY ct.id_tech_parent, nome
		");
		
		$html_tech = [];
		
		foreach ($lista_techs_imperio as $id) {
			$tech = new tech($id->id_tech);
			
			if ($tech->id_tech_parent !=0) {
				$id_chave = $tech->id_tech_parent;
				if ($id->custo_pago != 0) {
					$html_tech[$id_chave] .= " -> <span style='font-style: italic;'>{$tech->nome}</span> [{$id->custo_pago}/{$tech->custo}]";
				} else {
					$html_tech[$id_chave] .= " -> ".$tech->nome;
				}
			} else {
				$id_chave = $tech->id;
				if ($id->custo_pago != 0) {
					$html_tech[$id_chave] = "<span style='font-style: italic;'>{$tech->nome}</span> [{$id->custo_pago}/{$tech->custo}]";
				} else {
					$html_tech[$id_chave] = $tech->nome;
				}
			}
		}
		
		
		foreach ($html_tech as $chave => $valor) {
			$html .= $valor.";<br>";
		}

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
		$html = "<h3>Construção de Naves</h3>
		<div id='dados'>Tamanho: 1; Velocidade: 10; Alcance: 0; <br>
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
		<div id='impulso'>Motor de Impulso: <input type='number' id='qtd_impulso' onchange='return calcula_custos(event, this);' value='0' min='1' style='width: 50px;'></input> Mk: <input type='number' id='mk_impulso' onchange='return calcula_custos(event, this);' value='1' max='3' min='1' style='width: 50px;'></input></div>
		<div id='dobra'>Motor de Dobra: <input type='number' id='qtd_dobra' onchange='return calcula_custos(event, this);' value='0' min='0' max='3' style='width: 50px;'></input> Mk: <input type='number' id='mk_dobra' onchange='return calcula_custos(event, this);' value='1' max='3' min='1' style='width: 50px;'></input></div>
		<div id='combustivel'>Células de Combustível: <input type='number' id='qtd_combustivel' onchange='return calcula_custos(event, this);' value='0' min='0' style='width: 50px;'></input></div>
		<div id='especiais'><label>Pesquisa: </label><input type='checkbox' onchange='return calcula_custos(event, this);' id='qtd_pesquisa' id='pesquisa' value='1'></input></div>
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