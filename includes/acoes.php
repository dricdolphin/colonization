<?php
/**************************
ACOES.PHP
----------------
Cria o objeto "ações", que contém todas 
as ações de um determinado turno de um jogador.
***************************/

//Classe "acoes"
//Contém os dados da instalacao
class acoes 
{
	public $id;
	public $id_imperio;
	public $id_planeta;
	public $id_instalacao;
	public $pop;
	public $turno;
	public $data_modifica;
	public $planeta;
	public $instalacao;
	public $estrela;
	
	function __construct($id) {
		global $wpdb;
		
		$this->id = $id;

		$resultados = $wpdb->get_results("SELECT id_imperio, id_planeta, id_instalacao, pop, turno, data_modifica FROM colonization_acoes_turno WHERE id=".$this->id);
		$resultado = $resultados[0];
		
		$this->id_imperio = $resultado->id_imperio;
		$this->id_planeta = $resultado->id_planeta;
		$this->id_instalacao = $resultado->id_instalacao;
		$this->pop = $resultado->pop;
		$this->turno = $resultado->turno;
		$this->data_modifica = $resultado->data_modifica;
		
		$this->planeta = new planeta($this->id_planeta);
		$this->instalacao = new instalacao($this->id_instalacao);
		$this->estrela = new estrela($this->planeta->id_estrela);
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;
		
	
	$html = "		<td>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
				<input type='hidden' data-atributo='id_planeta' data-ajax='true' data-valor-original='{$this->id_imperio}' value='{$this->id_imperio}'></input>
				<input type='hidden' data-atributo='id_planeta' data-ajax='true' data-valor-original='{$this->id_planeta}' value='{$this->id_planeta}'></input>
				<input type='hidden' data-atributo='id_planeta' data-ajax='true' data-valor-original='{$this->id_instalacao}' value='{$this->id_instalacao}'></input>
				<input type='hidden' data-atributo='where_clause' value='id'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
				<input type='hidden' data-atributo='funcao_validacao' value='valida_acao'></input>
				<div data-atributo='nome_planeta' data-valor-original='{$this->planeta->nome} ({$this->estrela->X};{$this->estrela->Y};{$this->estrela->Z})'>{$this->planeta->nome} ({$this->estrela->X};{$this->estrela->Y};{$this->estrela->Z})</div>
			</td>
			<td><div data-atributo='nome_instalacao' data-valor-original='{$this->instalacao->nome}'>{$this->instalacao->nome}</div></td>
			<td><div data-atributo='pop' data-valor-original='{$this->pop}' data-ajax='true'><input type='range' min='0' max='10' value='{$this->pop}' oninput='altera_acao(this);'></input></div></td>
			<td><div style='visibility: hidden;'><a href='#' onclick='salva_acao(this);'>Salvar</a> | <a href='#' onclick='salva_acao(this,true);'>Cancelar</a></div></td>
			";
		
		return $html;
	
	
	}



}
?>