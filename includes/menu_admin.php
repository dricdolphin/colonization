<?php
/**************************
MENU_ADMIN.PHP
----------------
Responsável pelos menus da área de administração do Colonization
***************************/

class menu_admin {
	public $html_header = "";
	
	function __construct() {
		$colonization_ajax = new colonization_ajax();
		
		//Inicializa os dados básicos, usados na maior parte das funções
		$today = date("YmdHi"); 
		$this->html_header = "<link rel='stylesheet' type='text/css' href='../wp-content/plugins/colonization/colonization.css?v={$today}'>
		<script src='../wp-content/plugins/colonization/includes/novo_objetos.js?v={$today}'></script>
		<script src='../wp-content/plugins/colonization/includes/edita_objetos.js?v={$today}'></script>
		<script src='../wp-content/plugins/colonization/includes/valida_objetos.js?v={$today}'></script>
		<script src='../wp-content/plugins/colonization/includes/gerencia_objeto.js?v={$today}'></script>
		<script>";
		
		$lista = new lista_usuarios();
		$this->html_header .= $lista->html_lista;
		
		$lista = new lista_estrelas();
		$this->html_header .= $lista->html_lista;
		
		$lista = new lista_recursos();
		$this->html_header .= $lista->html_lista;

		$lista = new lista_planetas();
		$this->html_header .= $lista->html_lista;
		
		$lista = new lista_instalacoes();
		$this->html_header .= $lista->html_lista;
		
		$this->html_header .="</script>";
	}

	/******************
	function colonization_setup_menu()
	-----------
	Adiciona o menu do plugin no menu de Admin do WordPress
	******************/
	function colonization_setup_menu() {
		//Adiciona o menu "Colonization", que pode ser acessador por quem tem a opção de Admin ('manage_options')
		add_menu_page('Gerenciar Impérios','Colonization','manage_options','colonization_admin_menu',array($this,'colonization_admin_menu'));
		add_submenu_page('colonization_admin_menu','Impérios','Impérios','manage_options','colonization_admin_menu',array($this,'colonization_admin_menu'));
		add_submenu_page('colonization_admin_menu','Estrelas','Estrelas','manage_options','colonization_admin_estrelas',array($this,'colonization_admin_estrelas'));
		add_submenu_page('colonization_admin_menu','Planetas','Planetas','manage_options','colonization_admin_planetas',array($this,'colonization_admin_planetas'));
		add_submenu_page('colonization_admin_menu','Recursos','Recursos','manage_options','colonization_admin_recursos',array($this,'colonization_admin_recursos'));
		add_submenu_page('colonization_admin_menu','Instalações','Instalações','manage_options','colonization_admin_instalacoes',array($this,'colonization_admin_instalacoes'));
		add_submenu_page('colonization_admin_menu','Colônias','Colônias','manage_options','colonization_admin_colonias',array($this,'colonization_admin_colonias'));
		add_submenu_page('colonization_admin_menu','Ações','Ações','manage_options','colonization_admin_acoes',array($this,'colonization_admin_acoes'));
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
		
		if (isset($_GET['id'])) {
			$imperio = new imperio($_GET['id']);
			$imperio_recursos = new imperio_recursos($_GET['id']);
			$html_dados_imperio = $imperio_recursos->lista_dados();

			$html .= "<div><h2>COLONIZATION - Recursos do Império '{$imperio->nome}'</h2></div>
			<div>
			<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_imperio_recursos'>
			<thead>
			<tr><td>ID</td><td>Recurso</td><td>Qtd</td><td>Disponível</tr></tr>
			</thead>
			<tbody>";

			$html .= $html_dados_imperio;

			$html .= "\n</tbody>
			</table></div>
			<br>
			<div><a href='{$_SERVER['SCRIPT_NAME']}?page={$_GET['page']}'>Voltar aos Impérios</a>";			
		} else {
		
			$html .= "<div><h2>COLONIZATION - Impérios</h2></div>
			<div>
			<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_imperio'>
			<thead>
			<tr><td>ID</td><td>Usuário</td><td>Nome do Império</td><td>População</td><td>Pontuação</td><td>&nbsp;</td></tr>
			</thead>
			<tbody>";
			
			//Pega a lista de impérios
			$lista_id_imperio = $wpdb->get_results("SELECT id, id_jogador FROM colonization_imperio");
			$html_lista_imperios = "";
			
			foreach ($lista_id_imperio as $id) {
				$user = get_user_by('ID',$id->id_jogador); //Pega todos os usuários
				$imperio = new imperio($id->id);
				
				$html_dados_imperio = $imperio->lista_dados();
				//TODO -- Calcular Pontuação
				$pontuacao = 999;
				
				$html_lista_imperios .= "
				<tr>
				{$html_dados_imperio}
				</tr>";
			}
			
			$html.= $html_lista_imperios;
			
			$html .= "\n</tbody>
			</table></div>
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='novo_imperio();'>Adicionar novo Império</a></div>";
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
			<tr><td>Nome</td><td>Orbita a Estrela (X;Y;Z)</td><td style='width: 50px;'>Posição</td><td>Classe</td><td>Subclasse</td><td style='width: 60px;'>Tamanho</td><td>&nbsp;</td>
			</tr>
			</thead>
			<tbody>";

			//Pega a lista de planetas
			$lista_id = $wpdb->get_results("SELECT id FROM colonization_planeta WHERE id_estrela={$estrela->id} ORDER BY posicao");
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
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='novo_planeta({$estrela->id});'>Adicionar novo Planeta</a></div>
			<br>
			<div><a href='{$_SERVER['SCRIPT_NAME']}?page={$_GET['page']}'>Voltar às Estrelas</a>";
		
		} else {
			$html .= "<div><h2>COLONIZATION - Estrelas</h2></div>
			<div>
			<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_estrela'>
			<thead>
			<tr><th rowspan='2'>Nome da estrela</th><th colspan='3' style='width: 150px;'>Posição</th><th rowspan='2'>Tipo de estrela</th><th rowspan='2'>&nbsp;</th></tr>
			<tr><th style='width: 30px;'>X</th><th style='width: 30px;'>Y</th><th style='width: 30px;'>Z</th></tr>
			
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
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='nova_estrela();'>Adicionar nova Estrela</a></div>";
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
		
		$html = $this->html_header;
		
		if (isset($_GET['id'])) {
			$planeta = new planeta($_GET['id']);
			
			$html .= "<script>
			var id_colonia={$_GET['id']};
			</script>
			
			<div><h2>COLONIZATION - editando o Planeta '{$planeta->nome}'</h2></div>";

			$lista_colonia_recursos = $wpdb->get_results("SELECT id, id_recurso, MAX(turno) FROM colonization_planeta_recursos WHERE id_planeta={$planeta->id} GROUP BY id_recurso");
			$html_lista = "";

			//Recursos da Colônia
			$html .= "<div><h3>Recursos do Planeta</h3>
			<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_planeta_recursos'>
			<thead>
			<tr><td>ID</td><td>Recurso</td><td>Disponível</td>
			</tr>
			</thead>
			<tbody>
			";

			foreach ($lista_colonia_recursos as $id) {
				$planeta_recurso = new colonia_recurso($id->id);
				$html_dados = $planeta_recurso->lista_dados();

				$html_lista .= "
				<tr>
				{$html_dados}
				</tr>";
			}

			$html.= $html_lista;

			$html .= "\n</tbody>
			</table></div>
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='novo_colonia_recurso({$planeta->id});'>Adicionar novo Recurso</a></div>";

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
			<tr><td>ID</td><td>Nome</td><td style='width: 40px;'>Nível</td><td style='width: 90px;'>Turno Const.</td><td style='width: 90px;'>Turno Destr.</td><td>&nbsp;</td>
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
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='nova_colonia_instalacao({$planeta->id});'>Adicionar nova Instalação</a></div>";
			

			$html .= "<br>
			<div><a href='{$_SERVER['SCRIPT_NAME']}?page={$_GET['page']}'>Voltar aos Planetas</a>";
		} else {
			$html .= "<div><h2>COLONIZATION - Planetas</h2></div>
			<div>
			<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_planeta'>
			<thead>
			<tr><td>Nome</td><td>Orbita a Estrela (X;Y;Z)</td><td style='width: 50px;'>Posição</td><td>Classe</td><td>Subclasse</td><td style='width: 60px;'>Tamanho</td><td>&nbsp;</td>
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
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='novo_planeta();'>Adicionar novo Planeta</a></div>";
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
		<tr><td>Nome</td><td>Descrição</td><td>Acumulável</td>
		</tr>
		</thead>
		<tbody>";
		
		//Pega a lista de recursos
		$lista_id = $wpdb->get_results("SELECT id FROM colonization_recurso");
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
		<div><a href='#' class='page-title-action colonization_admin_botao' onclick='novo_recurso();'>Adicionar novo Recurso</a></div>";
		
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
			<tr><td>ID</td><td>Recurso</td><td>Quantidade por Nível</td>
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
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='novo_instalacao_recurso(0);'>Adicionar novo recurso PRODUZIDO</a></div>";
			
			/*************************************/
			
			$lista_instalacao_recursos = $wpdb->get_results("SELECT id FROM colonization_instalacao_recursos WHERE id_instalacao={$instalacao->id} AND consome=1");
			$html_dados = "";
			$html_lista = "";

			//Recursos consumidos
			$html .= "<div><h3>Recursos Consumidos</h3>
			<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_instalacao_recursos'>
			<thead>
			<tr><td>ID</td><td>Recurso</td><td>Quantidade por Nível</td>
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
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='novo_instalacao_recurso(1);'>Adicionar novo recurso CONSUMIDO</a></div>
			<br>
			<div><a href='{$_SERVER['SCRIPT_NAME']}?page={$_GET['page']}'>Voltar às Instalações</a>";
			
		} else {
			$html .= "<div><h2>COLONIZATION - Instalações</h2></div>
			<div>
			<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_instalacao'>
			<thead>
			<tr><td>Nome</td><td>Descrição</td><td>&nbsp;</td>
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
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='nova_instalacao();'>Adicionar nova Instalação</a></div>";
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

		if (isset($_GET['id'])) {
			$colonia = new colonia($_GET['id']);
			$imperio = new imperio($colonia->id_imperio);
			$planeta = new planeta($colonia->id_planeta);
			
			$html .= "<script>
			var id_colonia={$_GET['id']};
			</script>
			
			<div><h2>COLONIZATION - editando a Colônia '{$planeta->nome}' do Império '{$imperio->nome}'</h2></div>";

			$lista_colonia_recursos = $wpdb->get_results("SELECT id, id_recurso, MAX(turno) FROM colonization_planeta_recursos WHERE id_planeta={$planeta->id} GROUP BY id_recurso");
			$html_lista = "";

			//Recursos da Colônia
			$html .= "<div><h3>Recursos da Colônia</h3>
			<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_planeta_recursos'>
			<thead>
			<tr><td>ID</td><td>Recurso</td><td>Disponível</td>
			</tr>
			</thead>
			<tbody>
			";

			foreach ($lista_colonia_recursos as $id) {
				$planeta_recurso = new colonia_recurso($id->id);
				$html_dados = $planeta_recurso->lista_dados();

				$html_lista .= "
				<tr>
				{$html_dados}
				</tr>";
			}

			$html.= $html_lista;

			$html .= "\n</tbody>
			</table></div>
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='novo_colonia_recurso({$planeta->id});'>Adicionar novo Recurso</a></div>";

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
			<div><h3>Instalações da Colônia - Máximo de {$max_instalacoes}</h3>
			<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_planeta_instalacoes'>
			<thead>
			<tr><td>ID</td><td>Nome</td><td style='width: 40px;'>Nível</td><td style='width: 90px;'>Turno Const.</td><td style='width: 90px;'>Turno Destr.</td><td>&nbsp;</td>
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
			<div><a href='#' class='page-title-action colonization_admin_botao' onclick='nova_colonia_instalacao({$planeta->id});'>Adicionar nova Instalação</a></div>";
			

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
				$lista_id_colonias = $wpdb->get_results("SELECT id FROM colonization_imperio_colonias WHERE id_imperio={$id->id}");

				$html_lista = "			<div><table class='wp-list-table widefat fixed striped users' data-tabela='colonization_imperio_colonias' data-id-imperio='{$id->id}'>
				<thead>
				<tr><td>ID</td><td>Planeta</td><td style='width: 100px;'>População</td><td style='width: 100px;'>Poluição</td><td>Turno</td><td>&nbsp;</td>
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
				<div><a href='#' class='page-title-action colonization_admin_botao' onclick='nova_colonia({$imperio->id});'>Adicionar nova Colônia</a></div>";

				$html.= $html_lista;
			}
		}
		
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
			
			//TODO -- Pega a data da última ação
			$html_lista	.= "
			<div><h4>COLONIZATION - Ações do Império '{$imperio->nome}' - Turno {$turno->turno}</h4></div>
			<div><b>Recursos Atuais:</b></div>
			<div><b>Recursos Produzidos:</b></div>
			<div><b>Recursos Consumidos:</b></div>
			<table class='wp-list-table widefat fixed striped users' data-tabela='colonization_acoes_turno'>
			<thead>
			<tr><td>Colônia (X;Y;Z)</td><td>Instalação</td><td>Utilização (0-10)</td><td>&nbsp;</td></tr>
			</thead>
			<tbody>";
			
			//TODO -- Pegar Colônias e Instalações
			$html_lista .= "<tr><td><div>XPTO (1;2;3)</div></td><td><div>Instalação XPTO</div></td><td><div><input type='range' min='0' max='10' value='5'></input></div></td>
			<td><div><a href='#' onclick='salva_objeto(this);'>Salvar</a> | <a href='#' onclick='cancela_edicao(this);'>Cancelar</a></div></td>
			</tr>";
			
			
			$html_lista .= "</tbody>
			</table>";
		}

		$html .= $html_lista;

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
		
		$html = $this->html_header;
		
		$html = "<div><h2>COLONIZATION - RODA TURNO</h2></div>
		<br>
		<div><b>TURNO ATUAL - {$turno->turno}</b><br>
		DATA DO ÚLTIMO TURNO - {$turno->data_turno}</div>
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
			//TODO -- Pega a data da última ação
			$data_ultima_acao = "01/01/2020";
			$html_lista_imperios .= "<tr><td><div>".$imperio->nome."</div><div><a href='#' onclick='edita_acoes({$id->id_jogador});'>Editar ações</a></div></td><td>{$data_ultima_acao}</td><td>{$imperio->pontuacao}</td></tr>";
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

?>