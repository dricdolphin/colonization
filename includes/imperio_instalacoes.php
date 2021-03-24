<?php
/**************************
IMPERIO_INSTALACOES.PHP
----------------
Cria o objeto "imperio_instalacoes"
***************************/

//Classe "imperio_instalacoes"
//Contém os dados da Instalações não-públicas que o Império pode ter
class imperio_instalacoes 
{
	
	public $id;
	public $id_imperio;
	public $id_instalacao;
	
	function __construct($id) {
		global $wpdb;
		
		$this->id = $id;

		$resultado = $wpdb->get_results("
		SELECT cii.id AS id, cii.id_instalacao, cii.id_imperio
		FROM colonization_imperio_instalacoes AS cii
		WHERE cii.id = {$this->id}
		");

		$this->id_imperio = $resultado[0]->id_imperio;
		$this->id_instalacao = $resultado[0]->id_instalacao;
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;
		
		$html = "";

		$instalacao = new instalacao($this->id_instalacao);
		
		$html .= "<tr><td style='width: 300px;'>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
				<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='{$this->id_imperio}' value='{$this->id_imperio}'></input>
				<input type='hidden' data-atributo='id_instalacao' data-ajax='true' data-valor-original='{$this->id_instalacao}' value='{$this->id_instalacao}'></input>
				<input type='hidden' data-atributo='where_clause' value='id'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
				<div data-atributo='nome_instalacao' data-editavel='true' data-type='select' data-funcao='lista_instalacoes_ocultas_html' data-id-selecionado='{$this->id_instalacao}' data-valor-original='{$instalacao->nome}'>{$instalacao->nome}</div>
				<div data-atributo='gerenciar'><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a></div>
			</td>
			</tr>";

		return $html;
	}
}
?>