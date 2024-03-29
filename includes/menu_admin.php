<?php
/**************************
MENU_ADMIN.PHP
----------------
Responsável pelos menus da área de administração do Colonization
***************************/

class menu_admin {
	public $html_header = "";
	
	function __construct() {
		global $asgarosforum, $plugin_colonization;
		
		add_action( 'wp_enqueue_scripts', array($this,'colonization_scripts'));
		add_action( 'admin_enqueue_scripts', array($this,'colonization_admin_scripts'));
	
		if (empty($asgarosforum)) {//O plugin do Colonization REQUER a instalação do Fórum Asgaros
			$this->html_header = "<div>ATENÇÃO! O plugin 'Colonization' REQUER a instalação do plugin 'Asgaros Forum'.</div>";
		}
	}

	/******************
	function colonization_setup_menu()
	-----------
	Adiciona o menu do plugin no menu de Admin do WordPress
	******************/
	function colonization_setup_menu() {
		//Adiciona o menu "Colonization", que pode ser acessador por quem tem a opção de Editor ('publish_pages') -- os Admins tem essa função também, além da ('manage_options')
		add_menu_page('Colonization','Colonization','publish_pages','colonization_admin_menu',array($this,'colonization_admin_configura'),'none');
		add_submenu_page('colonization_admin_menu','Configuração','Configuração','publish_pages','colonization_admin_menu',array($this,'colonization_admin_configura'));
		add_submenu_page('colonization_admin_menu','Impérios','Impérios','publish_pages','colonization_admin_imperios',array($this,'colonization_admin_imperios'));
		add_submenu_page('colonization_admin_menu','Ações do Admin','Ações do Admin','publish_pages','colonization_admin_acoes_admin',array($this,'colonization_admin_acoes_admin'));
		add_submenu_page('colonization_admin_menu','Missões','Missões','publish_pages','colonization_admin_missoes',array($this,'colonization_admin_missoes'));
		add_submenu_page('colonization_admin_menu','Frotas','Frotas','publish_pages','colonization_admin_frotas',array($this,'colonization_admin_frotas'));
		add_submenu_page('colonization_admin_menu','Diplomacia','Diplomacia','publish_pages','colonization_admin_diplomacia',array($this,'colonization_admin_diplomacia'));
		add_submenu_page('colonization_admin_menu','Reabastecimentos','Reabastecimentos','publish_pages','colonization_admin_reabastece_imperio',array($this,'colonization_admin_reabastece_imperio'));
		add_submenu_page('colonization_admin_menu','Estrelas','Estrelas','publish_pages','colonization_admin_estrelas',array($this,'colonization_admin_estrelas'));
		add_submenu_page('colonization_admin_menu','Estrelas Visitadas','Estrelas Visitadas','publish_pages','colonization_admin_estrelas_visitadas',array($this,'colonization_admin_estrelas_visitadas'));		
		add_submenu_page('colonization_admin_menu','Recursos','Recursos','publish_pages','colonization_admin_recursos',array($this,'colonization_admin_recursos'));
		add_submenu_page('colonization_admin_menu','Techs','Techs','publish_pages','colonization_admin_techs',array($this,'colonization_admin_techs'));		
		add_submenu_page('colonization_admin_menu','Instalações','Instalações','publish_pages','colonization_admin_instalacoes',array($this,'colonization_admin_instalacoes'));
		add_submenu_page('colonization_admin_menu','Planetas','Planetas','publish_pages','colonization_admin_planetas',array($this,'colonization_admin_planetas'));
		add_submenu_page('colonization_admin_menu','Colônias','Colônias','publish_pages','colonization_admin_colonias',array($this,'colonization_admin_colonias'));
		add_submenu_page('colonization_admin_menu','Ações','Ações','publish_pages','colonization_admin_acoes',array($this,'colonization_admin_acoes'));
		add_submenu_page('colonization_admin_menu','Roda Turno','Roda Turno','publish_pages','colonization_admin_roda_turno',array($this,'colonization_admin_roda_turno'));
		add_action( 'admin_bar_menu', array($this,'add_link_forum_admin_bar'),999 ); //Adiciona um link para o Fórum
	}


	/******************
	function colonization_admin_scripts()
	-----------
	Adiciona os scripts do plugin ao Admin
	******************/
	function colonization_admin_scripts ($hook) {
		$hoje = date("YmdHi"); 
			wp_enqueue_script('gerencia_listas_js', '/wp-content/plugins/colonization/js/listas_js.js',false,$hoje);
			wp_enqueue_script('novo_objetos_js', '/wp-content/plugins/colonization/js/novo_objetos.js',false,$hoje);
			wp_enqueue_script('edita_objetos_js', '/wp-content/plugins/colonization/js/edita_objetos.js',false,$hoje);
			wp_enqueue_script('valida_objetos_js', '/wp-content/plugins/colonization/js/valida_objetos.js',false,$hoje);
			wp_enqueue_script('gerencia_objeto_js', '/wp-content/plugins/colonization/js/gerencia_objeto.js',false,$hoje);
			wp_enqueue_script('acoes_js', '/wp-content/plugins/colonization/js/acoes.js',false,$hoje);
			wp_enqueue_script('funcoes_auxiliares_js', '/wp-content/plugins/colonization/js/funcoes_auxiliares.js',false,$hoje);
			wp_enqueue_script('naves_js', '/wp-content/plugins/colonization/js/naves.js',false,$hoje);
			wp_enqueue_script('estrela_js', '/wp-content/plugins/colonization/js/estrela.js',false,$hoje);
			wp_enqueue_style('fonteawesome_css', '/wp-includes/fontawesome/css/all.css',false,$hoje);
			wp_enqueue_style('colonization_css', '/wp-content/plugins/colonization/colonization.css',false,$hoje);
	}
	
	/******************
	function colonization_scripts()
	-----------
	Adiciona os scripts do plugin
	******************/
	function colonization_scripts ($hook) {
		$hoje = date("YmdHi"); 
		wp_enqueue_script('novo_objetos_js', '/wp-content/plugins/colonization/js/novo_objetos.js',false,$hoje);
		wp_enqueue_script('edita_objetos_js', '/wp-content/plugins/colonization/js/edita_objetos.js',false,$hoje);
		wp_enqueue_script('valida_objetos_js', '/wp-content/plugins/colonization/js/valida_objetos.js',false,$hoje);
		wp_enqueue_script('gerencia_objeto_js', '/wp-content/plugins/colonization/js/gerencia_objeto.js',false,$hoje);
		wp_enqueue_script('acoes_js', '/wp-content/plugins/colonization/js/acoes.js',false,$hoje);
		wp_enqueue_script('gerencia_listas_js', '/wp-content/plugins/colonization/js/listas_js.js',false,$hoje);
		wp_enqueue_script('naves_js', '/wp-content/plugins/colonization/js/naves.js',false,$hoje);
		wp_enqueue_script('estrela_js', '/wp-content/plugins/colonization/js/estrela.js',false,$hoje);
		wp_enqueue_script('funcoes_auxiliares_js', '/wp-content/plugins/colonization/js/funcoes_auxiliares.js',false,$hoje);
		wp_enqueue_script('google_chars', 'https://www.gstatic.com/charts/loader.js',false,$hoje);
		wp_enqueue_style('fonteawesome_css', '/wp-includes/fontawesome/css/all.css',false,$hoje);
		wp_enqueue_style('colonization_css', '/wp-content/plugins/colonization/colonization.css',false,$hoje);
		//<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		
	}	


	/******************
	function add_link_forum_admin_bar()
	-----------
	Adiciona um link para o Fórum no admin-bar
	******************/
	function add_link_forum_admin_bar($admin_bar) {         
		$args = array(
			'parent' => 'site-name',
			'id'     => 'forum',
			'title'  => 'Fórum',
			'href'   => '/forum',
			'meta'   => false
		);
		$admin_bar->add_node( $args );       
	}	


	//colonization_admin_configura
	function colonization_admin_configura() {
		global $wpdb, $wp_admin_bar;
		
		$html = $this->html_header;

		$html .= "<div><h2>COLONIZATION - Configurações</h2></div>
		<div>
		<table class='wp-list-table widefat tabela_admin fixed striped users' data-tabela='colonization_referencia_forum'>
		<thead>
		<tr><th>ID</th><th>Descrição</th><th>ID Post ou Página</th><th>Página</th></tr>
		</thead>
		<tbody>";
		
		//Pega a lista de configurações
		$lista_id = $wpdb->get_results("SELECT id FROM colonization_referencia_forum");
		$html_lista = "";
		
		foreach ($lista_id as $id) {
			$configuracao = new configuracao($id->id);
			
			$html_dados = $configuracao->lista_dados();
			$html_lista .= "
			<tr>
			{$html_dados}
			</tr>";
		}
		
		$html.= $html_lista;
		
		$html .= "\n</tbody>
		</table></div>
		<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return nova_configuracao(event);'>Adicionar nova Configuração</a></div>";
		
		echo $html;
	}

	
	/******************
	function colonization_admin_imperios()
	-----------
	Exibe a página onde você pode criar e editar Impérios (dados básicos)
	******************/
	function colonization_admin_imperios() {
		global $wpdb, $wp_admin_bar;
		
		$html = $this->html_header;
		
		if (isset($_GET['id'])) {
			$imperio = new imperio($_GET['id'],true);
			$imperio_recursos = new imperio_recursos($_GET['id']);
			$html_dados_imperio = $imperio_recursos->lista_dados();

			$html .= "<div><h2>COLONIZATION - Recursos do Império '{$imperio->nome}'</h2></div>
			<div>
			<table class='wp-list-table widefat tabela_admin fixed striped users' data-tabela='colonization_imperio_recursos'>
			<thead>
			<tr><th>ID</th><th>Recurso</th><th>Qtd</th><th>Disponível</th></tr>
			</thead>
			<tbody>";

			$html .= $html_dados_imperio;

			$html .= "\n</tbody>
			</table></div>";
			
			//$resultados = $wpdb->get_results("SELECT id FROM colonization_imperio_techs WHERE id_imperio={$imperio->id}");
			$tech = new tech();
			$resultados = $tech->query_tech("",$imperio->id);
			
			$html_techs_imperio = "";
			foreach ($resultados as $resultado) {
				$id_imperio_techs = $wpdb->get_var("SELECT id FROM colonization_imperio_techs WHERE id_imperio={$imperio->id} AND id_tech={$resultado->id}");
				if (!empty($id_imperio_techs)) {
					$imperio_techs = new imperio_techs($id_imperio_techs);

					$html_techs_imperio .= $imperio_techs->lista_dados();
				}
			}
			
			$html .= "<div><h2>COLONIZATION - Techs do Império '{$imperio->nome}'</h2></div>
			<div>
			<table class='wp-list-table widefat tabela_admin fixed striped users' data-tabela='colonization_imperio_techs' style='width: 700px;'>
			<thead>
			<tr><th style='width: 500px;'>Tech</th><th style='width: 150px;'>Pesquisas Gastas<br>(0 = completado)</th><th style='width: 40px;'>Turno</th><th style='width: 150px;'>Tech Inicial</th></tr>
			</thead>
			<tbody>";

			$html .= $html_techs_imperio;

			$html .= "\n</tbody>
			</table></div>
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return nova_tech_imperio(event, {$imperio->id});'>Adicionar nova Tech</a></div>";

			$resultados = $wpdb->get_results("SELECT id FROM colonization_imperio_instalacoes WHERE id_imperio={$imperio->id}");
		
			$html_instalacoes_imperio = "";
			foreach ($resultados as $resultado) {
				$imperio_instalacoes = new imperio_instalacoes($resultado->id);
				$html_instalacoes_imperio .= $imperio_instalacoes->lista_dados();
			}
			
			$html .= "<br><div><h2>COLONIZATION - Instalações Permitidas '{$imperio->nome}'</h2></div>
			<div>
			<table class='wp-list-table widefat tabela_admin fixed striped users' data-tabela='colonization_imperio_instalacoes' style='width: 400px;'>
			<thead>
			<tr><th style='width: 300px;'>Instalações não-Publicas liberadas</th></tr>
			</thead>
			<tbody>";

			$html .= $html_instalacoes_imperio;

			$html .= "\n</tbody>
			</table></div>
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return nova_instalacao_imperio(event, {$imperio->id});'>Adicionar nova Instalação</a></div>";

			$resultados = $wpdb->get_results("SELECT id FROM colonization_imperio_techs_permitidas WHERE id_imperio={$imperio->id}");
		
			$html_techs_permitidas_imperio = "";
			foreach ($resultados as $resultado) {
				$techs_permitidas_imperio = new techs_permitidas_imperio($resultado->id);
				$html_techs_permitidas_imperio .= $techs_permitidas_imperio->lista_dados();
			}
			
			$html .= "<br><div><h2>COLONIZATION - Techs Permitidas '{$imperio->nome}'</h2></div>
			<div>
			<table class='wp-list-table widefat tabela_admin fixed striped users' data-tabela='colonization_imperio_techs_permitidas' style='width: 400px;'>
			<thead>
			<tr><th style='width: 300px;'>Techs não-Publicas liberadas</th></tr>
			</thead>
			<tbody>";

			$html .= $html_techs_permitidas_imperio;

			$html .= "\n</tbody>
			</table></div>
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return nova_tech_permitida_imperio(event, {$imperio->id});'>Adicionar nova Tech</a></div>";

			
			$html .= "<br>
			<div><a href='{$_SERVER['SCRIPT_NAME']}?page={$_GET['page']}'>Voltar aos Impérios</a>";			
		} else {
			
			$html .= "<div><h2>COLONIZATION - Impérios</h2></div>
			<div>
			<table class='wp-list-table widefat tabela_admin fixed striped users' data-tabela='colonization_imperio'>
			<thead>
			<tr class='th_linha_1'><th>ID</th><th>Usuário</th><th>Nome do Império</th><th>Prestígio</th><th>&nbsp;</th></tr>
			</thead>
			<tbody>";
			
			//Pega a lista de impérios
			$lista_id_imperio = $wpdb->get_results("SELECT id, id_jogador FROM colonization_imperio");
			$html_lista_imperios = "";
			
			foreach ($lista_id_imperio as $id) {
				$imperio = new imperio($id->id,true);
				
				$html_dados_imperio = $imperio->lista_dados();

				$html_lista_imperios .= "
				<tr>
				{$html_dados_imperio}
				</tr>";
			}
			
			$html.= $html_lista_imperios;
			
			$html .= "\n</tbody>
			</table></div>
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return novo_imperio(event);'>Adicionar novo Império</a></div>";
		}
		
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

		if (isset($_GET['id'])) {
			$estrela = new estrela($_GET['id']);
			
			$html .= "<div><h2>COLONIZATION - Sistema Estelar da estrela '{$estrela->nome}' - {$estrela->X};{$estrela->Y};{$estrela->Z} </h2></div>
			<div>
			<table class='wp-list-table widefat tabela_admin fixed striped users' data-tabela='colonization_planeta'>
			<thead>
			<tr><th style='width: 100px;'>ID</th><th>Nome</th><th>Orbita a Estrela (X;Y;Z)</th><th style='width: 60px;'>Posição</th><th>Classe</th><th>Subclasse</th><th style='width: 70px;'>Tamanho</th><th style='width: 90px;'>Inóspito</th><th>&nbsp;</th>
			</tr>
			</thead>
			<tbody>";

			//Pega a lista de planetas
			$lista_id = $wpdb->get_results("SELECT id FROM colonization_planeta WHERE id_estrela={$estrela->id} ORDER BY posicao");
			$html_lista = "";
			
			foreach ($lista_id as $id) {
				$planeta = new planeta($id->id);
				
				$html_dados = $planeta->lista_dados($_GET['id']);

				$html_lista .= "
				<tr>
				{$html_dados}
				</tr>";
			}
			
			$html.= $html_lista;
			
			$html .= "\n</tbody>
			</table></div>
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return novo_planeta(event, {$estrela->id});'>Adicionar novo Planeta</a></div>
			<br>
			<div><a href='{$_SERVER['SCRIPT_NAME']}?page={$_GET['page']}'>Voltar às Estrelas</a>";
		
		} else {
			$html .= "<div><h2>COLONIZATION - Estrelas</h2></div>
			<div>
			<table class='wp-list-table widefat tabela_admin fixed striped users' data-tabela='colonization_estrela'>
			<thead>
			<tr class='th_linha_1'><th rowspan='2' style='width: 150px;'>ID</th><th rowspan='2'  style='width: 150px;'>Nome da estrela</th><th rowspan='2' style='width: 200px;'>Descrição</th><th rowspan='2' style='width: 200px;'>Comentários</th><th colspan='3' style='width: 150px;'>Posição</th><th rowspan='2'>Tipo de estrela</th><th rowspan='2'>Cerco</th><th rowspan='2'>IDs Buracos de Minhoca</th><th rowspan='2'>&nbsp;</th></tr>
			<tr><th class='th_linha_2' style='width: 30px;'>X</th><th class='th_linha_2' style='width: 30px;'>Y</th><th class='th_linha_2' style='width: 30px;'>Z</th></tr>
			</thead>
			<tbody>";
			
			//Pega a lista de estrelas
			$lista_id_estrelas = $wpdb->get_results("SELECT id FROM colonization_estrela ORDER BY X,Y,Z");
			$html_lista_estrelas = "";
			
			foreach ($lista_id_estrelas as $id) {
				$estrela = new estrela($id->id);
				
				$html_dados_estrela = $estrela->lista_dados();

				$html_lista_estrelas .= "
				<tr>
				{$html_dados_estrela}
				</tr>";
			}
			
			$html.= $html_lista_estrelas;
			
			$html .= "\n</tbody>
			</table></div>
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return nova_estrela(event);'>Adicionar nova Estrela</a></div>";
		}
		echo $html;
	}

	/******************
	function colonization_admin_planetas()
	-----------
	Exibe a página de gestão de planetas
	******************/
	function colonization_admin_planetas() {
		global $wpdb;
		$turno = new turno();
		$html = $this->html_header;
		
		if (isset($_GET['id'])) {
			$planeta = new planeta($_GET['id']);
			
			$html .= "<script>
			var id_colonia={$_GET['id']};
			</script>
			
			<div><h2>COLONIZATION - editando o Planeta '{$planeta->nome}'</h2></div>";

			$lista_planeta_recursos = $wpdb->get_results("SELECT id, id_recurso FROM colonization_planeta_recursos WHERE id_planeta={$planeta->id} AND turno={$turno->turno}");
			$html_lista = "";

			//Recursos da Colônia
			$html .= "<div><h3>Recursos do Planeta</h3>
			<table class='wp-list-table widefat tabela_admin fixed striped users' data-tabela='colonization_planeta_recursos'>
			<thead>
			<tr><th>ID</th><th>Recurso</th><th>Disponível</th><th>Turno</th>
			</tr>
			</thead>
			<tbody>
			";

			foreach ($lista_planeta_recursos as $id) {
				$planeta_recurso = new planeta_recurso($id->id);
				
				$html_dados = $planeta_recurso->lista_dados();

				$html_lista .= "
				<tr>
				{$html_dados}
				</tr>";
			}
			
			$link_auto_popular_recursos = "";
			if (empty($lista_planeta_recursos)) {
				$link_auto_popular_recursos = "&nbsp; <a href='#' class='page-title-action colonization_admin_botao' onclick='return popular_recursos_planeta(event, this, {$planeta->id})'>Popular lista de Recursos</a>";
			}
			
			$html.= $html_lista;

			$html .= "\n</tbody>
			</table></div>
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return novo_planeta_recurso(event, {$planeta->id});'>Adicionar novo Recurso</a>{$link_auto_popular_recursos}</div>";

			/*************************************/
			$lista_colonia_instalacoes = $wpdb->get_results("SELECT id, id_instalacao FROM colonization_planeta_instalacoes WHERE id_planeta={$planeta->id}");
			$html_dados = "";
			$html_lista = "";
			
			if ($planeta->tamanho == 1) {
				$max_instalacoes = "{$planeta->tamanho} instalação";
			} else {
				$max_instalacoes = "{$planeta->tamanho} instalações";
			}
			
			//Instalações da Colônia
			$html .= "<br>
			<div><h3>Instalações do Planeta - Máximo de {$max_instalacoes}</h3>
			<table class='wp-list-table widefat tabela_admin fixed striped users' data-tabela='colonization_planeta_instalacoes'>
			<thead>
			<tr><th>ID</th><th>Nome</th><th style='width: 40px;'>Nível</th><th style='width: 90px;'>Turno Const.</th><th style='width: 90px;'>Inst. Inicial</th><th style='width: 90px;'>Turno Desmonta</th><th style='width: 90px;'>Turno Destr.</th><th>&nbsp;</th>
			</tr>
			</thead>
			<tbody>
			";

			foreach ($lista_colonia_instalacoes as $id) {
				$planeta_instalacao = new colonia_instalacao($id->id);
				
				$html_dados = $planeta_instalacao->lista_dados();

				$html_lista .= "
				<tr>
				{$html_dados}
				</tr>";
			}
			
			$html.= $html_lista;
			
			$html .= "\n</tbody>
			</table></div>
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return nova_colonia_instalacao(event, {$planeta->id});'>Adicionar nova Instalação</a></div>";
			
			if (!empty($_GET['id_estrela'])) {//Se veio da edição de planetas da ESTRELA
				$html .= "<br>
				<div><a href='{$_SERVER['SCRIPT_NAME']}?page=colonization_admin_estrelas&id={$_GET['id_estrela']}'>Voltar à Estrela</a>";
			} else {
				$html .= "<br>
				<div><a href='{$_SERVER['SCRIPT_NAME']}?page={$_GET['page']}'>Voltar aos Planetas</a>";				
			}
		} else {
			$html .= "<div><h2>COLONIZATION - Planetas</h2></div>
			<div>
			<table class='wp-list-table widefat tabela_admin fixed striped users' data-tabela='colonization_planeta'>
			<thead>
			<tr class='th_linha_1'><th style='width: 120px;'>ID</th><th>Nome</th><th>Orbita a Estrela (X;Y;Z)</th><th style='width: 60px;'>Posição</th><th>Classe</th><th>Subclasse</th><th style='width: 70px;'>Tamanho</th><th style='width: 90px;'>Inóspito</th><th>&nbsp;</th>
			</tr>
			</thead>
			<tbody>";
			
			//Pega a lista de planetas
			$lista_id = $wpdb->get_results("SELECT colonization_planeta.id AS id 
			FROM colonization_planeta 
			LEFT JOIN colonization_estrela
			ON colonization_planeta.id_estrela = colonization_estrela.id
			ORDER BY colonization_estrela.X, colonization_estrela.Y, colonization_estrela.Z, colonization_planeta.posicao");

			$html_lista = "";
			
			foreach ($lista_id as $id) {
				$planeta = new planeta($id->id);
				
				$html_dados = $planeta->lista_dados();

				$html_lista .= "
				<tr>
				{$html_dados}
				</tr>";
			}
			
			$html.= $html_lista;
			
			$html .= "\n</tbody>
			</table></div>
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return novo_planeta(event);'>Adicionar novo Planeta</a></div>";
		}
		echo $html;

	}

		
	
	/******************
	function colonization_admin_estrelas_visitadas()
	-----------
	Exibe a página com todas as estrelas e mostra por quais impérios elas já foram visitadas
	******************/
	function colonization_admin_estrelas_visitadas() {
		global $plugin_colonization, $wpdb;

		$html = $this->html_header;
		
		$html .= "<div><h2>COLONIZATION - Estrelas Visitadas</h2></div>
		<div>
		";

		$lista_id = $wpdb->get_results("
		SELECT ce.id 
		FROM colonization_estrela AS ce
		ORDER BY ce.nome");
		
		$lista_ids_imperios = $wpdb->get_results("
		SELECT id, nome
		FROM colonization_imperio
		ORDER BY nome
		");
		
		$html_lista = "";
		
		$lista_id_estrela = [];
		$coluna = 1;
		
		foreach ($lista_id as $id) {
			$estrela = new estrela($id->id);
			
			$html_lista_imperios = "";
			foreach ($lista_ids_imperios as $id_imperio) {
				$estrela_visitada = $wpdb->get_var("SELECT id FROM colonization_estrelas_historico WHERE id_imperio={$id_imperio->id} AND id_estrela={$estrela->id}");
				$abastece_checked = "";
				if (!empty($estrela_visitada)) {
					$abastece_checked = "checked";
				}
				
				$html_lista_imperios .= "<input type='checkbox' onchange='return salva_reabastece(event, this,{$id_imperio->id},{$estrela->id},\"colonization_estrelas_historico\");' {$abastece_checked}></input><label>{$id_imperio->nome}</label><br>";
			}
			
			$html_lista .= "<div style='display: inline-block; width: 160px; padding: 2px; margin: 5px;'>
			<b>{$estrela->nome} ({$estrela->X};{$estrela->Y};{$estrela->Z})</b><br>
			{$html_lista_imperios}
			</div>";
		
			$coluna++;
			if ($coluna == 6) {
				$coluna = 1;
				$html_lista .= "<br>";
			}
		
		}
		
		$html .= $html_lista;
		
		//$html .= $plugin_colonization->colonization_exibe_autoriza_reabastece_imperio();
		
		$html .= "</div>";
		
		echo $html;
	}


	/******************
	function colonization_admin_reabastece_imperio()
	-----------
	Exibe a página de pontos de reabastecimento de um Império
	******************/
	function colonization_admin_reabastece_imperio() {
		global $plugin_colonization, $wpdb;
		

		$html = $this->html_header;
		
		$html .= "<div><h2>COLONIZATION - Pontos de Reabastecimento</h2></div>
		<div>
		";
		
		$html .= $plugin_colonization->colonization_exibe_autoriza_reabastece_imperio();
		
		$html .= "</div>";
		
		echo $html;
	}

	//colonization_admin_diplomacia
	/******************
	function colonization_admin_diplomacia()
	-----------
	Exibe o sistema de Diplomacia
	******************/
	function colonization_admin_diplomacia() {
		global $plugin_colonization, $wpdb;
		

		$html = $this->html_header;
		
		$imperios = [];
		$imperios_contato = [];
		
		$ids_imperios = $wpdb->get_results("SELECT id FROM colonization_imperio");
		foreach ($ids_imperios as $ids_imperio) {
			$imperios[] = new imperio($ids_imperio->id, true);
		}
		
		$imperios_contato = $imperios;
		
		$nomes_npcs = $wpdb->get_results("SELECT DISTINCT nome_npc FROM colonization_imperio_colonias WHERE nome_npc != ''");
		foreach ($nomes_npcs as $nome_npcs) {
			$imperios_contato[] = new imperio(0, true);
			$ultimo_index = count($imperios_contato)-1;
			$imperios_contato[$ultimo_index]->nome = $nome_npcs->nome_npc;
		}

		$html .= "<div><h2>COLONIZATION - Diplomacia</h2></div>
		<div>
		";
		
		$html_contato_imperio = [];
		$html_comercio_imperio = [];
		foreach ($imperios as $chave_atual => $imperio_atual) {
			$html_lista_contato_imperios = "";
			$html_lista_comercio_imperios = "";
			$html_contato_imperio[$chave_atual] = "<div data-atributo='lista_imperios_contato' class='lista_imperios_contato par_impar' data-tabela='colonization_diplomacia'>
			<div class='titulo_imperio'>Império {$imperio_atual->nome}</div>
			<div class='subtitulo'>Primeiro Contato</div>
			";
			
			$html_comercio_imperio[$chave_atual] = "<div data-atributo='lista_imperios_contato' class='lista_imperios_contato par_impar' data-tabela='colonization_diplomacia'>
			<div class='titulo_imperio'>Império {$imperio_atual->nome}</div>
			<div class='subtitulo'>Acordo Comercial</div>
			";
			
			foreach ($imperios_contato as $chave_contato => $imperio_contato) {
				if ($imperio_contato->id == $imperio_atual->id) {
					continue;
				}
				$nome_npc = "";
				$texto_npc = "";
				if ($imperio_contato->id == 0) {
					$nome_npc = $imperio_contato->nome;
					$texto_npc = "NPC: ";
				}
				
				$encontro = $wpdb->get_var("SELECT id FROM colonization_diplomacia WHERE id_imperio={$imperio_atual->id} AND id_imperio_contato={$imperio_contato->id} AND nome_npc='{$nome_npc}'");
				$comercio = $wpdb->get_var("SELECT id FROM colonization_diplomacia WHERE id_imperio={$imperio_atual->id} AND id_imperio_contato={$imperio_contato->id} AND nome_npc='{$nome_npc}' and acordo_comercial=true");
				
				$encontro_checked = "";
				$encontro_disabled = "";
				if (!empty($encontro)) {
					$encontro_checked = "checked";
					$encontro_disabled = "disabled";
				}

				$comercio_checked = "";
				if (!empty($comercio)) {
					$comercio_checked = "checked";
				}
				
				$html_lista_contato_imperios .= "<input type='checkbox' onchange='return salva_diplomacia(event, this,{$imperio_atual->id},{$imperio_contato->id},\"{$nome_npc}\",\"encontro\");' {$encontro_checked} {$encontro_disabled}></input><label>{$texto_npc}{$imperio_contato->nome}</label><br>\n";
				$html_lista_comercio_imperios .= "<input type='checkbox' onchange='return salva_diplomacia(event, this,{$imperio_atual->id},{$imperio_contato->id},\"{$nome_npc}\",\"acordo_comercial\");' {$comercio_checked}></input><label>{$texto_npc}{$imperio_contato->nome}</label><br>\n";
			}
			
			$html_contato_imperio[$chave_atual] .= $html_lista_contato_imperios;
			$html_contato_imperio[$chave_atual] .= "</div>";
			
			$html_comercio_imperio[$chave_atual] .= $html_lista_comercio_imperios;
			$html_comercio_imperio[$chave_atual] .= "</div>";
		}
		

		foreach ($html_contato_imperio as $chave => $valor) {
			$html .= $valor;
		}

		$html .= "<hr>";
		
		foreach ($html_comercio_imperio as $chave => $valor) {
			$html .= $valor;
		}
		
		$html .= "</div>";
		
		echo $html;
	}

	
	/******************
	function colonization_admin_recursos()
	-----------
	Exibe a página de gestão de recursos
	******************/
	function colonization_admin_recursos() {
		global $wpdb;
		
		$html = $this->html_header;
		
		$html .= "<div><h2>COLONIZATION - Recursos</h2></div>
		<div>
		<table class='wp-list-table widefat tabela_admin fixed striped users' data-tabela='colonization_recurso'>
		<thead>
		<tr class='th_linha_1'><th>ID</th><th>Nome</th><th>Descrição</th><th>Ícone</th><th>Nível</th><th>Acumulável</th><th>Extrativo</th><th>Local</th>
		</tr>
		</thead>
		<tbody>";
		
		//Pega a lista de recursos
		$lista_id = $wpdb->get_results("SELECT id FROM colonization_recurso ORDER BY nivel, nome");
		$html_lista = "";
		
		foreach ($lista_id as $id) {
			$recurso = new recurso($id->id);
			
			$html_dados = $recurso->lista_dados();

			$html_lista .= "
			<tr>
			{$html_dados}
			</tr>";
		}
		
		$html.= $html_lista;
		
		$html .= "\n</tbody>
		</table></div>
		<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return novo_recurso(event);'>Adicionar novo Recurso</a></div>";
		
		echo $html;
	}

	/******************
	function colonization_admin_techs()
	-----------
	Exibe a página de gestão de Techs
	******************/
	function colonization_admin_techs() {
		global $wpdb;
		
		$html = $this->html_header;
		
		$html .= "<div><h2>COLONIZATION - Techs</h2></div>
		<div>
		<table class='wp-list-table widefat tabela_admin fixed striped users' data-tabela='colonization_tech'>
		<thead>
		<tr class='th_linha_1'>
		<th style='width: 200px;'>ID</th><th style='width: 260px;'>Nome</th><th style='width: 200px;'>Descrição</th><th style='width: 60px;'>Nível</th><th style='width: 60px;'>Custo</th><th style='width: 80px;'>Tech Parente</th><th style='width: 120px;'>Lista Requisitos</th><th style='width: 80px;'>Bélica</th><th style='width: 80px;'>Parte Nave</th><th style='width: 80px;'>Pública</th><th style='width: 200px;'>Especiais</th><th style='width: 200px;'>Ícone</th>
		</tr>
		</thead>
		<tbody>";
		
		//Pega a lista de recursos
		$techs = new tech();
		$lista_id = $techs->query_tech();
		$html_lista = "";
		
		foreach ($lista_id as $id) {
			$tech = new tech($id->id);
			
			$html_dados = $tech->lista_dados();

			$html_lista .= "
			<tr>
			{$html_dados}
			</tr>";
		}
		
		$html.= $html_lista;
		
		$html .= "\n</tbody>
		</table></div>
		<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return nova_tech(event);'>Adicionar nova Tech</a></div>";
		
		echo $html;
	}
	
	/******************
	function colonization_admin_instalacoes()
	-----------
	Exibe a página de gestão de instalações
	******************/
	function colonization_admin_instalacoes() {
		global $wpdb;
		
		$html = $this->html_header;
		$html_lista = "";
		
		if (isset($_GET['id'])) {
			$instalacao = new instalacao($_GET['id']);
			
			$html .= "<script>
			var id_instalacao={$_GET['id']};
			</script>
			
			<div><h2>COLONIZATION - editando a Instalação '{$instalacao->nome}'</h2></div>";

			$lista_instalacao_recursos = $wpdb->get_results("SELECT id FROM colonization_instalacao_recursos WHERE id_instalacao={$instalacao->id} AND consome=0");

			//Recursos produzidos
			$html .= "<div><h3>Recursos Produzidos</h3>
			<table class='wp-list-table widefat tabela_admin fixed striped users' data-tabela='colonization_instalacao_recursos'>
			<thead>
			<tr><th>ID</th><th>Recurso</th><th>Quantidade por Nível</th>
			</tr>
			</thead>
			<tbody>
			";

			foreach ($lista_instalacao_recursos as $id) {
				$recurso_instalacao = new recurso_instalacao($id->id);
				
				$html_dados = $recurso_instalacao->lista_dados();

				$html_lista .= "
				<tr>
				{$html_dados}
				</tr>";
			}
			
			$html.= $html_lista;
			
			$html .= "\n</tbody>
			</table></div>
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return novo_instalacao_recurso(event, 0);'>Adicionar novo recurso PRODUZIDO</a></div>";
			
			/*************************************/
			
			$lista_instalacao_recursos = $wpdb->get_results("SELECT id FROM colonization_instalacao_recursos WHERE id_instalacao={$instalacao->id} AND consome=1");
			$html_dados = "";
			$html_lista = "";

			//Recursos consumidos
			$html .= "<div><h3>Recursos Consumidos</h3>
			<table class='wp-list-table widefat tabela_admin fixed striped users' data-tabela='colonization_instalacao_recursos'>
			<thead>
			<tr><th>ID</th><th>Recurso</th><th>Quantidade por Nível</th>
			</tr>
			</thead>
			<tbody>
			";

			foreach ($lista_instalacao_recursos as $id) {
				$recurso_instalacao = new recurso_instalacao($id->id);
				
				$html_dados = $recurso_instalacao->lista_dados();

				$html_lista .= "
				<tr>
				{$html_dados}
				</tr>";
			}

			$html.= $html_lista;

			$html .= "\n</tbody>
			</table></div>
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return novo_instalacao_recurso(event, 1);'>Adicionar novo recurso CONSUMIDO</a></div>
			<br>
			<div><a href='{$_SERVER['SCRIPT_NAME']}?page={$_GET['page']}'>Voltar às Instalações</a>";
			
		} else {
			$html .= "<div><h2>COLONIZATION - Instalações</h2></div>
			<div>
			<table class='wp-list-table widefat tabela_admin fixed striped users' data-tabela='colonization_instalacao'>
			<thead>
			<tr class='th_linha_1'><th>ID</th><th>Nome</th><th>Descrição</th><th>Tech Requisito</th><th style='width: 40px;'>Slots</th style='width: 80px;'><th>Autônoma</th><th style='width: 120px;'>Desguarnecida</th><th style='width: 120px;'>Pode desativar</th><th style='width: 60px;'>Oculta</th><th style='width: 60px;'>Publica</th><th>Especiais</th><th>Ícone</th><th>Custos</th><th>&nbsp;</th>
			</tr>
			</thead>
			<tbody>";
			
			//Pega a lista de instalações
			$lista_id = $wpdb->get_results("SELECT id FROM colonization_instalacao ORDER BY nome");
			$html_lista = "";
			
			foreach ($lista_id as $id) {
				$instalacao = new instalacao($id->id);
				
				$html_dados = $instalacao->lista_dados();

				$html_lista .= "
				<tr>
				{$html_dados}
				</tr>";
			}
			
			$html.= $html_lista;
			
			$html .= "\n</tbody>
			</table></div>
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return nova_instalacao(event);'>Adicionar nova Instalação</a></div>";
		}
		
		echo $html;
	}	


	/******************
	function colonization_admin_colonias()
	-----------
	Exibe a página de gestão de colônias
	******************/
	function colonization_admin_colonias() {
		global $wpdb;
		
		$html = $this->html_header;
		$html_lista = "";
		$turno = new turno();
		
		if (isset($_GET['id'])) {
			$colonia = new colonia($_GET['id']);
			$imperio = new imperio($colonia->id_imperio,true);
			if ($imperio->id == 0) {
				$imperio->nome = $colonia->nome_npc;
			}
			$planeta = new planeta($colonia->id_planeta);
			
			$html .= "<script>
			var id_colonia={$_GET['id']};
			</script>
			
			<div><h2>COLONIZATION - editando a Colônia '{$planeta->nome}' do Império '{$imperio->nome}'</h2></div>";

			$max_turnos = $wpdb->get_results("SELECT id_recurso, MAX(turno) as turno FROM colonization_planeta_recursos WHERE id_planeta={$planeta->id} GROUP BY id_recurso, id_planeta");
			foreach ($max_turnos as $max_turno) {
				if ($max_turno->turno < $turno->turno) {//Atualiza os recursos do planeta caso não esteja no Turno Atual
					$id_planeta_recurso = $wpdb->get_var("SELECT id FROM colonization_planeta_recursos WHERE id_planeta={$planeta->id} AND id_recurso={$max_turno->id_recurso} AND turno={$max_turno->turno}");
					$planeta_recurso = new planeta_recurso($id_planeta_recurso);
					$wpdb->query("INSERT INTO colonization_planeta_recursos SET turno={$turno->turno}, id_planeta={$planeta->id}, id_recurso={$max_turno->id_recurso}, disponivel={$planeta_recurso->qtd_disponivel}");					
				}
			}
			
			$lista_planeta_recursos = $wpdb->get_results("SELECT colonization_planeta_recursos.id, id_recurso, turno 
			FROM colonization_planeta_recursos
			JOIN colonization_recurso
			ON colonization_recurso.id = colonization_planeta_recursos.id_recurso
			WHERE id_planeta={$planeta->id} 
			AND turno={$turno->turno} ORDER BY nome");
			$html_lista = "";

			//Recursos da Colônia
			$html .= "<div><h3>Recursos da Colônia</h3>
			<table class='wp-list-table widefat tabela_admin fixed striped users' data-tabela='colonization_planeta_recursos'>
			<thead>
			<tr><th>ID</th><th>Recurso</th><th>Disponível</th><th style='width: 50px;'>Turno</th>
			</tr>
			</thead>
			<tbody>
			";

			foreach ($lista_planeta_recursos as $id) {
				$planeta_recurso = new planeta_recurso($id->id);
				
				$html_dados = $planeta_recurso->lista_dados();

				$html_lista .= "
				<tr>
				{$html_dados}
				</tr>";
			}

			$html.= $html_lista;

			$html .= "\n</tbody>
			</table></div>
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return novo_planeta_recurso(event, {$planeta->id});'>Adicionar novo Recurso</a></div>";

			/*************************************/
			$lista_colonia_instalacoes = $wpdb->get_results(
			"SELECT cpi.id, cpi.id_instalacao 
			FROM colonization_planeta_instalacoes AS cpi 
			JOIN colonization_instalacao AS ci
			ON ci.id = cpi.id_instalacao
			WHERE cpi.id_planeta={$planeta->id}
			ORDER BY ci.nome");
			$html_dados = "";
			$html_lista = "";
			
			//Instalações da Colônia
			$html .= "<br>
			<div><h3>Instalações do Planeta - {$planeta->instalacoes}/{$planeta->tamanho} slots</h3>
			<table class='wp-list-table widefat tabela_admin fixed striped users' data-tabela='colonization_planeta_instalacoes'>
			<thead>
			<tr><th>ID</th><th>Nome</th><th style='width: 40px;'>Nível</th><th style='width: 90px;'>Turno Const.</th><th style='width: 90px;'>Inst. Inicial</th><th style='width: 90px;'>Turno Desmonta</th><th style='width: 90px;'>Turno Destr.</th><th>&nbsp;</th>
			</tr>
			</thead>
			<tbody>
			";

			foreach ($lista_colonia_instalacoes as $id) {
				$planeta_instalacao = new colonia_instalacao($id->id);
				
				$html_dados = $planeta_instalacao->lista_dados();

				$html_lista .= "
				<tr>
				{$html_dados}
				</tr>";
			}
			
			$html.= $html_lista;
			
			$html .= "\n</tbody>
			</table></div>
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return nova_colonia_instalacao(event, {$planeta->id});'>Adicionar nova Instalação</a></div>";
			

			$html .= "<br>
			<div><a href='{$_SERVER['SCRIPT_NAME']}?page={$_GET['page']}'>Voltar às Colônias</a>";
			
		} else {
		
			$html .= "<div><h2>COLONIZATION - Colônias</h2></div>";
			
			//Pega a lista de instalações
			$lista_id = $wpdb->get_results("SELECT 0 as id FROM DUAL
			UNION
			SELECT id FROM colonization_imperio");
			$html_lista = "";
			
			$imperios_npcs = "";
			foreach ($lista_id as $id) {
				$imperio = new imperio($id->id,true);
				if ($imperio->id == 0) {
					$imperio->nome = "Impérios NPCs";
					$imperios_npcs = "<th>Nome do Império</th>";
				}
				//$html_dados = $imperio->lista_dados();
				
				$html .= "<br><div><h3>Colônias de '{$imperio->nome}'</h3></div>";
				
				$html_lista = "			
				<div><table class='wp-list-table widefat tabela_admin fixed striped users' data-tabela='colonization_imperio_colonias' data-id-imperio='{$id->id}' data-nome-imperio='{$imperio->nome}'>
				<thead>
				<tr><th>ID</th>{$imperios_npcs}<th>Planeta</th><th style='width: 80px;'>Capital</th><th style='width: 80px;'>Vassalo</th><th style='width: 100px;'>População</th><th style='width: 100px;'>Robôs</th><th style='width: 100px;'>Poluição</th><th style='width: 100px;'>Satisfação</th><th>Turno</th><th>&nbsp;</th>
				</tr>
				</thead>
				<tbody>";

				$id_estrelas_imperio = $wpdb->get_results("
				SELECT DISTINCT ce.id
				FROM colonization_imperio_colonias AS cic
				JOIN colonization_planeta AS cp
				ON cp.id = cic.id_planeta
				JOIN colonization_estrela AS ce
				ON ce.id = cp.id_estrela
				WHERE cic.id_imperio = {$imperio->id} 
				AND cic.turno = {$turno->turno}
				ORDER BY cic.nome_npc, cic.capital DESC, cic.vassalo ASC, ce.X, ce.Y, ce.Z
				");

				$lista_id_colonias = [];
				foreach ($id_estrelas_imperio as $id_estrela) {
					$resultados_temp =$wpdb->get_results("
					SELECT cic.id 
					FROM colonization_imperio_colonias AS cic
					JOIN colonization_planeta AS cp
					ON cp.id = cic.id_planeta
					JOIN colonization_estrela AS ce
					ON ce.id = cp.id_estrela
					WHERE cic.id_imperio={$imperio->id} AND cic.turno={$turno->turno}
					AND ce.id={$id_estrela->id}
					ORDER BY cic.capital DESC, cic.vassalo ASC, cp.posicao
					");
					
					$lista_id_colonias = array_merge($lista_id_colonias,$resultados_temp);
				}
				
				foreach ($lista_id_colonias as $id_colonia) {
					$colonia = new colonia($id_colonia->id);
					
					$html_dados = $colonia->lista_dados();
					
					$html_lista .= "
					<tr>
					{$html_dados}
					</tr>";
					

				}
				$html_lista .= "\n</tbody>
				</table></div>
				<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return nova_colonia(event, {$imperio->id});'>Adicionar nova Colônia</a></div>";

				$html.= $html_lista;
			
			$imperios_npcs = "";
			}
		}
		
		echo $html;
	}	
	
	/******************
	function colonization_admin_frotas()
	-----------
	Exibe as Frotas dos Impérios
	******************/
	function colonization_admin_frotas() {
		global $wpdb;
		$turno = new turno();
		
		$html = $this->html_header;

		//Códigos das Techs Auxiliares
		$tech = new tech();
		$ids_techs = $tech->query_tech(" AND ct.parte_nave = true AND ct.especiais LIKE '%id=%'");
		$html_javascript = "";
		foreach ($ids_techs AS $id_tech) {
			$tech = new tech($id_tech->id);
			
			$especiais = explode(";",$tech->especiais);
			foreach ($especiais as $chave => $especial) {
				if (str_contains($especial,"id=")) {
					$valor_especial = explode("=",$especial);
					$html_javascript .= "descricao_parte['{$valor_especial[1]}'] = \"{$tech->nome}\";\n";
					break;
				}
			}
		}
		
		$html .= "<script type='text/javascript'>
		{$html_javascript}
		</script>
		<div><h2>COLONIZATION - Frotas dos Impérios</h2></div>";
		
		//Pega a lista de impérios
		$lista_id_imperio = $wpdb->get_results("SELECT id FROM colonization_imperio");
		$html_lista = "";
		
		//Naves dos NPCs
		$html_lista	.= "
		<div><h4>COLONIZATION - Frotas dos NPCs - Turno {$turno->turno}</h4></div>
		<table class='wp-list-table widefat tabela_admin fixed striped users' data-id-imperio='0' data-tabela='colonization_imperio_frota'>
		<thead>
		<tr class='th_linha_1'>
		<th rowspan='2' style='width: 200px;'>Nome do Império</th>
		<th rowspan='2' style='width: 140px;'>Nome da nave</th>
		<th rowspan='2' style='width: 100px;'>Categoria</th>
		<th rowspan='2' style='width: 30px;'>Qtd</th>
		<th colspan='3' style='width: 120px;'>Posição</th>
		<th rowspan='2' style='width: 40px;'>Turno</th>
		<th rowspan='2' style='width: 40px;'>Dest.</th>
		<th rowspan='2' style='width: 80px;'>&nbsp;</th>
		</tr>
		<tr class='th_linha_2'><th style='width: 40px;'>X</th><th class='th_linha_2' style='width: 40px;'>Y</th><th class='th_linha_2' style='width: 40px;'>Z</th>
		</tr>
		</thead>
		<tbody>";
		
		
		$lista_id_frota = $wpdb->get_results("SELECT id FROM colonization_imperio_frota WHERE id_imperio=0");
		foreach ($lista_id_frota as $id_frota) {
			$frota = new frota($id_frota->id);
			
			$html_dados = $frota->lista_dados();
			
			$html_lista .= "
			<tr>
			{$html_dados}
			</tr>";	
		}
		//$html_lista .= $imperio_acoes->lista_dados();
			
		$html_lista .= "</tbody>
		</table>
		<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return nova_nave(event, 0);'>Adicionar nova Nave</a></div>";


		//Naves dos Jogadores
		foreach ($lista_id_imperio as $id) {
			$imperio = new imperio($id->id,true);
			$id_estrela_capital = $wpdb->get_var("
			SELECT cp.id_estrela
			FROM colonization_imperio_colonias AS cic
			JOIN colonization_planeta AS cp
			ON cp.id = cic.id_planeta
			WHERE cic.id_imperio={$imperio->id}
			AND cic.turno={$turno->turno}
			AND cic.capital=true");
			$estrela_capital =  new estrela($id_estrela_capital);
			
			$html_lista	.= "
			<div><h4>COLONIZATION - Frotas do Império '{$imperio->nome}' - Turno {$turno->turno}</h4></div>
			<table class='wp-list-table widefat tabela_admin fixed striped users' data-id-imperio='{$id->id}' data-tabela='colonization_imperio_frota' data-X='{$estrela_capital->X}' data-Y='{$estrela_capital->Y}' data-Z='{$estrela_capital->Z}'>
			<thead>
			<tr class='th_linha_1'><th rowspan='2' style='width: 140px;'>Nome da nave</th>
			<th rowspan='2' style='width: 100px;'>Categoria</th>
			<th rowspan='2' style='width: 30px;'>Qtd</th>
			<th colspan='3' style='width: 120px;'>Posição</th>
			<th rowspan='2' style='width: 90px;'>String da Nave</th>
			<th rowspan='2' style='width: 70px;'>Tamanho</th>
			<th rowspan='2' style='width: 40px;'>HP</th>
			<th rowspan='2' style='width: 75px;'>Velocidade</th>
			<th rowspan='2' style='width: 60px;'>Alcance</th>
			<th colspan='3' style='width: 220px;'>Poder de Fogo</th>
			<th rowspan='2' style='width: 75px;'>Blindagem</th>
			<th rowspan='2' style='width: 60px;'>Escudos</th>
			<th rowspan='2' style='width: 70px;'>Bombard.</th>
			<th rowspan='2' style='width: 60px;'>Invasão</th>
			<th rowspan='2' style='width: 70px;'>Pesquisa</th>
			<th rowspan='2' style='width: 70px;'>Camufl.</th>
			<th rowspan='2' style='width: 100px;'>Estação Orbital</th>
			<th rowspan='2' style='width: 120px;'>Especiais</th>
			<th rowspan='2' style='width: 40px;'>Turno</th>
			<th rowspan='2' style='width: 40px;'>Dest.</th>
			<th rowspan='2' style='width: 80px;'>&nbsp;</th>
			</tr>
			<tr class='th_linha_2'><th style='width: 40px;'>X</th><th style='width: 40px;'>Y</th><th style='width: 40px;'>Z</th><th style='width: 60px;'>Laser</th><th style='width: 60px;'>Torpedo</th><th style='width: 60px;'>Projétil</th></tr>
			</thead>
			<tbody>";
			
			$lista_id_frota = $wpdb->get_results("SELECT id FROM colonization_imperio_frota WHERE id_imperio={$imperio->id} AND turno_destruido=0");
			
			foreach ($lista_id_frota as $id_frota) {
				$frota = new frota($id_frota->id);
				
				$html_dados = $frota->lista_dados();
				
				$html_lista .= "
				<tr>
				{$html_dados}
				</tr>";	
			}
			//$html_lista .= $imperio_acoes->lista_dados();
			
			$html_lista .= "</tbody>
			</table>
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return nova_nave(event, {$imperio->id},{$estrela_capital->X},{$estrela_capital->Y},{$estrela_capital->Z});'>Adicionar nova Nave</a></div>";
		}

		$html .= $html_lista;

		echo $html;
	}


	
	/******************
	function colonization_admin_acoes()
	-----------
	Exibe as ações dos Impérios
	******************/
	function colonization_admin_acoes() {
		global $wpdb, $plugin_colonization;
		$turno = new turno();
		
		$html = $this->html_header;
		
		$html .= "<div><h2>COLONIZATION - Ações dos Impérios</h2></div>";
		
		
		//Pega a lista de impérios
		$lista_id_imperio = $wpdb->get_results("SELECT id FROM colonization_imperio");
		$html_lista = "";
		
		foreach ($lista_id_imperio as $id) {
			//$imperio = new imperio($id->id,true);
			//$imperio_acoes = new acoes($imperio->id,$turno->turno);
			
			//$lista_colonias = $imperio->exibe_lista_colonias();
			//$recursos_atuais = $imperio->exibe_recursos_atuais();
			//$recursos_produzidos = $imperio_acoes->exibe_recursos_produzidos();
			//$recursos_consumidos = $imperio_acoes->exibe_recursos_consumidos();
			//$recursos_balanco = $imperio_acoes->exibe_recursos_balanco();
			
			$atts = [];
			$atts['id'] = $id->id;
			$html_lista .= $plugin_colonization->colonization_exibe_acoes_imperio($atts);
			
			/***
			$html_lista	.= "
			<div><h4>COLONIZATION - Ações do Império '{$imperio->nome}' - Turno {$turno->turno}</h4></div>
			<div id='lista_colonias_imperio_{$imperio->id}'>{$lista_colonias}</div>
			<div id='recursos_atuais_imperio_{$imperio->id}'>{$recursos_atuais}</div>
			<div id='recursos_produzidos_imperio_{$imperio->id}'>{$recursos_produzidos}</div>
			<div id='recursos_consumidos_imperio_{$imperio->id}'>{$recursos_consumidos}</div>
			<div id='recursos_balanco_imperio_{$imperio->id}'>{$recursos_balanco}</div>
			<table class='wp-list-table widefat tabela_admin fixed striped users' data-tabela='colonization_acoes_turno'>
			<thead>
			<tr style='background-color: #E5E5E5; font-weight: 700;'><td style='width: 40%;'>Slots | Colônia (X;Y;Z;P) | 
			(<div class='fas fa-user-circle	tooltip'><span class='tooltiptext'>MdO Disponível Sistema</span></div>)
			<div class='fas fa-user-clock tooltip'><span class='tooltiptext'>MdO</span></div>
			/<div class='fas fa-user tooltip'><span class='tooltiptext'>Pop</span></div>
			</td>
			<td style='width: 35%;'>Instalação</td><td style='width: 35%;'>Utilização (0-10)</td><td style='width: 2%;'>&nbsp;</td></tr>
			</thead>
			<tbody>";
			
			//O objeto "Ações" é diferente dos demais. A lista_dados() não retorna um único objeto, mas sim TODOS as ações do Império
			$html_dados = $imperio_acoes->lista_dados();
			
			$html_lista .= $html_dados;
			
			$html_lista .= "</tbody>
			</table>";
			***/
		}

		$html .= $html_lista;

		echo $html;
	}


//colonization_admin_missoes
	/******************
	function colonization_admin_missoes()
	-----------
	Exibe as Ações do Admin
	******************/
	function colonization_admin_missoes() {
		global $wpdb;

		$html = "<div><h2>COLONIZATION - Missões</h2></div>";
		
		$html .= "<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return nova_missao(event);'>Adicionar nova Missão</a></div><br>
		<div><table class='wp-list-table widefat tabela_admin fixed striped users' data-tabela='colonization_missao'>
		<thead>
		<tr><th style='width: 80px;'>ID</th><th style='width: 140px;'>Descrição</th><th style='width: 140px;'>Vitória</th><th style='width: 140px;'>Derrota</th>
		<th style='width: 60px;'>ID Império</th><th style='width: 120px;'>IDs Aceitam</th><th style='width: 120px;'>IDs Rejeitam</th>
		<th style='width: 40px;'>Ativa</th><th style='width: 60px;'>Turno</th><th style='width: 60px;'>Validade</th>
		<th style='width: 120px;'>IDs Sucesso</th><th style='width: 40px;'>Sucesso</th><th style='width: 40px;'>Obrigatória</th>
		</tr>
		</thead>
		<tbody>";

		$lista_id = $wpdb->get_results("SELECT id FROM colonization_missao ORDER BY ativo DESC, id_imperio, turno");
		$html_lista = "";
		
		foreach ($lista_id as $id) {		
			$missao = new missoes($id->id);
			
			$html_dados = $missao->lista_dados();
			
			$html_lista .= "
			<tr>
			{$html_dados}
			</tr>";
		}

		$html .= $html_lista;
		
		$html .= "\n</tbody>
		</table></div>
		<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return nova_missao(event);'>Adicionar nova Missão</a></div><br>";
		
		echo $html;
	}

	/******************
	function colonization_admin_acoes_admin()
	-----------
	Exibe as Ações do Admin
	******************/
	function colonization_admin_acoes_admin() {
		global $wpdb;

		$html = "<div><h2>COLONIZATION - Ações do Admin</h2></div>
		<div><h3>Lista de Recursos:</h3>";
	
		$lista_recursos = $wpdb->get_results("SELECT id FROM colonization_recurso WHERE acumulavel=true AND local=false ORDER BY nivel, nome");
		$html_lista = "";
		$html_lista_recursos = "";
		
		$recursos_na_linha = 1;
		foreach ($lista_recursos as $id) {		
			$recurso = new recurso($id->id);
			
			if ($recursos_na_linha == 8) {
				$recursos_na_linha = 1;
				$html_lista_recursos .= "<br>";
			}
			
			$html_lista_recursos .= "<div style='display: inline-block; width: 140px; padding: 2px; margin: 2px;'><a href='#' onclick='return inclui_recurso(event, {$recurso->id});'>#{$recurso->id} - {$recurso->nome}</a></div>";
			$recursos_na_linha++;
		}
		
		$html .= $html_lista_recursos."
		</div><br>
		<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return nova_acao_admin(event);'>Adicionar nova Ação Admin</a></div><br>";
		
		$html .= "<div><table class='wp-list-table widefat tabela_admin fixed striped users' data-tabela='colonization_acoes_admin'>
		<thead>
		<tr class='th_linha_1'><th style='width: 120px;'>Nome do Império</th><th style='width: 240px;'>Recursos Consumidos &nbsp; || &nbsp; Qtd</th><th style='width: 200px;'>Descrição</th><th style='width: 50px;'>Turno</th></tr>
		</thead>
		<tbody>";

		$lista_id_acoes = $wpdb->get_results("SELECT id FROM colonization_acoes_admin ORDER BY id_imperio, turno, id");
		$html_lista = "";
		
		foreach ($lista_id_acoes as $id) {		
			$acao_admin = new acao_admin($id->id);
			
			$html_dados = $acao_admin->lista_dados();
			
			$html_lista .= "
			<tr>
			{$html_dados}
			</tr>";
		}

		$html .= $html_lista;
		
		$html .= "\n</tbody>
		</table></div>
		<div><h3>Lista de Recursos:</h3>
		<div>{$html_lista_recursos}</div><br>
		<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return nova_acao_admin(event);'>Adicionar nova Ação Admin</a></div><br>
		<div id='div_resposta'>&nbsp;</div>";
		
		echo $html;
	}

	/******************
	function colonization_admin_roda_turno()
	-----------
	Exibe a opção de rodar turnos
	******************/
	function colonization_admin_roda_turno() {
		global $wpdb;
		$turno = new turno();
		//$roda_turno = new roda_turno();
		
		$html = $this->html_header;
		
		$timezone = new DateTimeZone('America/Sao_Paulo');
		$data_atual = new DateTime("now", $timezone);
		$string_timezone = $data_atual->getTimezone()->getName();
		$string_data_atual = $data_atual->format('Y-m-d H:i:s');

		$proxima_semana = new DateTime($turno->data_turno);
		$proxima_semana->modify('+7 days');
		$proxima_semana_string = $proxima_semana->format('Y-m-d H:i:s');
		
		$html = "<div id='div_turno'><h2>COLONIZATION - RODA TURNO</h2>
		<h3>TURNO ATUAL - {$turno->turno}</h3><br>
		<div>DATA ATUAL - {$string_data_atual}</div>
		<div>DATA DO TURNO ATUAL - {$turno->data_turno}</div>
		<div>DATA DO PRÓXIMO TURNO - {$proxima_semana_string}</div></div>
		<table class='wp-list-table widefat tabela_admin fixed striped users' id='dados_acoes_imperios'>
		<thead>
		<tr><th style='width: 200px;'>Nome do Império</th><th style='width: 200px;'>Dt Última Modificação</th><th style='width: 80px;'>Pontuação</th><th style='width: 100%;'>Balanço dos Recursos</th></tr>
		</thead>
		<tbody>";
		
		//Pega a lista de impérios
		$lista_id_imperio = $wpdb->get_results("SELECT id FROM colonization_imperio");
		$html_lista_imperios = "";
		
		foreach ($lista_id_imperio as $id) {
			$imperio = new imperio($id->id,true);
			$acoes = new acoes($imperio->id,$turno->turno);
			$balanco = $acoes->exibe_recursos_balanco();
			
			$html_lista_imperios .= "<tr><td><div>".$imperio->nome."</div></td><td>{$acoes->max_data_modifica}</td><td>{$imperio->pontuacao}</td><td>{$balanco}</td></tr>";
		}

		$html .= $html_lista_imperios;
		
		$html .= "\n</tbody>
		</table>
		<br>";

		$proximo_turno = $turno->turno + 1;

		if ($turno->encerrado == 0) {
			$diferenca_datas = $data_atual->diff($proxima_semana);
			
			$html .= "<div>{$diferenca_datas->invert} | {$diferenca_datas->h} | {$diferenca_datas->invert} => {$turno->encerrado} </div>";
			if (($diferenca_datas->invert == 1 || $diferenca_datas->h < 15) && $turno->encerrado == 0) {
				$html .= "<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return encerra_turno(event, this);'>Encerrar o Turno</a></div><br><br>";
			} else {
				$data_encerra = new DateTime($proxima_semana_string);
				$data_encerra->modify('-15 hours');
				$data_encerra_string = $data_encerra->format('Y-m-d H:i:s');
				$html .= "<div>TURNO EM ANDAMENTO! O Turno só poderá ser ENCERRADO à partir de {$data_encerra_string}</div>";
			}
		} else {
			$html .= "<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return roda_turno(event);'>Rodar Turno</a></div>";
		}
		
		$html .= "<br>
		<div id='resultado_turno'>&nbsp;</div>";
		//$html = $roda_turno->executa_roda_turno();
		//$html .= $roda_turno->aumenta_pop_colonias();
		
		echo $html;
	}
}

?>