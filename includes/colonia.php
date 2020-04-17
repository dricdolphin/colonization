<?php
/**************************
COLONIA.PHP
----------------
Cria o objeto "colônia"
***************************/

//Classe "colonia"
//Contém os dados da colonia
class colonia 
{
	public $id;
	public $id_imperio;
	public $id_planeta;
	public $pop;
	public $poluicao;
	public $turno;
	public $planeta;
	public $estrela;
	public $instalacoes;
	
	function __construct($id, $turno=0) {
		global $wpdb;
		
		$this->turno = new turno($turno);
		
		$this->id = $id;

		$resultados = $wpdb->get_results("SELECT id_imperio, id_planeta, pop, poluicao FROM colonization_imperio_colonias WHERE id=".$this->id);
		$resultado = $resultados[0];				
		
		$this->id_imperio = $resultado->id_imperio;
		$this->id_planeta = $resultado->id_planeta;
		$this->pop = $resultado->pop;
		$this->poluicao = $resultado->poluicao;
		$this->instalacoes = $wpdb->get_var("SELECT SUM(ci.slots) 
		FROM colonization_planeta_instalacoes AS cpi
		JOIN colonization_instalacao AS ci
		ON ci.id = cpi.id_instalacao
		WHERE cpi.id_planeta={$this->id_planeta}");
		
		$this->planeta = new planeta($this->id_planeta);
		$this->estrela = new estrela($this->planeta->id_estrela);

		//Atualiza os recursos da Colônia para o Turno atual, se necessário
		$max_turnos = $wpdb->get_results("SELECT cpr.id_recurso, MAX(cpr.turno) as turno 
		FROM colonization_planeta_recursos AS cpr
		WHERE cpr.id_planeta={$this->id_planeta} 
		GROUP BY cpr.id_recurso, cpr.id_planeta");

		foreach ($max_turnos as $max_turno) {
			if ($max_turno->turno < $this->turno->turno) {//Atualiza os recursos do planeta caso não esteja no Turno Atual
				$wpdb->query("UPDATE colonization_planeta_recursos SET turno={$this->turno->turno} WHERE turno={$max_turno->turno} AND id_planeta={$this->id_planeta} AND id_recurso={$max_turno->id_recurso}");
			}
		}
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;

		//Exibe os dados do Império		
		$html = "		
			<td>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
				<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='{$this->id_imperio}' value='{$this->id_imperio}'></input>
				<input type='hidden' data-atributo='id_planeta' data-ajax='true' data-valor-original='{$this->id_planeta}' value='{$this->id_planeta}'></input>
				<input type='hidden' data-atributo='where_clause' value='id'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
				<input type='hidden' data-atributo='funcao_validacao' value='valida_colonia'></input>
				<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir este planeta e todas suas ligações (recursos, instalações etc)?'></input>
				<div data-atributo='id' data-ajax='true'>{$this->id}</div>
				<div><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a></div>
			</td>
			<td><div data-atributo='nome_planeta' data-editavel='true' data-type='select' data-funcao='lista_planetas_html' data-id-selecionado='{$this->id_planeta}' data-valor-original='{$this->planeta->nome} - {$this->estrela->X};{$this->estrela->Y};{$this->estrela->Z} / {$this->planeta->posicao}'>{$this->planeta->nome} - {$this->estrela->X};{$this->estrela->Y};{$this->estrela->Z} / {$this->planeta->posicao}</div></td>
			<td><div data-atributo='pop' data-editavel='true' data-valor-original='{$this->pop}' data-style='width: 60px;'>{$this->pop}</div></td>
			<td><div data-atributo='poluicao' data-editavel='true' data-valor-original='{$this->poluicao}' data-style='width: 30px;'>{$this->poluicao}</div></td>
			<td><div data-atributo='turno' data-editavel='true' data-valor-original='{$this->turno->turno}' data-style='width: 30px;'>{$this->turno->turno}</div></td>
			<td><div data-atributo='gerenciar'><a href='#' onclick='return gerenciar_objeto(event, this);'>Gerenciar Objeto</a></div></td>";
		
		return $html;
	}

	/***********************
	function exibe_recursos_colonia()
	----------------------
	Exibe os recursos da Colônia
	***********************/
	function exibe_recursos_colonia() {
		global $wpdb;

		$resultados = $wpdb->get_results("
		SELECT cpr.id
		FROM colonization_planeta_recursos AS cpr
		WHERE cpr.turno = {$this->turno->turno} AND
		cpr.id_planeta={$this->planeta->id}
		");
		
		$html = "";
		foreach ($resultados as $resultado) {
			$planeta_recurso = new planeta_recurso($resultado->id);
			$imperio_recursos = new imperio_recursos($this->id_imperio);
			$recurso = new recurso($planeta_recurso->id_recurso);
			
			$chave_recurso = array_search($planeta_recurso->id_recurso,$imperio_recursos->id_recurso);
			if ($imperio_recursos->disponivel[$chave_recurso] == 1) {//Somente exibe os recursos que o Império já conhecer
				$html .= "{$recurso->nome}: {$planeta_recurso->disponivel}, ";
			}
		}
		
		if ($html != "") {
			$html = substr($html,0,-2);
		}
		
		return $html;
	}
}
?>