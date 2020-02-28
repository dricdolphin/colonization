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
	public $id = 0;
	public $nome = "";
	public $id_jogador = 0;
		

	/***********************
	function __construct(id_imperio = null)
	----------------------
	Inicializa os dados do Império
	$id_imperio = null -- Se não for passado um valor, o valor padrão é o id de usuário
	***********************/
	function __construct($id) {
		global $wpdb;
		$user = wp_get_current_user();
		$roles = $user->roles[0];
		
		//Somente cria um objeto com ID diferente se o usuário tiver perfil de administrador
		if (is_null($id) || $roles != "administrator") {
			$this->id_jogador = get_current_user_id();
			$this->id = $wpdb->get_var("SELECT id FROM colonization_imperio WHERE id_jogador=".$this->id_jogador);;
		} else {
			$this->id = $id;
			$this->id_jogador = $wpdb->get_var("SELECT id_jogador FROM colonization_imperio WHERE id=".$this->id);
		}
		
		$this->nome = $wpdb->get_var("SELECT nome FROM colonization_imperio WHERE id=".$this->id);
		//$this->planeta[] = pega_planetas_imperio(this.id);
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		$user = get_user_by('ID',$this->id_jogador);
		
		//Exibe os dados do Império
		$html = "			<td><input type='hidden' data-atributo='id_jogador' data-valor-original='{$this->id_jogador}' value='{$this->id_jogador}'></input>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
				<input type='hidden' data-atributo='where_clause' value='id_jogador'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id_jogador}'></input>
				<input type='hidden' data-atributo='funcao_validacao' value='valida_imperio'></input>
				<input type='hidden' data-atributo='mensagem_exclui_objeto' value=\"Deseja mesmo excluir o Império '{$this->nome}'?\"></input>
				<div data-atributo='ID' >{$this->id}</div>
				<div><a href='#' onclick='edita_objeto(this);'>Editar</a> | <a href='#' onclick='excluir_objeto(this);'>Excluir</a></div>
			</td>
			<td><div data-atributo='nome_jogador'>{$user->display_name}</div></td>
			<td><div data-atributo='nome' data-valor-original='{$this->nome}' data-editavel='true'>{$this->nome}</div></td>";
		return $html;
	}


	/***********************
	function imperio_exibe_imperio()
	----------------------
	Exibe os dados do Império
	***********************/
	function imperio_exibe_imperio() {
		//TODO -- Exibe todos os dados do Império numa tabela. Recursos Globais, Planetas e Recursos Locais, e posição das Frotas
		
		//Exibe os dados do Império
		$html = "<div>Dados do Império '{$this->nome}'</div>";
		return $html;
	}
	
}
?>