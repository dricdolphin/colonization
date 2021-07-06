<?php
/**************************
TECHS_PERMITIDAS_IMPERIO.PHP
----------------
Cria o objeto "techs_permitidas_imperio"
***************************/

//Classe "techs_permitidas_imperio"
//Contém os dados da Instalações não-públicas que o Império pode ter
class techs_permitidas_imperio 
{
	
	public $id;
	public $id_imperio;
	public $id_tech;
	
	function __construct($id) {
		global $wpdb;
		
		$this->id = $id;

		$resultado = $wpdb->get_results("
		SELECT citp.id AS id, citp.id_tech, citp.id_imperio
		FROM colonization_imperio_techs_permitidas AS citp
		WHERE citp.id = {$this->id}
		");

		$this->id_imperio = $resultado[0]->id_imperio;
		$this->id_tech = $resultado[0]->id_tech;
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;
		
		$html = "";

		$tech = new tech($this->id_tech);
		
		$html .= "<tr><td style='width: 300px;'>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
				<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='{$this->id_imperio}' value='{$this->id_imperio}'></input>
				<input type='hidden' data-atributo='id_tech' data-ajax='true' data-valor-original='{$this->id_tech}' value='{$this->id_tech}'></input>
				<input type='hidden' data-atributo='where_clause' value='id'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
				<div data-atributo='nome_instalacao' data-editavel='true' data-type='select' data-funcao='lista_techs_ocultas_html' data-argumentos='{\"id_imperio\":\"{$this->id_imperio}\"}' data-id-selecionado='{$this->id_tech}' data-valor-original='{$tech->nome}'>{$tech->nome}</div>
				<div data-atributo='gerenciar'><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a></div>
			</td>
			</tr>";

		return $html;
	}
}
?>