<?php
/**************************
RECURSO.PHP
----------------
Cria o objeto "recurso" 
***************************/

//Classe "recurso"
//Contém a lista de recursos e seus atributos de acordo com o Império e planeta
class recurso 
{
	public $id;
	public $nome;
	public $descricao;
	public $acumulavel;
	
	function __construct($id) {
		global $wpdb;
		
		$this->id = $id;

		$resultados = $wpdb->get_results("SELECT nome, descricao, acumulavel FROM colonization_recurso WHERE id=".$this->id);
		$resultado = $resultados[0];
		
		$this->nome = $resultado->nome;
		$this->descricao = $resultado->descricao;
		$this->acumulavel = $resultado->acumulavel;
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;
		
		if ($this->acumulavel == 1) {
			$acumulavel_checked = "checked";
		} else {
			$acumulavel_checked = "";
		}
		
		//Exibe os dados do Império
		$html = "				<td>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
				<input type='hidden' data-atributo='where_clause' value='id'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
				<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>
				<div data-atributo='nome' data-valor-original='{$this->nome}' data-editavel='true'>{$this->nome}</div>
				<div><a href='#' onclick='edita_objeto(this);'>Editar</a> | <a href='#' onclick='excluir_objeto(this);'>Excluir</a></div>
			</td>
			<td><div data-atributo='descricao' data-editavel='true' data-valor-original='{$this->descricao}'>{$this->descricao}</div></td>
			<td><div data-atributo='acumulavel' data-type='checkbox' data-editavel='true' data-valor-original='{$this->acumulavel}'><input type='checkbox' data-atributo='acumulavel' data-ajax='true' {$acumulavel_checked} disabled></input></div></td>";
		return $html;
	}
}

?>