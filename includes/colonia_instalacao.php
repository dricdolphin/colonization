<?php
//colonization_planeta_instalacoes
/**************************
COLONIA.PHP
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
	public $turno;
	public $turno_destroi;
	public $instalacao;
	public $planeta;
	public $imperio;
	
	function __construct($id) {
		global $wpdb;
		
		$this->id = $id;

		$resultados = $wpdb->get_results("SELECT id_planeta, id_instalacao, turno, turno_destroi FROM colonization_planeta_instalacoes WHERE id=".$this->id);
		$resultado = $resultados[0];				
		
		$this->id_planeta = $resultado->id_planeta;
		$this->id_instalacao = $resultado->id_instalacao;
		$this->turno = $resultado->turno;
		$this->turno_destroi = $resultado->turno_destroi;
		
		$this->planeta = new planeta($this->id_planeta);
		$this->instalacao = new instalacao($this->planeta->id_instalacao);
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;

		//<tr><td>ID</td><td>Nome</td><td>Nível</td><td>Turno Const.</td><td>Destruir Instalação</td>


		//Exibe os dados do Império		
		$html = "		<td>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
				<input type='hidden' data-atributo='id_instalacao' data-ajax='true' data-valor-original='{$this->id_instalacao}' value='{$this->id_planeta}'></input>
				<input type='hidden' data-atributo='id_planeta' data-ajax='true' data-valor-original='{$this->id_planeta}' value='{$this->id_planeta}'></input>
				<input type='hidden' data-atributo='where_clause' value='id'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
				<input type='hidden' data-atributo='funcao_validacao' value='valida_colonia_instalacao'></input>
				<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir esta instalação?'></input>
				<div data-atributo='id' data-ajax='true'>{$this->id}</div>
				<div><a href='#' onclick='edita_objeto(this);'>Editar</a> | <a href='#' onclick='excluir_objeto(this);'>Excluir</a></div>
			</td>
			<td><div data-atributo='nome_instalacao' data-editavel='true' data-type='select' data-funcao='lista_instalacoes_html' data-id-selecionado='{$this->id_instalacao}' data-valor-original='{$this->instalacao->nome}'>{$this->instalacao->nome}</div></td>
			<td><div data-atributo='nivel' data-editavel='true' data-valor-original='{$this->nivel} data-style='width: 30px;'>{$this->nivel}</div></td>
			<td><div data-atributo='turno' data-editavel='true' data-valor-original='{$this->turno}' data-style='width: 30px;'>{$this->turno}</div></td>
			<td><div data-atributo='turno_destroi' data-valor-original='{$this->turno_destroi} data-style='width: 30px;'>{$this->turno_destroi}</div></td>
			<td><div data-atributo='gerenciar'><a href='#' onclick='destruir_instalacao(this);'>Destruir Instalação</a></div></td>";
		
		return $html;
	}
}
?>