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
	public $nome_npc;
	public $id_planeta;
	public $capital;
	public $vassalo;
	public $icone_capital;
	public $icone_vassalo;
	public $pop;
	public $pop_robotica;
	public $mdo;
	public $poluicao;
	public $turno;
	public $planeta;
	public $estrela;
	public $instalacoes;
	public $num_instalacoes;
	public $pdf_planetario;
	public $defesa_invasao = 0;
	public $qtd_defesas = 0;
	public $html_pop_colonia;
	public $html_instalacao_ataque = [];
	
	function __construct($id, $turno=0) {
		global $wpdb;
		
		$this->turno = new turno($turno);
		
		$this->id = $id;

		$resultados = $wpdb->get_results("SELECT id_imperio, nome_npc, id_planeta, capital, vassalo, pop, pop_robotica, poluicao FROM colonization_imperio_colonias WHERE id=".$this->id);
		$resultado = $resultados[0];				
		
		$this->id_imperio = $resultado->id_imperio;
		$this->nome_npc = $resultado->nome_npc;
		$this->id_planeta = $resultado->id_planeta;
		$this->capital = $resultado->capital;
		$this->vassalo = $resultado->vassalo;
		$this->pop = $resultado->pop;
		$this->pop_robotica = $resultado->pop_robotica;
		$this->poluicao = $resultado->poluicao;
		$this->instalacoes = $wpdb->get_var("SELECT SUM(ci.slots) 
		FROM colonization_planeta_instalacoes AS cpi
		JOIN colonization_instalacao AS ci
		ON ci.id = cpi.id_instalacao
		WHERE cpi.id_planeta={$this->id_planeta}
		AND cpi.turno <={$this->turno->turno}");
		
		$this->num_instalacoes = $wpdb->get_var("SELECT COUNT(cpi.id) 
		FROM colonization_planeta_instalacoes AS cpi
		JOIN colonization_instalacao AS ci
		ON ci.id = cpi.id_instalacao
		WHERE cpi.id_planeta={$this->id_planeta}
		AND cpi.turno <={$this->turno->turno}
		AND ci.oculta = false
		");		
		
		$this->planeta = new planeta($this->id_planeta, $this->turno->turno);
		$this->estrela = new estrela($this->planeta->id_estrela);

		$this->icone_capital = "";
		if ($this->capital == 1) {
			$this->icone_capital = "<div class='fas fa-crown tooltip' style='color: #DAA520;'>&nbsp;<span class='tooltiptext'>Capital</span></div>";
		}

		$this->icone_vassalo = "";
		if ($this->vassalo == 1) {
			$this->icone_vassalo = "<div class='fas fa-pray tooltip' style='color: #4A4B14;'>&nbsp;<span class='tooltiptext'>Vassalo</span></div>";
		}

		$this->html_pop_colonia = "{$this->pop}";
		if ($this->pop_robotica > 0) {
			$this->html_pop_colonia .= "(<div class='fas fa-users-cog tooltip'>&nbsp;<span class='tooltiptext'>População Robótica</span></div>{$this->pop_robotica})";
		}

		$this->qtd_defesas = round(($this->pop/10),0,PHP_ROUND_HALF_DOWN);

		/***
		if ($this->qtd_defesas > 0) {
			$imperio = new imperio($this->id_imperio,false,$this->turno->turno);
			$this->pdf_planetario = round(($imperio->pdf_planetario*$this->qtd_defesas/10),0,PHP_ROUND_HALF_DOWN);
			$this->defesa_invasao = $imperio->defesa_invasao*$this->qtd_defesas;
		}
		//***/

		//Atualiza os recursos da Colônia para o Turno atual, se necessário
		$max_turnos = $wpdb->get_results("SELECT cpr.id_recurso, MAX(cpr.turno) as turno 
		FROM colonization_planeta_recursos AS cpr
		WHERE cpr.id_planeta={$this->id_planeta} 
		GROUP BY cpr.id_recurso, cpr.id_planeta");

		foreach ($max_turnos as $max_turno) {
			if ($max_turno->turno < $this->turno->turno) {//Atualiza os recursos do planeta caso não esteja no Turno Atual
				$id_planeta_recurso = $wpdb->get_var("SELECT id FROM colonization_planeta_recursos WHERE id_planeta={$this->id_planeta} AND id_recurso={$max_turno->id_recurso} AND turno={$max_turno->turno}");
				$planeta_recurso = new planeta_recurso($id_planeta_recurso);
				$wpdb->query("INSERT INTO colonization_planeta_recursos SET turno={$this->turno->turno}, id_planeta={$this->id_planeta}, id_recurso={$max_turno->id_recurso}, disponivel={$planeta_recurso->qtd_disponivel}");
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

		$capital_checked = "";
		if ($this->capital == 1) {
			$capital_checked = "checked";
		}
		
		$vassalo_checked = "";
		if ($this->vassalo == 1) {
			$vassalo_checked = "checked";
		}

		if ($this->id_imperio == 0) {
			$imperios_npc = "<td><div data-atributo='nome_npc' data-editavel='true' data-valor-original='{$this->nome_npc}' data-style='width: 120px;'>{$this->nome_npc}</div></td>";
		} else {
			$imperios_npc = "";
		}

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
				<div data-atributo='gerenciar'><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a></div>
			</td>
			{$imperios_npc}
			<td><div data-atributo='nome_planeta' data-editavel='true' data-type='select' data-funcao='lista_planetas_html' data-id-selecionado='{$this->id_planeta}' data-valor-original='{$this->planeta->nome} - {$this->estrela->X};{$this->estrela->Y};{$this->estrela->Z} / {$this->planeta->posicao}'>{$this->planeta->nome} - {$this->estrela->X};{$this->estrela->Y};{$this->estrela->Z} / {$this->planeta->posicao}</div></td>
			<td><div data-atributo='capital' data-type='checkbox' data-editavel='true' data-valor-original='{$this->capital}'><input type='checkbox' data-atributo='capital' data-ajax='true' {$capital_checked} disabled></input></div></td>			
			<td><div data-atributo='vassalo' data-type='checkbox' data-editavel='true' data-valor-original='{$this->vassalo}'><input type='checkbox' data-atributo='vassalo' data-ajax='true' {$vassalo_checked} disabled></input></div></td>			
			<td><div data-atributo='pop' data-editavel='true' data-valor-original='{$this->pop}' data-style='width: 60px;'>{$this->pop}</div></td>
			<td><div data-atributo='pop_robotica' data-editavel='true' data-valor-original='{$this->pop_robotica}' data-style='width: 60px;'>{$this->pop_robotica}</div></td>
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
				$html .= "{$recurso->nome}: {$planeta_recurso->qtd_disponivel}, ";
			}
		}
		
		if ($html != "") {
			$html = substr($html,0,-2);
		}
		
		return $html;
	}
}
?>