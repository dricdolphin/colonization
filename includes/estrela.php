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
	public $id;
	public $nome;
	public $descricao;
	public $X;
	public $Y;
	public $Z;
	public $tipo;
	public $colonias;
	public $ids_estrelas_destino;
	public $destinos_buracos_minhoca = [];
	
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

		$resultados = $wpdb->get_results("SELECT id, nome, descricao, X, Y, Z, tipo, ids_estrelas_destino FROM colonization_estrela WHERE id=".$this->id);
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
		$this->X = $resultado->X;
		$this->Y = $resultado->Y;
		$this->Z = $resultado->Z;
		$this->tipo = $resultado->tipo;
		$this->ids_estrelas_destino = $resultado->ids_estrelas_destino;
		$this->destinos_buracos_minhoca = explode(";",$this->ids_estrelas_destino);
		
		$this->colonias = $wpdb->get_var("
		SELECT COUNT(ce.id) FROM 
		colonization_planeta AS cp
		JOIN colonization_estrela AS ce
		ON ce.id = cp.id_estrela
		AND ce.id = {$this->id}
		JOIN colonization_imperio_colonias AS cic
		ON cic.id_planeta = cp.id"); //Pega se tem colônias
	}
	
	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;
		
		$estilo_colonias = "";
		if ($this->colonias > 0  ) {
			$estilo_colonias = "style='font-weight: bold;'";
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
			<td><div data-atributo='X' data-style='width: 100%;' data-editavel='true' data-valor-original='{$this->X}'>{$this->X}</div></td>
			<td><div data-atributo='Y' data-style='width: 100%;' data-editavel='true' data-valor-original='{$this->Y}'>{$this->Y}</div></td>
			<td><div data-atributo='Z' data-style='width: 100%;' data-editavel='true' data-valor-original='{$this->Z}' >{$this->Z}</div></td>
			<td><div data-atributo='tipo' data-valor-original='{$this->tipo}' data-editavel='true'>{$this->tipo}</div></td>
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
	
	/******************
	function html_planetas_na_estrela($id_estrela)
	-----------
	Retorna o HTML de todos os planetas que estão em uma estrela
	******************/	
	function pega_html_planetas_estrela($detalhes_planetas = true, $exibe_recursos_planetas=false, $turno_visitado=0) {
		global $wpdb;
		
		//Pega os planetas que orbitam a estrela
		$ids_planetas_estrela = $wpdb->get_results("
		SELECT id FROM colonization_planeta AS cp
		WHERE cp.id_estrela = {$this->id}
		ORDER BY cp.posicao, cp.id
		");

		$turno = new turno($turno_visitado);
		$html_planetas = "<div class='lista_planetas'>";
		foreach ($ids_planetas_estrela as $id_planeta) {
			$planeta = new planeta($id_planeta->id, $turno->turno);
			$icone_colonia = "";
			
			if ($planeta->classe == "Lua") {
				$planeta->icone_habitavel = "<div class='fas fa-moon tooltip' style='color: #912611; font-size: 0.85em;'>&nbsp;<span style='font-size: 1.18em;' class='tooltiptext'>Lua</span></div>";;
			} elseif ($planeta->classe == "Gigante Gasoso") {
				$planeta->icone_habitavel = "<div class='fas fa-planet-ringed tooltip' style='color: #912611; font-size: 0.85em;'>&nbsp;<span style='font-size: 1.18em;' class='tooltiptext'>Gigante Gasoso</span></div>";
			}

			$id_colonia = $wpdb->get_var("SELECT id FROM colonization_imperio_colonias WHERE id_planeta={$planeta->id} AND turno={$turno->turno}");
			if (!empty($id_colonia)) {
				$icone_colonia = "<div class='far fa-flag tooltip' style='font-size: 0.85em; top: -0.3em; right: -0.2em;'>&nbsp;<span style='font-size: 1.18em;' class='tooltiptext'>Colonizado</span></div>";;;
			}
			
			$html_planetas .= "{$planeta->posicao}-{$icone_colonia}{$planeta->icone_habitavel}{$planeta->nome}"; 
			if ($detalhes_planetas) {
				$html_planetas .= " ({$planeta->subclasse} - Tam: {$planeta->tamanho})";
			}
			
			if ($exibe_recursos_planetas) {
				$html_recursos_planeta = $planeta->exibe_recursos_planeta(true);
				$html_planetas .= "<div class='recursos_planeta'>{$html_recursos_planeta}</div>";
			}
			$html_planetas .= "; ";
		}
	
	return $html_planetas."</div>";
	}
}
?>