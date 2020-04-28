<?php
/**************************
MENU_ADMIN.PHP
----------------
Responsável pelos menus da área de administração do Colonization
***************************/

class menu_admin {
	public $html_header = "";
	
	function __construct() {
		global $asgarosforum;
		
		$colonization_ajax = new colonization_ajax();
		
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
		add_menu_page('Gerenciar Impérios','Colonization','publish_pages','colonization_admin_menu',array($this,'colonization_admin_menu'),'none');
		add_submenu_page('colonization_admin_menu','Impérios','Impérios','publish_pages','colonization_admin_menu',array($this,'colonization_admin_menu'));
		add_submenu_page('colonization_admin_menu','Estrelas','Estrelas','publish_pages','colonization_admin_estrelas',array($this,'colonization_admin_estrelas'));
		add_submenu_page('colonization_admin_menu','Planetas','Planetas','publish_pages','colonization_admin_planetas',array($this,'colonization_admin_planetas'));
		add_submenu_page('colonization_admin_menu','Recursos','Recursos','publish_pages','colonization_admin_recursos',array($this,'colonization_admin_recursos'));
		add_submenu_page('colonization_admin_menu','Techs','Techs','publish_pages','colonization_admin_techs',array($this,'colonization_admin_techs'));		
		add_submenu_page('colonization_admin_menu','Instalações','Instalações','publish_pages','colonization_admin_instalacoes',array($this,'colonization_admin_instalacoes'));
		add_submenu_page('colonization_admin_menu','Colônias','Colônias','publish_pages','colonization_admin_colonias',array($this,'colonization_admin_colonias'));
		add_submenu_page('colonization_admin_menu','Frotas','Frotas','publish_pages','colonization_admin_frotas',array($this,'colonization_admin_frotas'));
		add_submenu_page('colonization_admin_menu','Ações','Ações','publish_pages','colonization_admin_acoes',array($this,'colonization_admin_acoes'));
		add_submenu_page('colonization_admin_menu','Ações do Admin','Ações do Admin','publish_pages','colonization_admin_acoes_admin',array($this,'colonization_admin_acoes_admin'));
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
			wp_enqueue_script('funcoes_auxiliares_js', '/wp-content/plugins/colonization/js/funcoes_auxiliares.js',false,$hoje);
			wp_enqueue_script('fonte_awesome_js', 'https://kit.fontawesome.com/f748229918.js',false,$hoje);
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
		wp_enqueue_script('gerencia_listas_js', '/wp-content/plugins/colonization/js/listas_js.js',false,$hoje);
		wp_enqueue_script('gerencia_naves_js', '/wp-content/plugins/colonization/js/naves.js',false,$hoje);
		wp_enqueue_script('funcoes_auxiliares_js', '/wp-content/plugins/colonization/js/funcoes_auxiliares.js',false,$hoje);
		wp_enqueue_script('fonte_awesome_js', 'https://kit.fontawesome.com/f748229918.js',false,$hoje);
		wp_enqueue_script('google_charts', 'https://www.gstatic.com/charts/loader.js');
		wp_enqueue_style('colonization_css', '/wp-content/plugins/colonization/colonization.css',false,$hoje);
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


	
	/******************
	function colonization_admin_menu()
	-----------
	Exibe a página principal do plugin, onde você pode criar e editar Impérios (dados básicos)
	******************/
	function colonization_admin_menu() {
		global $wpdb, $wp_admin_bar;
		
		$html = $this->html_header;
		
		if (isset($_GET['id'])) {
			$imperio = new imperio($_GET['id']);
			$imperio_recursos = new imperio_recursos($_GET['id']);
			$html_dados_imperio = $imperio_recursos->lista_dados();

			$html .= "<div><h2>COLONIZATION - Recursos do Império '{$imperio->nome}'</h2></div>
			<div>
			<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_imperio_recursos'>
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
			<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_imperio_techs' style='width: 700px;'>
			<thead>
			<tr><th style='width: 500px;'>Tech</th><th style='width: 150px;'>Pesquisas Gastas<br>(0 = completado)</th><th style='width: 40px;'>Turno</th></tr>
			</thead>
			<tbody>";

			$html .= $html_techs_imperio;

			$html .= "\n</tbody>
			</table></div>
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return nova_tech_imperio(event, {$imperio->id});'>Adicionar nova Tech</a></div>";
			
			$html .= "<br>
			<div><a href='{$_SERVER['SCRIPT_NAME']}?page={$_GET['page']}'>Voltar aos Impérios</a>";			
		} else {
			
			$html .= "<div><h2>COLONIZATION - Impérios</h2></div>
			<div>
			<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_imperio'>
			<thead>
			<tr><th>ID</th><th>Usuário</th><th>Nome do Império</th><th>Prestígio</th><th>População</th><th>Pontuação</th><th>&nbsp;</th></tr>
			</thead>
			<tbody>";
			
			//Pega a lista de impérios
			$lista_id_imperio = $wpdb->get_results("SELECT id, id_jogador FROM colonization_imperio");
			$html_lista_imperios = "";
			
			foreach ($lista_id_imperio as $id) {
				$imperio = new imperio($id->id);
				$acoes = new acoes($id->id);
				
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
			<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_planeta'>
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
			<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_estrela'>
			<thead>
			<tr><th rowspan='2' style='width: 150px;'>ID</th><th rowspan='2'>Nome da estrela</th><th colspan='3' style='width: 150px;'>Posição</th><th rowspan='2'>Tipo de estrela</th><th rowspan='2'>&nbsp;</th></tr>
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

			//Atualiza os recursos da Colônia para o Turno atual, se necessário
			$max_turnos = $wpdb->get_results("SELECT cpr.id_recurso, MAX(cpr.turno) as turno 
			FROM colonization_planeta_recursos AS cpr
			WHERE cpr.id_planeta={$planeta->id} 
			GROUP BY cpr.id_recurso, cpr.id_planeta");

			foreach ($max_turnos as $max_turno) {
				if ($max_turno->turno < $turno->turno) {//Atualiza os recursos do planeta caso não esteja no Turno Atual
					$wpdb->query("UPDATE colonization_planeta_recursos SET turno={$turno->turno} WHERE turno={$max_turno->turno} AND id_planeta={$planeta->id} AND id_recurso={$max_turno->id_recurso}");
				}
			}

			$lista_planeta_recursos = $wpdb->get_results("SELECT id, id_recurso FROM colonization_planeta_recursos WHERE id_planeta={$planeta->id} AND turno={$turno->turno}");
			$html_lista = "";

			//Recursos da Colônia
			$html .= "<div><h3>Recursos do Planeta</h3>
			<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_planeta_recursos'>
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

			$html.= $html_lista;

			$html .= "\n</tbody>
			</table></div>
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return novo_planeta_recurso(event, {$planeta->id});'>Adicionar novo Recurso</a></div>";

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
			<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_planeta_instalacoes'>
			<thead>
			<tr><th>ID</th><th>Nome</th><th style='width: 40px;'>Nível</th><th style='width: 90px;'>Turno Const.</th><th style='width: 90px;'>Turno Destr.</th><th>&nbsp;</th>
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
			<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_planeta'>
			<thead>
			<tr><th style='width: 120px;'>ID</th><th>Nome</th><th>Orbita a Estrela (X;Y;Z)</th><th style='width: 60px;'>Posição</th><th>Classe</th><th>Subclasse</th><th style='width: 70px;'>Tamanho</th><th style='width: 90px;'>Inóspito</th><th>&nbsp;</th>
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
	function colonization_admin_recursos()
	-----------
	Exibe a página de gestão de recursos
	******************/
	function colonization_admin_recursos() {
		global $wpdb;
		
		$html = $this->html_header;
		
		$html .= "<div><h2>COLONIZATION - Recursos</h2></div>
		<div>
		<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_recurso'>
		<thead>
		<tr><th>ID</th><th>Nome</th><th>Descrição</th><th>Nível</th><th>Acumulável</th><th>Extrativo</th><th>Local</th>
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
		<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_tech'>
		<thead>
		<tr>
		<th style='width: 200px;'>ID</th><th style='width: 260px;'>Nome</th><th>Descrição</th><th style='width: 60px;'>Nível</th><th style='width: 60px;'>Custo</th><th style='width: 80px;'>Tech Parente</th><th style='width: 120px;'>Lista Requisitos</th><th style='width: 80px;'>Bélica</th><th style='width: 80px;'>Pública</th><th style='width: 200px;'>Especiais</th><th style='width: 200px;'>Ícone</th>
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
			<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_instalacao_recursos'>
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
			<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_instalacao_recursos'>
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
			<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_instalacao'>
			<thead>
			<tr><th>ID</th><th>Nome</th><th>Descrição</th><th style='width: 40px;'>Slots</th><th>Autônoma</th><th>Desguarnecida</th><th>Oculta</th><th>Ícone</th><th>&nbsp;</th>
			</tr>
			</thead>
			<tbody>";
			
			//Pega a lista de instalações
			$lista_id = $wpdb->get_results("SELECT id FROM colonization_instalacao");
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
			$imperio = new imperio($colonia->id_imperio);
			$planeta = new planeta($colonia->id_planeta);
			
			$html .= "<script>
			var id_colonia={$_GET['id']};
			</script>
			
			<div><h2>COLONIZATION - editando a Colônia '{$planeta->nome}' do Império '{$imperio->nome}'</h2></div>";

			$max_turnos = $wpdb->get_results("SELECT id_recurso, MAX(turno) as turno FROM colonization_planeta_recursos WHERE id_planeta={$planeta->id} GROUP BY id_recurso, id_planeta");
			foreach ($max_turnos as $max_turno) {
				if ($max_turno->turno < $turno->turno) {//Atualiza os recursos do planeta caso não esteja no Turno Atual
					$wpdb->query("UPDATE colonization_planeta_recursos SET turno={$turno->turno} WHERE turno={$max_turno->turno} AND id_planeta={$planeta->id} AND id_recurso={$max_turno->id_recurso}");
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
			<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_planeta_recursos'>
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
			$lista_colonia_instalacoes = $wpdb->get_results("SELECT id, id_instalacao FROM colonization_planeta_instalacoes WHERE id_planeta={$planeta->id}");
			$html_dados = "";
			$html_lista = "";
			
			//Instalações da Colônia
			$html .= "<br>
			<div><h3>Instalações do Planeta - {$planeta->instalacoes}/{$planeta->tamanho} slots</h3>
			<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_planeta_instalacoes'>
			<thead>
			<tr><th>ID</th><th>Nome</th><th style='width: 40px;'>Nível</th><th style='width: 90px;'>Turno Const.</th><th style='width: 90px;'>Turno Destr.</th><th>&nbsp;</th>
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
			$lista_id = $wpdb->get_results("SELECT id FROM colonization_imperio");
			$html_lista = "";
			
			foreach ($lista_id as $id) {
				$imperio = new imperio($id->id);
				//$html_dados = $imperio->lista_dados();
				
				$html .= "<br><div><h3>Colônias do Império '{$imperio->nome}'</h3></div>";
				$lista_id_colonias = $wpdb->get_results("SELECT id FROM colonization_imperio_colonias WHERE id_imperio={$id->id} AND turno={$turno->turno}");

				$html_lista = "			<div><table class='wp-list-table widefat fixed striped users' data-tabela='colonization_imperio_colonias' data-id-imperio='{$id->id}'>
				<thead>
				<tr><th>ID</th><th>Planeta</th><th style='width: 100px;'>População</th><th style='width: 100px;'>Poluição</th><th>Turno</th><th>&nbsp;</th>
				</tr>
				</thead>
				<tbody>";
				
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
		
		$html .= "<div><h2>COLONIZATION - Frotas dos Impérios</h2></div>";
		
		
		//Pega a lista de impérios
		$lista_id_imperio = $wpdb->get_results("SELECT id FROM colonization_imperio");
		$html_lista = "";
		
		foreach ($lista_id_imperio as $id) {
			$imperio = new imperio($id->id);

			
			$html_lista	.= "
			<div><h4>COLONIZATION - Frotas do Império '{$imperio->nome}' - Turno {$turno->turno}</h4></div>
			<table class='wp-list-table widefat fixed striped users' data-id-imperio='{$id->id}' data-tabela='colonization_imperio_frota'>
			<thead>
			<tr><th rowspan='2' style='width: 140px;'>Nome da nave</th>
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
			<th rowspan='2' style='width: 100px;'>Estação Orbital</th>
			<th rowspan='2' style='width: 120px;'>Especiais</th>
			<th rowspan='2' style='width: 40px;'>Turno</th>
			<th rowspan='2' style='width: 80px;'>&nbsp;</th>
			</tr>
			<tr><th class='th_linha_2' style='width: 40px;'>X</th><th class='th_linha_2' style='width: 40px;'>Y</th><th class='th_linha_2' style='width: 40px;'>Z</th><th class='th_linha_2' style='width: 60px;'>Laser</th><th class='th_linha_2' style='width: 60px;'>Torpedo</th><th class='th_linha_2' style='width: 60px;'>Projétil</th></tr>
			</thead>
			<tbody>";
			
			
			$lista_id_frota = $wpdb->get_results("SELECT id FROM colonization_imperio_frota WHERE id_imperio={$imperio->id}");
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
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return nova_nave(event, {$imperio->id});'>Adicionar nova Nave</a></div>";
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
		global $wpdb;
		$turno = new turno();
		
		$html = $this->html_header;
		
		$html .= "<div><h2>COLONIZATION - Ações dos Impérios</h2></div>";
		
		
		//Pega a lista de impérios
		$lista_id_imperio = $wpdb->get_results("SELECT id FROM colonization_imperio");
		$html_lista = "";
		
		foreach ($lista_id_imperio as $id) {
			$imperio = new imperio($id->id);
			$imperio_acoes = new acoes($imperio->id,$turno->turno);
			
			$lista_colonias = $imperio->exibe_lista_colonias();
			$recursos_atuais = $imperio->exibe_recursos_atuais();
			$recursos_produzidos = $imperio_acoes->exibe_recursos_produzidos();
			$recursos_consumidos = $imperio_acoes->exibe_recursos_consumidos();
			
			$html_lista	.= "
			<div><h4>COLONIZATION - Ações do Império '{$imperio->nome}' - Turno {$turno->turno}</h4></div>
			<div id='lista_colonias_imperio_{$imperio->id}'>{$lista_colonias}</div>
			<div id='recursos_atuais_imperio_{$imperio->id}'>{$recursos_atuais}</div>
			<div id='recursos_produzidos_imperio_{$imperio->id}'>{$recursos_produzidos}</div>
			<div id='recursos_consumidos_imperio_{$imperio->id}'>{$recursos_consumidos}</div>
			<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_acoes_turno'>
			<thead>
			<tr><th>Colônia (X;Y;Z) | P</th><th>Instalação</th><th>Utilização (0-10)</th><th>&nbsp;</th></tr>
			</thead>
			<tbody>";
			
			//O objeto "Ações" é diferente dos demais. A lista_dados() não retorna um único objeto, mas sim TODOS as ações do Império
			$html_dados = $imperio_acoes->lista_dados();
			
			$html_lista .= $html_dados;
			
			$html_lista .= "</tbody>
			</table>";
		}

		$html .= $html_lista;

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
	
		$lista_recursos = $wpdb->get_results("SELECT id FROM colonization_recurso ORDER BY nivel, nome");
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
		
		$html .= "<div><table class='wp-list-table widefat fixed striped users' data-tabela='colonization_acoes_admin'>
		<thead>
		<tr><th style='width: 120px;'>Nome do Império</th><th style='width: 240px;'>Recursos Consumidos &nbsp; || &nbsp; Qtd</th><th style='width: 200px;'>Descrição</th><th style='width: 50px;'>Turno</th></tr>
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
		$proxima_semana = new DateTime($turno->data_turno);
		$proxima_semana->modify('+7 days');
		$proxima_semana = $proxima_semana->format('Y-m-d H:i:s');
		
		$html = "<div id='div_turno'><h2>COLONIZATION - RODA TURNO</h2>
		<h3>TURNO ATUAL - {$turno->turno}</h3>
		<div>DATA DO TURNO ATUAL - {$turno->data_turno}</div>
		<div>DATA DO PRÓXIMO TURNO - {$proxima_semana}</div></div>
		<table class='wp-list-table widefat fixed striped users' id='dados_acoes_imperios'>
		<thead>
		<tr><th style='width: 200px;'>Nome do Império</th><th style='width: 200px;'>Dt Última Modificação</th><th style='width: 80px;'>Pontuação</th><th style='width: 100%;'>Balanço dos Recursos</th></tr>
		</thead>
		<tbody>";
		
		//Pega a lista de impérios
		$lista_id_imperio = $wpdb->get_results("SELECT id FROM colonization_imperio");
		$html_lista_imperios = "";
		
		foreach ($lista_id_imperio as $id) {
			$imperio = new imperio($id->id);
			$acoes = new acoes($imperio->id,$turno->turno);
			$balanco = $acoes->exibe_recursos_balanco();
			
			$html_lista_imperios .= "<tr><td><div>".$imperio->nome."</div></td><td>{$acoes->max_data_modifica}</td><td>{$imperio->pontuacao}</td><td>{$balanco}</td></tr>";
		}

		$html .= $html_lista_imperios;
		
		$html .= "\n</tbody>
		</table>
		<br>
		<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return roda_turno(event);'>Rodar Turno</a></div>
		<br>
		<div id='resultado_turno'>&nbsp;</div>";

		//$html = $roda_turno->executa_roda_turno();
		
		echo $html;
	}
}

?>