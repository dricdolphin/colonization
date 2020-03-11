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
	public $id_instalacao = [];
	public $pop = [];
	public $turno;
	public $data_modifica = [];
	
	function __construct($id_imperio, $turno=0) {
		global $wpdb;
		
		$this->id_imperio = $id_imperio;
		$this->turno = new turno($turno);

		$resultados =$wpdb->get_results("
			SELECT cic.id_imperio, cat.id AS id, cic.id_planeta AS id_planeta, cpi.id_instalacao AS id_instalacao, cat.pop AS pop, cat.data_modifica AS data_modifica
			FROM colonization_imperio_colonias AS cic 
			JOIN colonization_planeta_instalacoes AS cpi
			ON cpi.id_planeta = cic.id_planeta
			LEFT JOIN 
			(SELECT id, id_planeta, id_instalacao, id_imperio, pop, data_modifica
			 FROM colonization_acoes_turno
			 WHERE id_imperio={$this->id_imperio} AND turno={$this->turno->turno}
			) AS cat
			ON cat.id_planeta = cic.id_planeta
			AND cat.id_instalacao = cpi.id_instalacao
			AND cat.id_imperio = cic.id_imperio
			JOIN colonization_instalacao AS ci
			ON ci.id = cpi.id_instalacao
			JOIN colonization_planeta AS cp
			ON cp.id = cic.id_planeta
			JOIN colonization_estrela AS ce
			ON ce.id = cp.id_estrela
			WHERE cic.id_imperio = {$this->id_imperio}
			ORDER BY ce.X, ce.Y, ce.Z, cp.posicao, cpi.id
			");
		
		$chave = 0;
		foreach ($resultados as $valor) {
			if ($valor->id === null) {
				$this->id[$chave] = 0;
				$this->id_planeta[$chave] = $valor->id_planeta;
				$this->id_instalacao[$chave] = $valor->id_instalacao;
				$this->pop[$chave] = 0;
				$this->data_modifica[$chave] = $this->turno->data_turno;
			} else {
				$this->id[$chave] = $valor->id;
				$this->id_planeta[$chave] = $valor->id_planeta;
				$this->id_instalacao[$chave] = $valor->id_instalacao;
				$this->pop[$chave] = $valor->pop;
				$this->data_modifica[$chave] = $valor->data_modifica;
			}
			$chave++;
		}
		
		$chave = 0;
		if (isset($this->id[$chave])) {
			if ($this->id[$chave] == 0) {//As chaves estão em branco, vamos criá-las!
				foreach ($this->id as $chave => $valor) {
					$wpdb->query("INSERT INTO colonization_acoes_turno 
					SET id_imperio={$this->id_imperio}, id_planeta={$this->id_planeta[$chave]}, id_instalacao={$this->id_instalacao[$chave]}, 
					pop={$this->pop[$chave]}, data_modifica='{$this->data_modifica[$chave]}', turno={$this->turno->turno}");
					$this->id[$chave] = $wpdb->insert_id;;
				}
			}
		}
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;
		
		$html = "";
		foreach ($this->id AS $chave => $valor) {
			$planeta = new planeta($this->id_planeta[$chave]);
			$estrela = new estrela($planeta->id_estrela);
			$instalacao = new instalacao($this->id_instalacao[$chave]);
			
			$html .= "		<tr><td>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id[$chave]}' value='{$this->id[$chave]}'></input>
				<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='{$this->id_imperio}' value='{$this->id_imperio}'></input>
				<input type='hidden' data-atributo='id_planeta' data-ajax='true' data-valor-original='{$this->id_planeta[$chave]}' value='{$this->id_planeta[$chave]}'></input>
				<input type='hidden' data-atributo='id_instalacao' data-ajax='true' data-valor-original='{$this->id_instalacao[$chave]}' value='{$this->id_instalacao[$chave]}'></input>
				<input type='hidden' data-atributo='turno' data-ajax='true' data-valor-original='{$this->turno->turno}' value='{$this->turno->turno}'></input>
				<input type='hidden' data-atributo='where_clause' value='id'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id[$chave]}'></input>
				<input type='hidden' data-atributo='funcao_validacao' value='valida_acao'></input>
				<div data-atributo='nome_planeta' data-valor-original='{$planeta->nome} ({$estrela->X};{$estrela->Y};{$estrela->Z})'>{$planeta->nome} ({$estrela->X};{$estrela->Y};{$estrela->Z})</div>
			</td>
			<td><div data-atributo='nome_instalacao' data-valor-original='{$instalacao->nome}'>{$instalacao->nome}</div></td>
			<td><div data-atributo='pop' data-valor-original='{$this->pop[$chave]}' data-ajax='true' style='display: flex; align-items: center; justify-content:center;'><input data-atributo='pop' data-ajax='true' type='range' min='0' max='10' value='{$this->pop[$chave]}' oninput='return altera_acao(event, this);'></input>&nbsp;&nbsp;&nbsp;<label data-atributo='pop'>{$this->pop[$chave]}</label></div></td>
			<td><div data-atributo='gerenciar' style='visibility: hidden;'><a href='#' onclick='return salva_acao(event, this);'>Salvar</a> | <a href='#' onclick='return salva_acao(event, this,true);'>Cancelar</a></div></td>
			</tr>";
		}
		
		return $html;
	
	
	}



}
?>