<?php
/**************************
CONFIGURACAO.PHP
----------------
Cria o objeto "configuração" e mostra os dados
***************************/

//Classe "configuracao"
//Contém os dados de configuracao
class configuracao 
{
	public $id;
	public $descricao;
	public $id_post;
	public $page_id;
	public $deletavel;
	
	function __construct($id) {
		global $wpdb;

		$this->id = $id;

		$resultados = $wpdb->get_results("SELECT descricao, id_post, page_id, deletavel FROM colonization_referencia_forum WHERE id=".$this->id);
		$resultado = $resultados[0];
		
		$this->descricao = $resultado->descricao;
		$this->id_post = $resultado->id_post;
		$this->page_id = $resultado->page_id;
		$this->deletavel = $resultado->deletavel;
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;
		
		$page_id_checked = "";
		if ($this->page_id == 1) {
			$page_id_checked = "checked";
		}

		$deletavel = " | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a>";
		$editavel = "data-editavel='true'";
		if ($this->deletavel == 0) {
			$deletavel = "";
			$editavel = "";
			
		}
		
		//Exibe os dados do objeto	
		$html = "			<td>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
				<input type='hidden' data-atributo='where_clause' value='id'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
				<input type='hidden' data-atributo='deletavel' value='{$this->deletavel}'></input>
				<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>
				<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir esta configuração?'></input>
				<div data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'>{$this->id}</div>
				<div data-atributo='gerenciar'><a href='#' onclick='return edita_objeto(event, this);'>Editar</a>{$deletavel}</div>
			</td>
			<td><div data-atributo='descricao' {$editavel} data-valor-original='{$this->descricao}'>{$this->descricao}</div></td>
			<td><div data-atributo='id_post' data-style='width: 30px;' data-editavel='true' data-valor-original='{$this->id_post}'>{$this->id_post}</div></td>
			<td><div data-atributo='page_id' data-type='checkbox' {$editavel} data-valor-original='{$this->page_id}'><input type='checkbox' data-atributo='page_id' data-ajax='true' {$page_id_checked} disabled></input></div></td>
			";

		return $html;
	}
}

?>