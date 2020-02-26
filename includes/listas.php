<?php
/**************************
LISTAS.PHP
----------------
Contém objetos de criação de listas
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

//Classe "lista_usuarios"
//Mostra a lista de usuários, com os IDs
class lista_usuarios 
{
	public $html_lista;
	
	function __construct() {
		$users = get_users(array( 'fields' => array( 'ID', 'display_name' ) )); //Pega todos os usuários
		$lista_usuarios = "";
		foreach ($users as $user) {
			$lista_usuarios .= "
+\"		<option value='{$user->ID}'>{$user->display_name}</option>\"\n";
		}

		$this->html_lista = 
"		/******************
		function lista_jogadores_html(id_jogador =0)
		--------------------
		Cria a lista de jogadores
		id_imperio = 0 -- define qual jogador está selecionado
		******************/
		function lista_jogadores_html(id_jogador = 0) {
			
			var html = \"			<select data-atributo='id_jogador'>\"
			$lista_usuarios
			+\"			</select>\";
				
			return html;
		}";
	}
}

//Classe "lista_recursos"
//Mostra a lista de recursos, com os IDs
class lista_recursos
{
	public $html_lista;
	
	function __construct() {
		global $wpdb;
		$resultados = $wpdb->get_results("SELECT id, nome from colonization_recurso");

		$lista_valores = "";
		$lista_options = "";
		$index = 0;
		foreach ($resultados as $resultado) {
			$lista_options .= "			lista[{$index}]=\"<option value='{$resultado->id}'\"+selecionado[{$index}]+\">{$resultado->nome}</option>\";\n";
			$lista_valores .= "			lista_valores[{$index}]={$resultado->id};\n";
			$index++;
		}

		$this->html_lista = 
"		/******************
		function lista_recursos_html(id=0)
		--------------------
		Cria a lista de recursos
		id -- qual ID está selecionado
		******************/
		function lista_recursos_html(id=0) {
			
			var lista=[];
			var lista_valores=[];
			var selecionado=[];
{$lista_valores}
			
			var html = \"			<select data-atributo='id_recurso'>\";
			for (var index = 0; index < lista_valores.length; index++) {
				if (lista_valores[index] == id) {
					selecionado[index] = 'selected';
				} else {
					selecionado[index] = '';
				}
			}
			
{$lista_options}
			for (index = 0; index < lista.length; index++) {
				html = html+lista[index];
			}
			html = html +\"			</select>\";


			return html;
		}";
	}
}
?>