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
	function __construct($id, $super=false)
	----------------------
	Inicializa os dados do Império
	$id_imperio = null -- Se não for passado um valor, o valor padrão é o id de usuário
	$super -- Define se é para forçar o objeto (ignora as proteções)
	***********************/
	function __construct($id, $super=false) {
		global $wpdb;
		
		$user = wp_get_current_user();
		$roles = $user->roles[0];
		
		//Somente cria um objeto com ID diferente se o usuário tiver perfil de administrador
		if ((is_null($id) || $roles != "administrator") && !$super) {
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
		
		//A pontuação será: No de Colonias*100 + No de Instalações x Nível x 10 + Pop + Estoque + Custo das Naves
		$pontuacao = $wpdb->get_var("SELECT COUNT(id)*100 FROM colonization_imperio_colonias WHERE id_imperio={$this->id}");
		$this->pontuacao = $this->pontuacao + $pontuacao;
		
		$pontuacao = $wpdb->get_var("SELECT SUM(nivel)*10 
		FROM colonization_planeta_instalacoes AS cpi
		JOIN colonization_imperio_colonias  AS cic
		ON cic.id_planeta = cpi.id_planeta
		WHERE cic.id_imperio={$this->id}");
		$this->pontuacao = $this->pontuacao + $pontuacao;

		$pontuacao = $wpdb->get_var("SELECT SUM(pop) FROM colonization_imperio_colonias WHERE id_imperio={$this->id}");
		$this->pontuacao = $this->pontuacao + $pontuacao;

		$pontuacao = $wpdb->get_var("SELECT SUM(qtd) FROM colonization_imperio_recursos WHERE id_imperio={$this->id}");
		$this->pontuacao = $this->pontuacao + $pontuacao;
		
		$pontuacao = $wpdb->get_var("SELECT (SUM(tamanho)+SUM(PDF_laser)+SUM(PDF_projetil)+SUM(PDF_torpedo)+SUM(blindagem)+SUM(escudos)+SUM(alcance))*2 AS pontuacao FROM colonization_imperio_frota WHERE id_imperio={$this->id}");
		$this->pontuacao = $this->pontuacao + $pontuacao;
		

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
				<div><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a></div>
			</td>
			<td><div data-atributo='nome_jogador'>{$user->display_name}</div></td>
			<td><div data-atributo='nome' data-valor-original='{$this->nome}' data-editavel='true'>{$this->nome}</div></td>
			<td><div data-atributo='pop' data-valor-original=''>{$this->pop}</div></td>
			<td><div data-atributo='pontuacao' data-valor-original=''>{$this->pontuacao}</div></td>
			<td><div data-atributo='gerenciar'><a href='#' onclick='return gerenciar_objeto(event, this);'>Gerenciar Objeto</a></div></td>";

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
	
	/***********************
	function exibe_recursos_atuais()
	----------------------
	Exibe os recursos atuais Império
	***********************/
	function exibe_recursos_atuais($turno=0) {
		global $wpdb;
		
		$turno = new turno($turno);
		
		$resultados = $wpdb->get_results("
		SELECT cir.qtd, cr.nome
		FROM colonization_imperio_recursos AS cir
		JOIN colonization_recurso AS cr
		ON cr.id=cir.id_recurso
		WHERE cir.id_imperio = {$this->id} AND turno={$turno->turno}
		AND cr.acumulavel = true
		AND cir.disponivel = true
		");
		
		$html = "<b>Recursos atuais:</b> ";
		foreach ($resultados as $resultado) {
			$html .= "{$resultado->nome} - {$resultado->qtd}; ";
		}
		
		return $html;
	}
	
	/***********************
	function exibe_lista_colonias()
	----------------------
	Exibe as Colônias atuais Império
	***********************/
	function exibe_lista_colonias($turno=0) {
		global $wpdb;
		
		$turno = new turno($turno);
		
		$resultados = $wpdb->get_results("
		SELECT cp.nome, cic.pop
		FROM colonization_imperio_colonias AS cic
		JOIN colonization_planeta AS cp
		ON cp.id=cic.id_planeta
		WHERE cic.id_imperio = {$this->id}
		");
		
		$html = "";
		foreach ($resultados as $resultado) {
			$html .= "{$resultado->nome} - Pop: {$resultado->pop}; ";
		}
		
		return $html;
	}
	
}
?>