<?php
/**************************
MODELO_NAVES.PHP
----------------
Cria o objeto "modelo_naves"
***************************/

//Classe "modelo_naves"
//Contém os dados dos modelos de naves
class modelo_naves
{
	public $id;
	public $id_imperio;
	public $nome_modelo;
	public $string_nave;
	public $texto_nave;
	public $texto_custo;
	
	/***********************
	function __construct()
	----------------------
	Inicializa os dados do turno
	***********************/
	function __construct($id=0) {
		global $wpdb;

		if ($id == 0) {
			$this->id = 0;
			return;
		}			
			
		$resultados = $wpdb->get_results("SELECT id, descricao, id_tech, slots, autonoma, desguarnecida, sempre_ativa, oculta, publica, icone, especiais, custos FROM colonization_instalacao WHERE id=".$this->id);
		$resultado = $resultados[0];
		
		$this->id = $id;
		$this->id_imperio = $resultado->id_imperio;
		$this->nome_modelo = $resultado->nome_modelo;
		$this->string_nave = $resultado->string_nave;
		$this->texto_nave = $resultado->texto_nave;
		$this->texto_custo = $resultado->texto_custo;
	}
	
	function lista_dados() {
		global $wpdb;
		
		$html = "<tr><td><input type='hidden' data-atributo='string_nave' value='{$this->string_nave}'></input>{$this->nome_modelo}</td><td>{$this->nome_modelo}</td><td>{$this->nome_modelo}</td><td><a href='#' onclick='return carrega_nave(event, this);'>Carregar Nave</a><br><a href='#' onclick='return deleta_nave(event, this, {$this->id});'>Deletar Nave</a></td></tr>";
	
		return $html;
	}
	
}
?>