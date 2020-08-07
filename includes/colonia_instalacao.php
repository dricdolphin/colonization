<?php
//colonization_planeta_instalacoes
/**************************
COLONIA_INSTALACAO.PHP
----------------
Cria o objeto "colônia_instalação"
***************************/

//Classe "colonia_instalacao"
//Contém as instalações de uma colônia
class colonia_instalacao
{
	public $id;
	public $id_planeta;
	public $id_instalacao;
	public $nivel;
	public $turno;
	public $turno_destroi;
	public $instalacao;
	public $planeta;
	public $imperio;
	
	function __construct($id) {
		global $wpdb;
		
		$this->id = $id;

		$resultados = $wpdb->get_results("SELECT id_planeta, id_instalacao, nivel, turno, turno_destroi FROM colonization_planeta_instalacoes WHERE id=".$this->id);
		$resultado = $resultados[0];				
		
		$this->id_planeta = $resultado->id_planeta;
		$this->id_instalacao = $resultado->id_instalacao;
		$this->nivel = $resultado->nivel;
		$this->turno = $resultado->turno;
		$this->turno_destroi = $resultado->turno_destroi;
		
		//$this->planeta = new planeta($this->id_planeta);
		//$this->instalacao = new instalacao($this->id_instalacao);
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;
		
		$this->instalacao = new instalacao($this->id_instalacao);
		
		if ($this->turno_destroi === null) {
			$texto_destruir	= "Destruir Instalação";
			$this->turno_destroi = "&nbsp;";
		} else {
			$texto_destruir	= "Reparar Instalação";
		}
		
		//Exibe os dados da colônia
		$html = "		<td>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
				<input type='hidden' data-atributo='id_instalacao' data-ajax='true' data-valor-original='{$this->id_instalacao}' value='{$this->id_instalacao}'></input>
				<input type='hidden' data-atributo='id_planeta' data-ajax='true' data-valor-original='{$this->id_planeta}' value='{$this->id_planeta}'></input>
				<input type='hidden' data-atributo='where_clause' value='id'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
				<input type='hidden' data-atributo='funcao_validacao' value='valida_colonia_instalacao'></input>
				<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir esta instalação?'></input>
				<div data-atributo='id' data-ajax='true'>{$this->id}</div>
				<div data-atributo='gerenciar'><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a></div>
			</td>
			<td><div data-atributo='nome_instalacao' data-editavel='true' data-type='select' data-funcao='lista_instalacoes_html' data-id-selecionado='{$this->id_instalacao}' data-valor-original='{$this->instalacao->nome}'>{$this->instalacao->nome}</div></td>
			<td><div data-atributo='nivel' data-editavel='true' data-valor-original='{$this->nivel}' data-style='width: 30px;'>{$this->nivel}</div></td>
			<td><div data-atributo='turno' data-editavel='true' data-valor-original='{$this->turno}' data-style='width: 30px;'>{$this->turno}</div></td>
			<td><div data-atributo='turno_destroi' data-valor-original='{$this->turno_destroi} data-style='width: 30px;'>{$this->turno_destroi}</div></td>
			<td><div data-atributo='gerenciar'><a href='#' onclick='return destruir_instalacao(event, this);'>{$texto_destruir}</a></div></td>";
		
		return $html;
	}
}
?>