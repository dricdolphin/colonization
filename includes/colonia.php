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
	public $nome_npc = "";
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
	public $id_estrela;
	public $instalacoes;
	public $instalacoes_planeta = [];
	public $num_instalacoes;
	public $pdf_planetario;
	public $defesa_invasao = 0;
	public $qtd_defesas = 0;
	public $html_pop_colonia;
	public $html_instalacao_ataque = [];
	public $bonus_extrativo = false;
	public $bonus_recurso = [];
	
	function __construct($id, $turno=0) {
		global $wpdb;
		
		$this->turno = new turno($turno);
		
		$this->id = $id;

		$resultados = $wpdb->get_results("SELECT id_imperio, nome_npc, id_planeta, capital, vassalo, pop, pop_robotica, poluicao FROM colonization_imperio_colonias WHERE id=".$this->id);
		$resultado = $resultados[0];				
		
		$this->id_imperio = $resultado->id_imperio;
		if ($this->id_imperio == 0) {
			$this->nome_npc = $resultado->nome_npc;
		}
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
		
		//$this->planeta = new planeta($this->id_planeta, $this->turno->turno);
		//$this->estrela = new estrela($this->planeta->id_estrela);
		$this->id_estrela = $wpdb->get_var("SELECT id_estrela FROM colonization_planeta WHERE id = {$this->id_planeta}");
		
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
		$max_turnos = $wpdb->get_results("SELECT t1.id_recurso, t1.turno 
		FROM (
			SELECT cpr.id_recurso AS id_recurso, MAX(cpr.turno) as turno 
			FROM colonization_planeta_recursos AS cpr
			WHERE cpr.id_planeta={$this->id_planeta} 
			GROUP BY cpr.id_recurso, cpr.id_planeta) AS t1
		WHERE t1.turno < {$this->turno->turno}
		");

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
			$imperios_npc = "<td><div data-atributo='nome_npc' data-editavel='true' data-valor-original='{$this->nome_npc}' data-branco='true' data-style='width: 120px;'>{$this->nome_npc}</div></td>";
		} else {
			$imperios_npc = "";
		}

		$this->planeta = new planeta ($this->id_planeta);
		$this->estrela = new estrela ($this->id_estrela);
		$imperio = new imperio($this->id_imperio);
		
		$string_argumentos = '{"id_remove":"0", "npcs":"0"}';
		$html = "		
			<td>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
				<input type='hidden' data-atributo='id_planeta' data-ajax='true' data-valor-original='{$this->id_planeta}' value='{$this->id_planeta}'></input>
				<input type='hidden' data-atributo='where_clause' value='id'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
				<input type='hidden' data-atributo='funcao_validacao' value='valida_colonia'></input>
				<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir este planeta e todas suas ligações (recursos, instalações etc)?'></input>
				<input type='hidden' data-atributo='funcao_pos_processamento' value='altera_imperio_colonia'></input>
				<div data-atributo='id' data-ajax='true'>{$this->id}</div>
				<div data-atributo='nome_imperio' data-editavel='true' data-type='select' data-funcao='lista_imperios_html' data-argumentos='{$string_argumentos}' data-id-selecionado='{$this->id_imperio}' data-valor-original='{$imperio->nome}'>{$imperio->nome}</div>
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
		cpr.id_planeta={$this->id_planeta}
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
	

	/***********************
	function popula_instalacoes_planeta()
	----------------------
	Popula a variável $this->instalacoes_planeta[]
	***********************/	
	function popula_instalacoes_planeta() {
		global $wpdb;
		
		$instalacoes_colonia = $wpdb->get_results("SELECT 
		cpi.id, cpi.id_instalacao
		FROM colonization_planeta_instalacoes AS cpi
		WHERE cpi.id_planeta={$this->id_planeta} AND cpi.turno<={$this->turno->turno}
		");
		
		foreach ($instalacoes_colonia as $id_instalacao) {
			$turno_upgrade = $wpdb->get_var("SELECT MIN(turno) FROM colonization_planeta_instalacoes_upgrade WHERE id_planeta_instalacoes={$id_instalacao->id} AND turno > {$this->turno->turno}");
			if ($turno_upgrade > $this->turno->turno) {
				$id_instalacao->id_instalacao = $wpdb->get_var("SELECT id_instalacao_anterior FROM colonization_planeta_instalacoes_upgrade WHERE id_planeta_instalacoes={$id_instalacao->id} AND turno = {$turno_upgrade}");
			}				

			$this->instalacoes_planeta[$id_instalacao->id] = $id_instalacao->id_instalacao;
		}
	}
	
	/***********************
	function bonus_extrativo()
	----------------------
	Popula e retorna se existe alguma instalação que produz um bônus nos extrativos
	***********************/
	function bonus_extrativo() {
		global $wpdb;
		
		if (empty($this->instalacoes_planeta)) {
			$this->popula_instalacoes_planeta();
		}
		
		if ($this->bonus_extrativo === false) {
			$this->bonus_extrativo = 0;
			
			foreach ($this->instalacoes_planeta as $id_planeta_instalacoes => $id_instalacao) {
				
				$instalacao = new instalacao($id_instalacao);
				$this->bonus_extrativo = $this->bonus_extrativo + $instalacao->bonus_extrativo;
			}
		}
		
		return $this->bonus_extrativo;
	}

	/***********************
	function bonus_recurso()
	----------------------
	Popula e retorna se existe alguma instalação que produz um bônus de um recurso específico
	***********************/	
	function bonus_recurso($id_recurso) {
		global $wpdb;

		if (empty($this->instalacoes_planeta)) {
			$this->popula_instalacoes_planeta();
		}
		
		if (empty($this->bonus_recurso[$id_recurso])) {
			$this->bonus_recurso[$id_recurso] = 0;
			
			foreach ($this->instalacoes_planeta as $id_planeta_instalacoes => $id_instalacao) {				
				$instalacao = new instalacao($id_instalacao);
				$this->bonus_recurso[$id_recurso] = $this->bonus_recurso[$id_recurso] + $instalacao->bonus_recurso($id_recurso);
			}
		}
		
		return $this->bonus_recurso[$id_recurso];		
	}


	/***********************
	function pega_alimentos_consumidos_planeta()
	----------------------
	Calcula o consumo de Alimentos de um planeta por sua Pop
	***********************/		
	function pega_alimentos_consumidos_planeta ($imperio_alimento_inospito) {

		if (empty($this->planeta)) {
			$this->planeta = new planeta ($this->id_planeta);
		}
		
		//Existem algumas Techs que aumentam o consumo de alimentos
		$consumo_extra_inospito = 0;
		if ($this->planeta->inospito == 1 && $this->planeta->terraforma == 0) {
			if ($this->pop > $this->planeta->pop_inospito) {
				$pop_inospito = $this->pop - $this->planeta->pop_inospito;
				$consumo_extra_inospito = $pop_inospito * $imperio_alimento_inospito;
			}
		}
			
		//População acima do limite de Slots do planeta consome o DOBRO de alimento
		$consumo_extra_pop = 0;
		if ($this->pop > $this->planeta->tamanho*10) {
			$consumo_extra_pop = ($this->pop - $this->planeta->tamanho*10);
		}
	
		return $this->pop + $consumo_extra_inospito + $consumo_extra_pop;
	}
	
	
}
?>