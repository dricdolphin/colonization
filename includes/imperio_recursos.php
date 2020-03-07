<?php
/**************************
IMPERIO_RECURSOS.PHP
----------------
Cria o objeto "imperio_recursos"
***************************/

//Classe "imperio_recursos"
//Contém os dados dos recursos do Império
class imperio_recursos 
{
	public $id_imperio;
	public $id = [];
	public $id_recurso = [];
	public $qtd = [];
	public $turno;

	
	function __construct($id_imperio) {
		global $wpdb;
		
		$this->id_imperio = $id_imperio;
		$this->turno = new turno();
		
		$resultado = $wpdb->get_results("
		SELECT cir.id AS id, cr.id AS id_recurso, cir.qtd AS qtd
		FROM 
		(SELECT * FROM colonization_recurso) AS cr
		LEFT JOIN 
		(SELECT id, id_recurso, qtd FROM colonization_imperio_recursos 
		WHERE id_imperio = {$this->id_imperio}
		AND turno = {$this->turno->turno}) AS cir
		ON cr.id = cir.id_recurso");
		
		$chave = 0;
		foreach ($resultado as $valor) {
			if ($valor->id === null) {
				$this->id[$chave] = 0;
				$this->id_recurso[$chave] = $valor->id_recurso;
				$this->qtd[$chave] = 0;
			} else {
				$this->id[$chave] = $valor->id;
				$this->id_recurso[$chave] = $valor->id_recurso;
				$this->qtd[$chave] = $valor->qtd;
			}
			$chave++;
		}
		
		$chave = 0;
		if (isset($this->id[$chave])) {
			if ($this->id[$chave] == 0) {//As chaves estão em branco, vamos criá-las!
				foreach ($this->id as $chave => $valor) {
					$wpdb->query("INSERT INTO colonization_imperio_recursos SET id_recurso={$this->id_recurso[$chave]}, qtd=0, turno={$this->turno->turno}, id_imperio={$this->id_imperio}");
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
			$recurso = new recurso($this->id_recurso[$chave]);
			$html .= "<tr><td>
					<input type='hidden' data-atributo='id' data-valor-original='{$this->id[$chave]}' value='{$this->id[$chave]}'></input>
					<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='{$this->id_imperio}' value='{$this->id_imperio}'></input>
					<input type='hidden' data-atributo='id_recurso' data-ajax='true' data-valor-original='{$this->id_recurso[$chave]}' value='{$this->id_recurso[$chave]}'></input>
					<input type='hidden' data-atributo='where_clause' value='id'></input>
					<input type='hidden' data-atributo='where_value' value='{$this->id[$chave]}'></input>
					<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>
					<input type='hidden' data-atributo='funcao_pos_processamento' value='remove_excluir'></input>
					<div data-atributo='id' data-ajax='true'>{$this->id[$chave]}</div>
					<div><a href='#' onclick='edita_objeto(this);'>Editar</a></div>
				</td>
				<td><div data-atributo='nome_recurso'>{$recurso->nome}</div></td>
				<td><div data-atributo='qtd' data-editavel='true' data-valor-original='{$this->qtd[$chave]}' data-style='width: 80px;'>{$this->qtd[$chave]}</div></td>
				</tr>";
		}

		return $html;
	}
}
?>