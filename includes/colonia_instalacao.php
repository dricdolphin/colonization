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
	public $turno_desmonta;
	public $instalacao_inicial;
	public $instalacao;
	public $planeta;
	public $imperio;
	
	function __construct($id) {
		global $wpdb;
		
		$this->id = $id;

		$resultados = $wpdb->get_results("SELECT id_planeta, id_instalacao, nivel, turno, instalacao_inicial, turno_destroi, turno_desmonta FROM colonization_planeta_instalacoes WHERE id=".$this->id);
		$resultado = $resultados[0];				
		
		$this->id_planeta = $resultado->id_planeta;
		$this->id_instalacao = $resultado->id_instalacao;
		$this->nivel = $resultado->nivel;
		$this->turno = $resultado->turno;
		$this->instalacao_inicial = $resultado->instalacao_inicial;
		$this->turno_destroi = $resultado->turno_destroi;
		$this->turno_desmonta = $resultado->turno_desmonta;
		
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

		if ($this->instalacao_inicial == 1) {
			$instalacao_inicial_checked = "checked";
		} else {
			$instalacao_inicial_checked = "";
		}
		
		$this->instalacao = new instalacao($this->id_instalacao);
		
		if (empty($this->turno_destroi)) {
			$texto_destruir	= "Destruir Instalação";
			$this->turno_destroi = "&nbsp;";
		} else {
			$texto_destruir	= "Reparar Instalação";
		}
		
		$html_desmontar = "&nbsp;";
		if (empty($this->turno_desmonta)) {
			$turno_atual = new turno();
			$html_desmontar = "<a href='#' onclick='return desmonta_instalacao(event, this, {$turno_atual->turno});'>Desmantelar</a>";
			$this->turno_desmonta = "&nbsp;";
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
			<td><div data-atributo='instalacao_inicial' data-type='checkbox' data-editavel='true' data-valor-original='{$this->instalacao_inicial}'><input type='checkbox' data-atributo='instalacao_inicial' data-ajax='true' {$instalacao_inicial_checked} disabled></input></div></td>			
			<td><div data-atributo='turno_desmonta' data-style='width: 30px;'  data-valor-original='{$this->turno_desmonta}' data-branco='true'>{$this->turno_desmonta}</div></td>
			<td><div data-atributo='turno_destroi' data-valor-original='{$this->turno_destroi}' data-style='width: 30px;' data-branco='true'>{$this->turno_destroi}</div></td>
			<td>
			<div data-atributo='gerenciar' id='destruir_{$this->id}'><a href='#' onclick='return destruir_instalacao(event, this);'>{$texto_destruir}</a></div>
			<div data-atributo='gerenciar'>{$html_desmontar}</div>
			</td>";
		
		return $html;
	}
}
?>