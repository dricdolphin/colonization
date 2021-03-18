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
	public $id;
	public $nome;
	public $id_jogador;
	public $prestigio;
	public $pop = 0;
	public $pontuacao = 0;
	public $pontuacao_tech = 0;
	public $pontuacao_colonia = 0;
	public $pontuacao_desenvolvimento = 0;
	public $pontuacao_belica = 0;
	public $html_header;
	public $turno;
	public $acoes;
	
	//Todos esses atributos são na verdade relativos à Techs do Império e em teoria deveriam estar no objeto Imperio_Techs
	public $icones_html = "";
	public $max_pop = 0;
	public $limite_poluicao = 100;
	public $alcance_logistica = 0;
	public $bonus_alcance = 0;
	public $bonus_recurso = [];
	public $sinergia = [];
	public $extrativo = [];
	public $bonus_todos_recursos = 0;
	public $max_bonus_recurso = [];
	public $bonus_pesquisa_naves = 0;
	public $crescimento_pop = 1;
	public $sensores = 0;
	public $coloniza_inospito = 0;
	public $alimento_inospito = 0;
	
	//Atributos de defesa planetária
	public $pdf_planetario = 10;
	public $pdf_torpedo = 0;
	public $defesa_invasao = 1;
	public $torpedos_sistema_estelar = false;
	public $icone_torpedos_sistema_estelar = "";
	public $torpedeiros_sistema_estelar = false;
	public $icone_torpedeiros_sistema_estelar = "";
	
	public $debug = "";
	

	/***********************
	function __construct($id, $super=false)
	----------------------
	Inicializa os dados do Império
	$id_imperio = null -- Se não for passado um valor, o valor padrão é o id de usuário
	$super -- Define se é para forçar o objeto (ignora as proteções)
	$turno -- Qual turno deve ser exibido
	***********************/
	function __construct($id=0, $super=false, $turno=0) {
		global $wpdb;
		$this->turno = new turno($turno);
		
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}

		$this->id = $id;
		//Somente cria um objeto com ID diferente se o usuário tiver perfil de administrador
		if ($roles != "administrator" && $super == false) {
			$this->id_jogador = get_current_user_id();
			
			$this->id = $wpdb->get_var("SELECT id FROM colonization_imperio WHERE id_jogador={$this->id_jogador}");
		} 
				
		if ($this->id == "") {
			$this->id = 0;
		}

		$resultado = $wpdb->get_results("SELECT id, id_jogador, nome, prestigio FROM colonization_imperio WHERE id={$this->id}");
		
		if (empty($resultado)) {
			$this->id = 0;
			return;
		}

		$resultado = $resultado[0];
		
		$this->id = $resultado->id;
		$this->id_jogador = $resultado->id_jogador;
		$this->nome = $resultado->nome;
		$this->prestigio = $resultado->prestigio;
		
		$this->pop = $wpdb->get_var("SELECT 
		(CASE 
		WHEN SUM(pop) IS NULL THEN 0
		ELSE SUM(pop)
		END) AS pop
		FROM colonization_imperio_colonias
		WHERE id_imperio={$this->id}
		AND turno={$this->turno->turno}");
		
		//A pontuação será: No de Colonias*100 + No de Instalações x Nível x 10 + Pop + Recursos + Custo das Naves + Custo das Techs
		$pontuacao = $wpdb->get_var("SELECT COUNT(id)*100 FROM colonization_imperio_colonias WHERE id_imperio={$this->id} AND turno={$this->turno->turno}");
		$this->pontuacao = $this->pontuacao + $pontuacao;
		$this->pontuacao_colonia = $this->pontuacao_colonia + $pontuacao;
		
		$pontuacao = $wpdb->get_var("SELECT SUM(pop) FROM colonization_imperio_colonias WHERE id_imperio={$this->id} AND turno={$this->turno->turno}");
		$this->pontuacao = $this->pontuacao + $pontuacao;
		$this->pontuacao_colonia = $this->pontuacao_colonia + $pontuacao;

		$pontuacao = $wpdb->get_var("SELECT SUM(cpi.nivel)*10 
		FROM colonization_planeta_instalacoes AS cpi
		JOIN colonization_imperio_colonias  AS cic
		ON cic.id_planeta = cpi.id_planeta
		WHERE cic.id_imperio={$this->id}
		AND cic.turno = {$this->turno->turno}");
		$this->pontuacao = $this->pontuacao + $pontuacao;
		$this->pontuacao_desenvolvimento = $this->pontuacao_desenvolvimento + $pontuacao;

		$pontuacao = $wpdb->get_var("SELECT SUM(qtd) FROM colonization_imperio_recursos WHERE id_imperio={$this->id} AND turno={$this->turno->turno}");
		$this->pontuacao = $this->pontuacao + $pontuacao;
		$this->pontuacao_desenvolvimento = $this->pontuacao_desenvolvimento + $pontuacao;
		
		$pontuacao = $wpdb->get_var("SELECT SUM(qtd*(tamanho*2 + PDF_laser + PDF_projetil + PDF_torpedo + blindagem + escudos + pesquisa + FLOOR(alcance/1.8))) AS pontuacao FROM colonization_imperio_frota WHERE id_imperio={$this->id} AND turno<={$this->turno->turno} AND turno_destruido<{$this->turno->turno}");
		$this->pontuacao = $this->pontuacao + $pontuacao;
		$this->pontuacao_belica = $this->pontuacao_belica + $pontuacao;

		$pontuacao = $wpdb->get_var("SELECT SUM(custo) 
		FROM
		(SELECT (CASE WHEN cit.custo_pago > 0 THEN cit.custo_pago ELSE ct.custo END)*ct.nivel AS custo
		FROM colonization_imperio_techs AS cit
		JOIN colonization_tech AS ct
		ON ct.id=cit.id_tech
		WHERE cit.id_imperio={$this->id}
		AND cit.turno <= {$this->turno->turno}
		) AS custo_tech");
		$this->pontuacao = $this->pontuacao + $pontuacao;
		$this->pontuacao_tech = $this->pontuacao_tech + $pontuacao;

		//***********************************
		// ALTERAÇÕES DE TECH (ESPECIAIS)
		//***********************************
		//No momento existem as seguintes funções especiais para Techs:
		//max_pop=porcentagem -- tech que aumenta o máximo de pop de uma colônia
		//crescimento_pop=velocidade -- tech que aumenta a velocidade de crescimento da Pop
		//logistica=alcance -- tech que permite criar Instalações e Colônias à uma determinada distância
		//bonus_logistica=bonus -- bônus de alcance
		//limite_poluicao=porcentagem -- tech que aumenta o limite de poluição aceitável
		//sensores=nivel -- nível das Techs de Sensores
		//bonus_pesquisa_naves=valor -- tech que dá bonus nas pesquisas das naves
		//coloniza_inospito=valor -- Pode colonizar planetas inóspitos
		//alimento_inospito=valor -- Consome recursos a mais em planetas inóspitos
		//
		//produz_recurso=porcentagem -- tech que dá um bônus em algum recurso
		//Essa Tech pode ter os seguintes atributos
		//id_recurso=recursos -- pode ser uma lista separada por vírgula ou *, para todos os recursos => OBRIGATÓRIO
		//extrativo -- se aplica apenas à recursos com o atributo extrativo
		//sinergia -- se aplica apenas se houver mais de uma instalação que produza o recurso
		//max_bonus_recurso=qtd_total -- o bônus tem um limite máximo (unitário)
		
		$especiais_lista = $wpdb->get_results("SELECT ct.id AS id, ct.especiais AS especiais
		FROM colonization_imperio_techs AS cit
		JOIN colonization_tech AS ct
		ON ct.id = cit.id_tech
		WHERE cit.id_imperio={$this->id} 
		AND cit.custo_pago = 0
		AND ct.especiais != ''
		AND turno <= {$this->turno->turno}");
		
		foreach ($especiais_lista AS $id) {
			$especiais = explode(";",$id->especiais);
			
			//Especiais -- max_pop
			$max_pop = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'max_pop') !== false;
			}));
			
			if (!empty($max_pop)) {
				$max_pop_valor = explode("=",$max_pop[0]);
				$this->max_pop = $this->max_pop	+ $max_pop_valor[1];
			}
			
			//Especiais -- crescimento_pop
			$crescimento_pop = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'crescimento_pop') !== false;
			}));
			
			if (!empty($crescimento_pop)) {
				$crescimento_pop_valor = explode("=",$crescimento_pop[0]);
				$this->crescimento_pop = $this->crescimento_pop	+ $crescimento_pop_valor[1];
			}
			
			//Especiais -- sensores
			$sensores = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'sensores') !== false;
			}));
			
			if (!empty($sensores)) {
				$sensores_valor = explode("=",$sensores[0]);
				$this->sensores = $this->sensores	+ $sensores_valor[1];
			}			
			
			//Especiais -- logistica
			$logistica = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'logistica') !== false;
			}));
			
			if (!empty($logistica)) {
				$logistica_valor = explode("=",$logistica[0]);
				if ($this->alcance_logistica < $logistica_valor[1]) {
					$this->alcance_logistica = $logistica_valor[1];
				}
			}
			
			//Especiais -- bonus_alcance
			$bonus_alcance = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'bonus_alcance') !== false;
			}));
			
			if (!empty($bonus_alcance)) {
				$bonus_alcance_valor = explode("=",$bonus_alcance[0]);
				if ($this->bonus_alcance < $bonus_alcance_valor[1]) {
					$this->bonus_alcance = $bonus_alcance_valor[1];
				}
			}			
			
			//Especiais -- bonus_logistica
			$bonus_logistica = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'bonus_logistica') !== false;
			}));
			
			if (!empty($bonus_logistica)) {
				$bonus_logistica_valor = explode("=",$bonus_logistica[0]);
				$this->alcance_logistica = $this->alcance_logistica + $bonus_logistica_valor[1];
			}

			//Especiais -- limite_poluicao
			$limite_poluicao = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'limite_poluicao') !== false;
			}));
			
			if (!empty($limite_poluicao)) {
				$limite_poluicao_valor = explode("=",$limite_poluicao[0]);
				$this->limite_poluicao = $this->limite_poluicao + $limite_poluicao_valor[1];
			}
			
			//Especiais -- bonus_pesquisa_naves
			$bonus_pesquisa_naves = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'bonus_pesquisa_naves') !== false;
			}));
			
			if (!empty($bonus_pesquisa_naves)) {
				$bonus_pesquisa_naves = explode("=",$bonus_pesquisa_naves[0]);
				$this->bonus_pesquisa_naves = $this->bonus_pesquisa_naves + $bonus_pesquisa_naves[1];
			}			
			
			//Especiais -- coloniza_inospito
			$coloniza_inospito = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'coloniza_inospito') !== false;
			}));
			
			if (!empty($coloniza_inospito)) {
				$coloniza_inospito_valor = explode("=",$coloniza_inospito[0]);
				$this->coloniza_inospito = $coloniza_inospito_valor[1];
			}

			//Especiais -- alimento_inospito
			$alimento_inospito = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'alimento_inospito') !== false;
			}));
			
			if (!empty($alimento_inospito)) {
				$alimento_inospito_valor = explode("=",$alimento_inospito[0]);
				$this->alimento_inospito = $alimento_inospito_valor[1];
			}			
			
			//Especiais -- produz_recursos
			$bonus_recurso = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'produz_recurso') !== false;
			}));
			
			if (!empty($bonus_recurso)) {
				$bonus_recurso = explode("=",$bonus_recurso[0]);
				$bonus_recurso = $bonus_recurso[1];

				//Sempre tem um id_recurso
				$id_recurso = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'id_recurso') !== false;
				}));
				
				$id_recurso = explode("=",$id_recurso[0]);
				$id_recurso = explode(",",$id_recurso[1]);
				
				//Atributos opcionais
				$sinergia = array_search("sinergia",$especiais);
				if ($sinergia !== false) {
					$sinergia = true;
				}
				
				$extrativo = array_search("extrativo",$especiais);
				if ($extrativo !== false) {
					$extrativo = true;
				}
				
				$max_bonus_recurso = array_values(array_filter($especiais, function($value) {
					return strpos($value, 'max_bonus_recurso') !== false;
				}));
				
				if (!empty($max_bonus_recurso)) {
					$max_bonus_recurso = explode("=",$max_bonus_recurso[0]);
					$max_bonus_recurso = $max_bonus_recurso[1];
				} else {
					$max_bonus_recurso = false;
				}


				//Popula o array
				foreach ($id_recurso as $chave => $valor) {
					if (empty($this->bonus_recurso[$valor])) {
						$this->bonus_recurso[$valor] = $bonus_recurso;
					} else {
						$this->bonus_recurso[$valor] = $this->bonus_recurso[$valor]+$bonus_recurso;
					}
					$this->sinergia[$valor] = $sinergia;
					$this->extrativo[$valor] = $extrativo;
					
					if ($valor == "*" && !$extrativo) {
						$this->bonus_todos_recursos = 1;
					}
					
					if (empty($this->max_bonus_recurso[$valor])) {
						$this->max_bonus_recurso[$valor] = 0;
					}
					
					if (!$max_bonus_recurso || !$this->max_bonus_recurso[$valor]) {//Se alguma Tech tem bônus SEM restrições, então NÃO HÁ RESTRIÇÕES
						$this->max_bonus_recurso[$valor] = $max_bonus_recurso;	
					} else {
						if (!empty($this->max_bonus_recurso[$valor])) { //O max_bônus vai crescendo conforme há outras Techs com max_bonus
							$this->max_bonus_recurso[$valor] = $this->max_bonus_recurso[$valor] + $max_bonus_recurso;
						} else {
							$this->max_bonus_recurso[$valor] = $max_bonus_recurso;
						}
					}
				}
			}
		
			//Atributos de defesa planetária
			//Especiais -- pdf_planetario
			$pdf_planetario = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'pdf_planetario') !== false;
			}));
			
			if (!empty($pdf_planetario)) {
				$pdf_planetario_valor = explode("=",$pdf_planetario[0]);
				$this->pdf_planetario = $this->pdf_planetario	+ $pdf_planetario_valor[1];
			}

			//Especiais -- pdf_torpedo
			$pdf_torpedo = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'pdf_torpedo') !== false;
			}));
			
			if (!empty($pdf_torpedo)) {
				$pdf_torpedo_valor = explode("=",$pdf_torpedo[0]);
				$this->pdf_torpedo = $this->pdf_torpedo	+ $pdf_torpedo_valor[1];
			}			

			//Especiais -- defesa_invasao
			$defesa_invasao = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'defesa_invasao') !== false;
			}));
			
			if (!empty($defesa_invasao)) {
				$defesa_invasao_valor = explode("=",$defesa_invasao[0]);
				$this->defesa_invasao = $this->defesa_invasao	+ $defesa_invasao_valor[1];
			}
			
			//Especiais -- torpedos_sistema_estelar
			$torpedos_sistema_estelar = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'torpedos_sistema_estelar') !== false;
			}));
			
			if (!empty($torpedos_sistema_estelar)) {
				$this->torpedos_sistema_estelar = true;
				$tech_torpededos = new tech ($id->id);
				$this->icone_torpedos_sistema_estelar = " <div class='{$tech_torpededos->icone} tooltip'><span class='tooltiptext'>{$tech_torpededos->nome}</span></div>";				
			}
			
			//Especiais -- torpedeiros_sistema_estelar			
			$torpedeiros_sistema_estelar = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'torpedeiros_sistema_estelar') !== false;
			}));
			
			if (!empty($torpedeiros_sistema_estelar)) {
				$torpedeiros_sistema_estelar_valor = explode("=",$torpedeiros_sistema_estelar[0]);
				$this->torpedeiros_sistema_estelar = $torpedeiros_sistema_estelar_valor[1];
				$tech_torpedeiro = new tech ($id->id);
				$this->icone_torpedeiros_sistema_estelar = " <div class='{$tech_torpedeiro->icone} tooltip'><span class='tooltiptext'>{$tech_torpedeiro->nome}</span></div>";
			}

		}

		//Algumas Techs tem ícones, que devem ser mostrados do lado do nome do jogador
		$tech = new tech();
		$icones = $tech->query_tech(" AND ct.icone != ''", $this->id);

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}

		$icone_html = [];
		
		foreach ($icones AS $icone) {
			$tech_icone = new tech($icone->id);
			
			if ($icone->custo_pago == 0) {
				$mostra_logistica = "";
				if (strpos($tech_icone->especiais,"logistica") !== false && strpos($tech_icone->especiais,"bonus_logistica") === false) {
					$mostra_logistica = " ".$this->alcance_logistica."pc";
				}
				if ($tech_icone->id_tech_parent != 0) {
					$tech_parent = new tech ($tech_icone->id_tech_parent);
					$icone_colocado = false;
					do {
						if (!empty($icone_html[$tech_parent->id])) {
							$icone_html[$tech_parent->id] = " <div class='{$tech_icone->icone} tooltip'>{$mostra_logistica}<span class='tooltiptext'>{$tech_icone->nome}</span></div>";
							$icone_colocado = true;
							break;
						} 
						$tech_parent = new tech ($tech_parent->id_tech_parent);
					} while ($tech_parent->id != 0);
					if (!$icone_colocado) {
						$icone_html[$tech_icone->id] = " <div class='{$tech_icone->icone} tooltip'>{$mostra_logistica}<span class='tooltiptext'>{$tech_icone->nome}</span></div>";
					}
				} else {
					$icone_html[$tech_icone->id] = " <div class='{$tech_icone->icone} tooltip'>{$mostra_logistica}<span class='tooltiptext'>{$tech_icone->nome}</span></div>";
				}
				//$this->icones_html .= " <div class='{$tech_icone->icone} tooltip'><span class='tooltiptext'>{$tech_icone->nome}</span></div>";
			}
		}
		
		foreach ($icone_html as $chave => $html) {
			$this->icones_html .= $html;
		}

		
		if ($this->max_pop >0) {
			$this->icones_html .= " <div class='fas fa-user-plus tooltip'>{$this->max_pop}%<span class='tooltiptext'>Bônus de população</span></div>";
		}
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
				<div data-atributo='ID'>{$this->id}</div>
				<div data-atributo='gerenciar'><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a></div>
			</td>
			<td><div data-atributo='nome_jogador'>{$user->display_name}{$this->icones_html}</div></td>
			<td><div data-atributo='nome' data-valor-original='{$this->nome}' data-editavel='true'>{$this->nome}</div></td>
			<td><div data-atributo='prestigio' data-valor-original='{$this->prestigio}' data-editavel='true' data-style='width: 40px;'>{$this->prestigio}</div></td>
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

		if ($this->id == 0) {
			return;
		}		
		
		$total_colonias = $wpdb->get_var("SELECT COUNT(id) FROM colonization_imperio_colonias WHERE id_imperio={$this->id} AND turno={$this->turno->turno}");
		//Exibe os dados básicos do Império
		$html = "<div>
		<h3>{$this->nome}</h3> 
		<b>População:</b> {$this->pop} - <b>Total de Colônias:</b> {$total_colonias}<br>
		<b>Pontuação:</b> {$this->pontuacao}<br>
		Desenvolvimento: {$this->pontuacao_desenvolvimento}<br>			
		Colônias: {$this->pontuacao_colonia}<br>
		Techs: {$this->pontuacao_tech}<br>
		Bélica: {$this->pontuacao_belica}<br>		
		</div>";
		return $html;
	}

	/***********************
	function imperio_exibe_colonias_imperio()
	----------------------
	Exibe os dados do Império
	***********************/
	function imperio_exibe_colonias_imperio() {
		global $wpdb;
		
		if ($this->id == 0) {
			return;
		}

		$id_estrelas_imperio = $wpdb->get_results("
		SELECT DISTINCT ce.id
		FROM colonization_imperio_colonias AS cic
		JOIN colonization_planeta AS cp
		ON cp.id = cic.id_planeta
		JOIN colonization_estrela AS ce
		ON ce.id = cp.id_estrela
		WHERE cic.id_imperio = {$this->id} 
		AND cic.turno = {$this->turno->turno}
		ORDER BY cic.capital DESC, ce.X, ce.Y, ce.Z
		");

		$resultados = [];
		foreach ($id_estrelas_imperio as $id_estrela) {
			$resultados_temp =$wpdb->get_results("
			SELECT cic.id 
			FROM colonization_imperio_colonias AS cic
			JOIN colonization_planeta AS cp
			ON cp.id = cic.id_planeta
			JOIN colonization_estrela AS ce
			ON ce.id = cp.id_estrela
			WHERE cic.id_imperio={$this->id} AND cic.turno={$this->turno->turno}
			AND ce.id={$id_estrela->id}
			ORDER BY cic.capital DESC, cp.posicao
			");
			
			$resultados = array_merge($resultados,$resultados_temp);
		}
		
		$html = $this->html_header;
		
		$html .= "<table class='wp-list-table widefat fixed striped users'>
		<thead>
		<tr><th style='width: 22%;'>Estrela (X;Y;Z;P)</th><th style='width: 23%;'>Planeta</th><th style='width: 30%;'>Defesas</th><th style='width: 10%;'>Pop.</th><th style='width: 15%;'>Poluição</th></tr>
		</thead>
		<tbody>
		";
		
		foreach ($resultados as $id) {
			$colonia = new colonia($id->id);
			$planeta = new planeta($colonia->id_planeta);
			$estrela = new estrela($planeta->id_estrela);

			$html_pop_colonia = "{$colonia->pop}";
			if ($colonia->pop_robotica > 0) {
				$html_pop_colonia .= "(<div class='fas fa-users-cog tooltip'>&nbsp;<span class='tooltiptext'>População Robótica</span></div>{$colonia->pop_robotica})";
			}
			
			$html_defesas = "";
			if (!empty($planeta->escudos)) {
				$html_defesas .= $planeta->escudos."<br>";
			}
			
			if ($colonia->qtd_defesas > 0) {
				$colonia->pdf_planetario = round(($this->pdf_planetario*$colonia->qtd_defesas/10),0,PHP_ROUND_HALF_DOWN);
				$colonia->defesa_invasao = $this->defesa_invasao*$colonia->qtd_defesas;

				$html_defesas .= "PdF Planetário: {$colonia->pdf_planetario}<br>Defesa Invasão: {$colonia->defesa_invasao}";
				
				if ($this->torpedos_sistema_estelar) {
					$html_defesas .= "<br>{$this->icone_torpedos_sistema_estelar} x{$colonia->qtd_defesas} (Pdf: {$this->pdf_torpedo})";
				}
				
				if ($this->torpedeiros_sistema_estelar !== false) {

					switch($this->torpedeiros_sistema_estelar) {
						case 1:
							$nivel = "Mk I";
							break;
						case 2:
							$nivel = "Mk II";
							break;					
						default:
							$nivel = "";
					}

					$html_defesas .= "<br>{$this->icone_torpedeiros_sistema_estelar} {$nivel} x{$colonia->qtd_defesas} ";
				}
			}
			
			if ($html_defesas != "") {
				$html_defesas .= "<br>";
			}
			
			foreach ($planeta->html_instalacao_ataque as $chave => $html_instalacao) {
				$html_defesas .= $html_instalacao;
			}
			
			if ($html_defesas == "") {
				$html_defesas = "&nbsp;";
			}
			
			$html .= "<tr>
			<td>{$estrela->nome} ({$estrela->X};{$estrela->Y};{$estrela->Z};{$planeta->posicao})</td>
			<td>{$colonia->icone_capital}{$planeta->nome}&nbsp;{$planeta->icone_habitavel}</td>
			<td>{$html_defesas}</td>
			<td>{$colonia->html_pop_colonia}</td><td>{$colonia->poluicao}</td>
			</tr>";
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
	function exibe_recursos_atuais($icones = false) {
		global $wpdb;

		if ($this->id == 0) {
			return;
		}
		
		$resultados = $wpdb->get_results("
		SELECT cir.qtd, cr.nome, cr.descricao, cr.icone
		FROM colonization_imperio_recursos AS cir
		JOIN colonization_recurso AS cr
		ON cr.id=cir.id_recurso
		WHERE cir.id_imperio = {$this->id} AND turno={$this->turno->turno}
		AND cr.acumulavel = true
		AND cir.disponivel = true
		ORDER BY cr.nivel, cr.extrativo, cr.nome
		");
		
		$html = "<b>Recursos atuais:</b> ";
		foreach ($resultados as $resultado) {
			$nome_recurso = $resultado->nome;
			$nome_tooltip = "";
			if ($icones) {
				if ($resultado->icone != "") {
					$nome_recurso = "<div class='{$resultado->icone}'></div>";
					$nome_tooltip = "{$resultado->nome}: ";
				}
			}
			$html .= "<div class='tooltip' style='display: inline-block;'>{$nome_recurso} - {$resultado->qtd}; &nbsp;
						<span class='tooltiptext'>{$nome_tooltip}{$resultado->descricao}</span>
					</div>";

			//$html .= "{$resultado->nome} - {$resultado->qtd}; ";
		}
		
		return $html;
	}
	
	
	/***********************
	function pop_mdo_sistema()
	----------------------
	Exibe o MdO de um Sistema Estelar que o Império controla
	***********************
	function pop_mdo_sistema ($id_estrela) {
		global $wpdb;
		
		$resultados = $wpdb->get_results("
		SELECT cic.id AS id_colonia
		FROM colonization_imperio_colonias AS cic
		JOIN colonization_planeta AS cp
		ON cp.id=cic.id_planeta
		JOIN colonization_estrela AS ce
		ON ce.id=cp.id_estrela
		WHERE cic.id_imperio = {$this->id}
		AND cic.turno = {$this->turno->turno}
		AND cp.id_estrela={$id_estrela}
		ORDER BY cic.capital DESC, ce.X, ce.Y, ce.Z, cp.posicao, cic.id_planeta
		");

		if (!empty($resultados)) {
			//$imperio = new imperio($this->id, false, $this->turno->turno);
			if (empty($this->acoes)) {
				$this->acoes = new acoes($this->id, $this->turno->turno);
			}
			//$acoes = new acoes($this->id, $this->turno->turno);
		} else {
			$resposta = [];
			$resposta['pop'] = 0;
			$resposta['mdo'] = 0;

			return $resposta;
		}

		foreach ($resultados as $resultado) {
			$colonia = new colonia ($resultado->id_colonia);
			$planeta = $colonia->planeta;
			$estrela = $colonia->estrela;

			$mdo = $this->acoes->mdo_planeta($planeta->id);
			
			if (empty($mdo_sistema)) {
				$mdo_sistema = $mdo;
				$pop_sistema = $colonia->pop + $colonia->pop_robotica;
			} else {
				$mdo_sistema = $mdo_sistema + $mdo;
				$pop_sistema = $pop_sistema + $colonia->pop + $colonia->pop_robotica;
			}
		}

		$resposta = [];
		$resposta['pop'] = $pop_sistema;
		$resposta['mdo'] = $mdo_sistema;
		
		return $resposta;
	}
	//***********************/	

	
	/***********************
	function exibe_lista_colonias()
	----------------------
	Exibe as Colônias atuais Império
	
	$id_colonia -- se diferente de zero, processa uma colônia específica que tenha sido modificada
	$salva_lista -- salva os dados da lista (após um reprocessamento)
	***********************/
	function exibe_lista_colonias($id_colonia=array(0,0), $salva_lista=false) {
		global $wpdb, $start_time;
		
		if ($this->id == 0) {
			return;
		}

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}

		
		$this->debug = "";
			$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
			$this->debug .= "imperio->exibe_lista_colonias:  {$diferenca}ms \n";
		$id_estrelas_imperio = $wpdb->get_results("
		SELECT DISTINCT ce.id
		FROM colonization_imperio_colonias AS cic
		JOIN colonization_planeta AS cp
		ON cp.id = cic.id_planeta
		JOIN colonization_estrela AS ce
		ON ce.id = cp.id_estrela
		WHERE cic.id_imperio = {$this->id} 
		AND cic.turno = {$this->turno->turno}
		ORDER BY cic.capital DESC, cic.vassalo ASC, ce.X, ce.Y, ce.Z
		");
			$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
			$this->debug .= "imperio->exibe_lista_colonias -> Query {$diferenca}ms \n";

		$resultados = [];
		foreach ($id_estrelas_imperio as $id_estrela) {
			$resultados_temp =$wpdb->get_results("
			SELECT cic.id 
			FROM colonization_imperio_colonias AS cic
			JOIN colonization_planeta AS cp
			ON cp.id = cic.id_planeta
			JOIN colonization_estrela AS ce
			ON ce.id = cp.id_estrela
			WHERE cic.id_imperio={$this->id} AND cic.turno={$this->turno->turno}
			AND ce.id={$id_estrela->id}
			ORDER BY cic.capital DESC, cic.vassalo ASC, cp.posicao
			");
			
			$resultados = array_merge($resultados,$resultados_temp);
		}
			$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
			$this->debug .= "imperio->exibe_lista_colonias -> foreach ordena estrelas {$diferenca}ms \n";	

		$html_lista = "<b>Lista de Colônias</b><br>";
		$html_planeta = [];
		$planeta_id_estrela = [];
		$html_sistema = [];
		$qtd_defesas_sistema = [];
		$mdo_sistema = [];
		$pop_sistema = [];

		if (!empty($resultados)) {
			//$imperio = new imperio($this->id, false, $this->turno->turno);
			if (empty($this->acoes)) {
				$sem_balanco = true;
				$this->acoes = new acoes($this->id, $this->turno->turno, $sem_balanco);
					$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
					$this->debug .= "imperio->exibe_lista_colonias -> new Ações {$diferenca}ms \n";
			}
		}
		
		//Para agilizar o processamento, salvamos os dados no DB e só processamos todos os balanços quando necessário
		//$wpdb->query("DELETE FROM colonization_lista_colonias_turno WHERE id_imperio = {$this->id} AND turno = {$this->turno->turno}");
		$lista_colonias_db = stripslashes(str_replace(array("\\n", "\\r", "\\t"), "", $wpdb->get_var("SELECT json_balancos FROM colonization_lista_colonias_turno WHERE id_imperio = {$this->id} AND turno = {$this->turno->turno}")));
			if ($roles == "administrator") {
				//TODO -- Debug
			}
	
		$mdo = 0;
		$estrela = [];
		$planeta = [];
		$colonia = [];
		$instalacao = [];

		$flag_nova_lista = true;
		if (empty($lista_colonias_db)) {
			$html_planeta = [];
			$planeta_id_estrela = [];
			$mdo_sistema = [];
			$pop_sistema = [];
			$qtd_defesas_sistema = [];
		} else {
			$flag_nova_lista = false;
			$lista_colonias_db = json_decode($lista_colonias_db, true, 512, JSON_UNESCAPED_UNICODE);
			
			$html_planeta_temp = $lista_colonias_db['html_planeta'];
			$planeta_id_estrela = $lista_colonias_db['planeta_id_estrela'];
			$mdo_sistema = $lista_colonias_db['mdo_sistema'];
			$pop_sistema = $lista_colonias_db['pop_sistema'];
			$qtd_defesas_sistema = $lista_colonias_db['qtd_defesas_sistema'];
			
			if (!empty($id_colonia)) {
				if (!empty($id_colonia[0])) {
					$colonia[$id_colonia[0]] = new colonia($id_colonia[0]);
					$estrela[$colonia[$id_colonia[0]]->id_estrela] = new estrela($colonia[$id_colonia[0]]->id_estrela);
					$mdo_sistema[$colonia[$id_colonia[0]]->id_estrela] = "";
				}
				
				if (!empty($id_colonia[1])) {
					$colonia[$id_colonia[1]] = new colonia($id_colonia[1]);
					$estrela[$colonia[$id_colonia[1]]->id_estrela] = new estrela($colonia[$id_colonia[1]]->id_estrela);
					$mdo_sistema[$colonia[$id_colonia[1]]->id_estrela] = "";
				}				
			}
			
			foreach ($html_planeta_temp as $chave => $html_do_planeta) {
				$html_planeta[$chave] = html_entity_decode($html_do_planeta, ENT_QUOTES);
			}
		}
		


		foreach ($resultados as $resultado) {
			if (!$flag_nova_lista && $id_colonia[0] != $resultado->id && $id_colonia[1] != $resultado->id) { //Só processa se houve alguma alteração
				$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
				$this->debug .= "imperio->exibe_lista_colonias -> foreach() pulando Colônia({$resultado->id}) {$diferenca}ms \n";
				continue;
			}
			
			if (empty($colonia[$resultado->id])) {
				$colonia[$resultado->id] = new colonia($resultado->id);
				$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
				$this->debug .= "imperio->exibe_lista_colonias -> foreach() new Colonia {$diferenca}ms \n";
			}
			
			if (empty($planeta[$colonia[$resultado->id]->id_planeta])) {
				$planeta[$colonia[$resultado->id]->id_planeta] = new planeta($colonia[$resultado->id]->id_planeta);
				$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
				$this->debug .= "imperio->exibe_lista_colonias -> foreach() new Planeta {$diferenca}ms \n";				
			}
			if (empty($estrela[$colonia[$resultado->id]->id_estrela])) {
				$estrela[$colonia[$resultado->id]->id_estrela] = new estrela($colonia[$resultado->id]->id_estrela);
				$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
				$this->debug .= "imperio->exibe_lista_colonias -> foreach() new Estrela {$diferenca}ms \n";				
			}
			$planeta_id_estrela[$colonia[$resultado->id]->id_planeta] = $colonia[$resultado->id]->id_estrela;
			

			if (empty($mdo_sistema[$planeta_id_estrela[$colonia[$resultado->id]->id_planeta]])) {
				$pop_mdo_sistema = $this->acoes->pop_mdo_sistema($colonia[$resultado->id]->id_estrela);
				$mdo_sistema[$planeta_id_estrela[$colonia[$resultado->id]->id_planeta]] = $pop_mdo_sistema['mdo'];
				$pop_sistema[$planeta_id_estrela[$colonia[$resultado->id]->id_planeta]] = $pop_mdo_sistema['pop'];
				$qtd_defesas_sistema[$planeta_id_estrela[$colonia[$resultado->id]->id_planeta]] = $this->acoes->defesa_sistema($colonia[$resultado->id]->id_estrela);
			}
			
			if ($colonia[$resultado->id]->poluicao < round($this->limite_poluicao*0.33,0)) {
				$poluicao = "<span style='color: #007426;'>{$colonia[$resultado->id]->poluicao}</span>";
			} elseif ($colonia[$resultado->id]->poluicao < round($this->limite_poluicao*0.66,0)) {
				$poluicao = "<span style='color: #ffce00;'>{$colonia[$resultado->id]->poluicao}</span>";
			} elseif ($colonia[$resultado->id]->poluicao < $this->limite_poluicao) {				
				$poluicao = "<span style='color: #f1711d;'>{$colonia[$resultado->id]->poluicao}</span>";
			} else {
				$poluicao = "<span style='color: #ee1509;'>{$colonia[$resultado->id]->poluicao}</span>";
			}
				$id_instalacoes = $wpdb->get_results(
				"SELECT cpi.id_instalacao 
				FROM colonization_planeta_instalacoes AS cpi 
				JOIN colonization_instalacao AS ci
				ON ci.id = cpi.id_instalacao
				WHERE cpi.id_planeta = {$colonia[$resultado->id]->id_planeta} AND cpi.turno <= {$this->turno->turno}
				ORDER BY ci.nome");
				
				$icones_planeta = [];
				$qtd_instalacao_icone = [];
				foreach ($id_instalacoes as $id_instalacao) {
					if (empty($instalacao[$id_instalacao->id_instalacao])) {
						$instalacao[$id_instalacao->id_instalacao] = new instalacao($id_instalacao->id_instalacao);
						$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
						$this->debug .= "imperio->exibe_lista_colonias -> foreach() new Instalacao {$diferenca}ms \n";
					}
					
					
					if (!empty($instalacao[$id_instalacao->id_instalacao]->icone)) {
						if (empty($icones_planeta[$instalacao[$id_instalacao->id_instalacao]->id])) {
							$qtd_instalacao_icone[$instalacao[$id_instalacao->id_instalacao]->id] = 1;
							$icones_planeta[$instalacao[$id_instalacao->id_instalacao]->id] = " <div class='{$instalacao[$id_instalacao->id_instalacao]->icone} tooltip'><span class='tooltiptext'>{$instalacao[$id_instalacao->id_instalacao]->nome}</span></div>";
						} else {
							$qtd_instalacao_icone[$instalacao[$id_instalacao->id_instalacao]->id]++;
							$icones_planeta[$instalacao[$id_instalacao->id_instalacao]->id] = " <div class='{$instalacao[$id_instalacao->id_instalacao]->icone} tooltip'>x{$qtd_instalacao_icone[$instalacao[$id_instalacao->id_instalacao]->id]}<span class='tooltiptext'>{$instalacao[$id_instalacao->id_instalacao]->nome}</span></div>";
						}
					}
				}
				
				$planeta_id_estrela[$colonia[$resultado->id]->id_planeta] = $colonia[$resultado->id]->id_estrela;
				$pop_colonia = $colonia[$resultado->id]->pop + $colonia[$resultado->id]->pop_robotica;
				
				$mdo = $this->acoes->mdo_planeta($colonia[$resultado->id]->id_planeta);
				
				$id_poluicao = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome = 'Poluição'");
				$balanco_poluicao_planeta = "";
				if (!empty($this->acoes->recursos_balanco_planeta[$id_poluicao][$colonia[$resultado->id]->id_planeta])) {
					if ($this->acoes->recursos_balanco_planeta[$id_poluicao][$colonia[$resultado->id]->id_planeta] > 0) {
						$balanco_poluicao_planeta = "(<span style='color: red;'>+{$this->acoes->recursos_balanco_planeta[$id_poluicao][$colonia[$resultado->id]->id_planeta]}</span>)";
					} else {
						$balanco_poluicao_planeta = "(<span style='color: green;'>{$this->acoes->recursos_balanco_planeta[$id_poluicao][$colonia[$resultado->id]->id_planeta]}</span>)";
					}
				}
				
				$html_icones_planeta = "";
				foreach ($icones_planeta as $id_instalacao => $html) {
					$html_icones_planeta .= $html;
				}
				
				$mdo_disponivel_sistema = $pop_sistema[$planeta_id_estrela[$colonia[$resultado->id]->id_planeta]] - $mdo_sistema[$planeta_id_estrela[$colonia[$resultado->id]->id_planeta]];
				$html_transfere_pop = "";
				$mdo_disponivel_planeta = $colonia[$resultado->id]->pop - $mdo;
				if ($mdo_disponivel_planeta > $mdo_disponivel_sistema) {
					$mdo_disponivel_planeta = $mdo_disponivel_sistema;
				}
				$html_lista_planetas = "";
				$lista_options_colonias = "";
				if ($mdo_disponivel_sistema > 0 && $mdo_disponivel_planeta > 0 && $this->turno->bloqueado == 1 && ($colonia[$resultado->id]->vassalo == 0 || ($colonia[$resultado->id]->vassalo == 1 && $roles == "administrator"))) {
					$ids_colonias = $wpdb->get_results("SELECT id FROM colonization_imperio_colonias WHERE id_imperio={$this->id} AND turno={$this->turno->turno} AND id != {$resultado->id}");
					foreach ($resultados as $id_colonia_imperio) {
						if (empty($colonia[$id_colonia_imperio->id])) {
							$colonia[$id_colonia_imperio->id] = new colonia($id_colonia_imperio->id);
							$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
							$this->debug .= "imperio->exibe_lista_colonias -> foreach() new Colonia {$diferenca}ms \n";
						}
						
						if (empty($planeta[$colonia[$id_colonia_imperio->id]->id_planeta])) {
							$planeta[$colonia[$id_colonia_imperio->id]->id_planeta] = new planeta($colonia[$id_colonia_imperio->id]->id_planeta);
							$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
							$this->debug .= "imperio->exibe_lista_colonias -> foreach() new Planeta {$diferenca}ms \n";
						}
						if ($id_colonia_imperio->id != $resultado->id && ($colonia[$id_colonia_imperio->id]->vassalo == 0 || ($colonia[$id_colonia_imperio->id]->vassalo == 1 && $roles == "administrator"))) {
								$lista_options_colonias .= "<option data-atributo='id_colonia' value='{$id_colonia_imperio->id}'>{$planeta[$colonia[$id_colonia_imperio->id]->id_planeta]->nome}</option> \n";
						}
					}

					$html_lista_planetas = "<b>Transferir</b> <input data-atributo='pop' data-ajax='true' data-valor-original='1' type='range' min='1' max='{$mdo_disponivel_planeta}' value='1' oninput='return altera_pop_transfere(event, this);' style='width: 80px;'></input>&nbsp;&nbsp;&nbsp;<label data-atributo='pop' style='width: 30px;'>1</label>
					&nbsp; <b>Pop para</b> &nbsp; 
					<select class='select_lista_planetas'>
					{$lista_options_colonias}
					</select> &nbsp; <a href='#' onclick='return transfere_pop(event,this,{$this->id},{$resultado->id},{$colonia[$resultado->id]->id_planeta},{$colonia[$resultado->id]->id_estrela});'>TRANSFERIR!</a>
					";
					$html_transfere_pop = "<div style='display: inline;'><a href='#' onclick='return mostra_div_transferencia(event, this);'><div class='fas fa-walking tooltip'><span class='tooltiptext'>Transferir Pop para outro planeta</span> ({$mdo_disponivel_planeta})</div></a>
					<div data-atributo='lista_planetas' class='div_lista_planetas'>{$html_lista_planetas}</div>
					</div>";
				}
				
				$html_planeta[$colonia[$resultado->id]->id_planeta] = "<div><span style='font-style: italic;'>{$colonia[$resultado->id]->icone_capital}{$planeta[$colonia[$resultado->id]->id_planeta]->nome}&nbsp;{$colonia[$resultado->id]->icone_vassalo}{$planeta[$colonia[$resultado->id]->id_planeta]->icone_habitavel}{$html_icones_planeta}</span> - MdO/Pop: {$mdo}/{$colonia[$resultado->id]->html_pop_colonia} - Poluição: {$poluicao} {$balanco_poluicao_planeta} {$html_transfere_pop}</div>";
		}
		$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
		$this->debug .= "imperio->exibe_lista_colonias -> foreach() Dados das Colônias {$diferenca}ms \n";

		//Salva todas as variáveis globais de balanço e produção no Banco de Dados
		
		if ($flag_nova_lista || $id_colonia[0] != 0) {
			$html_planeta_temp = [];		
			foreach ($html_planeta as $chave => $html_do_planeta) {
				$html_planeta_temp[$chave] = htmlentities($html_do_planeta, ENT_QUOTES);
			}
			$dados_html_para_salvar = [];
			$dados_html_para_salvar['html_planeta'] = $html_planeta_temp;
			$dados_html_para_salvar['planeta_id_estrela'] = $planeta_id_estrela;
			$dados_html_para_salvar['mdo_sistema'] = $mdo_sistema;
			$dados_html_para_salvar['pop_sistema'] = $pop_sistema;
			$dados_html_para_salvar['qtd_defesas_sistema'] = $qtd_defesas_sistema;

			$json_lista_colonias = addslashes(json_encode($dados_html_para_salvar, JSON_UNESCAPED_UNICODE));
			$wpdb->query("DELETE FROM colonization_lista_colonias_turno WHERE id_imperio = {$this->id} AND turno = {$this->turno->turno}");
			$wpdb->query("INSERT INTO colonization_lista_colonias_turno SET json_balancos = '{$json_lista_colonias}', id_imperio = {$this->id}, turno = {$this->turno->turno}");

			if ($roles == "administrator") {
				//TODO -- Debug
			}
		}

		foreach ($html_planeta AS $id_planeta => $html) {
			if (empty($html_sistema[$planeta_id_estrela[$id_planeta]])) {
				if (empty($estrela[$planeta_id_estrela[$id_planeta]])) {
					$estrela[$planeta_id_estrela[$id_planeta]] = new estrela($planeta_id_estrela[$id_planeta]);
				}
				
				$html_defesas_sistema = "";
				if ($qtd_defesas_sistema[$planeta_id_estrela[$id_planeta]] > 0) {
					if ($this->torpedos_sistema_estelar) {
						$html_defesas_sistema = "{$this->icone_torpedos_sistema_estelar} x{$qtd_defesas_sistema[$planeta_id_estrela[$id_planeta]]} (Pdf: {$this->pdf_torpedo})";
					}
					
					if ($this->torpedeiros_sistema_estelar !== false) {
						switch($this->torpedeiros_sistema_estelar) {
							case 1:
								$nivel = "Mk I";
								break;
							case 2:
								$nivel = "Mk II";
								break;					
							default:
								$nivel = "";
						}

						$html_defesas_sistema .= " {$this->icone_torpedeiros_sistema_estelar} {$nivel} x{$qtd_defesas_sistema[$planeta_id_estrela[$id_planeta]]}";
					}
				}

				$html_sistema[$planeta_id_estrela[$id_planeta]] = "
				<div style='margin-bottom: 5px;'><div><span style='text-decoration: underline;'>Colônias em <span style='font-weight: 600; color: #4F4F4F;'>
				{$estrela[$planeta_id_estrela[$id_planeta]]->nome} ({$estrela[$planeta_id_estrela[$id_planeta]]->X};{$estrela[$planeta_id_estrela[$id_planeta]]->Y};{$estrela[$planeta_id_estrela[$id_planeta]]->Z})</span></span> - MdO/Pop: {$mdo_sistema[$planeta_id_estrela[$id_planeta]]}/{$pop_sistema[$planeta_id_estrela[$id_planeta]]}
				{$html_defesas_sistema}
				</div>";
			}
			$html_sistema[$planeta_id_estrela[$id_planeta]] .= $html;
		}
		$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
		$this->debug .= "imperio->exibe_lista_colonias -> foreach() Ordenação do HTML {$diferenca}ms \n";
		
		foreach ($html_sistema AS $id_sistema => $html) {
			$html_lista .= $html."</div>";
		}
		
		return $html_lista;
	}
	
}
?>