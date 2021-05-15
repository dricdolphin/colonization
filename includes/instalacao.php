<?php
/**************************
INSTALACAO.PHP
----------------
Cria o objeto "instalação" e mostra os dados da instalação
***************************/

//Classe "instalacao"
//Contém os dados da instalacao
class instalacao 
{
	public $id;
	public $nome;
	public $descricao;
	public $id_tech;
	public $slots;
	public $autonoma;
	public $desguarnecida;
	public $sempre_ativa;
	public $oculta;
	public $publica;
	public $icone;
	public $especiais;
	public $limite = 0;
	public $limite_sistema = 0;
	public $pop_inospito = false;
	public $nao_extrativo = false;
	public $bonus_extrativo = 0;
	public $nivel_maximo = false;
	public $somente_gigante_gasoso = false;
	//public $bonus_recurso = false;
	public $custos;
	public $id_tech_requisito;
	public $recursos_produz = [];
	public $recursos_produz_qtd = [];
	public $recursos_produz_qtd_comercio = [];	
	public $comercio_potencial = 0;
	public $recursos_consome = [];
	public $recursos_consome_qtd = [];
	public $consumo_fixo = [];
	public $consumo_fixo_qtd = [];
	public $comercio = false;
	public $comercio_processou = false;
	public $requer_instalacao_sistema = false;
	public $espacoporto = false;
	public $base_colonial = false;
	
	function __construct($id) {
		global $wpdb;
		
		$this->id = $id;

		$resultados = $wpdb->get_results("SELECT nome, descricao, id_tech, slots, autonoma, desguarnecida, sempre_ativa, oculta, publica, icone, especiais, custos FROM colonization_instalacao WHERE id=".$this->id);
		if (empty($resultados)) {
			$this->id = 0;
			return;
		}

		$resultado = $resultados[0];
		
		$this->nome = $resultado->nome;
		$this->descricao = $resultado->descricao;
		$this->id_tech = $resultado->id_tech;
		$this->slots = $resultado->slots;
		$this->autonoma = $resultado->autonoma;
		$this->desguarnecida = $resultado->desguarnecida;
		$this->sempre_ativa = $resultado->sempre_ativa;
		$this->icone = $resultado->icone;
		$this->oculta = $resultado->oculta;
		$this->publica = $resultado->publica;
		$this->especiais = $resultado->especiais;
		$this->custos = $resultado->custos;
		
		$recursos = $wpdb->get_results("SELECT id_recurso, qtd_por_nivel FROM colonization_instalacao_recursos WHERE id_instalacao={$this->id} AND consome = false");
		foreach ($recursos as $recurso) {
			$this->recursos_produz[] = $recurso->id_recurso;
			$this->recursos_produz_qtd[] = $recurso->qtd_por_nivel;
		}
		
		$recursos = $wpdb->get_results("SELECT id_recurso, qtd_por_nivel FROM colonization_instalacao_recursos WHERE id_instalacao={$this->id} AND consome = true");
		foreach ($recursos as $recurso) {
			$this->recursos_consome[] = $recurso->id_recurso;
			$this->recursos_consome_qtd[] = $recurso->qtd_por_nivel;
		}
	
	
		//Especiais
		$especiais = explode(";",$this->especiais);

		//Especiais: pop_inospito=qtd
		$pop_inospito = array_values(array_filter($especiais, function($value) {
			return strpos($value, 'pop_inospito') !== false;
		}));
			
		if (!empty($pop_inospito)) {
			$this->pop_inospito = true;
		}

		//somente_gigante_gasoso
		$somente_gigante_gasoso = array_values(array_filter($especiais, function($value) {
			return strpos($value, 'somente_gigante_gasoso') !== false;
		}));
		
		if (!empty($somente_gigante_gasoso)) {
			$this->somente_gigante_gasoso = true;
		}

		//espacoporto
		$espacoporto = array_values(array_filter($especiais, function($value) {
			return strpos($value, 'espacoporto') !== false;
		}));
		
		if (!empty($espacoporto)) {
			$this->espacoporto = true;
		}

		//base_colonial
		$base_colonial = array_values(array_filter($especiais, function($value) {
			return strpos($value, 'base_colonial') !== false;
		}));

		if (!empty($base_colonial)) {
			$this->base_colonial = true;
		}
		
		//requer_instalacao_sistema
		$requer_instalacao_sistema = array_values(array_filter($especiais, function($value) {
			return strpos($value, 'requer_instalacao_sistema') !== false;
		}));

		if (!empty($requer_instalacao_sistema)) {
			$requer_instalacao_sistema_valor = explode("=",$requer_instalacao_sistema[0]);
			$this->requer_instalacao_sistema = $requer_instalacao_sistema_valor[1];
		}

		//limite=qtd -- determina quantas dessa Instalação podem ser construídas no planeta (default 0, para sem limite)
		$limite = array_values(array_filter($especiais, function($value) {
			return strpos($value, 'limite') !== false;
		}));

		if (!empty($limite)) {
			$limite_valor = explode("=",$limite[0]);
			$this->limite = $this->limite + $limite_valor[1];
		}

		//limite_sistema=qtd -- determina quantas dessa Instalação podem ser construídas no planeta (default 0, para sem limite)
		$limite_sistema = array_values(array_filter($especiais, function($value) {
			return strpos($value, 'limite_sistema') !== false;
		}));

		if (!empty($limite_sistema)) {
			$limite_valor = explode("=",$limite[0]);
			$this->limite_sistema = $this->limite_sistema + $limite_valor[1];
		}

		//nivel_maximo=1
		$nivel_maximo = array_values(array_filter($especiais, function($value) {
			return strpos($value, 'nivel_maximo') !== false;
		}));

		if (!empty($nivel_maximo)) {
			$nivel_maximo_valor = explode("=",$nivel_maximo[0]);
			$this->nivel_maximo = $nivel_maximo_valor[1];
		}		
		
		//nao_extrativo
		$nao_extrativo = array_values(array_filter($especiais, function($value) {
			return strpos($value, 'nao_extrativo') !== false;
		}));

		if (!empty($nao_extrativo)) {
			//$nao_extrativo_valor = explode("=",$nao_extrativo[0]);
			$this->nao_extrativo = true;
		}
		
		$bonus_extrativo = array_values(array_filter($especiais, function($value) {
			return strpos($value, 'bonus_extrativo') !== false;
		}));

		if (!empty($bonus_extrativo)) {
			$bonus_extrativo_valor = explode("=",$bonus_extrativo[0]);
			$this->bonus_extrativo = $bonus_extrativo_valor[1];
		}		


		//consumo_fixo=11,100
		$consumo_fixo = array_values(array_filter($especiais, function($value) {
			return strpos($value, 'consumo_fixo') !== false;
		}));

		if (!empty($consumo_fixo)) {
			$consumo_fixo_valores = explode("=",$consumo_fixo[0]);
			$consumo_fixo_valores = $consumo_fixo_valores[1];
			
			$consumo_fixo_valores = array_filter(explode(",",$consumo_fixo_valores));
			
			$chave_consumo_fixo = 0;
			foreach ($consumo_fixo_valores as $chave => $id_recurso_qtd) {
				//O consomo fixo tem o formato consumo_fixo=id_recurso_1,qtd_recurso_1,id_recurso_2,qtd_recurso_2
				if ($chave % 2 == 1) {//Desse modo, pegamos apenas os arrays com chaves PARES
					continue;
				}
				
				$chave_id_recurso = $chave;
				$chave_qtd = $chave+1;
				
				$this->consumo_fixo[$chave_consumo_fixo] = $consumo_fixo_valores[$chave_id_recurso];
				$this->consumo_fixo_qtd[$chave_consumo_fixo] = $consumo_fixo_valores[$chave_qtd];
				$chave_consumo_fixo++;
			}
			
		}	
		
		//comercio=1
		$comercio = array_values(array_filter($especiais, function($value) {
			return strpos($value, 'comercio') !== false;
		}));

		
		if (!empty($comercio)) {//Esta é uma instalação comercial. Ela gera Pesquisa e Industrializáveis, dependendo da Pop da colônia e do número de colônias dentro do alcance
			if (!empty($comercio)) {
				$comercio_valor = explode("=",$comercio[0]);
				$this->comercio = $comercio_valor[1];
			}
			
			//O bônus base é de 0 Pesquisas e 0 Industrializáveis
			$id_pesquisa = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='Pesquisa'");
			$id_industrializaveis = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='Industrializáveis'");
			$id_plasma = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='Plasma de Dobra'");
			
			$chave_pesquisa = array_search($id_pesquisa, $this->recursos_produz);
			if ($chave_pesquisa === false) {
				$chave_pesquisa = count($this->recursos_produz)+1;
				$this->recursos_produz[$chave_pesquisa] = $id_pesquisa;
				$this->recursos_produz_qtd[$chave_pesquisa] = 0;
				$this->recursos_produz_qtd_comercio[$chave_pesquisa] = 0;
			}
			
			$chave_industrializaveis = array_search($id_industrializaveis, $this->recursos_produz);
			if ($chave_industrializaveis === false) {
				$chave_industrializaveis = count($this->recursos_produz)+1;
				$this->recursos_produz[$chave_industrializaveis] = $id_industrializaveis;
				$this->recursos_produz_qtd[$chave_industrializaveis] = 0;
				$this->recursos_produz_qtd_comercio[$chave_industrializaveis] = 0;
			}

			$chave_plasma = array_search($id_plasma, $this->recursos_produz);
			if ($chave_plasma === false) {
				$chave_plasma = count($this->recursos_produz)+1;
				$this->recursos_produz[$chave_plasma] = $id_plasma;
				$this->recursos_produz_qtd[$chave_plasma] = 0;
				$this->recursos_produz_qtd_comercio[$chave_plasma] = 0;
			}
		}	
		
		//custo_instalacao=70;id_instalacao=29,57
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;
		
		if ($this->autonoma == 1) {
			$autonoma_checked = "checked";
		} else {
			$autonoma_checked = "";
		}

		if ($this->desguarnecida == 1) {
			$desguarnecida_checked = "checked";
		} else {
			$desguarnecida_checked = "";
		}

		if ($this->sempre_ativa == 1) {
			$sempre_ativa_checked = "checked";
		} else {
			$sempre_ativa_checked = "";
		}		
		
		if ($this->oculta == 1) {
			$oculta_checked = "checked";
		} else {
			$oculta_checked = "";
		}

		if ($this->publica == 1) {
			$publica_checked = "checked";
		} else {
			$publica_checked = "";
		}
		
		$tech = new tech($this->id_tech);
		//Exibe os dados do objeto
		$html = "				<td>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
				<input type='hidden' data-atributo='where_clause' value='id'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
				<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>
				<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir esta instalação e todas suas ligações (recursos produzidos, consumidos etc)?'></input>
				<div data-atributo='id' data-valor-original='{$this->id}'>{$this->id}</div>
				<div data-atributo='gerenciar'><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a></div>
			</td>
			<td><div data-atributo='nome' data-valor-original='{$this->nome}' data-editavel='true'>{$this->nome}</div></td>
			<td><div data-atributo='descricao' data-editavel='true' data-valor-original='{$this->descricao}'>{$this->descricao}</div></td>
			<td><div data-atributo='nome_tech' data-editavel='true' data-type='select' data-funcao='lista_techs_html' data-id-selecionado='{$this->id_tech}' data-valor-original='{$tech->nome}'>{$tech->nome}</div></td>
			<td><div data-atributo='slots' data-editavel='true' data-valor-original='{$this->slots}' data-style='width: 30px;'>{$this->slots}</div></td>
			<td><div data-atributo='autonoma' data-type='checkbox' data-editavel='true' data-valor-original='{$this->autonoma}'><input type='checkbox' data-atributo='autonoma' data-ajax='true' {$autonoma_checked} disabled></input></div></td>
			<td><div data-atributo='desguarnecida' data-type='checkbox' data-editavel='true' data-valor-original='{$this->desguarnecida}'><input type='checkbox' data-atributo='desguarnecida' data-ajax='true' {$desguarnecida_checked} disabled></input></div></td>
			<td><div data-atributo='sempre_ativa' data-type='checkbox' data-editavel='true' data-valor-original='{$this->sempre_ativa}'><input type='checkbox' data-atributo='sempre_ativa' data-ajax='true' {$sempre_ativa_checked} disabled></input></div></td>
			<td><div data-atributo='oculta' data-type='checkbox' data-editavel='true' data-valor-original='{$this->oculta}'><input type='checkbox' data-atributo='oculta' data-ajax='true' {$oculta_checked} disabled></input></div></td>
			<td><div data-atributo='publica' data-type='checkbox' data-editavel='true' data-valor-original='{$this->publica}'><input type='checkbox' data-atributo='publica' data-ajax='true' {$publica_checked} disabled></input></div></td>
			<td><div data-atributo='especiais' data-editavel='true' data-branco='true' data-valor-original='{$this->especiais}'>{$this->especiais}</div></td>
			<td><div data-atributo='icone' data-editavel='true' data-branco='true' data-valor-original='{$this->icone}'>{$this->icone}</div></td>
			<td><div data-atributo='custos' data-editavel='true' data-branco='true' data-valor-original='{$this->custos}'>{$this->custos}</div></td>
			<td><div data-atributo='gerenciar'><a href='#' onclick='return gerenciar_objeto(event, this);'>Gerenciar Objeto</a></div></td>";
		return $html;
	}
	


	/***********************
	function tech_requisito_upgrade()
	----------------------
	Verifica qual Tech é requisito para o Upgrade (se houver)
	***********************/
	function tech_requisito_upgrade($nivel_upgrade) {
		global $wpdb;
		
		$nivel = 1;
		$tech_requisito[$nivel] = new tech($this->id_tech); //Pega todos os níveis de Tech
		while ($tech_requisito[$nivel]->id != 0) {
			$id_tech_child = $wpdb->get_var("SELECT id FROM colonization_tech 
			WHERE id_tech_parent={$tech_requisito[$nivel]->id} 
			OR id_tech_parent LIKE '{$tech_requisito[$nivel]->id};%'
			OR id_tech_parent LIKE '%;{$tech_requisito[$nivel]->id};%'
			OR id_tech_parent LIKE '%;{$tech_requisito[$nivel]->id}'");
			
			$nivel++;
			if (!empty($id_tech_child)) {
				$tech_requisito[$nivel] = new tech($id_tech_child);
			} else {
				$tech_requisito[$nivel] = new tech(0);
			}
		}
	
		if (empty($tech_requisito[$nivel_upgrade])) {
			$tech_requisito[$nivel_upgrade] = new tech(0);
		}
		return $tech_requisito[$nivel_upgrade]->id;
	}

	/***********************
	function bonus_recurso($id_recurso)
	----------------------
	Retorna o valor de bonus de um recurso produzido no planeta onde a Instalação estiver (se houver)
	$id_recurso - id do recurso
	***********************/
	function bonus_recurso($id_recurso) {
		$especiais = explode(";",$this->especiais);

		$id_recurso_especiais = array_values(array_filter($especiais, function($value) {
			return strpos($value, "id_recurso") !== false;
		}));
		
		if (empty($id_recurso_especiais)) {
			return 0;
		} else {
			$id_recurso_especiais = explode("=",$id_recurso_especiais[0]);
			
			if ($id_recurso_especiais[1] != $id_recurso) {
				return 0;
			}
		}
		
		$bonus_recurso = array_values(array_filter($especiais, function($value) {
			return strpos($value, 'bonus_recurso') !== false;
		}));

		if (!empty($bonus_recurso)) {
			$bonus_recurso_valor = explode("=",$bonus_recurso[0]);
			return $bonus_recurso_valor[1];
		}
	}
	
	/***********************
	function html_custo()
	----------------------
	Retorna o HTML com o custo da Instalação
	
	***********************/	
	function html_custo() {
		$custos = explode(";",$this->custos);
		
		$html = "";
		foreach ($custos as $custo) {
			$dados_custo = explode("=",$custo);
			$recurso = new recurso($dados_custo[0]);
			
			
			$nome_recurso = $recurso->nome;
			$nome_tooltip = "";			
			if ($recurso->icone != "") {
				$nome_recurso = "<div class='{$recurso->icone}'></div>";
				$nome_tooltip = "{$recurso->nome}: ";
			}			
			$html .= "<div class='tooltip' style='display: inline-block;'>{$nome_recurso}: {$dados_custo[1]}; &nbsp;
			<span class='tooltiptext'>{$nome_tooltip}</span>
			</div>";
		}
		
		return $html;
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function produz_comercio($colonia_atual, $nivel_instalacao_atual) {
		global $wpdb;

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];	
		}		

		if (!$this->comercio) {//Só faz os cálculos se for uma Instalação comercial
			return false;
		}
		
		//$this->recursos_produz_qtd_comercio = [];
		$id_pesquisa = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='Pesquisa'");
		$id_industrializaveis = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='Industrializáveis'");		
		$id_plasma = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome='Plasma de Dobra'");		

		$chave_pesquisa = array_search($id_pesquisa, $this->recursos_produz);
		$chave_industrializaveis = array_search($id_industrializaveis, $this->recursos_produz);
		$chave_plasma = array_search($id_plasma, $this->recursos_produz);
		
		$this->recursos_produz_qtd_comercio[$chave_pesquisa] = 0;
		$this->recursos_produz_qtd_comercio[$chave_industrializaveis] = 0;
		$this->recursos_produz_qtd_comercio[$chave_plasma] = 0;
		
		$imperio = new imperio($colonia_atual->id_imperio);
		
		$planeta_atual = new planeta($colonia_atual->id_planeta);
		$estrela_atual = new estrela($planeta_atual->id_estrela);
		
		$ids_colonia_imperio = $wpdb->get_results("
		SELECT DISTINCT cic.id, cic.id_planeta, cp.id_estrela
		FROM colonization_imperio_colonias AS cic 
		JOIN colonization_planeta AS cp
		ON cp.id = cic.id_planeta
		WHERE cic.id_imperio={$imperio->id} AND cic.turno={$imperio->turno->turno}");
		
		$qtd_colonias = 0;
		foreach($ids_colonia_imperio as $ids_colonia) {
			if ($ids_colonia->id_planeta != $colonia_atual->id_planeta || $colonia_atual->capital == 1) {//O próprio planeta não conta para o bônus, exceto se for a Capital.
				if ($estrela_atual->distancia_estrela($ids_colonia->id_estrela) <= $imperio->alcance_logistica) { //Só colônias dentro do Alcance Logístico contam
					$qtd_colonias++;
					$this->recursos_produz_qtd_comercio[$chave_pesquisa] = $this->recursos_produz_qtd_comercio[$chave_pesquisa] + $this->comercio;
					$this->recursos_produz_qtd_comercio[$chave_industrializaveis] = $this->recursos_produz_qtd_comercio[$chave_industrializaveis] + $this->comercio;
					$this->recursos_produz_qtd_comercio[$chave_plasma] = $this->recursos_produz_qtd_comercio[$chave_plasma] + 10*+ $this->comercio;
				}
			}
		}
		if ($roles == "administrator") {
			//echo "Colônias contribuindo para o Comércio da Colônia {$colonia_atual->id}: {$qtd_colonias}<br>\n";
		}
		
		//TODO -- bônus para contato com outros Impérios
		$qtd_contatos = 0;
		$ids_imperios_contato = $wpdb->get_results("SELECT DISTINCT id_imperio_contato, nome_npc, acordo_comercial FROM colonization_diplomacia WHERE id_imperio={$imperio->id}");
		$fator_comercio = 1;
		if ($colonia_atual->capital != 1) {//O bônus com outros Impérios é menor fora da capital
			$fator_comercio = 0.1;
		} elseif ($this->comercio == 1) {
			$fator_comercio = 0; //Só Espaçoportos tem bônus de comercialização
		}

		foreach ($ids_imperios_contato as $imperios_contato) {
			//TODO -- no futuro pode haver bloqueios comerciais
			$qtd_contatos++;
			$this->recursos_produz_qtd_comercio[$chave_pesquisa] = $this->recursos_produz_qtd_comercio[$chave_pesquisa] + $this->comercio*$fator_comercio;
			$this->recursos_produz_qtd_comercio[$chave_industrializaveis] =  $this->recursos_produz_qtd_comercio[$chave_industrializaveis] + $this->comercio*$fator_comercio;
			$this->recursos_produz_qtd_comercio[$chave_plasma] = $this->recursos_produz_qtd_comercio[$chave_plasma] + $this->comercio*10*$fator_comercio;
			if ($imperios_contato->acordo_comercial == 1) {
				$qtd_contatos++;
				$this->recursos_produz_qtd_comercio[$chave_pesquisa] = $this->recursos_produz_qtd_comercio[$chave_pesquisa] + $this->comercio*$fator_comercio;
				$this->recursos_produz_qtd_comercio[$chave_industrializaveis] =  $this->recursos_produz_qtd_comercio[$chave_industrializaveis] + $this->comercio*$fator_comercio;
				$this->recursos_produz_qtd_comercio[$chave_plasma] = $this->recursos_produz_qtd_comercio[$chave_plasma] + $this->comercio*10*$fator_comercio;	
			}
		}
		
		//O valor produzido é sempre DIVIDIDO pelo nível da Instalação
		$this->recursos_produz_qtd_comercio[$chave_pesquisa] = ($this->recursos_produz_qtd_comercio[$chave_pesquisa]/$nivel_instalacao_atual);
		$this->recursos_produz_qtd_comercio[$chave_industrializaveis] =  ($this->recursos_produz_qtd_comercio[$chave_industrializaveis]/$nivel_instalacao_atual);
		$this->recursos_produz_qtd_comercio[$chave_plasma] = ($this->recursos_produz_qtd_comercio[$chave_plasma]/$nivel_instalacao_atual);

		$this->comercio_potencial = $this->recursos_produz_qtd_comercio[$chave_pesquisa];
		//Limita a quantidade de Recursos que as instalações Comerciais podem produzir
		if ($this->recursos_produz_qtd_comercio[$chave_pesquisa] > 10*$this->comercio) {
			$this->recursos_produz_qtd_comercio[$chave_pesquisa] = 10*$this->comercio;
			$this->recursos_produz_qtd_comercio[$chave_industrializaveis] = 10*$this->comercio;
			$this->recursos_produz_qtd_comercio[$chave_plasma] = 100*$this->comercio;
		}
		
		return true;
	}
}
?>