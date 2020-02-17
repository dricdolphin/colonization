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
		$resultados = $wpdb->get_results("SELECT nome, X, Y, Z, tipo FROM colonization_estrela WHERE id=".$this->id);
		$resultado = $resultados[0];
		$this->id = $id_estrela;
		$this->nome = $resultado->nome;
		$this->X = $resultado->X;
		$this->Y = $resultado->Y;
		$this->Z = $resultado->Z;
		$this->tipo = $resultado->tipo;
	}
	
}

?>