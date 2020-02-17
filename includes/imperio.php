<?php
/**************************
IMPERIO.PHP
----------------
Cria o objeto "Império" e mostra os dados do Império
***************************/

//Classe "imperio"
//Contém os dados do Império
class imperio 
{
//TODO -- Criar a classe

	//TODO -- Criar as variáveis do Império
	//planeta imperio_planetas[];
	//recurso imperio_recursos[];
	public $imperio_nome = "";
	public $id = 0;
		

	/***********************
	function __construct(id_imperio = null)
	----------------------
	Inicializa os dados do Império
	$id_imperio = null -- Se não for passado um valor, o valor padrão é o id de usuário
	***********************/
	function __construct($id_imperio = null) {
		global $wpdb;
		//TODO - inicializa o Império
		//TODO - queries para puxar os dados do Império
		$user = wp_get_current_user();
		$roles = $user->roles[0];
		
		//Somente cria um objeto com ID diferente se o usuário tiver perfil de administrador
		if (is_null($id_imperio) || $roles != "administrator") {
			$this->id = get_current_user_id();
		} else {
			$this->id = $id_imperio;
		}
		
		//As funções abaixo ainda serão criadas
		$nome = $wpdb->get_var("SELECT nome FROM colonization_imperio WHERE id_jogador=".$this->id);
		$this->imperio_nome = $nome;
		//$this->planeta[] = pega_planetas_imperio(this.id);
	}

	/***********************
	function imperio_exibe_imperio()
	----------------------
	Exibe os dados do Império
	***********************/
	function imperio_exibe_imperio() {
		//Exibe os dados do Império
		$html = "<div>".$this->id."</div>";
		return $html;
	}
	
}
?>