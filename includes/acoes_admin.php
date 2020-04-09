<?php
/**************************
ACOES_ADMIN.PHP
----------------
Cria o objeto "acoes_admin", que contém uma lista
com todos os IDs e dados das Ações do Admin
***************************/

class acao_admin
{
	public $id;
	public $id_imperio;
	public $turno;
	public $lista_recursos;
	public $qtd;
	public $descricao;
	
	function __construct($id) {
		global $wpdb;
		
		$this->id = $id;
		
		$resultados = $wpdb->get_results("SELECT id, id_imperio, turno, lista_recursos, qtd, descricao FROM colonization_acoes_admin WHERE id={$this->id}");
		
		foreach ($resultados as $resultado) {
			$this->id_imperio = $resultado->id_imperio;
			$this->turno = $resultado->turno;
			$this->lista_recursos = $resultado->lista_recursos;
			$this->qtd = $resultado->qtd;
			$this->descricao = $resultado->descricao;
		}
	}
	
	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;
		$imperio = new imperio($this->id_imperio);
		
		//Explode os valores e transforma em um jeito mais legível
		$recursos = explode(";",$this->lista_recursos);
		$qtds = explode(";",$this->qtd);
		
		$lista_recursos_qtd = "";
		foreach ($recursos AS $chave => $valor) {
			$recurso = new recurso($valor);
			$lista_recursos_qtd .= "{$recurso->nome}: {$qtds[$chave]}; ";
		}
		
		if ($lista_recursos_qtd != "") {
			$lista_recursos_qtd = substr($lista_recursos_qtd,0,-2);
		}
		
		$html = "<td>
			<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
			<input type='hidden' data-atributo='id_imperio' data-valor-original='{$this->id_imperio}' value='{$this->id_imperio}'></input>
			<input type='hidden' data-atributo='where_clause' value='id'></input>
			<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
			<input type='hidden' data-atributo='funcao_validacao' value='valida_acao_admin'></input>
			<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Remover esta Ação não irá reverter seus efeitos. Deseja continuar?'></input>
			<div data-atributo='nome_imperio' data-editavel='true' data-type='select' data-funcao='lista_imperios_html' data-id-selecionado='{$this->id_imperio}' data-valor-original='{$imperio->nome}'>{$imperio->nome}</div>
			<div><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a></div>
			</td>
		<td><div id='lista_recursos_{$this->id}' data-atributo='lista_recursos_qtd' data-valor-original='{$lista_recursos_qtd}'>{$lista_recursos_qtd}</div>
		<div><div data-atributo='lista_recursos' data-editavel='true' data-style='width: 120px;' data-valor-original='{$this->lista_recursos}' style='display: inline-block;'>{$this->lista_recursos}</div>
		&nbsp; || &nbsp; <div data-atributo='qtd' data-editavel='true' data-style='width: 120px;' data-valor-original='{$this->qtd}' style='display: inline-block;'>{$this->qtd}</div></td></div>
		<td><div data-atributo='descricao' data-editavel='true' data-valor-original='{$this->descricao}'>{$this->descricao}</div></td>
		<td><div data-atributo='turno' data-editavel='true' data-style='width: 30px;' data-valor-original='{$this->turno}'>{$this->turno}</div></td>
			";
	
		return $html;
	}
}

?>