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
	public $icone;
	
	function __construct($id) {
		global $wpdb;
		$wpdb->hide_errors();
		$this->id = $id;

		$resultados = $wpdb->get_results("SELECT nome, descricao, nivel, acumulavel, extrativo, local, icone FROM colonization_recurso WHERE id=".$this->id);
		if (empty($resultados)) {
			$this->id = 0;
			return;
		}
		
		$resultado = $resultados[0];

		$this->nome = $resultado->nome;
		$this->descricao = $resultado->descricao;
		$this->nivel = $resultado->nivel;
		$this->acumulavel = $resultado->acumulavel;
		$this->extrativo = $resultado->extrativo;
		$this->local = $resultado->local;
		$this->icone = $resultado->icone;
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
				<div data-atributo='gerenciar'><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a></div>
			</td>
			<td><div data-atributo='nome' data-valor-original='{$this->nome}' data-editavel='true'>{$this->nome}</div></td>
			<td><div data-atributo='descricao' data-editavel='true' data-valor-original='{$this->descricao}'>{$this->descricao}</div></td>
			<td><div data-atributo='icone' data-editavel='true' data-branco='true' data-valor-original='{$this->icone}'>{$this->icone}</div></td>
			<td><div data-atributo='nivel' data-editavel='true' data-valor-original='{$this->nivel}'>{$this->nivel}</div></td>
			<td><div data-atributo='acumulavel' data-type='checkbox' data-editavel='true' data-valor-original='{$this->acumulavel}'><input type='checkbox' data-atributo='acumulavel' data-ajax='true' {$acumulavel_checked} disabled></input></div></td>
			<td><div data-atributo='extrativo' data-type='checkbox' data-editavel='true' data-valor-original='{$this->extrativo}'><input type='checkbox' data-atributo='extrativo' data-ajax='true' {$extrativo_checked} disabled></input></div></td>
			<td><div data-atributo='local' data-type='checkbox' data-editavel='true' data-valor-original='{$this->local}'><input type='checkbox' data-atributo='local' data-ajax='true' {$local_checked} disabled></input></div></td>";

		return $html;
	}
	
	/***********************
	function html_icone()
	----------------------
	Exibe o nome do ícone do Recurso
	
	$exibe_descricao -- define se é para exibir também a descrição do recurso
	***********************/	
	function html_icone($exibe_descricao = false) {
		$nome_recurso = $this->nome;
		$nome_tooltip = "";
		
		if ($this->icone != "") {
				$nome_recurso = "<div class='{$this->icone}'></div>";
				$nome_tooltip = $this->nome;
		}
		
		$descricao = "";
		if ($exibe_descricao) {
			$descricao = ": {$this->descricao}";
		}
		
		$html = "<div class='tooltip' style='display: inline-block;'>{$nome_recurso}{$descricao}<span class='tooltiptext'>{$nome_tooltip}</span></div>";		
		
		return $html;
	}
	

}

?>