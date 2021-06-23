<?php
/**************************
ACOES.PHP
----------------
Cria o objeto "ações", que contém todas 
as ações de um determinado turno de um jogador.
***************************/

//Classe "acoes"
//Contém os dados da instalacao
class acoes 
{
	public $id = [];
	public $imperio;
	public $id_imperio;
	public $id_planeta = [];
	public $id_colonia = [];
	public $id_instalacao = [];
	public $id_planeta_instalacoes = [];
	public $nivel_instalacao = [];
	public $turno_desmonta = [];
	public $turno_destroi = [];
	public $pop = [];
	public $desativado = [];
	public $turno;
	public $data_modifica = [];
	public $max_data_modifica;
	
	//Recursos produzidos, consumidos e balanços
	public $recursos_produzidos_planeta_instalacao = []; //Número de Instalações, por planeta, que geram um determinado recurso
	public $recursos_produzidos_planeta_instalacao_bonus = []; //Número de Instalações, por planeta, que geram um determinado recurso
	public $recursos_produzidos_planeta_bonus = []; //Bônus de recursos, por planeta
	public $recursos_produzidos = [];
	public $recursos_produzidos_planeta = [];
	public $recursos_produzidos_id_planeta_instalacoes = [];
	public $recursos_produzidos_nome = [];
	
	public $recursos_extraidos_planeta = [];
	
	public $recursos_consumidos = [];
	public $recursos_consumidos_planeta = [];
	public $recursos_consumidos_id_planeta_instalacoes = [];
	public $recursos_consumidos_nome = [];
	public $recursos_balanco = [];
	public $recursos_balanco_planeta = [];
	public $recursos_balanco_nome = [];
	public $disabled = "";
	
	public $debug = "";
	
	
	function __construct($id_imperio, $turno=0, $sem_balanco=false) {
		global $wpdb;
		
		//É necessário pegar o id_imperio à partir do objeto "Império", pois este contém a validação do jogador
		$this->turno = new turno($turno);
		if ($turno > $this->turno->turno) {
			$this->disabled = 'disabled';
		}
					
		//$this->imperio = new imperio($id_imperio, false, $this->turno->turno);
		$this->id_imperio = $id_imperio;

		$id_estrelas_imperio = $wpdb->get_results("
		SELECT DISTINCT ce.id
		FROM colonization_imperio_colonias AS cic
		JOIN colonization_planeta AS cp
		ON cp.id = cic.id_planeta
		JOIN colonization_estrela AS ce
		ON ce.id = cp.id_estrela
		WHERE cic.id_imperio = {$this->id_imperio} 
		AND cic.turno = {$this->turno->turno}
		ORDER BY cic.capital DESC, cic.vassalo ASC, ce.X, ce.Y, ce.Z
		");

		$resultados = [];
		$index = 0;
		foreach ($id_estrelas_imperio as $id_estrela) {
			$resultados_temp = $wpdb->get_results("
				SELECT cic.id AS id_colonia, cic.id_imperio, cat.id AS id, cic.id_planeta AS id_planeta, ci.pode_desativar, ci.desguarnecida,
				cpi.id AS id_planeta_instalacoes, cpi.id_instalacao AS id_instalacao, cpi.nivel AS nivel_instalacao, cpi.turno_destroi AS turno_destroi, 
				cpi.turno_desmonta AS turno_desmonta,
				cat.pop AS pop, cat.desativado AS desativado, cat.data_modifica AS data_modifica
				FROM colonization_imperio_colonias AS cic 
				JOIN colonization_planeta_instalacoes AS cpi
				ON cpi.id_planeta = cic.id_planeta
				LEFT JOIN 
				(SELECT id, id_planeta, id_instalacao, id_planeta_instalacoes, id_imperio, pop, desativado, data_modifica
				 FROM colonization_acoes_turno
				 WHERE id_imperio={$this->id_imperio} AND turno={$this->turno->turno}
				) AS cat
				ON cat.id_planeta = cic.id_planeta
				AND cat.id_instalacao = cpi.id_instalacao
				AND cat.id_planeta_instalacoes = cpi.id
				AND cat.id_imperio = cic.id_imperio
				JOIN colonization_instalacao AS ci
				ON ci.id = cpi.id_instalacao
				JOIN colonization_planeta AS cp
				ON cp.id = cic.id_planeta
				JOIN colonization_estrela AS ce
				ON ce.id = cp.id_estrela
				WHERE cic.id_imperio = {$this->id_imperio} 
				AND cic.turno = {$this->turno->turno}
				AND cpi.turno <={$this->turno->turno}
				AND ce.id = {$id_estrela->id}
				ORDER BY cic.capital DESC, cic.vassalo ASC, cp.posicao, cpi.id_planeta, (CASE WHEN ci.nome='Espaçoporto' OR ci.nome='Base Colonial' THEN 0 ELSE 1 END), ci.nome, cpi.id
				");
			
			$resultados = array_merge($resultados, $resultados_temp);
		}

		$chave = 0;
		foreach ($resultados as $valor) {
			if ($valor->id === null) {
				$this->id[$chave] = 0;
				$this->id_planeta[$chave] = $valor->id_planeta;
				$this->id_colonia[$chave] = $valor->id_colonia;
				$this->id_instalacao[$chave] = $valor->id_instalacao;
				$this->id_planeta_instalacoes[$chave] = $valor->id_planeta_instalacoes;
				$this->nivel_instalacao[$chave] = $valor->nivel_instalacao;
				$this->pop[$chave] = 0;
				$this->desativado[$chave] = 0;
				if ($valor->desguarnecida == 1 && $valor->pode_desativar == 1) {//Instalações novas que sejam desguarnecidas e possam ser desativadas começam DESATIVADAS
					$this->desativado[$chave] = 1;
				}
				$this->turno_destroi[$chave] = "";
				$this->turno_desmonta[$chave] = "";
				$this->data_modifica[$chave] = $this->turno->data_turno;
			} else {
				$this->id[$chave] = $valor->id;
				$this->id_planeta[$chave] = $valor->id_planeta;
				$this->id_colonia[$chave] = $valor->id_colonia;
				$this->id_instalacao[$chave] = $valor->id_instalacao;
				$this->id_planeta_instalacoes[$chave] = $valor->id_planeta_instalacoes;
				$this->nivel_instalacao[$chave] = $valor->nivel_instalacao;
				$this->pop[$chave] = intval($valor->pop);
				$this->desativado[$chave] = $valor->desativado;
				if ($this->desativado[$chave] == 1) {
					$this->pop[$chave] = 0;
				}
				$this->turno_destroi[$chave] = "";
				$this->turno_desmonta[$chave] = $valor->turno_desmonta;
				$this->data_modifica[$chave] = $valor->data_modifica;
				
				$turno_upgrade = $wpdb->get_var("SELECT MIN(turno) FROM colonization_planeta_instalacoes_upgrade WHERE id_planeta_instalacoes={$this->id_planeta_instalacoes[$chave]} AND turno > {$this->turno->turno}");
				if ($turno_upgrade > $this->turno->turno) {
					$this->nivel_instalacao[$chave] = $wpdb->get_var("SELECT nivel_anterior FROM colonization_planeta_instalacoes_upgrade WHERE id_planeta_instalacoes={$this->id_planeta_instalacoes[$chave]} AND turno = {$turno_upgrade}");
					$this->id_instalacao[$chave] = $wpdb->get_var("SELECT id_instalacao_anterior FROM colonization_planeta_instalacoes_upgrade WHERE id_planeta_instalacoes={$this->id_planeta_instalacoes[$chave]} AND turno = {$turno_upgrade}");
				}

				if ($valor->turno_destroi <= $this->turno->turno) {
					$this->turno_destroi[$chave] = $valor->turno_destroi;
				}
				
				if ($this->turno_destroi[$chave] != "" && $this->turno_destroi[$chave] != 0 && $this->turno_destroi[$chave] >= $this->turno->turno) {
					$this->pop[$chave] = 0;
				}
			}
			$chave++;
		}
		
		$chave = 0;
		//*****
		if (isset($this->id[$chave])) {
			foreach ($this->id as $chave => $valor) {
				if ($this->id[$chave] == 0) {//As chaves estão em branco, vamos criá-las!
					//Se tiver um valor no turno anterior, é para mantê-lo no turno atual
					$turno_anterior = $this->turno->turno - 1;
					$pop_turno_anterior = $wpdb->get_var("SELECT pop FROM colonization_acoes_turno 
					WHERE id_imperio={$this->id_imperio} AND id_planeta={$this->id_planeta[$chave]} AND id_instalacao={$this->id_instalacao[$chave]} AND turno={$turno_anterior} AND id_planeta_instalacoes={$this->id_planeta_instalacoes[$chave]}");
					$desativado_turno_anterior = $wpdb->get_var("SELECT desativado FROM colonization_acoes_turno 
					WHERE id_imperio={$this->id_imperio} AND id_planeta={$this->id_planeta[$chave]} AND id_instalacao={$this->id_instalacao[$chave]} AND turno={$turno_anterior} AND id_planeta_instalacoes={$this->id_planeta_instalacoes[$chave]}");					
					
					if ($pop_turno_anterior === null) {
						$wpdb->query("INSERT INTO colonization_acoes_turno 
						SET id_imperio={$this->id_imperio}, id_planeta={$this->id_planeta[$chave]}, id_instalacao={$this->id_instalacao[$chave]}, id_planeta_instalacoes={$this->id_planeta_instalacoes[$chave]},
						pop={$this->pop[$chave]}, desativado={$this->desativado[$chave]}, data_modifica='{$this->data_modifica[$chave]}', turno={$this->turno->turno}");
					} else {
						$wpdb->query("INSERT INTO colonization_acoes_turno 
						SET id_imperio={$this->id_imperio}, id_planeta={$this->id_planeta[$chave]}, id_instalacao={$this->id_instalacao[$chave]}, id_planeta_instalacoes={$this->id_planeta_instalacoes[$chave]},
						pop={$pop_turno_anterior}, desativado={$desativado_turno_anterior}, data_modifica='{$this->data_modifica[$chave]}', turno={$this->turno->turno}");
						$this->pop[$chave] = $pop_turno_anterior;
						$this->desativado[$chave] == $desativado_turno_anterior;
					}
					$this->id[$chave] = $wpdb->insert_id;
				}
			}
		}
		//*****/
		
		if ($sem_balanco === false) {
			$this->pega_balanco_recursos();
		}
		
		$this->max_data_modifica = $wpdb->get_var("SELECT MAX(data_modifica) FROM colonization_acoes_turno WHERE id_imperio={$this->id_imperio} AND turno={$this->turno->turno}");
	}

	/***********************
	function mdo_planeta()
	----------------------
	Exibe a MdO alocada em um planeta
	----
	$id_planeta = planeta escolhido
	***********************/
	function mdo_planeta($id_planeta) {
		global $wpdb;
		
		$mdo = 0;

		/***
		$chaves = array_keys($this->id_planeta, $id_planeta);
		$mdo = array_intersect_key($this->pop, $chaves);
		$mdo = array_sum($mdo);
		//***/
		
		for ($chave=0; $chave < count($this->id_planeta); $chave++) {
			if ($this->id_planeta[$chave] == $id_planeta) {
				$mdo = $mdo + intval($this->pop[$chave]);
			}
		}

		return $mdo;
	}


	/***********************
	function exibe_pop_mdo_planeta()
	----------------------
	Exibe a os dados de Pop e MdO de um planeta
	----
	$id_planeta = planeta escolhido
	***********************/	
	function exibe_pop_mdo_planeta($id_planeta, $imperio = [], $planeta = []) {
		global $wpdb, $start_time;
		
		//$planeta = new planeta($id_planeta, $this->turno->turno);
		$id_colonia = $wpdb->get_var("SELECT id FROM colonization_imperio_colonias WHERE id_planeta={$id_planeta} AND id_imperio={$this->id_imperio} AND turno={$this->turno->turno}");
		$id_estrela = $wpdb->get_var("SELECT cp.id_estrela
		FROM colonization_imperio_colonias AS cic
		JOIN colonization_planeta AS cp
		ON cp.id=cic.id_planeta
		WHERE cic.id={$id_colonia}");
		$colonia = new colonia($id_colonia, $this->turno->turno);
		if (!empty($planeta)) {
			$colonia->planeta = $planeta;
		}
		$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
		$this->debug .= "exibe_pop_mdo_planeta => new colonia({$id_colonia}) {$diferenca}ms\n";		
		
		$mdo_planeta = $this->mdo_planeta($colonia->id_planeta);
		$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
		$this->debug .= "exibe_pop_mdo_planeta => mdo_planeta() {$diferenca}ms\n";
		
		$pop_sistema = $this->pop_mdo_sistema($id_estrela);
		$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
		$this->debug .= "exibe_pop_mdo_planeta => pop_mdo_sistema() {$diferenca}ms\n";
		
		$pop_disponivel_sistema = $pop_sistema['pop'] - $pop_sistema['mdo'];		

		$id_alimento = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome = 'Alimentos'");
		if (empty($imperio) && empty($this->imperio)) {
			$imperio = new imperio($this->id_imperio, $this->turno->turno);
			$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
			$this->debug .= "exibe_pop_mdo_planeta => new imperio({$this->id_imperio}) {$diferenca}ms\n";
			$this->imperio = $imperio;
		} elseif (empty($imperio) && !empty($this->imperio)) {
			$imperio = $this->imperio;
		}

		$imperio_recursos = new imperio_recursos($imperio->id, $this->turno->turno);
		if (empty($imperio->acoes)) {
			$imperio->acoes = $this;
		}			
		
		$chave_alimento = array_search($id_alimento, $imperio_recursos->id_recurso);
		$alimentos = floor($this->recursos_balanco[$id_alimento] + $imperio_recursos->qtd[$chave_alimento]);
		$nova_pop = $colonia->crescimento_colonia($imperio, $alimentos, $this->recursos_balanco[$id_alimento]);
		$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
		$this->debug .= "exibe_pop_mdo_planeta => colonia->crescimento_colonia() {$diferenca}ms\n";		

		$html_nova_pop = "";
		if ($nova_pop > 0) {
			$html_nova_pop = " (<span class='tooltip'><span class='tooltiptext'>Crescimento Populacional</span><span style='color: green; font-family: Verdana, Tahoma, sans-serif;'>+{$nova_pop}</span></span>)";	
		} elseif ($nova_pop < 0) {
			$html_nova_pop = " (<span class='tooltip'><span class='tooltiptext'>Crescimento Populacional</span><span style='color: red; font-family: Verdana, Tahoma, sans-serif;'>{$nova_pop}</span></span>)";	
		}

		return "<div style='display: inline-block;' name='mdo_sistema_{$id_estrela}'>({$pop_disponivel_sistema})</div> {$mdo_planeta}/{$colonia->pop}{$html_nova_pop}";
	}

	/***********************
	function pop_mdo_sistema()
	----------------------
	Exibe o MdO de um Sistema Estelar que o Império controla
	***********************/	
	function pop_mdo_sistema ($id_estrela) {
		global $wpdb;
		
		$resultados = $wpdb->get_results("
		SELECT cic.id AS id_colonia, cic.pop, cic.pop_robotica, cic.id_planeta
		FROM colonization_imperio_colonias AS cic
		JOIN colonization_planeta AS cp
		ON cp.id=cic.id_planeta
		JOIN colonization_estrela AS ce
		ON ce.id=cp.id_estrela
		WHERE cic.id_imperio = {$this->id_imperio}
		AND cic.turno = {$this->turno->turno}
		AND cp.id_estrela={$id_estrela}
		ORDER BY cic.capital DESC, cic.vassalo ASC, ce.X, ce.Y, ce.Z, cp.posicao, cic.id_planeta
		");

		if (empty($resultados)) {
			$resposta = [];
			$resposta['pop'] = 0;
			$resposta['mdo'] = 0;

			return $resposta;
		}
		
		$mdo_sistema = 0;
		$pop_sistema = 0;
		foreach ($resultados as $resultado) {
			//$colonia = new colonia($resultado->id_colonia, $this->turno->turno);
			
			//$planeta = $colonia->planeta;
			//$estrela = $colonia->estrela;

			$mdo = $this->mdo_planeta($resultado->id_planeta);
			$mdo_sistema = $mdo_sistema + $mdo;
			$pop_sistema = $pop_sistema + $resultado->pop + $resultado->pop_robotica;
		}

		$resposta = [];
		$resposta['pop'] = $pop_sistema;
		$resposta['mdo'] = $mdo_sistema;
		
		return $resposta;
	}

	/***********************
	function defesa_sistema()
	----------------------
	Exibe a Defesa de Sistema Estelar de uma Estrela específica
	***********************/	
	function defesa_sistema($id_estrela) {
		global $wpdb;

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];	
			$banido = get_user_meta($user->ID, 'asgarosforum_role', true);
			if ($banido === "banned") {
				$banido = true;
			} else {
				$banido = false;
			}
		}

		
		$resultados = $wpdb->get_results("
		SELECT cic.id AS id_colonia
		FROM colonization_imperio_colonias AS cic
		JOIN colonization_planeta AS cp
		ON cp.id=cic.id_planeta
		JOIN colonization_estrela AS ce
		ON ce.id=cp.id_estrela
		WHERE cic.id_imperio = {$this->id_imperio}
		AND cic.turno = {$this->turno->turno}
		AND cp.id_estrela={$id_estrela}
		ORDER BY cic.capital DESC, cic.vassalo ASC, ce.X, ce.Y, ce.Z, cp.posicao, cic.id_planeta
		");

		$defesa_sistema['torpedos'] = 0;
		$defesa_sistema['torpedeiros'] = 0;
		$defesa_sistema['minas_subespaciais'] = 0;
		if (empty($resultados)) {
			return $defesa_sistema ;
		}

		foreach ($resultados as $resultado) {
			$colonia = new colonia($resultado->id_colonia, $this->turno->turno);
			
			$defesa_sistema['torpedos'] = $defesa_sistema['torpedos'] + $colonia->qtd_defesas;
			$defesa_sistema['torpedeiros'] = $defesa_sistema['torpedeiros'] + $colonia->bonus_torpedeiros();
			$defesa_sistema['minas_subespaciais'] = $defesa_sistema['minas_subespaciais'] + $colonia->minas_subespaciais();
		}
		$defesa_sistema['torpedeiros'] = $defesa_sistema['torpedeiros'] + $defesa_sistema['torpedos'];

		return $defesa_sistema;
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	----
	$turno_atual = somente libera para edição se o Turno exibido for o Turno atual
	***********************/
	function lista_dados($turno_atual=true) {
		global $wpdb, $plugin_colonization, $start_time;
		
		$html = "";
		$ultimo_planeta = 0;
		$estilo_par = "style='background-color: #FAFAFA;'";
		$estilo_impar = "style='background-color: #F0F0F0;'";
		$primeira_linha = "&nbsp;";
		
		$estilo = $estilo_impar;
		$instalacao = [];
		$estrelas = [];
		$planetas = [];
		$colonias = [];

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];	
			$banido = get_user_meta($user->ID, 'asgarosforum_role', true);
			if ($banido === "banned") {
				$banido = true;
			} else {
				$banido = false;
			}
		}
		
		$imperio = new imperio($this->id_imperio);
		$imperio_recursos = new imperio_recursos($imperio->id, $this->turno->turno);
		$id_alimento = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome = 'Alimentos'");
		$id_industrializaveis = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='Industrializáveis'");		
		foreach ($this->id AS $chave => $valor) {
			if (empty($instalacao[$this->id_instalacao[$chave]])) {
				$instalacao[$this->id_instalacao[$chave]] = new instalacao($this->id_instalacao[$chave]);
				$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
				$this->debug .= "acoes->lista_dados => new instalacao({$this->id_instalacao[$chave]}) {$diferenca}ms\n";
			}
			//$colonia_instalacao = new colonia_instalacao($this->id_planeta_instalacoes[$chave]);
			
			if ($instalacao[$this->id_instalacao[$chave]]->oculta != 0 || ($this->turno_desmonta[$chave] <= $this->turno->turno && !empty($this->turno_desmonta[$chave]))) {
				continue; //Caso seja uma instalação oculta ou se tiver sido desmontada, deve pular
			}
			
			$html_upgrade = "";
			$this->disabled = "";
			
			$visivel = "";
			$turno_atual = new turno();
			if (($this->turno->turno != $turno_atual->turno) || ($this->turno->encerrado == 1 && $roles != "administrator") || $banido) {
					$this->disabled = "disabled";
					$visivel = "style='display: none;'";
			}


			if ($ultimo_planeta != $this->id_planeta[$chave]) {
				if (empty($planetas[$this->id_planeta[$chave]])) {
					$planetas[$this->id_planeta[$chave]] = new planeta($this->id_planeta[$chave], $this->turno->turno);
					$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
					$this->debug .= "acoes->lista_dados => new planeta({$this->id_planeta[$chave]}) {$diferenca}ms\n";
				}
				$planeta = $planetas[$this->id_planeta[$chave]];
				$html_recursos_planeta = $planeta->exibe_recursos_planeta();
				$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
				$this->debug .= "acoes->lista_dados => planeta->exibe_recursos_planeta() {$diferenca}ms\n";				
				
				if (empty($estrelas[$planeta->id_estrela])) {
					$estrelas[$planeta->id_estrela]= new estrela($planeta->id_estrela);
					$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
					$this->debug .= "acoes->lista_dados => new estrela({$planeta->id_estrela}) {$diferenca}ms\n";
				}
				$estrela = $estrelas[$planeta->id_estrela];
				
				if (empty($colonias[$this->id_colonia[$chave]])) {
					$colonias[$this->id_colonia[$chave]] = new colonia($this->id_colonia[$chave], $this->turno->turno);
					$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
					$this->debug .= "acoes->lista_dados => new colonia({$this->id_colonia[$chave]}) {$diferenca}ms\n";
				}
				$colonia = $colonias[$this->id_colonia[$chave]];

				$html_nova_instalacao_jogador = "<div data-atributo='link_nova_instalacao_jogador' class='link_nova_instalacao_jogador'><a href='#' onclick='return nova_instalacao_jogador(event,this,{$planeta->id},{$this->id_imperio});' {$visivel}>Nova Instalação</a></div>";
				$ultimo_planeta = $this->id_planeta[$chave];				
				$slots = 0;
				$balanco_planeta = "";
				
				$balanco_planeta = $this->exibe_balanco_planeta($planeta->id);
				$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
				$this->debug .= "acoes->lista_dados => exibe_balanco_planeta({$planeta->id}) {$diferenca}ms\n";				
				if (empty($this->imperio)) {
					$imperio = new imperio($this->id_imperio, $this->turno->turno);
					$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
					$this->debug .= "exibe_pop_mdo_planeta => new imperio({$this->id_imperio}) {$diferenca}ms\n";
					$this->imperio = $imperio;
				}
				$pop_mdo_planeta = $this->exibe_pop_mdo_planeta($planeta->id, $this->imperio, $planeta);
				
				$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
				$this->debug .= "acoes->lista_dados => exibe_pop_mdo_planeta({$planeta->id}) {$diferenca}ms\n";
				
				if ($colonia->vassalo == 1 && $roles != "administrator") {
					$this->disabled = "disabled";
					$visivel = "style='display: none;'";
					$html_nova_instalacao_jogador = "";
				}
				
				$primeira_linha = "<td rowspan='{$colonia->num_instalacoes}' data-atributo='dados_colonia' style='height: 180px;'>
				<div data-atributo='nome_planeta'>
					<div data-atributo='slots_planeta' style='display: inline-block;'>{$colonia->instalacoes}/{$planeta->tamanho()} | </div>
					<div data-atributo='dados_colonia' style='display: inline-block;'>{$colonia->icone_capital}{$colonia->icone_vassalo}{$planeta->icone_habitavel()}{$planeta->nome} ({$estrela->X};{$estrela->Y};{$estrela->Z};{$planeta->posicao}) | </div>
					<div style='display: inline-block;' data-atributo='pop_mdo_planeta' id='pop_mdo_planeta_{$planeta->id}'>{$pop_mdo_planeta}</div>
					<div data-atributo='recursos_planeta' style='margin-bottom: 2px;'><span style='color: #6d351a; font-weight: bold;'>Jazidas:</span> {$html_recursos_planeta}</div>
					<div data-atributo='balanco_recursos_planeta' id='balanco_planeta_{$planeta->id}'>{$balanco_planeta}</div>
				{$html_nova_instalacao_jogador}
				</div>
				</td>";
				if ($estilo == $estilo_par) {
					$estilo = $estilo_impar;
				} else {
					$estilo = $estilo_par;
				}					
			} else {
				$primeira_linha = "&nbsp;";
			}
	
			if ($colonia->vassalo == 1 && $roles != "administrator") {
				$this->disabled = "disabled";
				$visivel = "style='display: none;'";
				$html_nova_instalacao_jogador = "";
			}
			
			if ($this->disabled != "disabled" && empty($this->turno_destroi[$chave])) {
				//Verifica se há uma Tech para Upgrade e se o Império tem essa Tech
				
				$nivel_upgrade = $this->nivel_instalacao[$chave] + 1;
				$tech_upgrade = $instalacao[$this->id_instalacao[$chave]]->tech_requisito_upgrade($nivel_upgrade);
				while (!empty($tech_imperio = $wpdb->get_var("SELECT id FROM colonization_imperio_techs WHERE id_imperio={$this->id_imperio} AND id_tech={$tech_upgrade} AND custo_pago=0"))) {
					if ($instalacao[$this->id_instalacao[$chave]]->nivel_maximo === false || $nivel_upgrade < $instalacao[$this->id_instalacao[$chave]]->nivel_maximo) {//Não tem nível máximo, ou o nível atual é menor que o nível máximo
						$html_upgrade = "<div data-atributo='upgrade' class='upgrade' {$visivel}><a href='#' onclick='return upgrade_instalacao(event,this,{$nivel_upgrade});'><i class='fas fa-level-up tooltip'><span class='tooltiptext'>Upgrade</span></i></a></div>";
						$nivel_upgrade++;
						$tech_upgrade = $instalacao[$this->id_instalacao[$chave]]->tech_requisito_upgrade($nivel_upgrade);
					} else {
						break;
					}
				}
			}
			
			$nivel = $plugin_colonization->html_mk($this->nivel_instalacao[$chave]);
			
			if ($instalacao[$this->id_instalacao[$chave]]->desguarnecida == 0) {
				$exibe_acoes = "<input data-atributo='pop' data-ajax='true' data-valor-original='{$this->pop[$chave]}' type='range' min='0' max='10' value='{$this->pop[$chave]}' oninput='return altera_acao(event, this);' onmouseup='return valida_acao(event, this);' ontouchend='return valida_acao(event, this);' {$this->disabled}></input>&nbsp;&nbsp;&nbsp;<label data-atributo='pop' style='width: 30px;'>{$this->pop[$chave]}</label>";
			} elseif ($instalacao[$this->id_instalacao[$chave]]->pode_desativar == 1) {
				//Instalações Desguarnecidas podem ser desativadas
				if ($this->desativado[$chave] == 0) {
					$exibe_acoes = "<label class='switch'><input type='checkbox' data-ajax='true' data-atributo='desativado' onclick='return desativar_instalacao(event,this,{$this->id[$chave]});' data-valor-original='{$this->desativado[$chave]}' value='{$this->desativado[$chave]}' {$this->disabled}><span class='slider round'></span></label>";
					//$exibe_acoes = "<a href='#' onclick='return desativar_instalacao(event,this,{$this->id[$chave]});'><i class='fas fa-toggle-off tooltip' style='color: #AA0000; font-size: x-large;'></i></a>";
				} else {
					$exibe_acoes = "<label class='switch'><input type='checkbox' data-atributo='desativado' data-ajax='true' onclick='return desativar_instalacao(event,this,{$this->id[$chave]});' data-valor-original='{$this->desativado[$chave]}' value='{$this->desativado[$chave]}' checked {$this->disabled}><span class='slider round'></span></label>";
					//$exibe_acoes = "<a href='#' onclick='return desativar_instalacao(event,this,{$this->id[$chave]});'><i class='fas fa-toggle-on tooltip' style='color: #007700; font-size: x-large;'></i></a>";	
				}
			} else {
				$exibe_acoes = "&nbsp;";
			}
			
			//Verifica se a Instalação tem algum html_especial
			$div_html_especial = "";
			if ($instalacao[$this->id_instalacao[$chave]]->html_especial != "") {
				$qtd_pop = false;
				$chama_funcao = $instalacao[$this->id_instalacao[$chave]]->html_especial;
				if ($instalacao[$this->id_instalacao[$chave]]->produz_droids) {
					$chave_industrializaveis = array_search($id_industrializaveis, $imperio_recursos->id_recurso);				
					$qtd_pop = $imperio_recursos->qtd[$chave_industrializaveis]/10;
				} elseif ($instalacao[$this->id_instalacao[$chave]]->produz_clones) {
					$chave_alimento = array_search($id_alimento, $imperio_recursos->id_recurso);
					$qtd_pop = $imperio_recursos->qtd[$chave_alimento]/100;
				}
				
				if ($qtd_pop !== false && ($instalacao[$this->id_instalacao[$chave]]->produz_droids || $instalacao[$this->id_instalacao[$chave]]->produz_clones)) {
					$div_html_especial = $instalacao[$this->id_instalacao[$chave]]->$chama_funcao($this->id_colonia[$chave],$qtd_pop);
					$div_html_especial = "<div>{$div_html_especial}</div>";
				} elseif ($instalacao[$this->id_instalacao[$chave]]->anti_dobra) {
					$div_html_especial = $instalacao[$this->id_instalacao[$chave]]->$chama_funcao($planetas[$this->id_planeta[$chave]]->id_estrela);
					$div_html_especial = "<div>{$div_html_especial}</div>";					
				}
			}
			
			$div_desmonta_instalacao = "";
			if (!empty($this->turno_destroi[$chave])) {
				$exibe_acoes = "<span style='color: #DD0000; font-weight: bold;'>DESTRUÍDA!</span>";
				$div_html_especial = "";
				$div_desmonta_instalacao = "<div class='repara_instalacao'><a href='#' onclick='return repara_instalacao(event, this);' {$visivel}>Reparar Instalação</a></div>";
			}
			
			if ($instalacao[$this->id_instalacao[$chave]]->oculta == 0) {
				$html_producao_consumo_instalacao = $this->html_producao_consumo_instalacao($chave);
				
				/***
				if (!empty($this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]])) {
					foreach ($this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]] as $id_recurso => $qtd) {
						$recurso = new recurso($id_recurso);
						$html_producao_consumo_instalacao .= "{$recurso->html_icone()}: {$qtd}; ";
					}
				}

				if (!empty($this->recursos_consumidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]])) {
					foreach ($this->recursos_consumidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]] as $id_recurso => $qtd) {
						$recurso = new recurso($id_recurso);
						$html_producao_consumo_instalacao .= "{$recurso->html_icone()}: <span style='color: #FF2222;'>-{$qtd}</span>; ";
					}				
				}
				***/
				
				$ja_destruiu = "";
				if (!empty($this->turno_destroi[$chave])) {
					$ja_destruiu = ", true";
				}
				//if ($roles == "administrator") {
				if ($instalacao[$this->id_instalacao[$chave]]->pode_desativar == 1 || $instalacao[$this->id_instalacao[$chave]]->slots > 0) {
					$div_desmonta_instalacao .= "<div data-atributo='desmonta_instalacao'><a href='#' onclick='return desmonta_instalacao(event, this, {$this->turno->turno},true{$ja_destruiu});' {$visivel}>Desmantelar</a></div>";
				}
				//}
				
				$html_custo_instalacao = $instalacao[$this->id_instalacao[$chave]]->html_custo();
				$html .= "		
				<tr {$estilo}>
					{$primeira_linha}
				<td>
					<input type='hidden' data-atributo='id' data-valor-original='{$this->id[$chave]}' value='{$this->id[$chave]}'></input>
					<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='{$this->id_imperio}' value='{$this->id_imperio}'></input>
					<input type='hidden' data-atributo='id_planeta' data-ajax='true' data-valor-original='{$this->id_planeta[$chave]}' value='{$this->id_planeta[$chave]}'></input>
					<input type='hidden' data-atributo='id_estrela' data-valor-original='{$estrela->id}' value='{$estrela->id}'></input>
					<input type='hidden' data-atributo='id_instalacao' data-ajax='true' data-valor-original='{$this->id_instalacao[$chave]}' value='{$this->id_instalacao[$chave]}'></input>
					<input type='hidden' data-atributo='id_planeta_instalacoes' data-ajax='true' data-valor-original='{$this->id_planeta_instalacoes[$chave]}' value='{$this->id_planeta_instalacoes[$chave]}'></input>
					<input type='hidden' data-atributo='turno' data-ajax='true' data-valor-original='{$this->turno->turno}' value='{$this->turno->turno}'></input>
					<input type='hidden' data-atributo='where_clause' value='id'></input>
					<input type='hidden' data-atributo='nivel' value='{$this->nivel_instalacao[$chave]}'></input>
					<input type='hidden' data-atributo='where_value' value='{$this->id[$chave]}'></input>
					<input type='hidden' data-atributo='funcao_validacao' value='valida_acao'></input>
					<div data-atributo='nome_instalacao' data-valor-original='{$instalacao[$this->id_instalacao[$chave]]->nome}' class='nome_instalacao tooltip'>
					{$instalacao[$this->id_instalacao[$chave]]->nome}<span class='tooltiptext'>{$instalacao[$this->id_instalacao[$chave]]->descricao}</span><label data-atributo='nivel'> {$nivel}</label>
					</div>
					{$html_upgrade}
					{$div_desmonta_instalacao}
					{$div_html_especial}
					<div data-atributo='custo_instalacao' data-valor-original='' class='custo_instalacao'><label>Custo por nível:</label> {$html_custo_instalacao}</div>
					<div data-atributo='balanco_instalacao' id='{$this->id_planeta_instalacoes[$chave]}' class='balanco_instalacao'><label>Balanço da produção:</label> {$html_producao_consumo_instalacao}</div>
				</td>
				<td><div data-atributo='pop' data-valor-original='{$this->pop[$chave]}' data-ajax='true' style='display: flex; align-items: center; justify-content:center;'>{$exibe_acoes}</div></td>";
				//<td><div data-atributo='gerenciar' style='visibility: hidden;'><a href='#' onclick='return salva_acao(event, this);'>Salvar</a> | <a href='#' onclick='return salva_acao(event, this,true);'>Cancelar</a></div></td>
				$html .= "<td><div data-atributo='gerenciar' style='visibility: hidden; margin: auto; text-align: center;' class='fas fa-hourglass-half'></div></td>
				</tr>";
			}
		}
		
		return $html;
	}


	/***********************
	function html_producao_consumo_instalacao($chave)
	----------------------
	Pegoa o HTML com a produção de uma Instalação
	
	$chave -- chave da ação sendo processada
	***********************/
	function html_producao_consumo_instalacao($chave) {
		global $wpdb;
		$html_producao_consumo_instalacao = "";
		
		if (!empty($this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]])) {
			
			$id_recursos = implode(",",array_filter(array_keys($this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]])));
			$id_recursos_ordenados = $wpdb->get_results("SELECT cr.id
			FROM colonization_recurso AS cr
			WHERE cr.id IN ({$id_recursos})
			ORDER BY cr.nivel, cr.nome
			");
			
			foreach ($id_recursos_ordenados as $id_recurso) {
				$recurso = new recurso($id_recurso->id);
				$html_producao_consumo_instalacao .= "{$recurso->html_icone()}: {$this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$recurso->id]}; ";
			}
		}

		if (!empty($this->recursos_consumidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]])) {
			$id_recursos = implode(",",array_filter(array_keys($this->recursos_consumidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]])));
			$id_recursos_ordenados = $wpdb->get_results("SELECT cr.id
			FROM colonization_recurso AS cr
			WHERE cr.id IN ({$id_recursos})
			ORDER BY cr.nivel, cr.nome
			");
			foreach ($id_recursos_ordenados as $id_recurso) {
				$recurso = new recurso($id_recurso->id);
				$html_producao_consumo_instalacao .= "{$recurso->html_icone()}: <span style='color: #FF2222;'>-{$this->recursos_consumidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$recurso->id]}</span>; ";
			}				
		}
		return $html_producao_consumo_instalacao;				
	}



	/***********************
	function pega_balanco_recursos($id_planeta_instalacoes=0, $salva_balanco=false)
	----------------------
	Pega os Recursos produzidos, consumidos e seu balanço
	
	$id_planeta_instalacoes -- id da instalação do planeta quando estamos alterando apenas um dado (default 0, ou seja, calcula tudo)
	$salva_balanco -- salva os balanços após ter processado
	***********************/
	function pega_balanco_recursos($id_planeta_instalacoes=0, $salva_balanco=false, $imperio = "") {
		global $wpdb, $start_time;

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];	
			$banido = get_user_meta($user->ID, 'asgarosforum_role', true);
			if ($banido === "banned") {
				$banido = true;
			} else {
				$banido = false;
			}
		}
		
		$bonus_sinergia_tech = 0;
		$instalacao_tech = 0;
		if ($imperio == "") {
			$imperio = new imperio($this->id_imperio,false,$this->turno->turno);
			$imperio->acoes = $this;
			$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
			$this->debug .= "acoes->pega_balanco_recursos() -> new Imperio {$diferenca}ms \n";	
		}

		$id_alimento = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome = 'Alimentos'");
		$id_energia = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome = 'Energia'");
		$id_poluicao = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome = 'Poluição'");
		$id_pesquisa = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='Pesquisa'");
		$id_industrializaveis = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='Industrializáveis'");
		$id_comercio = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='Capacidade de Comércio'");
		$id_comercio_maximo = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='Comércio Máximo'");

		//Para agilizar o processamento, salvamos os dados no DB e só processamos todos os balanços quando necessário
		//$wpdb->query("DELETE FROM colonization_balancos_turno WHERE id_imperio = {$this->id_imperio} AND turno = {$this->turno->turno}");
		$balancos_db = stripslashes(str_replace(array("\\n", "\\r", "\\t"), "", $wpdb->get_var("SELECT json_balancos FROM colonization_balancos_turno WHERE id_imperio = {$this->id_imperio} AND turno = {$this->turno->turno}")));
		$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
		$this->debug .= "acoes->pega_balanco_recursos() -> query JSON: SELECT json_balancos FROM colonization_balancos_turno WHERE id_imperio = {$this->id_imperio} AND turno = {$this->turno->turno} {$diferenca}ms \n";
		$this->debug .= "acoes->pega_balanco_recursos() -> balancos_db: {$balancos_db} {$diferenca}ms \n";
		
		$flag_novo_balanco = true;
		if (empty($balancos_db)) {
			$this->recursos_produzidos_planeta_instalacao = [];
			$this->recursos_produzidos_planeta_bonus = [];
			$this->recursos_produzidos_planeta_instalacao_bonus = [];
			$this->recursos_produzidos_planeta = [];
			$this->recursos_produzidos_id_planeta_instalacoes = [];
			
			$this->recursos_extraidos_planeta = [];

			$this->recursos_produzidos_nome = [];
			$this->recursos_consumidos_nome = [];
			$this->recursos_balanco_nome = [];
			$this->recursos_consumidos_planeta = [];
			$this->recursos_consumidos_id_planeta_instalacoes = [];
		} else {
			$balancos_db = json_decode($balancos_db, false, 512, JSON_UNESCAPED_UNICODE);
			$flag_novo_balanco = false;
			foreach ($balancos_db as $property => $value) {
				$this->$property = json_decode(json_encode($value),true);
			}
			
			/***
			$this->recursos_produzidos_planeta_instalacao = json_decode(json_encode($balancos_db->recursos_produzidos_planeta_instalacao),true); //Número de Instalações, por planeta, que geram um determinado recurso
			$this->recursos_produzidos_planeta_bonus = json_decode(json_encode($balancos_db->recursos_produzidos_planeta_bonus),true); //Bônus de recursos, por planeta
			$this->recursos_produzidos_planeta_instalacao_bonus = json_decode(json_encode($balancos_db->recursos_produzidos_planeta_instalacao_bonus),true); //Bônus de recursos, por instalação de um planeta
			$this->recursos_produzidos_planeta = json_decode(json_encode($balancos_db->recursos_produzidos_planeta),true);
			$this->recursos_produzidos_id_planeta_instalacoes = json_decode(json_encode($balancos_db->recursos_produzidos_id_planeta_instalacoes),true);

			$this->recursos_extraidos_planeta = json_decode(json_encode($balancos_db->recursos_extraidos_planeta),true);	
			
			$this->recursos_consumidos_planeta = json_decode(json_encode($balancos_db->recursos_consumidos_planeta),true);
			$this->recursos_consumidos_id_planeta_instalacoes = json_decode(json_encode($balancos_db->recursos_consumidos_id_planeta_instalacoes),true);

			$this->recursos_produzidos_nome = json_decode(json_encode($balancos_db->recursos_produzidos_nome),true);
			$this->recursos_consumidos_nome = json_decode(json_encode($balancos_db->recursos_consumidos_nome),true);
			$this->recursos_balanco_nome = json_decode(json_encode($balancos_db->recursos_balanco_nome),true);
			//***/
			
			//$this->recursos_produzidos = (array) $balancos_db->recursos_produzidos;
			//$this->recursos_consumidos = (array) $balancos_db->recursos_consumidos;
			//$this->recursos_balanco = (array) $balancos_db->recursos_balanco;
			//$this->recursos_balanco_planeta = (array) $balancos_db->recursos_balanco_planeta;
			$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
			$this->debug .= "acoes->pega_balanco_recursos() -> pegou dados do JSON {$diferenca}ms \n";
		}

		$this->recursos_produzidos = [];
		$this->recursos_consumidos = [];			

		$this->recursos_balanco = [];
		$this->recursos_balanco_planeta = [];

		$colonia = [];
		$planeta = [];
		$instalacao = [];
		$recurso = [];

		//Pega a produção e o consumo relativo a cada Ação e sua respectiva Instalação
		$colonia_sendo_alterada = false;
		foreach ($this->id AS $chave => $valor) {
			//Se a instalação já foi processada, não precisa ser processada novamente, EXCETO se for uma instalação que está sendo ALTERADA ($id_planeta_instalacoes != 0)
			if (!$flag_novo_balanco && $this->id_planeta_instalacoes[$chave] != $id_planeta_instalacoes) {
				$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
				$this->debug .= "acoes->pega_balanco_recursos() -> pulando a ação({$this->id[$chave]}) {$diferenca}ms \n";	
				continue;
			} elseif (!$flag_novo_balanco && $this->id_planeta_instalacoes[$chave] == $id_planeta_instalacoes) {
				$colonia_sendo_alterada = $this->id_colonia[$chave];
				$this->debug .= "colonia_sendo_alterada: {$colonia_sendo_alterada} || planeta: {$this->id_planeta[$chave]}\n";
				//Remove a produção e consumo dessa Instalação das variáveis planetárias e zera a produção e o consumo dessa instalação
				foreach ($this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]] as $id_recurso_instalacao_planeta => $qtd_produzida_instalacao) {
					$this->debug .= "Removendo {$id_recurso_instalacao_planeta} da produção do planeta => {$this->recursos_produzidos_planeta[$id_recurso_instalacao_planeta][$this->id_planeta[$chave]]} - {$qtd_produzida_instalacao}\n";
					$this->recursos_produzidos_planeta[$id_recurso_instalacao_planeta][$this->id_planeta[$chave]] = $this->recursos_produzidos_planeta[$id_recurso_instalacao_planeta][$this->id_planeta[$chave]] - $qtd_produzida_instalacao;
					$this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso_instalacao_planeta] = 0;
					
					$this->debug .= "Removendo {$id_recurso_instalacao_planeta} do bônus de produção do planeta => {$this->recursos_produzidos_planeta_bonus[$id_recurso_instalacao_planeta][$this->id_planeta[$chave]]} - {$this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso_instalacao_planeta][$this->id_planeta_instalacoes[$chave]]}\n";
					$this->recursos_produzidos_planeta_bonus[$id_recurso_instalacao_planeta][$this->id_planeta[$chave]] = $this->recursos_produzidos_planeta_bonus[$id_recurso_instalacao_planeta][$this->id_planeta[$chave]] - $this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso_instalacao_planeta][$this->id_planeta_instalacoes[$chave]];
					$this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso_instalacao_planeta][$this->id_planeta_instalacoes[$chave]] = 0;
					
				}
				
				foreach ($this->recursos_consumidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]] as $id_recurso_instalacao_planeta => $qtd_consumida_instalacao) {
					$this->recursos_consumidos_planeta[$id_recurso_instalacao_planeta][$this->id_planeta[$chave]] = $this->recursos_consumidos_planeta[$id_recurso_instalacao_planeta][$this->id_planeta[$chave]] - $qtd_consumida_instalacao;
					$this->recursos_consumidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso_instalacao_planeta] = 0;
				}
			}
			
			if (!empty($this->turno_destroi[$chave])) {//Se a Instalação está destruída, ela não produz nem consome nada
				continue;
			}
			
			if (empty($colonia[$this->id_colonia[$chave]])) {
				$colonia[$this->id_colonia[$chave]] = new colonia($this->id_colonia[$chave],$this->turno->turno);
					$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
					$this->debug .= "acoes->pega_balanco_recursos() -> new Colonia({$this->id_colonia[$chave]}) {$diferenca}ms \n";	
			}
				
			if (empty($planeta[$this->id_planeta[$chave]])) {
				$planeta[$this->id_planeta[$chave]] = new planeta($this->id_planeta[$chave],$this->turno->turno);
				$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
				$this->debug .= "acoes->pega_balanco_recursos() -> new Planeta({$this->id_planeta[$chave]}) {$diferenca}ms \n";	
			}			
			
			if (empty($instalacao[$this->id_instalacao[$chave]])) {
				$instalacao[$this->id_instalacao[$chave]] = new instalacao($this->id_instalacao[$chave]);
					$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
					$this->debug .= "acoes->pega_balanco_recursos() -> new Instalação({$this->id_instalacao[$chave]}) {$diferenca}ms \n";			
			}
			
			//*** Pega a Produção das Instalações ***//
			foreach ($instalacao[$this->id_instalacao[$chave]]->recursos_produz as $chave_recursos => $id_recurso) {
				if (empty($this->recursos_produzidos[$id_recurso])) {
					if (empty($recurso[$id_recurso])) {
						$recurso[$id_recurso] = new recurso($id_recurso);
							$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
							$this->debug .= "acoes->pega_balanco_recursos() -> new Recurso({$id_recurso}) {$diferenca}ms \n";	
					}

					$this->recursos_produzidos[$id_recurso] = 0;
					$this->recursos_produzidos_nome[$id_recurso] = $recurso[$id_recurso]->nome;
					$this->recursos_balanco_nome[$id_recurso] = $recurso[$id_recurso]->nome;
				}
				
				if (empty($this->recursos_produzidos_planeta[$id_recurso][$this->id_planeta[$chave]])) {
					$this->recursos_produzidos_planeta[$id_recurso][$this->id_planeta[$chave]] = 0;
					$this->recursos_extraidos_planeta[$id_recurso][$this->id_planeta[$chave]] = 0;
					$this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] = 0;
				}
				
				if (empty($this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso])) {
					$this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso] = 0;
					$this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] = 0;
				}
				
				if (empty($this->recursos_produzidos_planeta_instalacao[$id_recurso][$this->id_planeta[$chave]])) {
					if ($this->pop[$chave] != 0) {
						$this->recursos_produzidos_planeta_instalacao[$id_recurso][$this->id_planeta[$chave]] = 1;
					} else {
						$this->recursos_produzidos_planeta_instalacao[$id_recurso][$this->id_planeta[$chave]] = 0;
					}
				} else {
					if ($this->pop[$chave] != 0) {
						$this->recursos_produzidos_planeta_instalacao[$id_recurso][$this->id_planeta[$chave]]++;
					}
				}
				
				//Se for uma instalação Comercial, já atualiza os valores de produção
				//$this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_comercio]
				if (empty($this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_comercio]) && $instalacao[$this->id_instalacao[$chave]]->comercio !== false) {
					$instalacao[$this->id_instalacao[$chave]]->produz_comercio($colonia[$this->id_colonia[$chave]], $this->nivel_instalacao[$chave]);
					$this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_comercio] = floor($instalacao[$this->id_instalacao[$chave]]->comercio_potencial*$this->nivel_instalacao[$chave]);
					$this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_comercio_maximo] = 10*$instalacao[$this->id_instalacao[$chave]]->comercio*$this->nivel_instalacao[$chave];
				}
				
				if (empty($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])) {
					$instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos] = 0;
				}
				
				if ($instalacao[$this->id_instalacao[$chave]]->desguarnecida == 1 && $this->desativado[$chave] == 0) {
					//$this->recursos_produzidos[$id_recurso] = $this->recursos_produzidos[$id_recurso] + floor($instalacao->recursos_produz_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*10/10);
					$this->recursos_produzidos_planeta[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_produzidos_planeta[$id_recurso][$this->id_planeta[$chave]] + floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*10/10);
					$this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso] = $this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso] + floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*10/10);
					if ($recurso[$id_recurso]->extrativo == 1 && $instalacao[$this->id_instalacao[$chave]]->nao_extrativo == false) {
						$this->recursos_extraidos_planeta[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_extraidos_planeta[$id_recurso][$this->id_planeta[$chave]] + floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*10/10);
						
						$qtd_recurso_planeta = $wpdb->get_var("SELECT cpr.disponivel 
						FROM colonization_planeta_recursos AS cpr
						JOIN colonization_recurso AS cr
						ON cr.id = cpr.id_recurso
						WHERE cpr.id_planeta={$this->id_planeta[$chave]} AND cpr.turno={$this->turno->turno} 
						AND cpr.id_recurso={$id_recurso}");
						$desativa_instalacao_atual = 1;
						if ($qtd_recurso_planeta > 0) { 
							$desativa_instalacao_atual = 0;
						}
						//if ($this->recursos_extraidos_planeta[$id_recurso][$this->id_planeta[$chave]] > $qtd_recurso_planeta && ($id_planeta_instalacoes != $this->id_planeta_instalacoes[$chave])) {
						if ($this->recursos_extraidos_planeta[$id_recurso][$this->id_planeta[$chave]] > $qtd_recurso_planeta) {
							$this->recursos_extraidos_planeta[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_extraidos_planeta[$id_recurso][$this->id_planeta[$chave]] - floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*10/10) + $qtd_recurso_planeta;
							$this->recursos_produzidos_planeta[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_produzidos_planeta[$id_recurso][$this->id_planeta[$chave]] - floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*10/10) + $qtd_recurso_planeta;
							$this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso] = $this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso] - floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*10/10) + $qtd_recurso_planeta;

							$this->debug .= "ID_PLANETA_INSTALACOES: {$id_planeta_instalacoes} : {$this->id_planeta_instalacoes[$chave]} => produz {$qtd_recurso_planeta}\n";
							$wpdb->query("UPDATE colonization_acoes_turno SET pop=0, desativado={$desativa_instalacao_atual} WHERE id={$this->id[$chave]}");
							$this->pop[$chave] = 0;
							$this->desativado[$chave] = $desativa_instalacao_atual;
						}
					}
				} else {
					//$this->recursos_produzidos[$id_recurso] = $this->recursos_produzidos[$id_recurso] + floor($instalacao->recursos_produz_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10);
					$this->recursos_produzidos_planeta[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_produzidos_planeta[$id_recurso][$this->id_planeta[$chave]] + floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10);
					$this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso] = $this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso] + floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10);
					if ($recurso[$id_recurso]->extrativo == 1 && $instalacao[$this->id_instalacao[$chave]]->nao_extrativo == false) {
						$this->recursos_extraidos_planeta[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_extraidos_planeta[$id_recurso][$this->id_planeta[$chave]] + floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10);
						
						$qtd_recurso_planeta = $wpdb->get_var("SELECT cpr.disponivel 
						FROM colonization_planeta_recursos AS cpr
						JOIN colonization_recurso AS cr
						ON cr.id = cpr.id_recurso
						WHERE cpr.id_planeta={$this->id_planeta[$chave]} AND cpr.turno={$this->turno->turno} 
						AND cpr.id_recurso={$id_recurso}");
						$desativa_instalacao_atual = 0;
						$extrai_recursos_totais = 0;
						if ($qtd_recurso_planeta > 0 && $this->pop[$chave] == 1) { 
							$desativa_instalacao_atual = 1;
							$extrai_recursos_totais = $qtd_recurso_planeta;
						}						
						
						if ($this->recursos_extraidos_planeta[$id_recurso][$this->id_planeta[$chave]] > $qtd_recurso_planeta && (($id_planeta_instalacoes != $this->id_planeta_instalacoes[$chave]) || ($this->pop[$chave] == 1 && $id_planeta_instalacoes == $this->id_planeta_instalacoes[$chave]))) {
							$this->recursos_extraidos_planeta[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_extraidos_planeta[$id_recurso][$this->id_planeta[$chave]] - floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10) + $extrai_recursos_totais;
							$this->recursos_produzidos_planeta[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_produzidos_planeta[$id_recurso][$this->id_planeta[$chave]] - floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10) + $extrai_recursos_totais;
							$this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso] = $this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso] - floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10) + $extrai_recursos_totais;

							$this->debug .= "ID_PLANETA_INSTALACOES: {$id_planeta_instalacoes} : {$this->id_planeta_instalacoes[$chave]} \n";
							$wpdb->query("UPDATE colonization_acoes_turno SET pop={$desativa_instalacao_atual} WHERE id={$this->id[$chave]}");
							$this->pop[$chave] = 0;
							$this->desativado[$chave] = $desativa_instalacao_atual;
						}
					}					
				}

				if (!empty($imperio->bonus_recurso['*']) && $id_recurso !== $id_poluicao) {//Tem bônus para TODOS os recursos
					//Verifica as condições
					if (!empty($imperio->extrativo['*']) && $imperio->bonus_todos_recursos == 0) {//O bônus se aplica somente para recursos EXTRATIVOS
						if ($imperio->extrativo['*'] === true && $recurso[$id_recurso]->extrativo == 1) {
							if ($instalacao[$this->id_instalacao[$chave]]->desguarnecida == 1 && $this->desativado[$chave] == 0) {
								$this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] + intval(floor(floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*10/10)*(($imperio->bonus_recurso['*'])/100)));
								$this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] = $this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] + intval(floor(floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*10/10)*(($imperio->bonus_recurso['*'])/100)));
							} else {
								$this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] + intval(floor(floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10)*(($imperio->bonus_recurso['*'])/100)));
								$this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] = $this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] + intval(floor(floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10)*(($imperio->bonus_recurso['*'])/100)));
							}
							//$this->recursos_produzidos_id_planeta_instalacoes[$id_recurso][$this->id_planeta_instalacoes[$chave]] = $this->recursos_produzidos_id_planeta_instalacoes[$id_recurso][$this->id_planeta_instalacoes[$chave]] + + intval(floor(floor($instalacao->recursos_produz_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10)*(($imperio->bonus_recurso['*'])/100)));
						}
					} else {//O bônus se aplica à qualquer tipo de recurso
						if ($instalacao[$this->id_instalacao[$chave]]->desguarnecida == 1 && $this->desativado[$chave] == 0) {
							$this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] + intval(floor(floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*10/10)*(($imperio->bonus_recurso['*'])/100)));
							$this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] = $this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] + intval(floor(floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*10/10)*(($imperio->bonus_recurso['*'])/100)));
						} else {
							$this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] + intval(floor(floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10)*(($imperio->bonus_recurso['*'])/100)));	
							$this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] = $this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] + intval(floor(floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10)*(($imperio->bonus_recurso['*'])/100)));	
						}
						//$this->recursos_produzidos_id_planeta_instalacoes[$id_recurso][$this->id_planeta_instalacoes[$chave]] = $this->recursos_produzidos_id_planeta_instalacoes[$id_recurso][$this->id_planeta_instalacoes[$chave]] + intval(floor(floor($instalacao->recursos_produz_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10)*(($imperio->bonus_recurso['*'])/100)));
					}
				} 
				
				if (!empty($imperio->bonus_recurso[$id_recurso])) {//Tem bônus para o recurso ATUAL
					//Verifica as condições
					if (!empty($imperio->extrativo[$id_recurso])) {//O bônus se aplica somente para recursos EXTRATIVOS
						if ($imperio->extrativo[$id_recurso] === true && $recurso[$id_recurso]->extrativo == 1) {
							if ($instalacao[$this->id_instalacao[$chave]]->desguarnecida == 1 && $this->desativado[$chave] == 0) {
								$this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] + intval(floor(floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*10/10)*(($imperio->bonus_recurso[$id_recurso])/100)));
								$this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] = $this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] + intval(floor(floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*10/10)*(($imperio->bonus_recurso[$id_recurso])/100)));
							} else {
								$this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] + intval(floor(floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10)*(($imperio->bonus_recurso[$id_recurso])/100)));
								$this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] = $this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] + intval(floor(floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10)*(($imperio->bonus_recurso[$id_recurso])/100)));
							}
							//$this->recursos_produzidos_id_planeta_instalacoes[$id_recurso][$this->id_planeta_instalacoes[$chave]] = $this->recursos_produzidos_id_planeta_instalacoes[$id_recurso][$this->id_planeta_instalacoes[$chave]] + intval(floor(floor($instalacao->recursos_produz_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10)*(($imperio->bonus_recurso[$id_recurso])/100)));
						}
					} else {//O bônus se aplica à qualquer tipo de recurso
						if ($instalacao[$this->id_instalacao[$chave]]->desguarnecida == 1 && $this->desativado[$chave] == 0) {
							$this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] + intval(floor(floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*10/10)*(($imperio->bonus_recurso[$id_recurso])/100)));
							$this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] = $this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] + intval(floor(floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*10/10)*(($imperio->bonus_recurso[$id_recurso])/100)));
						} else {
							$this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] + intval(floor(floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10)*(($imperio->bonus_recurso[$id_recurso])/100)));
							$this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] = $this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] + intval(floor(floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10)*(($imperio->bonus_recurso[$id_recurso])/100)));
						}
						//$this->recursos_produzidos_id_planeta_instalacoes[$id_recurso][$this->id_planeta_instalacoes[$chave]] = $this->recursos_produzidos_id_planeta_instalacoes[$id_recurso][$this->id_planeta_instalacoes[$chave]] + intval(floor(floor($instalacao->recursos_produz_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10)*(($imperio->bonus_recurso[$id_recurso])/100)));
					}
				}

				$bonus_recurso_colonia = $colonia[$this->id_colonia[$chave]]->bonus_recurso($id_recurso);
				if ($bonus_recurso_colonia > 0) {
					if (!empty($imperio->max_bonus_recurso[$id_recurso])) {
						$imperio->max_bonus_recurso[$id_recurso] = false;
					}
					if ($instalacao[$this->id_instalacao[$chave]]->desguarnecida == 1 && $this->desativado[$chave] == 0) {
						$this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] + intval(floor(floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*10/10))*($bonus_recurso_colonia/100));
						$this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] = $this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] + intval(floor(floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*10/10))*($bonus_recurso_colonia/100));
					} else {
						$this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] + intval(floor(floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10))*($bonus_recurso_colonia/100));
						$this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] = $this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] + intval(floor(floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10))*($bonus_recurso_colonia/100));
					}
					//$this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso] = $this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso] + intval(floor(floor($instalacao->recursos_produz_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10))*($bonus_recurso_colonia/100));
					
					if ($roles == "administrator") {
						//echo "#{$this->id[$chave]} {$this->id_planeta[$chave]}/{$id_recurso}: {$this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]]} || {$this->recursos_produzidos_planeta_instalacao[$id_recurso][$this->id_planeta[$chave]]}<br>";
					}
				}

				$bonus_extrativo = $colonia[$this->id_colonia[$chave]]->bonus_extrativo()/100;
				if ($recurso[$id_recurso]->extrativo == 1 && $bonus_extrativo > 0) {
					if (!empty($imperio->max_bonus_recurso[$id_recurso])) {
						$imperio->max_bonus_recurso[$id_recurso] = false;
					}
					//$bonus_extrativo = $colonia[$this->id_colonia[$chave]]->bonus_extrativo/100;
					if ($instalacao[$this->id_instalacao[$chave]]->desguarnecida == 1 && $this->desativado[$chave] == 0) {
						$this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] + intval(floor(floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*10/10)))*$bonus_extrativo;
						$this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] = $this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] + intval(floor(floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*10/10)))*$bonus_extrativo;
					} else {
						$this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] + intval(floor(floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10)))*$bonus_extrativo;
						$this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] = $this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]] + intval(floor(floor(($instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd[$chave_recursos] + $instalacao[$this->id_instalacao[$chave]]->recursos_produz_qtd_comercio[$chave_recursos])*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10)))*$bonus_extrativo;						
					}
					//$this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso] = $this->recursos_produzidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso] + intval(floor(floor($instalacao->recursos_produz_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10)))*$bonus_extrativo;
					
					if ($roles == "administrator") {
						//echo "#{$this->id[$chave]} {$this->id_planeta[$chave]}/{$id_recurso}: {$this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]]}<br>";
					}
				}
				$this->debug .= "Bônus do recurso {$id_recurso} => {$this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]]} || {$this->recursos_produzidos_planeta_instalacao_bonus[$id_recurso][$this->id_planeta_instalacoes[$chave]]}\n";
			}

			//*** Pega o Consumo das Instalações ***//
			foreach ($instalacao[$this->id_instalacao[$chave]]->recursos_consome as $chave_recursos => $id_recurso) {
				if (empty($this->recursos_consumidos[$id_recurso])) {
					if (empty($recurso[$id_recurso])) {
						$recurso[$id_recurso] = new recurso($id_recurso);
						$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
						$this->debug .= "acoes->pega_balanco_recursos() -> new Recurso({$id_recurso}) {$diferenca}ms \n";
					}
					
					$this->recursos_consumidos[$id_recurso] = 0;
					$this->recursos_consumidos_nome[$id_recurso] = $recurso[$id_recurso]->nome;

					if (empty($this->recursos_balanco_nome[$id_recurso])) {
						$this->recursos_balanco_nome[$id_recurso] = $recurso[$id_recurso]->nome;
					}
				}

				if (empty($this->recursos_consumidos_planeta[$id_recurso][$this->id_planeta[$chave]])) {
					$this->recursos_consumidos_planeta[$id_recurso][$this->id_planeta[$chave]] = 0;
					
				}

				if (empty($this->recursos_consumidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso])) {
					$this->recursos_consumidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso] = 0;
				}
				
				if ($instalacao[$this->id_instalacao[$chave]]->desguarnecida == 1 && $this->desativado[$chave] == 0) {
					//$this->recursos_consumidos[$id_recurso] = $this->recursos_consumidos[$id_recurso] + floor($instalacao[$this->id_instalacao[$chave]]->recursos_consome_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*10/10);
					$this->recursos_consumidos_planeta[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_consumidos_planeta[$id_recurso][$this->id_planeta[$chave]] + floor($instalacao[$this->id_instalacao[$chave]]->recursos_consome_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*10/10);
					$this->recursos_consumidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso] = $this->recursos_consumidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso] + floor($instalacao[$this->id_instalacao[$chave]]->recursos_consome_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*10/10);
				} else {
					//$this->recursos_consumidos[$id_recurso] = $this->recursos_consumidos[$id_recurso] + floor($instalacao[$this->id_instalacao[$chave]]->recursos_consome_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10);
					$this->recursos_consumidos_planeta[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_consumidos_planeta[$id_recurso][$this->id_planeta[$chave]] + floor($instalacao[$this->id_instalacao[$chave]]->recursos_consome_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10);
					$this->recursos_consumidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso] = $this->recursos_consumidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso] + floor($instalacao[$this->id_instalacao[$chave]]->recursos_consome_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10);
				}
			}
			
			//Pega o consumo fixo das instalações
			if ($this->desativado[$chave] == 0) {
				foreach ($instalacao[$this->id_instalacao[$chave]]->consumo_fixo as $id_consumo_fixo => $id_recurso_fixo) {
					if (empty($this->recursos_consumidos[$id_recurso_fixo])) {
						if (empty($recurso[$id_recurso_fixo])) {
							$recurso[$id_recurso_fixo] = new recurso($id_recurso_fixo);
							$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
							$this->debug .= "acoes->pega_balanco_recursos() -> new Recurso({$id_recurso_fixo}) {$diferenca}ms \n";
						}
						
						$this->recursos_consumidos[$id_recurso_fixo] = 0;
						$this->recursos_consumidos_nome[$id_recurso_fixo] = $recurso[$id_recurso_fixo]->nome;

						if (empty($this->recursos_balanco_nome[$id_recurso_fixo])) {
							$this->recursos_balanco_nome[$id_recurso_fixo] = $recurso[$id_recurso_fixo]->nome;
						}
					}
					
					if (empty($this->recursos_consumidos_planeta[$id_recurso_fixo][$this->id_planeta[$chave]])) {
						$this->recursos_consumidos_planeta[$id_recurso_fixo][$this->id_planeta[$chave]] = 0;
						$this->recursos_consumidos_nome[$id_recurso_fixo] = $recurso[$id_recurso_fixo]->nome;
					}
					
					if (empty($this->recursos_consumidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso_fixo])) {
						$this->recursos_consumidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso_fixo] = 0;
						$this->recursos_consumidos_nome[$id_recurso_fixo] = $recurso[$id_recurso_fixo]->nome;						
					}
					
					//$this->recursos_consumidos[$id_recurso_fixo] = $this->recursos_consumidos[$id_recurso_fixo] + $instalacao[$this->id_instalacao[$chave]]->consumo_fixo_qtd[$id_consumo_fixo];
					$this->recursos_consumidos_planeta[$id_recurso_fixo][$this->id_planeta[$chave]] = $this->recursos_consumidos_planeta[$id_recurso_fixo][$this->id_planeta[$chave]] + $instalacao[$this->id_instalacao[$chave]]->consumo_fixo_qtd[$id_consumo_fixo];
					$this->recursos_consumidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso_fixo] = $this->recursos_consumidos_id_planeta_instalacoes[$this->id_planeta_instalacoes[$chave]][$id_recurso_fixo] + $instalacao[$this->id_instalacao[$chave]]->consumo_fixo_qtd[$id_consumo_fixo];
				}
			}
		}
		
		$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
		$this->debug .= "acoes->pega_balanco_recursos() -> foreach() Produção e Consumo das Instalações {$diferenca}ms \n";

		//Calcula os recursos produzidos totais
		foreach ($this->recursos_produzidos_nome as $id_recurso => $valor) {
			if (empty($this->recursos_produzidos[$id_recurso])) {
				$this->recursos_produzidos[$id_recurso]	= 0;
			}
			
			foreach ($this->recursos_produzidos_planeta[$id_recurso] as $id_planeta => $qtd_produzida_planeta) {
				if (empty($this->recursos_produzidos_planeta_bonus[$id_recurso][$id_planeta])) {
					$this->recursos_produzidos_planeta_bonus[$id_recurso][$id_planeta] = 0;
				}
				
				$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
				$this->debug .= "acoes->pega_balanco_recursos() -> Recursos Produzidos {$this->recursos_balanco_nome[$id_recurso]}:{$this->recursos_produzidos[$id_recurso]} + Planeta({$id_planeta})=>{$qtd_produzida_planeta} + {$this->recursos_produzidos_planeta_bonus[$id_recurso][$id_planeta]} \n";
				$this->recursos_produzidos[$id_recurso] = $this->recursos_produzidos[$id_recurso] + $qtd_produzida_planeta + $this->recursos_produzidos_planeta_bonus[$id_recurso][$id_planeta];
				//$this->recursos_produzidos_planeta[$id_recurso][$id_planeta] = $this->recursos_produzidos_planeta[$id_recurso][$id_planeta]; //+ $this->recursos_produzidos_planeta_bonus[$id_recurso][$id_planeta];
			}
		}
		$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
		$this->debug .= "acoes->pega_balanco_recursos() -> foreach() Produção Total {$diferenca}ms \n";

		/************
		*** AGORA AS NAVES PRODUZEM PESQUISA AO CHEGAR NUM NOVO SISTEMA
		//As naves podem produzir Pesquisa
		$pesquisa_naves = 0;
		$frota = $wpdb->get_results("SELECT id FROM colonization_imperio_frota
		WHERE id_imperio = {$this->id_imperio} AND pesquisa = 1 AND turno_destruido=0");
		
		foreach ($frota as $id) {
			$nave = new frota($id->id);
			$id_estrela = $wpdb->get_var("SELECT id FROM colonization_estrela WHERE X={$nave->X} AND Y={$nave->Y} AND Z={$nave->Z}");
			
			$pesquisa_anterior = "";
			if (!empty($id_estrela)) {
				$pesquisa_anterior = $wpdb->get_var("SELECT id FROM colonization_imperio_historico_pesquisa  WHERE id_imperio={$this->id_imperio} AND id_estrela={$id_estrela}");
				if (empty($pesquisa_anterior)) {//O sistema ainda não foi pesquisado, pode adicionar o bônus de pesquisa!
					$pesquisa_naves = $pesquisa_naves + $nave->qtd * (5 + $imperio->bonus_pesquisa_naves);
				}
			}
		}
		
		if (empty($this->recursos_produzidos[$id_pesquisa])) {
			$this->recursos_produzidos[$id_pesquisa] = $pesquisa_naves;
		} else {
			$this->recursos_produzidos[$id_pesquisa] = $this->recursos_produzidos[$id_pesquisa] + $pesquisa_naves;
		}
		***********/
		
		$ids_colonia = $wpdb->get_results("SELECT id, id_planeta FROM colonization_imperio_colonias WHERE id_imperio={$this->id_imperio} AND turno={$this->turno->turno}");

		//Adiciona o consumo de alimentos (e energia para os Robôs) para cada colônia e faz o Balanço da Produção e do Consumo de cada planeta
		$recurso_alimento = new recurso($id_alimento);
		$recurso_energia = new recurso($id_energia);
		$recurso_poluicao = new recurso($id_poluicao);
		
		foreach ($ids_colonia as $id) {
			if ($flag_novo_balanco) { //Somente calcula o consumo de Alimentos, Energia e Poluição das colônias se for um NOVO balanço
				if (empty($colonia[$id->id])) {
					$colonia[$id->id] = new colonia($id->id,$this->turno->turno);
					$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
				}	
				
				if (empty($planeta[$id->id_planeta])) {
					$planeta[$id->id_planeta] = new planeta($id->id_planeta,$this->turno->turno);
				}

				if (empty($this->recursos_consumidos[$id_alimento])) {
					$this->recursos_consumidos[$id_alimento] = 0;
					$this->recursos_consumidos_nome[$id_alimento] = $recurso_alimento->nome;
					$this->recursos_balanco_nome[$id_alimento] = $recurso_alimento->nome;
				}
				
				if (empty($this->recursos_consumidos_planeta[$id_alimento][$id->id_planeta])) {
					$this->recursos_consumidos_planeta[$id_alimento][$id->id_planeta] = 0;
				}
				
				if (empty($this->recursos_consumidos[$id_energia])) {
					$this->recursos_consumidos[$id_energia] = 0;
					$this->recursos_consumidos_nome[$id_energia] = $recurso_energia->nome;
					$this->recursos_balanco_nome[$id_energia] = $recurso_energia->nome;
				}

				if (empty($this->recursos_consumidos_planeta[$id_energia][$id->id_planeta])) {
					$this->recursos_consumidos_planeta[$id_energia][$id->id_planeta] = 0;
				}			

				if (empty($this->recursos_consumidos[$id_poluicao])) {
					$this->recursos_consumidos[$id_poluicao] = 0;
					$this->recursos_consumidos_nome[$id_poluicao] = $recurso_poluicao->nome;
					$this->recursos_balanco_nome[$id_poluicao] = $recurso_poluicao->nome;
				}			

				if (empty($this->recursos_consumidos_planeta[$id_poluicao][$id->id_planeta])) {
					$this->recursos_consumidos_planeta[$id_poluicao][$id->id_planeta] = 0;
				}						
				
				if (empty($this->recursos_consumidos_planeta[$id_alimento][$id->id_planeta])) {
					$this->recursos_consumidos_planeta[$id_alimento][$id->id_planeta] = $colonia[$id->id]->pega_alimentos_consumidos_planeta($imperio->alimento_inospito, $imperio->limite_poluicao);
				} else {
					$this->recursos_consumidos_planeta[$id_alimento][$id->id_planeta] = $this->recursos_consumidos_planeta[$id_alimento][$id->id_planeta] + $colonia[$id->id]->pega_alimentos_consumidos_planeta($imperio->alimento_inospito, $imperio->limite_poluicao);
				}
				
				//E por fim calcula o total de recursos de alimento considerando as Instalações, Pop e condições especiais
				//$this->recursos_consumidos[$id_alimento] = $this->recursos_consumidos[$id_alimento] + $this->recursos_consumidos_planeta[$id_alimento][$id->id_planeta];
				
				//Pop Robótica consome ENERGIA!
				//Primeiro desconsidera o consumo de energia pelas unidades do planeta
				//$this->recursos_consumidos[$id_energia] = $this->recursos_consumidos[$id_energia] - $this->recursos_consumidos_planeta[$id_energia][$id->id_planeta];
				//Depois recalcula o consumo de energia do planeta considerando o consumo da Pop Robótica
				if (empty($this->recursos_consumidos_planeta[$id_energia][$id->id_planeta])) {
					$this->recursos_consumidos_planeta[$id_energia][$id->id_planeta] = $colonia[$id->id]->pop_robotica;
				} else {
					$this->recursos_consumidos_planeta[$id_energia][$id->id_planeta] = $this->recursos_consumidos_planeta[$id_energia][$id->id_planeta] + $colonia[$id->id]->pop_robotica;
				}
				
				//E por fim acerta o consumo global levando em consideração o consumo das unidades E da Pop Robótica
				//$this->recursos_consumidos[$id_energia] = $this->recursos_consumidos[$id_energia] + $this->recursos_consumidos_planeta[$id_energia][$id->id_planeta];
				
				//A base de consumo de poluição de planetas habitáveis é de 25 unidades
				//Mas Techs Auxiliares podem afetar esse valor
				if ($planeta[$id->id_planeta]->inospito == 0 || $planeta[$id->id_planeta]->terraforma == 1) {
					if (empty($this->recursos_consumidos_planeta[$id_poluicao][$id->id_planeta])) {
						$this->recursos_consumidos_planeta[$id_poluicao][$id->id_planeta] =  25 + $imperio->consome_poluicao;
					} else {
						$this->recursos_consumidos_planeta[$id_poluicao][$id->id_planeta] = $this->recursos_consumidos_planeta[$id_poluicao][$id->id_planeta] + 25 + $imperio->consome_poluicao;
					}
				}
			}
			
			//Faz o balanço de todos os recursos produzidos e/ou consumidos no planeta
			foreach ($this->recursos_balanco_nome as $id_recurso => $nome) {
				if (empty($this->recursos_produzidos_planeta[$id_recurso][$id->id_planeta])) {
					$this->recursos_produzidos_planeta[$id_recurso][$id->id_planeta] = 0;
				}
				
				if (empty($this->recursos_consumidos_planeta[$id_recurso][$id->id_planeta])) {
					$this->recursos_consumidos_planeta[$id_recurso][$id->id_planeta] = 0;
				}
				
				$this->recursos_balanco_planeta[$id_recurso][$id->id_planeta] = $this->recursos_produzidos_planeta[$id_recurso][$id->id_planeta] - $this->recursos_consumidos_planeta[$id_recurso][$id->id_planeta];
				$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
				$this->debug .= "acoes->pega_balanco_recursos() -> Balanço Recursos {$this->recursos_balanco_nome[$id_recurso]}: Planeta({$id->id_planeta})=>{$this->recursos_balanco_planeta[$id_recurso][$id->id_planeta]}={$this->recursos_produzidos_planeta[$id_recurso][$id->id_planeta]} - {$this->recursos_consumidos_planeta[$id_recurso][$id->id_planeta]} \n";
			}
		}
		
		//Calcula os recursos consumidos totais
		foreach ($this->recursos_consumidos_nome as $id_recurso => $valor) {
			if (empty($this->recursos_consumidos[$id_recurso])) {
				$this->recursos_consumidos[$id_recurso]	= 0;
			}
			
			foreach ($this->recursos_consumidos_planeta[$id_recurso] as $id_planeta => $qtd_consumida_planeta) {
				$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
				$this->debug .= "acoes->pega_balanco_recursos() -> Recursos Consumidos {$this->recursos_balanco_nome[$id_recurso]}:{$this->recursos_consumidos[$id_recurso]} + Planeta({$id_planeta})=>{$qtd_consumida_planeta} \n";
				$this->recursos_consumidos[$id_recurso] = $this->recursos_consumidos[$id_recurso] + $qtd_consumida_planeta;
			}
		}		
		$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
		$this->debug .= "acoes->pega_balanco_recursos() -> foreach() Balanço Colônias {$diferenca}ms \n";
		
		//Faz o Balanço da Produção e do Consumo
		foreach ($this->recursos_balanco_nome as $id_recurso => $nome) {
			if (empty($recurso[$id_recurso])) {
				$recurso[$id_recurso] = new recurso($id_recurso);
			}
			if (empty($this->recursos_produzidos[$id_recurso])) {
				$this->recursos_produzidos[$id_recurso] = 0;
			}
			
			if (empty($this->recursos_consumidos[$id_recurso])) {
				$this->recursos_consumidos[$id_recurso] = 0;
			}
				
			if ($recurso[$id_recurso]->local == 0) {
				$this->recursos_balanco[$id_recurso] = floor($this->recursos_produzidos[$id_recurso] - $this->recursos_consumidos[$id_recurso]);
				$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
				$this->debug .= "acoes->pega_balanco_recursos() -> Balanço Recurso {$this->recursos_balanco_nome[$id_recurso]}: {$this->recursos_produzidos[$id_recurso]} - {$this->recursos_consumidos[$id_recurso]} {$diferenca}ms \n";
			}
		}
		$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
		$this->debug .= "acoes->pega_balanco_recursos() -> foreach() Balanço {$diferenca}ms \n";
		
		
		//Salva todas as variáveis globais de balanço e produção no Banco de Dados
		if ($flag_novo_balanco || $salva_balanco === true) {
			$balancos_db = [];//Zera a variável para salvar
			$balancos_db['recursos_produzidos_planeta_instalacao'] = $this->recursos_produzidos_planeta_instalacao;
			$balancos_db['recursos_produzidos_planeta_bonus'] = $this->recursos_produzidos_planeta_bonus;
			$balancos_db['recursos_produzidos_planeta_instalacao_bonus'] = $this->recursos_produzidos_planeta_instalacao_bonus;
			$balancos_db['recursos_produzidos_planeta'] = $this->recursos_produzidos_planeta;
			$balancos_db['recursos_produzidos_id_planeta_instalacoes'] = $this->recursos_produzidos_id_planeta_instalacoes;

			$balancos_db['recursos_extraidos_planeta'] = $this->recursos_extraidos_planeta;	

			$balancos_db['recursos_consumidos_planeta'] = $this->recursos_consumidos_planeta;
			$balancos_db['recursos_consumidos_id_planeta_instalacoes'] = $this->recursos_consumidos_id_planeta_instalacoes;

			$balancos_db['recursos_produzidos_nome'] = $this->recursos_produzidos_nome;
			$balancos_db['recursos_consumidos_nome'] = $this->recursos_consumidos_nome;
			$balancos_db['recursos_balanco_nome'] = $this->recursos_balanco_nome;
			
			//$balancos_db['recursos_produzidos'] = $this->recursos_produzidos;
			//$balancos_db['recursos_consumidos'] = $this->recursos_consumidos;
			//$balancos_db['recursos_balanco'] = $this->recursos_balanco;
			//$balancos_db['recursos_balanco_planeta'] = $this->recursos_balanco_planeta;		
			
			$balancos_db = json_encode($balancos_db, JSON_UNESCAPED_UNICODE);
			$wpdb->query("DELETE FROM colonization_balancos_turno WHERE id_imperio = {$this->id_imperio} AND turno = {$this->turno->turno}");
			$wpdb->query("INSERT INTO colonization_balancos_turno SET json_balancos = '{$balancos_db}', id_imperio = {$this->id_imperio}, turno = {$this->turno->turno}");
		}
	}


	/***********************
	function exibe_recursos_produzidos()
	----------------------
	Exibe os recursos produzidos pelas ações
	***********************/
	function exibe_recursos_produzidos() {
		global $wpdb;

		setlocale (LC_ALL, 'pt_BR');
		asort($this->recursos_produzidos_nome,SORT_LOCALE_STRING);

		$html = "<b>Recursos Produzidos:</b> ";
		foreach ($this->recursos_produzidos_nome as $id_recurso => $valor) {
			$html .= "{$valor}: {$this->recursos_produzidos[$id_recurso]}; ";
		}
		
		return $html;
	}

	/***********************
	function exibe_recursos_consumidos()
	----------------------
	Exibe os recursos consumidos pelas ações
	***********************/
	function exibe_recursos_consumidos() {
		global $wpdb;

		setlocale (LC_ALL, 'pt_BR');
		asort($this->recursos_consumidos_nome,SORT_LOCALE_STRING);
		
		$html = "<b>Recursos Consumidos:</b> ";
		foreach ($this->recursos_consumidos_nome as $id_recurso => $valor) {
			$html .= "{$valor}: {$this->recursos_consumidos[$id_recurso]}; ";
		}
	
		return $html;
	}

	/***********************
	function exibe_recursos_balanco()
	----------------------
	Exibe o balanço dos recursos
	***********************/
	function exibe_recursos_balanco() {
		global $wpdb;

		$html = "<span style='color: #2f4f4f ; font-weight: bold;'>Balanço dos Recursos:</span> ";			
		asort($this->recursos_balanco,SORT_NUMERIC);
		foreach ($this->recursos_balanco as $id_recurso => $qtd) {
			if ($qtd > 0) {
				$html .= "{$this->recursos_balanco_nome[$id_recurso]}: {$qtd}; ";
			} elseif ($qtd < 0) {
				$html .= "{$this->recursos_balanco_nome[$id_recurso]}: <span style='color: #FF2222;'>{$qtd}</span>; ";
			}
		}
		
		return $html;
	}

	/***********************
	function exibe_balanco_planeta()
	----------------------
	Exibe o balanço dos recursos de um planeta
	
	$id_planeta - id do planeta a exibir
	***********************/
	function exibe_balanco_planeta($id_planeta) {
		$balanco_temp = [];
		$balanco_planeta = "<span style='color: #2f4f4f ; font-weight: bold;'>Balanço dos Recursos:</span> ";
		foreach ($this->recursos_balanco_nome as $id_recurso => $nome) {
			if (!empty($this->recursos_balanco_planeta[$id_recurso][$id_planeta])) {
				$balanco_temp[$id_recurso] = $this->recursos_balanco_planeta[$id_recurso][$id_planeta];
			}
		}
		
		if (!empty($balanco_temp)) {
			asort($balanco_temp,SORT_NUMERIC);
			
			foreach ($balanco_temp as $id_recurso =>$qtd) {
				if ($qtd != 0) {
					$recurso = new recurso ($id_recurso);
					if ($qtd < 0) {
						$html_qtd = "<span style='color: #DD0000;'>{$qtd}</span>";
					} else {
						$html_qtd = $qtd;
					}
					
					if(empty($this->recursos_produzidos_planeta_bonus[$id_recurso][$id_planeta])) {
						$this->recursos_produzidos_planeta_bonus[$id_recurso][$id_planeta] = 0;
					}
					//$html_qtd = "<span style='color: #23A455;'>{$this->recursos_produzidos_planeta[$id_recurso][$id_planeta]} + {$this->recursos_produzidos_planeta_bonus[$id_recurso][$id_planeta]}</span> <span style='color: #DD0000;'>-{$this->recursos_consumidos_planeta[$id_recurso][$id_planeta]}</span>";
					$balanco_planeta .= "{$recurso->nome}: {$html_qtd}; ";
				}
			}
		}
		
		return $balanco_planeta;
	}

}
?>
