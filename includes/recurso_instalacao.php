<?php
/**************************
RECURSO_INSTALACAO.PHP
----------------
Cria o objeto "recurso_instalacao" 
***************************/

//Classe "recurso"
//Contém a lista de recursos e seus atributos de acordo com o Império e planeta
class recurso_instalacao
{
	public $id;
	public $id_instalacao;
	public $id_recurso;
	public $recurso;
	public $qtd_por_nivel;
	public $consome;
	
	function __construct($id) {
		global $wpdb;
		
		$this->id = $id;

		$resultados = $wpdb->get_results("SELECT id_instalacao, id_recurso, qtd_por_nivel, consome FROM colonization_instalacao_recursos WHERE id=".$this->id);
		$resultado = $resultados[0];
		
		$this->id_instalacao = $resultado->id_instalacao;
		$this->id_recurso = $resultado->id_recurso;
		$this->qtd_por_nivel = $resultado->qtd_por_nivel;
		$this->consome = $resultado->consome;
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
			<input type='hidden' data-atributo='id_instalacao' value='{$this->id_instalacao}'></input>
			<input type='hidden' data-atributo='id_recurso' data-ajax='true' value='{$this->id_recurso}'></input>
			<input type='hidden' data-atributo='consome' data-ajax='true' value='{$this->consome}'></input>
			<input type='hidden' data-atributo='where_clause' value='id'></input>
			<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
			<input type='hidden' data-atributo='funcao_validacao' value='valida_instalacao_recurso'></input>
			<div data-atributo='id' data-valor-original='{$this->id}' >{$this->id}</div>
			<div><a href='#' onclick='edita_objeto(this);'>Editar</a> | <a href='#' onclick='excluir_objeto(this);'>Excluir</a></div>
		</td>
		<td><div data-atributo='nome_recurso' data-editavel='true' data-type='select' data-funcao='lista_recursos_html' data-id-selecionado='{$this->id_recurso}' data-valor-original='{$this->recurso->nome}'>{$this->recurso->nome}</div></td>
		<td><div data-atributo='qtd_por_nivel' data-editavel='true' data-valor-original='{$this->qtd_por_nivel}'>{$this->qtd_por_nivel}</div></td>";
		return $html;
	}
}

?>