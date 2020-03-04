<?php
/**************************
COLONIA_RECURSO.PHP
----------------
Cria o objeto "colonia_recurso" 
***************************/

//Classe "colonia_recurso"
//Contém a lista de recursos da colônia
class colonia_recurso
{
	public $id;
	public $id_planeta;
	public $id_recurso;
	public $disponivel;
	public $recurso;
	
	function __construct($id) {
		global $wpdb;
		
		$this->id = $id;

		$resultados = $wpdb->get_results("SELECT id_planeta, id_recurso, disponivel FROM colonization_planeta_recursos WHERE id=".$this->id);
		$resultado = $resultados[0];
		
		$this->id_planeta = $resultado->id_planeta;
		$this->id_recurso = $resultado->id_recurso;
		$this->disponivel = $resultado->disponivel;
		$this->recurso = new recurso($this->id_recurso);
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;
		
		//Exibe os dados
		$html = "				
		<td>
			<input type='hidden' data-atributo='id' value='{$this->id}'></input>
			<input type='hidden' data-atributo='id_planeta' data-ajax='true' value='{$this->id_planeta}'></input>
			<input type='hidden' data-atributo='id_recurso' data-ajax='true' value='{$this->id_recurso}'></input>
			<input type='hidden' data-atributo='where_clause' value='id'></input>
			<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
			<input type='hidden' data-atributo='funcao_validacao' value='valida_colonia_recurso'></input>
			<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir este recurso?'></input>
			<div data-atributo='id' data-valor-original='{$this->id}' >{$this->id}</div>
			<div><a href='#' onclick='edita_objeto(this);'>Editar</a> | <a href='#' onclick='excluir_objeto(this);'>Excluir</a></div>
		</td>
		<td><div data-atributo='nome_recurso' data-editavel='true' data-type='select' data-funcao='lista_recursos_html' data-id-selecionado='{$this->id_recurso}' data-valor-original='{$this->recurso->nome}'>{$this->recurso->nome}</div></td>
		<td><div data-atributo='disponivel' data-editavel='true' data-valor-original='{$this->disponivel}'>{$this->disponivel}</div></td>";
		return $html;
	}
}

?>