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
	public $icone;
	public $especiais;
	public $limite = 0;
	public $nao_extrativo = false;
	public $bonus_extrativo = 0;
	//public $bonus_recurso = false;
	public $custos;
	public $id_tech_requisito;
	public $recursos_produz = [];
	public $recursos_produz_qtd = [];
	public $recursos_consome = [];
	public $recursos_consome_qtd = [];
	public $consumo_fixo = [];
	public $consumo_fixo_qtd = [];
	
	function __construct($id) {
		global $wpdb;
		
		$this->id = $id;

		$resultados = $wpdb->get_results("SELECT nome, descricao, id_tech, slots, autonoma, desguarnecida, sempre_ativa, oculta, icone, especiais, custos FROM colonization_instalacao WHERE id=".$this->id);
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
		$this->especiais = $resultado->especiais;
		$this->custos = $resultado->custos;
		
		$index = 0;
		$recursos = $wpdb->get_results("SELECT id_recurso, qtd_por_nivel FROM colonization_instalacao_recursos WHERE id_instalacao={$this->id} AND consome = false");
		foreach ($recursos as $recurso) {
			$this->recursos_produz[$index] = $recurso->id_recurso;
			$this->recursos_produz_qtd[$index] = $recurso->qtd_por_nivel;
			$index++;
		}
		
		$index = 0;
		$recursos = $wpdb->get_results("SELECT id_recurso, qtd_por_nivel FROM colonization_instalacao_recursos WHERE id_instalacao={$this->id} AND consome = true");
		foreach ($recursos as $recurso) {
			$this->recursos_consome[$index] = $recurso->id_recurso;
			$this->recursos_consome_qtd[$index] = $recurso->qtd_por_nivel;
			$index++;
		}
	
	
		//Especiais
		//limite=qtd -- determina quantas dessa Instalação podem ser construídas no planeta (default 0, para sem limite)
		$especiais = explode(";",$this->especiais);

		$limite = array_values(array_filter($especiais, function($value) {
			return strpos($value, 'limite') !== false;
		}));

		if (!empty($limite)) {
			$limite_valor = explode("=",$limite[0]);
			$this->limite = $this->limite + $limite_valor[1];
		}
		
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

		$consumo_fixo = array_values(array_filter($especiais, function($value) {
			return strpos($value, 'consumo_fixo') !== false;
		}));

		//consumo_fixo=11,100
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
}

?>