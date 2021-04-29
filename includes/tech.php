<?php
/**************************
TECH.PHP
----------------
Cria o objeto "tech" 
***************************/

//Classe "tech"
//Contém a lista de Techs
class tech 
{
	public $id;
	public $nome;
	public $descricao;
	public $custo;
	public $nivel;
	public $id_tech_parent;
	public $lista_requisitos;
	public $id_tech_requisito = [];
	public $id_tech_alternativa;
	public $belica;
	public $publica;
	public $especiais;
	public $icone;
	
	function __construct($id=0) {
		global $wpdb;
		
		$this->id = $id;
		
		if ($this->id == 0) {
			$this->nome = "";
			$this->id_tech_parent = 0;
			$this->lista_requisitos = "";
			return;
		}

		$resultados = $wpdb->get_results("SELECT nome, descricao, custo, nivel, id_tech_parent, lista_requisitos, belica, publica, especiais, icone 
		FROM colonization_tech WHERE id=".$this->id);
		$resultado = $resultados[0];
		
		$this->nome = $resultado->nome;
		$this->descricao = $resultado->descricao;
		$this->custo = $resultado->custo;
		$this->nivel = $resultado->nivel;
		$this->id_tech_parent = $resultado->id_tech_parent;
		$this->lista_requisitos = $resultado->lista_requisitos;
		$this->id_tech_requisito = explode(";",$resultado->lista_requisitos);
		$this->belica = $resultado->belica;
		$this->publica = $resultado->publica;
		$this->especiais = $resultado->especiais;
		$this->icone = $resultado->icone;
		
		$especiais = explode(";",$this->especiais);
		
		//Especiais -- tech_alternativa
		$tech_alternativa = array_values(array_filter($especiais, function($value) {
			return strpos($value, 'tech_alternativa') !== false;
		}));
		
		$this->id_tech_alternativa = 0;
		if (!empty($tech_alternativa)) {
			$tech_alternativa_valor = explode("=",$tech_alternativa[0]);
			$this->id_tech_alternativa = $tech_alternativa_valor[1];
		}
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;

		if ($this->belica == 1) {
			$belica_checked = "checked";
		} else {
			$belica_checked = "";
		}

		if ($this->publica == 1) {
			$publica_checked = "checked";
		} else {
			$publica_checked = "";
		}

		//Exibe os dados do Objeto
		$html = "				<td>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
				<input type='hidden' data-atributo='where_clause' value='id'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
				<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>
				<div data-atributo='id' data-valor-original='{$this->id}'>{$this->id}</div>
				<div data-atributo='gerenciar'><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a></div>
			</td>
			<td><div data-atributo='nome' data-editavel='true' data-valor-original='{$this->nome}'>{$this->nome}</div></td>
			<td><div data-atributo='descricao' data-editavel='true' data-valor-original='{$this->descricao}'>{$this->descricao}</div></td>
			<td><div data-atributo='nivel' data-editavel='true' data-valor-original='{$this->nivel}' data-style='width: 50px;'>{$this->nivel}</div></td>
			<td><div data-atributo='custo' data-editavel='true' data-valor-original='{$this->custo}' data-style='width: 50px;'>{$this->custo}</div></td>
			<td><div data-atributo='id_tech_parent' data-editavel='true' data-valor-original='{$this->id_tech_parent}' data-style='width: 30px;'>{$this->id_tech_parent}</div></td>
			<td><div data-atributo='lista_requisitos' data-editavel='true' data-branco='true' data-valor-original='{$this->lista_requisitos}' data-style='width: 100px;'>{$this->lista_requisitos}</div></td>
			<td><div data-atributo='belica' data-type='checkbox' data-editavel='true' data-valor-original='{$this->belica}'><input type='checkbox' data-atributo='belica' data-ajax='true' {$belica_checked} disabled></input></div></td>			
			<td><div data-atributo='publica' data-type='checkbox' data-editavel='true' data-valor-original='{$this->publica}'><input type='checkbox' data-atributo='publica' data-ajax='true' {$publica_checked} disabled></input></div></td>			
			<td><div data-atributo='especiais' data-editavel='true' data-branco='true' data-valor-original='{$this->especiais}'>{$this->especiais}</div></td>
			<td><div data-atributo='icone' data-editavel='true' data-branco='true' data-valor-original='{$this->icone}'>{$this->icone}</div></td>
			";			

		return $html;
	}
	
	/***********************
	function query_tech($where="",$join="")
	----------------------
	Pega todas as Techs
	----------------
	$where -- valor do where
	$id_imperio -- se for para pegar um império específico
	***********************/

	function query_tech($where="",$id_imperio=0) {
		global $wpdb;


		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		$tabela_colonization_tech = "colonization_tech";
		
		if ($id_imperio == 0) {
			$custo_pago = ", 0 as custo_pago";
			$join = "";
			if ($where == " AND ct.publica = 1") {//Coloca as Techs Públicas E as Techs que o Jogador tem acesso
				$imperio = new imperio();
				if ($imperio->id != 0) {
					$tabela_colonization_tech = "(SELECT DISTINCT ct.id, ct.id_tech_parent, ct.belica, ct.lista_requisitos, ct.nome, ct.nivel, ct.publica
					FROM (
						SELECT citp.id_tech AS id, ct.id_tech_parent, ct.belica, ct.lista_requisitos, ct.nome, ct.nivel, 1 AS publica
						FROM colonization_imperio_techs_permitidas AS citp
						JOIN colonization_tech AS ct
						ON ct.id=citp.id_tech
						WHERE citp.id_imperio = {$imperio->id}
						UNION
						SELECT ct.id, ct.id_tech_parent, ct.belica, ct.lista_requisitos, ct.nome, ct.nivel, ct.publica
						FROM colonization_tech AS ct
						WHERE ct.id NOT IN (SELECT citp.id_tech FROM colonization_imperio_techs_permitidas AS citp WHERE citp.id_imperio = {$imperio->id})
						) AS ct)";
				}
			}
		} else {
			$custo_pago = ", cit.custo_pago, cit.id_imperio";

			$join = "			
			JOIN colonization_imperio_techs AS cit
			ON cit.id_tech = ct.id
			";
			
			$where .= " AND cit.id_imperio = {$id_imperio}";
		}

		$nivel = 0;
		do {
			$nivel++;
			$lista_techs[$nivel] = $wpdb->get_results("
			SELECT ct.id, ct.id_tech_parent{$custo_pago} 
			FROM {$tabela_colonization_tech} AS ct
			{$join}
			WHERE ct.nivel = {$nivel}
			{$where}
			ORDER BY ct.belica, ct.lista_requisitos, ct.nome");

			/*** DEBUG ***
			if ($roles == "administrator" || $imperio->id == 7) {
				echo "
				SELECT ct.id, ct.id_tech_parent{$custo_pago} 
				FROM {$tabela_colonization_tech} AS ct
				{$join}
				WHERE ct.nivel = {$nivel}
				{$where}
				ORDER BY ct.belica, ct.lista_requisitos, ct.nome<br><br>";
			}
			//***/

		} while (!empty($lista_techs[$nivel]));
		
		//Nível máximo
		$nivel--;
		$lista_completa = [];
		$lista_completa_temp = [];
		
		$nivel=1;
		while (!empty($lista_techs[$nivel])) {
			foreach($lista_techs[$nivel] as $tech) {
				if ($nivel == 1) {
					$lista_completa[$tech->id] = $tech;
				} else {
					foreach ($lista_completa as $id_tech => $valor_tech) {
						$lista_completa_temp[$id_tech] = $valor_tech;
						$id_tech_parents = explode(";",$tech->id_tech_parent);
						foreach ($id_tech_parents as $chave => $id_tech_parent) {
							if ($id_tech_parent == $id_tech) {
								$lista_completa_temp[$tech->id] = $tech;
							}
						}
					}
					if (empty($lista_completa_temp[$tech->id])) {//Se não achou um Tech parente, coloca no final da lista
						$lista_completa_temp[$tech->id] = $tech;
					}
					
					$lista_completa = $lista_completa_temp;
					$lista_completa_temp = [];
				}
			}
			$nivel++;
		}

		return $lista_completa;
	}
}
?>