<?php
/**************************
LISTA_ESTRELAS.PHP
----------------
Cria o objeto "lista_estrelas", que contém uma lista
com todos os IDs, nomes e posições das estrelas
***************************/

//Classe "lista_estrelas"
//Mostra a lista de estrelas, com os IDs, nome e posição
class lista_estrelas
{
	public $html_lista;
	
	function __construct() {
		global $wpdb;
		$resultados = $wpdb->get_results("SELECT id, nome, X, Y, Z from colonization_estrela");

		$lista = "";
		foreach ($resultados as $resultado) {
			$lista .= "
+\"		<option value='{$resultado->id}'>{$resultado->nome} - {$resultado->X};{$resultado->Y};{$resultado->Z}</option>\"\n";
		}

		$this->html_lista = 
"		/******************
		function lista_estrelas_html(id=0)
		--------------------
		Cria a lista de estrelas
		id -- qual ID está selecionado
		******************/
		function lista_estrelas_html(id=0) {
			
			var html = \"			<select data-atributo='id_estrela'>\"
			$lista
			+\"			</select>\";
				
			return html;
		}";
	}
}

?>