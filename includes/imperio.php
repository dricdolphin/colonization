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
	public $html_header;
	public $turno;
	
	//Todos esses atributos são na verdade relativos à Techs do Império e em teoria deveriam estar no objeto Imperio_Techs
	public $icones_html = "";
	public $max_pop = 0;
	public $bonus_recurso = [];
	public $sinergia = [];
	public $extrativo = [];
	public $max_bonus_recurso = [];
	public $bonus_pesquisa_naves = 0;

	/***********************
	function __construct($id, $super=false)
	----------------------
	Inicializa os dados do Império
	$id_imperio = null -- Se não for passado um valor, o valor padrão é o id de usuário
	$super -- Define se é para forçar o objeto (ignora as proteções)
	$turno -- Qual turno deve ser exibido
	***********************/
	function __construct($id, $super=false, $turno=0) {
		global $wpdb;
		$this->turno = new turno($turno);
		
		$user = wp_get_current_user();
		$roles = $user->roles[0];
		
		//Somente cria um objeto com ID diferente se o usuário tiver perfil de administrador
		if ((is_null($id) || $roles != "administrator") && !$super) {
			$this->id_jogador = get_current_user_id();
			$this->id = $wpdb->get_var("SELECT id FROM colonization_imperio WHERE id_jogador=".$this->id_jogador);
		} else {
			$this->id = $id;
			if (empty($this->id)) {
				return;
			}
			$this->id_jogador = $wpdb->get_var("SELECT id_jogador FROM colonization_imperio WHERE id=".$this->id);
		}
		

		
		$this->nome = $wpdb->get_var("SELECT nome FROM colonization_imperio WHERE id=".$this->id);
		$this->prestigio = $wpdb->get_var("SELECT prestigio FROM colonization_imperio WHERE id=".$this->id);
		
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
		
		$pontuacao = $wpdb->get_var("SELECT SUM(nivel)*10 
		FROM colonization_planeta_instalacoes AS cpi
		JOIN colonization_imperio_colonias  AS cic
		ON cic.id_planeta = cpi.id_planeta
		WHERE cic.id_imperio={$this->id}
		AND cic.turno = {$this->turno->turno}");
		$this->pontuacao = $this->pontuacao + $pontuacao;

		$pontuacao = $wpdb->get_var("SELECT SUM(pop) FROM colonization_imperio_colonias WHERE id_imperio={$this->id} AND turno={$this->turno->turno}");
		$this->pontuacao = $this->pontuacao + $pontuacao;

		$pontuacao = $wpdb->get_var("SELECT SUM(qtd) FROM colonization_imperio_recursos WHERE id_imperio={$this->id} AND turno={$this->turno->turno}");
		$this->pontuacao = $this->pontuacao + $pontuacao;
		
		$pontuacao = $wpdb->get_var("SELECT SUM(qtd*(tamanho*2 + PDF_laser + PDF_projetil + PDF_torpedo + blindagem + escudos + FLOOR(alcance/1.8))) AS pontuacao FROM colonization_imperio_frota WHERE id_imperio={$this->id}");
		$this->pontuacao = $this->pontuacao + $pontuacao;

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

		//Algumas Techs tem ícones, que devem ser mostrados do lado do nome do jogador
		$tech = new tech();
		$icones = $tech->query_tech(" AND ct.icone != ''", $this->id);
		$icone_html = [];
		
		foreach ($icones AS $icone) {
			$tech = new tech($icone->id);
			$custo_pago = $wpdb->get_var("SELECT custo_pago
			FROM colonization_imperio_techs AS cit
			WHERE cit.id_tech = {$tech->id} AND cit.id_imperio = {$this->id}");
			
			if ($custo_pago == 0) {
				if ($tech->id_tech_parent != 0) {
					$ids_tech_parent = explode(";",$tech->id_tech_parent);
					foreach ($ids_tech_parent as $chave => $id_tech) {
						if (!empty($icone_html[$id_tech])) {
							$icone_html[$id_tech] = " <div class='{$tech->icone} tooltip'><span class='tooltiptext'>{$tech->nome}</span></div>";
						}
					}
				} else {
					$icone_html[$tech->id] = " <div class='{$tech->icone} tooltip'><span class='tooltiptext'>{$tech->nome}</span></div>";
				}
				//$this->icones_html .= " <div class='{$tech->icone} tooltip'><span class='tooltiptext'>{$tech->nome}</span></div>";
			}
		}
		
		foreach ($icone_html as $chave => $html) {
			$this->icones_html .= $html;
		}
		
		//***********************************
		// ALTERAÇÕES DE TECH (ESPECIAIS)
		//***********************************
		//No momento existem as seguintes funções especiais para Techs:
		//max_pop=porcentagem -- tech que aumenta o máximo de pop de uma colônia 
		//
		//bonus_pesquisa_naves=valor -- tech que dá bonus nas pesquisas das naves
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

			//Especiais -- bonus_pesquisa_naves
			$bonus_pesquisa_naves = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'bonus_pesquisa_naves') !== false;
			}));
			
			if (!empty($bonus_pesquisa_naves)) {
				$bonus_pesquisa_naves = explode("=",$bonus_pesquisa_naves[0]);
				$this->bonus_pesquisa_naves = $this->bonus_pesquisa_naves + $bonus_pesquisa_naves[1];
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
		
		$colonias = $wpdb->get_results("SELECT id FROM colonization_imperio_colonias WHERE id_imperio={$this->id} AND turno={$this->turno->turno}");
		
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
	function exibe_recursos_atuais() {
		global $wpdb;
		
		$resultados = $wpdb->get_results("
		SELECT cir.qtd, cr.nome
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
			$html .= "{$resultado->nome} - {$resultado->qtd}; ";
		}
		
		return $html;
	}
	
	/***********************
	function exibe_lista_colonias()
	----------------------
	Exibe as Colônias atuais Império
	***********************/
	function exibe_lista_colonias() {
		global $wpdb;
		
		$resultados = $wpdb->get_results("
		SELECT cp.nome, cic.pop, cic.poluicao, cic.id_imperio, cic.id_planeta, cp.id_estrela
		FROM colonization_imperio_colonias AS cic
		JOIN colonization_planeta AS cp
		ON cp.id=cic.id_planeta
		JOIN colonization_estrela AS ce
		ON ce.id=cp.id_estrela
		WHERE cic.id_imperio = {$this->id}
		AND cic.turno = {$this->turno->turno}
		ORDER BY ce.X, ce.Y, ce.Z, cp.posicao, cic.id_planeta
		");
		
		$html_lista = "<b>Lista de Colônias</b><br>";
		$html_sistema = [];
		$html_planeta = [];
		$planeta_id_estrela = [];
		$mdo_sistema = [];
		$pop_sistema = [];

		if ($resultados[0]->id_imperio != "") {
			$imperio = new imperio($resultados[0]->id_imperio, false, $this->turno->turno);
			$acoes = new acoes($imperio->id, $this->turno->turno);
		}
		
		$mdo = 0;
		foreach ($resultados as $resultado) {
			$mdo = $acoes->mdo_planeta($resultado->id_planeta);
			$planeta_id_estrela[$resultado->id_planeta] = $resultado->id_estrela;
			if (empty($mdo_sistema[$planeta_id_estrela[$resultado->id_planeta]])) {
				$mdo_sistema[$planeta_id_estrela[$resultado->id_planeta]] = $mdo;
				$pop_sistema[$planeta_id_estrela[$resultado->id_planeta]] = $resultado->pop;
			} else {
				$mdo_sistema[$planeta_id_estrela[$resultado->id_planeta]] = $mdo_sistema[$planeta_id_estrela[$resultado->id_planeta]] + $mdo;
				$pop_sistema[$planeta_id_estrela[$resultado->id_planeta]] = $pop_sistema[$planeta_id_estrela[$resultado->id_planeta]] + $resultado->pop;
			}
			
			if ($resultado->poluicao < 25) {
				$poluicao = "<span style='color: #007426;'>{$resultado->poluicao}</span>";
			} elseif ($resultado->poluicao < 50) {
				$poluicao = "<span style='color: #ffce00;'>{$resultado->poluicao}</span>";
			} elseif ($resultado->poluicao < 75) {				
				$poluicao = "<span style='color: #f1711d;'>{$resultado->poluicao}</span>";
			} else {
				$poluicao = "<span style='color: #ee1509;'>{$resultado->poluicao}</span>";
			}
				$id_instalacoes = $wpdb->get_results("SELECT id_instalacao FROM colonization_planeta_instalacoes WHERE id_planeta = {$resultado->id_planeta} AND turno <= {$this->turno->turno}");
				
				$icones_planeta = "";
				foreach ($id_instalacoes as $id_instalacao) {
					$instalacao = new instalacao($id_instalacao->id_instalacao);
					
					if (!empty($instalacao->icone)) {
						$icones_planeta .= " <div class='{$instalacao->icone} tooltip'><span class='tooltiptext'>{$instalacao->nome}</span></div>";
					}
				}
				
				$planeta_id_estrela[$resultado->id_planeta] = $resultado->id_estrela;
				$html_planeta[$resultado->id_planeta] = "<span style='font-style: italic;'>{$resultado->nome}{$icones_planeta}</span> - MdO/Pop: {$mdo}/{$resultado->pop} - Poluição: {$poluicao}; ";
		}
		
		foreach ($html_planeta AS $id_planeta => $html) {
			if (empty($html_sistema[$planeta_id_estrela[$id_planeta]])) {
				$estrela = new estrela($planeta_id_estrela[$id_planeta]);
				$html_sistema[$planeta_id_estrela[$id_planeta]] = "Colônias em <span style='font-weight: 600; color: #4F4F4F;'>{$estrela->nome} ({$estrela->X};{$estrela->Y};{$estrela->Z})</span> - MdO/Pop: {$mdo_sistema[$planeta_id_estrela[$id_planeta]]}/{$pop_sistema[$planeta_id_estrela[$id_planeta]]}<br>";
			}
			$html_sistema[$planeta_id_estrela[$id_planeta]] .= $html;
		}
		
		foreach ($html_sistema AS $id_sistema => $html) {
			$html_lista .= $html."<br>";
		}
		
		return $html_lista;
	}
	
}
?>