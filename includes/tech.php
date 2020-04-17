<?php
/**************************
TECH.PHP
----------------
Cria o objeto "tech" 
***************************/

//Classe "tech"
//Contém a lista de Techs
class tech 
{
	public $id;
	public $nome;
	public $descricao;
	public $custo;
	public $nivel;
	public $id_tech_parent;
	public $lista_requisitos;
	public $id_tech_requisito = [];
	public $belica;
	public $publica;
	public $icone;
	
	function __construct($id) {
		global $wpdb;
		
		$this->id = $id;

		$resultados = $wpdb->get_results("SELECT nome, descricao, custo, nivel, id_tech_parent, lista_requisitos, belica, publica, icone FROM colonization_tech WHERE id=".$this->id);
		$resultado = $resultados[0];
		
		$this->nome = $resultado->nome;
		$this->descricao = $resultado->descricao;
		$this->custo = $resultado->custo;
		$this->nivel = $resultado->nivel;
		$this->id_tech_parent = $resultado->id_tech_parent;
		$this->lista_requisitos = $resultado->lista_requisitos;
		$this->id_tech_requisito = explode(";",$resultado->lista_requisitos);
		$this->belica = $resultado->belica;
		$this->publica = $resultado->publica;
		$this->icone = $resultado->icone;
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;

		if ($this->belica == 1) {
			$belica_checked = "checked";
		} else {
			$belica_checked = "";
		}

		if ($this->publica == 1) {
			$publica_checked = "checked";
		} else {
			$publica_checked = "";
		}

		//Exibe os dados do Objeto
		$html = "				<td>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
				<input type='hidden' data-atributo='where_clause' value='id'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
				<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>
				<div data-atributo='id' data-valor-original='{$this->id}'>{$this->id}</div>
				<div><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a></div>
			</td>
			<td><div data-atributo='nome' data-editavel='true' data-valor-original='{$this->nome}'>{$this->nome}</div></td>
			<td><div data-atributo='descricao' data-editavel='true' data-valor-original='{$this->descricao}'>{$this->descricao}</div></td>
			<td><div data-atributo='nivel' data-editavel='true' data-valor-original='{$this->nivel}' data-style='width: 50px;'>{$this->nivel}</div></td>
			<td><div data-atributo='custo' data-editavel='true' data-valor-original='{$this->custo}' data-style='width: 50px;'>{$this->custo}</div></td>
			<td><div data-atributo='id_tech_parent' data-editavel='true' data-valor-original='{$this->id_tech_parent}' data-style='width: 30px;'>{$this->id_tech_parent}</div></td>
			<td><div data-atributo='lista_requisitos' data-editavel='true' data-valor-original='{$this->lista_requisitos}' data-style='width: 100px;'>{$this->lista_requisitos}</div></td>
			<td><div data-atributo='belica' data-type='checkbox' data-editavel='true' data-valor-original='{$this->belica}'><input type='checkbox' data-atributo='belica' data-ajax='true' {$belica_checked} disabled></input></div></td>			
			<td><div data-atributo='publica' data-type='checkbox' data-editavel='true' data-valor-original='{$this->publica}'><input type='checkbox' data-atributo='publica' data-ajax='true' {$publica_checked} disabled></input></div></td>			
			<td><div data-atributo='icone' data-editavel='true' data-valor-original='{$this->icone}'>{$this->icone}</div></td>
			";			

		return $html;
	}
}

?>