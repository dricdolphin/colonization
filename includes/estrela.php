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
	public $colonias;
	
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
		$this->colonias = $wpdb->get_var("
		SELECT COUNT(ce.id) FROM 
		colonization_planeta AS cp
		JOIN colonization_estrela AS ce
		ON ce.id = cp.id_estrela
		AND ce.id = {$this->id}
		JOIN colonization_imperio_colonias AS cic
		ON cic.id_planeta = cp.id"); //Pega se tem colônias
	}
	
	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;
		
		$estilo_colonias = "";
		if ($this->colonias > 0  ) {
			$estilo_colonias = "style='font-weight: bold;'";
		}
		
		//Exibe os dados do Império
		$html = "				<td>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
				<input type='hidden' data-atributo='where_clause' value='id'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
				<input type='hidden' data-atributo='funcao_validacao' value='valida_estrela'></input>
				<div data-atributo='nome' data-valor-original='{$this->nome}' data-editavel='true' {$estilo_colonias}>{$this->nome}</div>
				<div><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='excluir_objeto(this,\"Deseja mesmo excluir esta estrela?\");'>Excluir</a></div>
			</td>
			<td><div data-atributo='X' data-style='width: 100%;' data-editavel='true' data-valor-original='{$this->X}'>{$this->X}</div></td>
			<td><div data-atributo='Y' data-style='width: 100%;' data-editavel='true' data-valor-original='{$this->Y}'>{$this->Y}</div></td>
			<td><div data-atributo='Z' data-style='width: 100%;' data-editavel='true' data-valor-original='{$this->Z}' >{$this->Z}</div></td>
			<td><div data-atributo='tipo' data-valor-original='{$this->tipo}' data-editavel='true'>{$this->tipo}</div></td>
			<td><div data-atributo='gerenciar'><a href='#' onclick='return gerenciar_objeto(event, this);'>Gerenciar Objeto</a></div></td>";
		return $html;
	}
	
	/***********************
	function distancia_estrela($id_estrela)
	----------------------
	Calcula a distância entre a estrela atual e outra estrela
	***********************/
	function distancia_estrela($id_estrela) {
		global $wpdb;
		
		$estrela_destino = new estrela($id_estrela);
		$distancia = (($this->x - $estrela_destino->x)^2 + ($this->y - $estrela_destino->y)^2 + ($this->z - $estrela_destino->z)^2)^0.5;
		
		return $distancia;
	}
	
}
?>