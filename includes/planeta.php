<?php
/**************************
PLANETA.PHP
----------------
Cria o objeto "planeta" e mostra os dados do Império
***************************/

//Classe "planeta"
//Contém os dados do planeta
class planeta 
{
	public $id;
	public $id_estrela;
	public $estrela;
	public $nome;
	public $posicao;
	public $classe;
	public $subclasse;
	public $tamanho;
	
	function __construct($id) {
		global $wpdb;
		
		$this->id = $id;

		$resultados = $wpdb->get_results("SELECT id_estrela, nome, posicao, classe, subclasse, tamanho FROM colonization_recursos WHERE id=".$this->id);
		$resultado = $resultados[0];				
		
		$this->id_estrela = $resultad['id_estrela'];
		$this->nome = $resultad['nome'];
		$this->posicao = $resultad['posicao'];
		$this->classe = $resultad['classe'];
		$this->subclasse = $resultad['subclasse'];
		$this->tamanho = $resultad['tamanho'];
		
		$estrela = new estrela($this->id_estrela);
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;
		
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
			<td><div data-atributo='acumulavel'  data-editavel='true' data-valor-original='{$this->acumulavel}'>{$this->acumulavel}</div></td>";
		return $html;
	}
}

?>