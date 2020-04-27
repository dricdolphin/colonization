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
	public $recursos_balanco_nome = [];
	public $disabled = "";
	
	
	
	function __construct($id_imperio, $turno=0) {
		global $wpdb;
		
		//É necessário pegar o id_imperio à partir do objeto "Império", pois este contém a validação do jogador
		$this->turno = new turno($turno);
		if ($turno > $this->turno->turno) {
			$this->disabled = 'disabled';
		}
					
		$imperio = new imperio($id_imperio, false, $this->turno->turno);
		$this->id_imperio = $imperio->id;

		$resultados =$wpdb->get_results("
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
			ORDER BY ce.X, ce.Y, ce.Z, cp.posicao, cpi.id_planeta, ci.nome, cpi.id
			");
		
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
			$chave++;
		}

		$chave = 0;
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
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	----
	$turno_atual = somente libera para edição se o Turno exibido for o Turno atual
	***********************/
	function lista_dados($turno_atual=true) {
		global $wpdb;
		
		if ($turno_atual) {
			$this->disabled = "";
		} else {
			$turno_atual = new turno();
			if ($this->turno->turno != $turno_atual->turno || $this->turno->encerrado == 1) {
				$this->disabled = "disabled";
			}
		}
		
		$html = "";
		$ultimo_planeta = 0;
		$estilo_par = "style='background-color: #FAFAFA;'";
		$estilo_impar = "style='background-color: #F0F0F0;'";
		$primeira_linha = "&nbsp;";
		
		$estilo = $estilo_impar;
		foreach ($this->id AS $chave => $valor) {
			$planeta = new planeta($this->id_planeta[$chave]);
			$estrela = new estrela($planeta->id_estrela);
			$instalacao = new instalacao($this->id_instalacao[$chave]);
			$colonia = new colonia($this->id_colonia[$chave]);
			$colonia_instalacao = new colonia_instalacao($this->id_planeta_instalacoes[$chave]);
			
			if ($ultimo_planeta != $planeta->id) {
				$slots = 0;
				$primeira_linha = "<td rowspan='{$colonia->instalacoes}'>
				<div data-atributo='nome_planeta'>{$planeta->nome} ({$estrela->X};{$estrela->Y};{$estrela->Z};{$planeta->posicao}) | {$colonia->instalacoes}/{$planeta->tamanho}</div>
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
				$exibe_acoes = "<input data-atributo='pop' data-ajax='true' data-valor-original='{$this->pop[$chave]}' type='range' min='0' max='10' value='{$this->pop[$chave]}' oninput='return altera_acao(event, this);' onmouseup='return efetua_acao(event, this);' {$this->disabled}></input>&nbsp;&nbsp;&nbsp;<label data-atributo='pop' style='width: 20px;'>{$this->pop[$chave]}</label>";
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
		$ids_colonia = $wpdb->get_results("SELECT id, id_planeta FROM colonization_imperio_colonias WHERE id_imperio={$this->id_imperio} AND turno={$this->turno->turno}");
		//Adiciona o consumo de alimentos para cada colônia
		foreach ($ids_colonia as $id) {
			$colonia = new colonia($id->id);
			$recurso = new recurso($id_alimento);

			if (empty($this->recursos_consumidos[$id_alimento])) {
				$this->recursos_consumidos[$id_alimento] = 0;
				$this->recursos_consumidos_planeta[$id_alimento][$id->id_planeta] = 0;
				$this->recursos_consumidos_nome[$id_alimento] = $recurso->nome;
				$this->recursos_balanco_nome[$id_alimento] = $recurso->nome;
			}
			
			if (empty($this->recursos_consumidos_planeta[$id_alimento][$id->id_planeta])) {
				$this->recursos_consumidos_planeta[$id_alimento][$id->id_planeta] = $colonia->pop;
			} else {
				$this->recursos_consumidos_planeta[$id_alimento][$id->id_planeta] = $this->recursos_consumidos_planeta[$id_alimento][$id->id_planeta] + $colonia->pop;
			}
			
			$this->recursos_consumidos[$id_alimento] = $this->recursos_consumidos[$id_alimento] + $colonia->pop;
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

		$html = "";			
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

}
?>
