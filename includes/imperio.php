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

	public $id;
	public $nome;
	public $id_jogador;
	public $pop = 0;
	public $pontuacao = 0;
	public $html_header;

	/***********************
	function __construct(id_imperio = null)
	----------------------
	Inicializa os dados do Império
	$id_imperio = null -- Se não for passado um valor, o valor padrão é o id de usuário
	***********************/
	function __construct($id) {
		global $wpdb;
		
		$today = date("YmdHi"); 
		$this->html_header = "<link rel='stylesheet' type='text/css' href='../wp-content/plugins/colonization/colonization.css?v={$today}'>
		<script src='../wp-content/plugins/colonization/includes/novo_objetos.js?v={$today}'></script>
		<script src='../wp-content/plugins/colonization/includes/edita_objetos.js?v={$today}'></script>
		<script src='../wp-content/plugins/colonization/includes/valida_objetos.js?v={$today}'></script>
		<script src='../wp-content/plugins/colonization/includes/gerencia_objeto.js?v={$today}'></script>";
		
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
		
		$this->pop = $wpdb->get_var("SELECT 
		(CASE 
		WHEN SUM(pop) IS NULL THEN 0
		ELSE SUM(pop)
		END) AS pop
		FROM colonization_imperio_colonias
		WHERE id_imperio={$this->id}");
		
		//TODO -- Criar a função para pegar a pontuação do Império
		$this->pontuacao = 999;
		
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
				<input type='hidden' data-atributo='funcao_pos_processamento' value='mais_dados_imperio'></input>
				<input type='hidden' data-atributo='mensagem_exclui_objeto' value=\"Deseja mesmo excluir o Império '{$this->nome}'?\"></input>
				<div data-atributo='ID' >{$this->id}</div>
				<div><a href='#' onclick='edita_objeto(this);'>Editar</a> | <a href='#' onclick='excluir_objeto(this);'>Excluir</a></div>
			</td>
			<td><div data-atributo='nome_jogador'>{$user->display_name}</div></td>
			<td><div data-atributo='nome' data-valor-original='{$this->nome}' data-editavel='true'>{$this->nome}</div></td>
			<td><div data-atributo='pop' data-valor-original=''>{$this->pop}</div></td>
			<td><div data-atributo='pontuacao' data-valor-original=''>{$this->pontuacao}</div></td>
			<td><div data-atributo='gerenciar'><a href='#' onclick='gerenciar_objeto(this);'>Gerenciar Objeto</a></div></td>";

		return $html;
	}


	/***********************
	function imperio_exibe_imperio()
	----------------------
	Exibe os dados do Império
	***********************/
	function imperio_exibe_imperio() {
		global $wpdb;
		
		$total_colonias = $wpdb->get_var("SELECT COUNT(id) FROM colonization_imperio_colonias WHERE id_imperio={$this->id}");
		//Exibe os dados básicos do Império
		$html = "<div>{$this->nome} - População: {$this->pop} - Pontuação: {$this->pontuacao}</div>
		<div>Total de Colônias: {$total_colonias}</div>";
		return $html;
	}

	/***********************
	function imperio_exibe_colonias_imperio()
	----------------------
	Exibe os dados do Império
	***********************/
	function imperio_exibe_colonias_imperio() {
		global $wpdb;
		
		$colonias = $wpdb->get_results("SELECT id FROM colonization_imperio_colonias WHERE id_imperio={$this->id}");
		
		$html = $this->html_header;
		
		$html .= "<table class='wp-list-table widefat fixed striped users'>
		<thead>
		<tr><td>Estrela (X;Y;Z)</td><td>Planeta (posição)</td><td>População</td><td>Poluição</td></tr>
		</thead>
		<tbody>
		";
		
		foreach ($colonias as $id) {
			$colonia = new colonia($id->id);
			$planeta = new planeta($colonia->id_planeta);
			$estrela = new estrela($planeta->id_estrela);
			$html .= "<tr><td>{$estrela->nome} ({$estrela->X};{$estrela->Y};{$estrela->Z})</td><td>{$planeta->nome} ({$planeta->posicao})</td><td>{$colonia->pop}</td><td>{$colonia->poluicao}</td></tr>";
		}
		
		$html .= "</tbody>
		</table>";

		return $html;
	}
}
?>