<?php
/**************************
ESTRELA.PHP
----------------
Cria o objeto "estrela" e mostra os dados da estrela
***************************/

//Classe "estrela"
//Contém os dados da estrela
class estrela
{
	public $id;
	public $nome;
	public $X;
	public $Y;
	public $Z;
	public $tipo;
	
	/***********************
	function __construct($id_estrela)
	----------------------
	Inicializa os dados da Estrela
	$id_estrela = ID da estrela
	***********************/
	function __construct($id_estrela) {
		global $wpdb;
		$this->id = $id_estrela;

		$resultados = $wpdb->get_results("SELECT nome, X, Y, Z, tipo FROM colonization_estrela WHERE id=".$this->id);
		$resultado = $resultados[0];
		
		$this->nome = $resultado->nome;
		$this->X = $resultado->X;
		$this->Y = $resultado->Y;
		$this->Z = $resultado->Z;
		$this->tipo = $resultado->tipo;
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
				<input type='hidden' data-atributo='funcao_validacao' value='valida_estrela'></input>
				<div data-atributo='nome' data-valor-original='{$this->nome}' data-editavel='true'>{$this->nome}</div>
				<div><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='excluir_objeto(this,\"Deseja mesmo excluir esta estrela?\");'>Excluir</a></div>
			</td>
			<td><div data-atributo='X' data-style='width: 100%;' data-editavel='true' data-valor-original='{$this->X}'>{$this->X}</div></td>
			<td><div data-atributo='Y' data-style='width: 100%;' data-editavel='true' data-valor-original='{$this->Y}'>{$this->Y}</div></td>
			<td><div data-atributo='Z' data-style='width: 100%;' data-editavel='true' data-valor-original='{$this->Z}' >{$this->Z}</div></td>
			<td><div data-atributo='tipo' data-valor-original='{$this->tipo}' data-editavel='true'>{$this->tipo}</div></td>
			<td><div data-atributo='gerenciar'><a href='#' onclick='return gerenciar_objeto(event, this);'>Gerenciar Objeto</a></div></td>";
		return $html;
	}
}
?>