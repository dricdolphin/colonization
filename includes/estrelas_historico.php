<?php
/**************************
ESTRELAS_HISTORICO.PHP
----------------
Cria o objeto "estrelas_historico" 
***************************/

//Classe "estrelas_historico"
//Contém a lista de estrelas que já foram visitadas pelos Impérios
class estrelas_historico 
{
	public $id;
	public $id_imperio;
	public $id_estrela;
	
	function __construct($id) {
		global $wpdb;
		
		$this->id = $id;

		$resultados = $wpdb->get_results("SELECT id, id_imperio, id_estrela FROM colonization_estrelas_historico WHERE id=".$this->id);
		$resultado = $resultados[0];
		
		$this->id_imperio = $resultado->id_imperio;
		$this->id_estrela = $resultado->id_estrela;
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;
		$imperio = new imperio($this->id_imperio);
		$estrela = new estrela($this->id_estrela);
		
		
		//Exibe os dados do objeto
		$html = "				<td>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
				<input type='hidden' data-atributo='where_clause' value='id'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
				<input type='hidden' data-atributo='funcao_validacao' value='valida_reabastecimento'></input>
				<div data-atributo='id' data-valor-original='{$this->id}'>{$this->id}</div>
				<div data-atributo='gerenciar'><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a></div>
			</td>
			<td><div data-atributo='nome_imperio' data-editavel='true' data-type='select' data-funcao='lista_imperios_html' data-id-selecionado='{$this->id_imperio}' data-valor-original='{$imperio->nome}'>{$imperio->nome}</div></td>
			<td><div data-atributo='nome_estrela' data-editavel='true' data-type='select' data-funcao='lista_estrelas_html' data-id-selecionado='{$this->id_estrela}' data-valor-original='{$estrela->nome} ({$estrela->X};{$estrela->Y};{$estrela->Z})'>{$estrela->nome} ({$estrela->X};{$estrela->Y};{$estrela->Z})</div></td>";

		return $html;
	}
}

?>