<?php
/**************************
LISTA_USUARIOS.PHP
----------------
Cria o objeto "lista_usuarios", que contém uma lista
com todos os IDs e nomes de usuários
***************************/

//Classe "acoes"
//Contém os dados da instalacao
class lista_usuarios 
{
	public $html_lista_usuarios;
	
	function __construct() {
		$users = get_users(array( 'fields' => array( 'ID', 'display_name' ) )); //Pega todos os usuários
		$lista_usuarios = "";
		foreach ($users as $user) {
			$lista_usuarios .= "+\"<option value='imperio_".$user->ID."'>".$user->display_name."</option>\"\n";
		}

		$this->html_lista_usuarios = 
		"/******************
		function lista_jogadores_html(id_imperio =0)
		--------------------
		Cria a lista de jogadores
		id_imperio = 0 -- define qual jogador está selecionado
		******************/
		function lista_jogadores_html(id_imperio = 0) {
			//TODO -- Pegar a lista de jogadores do WordPress
			var \$html = \"<select id='id_jogador'>\"
			$lista_usuarios
			+\"</select>\";
				
			return \$html;
		}";
	}
}

?>