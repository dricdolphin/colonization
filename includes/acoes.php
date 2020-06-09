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
	public $pop = [];
	public $turno;
	public $data_modifica = [];
	public $max_data_modifica;
	
	//Recursos produzidos, consumidos e balanços
	public $recursos_produzidos_planeta_instalacao = []; //Número de Instalações, por planeta, que geram um determinado recurso
	public $recursos_produzidos_planeta_bonus = []; //Bônus de recursos, por planeta
	public $recursos_produzidos = [];
	public $recursos_produzidos_planeta = [];
	public $recursos_produzidos_nome = [];
	public $recursos_consumidos = [];
	public $recursos_consumidos_planeta = [];
	public $recursos_consumidos_nome = [];
	public $recursos_balanco = [];
	public $recursos_balanco_planeta = [];
	public $recursos_balanco_nome = [];
	public $disabled = "";
	
	
	
	function __construct($id_imperio, $turno=0) {
		global $wpdb;
		
		//É necessário pegar o id_imperio à partir do objeto "Império", pois este contém a validação do jogador
		$this->turno = new turno($turno);
		if ($turno > $this->turno->turno) {
			$this->disabled = 'disabled';
		}
					
		$this->imperio = new imperio($id_imperio, false, $this->turno->turno);
		$this->id_imperio = $this->imperio->id;

		$id_estrelas_imperio = $wpdb->get_results("
		SELECT DISTINCT ce.id
		FROM colonization_imperio_colonias AS cic
		JOIN colonization_planeta AS cp
		ON cp.id = cic.id_planeta
		JOIN colonization_estrela AS ce
		ON ce.id = cp.id_estrela
		WHERE cic.id_imperio = {$this->id_imperio} 
		AND cic.turno = {$this->turno->turno}
		ORDER BY cic.capital DESC, ce.X, ce.Y, ce.Z
		");

		$resultados = [];
		$index = 0;
		foreach ($id_estrelas_imperio as $id_estrela) {
			$resultados_temp = $wpdb->get_results("
				SELECT cic.id AS id_colonia, cic.id_imperio, cat.id AS id, cic.id_planeta AS id_planeta, 
				cpi.id AS id_planeta_instalacoes, cpi.id_instalacao AS id_instalacao, cpi.nivel AS nivel_instalacao, cpi.turno_destroi AS turno_destroi, 
				cat.pop AS pop, cat.data_modifica AS data_modifica
				FROM colonization_imperio_colonias AS cic 
				JOIN colonization_planeta_instalacoes AS cpi
				ON cpi.id_planeta = cic.id_planeta
				LEFT JOIN 
				(SELECT id, id_planeta, id_instalacao, id_planeta_instalacoes, id_imperio, pop, data_modifica
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
				ORDER BY cic.capital DESC, cp.posicao, cpi.id_planeta, ci.nome, cpi.id
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
				$this->data_modifica[$chave] = $this->turno->data_turno;
			} else {
				$this->id[$chave] = $valor->id;
				$this->id_planeta[$chave] = $valor->id_planeta;
				$this->id_colonia[$chave] = $valor->id_colonia;
				$this->id_instalacao[$chave] = $valor->id_instalacao;
				$this->id_planeta_instalacoes[$chave] = $valor->id_planeta_instalacoes;
				$this->nivel_instalacao[$chave] = $valor->nivel_instalacao;
				$this->pop[$chave] = $valor->pop;
				$this->data_modifica[$chave] = $valor->data_modifica;
				
				$turno_upgrade = $wpdb->get_var("SELECT MIN(turno) FROM colonization_planeta_instalacoes_upgrade WHERE id_planeta_instalacoes={$this->id_planeta_instalacoes[$chave]} AND turno > {$this->turno->turno}");
				if ($turno_upgrade > $this->turno->turno) {
					$this->nivel_instalacao[$chave] = $wpdb->get_var("SELECT nivel_anterior FROM colonization_planeta_instalacoes_upgrade WHERE id_planeta_instalacoes={$this->id_planeta_instalacoes[$chave]} AND turno = {$turno_upgrade}");
					$this->id_instalacao[$chave] = $wpdb->get_var("SELECT id_instalacao_anterior FROM colonization_planeta_instalacoes_upgrade WHERE id_planeta_instalacoes={$this->id_planeta_instalacoes[$chave]} AND turno = {$turno_upgrade}");
				}
			}
			if (!empty($valor->turno_destroi)) {
				$this->pop[$chave] = 0;
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
					WHERE id_imperio={$this->id_imperio} AND id_planeta={$this->id_planeta[$chave]} AND turno={$turno_anterior} AND id_planeta_instalacoes={$this->id_planeta_instalacoes[$chave]}");
					
					if ($pop_turno_anterior === null) {
						$wpdb->query("INSERT INTO colonization_acoes_turno 
						SET id_imperio={$this->id_imperio}, id_planeta={$this->id_planeta[$chave]}, id_instalacao={$this->id_instalacao[$chave]}, id_planeta_instalacoes={$this->id_planeta_instalacoes[$chave]},
						pop={$this->pop[$chave]}, data_modifica='{$this->data_modifica[$chave]}', turno={$this->turno->turno}");
					} else {
						$wpdb->query("INSERT INTO colonization_acoes_turno 
						SET id_imperio={$this->id_imperio}, id_planeta={$this->id_planeta[$chave]}, id_instalacao={$this->id_instalacao[$chave]}, id_planeta_instalacoes={$this->id_planeta_instalacoes[$chave]},
						pop={$pop_turno_anterior}, data_modifica='{$this->data_modifica[$chave]}', turno={$this->turno->turno}");
						$this->pop[$chave] = $pop_turno_anterior;
					}
					$this->id[$chave] = $wpdb->insert_id;
				}
			}
		}
		//*****/
		
		$this->pega_balanco_recursos();
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

		for ($chave=0; $chave < count($this->id_planeta); $chave++) {
			if ($this->id_planeta[$chave] == $id_planeta) {
				$mdo = $mdo + $this->pop[$chave];
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
	function exibe_pop_mdo_planeta($id_planeta) {
		global $wpdb;
		
		$id_colonia = $wpdb->get_var("SELECT id FROM colonization_imperio_colonias WHERE id_planeta={$id_planeta} AND id_imperio={$this->imperio->id} AND turno={$this->turno->turno}");
		
		$planeta = new planeta($id_planeta, $this->turno->turno);
		$colonia = new colonia($id_colonia, $this->turno->turno);
		
		$mdo_planeta = $this->mdo_planeta($planeta->id);
		$pop_sistema = $this->imperio->pop_mdo_sistema($planeta->id_estrela);
		$pop_disponivel_sistema = $pop_sistema['pop'] - $pop_sistema['mdo'];		
		
		return "<div style='display: inline-block;' name='mdo_sistema_{$planeta->id_estrela}'>({$pop_disponivel_sistema})</div> {$mdo_planeta}/{$colonia->pop}";
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	----
	$turno_atual = somente libera para edição se o Turno exibido for o Turno atual
	***********************/
	function lista_dados($turno_atual=true) {
		global $wpdb;
		
		$html = "";
		$ultimo_planeta = 0;
		$estilo_par = "style='background-color: #FAFAFA;'";
		$estilo_impar = "style='background-color: #F0F0F0;'";
		$primeira_linha = "&nbsp;";
		
		$estilo = $estilo_impar;
		foreach ($this->id AS $chave => $valor) {
			$planeta = new planeta($this->id_planeta[$chave], $this->turno->turno);
			$estrela = new estrela($planeta->id_estrela);
			$instalacao = new instalacao($this->id_instalacao[$chave]);
			$colonia = new colonia($this->id_colonia[$chave], $this->turno->turno);
			$colonia_instalacao = new colonia_instalacao($this->id_planeta_instalacoes[$chave]);
			
			$user = wp_get_current_user();
			$roles = "";
			if (!empty($user->ID)) {
				$roles = $user->roles[0];		
			}

			$this->disabled = "";
			$turno_atual = new turno();
			if (($this->turno->turno != $turno_atual->turno) || $this->turno->encerrado == 1) {
					$this->disabled = "disabled";
			}

			if ($colonia->vassalo == 1 && $roles != "administrator") {
				$this->disabled = "disabled";
			}
			
			if ($ultimo_planeta != $planeta->id && $instalacao->oculta == 0) {
				$slots = 0;
				$balanco_planeta = "";
				
				$balanco_planeta = $this->exibe_balanco_planeta($planeta->id);
				
				$pop_mdo_planeta = $this->exibe_pop_mdo_planeta($planeta->id);
				
				$primeira_linha = "<td rowspan='{$colonia->num_instalacoes}'>
				<div data-atributo='nome_planeta'>{$colonia->instalacoes}/{$planeta->tamanho} | {$planeta->nome} ({$estrela->X};{$estrela->Y};{$estrela->Z};{$planeta->posicao}) | 
				<div style='display: inline-block;' data-atributo='pop_mdo_planeta' id='pop_mdo_planeta_{$planeta->id}'>{$pop_mdo_planeta}</div>
				<div data-atributo='balanco_recursos_planeta' id='balanco_planeta_{$planeta->id}'>{$balanco_planeta}</div>
				</td>";
				if ($estilo == $estilo_par) {
					$estilo = $estilo_impar;
				} else {
					$estilo = $estilo_par;
				}
				$ultimo_planeta = $planeta->id;
			} else {
				$primeira_linha = "&nbsp;";
			}				
			
			
			switch($this->nivel_instalacao[$chave]) {
			case 1:
				$nivel = "Mk I";
				break;
			case 2:
				$nivel = "Mk II";
				break;
			case 3:
				$nivel = "Mk III";
				break;
			case 4:
				$nivel = "Mk IV";
				break;
			case 5:
				$nivel = "Mk V";
				break;
			case 6:
				$nivel = "Mk VI";
				break;
			case 7:
				$nivel = "Mk VII";
				break;
			case 8:
				$nivel = "Mk VIII";
				break;
			default:
				$nivel = "";
			}
			
			if ($instalacao->desguarnecida == 0) {
				$exibe_acoes = "<input data-atributo='pop' data-ajax='true' data-valor-original='{$this->pop[$chave]}' type='range' min='0' max='10' value='{$this->pop[$chave]}' oninput='return altera_acao(event, this);' onmouseup='return efetua_acao(event, this);' ontouchend='return efetua_acao(event, this);' {$this->disabled}></input>&nbsp;&nbsp;&nbsp;<label data-atributo='pop' style='width: 30px;'>{$this->pop[$chave]}</label>";
			} else {
				$exibe_acoes = "&nbsp";
			}
			
			if (!empty($colonia_instalacao->turno_destroi)) {
				$exibe_acoes = "<span style='color: #DD0000; font-weight: bold;'>DESTRUÍDA!</span>";
			}
			
			if ($instalacao->oculta == 0) {
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
					<input type='hidden' data-atributo='where_value' value='{$this->id[$chave]}'></input>
					<input type='hidden' data-atributo='funcao_validacao' value='valida_acao'></input>
					<div data-atributo='nome_instalacao' data-valor-original='{$instalacao->nome}'>{$instalacao->nome} {$nivel}</div>
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
	function pega_balanco_recursos()
	----------------------
	Pega os Recursos produzidos, consumidos e seu balanço
	***********************/
	function pega_balanco_recursos() {
		global $wpdb;
		
		$bonus_sinergia_tech = 0;
		$instalacao_tech = 0;
		$imperio = new imperio($this->id_imperio);

		$this->recursos_produzidos_planeta_instalacao = []; //Número de Instalações, por planeta, que geram um determinado recurso
		$this->recursos_produzidos_planeta_bonus = []; //Bônus de recursos, por planeta
		$this->recursos_produzidos = [];
		$this->recursos_produzidos_planeta = [];
		$this->recursos_produzidos_nome = [];
		$this->recursos_consumidos = [];
		$this->recursos_consumidos_planeta = [];
		$this->recursos_consumidos_nome = [];
		$this->recursos_balanco = [];
		$this->recursos_balanco_nome = [];
		$this->recursos_balanco_planeta = [];
		
		
		//Pega a produção das Instalações
		foreach ($this->id AS $chave => $valor) {
			$colonia_instalacao = new colonia_instalacao($this->id_planeta_instalacoes[$chave]);
			$instalacao = new instalacao($colonia_instalacao->id_instalacao);
			
			foreach ($instalacao->recursos_produz as $chave_recursos => $id_recurso) {
				if (empty($this->recursos_produzidos[$id_recurso])) {
					$recurso = new recurso($id_recurso);

					$this->recursos_produzidos[$id_recurso] = 0;
					$this->recursos_produzidos_nome[$id_recurso] = $recurso->nome;
					$this->recursos_balanco_nome[$id_recurso] = $recurso->nome;
				}
				
				if (empty($this->recursos_produzidos_planeta[$id_recurso][$this->id_planeta[$chave]])) {
					$this->recursos_produzidos_planeta[$id_recurso][$this->id_planeta[$chave]] = 0;
					$this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] = 0;
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
				
				if ($instalacao->desguarnecida == 1) {
					//$this->recursos_produzidos[$id_recurso] = $this->recursos_produzidos[$id_recurso] + floor($instalacao->recursos_produz_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*10/10);
					$this->recursos_produzidos_planeta[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_produzidos_planeta[$id_recurso][$this->id_planeta[$chave]] + floor($instalacao->recursos_produz_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*10/10);					
				}
				
				//$this->recursos_produzidos[$id_recurso] = $this->recursos_produzidos[$id_recurso] + floor($instalacao->recursos_produz_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10);
				$this->recursos_produzidos_planeta[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_produzidos_planeta[$id_recurso][$this->id_planeta[$chave]] + floor($instalacao->recursos_produz_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10);
				/***************************************************
				--- MODIFICAÇÕES NA PRODUÇÃO DEVIDO À TECHS ---
				***************************************************/
				//ESPECIAIS -- Bônus de produção
				
				//public $bonus_recurso = [];
				//public $sinergia = [];
				//public $extrativo = [];
				//public $max_bonus_recurso = [];

				if (!empty($imperio->bonus_recurso['*'])) {//Tem bônus para TODOS os recursos
					//Verifica as condições
					if (!empty($imperio->extrativo['*'])) {//O bônus se aplica somente para recursos EXTRATIVOS
						if ($imperio->extrativo['*'] === true && $recurso->extrativo == 1) {
							$this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] + intval(floor(floor($instalacao->recursos_produz_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10)*(($imperio->bonus_recurso['*'])/100)));
						}
					} else {//O bônus se aplica à qualquer tipo de recurso
						$this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] + intval(floor(floor($instalacao->recursos_produz_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10)*(($imperio->bonus_recurso['*'])/100)));
					}
				} 
				
				if (!empty($imperio->bonus_recurso[$id_recurso])) {//Tem bônus para o recurso ATUAL
					//Verifica as condições
					if (!empty($imperio->extrativo[$id_recurso])) {//O bônus se aplica somente para recursos EXTRATIVOS
						if ($imperio->extrativo[$id_recurso] === true && $recurso->extrativo == 1) {
							$this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] + intval(floor(floor($instalacao->recursos_produz_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10)*(($imperio->bonus_recurso[$id_recurso])/100)));
						}
					} else {//O bônus se aplica à qualquer tipo de recurso
						$this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_produzidos_planeta_bonus[$id_recurso][$this->id_planeta[$chave]] + intval(floor(floor($instalacao->recursos_produz_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10)*(($imperio->bonus_recurso[$id_recurso])/100)));
					}
				}
			}
		}
		
		//Calcula os recursos produzidos totais
		foreach ($this->recursos_produzidos as $id_recurso => $valor) {
			foreach ($this->recursos_produzidos_planeta[$id_recurso] as $id_planeta => $qtd_produzida_planeta) {
				if (empty($this->recursos_produzidos_planeta_bonus[$id_recurso][$id_planeta])) {
					$this->recursos_produzidos_planeta_bonus[$id_recurso][$id_planeta] = 0;
				}
				
				if (!empty($imperio->max_bonus_recurso[$id_recurso])) {
					if ($imperio->max_bonus_recurso[$id_recurso] !== false) {//Tem um limite de bônus
						if ($this->recursos_produzidos_planeta_bonus[$id_recurso][$id_planeta] > $imperio->max_bonus_recurso[$id_recurso]) {
							$this->recursos_produzidos_planeta_bonus[$id_recurso][$id_planeta] = $imperio->max_bonus_recurso[$id_recurso];
						}
					}
				}
				
				if (!empty($imperio->sinergia[$id_recurso])) {
					if ($imperio->sinergia[$id_recurso] === true) {
						if ($this->recursos_produzidos_planeta_instalacao[$id_recurso][$id_planeta] < 2) {//Para ter o bônus de sinergia, precisa de DUAS instalações
							$this->recursos_produzidos_planeta_bonus[$id_recurso][$id_planeta] = 0;
						}
					}
				}
				
				$this->recursos_produzidos[$id_recurso] = $this->recursos_produzidos[$id_recurso] + $qtd_produzida_planeta + $this->recursos_produzidos_planeta_bonus[$id_recurso][$id_planeta];
				$this->recursos_produzidos_planeta[$id_recurso][$id_planeta] = $this->recursos_produzidos_planeta[$id_recurso][$id_planeta] + $this->recursos_produzidos_planeta_bonus[$id_recurso][$id_planeta];
			}
		}

		//As naves podem produzir Pesquisa
		$id_pesquisa = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='Pesquisa'");
		
		$pesquisa_naves = 0;

		/***
		$naves = $wpdb->get_var("SELECT SUM(qtd) FROM colonization_imperio_frota
		WHERE id_imperio = {$this->id_imperio} AND pesquisa = 1");
		//***/
		
		$frota = $wpdb->get_results("SELECT id FROM colonization_imperio_frota
		WHERE id_imperio = {$this->id_imperio} AND pesquisa = 1");
		
		foreach ($frota as $id) {
			$nave = new frota($id->id);
			$id_estrela = $wpdb->get_var("SELECT id FROM colonization_estrela WHERE X={$nave->X} AND Y={$nave->Y} AND Z={$nave->Z}");
			
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

		//Pega o Consumo das Instalações
		foreach ($this->id AS $chave => $valor) {
			$colonia_instalacao = new colonia_instalacao($this->id_planeta_instalacoes[$chave]);
			$instalacao = new instalacao($colonia_instalacao->id_instalacao);
			
			foreach ($instalacao->recursos_consome as $chave_recursos => $id_recurso) {
				if (empty($this->recursos_consumidos[$id_recurso])) {
					$recurso = new recurso($id_recurso);
					
					$this->recursos_consumidos[$id_recurso] = 0;
					$this->recursos_consumidos_planeta[$id_recurso][$this->id_planeta[$chave]] = 0;
					$this->recursos_consumidos_nome[$id_recurso] = $recurso->nome;

					if (empty($this->recursos_balanco_nome[$id_recurso])) {
						$this->recursos_balanco_nome[$id_recurso] = $recurso->nome;
					}
				}
				if (empty($this->recursos_consumidos_planeta[$id_recurso][$this->id_planeta[$chave]])) {
					$this->recursos_consumidos_planeta[$id_recurso][$this->id_planeta[$chave]] = 0;
				}
				if ($instalacao->desguarnecida == 1) {
					$this->recursos_consumidos[$id_recurso] = $this->recursos_consumidos[$id_recurso] + floor($instalacao->recursos_consome_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*10/10);
					$this->recursos_consumidos_planeta[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_consumidos_planeta[$id_recurso][$this->id_planeta[$chave]] + floor($instalacao->recursos_consome_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*10/10);					
				}
				$this->recursos_consumidos[$id_recurso] = $this->recursos_consumidos[$id_recurso] + floor($instalacao->recursos_consome_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10);
				$this->recursos_consumidos_planeta[$id_recurso][$this->id_planeta[$chave]] = $this->recursos_consumidos_planeta[$id_recurso][$this->id_planeta[$chave]] + floor($instalacao->recursos_consome_qtd[$chave_recursos]*$this->nivel_instalacao[$chave]*$this->pop[$chave]/10);
				/***************************************************
				--- MODIFICAÇÕES ESPECIAIS NO BALANÇO DO TURNO ---
				***************************************************/


			}
		}
		
		$id_alimento = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome = 'Alimentos'");
		$id_energia = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome = 'Energia'");
		$ids_colonia = $wpdb->get_results("SELECT id, id_planeta FROM colonization_imperio_colonias WHERE id_imperio={$this->id_imperio} AND turno={$this->turno->turno}");
		//Adiciona o consumo de alimentos (e energia para os Robôs) para cada colônia e faz o Balanço da Produção e do Consumo de cada planeta
		foreach ($ids_colonia as $id) {
			$colonia = new colonia($id->id);
			$planeta = new planeta($id->id_planeta);
			$recurso_alimento = new recurso($id_alimento);
			$recurso_energia = new recurso($id_energia);

			if (empty($this->recursos_consumidos[$id_alimento])) {
				$this->recursos_consumidos[$id_alimento] = 0;
				$this->recursos_consumidos_planeta[$id_alimento][$id->id_planeta] = 0;
				$this->recursos_consumidos_nome[$id_alimento] = $recurso_alimento->nome;
				$this->recursos_balanco_nome[$id_alimento] = $recurso_alimento->nome;
			}
			
			if (empty($this->recursos_consumidos[$id_energia])) {
				$this->recursos_consumidos[$id_energia] = 0;
				$this->recursos_consumidos_planeta[$id_energia][$id->id_planeta] = 0;
				$this->recursos_consumidos_nome[$id_energia] = $recurso_energia->nome;
				$this->recursos_balanco_nome[$id_energia] = $recurso_energia->nome;
			}			
			
			if (empty($this->recursos_consumidos_planeta[$id_alimento][$id->id_planeta])) {
				$this->recursos_consumidos_planeta[$id_alimento][$id->id_planeta] = $colonia->pop;
			} else {
				$this->recursos_consumidos_planeta[$id_alimento][$id->id_planeta] = $this->recursos_consumidos_planeta[$id_alimento][$id->id_planeta] + $colonia->pop;
			}
			
			//Existem algumas Techs que aumentam o consumo
			$consumo_extra_inospito = 0;
			if ($planeta->inospito == 1 ) {
				if ($colonia->pop > $planeta->pop_inospito) {
					$pop_inospito = $colonia->pop - $planeta->pop_inospito;
					$consumo_extra_inospito = $pop_inospito * $imperio->alimento_inospito;
				}
			}
			
			$this->recursos_consumidos[$id_alimento] = $this->recursos_consumidos[$id_alimento] + $colonia->pop + $consumo_extra_inospito;

			if (empty($this->recursos_consumidos_planeta[$id_energia][$id->id_planeta])) {
				$this->recursos_consumidos_planeta[$id_energia][$id->id_planeta] = $colonia->pop_robotica;
			} else {
				$this->recursos_consumidos_planeta[$id_energia][$id->id_planeta] = $this->recursos_consumidos_planeta[$id_energia][$id->id_planeta] + $colonia->pop_robotica;
			}
			
			$this->recursos_consumidos[$id_energia] = $this->recursos_consumidos[$id_energia] + $colonia->pop_robotica;
			
			foreach ($this->recursos_balanco_nome as $id_recurso => $nome) {
				if (empty($this->recursos_produzidos_planeta[$id_recurso][$id->id_planeta])) {
					$this->recursos_produzidos_planeta[$id_recurso][$id->id_planeta] = 0;
				}
				
				if (empty($this->recursos_consumidos_planeta[$id_recurso][$id->id_planeta])) {
					$this->recursos_consumidos_planeta[$id_recurso][$id->id_planeta] = 0;
				}

				$this->recursos_balanco_planeta[$id_recurso][$id->id_planeta] = $this->recursos_produzidos_planeta[$id_recurso][$id->id_planeta] - $this->recursos_consumidos_planeta[$id_recurso][$id->id_planeta];
			}
		}
		
		//Faz o Balanço da Produção e do Consumo
		foreach ($this->recursos_balanco_nome as $id_recurso => $nome) {
			if (empty($this->recursos_produzidos[$id_recurso])) {
				$this->recursos_produzidos[$id_recurso] = 0;
			}
			
			if (empty($this->recursos_consumidos[$id_recurso])) {
				$this->recursos_consumidos[$id_recurso] = 0;
			}

			$this->recursos_balanco[$id_recurso] = $this->recursos_produzidos[$id_recurso] - $this->recursos_consumidos[$id_recurso];
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

		$html = "<b>Balanço dos Recursos:</b> ";			
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

	function exibe_balanco_planeta($id_planeta) {
		$balanco_temp = [];
		$balanco_planeta = "<b>Balanço dos Recursos:</b> ";
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
					$balanco_planeta .= "{$recurso->nome}: {$html_qtd}; ";
				}
			}
		}
		
		return $balanco_planeta;
	}

}
?>
