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
	public $espalhamento = 0;
	public $html_header;
	public $turno;
	public $acoes;
	public $id_estrela_capital = 0;
	public $debug = "";
	public $popula_variaveis_imperio = false;
	
	private $attributes = []; //Array privado com todas as propriedades "mágicas"
	
	//Todos esses atributos são na verdade relativos à Techs do Império e em teoria deveriam estar no objeto Imperio_Techs
	//*** Por serem "custosas", estou passando esses atributos para as funções "mágicas" __get e __set ***
	private $icones_html = "";
	private $max_pop = 0;
	private $limite_poluicao = 100;
	private $alcance_logistica = 0;
	private $bonus_alcance_logistica = 0;
	private $bonus_alcance = 0;
	private $bonus_comercio = 0;
	private $bonus_recurso = [];
	private $sinergia = [];
	private $extrativo = [];
	private $bonus_todos_recursos = 0;
	private $max_bonus_recurso = [];
	private $bonus_pesquisa_naves = 0;
	private $crescimento_pop = 1;
	private $sensores = 0;
	private $anti_camuflagem = 0;
	private $coloniza_inospito = 0;
	private $alimento_inospito = 0;
	private $consome_poluicao = 0;
	private $bonus_invasao = 1;
	private $bonus_abordagem = 0;
	private $defesa_abordagem=0;
	private $camuflagem_grande=false;
	private $tricobalto_torpedo=false;
	private $tritanium_blindagem=false;
	private $neutronium_blindagem=false;
	
	private $mk_laser = 0;
	private $mk_torpedo = 0;
	private $mk_projetil = 0;
	private $mk_plasma = 0;
	private $mk_blindagem = 0;
	private $mk_escudos = 0;
	private $mk_dobra = 0;
	private $mk_impulso= 0;
	private $mk_bombardeamento = 0;
	private $mk_camuflagem = 0;
	private $nivel_estacao_orbital = 0;
	
	//Atributos de defesa planetária
	private $pdf_planetario = 10;
	private $pdf_torpedo = 0;
	private $pdf_plasma = 0;
	private $defesa_invasao = 1;
	private $torpedos_sistema_estelar = false;
	private $icone_torpedos_sistema_estelar = "";
	private $torpedeiros_sistema_estelar = false;
	private $icone_torpedeiros_sistema_estelar = "";
	//***/

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

		$pontuacao = $wpdb->get_var("
		SELECT COUNT(ce.id)*100 FROM (
			SELECT DISTINCT ce.id
			FROM colonization_imperio_colonias AS cic
			JOIN colonization_planeta AS cp
			ON cp.id = cic.id_planeta
			JOIN colonization_estrela AS ce
			ON ce.id = cp.id_estrela
			WHERE cic.id_imperio={$this->id} AND cic.turno={$this->turno->turno}
		) AS ce");
		$this->pontuacao = $this->pontuacao + $pontuacao;
		$this->pontuacao_colonia = $this->pontuacao_colonia + $pontuacao;		

		$pontuacao = $wpdb->get_var("SELECT COUNT(id)*20 FROM colonization_imperio_colonias WHERE id_imperio={$this->id} AND turno={$this->turno->turno}");
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

		$pontuacao = $wpdb->get_var("SELECT 
		SUM(cir.qtd*cr.nivel) 
		FROM colonization_imperio_recursos AS cir
		JOIN colonization_recurso AS cr
		ON cr.id=cir.id_recurso
		WHERE cir.id_imperio={$this->id} AND cir.turno={$this->turno->turno}");
		$this->pontuacao = $this->pontuacao + $pontuacao;
		$this->pontuacao_desenvolvimento = $this->pontuacao_desenvolvimento + $pontuacao;
		
		//Industrializáveis contam à parte
		$id_industrializaveis = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='Industrializáveis'");
		$pontuacao = $wpdb->get_var("SELECT 
		SUM(cir.qtd) 
		FROM colonization_imperio_recursos AS cir
		WHERE cir.id_imperio={$this->id} AND cir.turno={$this->turno->turno} AND cir.id_recurso={$id_industrializaveis}");
		$this->pontuacao = $this->pontuacao + $pontuacao;
		$this->pontuacao_desenvolvimento = $this->pontuacao_desenvolvimento + $pontuacao;		
		
		$pontuacao = $wpdb->get_var("SELECT SUM(qtd*(tamanho*2 + PDF_laser + PDF_projetil + PDF_torpedo + blindagem + escudos + pesquisa + FLOOR(alcance/1.8))) AS pontuacao FROM colonization_imperio_frota WHERE id_imperio={$this->id} AND turno<={$this->turno->turno} AND turno_destruido<{$this->turno->turno}");
		$this->pontuacao = $this->pontuacao + $pontuacao;
		$this->pontuacao_belica = $this->pontuacao_belica + $pontuacao;

		$pontuacao = $wpdb->get_var("SELECT SUM(custo) 
		FROM
		(SELECT (CASE WHEN cit.custo_pago > 0 THEN cit.custo_pago ELSE ct.custo END) AS custo
		FROM colonization_imperio_techs AS cit
		JOIN colonization_tech AS ct
		ON ct.id=cit.id_tech
		WHERE cit.id_imperio={$this->id}
		AND cit.turno <= {$this->turno->turno}
		) AS custo_tech");
		$this->pontuacao = $this->pontuacao + $pontuacao;
		$this->pontuacao_tech = $this->pontuacao_tech + $pontuacao;

		$this->id_estrela_capital = $wpdb->get_var("
		SELECT ce.id
		FROM colonization_imperio_colonias AS cit
		JOIN colonization_planeta AS cp
		ON cp.id = cit.id_planeta
		JOIN colonization_estrela AS ce
		ON ce.id=cp.id_estrela
		WHERE cit.turno = {$this->turno->turno}
		AND cit.capital=true AND id_imperio = {$this->id}");

		$this->espalhamento = $wpdb->get_var("SELECT CEIL(SUM(POW(POW(estrela_capital.X - estrelas_colonias.X,2) + POW(estrela_capital.Y - estrelas_colonias.Y,2) + POW(estrela_capital.Z - estrelas_colonias.Z,2),0.5))) AS espalhamento
		FROM (SELECT ce.X, ce.Y, ce.Z, cit.id_imperio, cit.turno
		FROM colonization_imperio_colonias AS cit
		JOIN colonization_planeta AS cp
		ON cp.id = cit.id_planeta
		JOIN colonization_estrela AS ce
		ON ce.id=cp.id_estrela
		WHERE cit.turno = (SELECT MAX(id) FROM colonization_turno_atual)
		AND cit.capital=true AND id_imperio = {$this->id}) AS estrela_capital
		JOIN 
		(SELECT DISTINCT ce.X, ce.Y, ce.Z, cit.id_imperio, cit.turno
		FROM colonization_imperio_colonias AS cit
		JOIN colonization_planeta AS cp
		ON cp.id = cit.id_planeta
		JOIN colonization_estrela AS ce
		ON ce.id=cp.id_estrela
		WHERE cit.turno = (SELECT MAX(id) FROM colonization_turno_atual)
		AND cit.capital=false AND cit.vassalo=false AND id_imperio = {$this->id}) AS estrelas_colonias
		ON estrelas_colonias.id_imperio = estrela_capital.id_imperio AND estrelas_colonias.turno = estrela_capital.turno");
	
		//$this->popula_variaveis_imperio();
	}

	/***********************
	function __set($name, $value)
	----------------------
	Método mágico para setar variáveis
	//***********************/
	public function __set($name, $value)
	{
		$this->$name = $value;
	}

	/***********************
	function __set($name, $value)
	----------------------
	Método mágico para pegar variáveis
	***********************/
	public function __get($name)
	{
		if (!$this->popula_variaveis_imperio) {
			$this->popula_variaveis_imperio();
		}
		
		return $this->$name;
	}
	//***/

	/***********************
	function popula_variaveis_imperio()
	----------------------
	Popula as variáveis do Império
	***********************/
	function popula_variaveis_imperio() {
		global $wpdb;
		
		if ($this->popula_variaveis_imperio) {
			return;
		}
		
		//***********************************
		// ALTERAÇÕES DE TECH (ESPECIAIS)
		//***********************************
		$especiais_lista = $wpdb->get_results("SELECT ct.id AS id, ct.especiais AS especiais, ct.icone
		FROM colonization_imperio_techs AS cit
		JOIN colonization_tech AS ct
		ON ct.id = cit.id_tech
		WHERE cit.id_imperio={$this->id} 
		AND cit.custo_pago = 0
		AND ct.especiais != ''
		AND ct.parte_nave = false
		AND turno <= {$this->turno->turno}");
		
		foreach ($especiais_lista AS $id) {
			$especiais = explode(";",$id->especiais);
			
			//Todos os MKs
			$mks = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'mk_') !== false;
			}));
			
			if (!empty($mks)) {
				//mk_[parte_nave]=valor
				$mks_valor = explode("=",$mks[0]);

				$propriedade = $mks_valor[0];
				if ($mks_valor[1] > $this->$propriedade) {
					$this->$propriedade = $mks_valor[1];
				}
			}

			//nivel_estacao_orbital
			$nivel_estacao_orbital = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'nivel_estacao_orbital') !== false;
			}));
			
			if (!empty($nivel_estacao_orbital)) {
				$nivel_estacao_orbital_valor = explode("=",$nivel_estacao_orbital[0]);

				if ($nivel_estacao_orbital_valor[1] > $this->nivel_estacao_orbital) {
					$this->nivel_estacao_orbital = $nivel_estacao_orbital_valor[1];
				}
			}
			
			//camuflagem_grande
			$camuflagem_grande = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'camuflagem_grande') !== false;
			}));
			
			if (!empty($camuflagem_grande)) {
				$this->camuflagem_grande = true;
			}
			
			//tricobalto_torpedo
			$tricobalto_torpedo = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'tricobalto_torpedo') !== false;
			}));
			
			if (!empty($tricobalto_torpedo)) {
				$this->tricobalto_torpedo = true;
			}
			
			//tritanium_blindagem;
			$tritanium_blindagem = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'tritanium_blindagem') !== false;
			}));
			
			if (!empty($tritanium_blindagem)) {
				$this->tritanium_blindagem = true;
			}
			
			//neutronium_blindagem;
			$neutronium_blindagem = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'neutronium_blindagem') !== false;
			}));
			
			if (!empty($neutronium_blindagem)) {
				$this->neutronium_blindagem = true;
			}			
			
			//defesa_abordagem
			$defesa_abordagem = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'defesa_abordagem') !== false;
			}));
			
			if (!empty($defesa_abordagem)) {
				$defesa_abordagem_valor = explode("=",$defesa_abordagem[0]);
				$this->defesa_abordagem = $this->defesa_abordagem + $defesa_abordagem_valor[1];
			}
			
			//bonus_abordagem
			$bonus_abordagem = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'bonus_abordagem') !== false;
			}));
			
			if (!empty($bonus_abordagem)) {
				$bonus_abordagem_valor = explode("=",$bonus_abordagem[0]);
				$this->bonus_abordagem = $this->bonus_abordagem	+ $bonus_abordagem_valor[1];
			}

			//bonus_invasao
			$bonus_invasao = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'bonus_invasao') !== false;
			}));
			
			if (!empty($bonus_invasao)) {
				$bonus_invasao_valor = explode("=",$bonus_invasao[0]);
				$this->bonus_invasao = $this->bonus_invasao	+ $bonus_invasao_valor[1];
			}
			
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

			//Especiais -- consome_poluicao
			$consome_poluicao = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'consome_poluicao') !== false;
			}));
			
			if (!empty($consome_poluicao)) {
				$consome_poluicao_valor = explode("=",$consome_poluicao[0]);
				$this->consome_poluicao = $this->consome_poluicao + $consome_poluicao_valor[1];
			}
			
			//Especiais -- sensores
			$sensores = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'sensores') !== false;
			}));
			
			if (!empty($sensores)) {
				$sensores_valor = explode("=",$sensores[0]);
				$this->sensores = $this->sensores	+ $sensores_valor[1];
			}	

			//Especiais -- anti_camuflagem
			$anti_camuflagem = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'anti_camuflagem') !== false;
			}));
			
			if (!empty($anti_camuflagem)) {
				$anti_camuflagem_valor = explode("=",$anti_camuflagem[0]);
				$this->anti_camuflagem = $this->anti_camuflagem + $anti_camuflagem_valor[1];
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

			//Especiais -- bonus_comercio
			$bonus_comercio = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'bonus_comercio') !== false;
			}));
			
			if (!empty($bonus_comercio)) {
				$bonus_comercio_valor = explode("=",$bonus_comercio[0]);
				if ($this->bonus_comercio < $bonus_comercio_valor[1]) {
					$this->bonus_comercio = $bonus_comercio_valor[1];
				}
			}	
			
			//Especiais -- bonus_logistica
			$bonus_logistica = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'bonus_logistica') !== false;
			}));
			
			if (!empty($bonus_logistica)) {
				$bonus_logistica_valor = explode("=",$bonus_logistica[0]);
				$this->bonus_alcance_logistica = $this->bonus_alcance_logistica + $bonus_logistica_valor[1];
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
				$this->pdf_planetario = $this->pdf_planetario + $pdf_planetario_valor[1];
			}

			//Especiais -- pdf_torpedo
			$pdf_torpedo = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'pdf_torpedo') !== false;
			}));
			
			if (!empty($pdf_torpedo)) {
				$pdf_torpedo_valor = explode("=",$pdf_torpedo[0]);
				$this->pdf_torpedo = $this->pdf_torpedo	+ $pdf_torpedo_valor[1];
			}
			
			//Especiais -- pdf_plasma
			$pdf_plasma = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'pdf_plasma') !== false;
			}));
			
			if (!empty($pdf_plasma)) {
				$pdf_plasma_valor = explode("=",$pdf_plasma[0]);
				$this->pdf_plasma = $this->pdf_plasma	+ $pdf_plasma_valor[1];
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
				//$tech_torpededos = new tech ($id->id);
				$this->icone_torpedos_sistema_estelar = " <div class='{$id->icone} tooltip'><span class='tooltiptext'>Torpedos Espaciais</span></div>";				
			}
			
			//Especiais -- torpedeiros_sistema_estelar			
			$torpedeiros_sistema_estelar = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'torpedeiros_sistema_estelar') !== false;
			}));
			
			if (!empty($torpedeiros_sistema_estelar)) {
				$torpedeiros_sistema_estelar_valor = explode("=",$torpedeiros_sistema_estelar[0]);
				$this->torpedeiros_sistema_estelar = $torpedeiros_sistema_estelar_valor[1];
				//$tech_torpedeiro = new tech ($id->id);
				$this->icone_torpedeiros_sistema_estelar = " <div class='{$id->icone} tooltip'><span class='tooltiptext'>Torpedeiros Espaciais</span></div>";
			}
		}
		//Depois de pegar o alcance e o bônus de logística, soma os dois
		$this->alcance_logistica = $this->alcance_logistica + $this->bonus_alcance_logistica;
		
		$this->popula_variaveis_imperio = true;
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
			<td><div data-atributo='nome_jogador'>{$user->display_name}{$this->icones_html()}</div></td>
			<td><div data-atributo='nome' data-valor-original='{$this->nome}' data-editavel='true'>{$this->nome}</div></td>
			<td><div data-atributo='prestigio' data-valor-original='{$this->prestigio}' data-editavel='true' data-style='width: 40px;'>{$this->prestigio}</div></td>
			<td><div data-atributo='pop' data-valor-original=''>{$this->pop}</div></td>
			<td><div data-atributo='pontuacao' data-valor-original=''>{$this->pontuacao}</div></td>
			<td><div data-atributo='gerenciar'><a href='#' onclick='return gerenciar_objeto(event, this);'>Gerenciar Objeto</a></div></td>";

		return $html;
	}

	/***********************
	function contatos_imperio()
	----------------------
	Lista com os Impérios que são conhecidos pelo Império atual
	***********************/
	function contatos_imperio() {
		global $wpdb;
		
		return $wpdb->get_results("SELECT DISTINCT ci.id, ci.nome 
			FROM colonization_imperio AS ci
			JOIN (SELECT DISTINCT id_imperio_contato FROM colonization_diplomacia WHERE id_imperio={$this->id}) AS cd
			ON ci.id = cd.id_imperio_contato
			ORDER BY ci.nome");
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
		global $wpdb, $plugin_colonization;
		
		if ($this->id == 0) {
			return;
		}
		
		if (!$this->popula_variaveis_imperio) {
			$this->popula_variaveis_imperio();
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
		
		$html .= "<table class='lista_colonias_imperio'>
		<thead>
		<tr><th style='width: 22%;'>Estrela (X;Y;Z;P)</th><th style='width: 23%;'>Planeta</th><th style='width: 30%;'>Defesas</th><th style='width: 10%;'>Pop.</th><th style='width: 15%;'>Poluição</th></tr>
		</thead>
		<tbody>
		";
		
		foreach ($resultados as $id) {
			$colonia = new colonia($id->id);
			if ($colonia->num_instalacoes == 0) {//Se não tiver Instalações na colônia, pula a mesma
				continue;
			}
			
			$planeta = new planeta($colonia->id_planeta);
			$planeta->popula_instalacoes_planeta();
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
					$pdf_torpedo_sistema_estelar = (($this->pdf_torpedo*2)-1);
					$html_defesas .= "<br>{$this->icone_torpedos_sistema_estelar} x{$colonia->qtd_defesas} (Pdf: {$pdf_torpedo_sistema_estelar})";
				}
				
				if ($this->torpedeiros_sistema_estelar !== false) {
					$nivel = $plugin_colonization->html_mk($this->torpedeiros_sistema_estelar);
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
			<td>{$colonia->icone_capital}{$planeta->nome}&nbsp;{$planeta->icone_habitavel()}</td>
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
	function pega_qtd_recurso_imperio($id_recurso)
	----------------------
	Pega a quantidade de um determinado recurso do Império
	
	$id_recurso - qual recurso está sendo verificado
	************************/
	function pega_qtd_recurso_imperio($id_recurso) {
		global $wpdb;
		
		return $wpdb->get_var("SELECT qtd FROM colonization_imperio_recursos WHERE id_recurso={$id_recurso} AND turno={$this->turno->turno} AND id_imperio={$this->id}");
	}
	
	
	/***********************
	function exibe_lista_colonias()
	----------------------
	Exibe as Colônias atuais Império
	
	$id_colonia -- se diferente de zero, processa uma colônia específica que tenha sido modificada
	$salva_lista -- salva os dados da lista (após um reprocessamento)
	***********************/
	function exibe_lista_colonias($id_colonia=array(0,0), $salva_lista=false) {
		global $wpdb, $start_time, $plugin_colonization;
		
		if ($this->id == 0) {
			return;
		}

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}

		if (!$this->popula_variaveis_imperio) {
			$this->popula_variaveis_imperio();
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
		$mdo_colonia = [];
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
	
		$estrela = [];
		$planeta = [];
		$colonia = [];
		$instalacao = [];

		$flag_nova_lista = true;
		if (empty($lista_colonias_db)) {
			$html_planeta = [];
			$planeta_id_estrela = [];
			$mdo_sistema = [];
			$mdo_colonia = [];
			$pop_sistema = [];
			$qtd_defesas_sistema = [];
		} else {
			$flag_nova_lista = false;
			$lista_colonias_db = json_decode($lista_colonias_db, true, 512, JSON_UNESCAPED_UNICODE);
			if (empty($lista_colonias_db['mdo_colonia'])) {
				$lista_colonias_db['mdo_colonia'] = [];
			}
			
			$html_planeta_temp = $lista_colonias_db['html_planeta'];
			//$html_transfere_pop_planeta_temp = $lista_colonias_db['html_transfere_pop_planeta'];
			$planeta_id_estrela = $lista_colonias_db['planeta_id_estrela'];
			$mdo_sistema = $lista_colonias_db['mdo_sistema'];
			$mdo_colonia = $lista_colonias_db['mdo_colonia'];
			$pop_sistema = $lista_colonias_db['pop_sistema'];
			$qtd_defesas_sistema = $lista_colonias_db['qtd_defesas_sistema'];
			
			if (!empty($id_colonia)) {
				if (!empty($id_colonia[0])) {
					$colonia[$id_colonia[0]] = new colonia($id_colonia[0]);
					$estrela[$colonia[$id_colonia[0]]->id_estrela] = new estrela($colonia[$id_colonia[0]]->id_estrela);
					$mdo_sistema[$colonia[$id_colonia[0]]->id_estrela] = "";
					$mdo_colonia[$id_colonia[0]] = 0;
				}
				
				if (!empty($id_colonia[1])) {
					$colonia[$id_colonia[1]] = new colonia($id_colonia[1]);
					$estrela[$colonia[$id_colonia[1]]->id_estrela] = new estrela($colonia[$id_colonia[1]]->id_estrela);
					$mdo_sistema[$colonia[$id_colonia[1]]->id_estrela] = "";
					$mdo_colonia[$id_colonia[1]] = 0;
				}				
			}
			
			foreach ($html_planeta_temp as $chave => $html_do_planeta) {
				$html_planeta[$chave] = html_entity_decode($html_do_planeta, ENT_QUOTES);
				//$html_transfere_pop_planeta[$chave] = html_entity_decode($html_transfere_pop_planeta_temp, ENT_QUOTES);
			}
		}

		$imperio_recursos = new imperio_recursos($this->id, $this->turno->turno);
		$id_alimento = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome = 'Alimentos'");
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
				AND (cpi.turno_destroi IS NULL or cpi.turno_destroi = 0)
				AND ci.icone != ''
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
				
				$mdo_colonia[$resultado->id] = $this->acoes->mdo_planeta($colonia[$resultado->id]->id_planeta);
				
				$id_poluicao = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome = 'Poluição'");
				$balanco_poluicao_planeta = "";
				
				$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
				$this->debug .= "imperio->exibe_lista_colonias -> Poluição da Colônia {$resultado->id}: {$this->acoes->recursos_balanco_planeta[$id_poluicao][$colonia[$resultado->id]->id_planeta]} {$diferenca}ms \n";
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
				
				$html_pdf_planetario = "";
				if ($colonia[$resultado->id]->qtd_defesas > 0) {
					$colonia[$resultado->id]->pdf_planetario = round(($this->pdf_planetario*$colonia[$resultado->id]->qtd_defesas/10),0,PHP_ROUND_HALF_DOWN);
					$html_pdf_planetario .= "<div class='mini_instalacao_ataque far fa-shield tooltip' style='display: inline;'><span class='tooltiptext'>PdF Planetário</span>:{$colonia[$resultado->id]->pdf_planetario}</div>";
				}
				
				$planeta[$colonia[$resultado->id]->id_planeta]->popula_instalacoes_planeta();
				foreach ($planeta[$colonia[$resultado->id]->id_planeta]->mini_html_instalacao_ataque as $chave => $html_instalacao) {
					$html_pdf_planetario .= $html_instalacao;
				}
				
				if ($html_pdf_planetario != "") {
					$html_pdf_planetario = " ({$html_pdf_planetario})";
				}

				$chave_alimento = array_search($id_alimento, $imperio_recursos->id_recurso);
				$alimentos = $this->acoes->recursos_balanco[$id_alimento] + $imperio_recursos->qtd[$chave_alimento];
				$nova_pop = $colonia[$resultado->id]->crescimento_colonia($this, $alimentos, $this->acoes->recursos_balanco[$id_alimento]);
				
				$html_nova_pop = "";
				if ($nova_pop > 0) {
					$html_nova_pop = " (<span class='tooltip'><span class='tooltiptext' style='font-size: 0.7em'>Crescimento Populacional</span><span style='color: green; font-family: Verdana, Tahoma, sans-serif;'>+{$nova_pop}</span></span>)";	
				} elseif ($nova_pop < 0) {
					$html_nova_pop = " (<span class='tooltip'><span class='tooltiptext' style='font-size: 0.7em'>Crescimento Populacional</span><span style='color: red; font-family: Verdana, Tahoma, sans-serif;'>{$nova_pop}</span></span>)";	
				}
				
				$html_planeta[$colonia[$resultado->id]->id_planeta] = "<div class='dados_planeta'><span style='font-style: italic;'>
				{$colonia[$resultado->id]->icone_capital}{$planeta[$colonia[$resultado->id]->id_planeta]->nome}&nbsp;{$colonia[$resultado->id]->icone_vassalo}{$planeta[$colonia[$resultado->id]->id_planeta]->icone_habitavel()}
				{$html_icones_planeta}</span> - MdO/Pop: {$mdo_colonia[$resultado->id]}/{$colonia[$resultado->id]->html_pop_colonia}{$html_nova_pop}
				{$html_pdf_planetario} - Poluição: {$poluicao} {$balanco_poluicao_planeta}</div>";
				//$html_transfere_pop_planeta[$colonia[$resultado->id]->id_planeta] = $html_transfere_pop;
		}
		$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
		$this->debug .= "imperio->exibe_lista_colonias -> foreach() Dados das Colônias {$diferenca}ms \n";

		//Salva todas as variáveis globais de balanço e produção no Banco de Dados
		
		if ($flag_nova_lista || $id_colonia[0] != 0) {
			$html_planeta_temp = [];		
			foreach ($html_planeta as $chave => $html_do_planeta) {
				$html_planeta_temp[$chave] = htmlentities($html_do_planeta, ENT_QUOTES);
				//$html_transfere_pop_temp[$chave] = htmlentities($html_transfere_pop_planeta[$chave], ENT_QUOTES);
			}
			$dados_html_para_salvar = [];
			$dados_html_para_salvar['html_planeta'] = $html_planeta_temp;
			//$dados_html_para_salvar['html_transfere_pop_planeta'] = $html_transfere_pop_planeta_temp;
			$dados_html_para_salvar['planeta_id_estrela'] = $planeta_id_estrela;
			$dados_html_para_salvar['mdo_sistema'] = $mdo_sistema;
			$dados_html_para_salvar['mdo_colonia'] = $mdo_colonia;
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
				if (!is_array($qtd_defesas_sistema[$planeta_id_estrela[$id_planeta]])) {
					$torpedos = $qtd_defesas_sistema[$planeta_id_estrela[$id_planeta]];
					$qtd_defesas_sistema[$planeta_id_estrela[$id_planeta]] = [];
					$qtd_defesas_sistema[$planeta_id_estrela[$id_planeta]]['torpedos'] = $torpedos;
					$qtd_defesas_sistema[$planeta_id_estrela[$id_planeta]]['torpedeiros'] = $torpedos;
					$qtd_defesas_sistema[$planeta_id_estrela[$id_planeta]]['minas_subespaciais'] = 0;
				}
				
				if ($qtd_defesas_sistema[$planeta_id_estrela[$id_planeta]]['torpedos'] > 0) {
					if ($this->torpedos_sistema_estelar) {
						$pdf_torpedo_sistema_estelar = (($this->pdf_torpedo*2)-1);
						$html_defesas_sistema = "{$this->icone_torpedos_sistema_estelar} x{$qtd_defesas_sistema[$planeta_id_estrela[$id_planeta]]['torpedos']} (Pdf: {$pdf_torpedo_sistema_estelar})";
					}
					
					if ($this->torpedeiros_sistema_estelar !== false) {
						$nivel = $plugin_colonization->html_mk($this->torpedeiros_sistema_estelar);
						$html_defesas_sistema .= " {$this->icone_torpedeiros_sistema_estelar} {$nivel} x{$qtd_defesas_sistema[$planeta_id_estrela[$id_planeta]]['torpedeiros']}";
					}
				}

				if (!empty($qtd_defesas_sistema[$planeta_id_estrela[$id_planeta]]['minas_subespaciais'])) {
					$html_defesas_sistema .= " <div class='fab fa-mixer tooltip'><span class='tooltiptext'>Minas Subespaciais (PdF Torpedos: {$qtd_defesas_sistema[$planeta_id_estrela[$id_planeta]]['minas_subespaciais']})</span></div>";
				}
				
				$html_nome_estrela = "{$estrela[$planeta_id_estrela[$id_planeta]]->nome} ({$estrela[$planeta_id_estrela[$id_planeta]]->X};{$estrela[$planeta_id_estrela[$id_planeta]]->Y};{$estrela[$planeta_id_estrela[$id_planeta]]->Z})";
				if ($roles == "administrator") {
					$html_nome_estrela = "<a href='#' onclick='return tirar_cerco(this, event, {$estrela[$planeta_id_estrela[$id_planeta]]->id}, true)'>{$html_nome_estrela}</a>";
				}
				
				
				$html_sistema[$planeta_id_estrela[$id_planeta]] = "
				<div style='margin-bottom: 5px;'><div style='display: inline;'><span style='text-decoration: underline;'>Colônias em <span style='font-weight: 600; color: #4F4F4F;'>
				{$html_nome_estrela}</span></span> - MdO/Pop: {$mdo_sistema[$planeta_id_estrela[$id_planeta]]}/{$pop_sistema[$planeta_id_estrela[$id_planeta]]}
				{$html_defesas_sistema}
				</div><br>";
			}

			//Define se exibe ou não o div de transferência de Pop
			$id_colonia = $wpdb->get_var("SELECT id FROM colonization_imperio_colonias WHERE id_imperio={$this->id} AND id_planeta={$id_planeta} AND turno={$this->turno->turno}");
			if (empty($colonia[$id_colonia])) {
				$colonia[$id_colonia] = new colonia($id_colonia);
				$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
				$this->debug .= "imperio->exibe_lista_colonias -> foreach() new Colonia {$diferenca}ms \n";
				if (empty($mdo_colonia[$id_colonia])) {
					$mdo_colonia[$id_colonia] = 0;
				}
			}
			
			$mdo_disponivel_sistema = $pop_sistema[$planeta_id_estrela[$id_planeta]] - $mdo_sistema[$planeta_id_estrela[$id_planeta]];
			$html_transfere_pop = "";
			$mdo_disponivel_planeta = $colonia[$id_colonia]->pop - $mdo_colonia[$id_colonia];
			$mdo_transfere = min($mdo_disponivel_sistema, $mdo_disponivel_planeta);
			if ($mdo_disponivel_planeta > $mdo_disponivel_sistema) {
				$mdo_disponivel_planeta = $mdo_disponivel_sistema;
			}
			$html_lista_planetas = "";
			$lista_options_colonias = "";
			if ($mdo_disponivel_sistema > 0 && $mdo_disponivel_planeta > 0 && $this->turno->bloqueado == 1 && ($colonia[$id_colonia]->vassalo == 0 || ($colonia[$id_colonia]->vassalo == 1 && $roles == "administrator"))) {
				$ids_colonias = $wpdb->get_results("SELECT id FROM colonization_imperio_colonias WHERE id_imperio={$this->id} AND turno={$this->turno->turno} AND id != {$id_colonia}");
				foreach ($ids_colonias as $id_colonia_imperio) {
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
					if ($id_colonia_imperio->id != $id_colonia && ($colonia[$id_colonia_imperio->id]->vassalo == 0 || ($colonia[$id_colonia_imperio->id]->vassalo == 1 && $roles == "administrator"))) {
							$lista_options_colonias .= "<option data-atributo='id_colonia' value='{$id_colonia_imperio->id}'>{$planeta[$colonia[$id_colonia_imperio->id]->id_planeta]->nome}</option> \n";
					}
				}
				$html_lista_planetas = "<b>Transferir</b> <input data-atributo='pop' data-ajax='true' data-valor-original='1' type='range' min='1' max='{$mdo_transfere}' value='1' oninput='return altera_pop_transfere(event, this);' style='width: 80px;'></input>&nbsp;&nbsp;&nbsp;<label data-atributo='pop' style='width: 30px;'>1</label>
				&nbsp; <b>Pop para</b> &nbsp; 
				<select class='select_lista_planetas'>
				{$lista_options_colonias}
				</select> &nbsp; <a href='#' onclick='return transfere_pop(event,this,{$this->id},{$id_colonia},{$colonia[$id_colonia]->id_planeta},{$colonia[$id_colonia]->id_estrela});'>TRANSFERIR!</a>
				";
				$html_transfere_pop = "<div style='display: inline;'><a href='#' onclick='return mostra_div_transferencia(event, this);'><div class='fas fa-walking tooltip' style='display: inline;'><span class='tooltiptext'>Transferir Pop para outro planeta</span> ({$mdo_transfere})</div></a>
				<div data-atributo='lista_planetas' class='div_lista_planetas'>{$html_lista_planetas}</div>
				</div>";
			}			
			
			$html_sistema[$planeta_id_estrela[$id_planeta]] .= "{$html}{$html_transfere_pop}<br>";
		}
		$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
		$this->debug .= "imperio->exibe_lista_colonias -> foreach() Ordenação do HTML {$diferenca}ms \n";
		
		foreach ($html_sistema AS $id_sistema => $html) {
			$sistema_sob_cerco = $wpdb->get_var("SELECT cerco FROM colonization_estrela WHERE id={$id_sistema}");
			$icone_cerco = "";
			if ($sistema_sob_cerco == 1) {
				$icone_cerco = "<div class='fas fa-bell-on tooltip' style='display: inline;'><span class='tooltiptext'>Sistema sob ataque!</span>&nbsp;</div>";
				if ($roles == "administrator") {
					$icone_cerco = "<a href='#' onclick='return tirar_cerco(this,event,{$id_sistema});'><div class='fas fa-bell-on tooltip' style='display: inline;'><span class='tooltiptext'>Sistema sob ataque!</span>&nbsp;</div></a>";	
				}
			}
			$html = substr_replace($html, $icone_cerco, 39, 0);
			$html_lista .= "{$html}</div>";
		}
		
		return $html_lista;
	}
	
	
	/***********************
	function icones_html()
	----------------------
	Popula a variável $icones_html e retorna a mesma
	************************/	
	function icones_html() {
		global $wpdb;
		//Algumas Techs tem ícones, que devem ser mostrados do lado do nome do jogador
		$tech = new tech();
		$icones = $tech->query_tech(" AND ct.icone != ''", $this->id);

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}

		$icone_html = [];
		
		if (!$this->popula_variaveis_imperio) {
			$this->popula_variaveis_imperio();
		}		
		
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
		
		return $this->icones_html;
	}
}
?>