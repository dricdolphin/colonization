<?php
/**************************
ESTRELA.PHP
----------------
Cria o objeto "estrela" e mostra os dados da estrela
***************************/

//Classe "estrela"
//Contém os dados da estrela
class estrela
{
	//Dados provenientes do DB
	public $id;
	public $nome;
	public $descricao;
	public $comentarios;
	public $X;
	public $Y;
	public $Z;
	public $tipo;
	public $ids_estrelas_destino;
	public $anti_dobra = false;
	
	//Dados derivados de outras informações
	private $tem_stargate = false;
	private $icone_stargate = "";
	private $destinos_buracos_minhoca = [];

	//Flags de funções processadas
	private $processou_tem_stargate = false;
	private $processou_destinos_buracos_minhoca = false;
	
	/***********************
	function __construct($id_estrela)
	----------------------
	Inicializa os dados da Estrela
	$id_estrela = ID da estrela
	***********************/
	function __construct($id_estrela) {
		global $wpdb;
		
		if (empty($id_estrela)) {
			$this->id = 0;
			$this->nome = "";
			$this->X = 0;
			$this->Y = 0;
			$this->Z = 0;
			
			return;
		}
		$this->id = $id_estrela;

		$resultados = $wpdb->get_results("SELECT id, nome, descricao, comentarios, X, Y, Z, tipo, ids_estrelas_destino, cerco, anti_dobra FROM colonization_estrela WHERE id=".$this->id);
		if (empty($resultados)) {
			$this->id = 0;
			$this->X = -1;
			$this->Y = -1;
			$this->Z = -1;
			return;
		}
		
		$resultado = $resultados[0];
		
		$this->nome = $resultado->nome;
		$this->descricao = $resultado->descricao;
		$this->comentarios = $resultado->comentarios;
		$this->X = $resultado->X;
		$this->Y = $resultado->Y;
		$this->Z = $resultado->Z;
		$this->tipo = $resultado->tipo;
		$this->cerco = $resultado->cerco;
		$this->anti_dobra = $resultado->anti_dobra;
		$this->ids_estrelas_destino = $resultado->ids_estrelas_destino;
	}
	

	/***********************
	function __set()
	----------------------
	Seta o valor de uma variável privada
	***********************/
	function __set($name, $value) {
		
		$this->$name = $value;
	}

	/***********************
	function __get()
	----------------------
	Popula e retorna o valor de variáveis privadas
	***********************/
	function __get($name) {
		global $wpdb;
		
		if (method_exists($this, $name)) {
			$this->$name();
		}
		
		return $this->$name;
	}	
	
	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;
		
		$estilo_colonias = "";
		if (count($this->colonias_na_estrela()) > 0  ) {
			$estilo_colonias = "style='font-weight: bold;'";
		}

		if ($this->cerco == 1) {
			$cerco_checked = "checked";
		} else {
			$cerco_checked = "";
		}		
		
		//Exibe os dados do Império
		$html = "				<td>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
				<input type='hidden' data-atributo='where_clause' value='id'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
				<input type='hidden' data-atributo='funcao_validacao' value='valida_estrela'></input>
				<div data-atributo='id' data-valor-original='{$this->id}'>{$this->id}</div>
				<div data-atributo='gerenciar'><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='excluir_objeto(this,\"Deseja mesmo excluir esta estrela?\");'>Excluir</a></div>
			</td>
			<td><div data-atributo='nome' data-valor-original='{$this->nome}' data-editavel='true' {$estilo_colonias}>{$this->nome}</div></td>
			<td><div data-atributo='descricao' data-type='textarea' data-editavel='true' data-valor-original='{$this->descricao}' data-style='width: 190px; height: 50px;' data-id='descricao'>{$this->descricao}</div></td>
			<td><div data-atributo='comentarios' data-type='textarea' data-editavel='true' data-valor-original='{$this->comentarios}' data-style='width: 190px; height: 50px;' data-id='comentarios'>{$this->comentarios}</div></td>
			<td><div data-atributo='X' data-style='width: 100%;' data-editavel='true' data-valor-original='{$this->X}'>{$this->X}</div></td>
			<td><div data-atributo='Y' data-style='width: 100%;' data-editavel='true' data-valor-original='{$this->Y}'>{$this->Y}</div></td>
			<td><div data-atributo='Z' data-style='width: 100%;' data-editavel='true' data-valor-original='{$this->Z}' >{$this->Z}</div></td>
			<td><div data-atributo='tipo' data-valor-original='{$this->tipo}' data-editavel='true'>{$this->tipo}</div></td>
			<td><div data-atributo='cerco' data-type='checkbox' data-editavel='true' data-valor-original='{$this->cerco}'><input type='checkbox' data-atributo='cerco' data-ajax='true' {$cerco_checked} disabled></input></div></td>			
			<td><div data-atributo='ids_estrelas_destino' data-valor-original='{$this->ids_estrelas_destino}' data-editavel='true' data-branco='true'>{$this->ids_estrelas_destino}</div></td>
			<td><div data-atributo='gerenciar'><a href='#' onclick='return gerenciar_objeto(event, this);'>Gerenciar Objeto</a></div></td>";
		return $html;
	}
	
	/***********************
	function distancia_estrela($id_estrela)
	----------------------
	Calcula a distância entre a estrela atual e outra estrela
	***********************/
	function distancia_estrela($id_estrela) {
		global $wpdb;
		
		$estrela_destino = new estrela($id_estrela);
		$distancia = ceil((($this->X - $estrela_destino->X)**2 + ($this->Y - $estrela_destino->Y)**2 + ($this->Z - $estrela_destino->Z)**2)**0.5);
		$dx = ($this->X - $estrela_destino->X)**2;
		$dy = ($this->Y - $estrela_destino->Y)**2;
		$dz = ($this->Z - $estrela_destino->Z)**2;
		
		return $distancia;
	}
	
	/***********************
	function colonias_na_estrela()
	----------------------
	Pega as colônias na estrela
	***********************/
	function colonias_na_estrela($turno = 0) {
		global $wpdb;
		
		$turno = new turno($turno);
		return $wpdb->get_results("
		SELECT cic.id 
		FROM colonization_imperio_colonias AS cic
		JOIN colonization_planeta AS cp
		ON cp.id = cic.id_planeta
		WHERE cic.turno={$turno->turno} AND cp.id_estrela={$this->id}");
	}
	
	/******************
	function html_planetas_na_estrela($id_estrela)
	-----------
	Retorna o HTML de todos os planetas que estão em uma estrela
	******************/	
	function pega_html_planetas_estrela($detalhes_planetas = true, $exibe_recursos_planetas=false, $turno_visitado=0) {
		global $wpdb;

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		//Pega os planetas que orbitam a estrela
		$ids_planetas_estrela = $wpdb->get_results("
		SELECT id FROM colonization_planeta AS cp
		WHERE cp.id_estrela = {$this->id}
		ORDER BY cp.posicao, cp.id
		");

		$turno = new turno($turno_visitado);
		$imperio = new imperio();
		$id_imperio_colonizador = $wpdb->get_var("
		SELECT DISTINCT cic.id_imperio
		FROM colonization_imperio_colonias AS cic
		JOIN colonization_planeta AS cp
		ON cp.id = cic.id_planeta
		WHERE cp.id_estrela = {$this->id} AND cic.turno = {$turno->turno}");

		$html_planetas = "<div class='lista_planetas'>";
		foreach ($ids_planetas_estrela as $id_planeta) {
			$planeta = new planeta($id_planeta->id, $turno->turno);
			$icone_colonia = "";
			
			if ($planeta->classe == "Lua") {
				$planeta->icone_habitavel = "<div class='fas fa-moon tooltip' style='color: #912611; font-size: 0.85em;'>&nbsp;<span style='font-size: 1.18em;' class='tooltiptext'>Lua</span></div>";;
			} elseif ($planeta->classe == "Gigante Gasoso") {
				$planeta->icone_habitavel = "<div class='fas fa-planet-ringed tooltip' style='color: #912611; font-size: 0.85em;'>&nbsp;<span style='font-size: 1.18em;' class='tooltiptext'>Gigante Gasoso</span></div>";
			} else {
				$planeta->icone_habitavel();
			}

			$id_colonia = $wpdb->get_var("SELECT id FROM colonization_imperio_colonias WHERE id_planeta={$planeta->id} AND turno={$turno->turno}");
			if (!empty($id_colonia)) {
				$colonia = new colonia($id_colonia);
				if ($colonia->id_imperio == 0) {
					$nome_colonizador = $colonia->nome_npc;
				} else {
					$nome_colonizador = $wpdb->get_var("SELECT nome FROM colonization_imperio WHERE id={$colonia->id_imperio}");
				}
				
				$icone_colonia = "<div class='far fa-flag tooltip'>&nbsp;<span style='font-size: 1.18em;' class='tooltiptext'>Colonizado por '{$nome_colonizador}'</span></div>";;;
			}
			
			$link_nome_planeta = $planeta->nome;
			if (($imperio->id == $id_imperio_colonizador || $roles == "administrator") && $icone_colonia == "" && $detalhes_planetas && $exibe_recursos_planetas && $id_imperio_colonizador != 0) {
				$link_nome_planeta = "<a href='#' onclick='return coloniza_planeta(this, event, {$planeta->id}, {$id_imperio_colonizador});'><span class='tooltip'>{$planeta->nome}<span class='tooltiptext'>Criar Colônia</span></span></a>";
			}
			
			$html_planetas .= "<div class='planeta_na_lista'>{$planeta->posicao}-{$icone_colonia}{$planeta->icone_habitavel}{$link_nome_planeta}"; 
			if ($detalhes_planetas) {
				$html_planetas .= " ({$planeta->subclasse} - Tam: {$planeta->tamanho()})";
			}
			
			if ($exibe_recursos_planetas) {
				$html_recursos_planeta = $planeta->exibe_recursos_planeta(true);
				$html_planetas .= "<div class='recursos_planeta'>{$html_recursos_planeta}</div>";
			}

			$html_planetas .= "</div>";
		}
	
		if (empty($ids_planetas_estrela)) {
			$html_planetas .= "Espaço Profundo em {$this->nome}";
		}
		return $html_planetas."</div>";
	}
	
	/******************
	function tem_stargate()
	-----------
	Popula a variável
	******************/	
	function tem_stargate() {
		global $wpdb;
		
		if ($this->processou_tem_stargate) {
			return $this->tem_stargate;
		}
		
		//Verifica se tem um StarGate no sistema
		$turno = new turno();
		$id_stargate = $wpdb->get_var("SELECT ci.id FROM colonization_instalacao AS ci WHERE ci.especiais LIKE '%stargate=true%'");
		
		$tem_stargate = $wpdb->get_var("
		SELECT COUNT(cpi.id)
		FROM colonization_planeta_instalacoes AS cpi
		JOIN colonization_imperio_colonias AS cic
		ON cic.id_planeta = cpi.id_planeta
		AND cic.turno = {$turno->turno}
		JOIN colonization_planeta AS cp
		ON cp.id = cic.id_planeta
		AND cp.id_estrela = {$this->id}
		WHERE cpi.id_instalacao = {$id_stargate}
		AND cic.turno = {$turno->turno}
		");

		if ($tem_stargate > 0) {
			$this->tem_stargate = true;
			$this->icone_stargate = "<i class='fad fa-galaxy'></i>";
		}

		$this->processou_tem_stargate = true;
		return $this->tem_stargate;
	}
	
	/******************
	function icone_stargate()
	-----------
	Popula a variável
	******************/	
	function icone_stargate() {
		//Essa variável é populada pela função tem_stargate
		if ($this->processou_tem_stargate) {
			return $this->icone_stargate;
		}
		
		$this->tem_stargate();
		return $this->icone_stargate;
	}


	/******************
	function destinos_buracos_minhoca()
	-----------
	Popula a variável, pegando todos os buracos de minhoca E StarGates
	******************/
	function destinos_buracos_minhoca() {
		global $wpdb;
		
		if ($this->processou_destinos_buracos_minhoca) {
			return $this->destinos_buracos_minhoca;
		}
		
		$turno = new turno();
		$id_stargate = $wpdb->get_var("SELECT ci.id FROM colonization_instalacao AS ci WHERE ci.especiais LIKE '%stargate=true%'");
		
		$ids_estrelas_com_stargate = [];
		if ($this->tem_stargate() > 0) {
			//Se tiver, o StarGate está ligado a TODOS os outros StarGates do Império
			$id_imperio_na_estrela = $wpdb->get_var("
			SELECT cic.id_imperio
			FROM colonization_imperio_colonias AS cic
			JOIN colonization_planeta AS cp
			ON cp.id = cic.id_planeta
			AND cp.id_estrela = {$this->id}
			WHERE cic.turno = {$turno->turno}
			");
			
			if (!empty($id_imperio_na_estrela)) {
				$ids_stargates = $wpdb->get_results("
				SELECT cp.id_estrela
				FROM colonization_planeta_instalacoes AS cpi
				JOIN colonization_imperio_colonias AS cic
				ON cic.id_planeta = cpi.id_planeta
				AND cic.turno = {$turno->turno}
				JOIN colonization_planeta AS cp
				ON cp.id = cic.id_planeta
				WHERE cpi.id_instalacao = {$id_stargate}
				AND cic.id_imperio = {$id_imperio_na_estrela}
				AND cic.turno = {$turno->turno}
				AND cp.id_estrela != {$this->id}
				");	
				
				foreach ($ids_stargates as $id_stargates) {
					$ids_estrelas_com_stargate[] = $id_stargates->id_estrela;
				}
			}
		}

		$this->destinos_buracos_minhoca = array_values(array_filter(array_unique(array_merge(explode(";",$this->ids_estrelas_destino),$ids_estrelas_com_stargate))));
		
		$this->processou_destinos_buracos_minhoca = true;
		return $this->destinos_buracos_minhoca;
	}
}
?>