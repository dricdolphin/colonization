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
	public $satisfacao;
	public $turno;
	public $planeta;
	public $estrela;
	public $id_estrela;
	public $instalacoes;
	public $instalacoes_planeta = [];
	public $nivel_instalacoes_planeta = [];
	public $num_instalacoes;
	public $pdf_planetario;
	public $defesa_invasao = 0;
	public $qtd_defesas = 0;
	public $minas_subespaciais = 0;
	public $html_pop_colonia;
	public $bonus_extrativo = false;
	public $bonus_recurso = [];
	public $comercio_processou = false;
	
	function __construct($id, $turno=0) {
		global $wpdb;
		
		$this->turno = new turno($turno);
		
		$this->id = $id;

		$resultados = $wpdb->get_results("SELECT id_imperio, nome_npc, id_planeta, capital, vassalo, pop, pop_robotica, poluicao, satisfacao FROM colonization_imperio_colonias WHERE id=".$this->id);
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
		$this->satisfacao = $resultado->satisfacao;
		$this->instalacoes = $wpdb->get_var("SELECT SUM(ci.slots) 
		FROM colonization_planeta_instalacoes AS cpi
		JOIN colonization_instalacao AS ci
		ON ci.id = cpi.id_instalacao
		WHERE cpi.id_planeta={$this->id_planeta}
		AND cpi.turno <={$this->turno->turno}
		AND (cpi.turno_desmonta IS NULL OR cpi.turno_desmonta=0 OR cpi.turno_desmonta > {$this->turno->turno})
		");
		
		$this->num_instalacoes = $wpdb->get_var("SELECT COUNT(cpi.id) 
		FROM colonization_planeta_instalacoes AS cpi
		JOIN colonization_instalacao AS ci
		ON ci.id = cpi.id_instalacao
		WHERE cpi.id_planeta={$this->id_planeta}
		AND cpi.turno <={$this->turno->turno}
		AND (cpi.turno_desmonta IS NULL OR cpi.turno_desmonta=0 OR cpi.turno_desmonta > {$this->turno->turno})
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

		if (empty($this->planeta)) {
			$this->planeta = new planeta($this->id_planeta);
			//$this->planeta->popula_instalacoes_colonia();
		}
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
			<td><div data-atributo='satisfacao' data-editavel='true' data-valor-original='{$this->satisfacao}' data-style='width: 30px;'>{$this->satisfacao}</div></td>
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
	function popula_instalacoes_colonia()
	----------------------
	Popula a variável $this->instalacoes_planeta[]
	***********************/	
	function popula_instalacoes_colonia() {
		global $wpdb;
		
		$instalacoes_colonia = $wpdb->get_results("SELECT 
		cpi.id, cpi.id_instalacao, cpi.nivel
		FROM colonization_planeta_instalacoes AS cpi
		WHERE cpi.id_planeta={$this->id_planeta} AND cpi.turno<={$this->turno->turno}
		");
		
		foreach ($instalacoes_colonia as $id_instalacao_colonia) {
			$turno_upgrade = $wpdb->get_var("SELECT MIN(turno) FROM colonization_planeta_instalacoes_upgrade WHERE id_planeta_instalacoes={$id_instalacao_colonia->id} AND turno > {$this->turno->turno}");
			if ($turno_upgrade > $this->turno->turno) {
				$id_instalacao_colonia->id_instalacao = $wpdb->get_var("SELECT id_instalacao_anterior FROM colonization_planeta_instalacoes_upgrade WHERE id_planeta_instalacoes={$id_instalacao_colonia->id} AND turno = {$turno_upgrade}");
				$id_instalacao_colonia->nivel = $wpdb->get_var("SELECT nivel_anterior FROM colonization_planeta_instalacoes_upgrade WHERE id_planeta_instalacoes={$id_instalacao_colonia->id} AND turno = {$turno_upgrade}");
			}				

			$this->instalacoes_planeta[$id_instalacao_colonia->id] = $id_instalacao_colonia->id_instalacao;
			$this->nivel_instalacoes_planeta[$id_instalacao_colonia->id] = $id_instalacao_colonia->nivel;
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
			$this->popula_instalacoes_colonia();
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
			$this->popula_instalacoes_colonia();
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
	function pega_alimentos_consumidos_planeta ($imperio_alimento_inospito, $imperio_limite_poluicao) {

		if (empty($this->planeta)) {
			$this->planeta = new planeta ($this->id_planeta);
		}
		
		if (!$this->planeta->popula_instalacoes_planeta) {
			$this->planeta->popula_instalacoes_planeta();
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
		if ($this->pop > $this->planeta->tamanho()*10) {
			$consumo_extra_pop = ($this->pop - $this->planeta->tamanho()*10);
		}
	
		//A poluição mais alta também aumenta o consumo de alimentos
		//$imperio->limite_poluicao
		$consumo_extra_poluicao = 0;
		if ($this->poluicao > 2*$imperio_limite_poluicao) {
			$fator_poluicao = ($this->poluicao)/(2*$imperio_limite_poluicao);
			$consumo_extra_poluicao = ceil($this->pop * (1-exp(1-$fator_poluicao)));
			
		}
	
		return $this->pop + $consumo_extra_inospito + $consumo_extra_pop + $consumo_extra_poluicao;
	}
	
	/***********************
	function pega_balanco_satisfacao()
	----------------------
	Calcula a satisfação de um planeta
	***********************/	
	function pega_balanco_satisfacao ($imperio) {
		if (empty($imperio->acoes)) {//Só faz o balanço se as Ações já tiverem sido processadas
			return 0;
		}
		//Pega as chaves das ações referentes à essa colônia
		$chaves_acoes = array_keys($imperio->acoes->id_colonia, $this->id);
		//*** TODO ***//
		//Definir fatores que afetam a Satisfação
	
	}
	
	/***********************
	function crescimento_colonia()
	----------------------
	Calcula o Crescimentos da colônia
	***********************/	
	function crescimento_colonia ($imperio, $alimentos, $balanco_alimentos) {
		global $wpdb;
		
		if (empty($imperio->acoes)) {//Só faz o balanço se as Ações já tiverem sido processadas
			return 0;
		}

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];	
			$banido = get_user_meta($user->ID, 'asgarosforum_role', true);
			if ($banido === "banned") {
				$banido = true;
			} else {
				$banido = false;
			}
		}
		
		//O aumento da população funciona assim: se houver comida sobrando DEPOIS do consumo, ela cresce em 5 por turno se pop<30, depois cresce 10 por turno até atingir (Tamanho do Planeta*10)
		//No entanto, a poluição reduz o crescimento populacional
		$nova_pop = 0;
		if (empty($this->planeta)) {
			$this->planeta = new planeta($this->id_planeta);
		}
		
		if (!$this->planeta->popula_instalacoes_planeta) {
			$this->planeta->popula_instalacoes_planeta();
		}
		
		$id_alimento = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome = 'Alimentos'");
		if ($roles == "administrator") {
			//echo "Alimentos: {$alimentos} | pop: {$this->pop} | colonia: {$this->id} | balanco_alimentos: {$balanco_alimentos}<br>";
		}		
		if ($alimentos > $this->pop && $balanco_alimentos > 0 && $this->vassalo == 0) {//Caso tenha alimentos suficientes E tenha balanço de alimentos positivo...
			if (($this->planeta->inospito == 0 && $this->planeta->terraforma == 0) || $imperio->coloniza_inospito == 1) {//Se for planeta habitável, a Pop pode crescer
				if ($this->poluicao <= $imperio->limite_poluicao) {//Se a poluição for maior que o limite de poluição do Império, a população não cresce
					$limite_pop_planeta = $this->planeta->tamanho()*10; 
					//Caso o Império tenha uma Tech de Bônus Populacional...
					if ($imperio->bonus_pop >0) {
						$limite_pop_planeta	= $limite_pop_planeta*(1+($imperio->bonus_pop/100));
						if ($this->planeta->tamanho() == 0) {//Planetas que não são planetas (i.e. destroços) não permitem o crescimento natural da Pop
							$limite_pop_planeta	= 0; 
						}									
					}
					
					$fator_cresce = 0;
					if ($this->pop <= $limite_pop_planeta) {//Tem espaço para crescer
						$fator_cresce = 0.0758*$this->pop + 3.1818;
						if ($fator_cresce < 5) {
							$fator_cresce = 5;
						} elseif ($fator_cresce > 10) {
							$fator_cresce = 10;
						}
						
						if ($this->planeta->subclasse == "Gaia") {
							$fator_cresce = $fator_cresce*2;
						}
						
						$nova_pop = $this->pop + ceil($fator_cresce*$imperio->bonus_crescimento_pop);
						if ($nova_pop > $limite_pop_planeta) {
							$nova_pop = $limite_pop_planeta;
						}
						$nova_pop = $nova_pop - $this->pop;
					}
				}
			}
		} else {
			//Caso os Alimentos sejam menores que a Pop da colônia, a população CAI em 10%
			if ($alimentos < $this->pop) {
				$nova_pop = round(0.9*$this->pop) - $this->pop;
			}
		}
		return $nova_pop;
	}
	
	/***********************
	function bonus_torpedeiros()
	----------------------
	Pega o bônus de Torpedeiros provenientes de Instalações
	***********************/	
	function bonus_torpedeiros() {
		global $wpdb;
		
		$bonus_torpedeiros = 0;
		if (empty($this->instalacoes_planeta)) {
			$this->popula_instalacoes_colonia();
		}
		
		foreach ($this->instalacoes_planeta as $id_planeta_instalacoes => $id_instalacao) {
			$instalacao = new instalacao($id_instalacao);
			if ($instalacao->torpedeiros_sistema_estelar != 0) {
				$bonus_torpedeiros = $bonus_torpedeiros + $instalacao->torpedeiros_sistema_estelar*$this->nivel_instalacoes_planeta[$id_planeta_instalacoes];
			}
		}
	
		return $bonus_torpedeiros;
	}
	
	/***********************
	function minas_subespaciais()
	----------------------
	Pega a qtd de Minas Subespaciais na colônia
	***********************/	
	function minas_subespaciais() {
		global $wpdb;
		
		$minas_subespaciais = 0;
		if (empty($this->instalacoes_planeta)) {
			$this->popula_instalacoes_colonia();
		}
		
		foreach ($this->instalacoes_planeta as $id_planeta_instalacoes => $id_instalacao) {
			$instalacao = new instalacao($id_instalacao);
			if ($instalacao->minas_subespaciais != 0) {
				$minas_subespaciais = $minas_subespaciais + $instalacao->minas_subespaciais*$this->nivel_instalacoes_planeta[$id_planeta_instalacoes];
			}
		}
	
		return $minas_subespaciais;
	}	
}
?>