<?php
/**************************
INSTALACAO.PHP
----------------
Cria o objeto "instalação" e mostra os dados da instalação
***************************/

//Classe "instalacao"
//Contém os dados da instalacao
class instalacao 
{
	public $id;
	public $nome;
	public $descricao;
	public $produz_recursos;
	public $consome_recursos;
	
	function __construct($id) {
		global $wpdb;
		
		$this->id = $id;

		$resultados = $wpdb->get_results("SELECT nome, descricao FROM colonization_instalacao WHERE id=".$this->id);
		$resultado = $resultados[0];
		
		$this->nome = $resultado->nome;
		$this->descricao = $resultado->descricao;
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;
		
		//Exibe os dados do objeto
		$html = "				<td>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
				<input type='hidden' data-atributo='where_clause' value='id'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
				<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>
				<div data-atributo='nome' data-valor-original='{$this->nome}' data-editavel='true'>{$this->nome}</div>
				<div><a href='#' onclick='edita_objeto(this);'>Editar</a> | <a href='#' onclick='excluir_objeto(this);'>Excluir</a></div>
			</td>
			<td><div data-atributo='descricao' data-editavel='true' data-valor-original='{$this->descricao}'>{$this->descricao}</div></td>
			<td><div data-atributo='gerenciar'><a href='#' onclick='gerenciar_objeto(this);'>Gerenciar Objeto</a></div></td>";
		return $html;
	}
}

?>