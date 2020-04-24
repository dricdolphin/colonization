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
	public $extrativo;
	public $local;
	
	function __construct($id) {
		global $wpdb;
		
		$this->id = $id;

		$resultados = $wpdb->get_results("SELECT nome, descricao, nivel, acumulavel, extrativo, local FROM colonization_recurso WHERE id=".$this->id);
		$resultado = $resultados[0];
		
		$this->nome = $resultado->nome;
		$this->descricao = $resultado->descricao;
		$this->nivel = $resultado->nivel;
		$this->acumulavel = $resultado->acumulavel;
		$this->extrativo = $resultado->extrativo;
		$this->local = $resultado->local;
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

		if ($this->extrativo == 1) {
			$extrativo_checked = "checked";
		} else {
			$extrativo_checked = "";
		}

		if ($this->local == 1) {
			$local_checked = "checked";
		} else {
			$local_checked = "";
		}
		
		//Exibe os dados do Império
		$html = "				<td>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
				<input type='hidden' data-atributo='where_clause' value='id'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
				<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>
				<div data-atributo='id' data-valor-original='{$this->id}'>{$this->id}</div>
				<div><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a></div>
			</td>
			<td><div data-atributo='nome' data-valor-original='{$this->nome}' data-editavel='true'>{$this->nome}</div></td>
			<td><div data-atributo='descricao' data-editavel='true' data-valor-original='{$this->descricao}'>{$this->descricao}</div></td>
			<td><div data-atributo='nivel' data-editavel='true' data-valor-original='{$this->nivel}'>{$this->nivel}</div></td>			
			<td><div data-atributo='acumulavel' data-type='checkbox' data-editavel='true' data-valor-original='{$this->acumulavel}'><input type='checkbox' data-atributo='acumulavel' data-ajax='true' {$acumulavel_checked} disabled></input></div></td>
			<td><div data-atributo='extrativo' data-type='checkbox' data-editavel='true' data-valor-original='{$this->extrativo}'><input type='checkbox' data-atributo='extrativo' data-ajax='true' {$extrativo_checked} disabled></input></div></td>
			<td><div data-atributo='local' data-type='checkbox' data-editavel='true' data-valor-original='{$this->local}'><input type='checkbox' data-atributo='local' data-ajax='true' {$local_checked} disabled></input></div></td>";			

		return $html;
	}
}

?>