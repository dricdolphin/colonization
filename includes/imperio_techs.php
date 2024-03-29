<?php
/**************************
IMPERIO_TECHS.PHP
----------------
Cria o objeto "imperio_techs"
***************************/

//Classe "imperio_techs"
//Contém os dados das Techs do Império
class imperio_techs 
{
	
	public $id;
	public $id_imperio;
	public $id_tech;
	public $custo_pago;
	public $turno;
	public $tech_inicial;
	
	function __construct($id) {
		global $wpdb;
		
		$resultado = $wpdb->get_results("
		SELECT cit.id AS id, cit.id_tech, cit.custo_pago, cit.turno, cit.id_imperio, cit.tech_inicial
		FROM colonization_imperio_techs AS cit
		WHERE cit.id = {$id}
		");

		if (!empty($resultado)) {
			$this->id = $id;
			$this->id_imperio = $resultado[0]->id_imperio;
			$this->id_tech = $resultado[0]->id_tech;
			$this->custo_pago = $resultado[0]->custo_pago;
			$this->turno = $resultado[0]->turno;
			$this->tech_inicial = $resultado[0]->tech_inicial;
		} else {
			$this->id = 0;
		}
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;

		if ($this->tech_inicial == 1) {
			$tech_inicial_checked = "checked";
		} else {
			$tech_inicial_checked = "";
		}
		
		$html = "";

		$tech = new tech($this->id_tech);
		
		$html .= "<tr><td style='width: 300px;'>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
				<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='{$this->id_imperio}' value='{$this->id_imperio}'></input>
				<input type='hidden' data-atributo='id_tech' data-ajax='true' data-valor-original='{$this->id_tech}' value='{$this->id_tech}'></input>
				<input type='hidden' data-atributo='where_clause' value='id'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
				<input type='hidden' data-atributo='funcao_validacao'value='valida_tech_imperio'></input>
				<div data-atributo='nome_tech' data-editavel='true' data-type='select' data-funcao='lista_techs_html' data-id-selecionado='{$this->id_tech}' data-valor-original='{$tech->nome}'>{$tech->nome}</div>
				<div data-atributo='gerenciar'><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a></div>
			</td>
			<td><div data-atributo='custo_pago' data-editavel='true' data-valor-original='{$this->custo_pago}' data-style='width: 30px;'>{$this->custo_pago}</div></td>
			<td><div data-atributo='turno' data-editavel='true' data-valor-original='{$this->turno}' data-style='width: 30px;'>{$this->turno}</div></td>
			<td><div data-atributo='tech_inicial' data-type='checkbox' data-editavel='true' data-valor-original='{$this->tech_inicial}'><input type='checkbox' data-atributo='tech_inicial' data-ajax='true' {$tech_inicial_checked} disabled></input></div></td>
			</tr>";

		return $html;
	}
}
?>