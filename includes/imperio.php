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
	public $turno;
	public $acoes;
	public $debug = "";
	
	//Todos esses atributos são na verdade relativos à Techs do Império e em teoria deveriam estar no objeto Imperio_Techs
	//*** Por serem "custosas", estou passando esses atributos para as funções "mágicas" __get e __set ***
	private $pop = 0;
	private $pontuacao = 0;
	private $pontuacao_tech = 0;
	private $pontuacao_colonia = 0;
	private $pontuacao_desenvolvimento = 0;
	private $pontuacao_belica = 0;
	private $espalhamento = 0;
	private $id_estrela_capital = 0;
	private $bonus_pop = 0;
	private $logistica = 0;
	private $bonus_logistica = 0;
	private $bonus_alcance = 0;
	private $bonus_comercio = 0;
	private $bonus_todos_recursos = 0;
	private $bonus_pesquisa_naves = 0;
	private $anti_camuflagem = 0;
	private $coloniza_inospito = 0;
	private $alimento_inospito = 0;
	private $bonus_consome_poluicao = 0;
	private $consome_poluicao = 25;
	private $limite_poluicao = 100;
	private $bonus_crescimento_pop = 1;
	private $bonus_invasao = 1;
	private $bonus_abordagem = 0;
	private $sensores = 0;
	private $bonus_defesa_abordagem = 0;
	private $icones_html = "";
	private $contatos_imperio = [];

	//Não sei como isso é acessado!
	private $bonus_recurso = [];
	private $extrativo = [];
	
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
	private $camuflagem_grande = false;
	private $tricobalto_torpedo = false;
	private $tritanium_blindagem = false;
	private $neutronium_blindagem = false;
	
	//Atributos de defesa planetária
	private $pdf_planetario = 10;
	private $pdf_torpedo = 0;
	private $pdf_plasma = 0;
	private $bonus_defesa_invasao = 1;
	private $torpedos_sistema_estelar = false;
	private $torpedeiros_sistema_estelar = false;
	private $icone_torpedeiros_sistema_estelar = "";
	private $icone_torpedos_sistema_estelar = "";
	//***/

	private $processou_popula_variaveis_imperio = false;
	private $processou_pop = false;
	private $processou_pontuacao = false;
	private $processou_id_estrela_capital = false;
	private $processou_contatos_imperio = false;
	private $processou_logistica = false;
	private $processou_consome_poluicao = false;
	private $processou_bonus_recurso = false;
	
	private $processou_especial = [];

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
		if (method_exists($this, $name)) {
			$this->$name();
		} else {
			$this->popula_especial($name);
		}
		
		return $this->$name;
	}
	//***/

	/******************
	function popula_especial($nome_especial)
	-----------
	Popula variáveis especiais (baseados em Techs)
	$nome_especial -- nome da variável
	******************/	
	function popula_especial($nome_especial) {
		global $wpdb, $plugin_colonization;

		if (isset($this->processou_especial[$nome_especial])) {
			if ($this->processou_especial[$nome_especial]) {
				return $this->$nome_especial;
			}
		}

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		$query = "SELECT ct.id AS id, ct.especiais AS especiais, ct.icone
		FROM colonization_imperio_techs AS cit
		JOIN colonization_tech AS ct
		ON ct.id = cit.id_tech
		WHERE cit.id_imperio={$this->id} 
		AND cit.custo_pago = 0
		AND ct.especiais LIKE '%{$nome_especial}%'
		AND ct.parte_nave = false
		AND turno <= {$this->turno->turno}";
		
		$especiais_lista = $wpdb->get_results($query);

		if (!empty($especiais_lista)) {
			if ($this->$nome_especial === false) {
				$this->$nome_especial = true;
			} else {
				foreach($especiais_lista as $especial) {
					$array_especiais = $plugin_colonization->converter_para_array($especial->especiais);
					if (isset($array_especiais[$nome_especial])) {
						if (str_contains($nome_especial,"bonus") || str_contains($nome_especial,"pdf")) {
							$this->$nome_especial = $this->$nome_especial + $array_especiais[$nome_especial];
						} elseif ($this->$nome_especial < $array_especiais[$nome_especial]) {
							$this->$nome_especial = $array_especiais[$nome_especial];
						}
					}
				}
			}
		}
		
		$this->processou_especial[$nome_especial] = true;
		
		return $this->$nome_especial;
	}

	/******************
	function consome_poluicao()
	-----------
	Popula a variável
	******************/	
	function consome_poluicao() {
		global $wpdb;
		
		if ($this->processou_consome_poluicao) {
			return $this->consome_poluicao;
		}
		
		$this->popula_especial('bonus_consome_poluicao');
		$this->processou_consome_poluicao = true;
		return $this->consome_poluicao + $this->bonus_consome_poluicao;
	}
	
	/******************
	function pop()
	-----------
	Popula a variável
	******************/	
	function pop() {
		global $wpdb;
		
		if ($this->processou_pop) {
			return $this->pop;
		}
		
		$this->pop = $wpdb->get_var("SELECT 
		(CASE 
		WHEN SUM(pop) IS NULL THEN 0
		ELSE SUM(pop)
		END) AS pop
		FROM colonization_imperio_colonias
		WHERE id_imperio={$this->id}
		AND turno={$this->turno->turno}");
		
		$this->processou_pop = true;
		
		return $this->pop;
	}
	
	/******************
	function pontuacao()
	-----------
	Popula a variável
	******************/	
	function pontuacao() {
		global $wpdb;

		if ($this->processou_pontuacao) {
			return true;
		}
		
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
		
		$pontuacao = $wpdb->get_var("SELECT SUM(pop)+SUM(pop_robotica) FROM colonization_imperio_colonias WHERE id_imperio={$this->id} AND turno={$this->turno->turno}");
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
		//$this->pontuacao = $this->pontuacao + $pontuacao;
		//$this->pontuacao_desenvolvimento = $this->pontuacao_desenvolvimento + $pontuacao;
		
		//Industrializáveis contam à parte
		$id_industrializaveis = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='Industrializáveis'");
		$pontuacao = $wpdb->get_var("SELECT 
		SUM(cir.qtd) 
		FROM colonization_imperio_recursos AS cir
		WHERE cir.id_imperio={$this->id} AND cir.turno={$this->turno->turno} AND cir.id_recurso={$id_industrializaveis}");
		//$this->pontuacao = $this->pontuacao + $pontuacao;
		//$this->pontuacao_desenvolvimento = $this->pontuacao_desenvolvimento + $pontuacao;		
		
		//$pontuacao = $wpdb->get_var("SELECT SUM(qtd*tamanho*2 + PDF_laser + PDF_projetil + PDF_torpedo + blindagem + escudos + pesquisa + FLOOR(alcance/1.8))) AS pontuacao FROM colonization_imperio_frota WHERE id_imperio={$this->id} AND turno<={$this->turno->turno} AND turno_destruido<{$this->turno->turno}");
		//$pontuacao = $wpdb->get_var("SELECT SUM(qtd*tamanho) AS pontuacao FROM colonization_imperio_frota WHERE id_imperio={$this->id} AND turno<={$this->turno->turno} AND (turno_destruido > {$this->turno->turno} OR turno_destruido = 0)");
		$naves = $wpdb->get_results("SELECT qtd, custo FROM colonization_imperio_frota WHERE id_imperio={$this->id} AND turno<={$this->turno->turno} AND (turno_destruido > {$this->turno->turno} OR turno_destruido = 0)");
		$pontuacao = 0;
		foreach ($naves as $resultado) {
			$custo_nave = json_decode(stripslashes($resultado->custo), false, 512, JSON_UNESCAPED_UNICODE);
			foreach ($custo_nave as $recurso => $qtd_recurso) {
				$pontuacao = $pontuacao + $qtd_recurso;
			}
		}
		$this->pontuacao = $this->pontuacao + $pontuacao;
		$this->pontuacao_belica = $this->pontuacao_belica + $pontuacao;

		$pontuacao = $wpdb->get_var("SELECT TRUNCATE(SUM(custo)/3,0)
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
	
		$this->processou_pontuacao = true;
		return true;
	}
	
	/******************
	function id_estrela_capital()
	-----------
	Popula a variável
	******************/	
	function id_estrela_capital() {
		global $wpdb;
		
		if ($this->processou_id_estrela_capital) {
			return $this->id_estrela_capital;
		}
		
		$this->id_estrela_capital = $wpdb->get_var("
		SELECT ce.id
		FROM colonization_imperio_colonias AS cit
		JOIN colonization_planeta AS cp
		ON cp.id = cit.id_planeta
		JOIN colonization_estrela AS ce
		ON ce.id=cp.id_estrela
		WHERE cit.turno = {$this->turno->turno}
		AND cit.capital=true AND id_imperio = {$this->id}");
		
		$this->processou_id_estrela_capital = true;
		return $this->id_estrela_capital;
	}

	/******************
	function logistica()
	-----------
	Popula a variável
	******************/	
	function logistica() {
		global $wpdb, $plugin_colonization;

		if ($this->processou_logistica) {
			return $this->logistica;
		}

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		if (!isset($this->processou_especial['logistica']) || !isset($this->processou_especial['bonus_logistica'])) {
			$this->popula_especial('logistica');
			$this->popula_especial('bonus_logistica');
		}
		
		//Depois de pegar o alcance e o bônus de logística, soma os dois
		$this->logistica = $this->logistica + $this->bonus_logistica;
		
		$this->processou_logistica = true;
		return $this->logistica;
	}
	
	
	/******************
	function bonus_recurso()
	-----------
	Retorna o valor do bônus dos Recursos do Império
	$id_recurso - recurso desejado, '*' para todos
	******************/		
	function bonus_recurso($id_recurso) {
		global $wpdb, $plugin_colonization;

		if ($this->processou_bonus_recurso) {
			if (isset($this->bonus_recurso[$id_recurso])) {
				return $this->bonus_recurso[$id_recurso];
			}
			return false;
		}
		
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		$query = "SELECT ct.id AS id, ct.especiais AS especiais, ct.icone
		FROM colonization_imperio_techs AS cit
		JOIN colonization_tech AS ct
		ON ct.id = cit.id_tech
		WHERE cit.id_imperio={$this->id} 
		AND cit.custo_pago = 0
		AND ct.especiais LIKE '%produz_recurso%'
		AND ct.parte_nave = false
		AND turno <= {$this->turno->turno}";
		
		$especiais_lista = $wpdb->get_results($query);

		if (!empty($especiais_lista)) {
				foreach($especiais_lista as $especial) {
					$array_especiais = $plugin_colonization->converter_para_array($especial->especiais);
					if (isset($array_especiais['id_recurso'])) {
						//Sempre tem um id_recurso
						$ids_recurso = explode(",",$array_especiais['id_recurso']);

						//Popula o array
						foreach ($ids_recurso as $chave => $id_recurso) {
							if (empty($this->bonus_recurso[$id_recurso])) {
								$this->bonus_recurso[$id_recurso] = $array_especiais['produz_recurso'];
							} else {
								$this->bonus_recurso[$id_recurso] = $this->bonus_recurso[$id_recurso] + $array_especiais['produz_recurso'];
							}
							
							//O atributo 'extrativo' se aplica como TRUE quando o bônus for SÓ para extrativos, e não houver nenhuma outra Tech que tenha colocado esse atributo como FALSE
							if (isset($array_especiais['extrativo']) && !isset($this->extrativo[$id_recurso])) {
								$this->extrativo[$id_recurso] = true;
							} else {
								$this->extrativo[$id_recurso] = false;
							}
						}
					}				
				}
			}

		$this->processou_bonus_recurso = true;
		return $this->bonus_recurso[$id_recurso];
	}
	
	
	/******************
	function extrativo()
	-----------
	Retorna o valor da variável extrativo
	$id_recurso - recurso desejado, '*' para todos
	******************/		
	function extrativo($id_recurso) {
		$this->bonus_recurso($id_recurso);
		
		if (isset($this->extrativo[$id_recurso])) {
			return $this->extrativo[$id_recurso];
		}
		return false;
	}
	
	/******************
	function icone_torpedos_sistema_estelar()
	-----------
	Retorna o valor da variável
	******************/		
	function icone_torpedos_sistema_estelar() {
		$this->popula_especial('torpedos_sistema_estelar');
		
		if ($this->torpedos_sistema_estelar) {
			$this->icone_torpedeiros_sistema_estelar = " <div class='{$id->icone} tooltip'><span class='tooltiptext'>Torpedos Espaciais</span></div>";
			return " <div class='{$id->icone} tooltip'><span class='tooltiptext'>Torpedos Espaciais</span></div>";
		}
		return "";
	}


	/******************
	function icone_torpedos_sistema_estelar()
	-----------
	Retorna o valor da variável
	******************/		
	function icone_torpedeiros_sistema_estelar() {
		$this->popula_especial('torpedeiros_sistema_estelar');
		
		if ($this->torpedeiros_sistema_estelar) {
			$this->icone_torpedeiros_sistema_estelar = " <div class='{$id->icone} tooltip'><span class='tooltiptext'>Torpedeiros Espaciais</span></div>";
			return " <div class='{$id->icone} tooltip'><span class='tooltiptext'>Torpedeiros Espaciais</span></div>";
		}
		return "";
	}
	
	/***********************
	function popula_variaveis_imperio()
	----------------------
	Popula as variáveis do Império
	***********************/
	private function popula_variaveis_imperio() {
		global $wpdb;
		
		if ($this->processou_popula_variaveis_imperio) {
			return true;
		}

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}		
		
		$this->processou_popula_variaveis_imperio = true;
		
		if ($roles == "administrator" && $this->id == 0) {
			foreach ($this as $chave => $valor) {
				if (str_contains($chave, "mk_")) {
					$this->$chave = 10;
				}
			}
			$this->nivel_estacao_orbital = 10;			
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
			<td><div data-atributo='nome_jogador'>{$user->display_name}{$this->icones_html()}</div></td>
			<td><div data-atributo='nome' data-valor-original='{$this->nome}' data-editavel='true'>{$this->nome}</div></td>
			<td><div data-atributo='prestigio' data-valor-original='{$this->prestigio}' data-editavel='true' data-style='width: 40px;'>{$this->prestigio}</div></td>
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
		
		if ($this->processou_contatos_imperio) {
			return $this->contatos_imperio;
		}
		
		$this->contatos_imperio = $wpdb->get_results("
		SELECT DISTINCT ci.id, ci.nome 
		FROM colonization_imperio AS ci
		JOIN (SELECT DISTINCT id_imperio_contato FROM colonization_diplomacia WHERE id_imperio={$this->id}) AS cd
		ON ci.id = cd.id_imperio_contato
		ORDER BY ci.nome");
		
		$this->processou_contatos_imperio = true;
		return $this->contatos_imperio;
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
		
		$this->pop();
		$this->pontuacao();
		
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
		
		$html = "<table class='lista_colonias_imperio'>
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
			
			$html .= $colonia->lista_colonias_imperio($this);
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
				$colonia[$resultado->id]->planeta = $planeta[$colonia[$resultado->id]->id_planeta];
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

			$planeta_id_estrela[$colonia[$resultado->id]->id_planeta] = $colonia[$resultado->id]->id_estrela;
			$pop_colonia = $colonia[$resultado->id]->pop + $colonia[$resultado->id]->pop_robotica;
			
			$mdo_colonia[$resultado->id] = $this->acoes->mdo_planeta($colonia[$resultado->id]->id_planeta);
			
			$id_poluicao = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome = 'Poluição'");
			$balanco_poluicao_planeta = "";
					
			$diferenca = round((hrtime(true) - $start_time)/1E+6,0);
			$this->debug .= "imperio->exibe_lista_colonias -> Poluição da Colônia {$resultado->id}: {$this->acoes->recursos_balanco_planeta[$id_poluicao][$colonia[$resultado->id]->id_planeta]} {$diferenca}ms \n";
			
			$chave_alimento = array_search($id_alimento, $imperio_recursos->id_recurso);
			$alimentos = $this->acoes->recursos_balanco[$id_alimento] + $imperio_recursos->qtd[$chave_alimento];
			$nova_pop = $colonia[$resultado->id]->crescimento_colonia($this, $alimentos, $this->acoes->recursos_balanco[$id_alimento]);
			$balanco_poluicao_planeta = $this->acoes->recursos_balanco_planeta[$id_poluicao][$colonia[$resultado->id]->id_planeta];
				
			$html_planeta[$colonia[$resultado->id]->id_planeta] = $colonia[$resultado->id]->html_colonia($this, $mdo_colonia[$resultado->id], $nova_pop, $balanco_poluicao_planeta);
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
				
				$html_planetas_na_estrela = $estrela[$planeta_id_estrela[$id_planeta]]->pega_html_planetas_estrela(true,true);
				$html_sistema[$planeta_id_estrela[$id_planeta]] = "
				<div  style='margin-bottom: 5px;'>
					<div class='nome_estrela_nave' style='display: inline;'  onclick='return abre_div_planetas(event, this);'><span style='text-decoration: underline;'>Colônias em <span style='font-weight: 600; color: #4F4F4F;'>
					{$html_nome_estrela}</span></span> - MdO/Pop: {$mdo_sistema[$planeta_id_estrela[$id_planeta]]}/{$pop_sistema[$planeta_id_estrela[$id_planeta]]}
					{$html_defesas_sistema}</div><div class='lista_planetas_nave'>{$html_planetas_na_estrela}</div><br>";
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
			$primeira_classe = "";
			if ($mdo_disponivel_sistema > 0 && $mdo_disponivel_planeta > 0 && $this->turno->bloqueado == 1 && ($colonia[$id_colonia]->vassalo == 0 || ($colonia[$id_colonia]->vassalo == 1 && $roles == "administrator"))) {
				$ids_colonias = $wpdb->get_results("
				SELECT cic.id, cic.id_planeta 
				FROM colonization_imperio_colonias AS cic
				JOIN colonization_planeta AS cp
				ON cp.id = cic.id_planeta
				JOIN colonization_estrela AS ce
				ON ce.id = cp.id_estrela
				WHERE cic.id_imperio={$this->id} AND cic.turno={$this->turno->turno} AND cic.id != {$id_colonia}
				ORDER BY cic.capital DESC, (CASE WHEN ce.id = {$this->id_estrela_capital} THEN 0 ELSE 1 END), cic.vassalo ASC, ce.X, ce.Y, ce.Z, cp.inospito ASC, cp.posicao, cic.id_planeta
				");
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
					$classe_habitavel = "";
					if ($id_colonia_imperio->id != $id_colonia && ($colonia[$id_colonia_imperio->id]->vassalo == 0 || ($colonia[$id_colonia_imperio->id]->vassalo == 1 && $roles == "administrator"))) {
							if ($planeta[$colonia[$id_colonia_imperio->id]->id_planeta]->inospito == 0) {
								$classe_habitavel = "verde_escuro_bold";
							} elseif ($planeta[$colonia[$id_colonia_imperio->id]->id_planeta]->inospito == 1 && $planeta[$colonia[$id_colonia_imperio->id]->id_planeta]->pop_inospito > 0) {
								$classe_habitavel = "marrom_bold";
							}
							if ($lista_options_colonias == "" && $classe_habitavel != "") {
								$primeira_classe = $classe_habitavel;
							}
							$lista_options_colonias .= "<option data-atributo='id_colonia' value='{$id_colonia_imperio->id}' class='{$classe_habitavel}'>{$planeta[$colonia[$id_colonia_imperio->id]->id_planeta]->nome}</option> \n";
					}
				}
				$html_lista_planetas = "<b>Transferir</b> <input data-atributo='pop' data-ajax='true' data-valor-original='1' type='range' min='1' max='{$mdo_transfere}' value='1' oninput='return altera_pop_transfere(event, this);' style='width: 80px;'></input>&nbsp;&nbsp;&nbsp;<label data-atributo='pop' style='width: 30px;'>1</label>
				&nbsp; <b>Pop para</b> &nbsp; 
				<select class='select_lista_planetas {$primeira_classe}' onchange='return troca_classe_select(this);'>
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
			$html = substr_replace($html, $icone_cerco, 45, 0);
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
		
		if (!$this->processou_popula_variaveis_imperio) {
			//$this->popula_variaveis_imperio();
		}		
		
		foreach ($icones AS $icone) {
			$tech_icone = new tech($icone->id);
			
			if ($icone->custo_pago == 0) {
				$mostra_logistica = "";
				if (strpos($tech_icone->especiais,"logistica") !== false && strpos($tech_icone->especiais,"bonus_logistica") === false) {
					$mostra_logistica = " ".$this->logistica()."pc";
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

		
		if ($this->bonus_pop > 0) {
			$this->icones_html .= " <div class='fas fa-user-plus tooltip'>{$this->bonus_pop}%<span class='tooltiptext'>Bônus de população</span></div>";
		}
		
		return $this->icones_html;
	}
}
?>