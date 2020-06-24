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
	public $bonus_recurso = [];
	public $sinergia = [];
	public $extrativo = [];
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
	public $torpedeiros_sistema_estelar = false;
	
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
		if (empty($this->id)) {
			$this->id = 0;
			return;
		}		
		
		//Somente cria um objeto com ID diferente se o usuário tiver perfil de administrador
		if ($roles != "administrator" && $super == false) {
			$this->id_jogador = get_current_user_id();
			
			$this->id = $wpdb->get_var("SELECT id FROM colonization_imperio WHERE id_jogador={$this->id_jogador}");
		} 
		
		$resultado = $wpdb->get_results("SELECT id, id_jogador, nome, prestigio FROM colonization_imperio WHERE id={$this->id}");
		
		if (empty($resultado)) {
			$this->id = 0;
			return;
		}

		$resultado = $resultado[0];
		$start_time = hrtime(true);
		
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
		
		$pontuacao = $wpdb->get_var("SELECT SUM(qtd*(tamanho*2 + PDF_laser + PDF_projetil + PDF_torpedo + blindagem + escudos + pesquisa + FLOOR(alcance/1.8))) AS pontuacao FROM colonization_imperio_frota WHERE id_imperio={$this->id}");
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
		AND ct.especiais != ''");
		
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
			}
			
			//Especiais -- torpedeiros_sistema_estelar			
			$torpedeiros_sistema_estelar = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'torpedeiros_sistema_estelar') !== false;
			}));
			
			if (!empty($torpedeiros_sistema_estelar)) {
				$torpedeiros_sistema_estelar_valor = explode("=",$torpedeiros_sistema_estelar[0]);
				$this->torpedeiros_sistema_estelar = $torpedeiros_sistema_estelar_valor[1];
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

			$end_time = hrtime(true);
			$diferenca = round(($end_time - $start_time)/1000000,0);
			$this->debug  .= "
			Objeto Império: Carregando dados... {$diferenca}ms";	

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
		
		/**
		//DEBUG
		//
		$user = wp_get_current_user();
		$roles = $user->roles[0];
		
		if ($roles == "administrator") {
			var_dump($this);
			echo "<br>";
		}
		
		/**/
		
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
		
		
		$total_colonias = $wpdb->get_var("SELECT COUNT(id) FROM colonization_imperio_colonias WHERE id_imperio={$this->id} AND turno={$this->turno->turno}");
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
			
			
			$qtd_defesas = round(($colonia->pop/10),0,PHP_ROUND_HALF_DOWN);
			
			/***
			public $pdf_planetario = 1;
			public $defesa_invasao = 1;
			public $torpedos_sistema_estelar = false;
			public $torpedeiros_sistema_estelar = false;
			//***/
			
			$html_defesas = "";
			if ($qtd_defesas > 0) {
				$pdf_planetario = round(($this->pdf_planetario*$qtd_defesas/10),0,PHP_ROUND_HALF_DOWN);
				$defesa_invasao = $this->defesa_invasao*$qtd_defesas;
				$html_defesas = "PdF Planetário: {$pdf_planetario}<br>Defesa Invasão: {$defesa_invasao}";
				
				if ($this->torpedos_sistema_estelar) {
					$html_defesas .= "<br>Torpedos Estelares: {$qtd_defesas} (Pdf Torpedo: {$this->pdf_torpedo})";
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

					$html_defesas .= "<br>Torpedeiros Estelares: {$qtd_defesas} {$nivel}";
				}
			}
			
			$qtd_instalacao_ataque_id = [];
			$html_instalacao_ataque = [];
			foreach ($planeta->instalacoes_ataque as $chave => $id_instalacao) {
				$instalacao_ataque = new instalacao($id_instalacao);
				$especiais = explode(";",$instalacao_ataque->especiais);
				//Especiais: pdf_instalacoes=valor
				$pdf_instalacoes = array_values(array_filter($especiais, function($value) {
					return strpos($value, 'pdf_instalacoes') !== false;
				}));				
				
				$pdf_instalacoes =  explode("=",$pdf_instalacoes[0]);
				$pdf_instalacoes =  $pdf_instalacoes[1];
				
				if (!empty($qtd_instalacao_ataque_id[$id_instalacao])) {
					$qtd_instalacao_ataque_id[$id_instalacao]++;
					$qtd_instalacao="{$qtd_instalacao_ataque_id[$id_instalacao]} x";
				} else {
					$qtd_instalacao_ataque_id[$id_instalacao] = 1;
					$qtd_instalacao = "";
				}
				
				$html_instalacao_ataque[$id_instalacao] = "{$qtd_instalacao} <div class='{$instalacao_ataque->icone} tooltip'><span class='tooltiptext'>{$instalacao_ataque->nome}</span></div> PdF Torpedo: {$pdf_instalacoes}<br>";
			}
			
			if ($html_defesas != "") {
				$html_defesas .= "<br>";
			}
			foreach ($html_instalacao_ataque as $chave => $html_instalacao) {
				$html_defesas .= $html_instalacao;
			}
			
			if ($html_defesas == "") {
				$html_defesas = "&nbsp;";
			}
			
			$html .= "<tr>
			<td>{$estrela->nome} ({$estrela->X};{$estrela->Y};{$estrela->Z};{$planeta->posicao})</td>
			<td>{$colonia->icone_capital}{$planeta->nome}&nbsp;{$planeta->icone_habitavel}</td>
			<td>{$html_defesas}</td>
			<td>{$html_pop_colonia}</td><td>{$colonia->poluicao}</td>
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
	function exibe_recursos_atuais() {
		global $wpdb;
		
		$resultados = $wpdb->get_results("
		SELECT cir.qtd, cr.nome, cr.descricao
		FROM colonization_imperio_recursos AS cir
		JOIN colonization_recurso AS cr
		ON cr.id=cir.id_recurso
		WHERE cir.id_imperio = {$this->id} AND turno={$this->turno->turno}
		AND cr.acumulavel = true
		AND cir.disponivel = true
		ORDER BY cr.nivel, cr.nome
		");
		
		$html = "<b>Recursos atuais:</b> ";
		foreach ($resultados as $resultado) {
			$html .= "<div class='tooltip' style='display: inline-block;'>{$resultado->nome} - {$resultado->qtd}; &nbsp;
						<span class='tooltiptext'>{$resultado->descricao}</span>
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
	***********************/
	function exibe_lista_colonias() {
		global $wpdb;

		$this->debug = "";
		
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

		$html_lista = "<b>Lista de Colônias</b><br>";
		$html_sistema = [];
		$html_planeta = [];
		$planeta_id_estrela = [];
		$mdo_sistema = [];
		$pop_sistema = [];

		if (!empty($resultados)) {
			//$imperio = new imperio($this->id, false, $this->turno->turno);
			if (empty($this->acoes)) {
				$this->acoes = new acoes($this->id, $this->turno->turno);
			}
		}
		
		$mdo = 0;
		$start_time_global = hrtime(true);
		foreach ($resultados as $resultado) {
			$colonia = new colonia ($resultado->id);
			$planeta = $colonia->planeta;
			$estrela = $colonia->estrela;
			$planeta_id_estrela[$planeta->id] = $estrela->id;

			$end_time = hrtime(true);
			$diferenca = round(($end_time - $start_time_global)/1000000,0);
			$this->debug  .= "
exibe_lista_colonias(): Criou objetos na entrada do ForEach para Colônia {$resultado->id}: {$diferenca}ms";
			
			$pop_mdo_sistema = $this->acoes->pop_mdo_sistema($estrela->id);

			$end_time = hrtime(true);
			$diferenca = round(($end_time - $start_time_global)/1000000,0);
			$this->debug  .= "
exibe_lista_colonias(): this->acoes->pop_mdo_sistema({$estrela->id}): {$diferenca}ms";
			
			$mdo_sistema[$planeta_id_estrela[$planeta->id]] = $pop_mdo_sistema['mdo'];
			$pop_sistema[$planeta_id_estrela[$planeta->id]] = $pop_mdo_sistema['pop'];
			
			if ($colonia->poluicao < round($this->limite_poluicao*0.33,0)) {
				$poluicao = "<span style='color: #007426;'>{$colonia->poluicao}</span>";
			} elseif ($colonia->poluicao < round($this->limite_poluicao*0.66,0)) {
				$poluicao = "<span style='color: #ffce00;'>{$colonia->poluicao}</span>";
			} elseif ($colonia->poluicao < $this->limite_poluicao) {				
				$poluicao = "<span style='color: #f1711d;'>{$colonia->poluicao}</span>";
			} else {
				$poluicao = "<span style='color: #ee1509;'>{$colonia->poluicao}</span>";
			}
				$id_instalacoes = $wpdb->get_results(
				"SELECT cpi.id_instalacao 
				FROM colonization_planeta_instalacoes AS cpi 
				JOIN colonization_instalacao AS ci
				ON ci.id = cpi.id_instalacao
				WHERE cpi.id_planeta = {$planeta->id} AND cpi.turno <= {$this->turno->turno}
				ORDER BY ci.nome");
				
				$icones_planeta = [];
				$qtd_instalacao_icone = [];
				
				foreach ($id_instalacoes as $id_instalacao) {
					$instalacao = new instalacao($id_instalacao->id_instalacao);
			$end_time = hrtime(true);
			$diferenca = round(($end_time - $start_time_global)/1000000,0);
			$this->debug  .= "
exibe_lista_colonias(): Criou objetos do ForEach das Instalações ({$id_instalacao->id_instalacao}): {$diferenca}ms";				
					
					if (!empty($instalacao->icone)) {
						if (empty($icones_planeta[$instalacao->id])) {
							$qtd_instalacao_icone[$instalacao->id] = 1;
							$icones_planeta[$instalacao->id] = " <div class='{$instalacao->icone} tooltip'><span class='tooltiptext'>{$instalacao->nome}</span></div>";
						} else {
							$qtd_instalacao_icone[$instalacao->id]++;
							$icones_planeta[$instalacao->id] = " <div class='{$instalacao->icone} tooltip'>x{$qtd_instalacao_icone[$instalacao->id]}<span class='tooltiptext'>{$instalacao->nome}</span></div>";
						}
					}
				}
				
				$planeta_id_estrela[$planeta->id] = $estrela->id;
				$pop_colonia = $colonia->pop + $colonia->pop_robotica;
				$html_pop_colonia = "{$pop_colonia}";
				if ($colonia->pop_robotica > 0) {
					$html_pop_colonia .= "(<div class='fas fa-users-cog tooltip'>&nbsp;<span class='tooltiptext'>População Robótica</span></div>{$colonia->pop_robotica})";
				}
				
				$mdo = $this->acoes->mdo_planeta($planeta->id);
			$end_time = hrtime(true);
			$diferenca = round(($end_time - $start_time_global)/1000000,0);
			$this->debug  .= "
exibe_lista_colonias(): acoes->mdo_planeta({$planeta->id}): {$diferenca}ms";
				
				$id_poluicao = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome = 'Poluição'");
				$balanco_poluicao_planeta = "";
				if (!empty($this->acoes->recursos_balanco_planeta[$id_poluicao][$planeta->id])) {
					if ($this->acoes->recursos_balanco_planeta[$id_poluicao][$planeta->id] > 0) {
						$balanco_poluicao_planeta = "(<span style='color: red;'>+{$this->acoes->recursos_balanco_planeta[$id_poluicao][$planeta->id]}</span>)";
					} else {
						$balanco_poluicao_planeta = "(<span style='color: green;'>{$this->acoes->recursos_balanco_planeta[$id_poluicao][$planeta->id]}</span>)";
					}
				}
				
				$html_icones_planeta = "";
				foreach ($icones_planeta as $id_instalacao => $html) {
			$end_time = hrtime(true);
			$diferenca = round(($end_time - $start_time_global)/1000000,0);
			$this->debug  .= "
exibe_lista_colonias(): ForEach dos Icones do Planeta: {$diferenca}ms";
					$html_icones_planeta .= $html;
				}

				$html_planeta[$planeta->id] = "<div><span style='font-style: italic;'>{$colonia->icone_capital}{$planeta->nome}&nbsp;{$colonia->icone_vassalo}{$planeta->icone_habitavel}{$html_icones_planeta}</span> - MdO/Pop: {$mdo}/{$html_pop_colonia} - Poluição: {$poluicao} {$balanco_poluicao_planeta}</div>";
			
		}
		
		foreach ($html_planeta AS $id_planeta => $html) {
			if (empty($html_sistema[$planeta_id_estrela[$id_planeta]])) {
				$estrela = new estrela($planeta_id_estrela[$id_planeta]);
				
				$html_sistema[$planeta_id_estrela[$id_planeta]] = "<div style='margin-bottom: 5px;'><div><span style='text-decoration: underline;'>Colônias em <span style='font-weight: 600; color: #4F4F4F;'>{$estrela->nome} ({$estrela->X};{$estrela->Y};{$estrela->Z})</span></span> - MdO/Pop: {$mdo_sistema[$planeta_id_estrela[$id_planeta]]}/{$pop_sistema[$planeta_id_estrela[$id_planeta]]}</div>";
			}
			$html_sistema[$planeta_id_estrela[$id_planeta]] .= $html;
		}
		
		foreach ($html_sistema AS $id_sistema => $html) {
			$html_lista .= $html."</div>";
		}
		
		return $html_lista;
	}
	
}
?>