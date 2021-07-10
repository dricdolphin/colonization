<?php
/**************************
MODELO_NAVES.PHP
----------------
Cria o objeto "modelo_naves"
***************************/

//Classe "modelo_naves"
//Contém os dados dos modelos de naves
class modelo_naves
{
	public $id;
	public $id_imperio;
	public $nome_modelo;
	public $string_nave;
	public $texto_nave;
	public $texto_custo;
	public $turno;

	private $JSON_custo;
	private $JSON_atributos;
	
	/***********************
	function __construct()
	----------------------
	Inicializa os dados do turno
	***********************/
	function __construct($id=0) {
		global $wpdb;

		if ($id == 0) {
			$this->id = 0;
			$this->id_imperio = 0;
			return;
		}			
			
		$resultados = $wpdb->get_results("SELECT id, id_imperio, nome_modelo, string_nave, texto_nave, texto_custo, turno FROM colonization_modelo_naves WHERE id={$id}");
		$resultado = $resultados[0];
		
		$this->id = $id;
		$this->id_imperio = $resultado->id_imperio;
		$this->nome_modelo = $resultado->nome_modelo;
		$this->string_nave = $resultado->string_nave;
		$this->texto_nave = $resultado->texto_nave;
		$this->texto_custo = $resultado->texto_custo;
		$this->turno = $resultado->turno;
	}

	/***********************
	function __set()
	----------------------
	Função mágica __set
	***********************/
	function __set($propriedade, $valor) {
		
		$this->$propriedade = $valor;
	}
	
	/***********************
	function get()
	----------------------
	Função mágia __get
	***********************/
	function __get($propriedade) {

		return $this->$propriedade();
	}


	/***********************
	function lista_dados()
	----------------------
	Lista os dados do objeto
	***********************/	
	function lista_dados() {
		global $wpdb;
		
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}		

		$html_nome_imperio = "";
		if ($this->id_imperio != 0 && $roles == "administrator") {
			$imperio = new imperio($this->id_imperio);
			$html_nome_imperio = "Império '{$imperio->nome}'<br>";
		}
		
		$string_nave = json_decode(stripslashes($this->string_nave),true, JSON_UNESCAPED_UNICODE);
		$string_nave['nome_modelo'] = $this->nome_modelo;
		$string_nave['id'] = $this->id;
		$this->string_nave = json_encode($string_nave, JSON_UNESCAPED_UNICODE);
		
		$html = "<tr><td><input type='hidden' data-atributo='string_nave' value='{$this->string_nave}'></input>{$html_nome_imperio}{$this->nome_modelo}</td><td>{$this->texto_nave}</td><td>{$this->texto_custo}</td><td>{$this->turno}</td>
		<td>
		<a href='#' onclick='return carrega_nave(event, this, {$this->id_imperio});'>Carregar Modelo</a><br>
		<a href='#' onclick='return deleta_nave(event, this, {$this->id});'>Deletar Modelo</a></td></tr>";
	
		return $html;
	}
	
	/***********************
	function JSON_custo()
	----------------------
	Inicializa a varíável JSON_custo
	***********************/
	function JSON_custo() {
		global $wpdb;
		
		$custo_modelo = explode(" | ", trim($this->texto_custo));
		$custo_modelo_temp = [];
		foreach ($custo_modelo as $chave => $valor) {
			$valor_explode = explode(":", $valor);
			$chave_nome_recurso = trim($valor_explode[0]);
			if (intval(trim($valor_explode[1])) != 0) {
				$custo_modelo_temp[$chave_nome_recurso] = intval(trim($valor_explode[1]));
			}
		}

		$this->JSON_custo = $custo_modelo_temp;
		return $this->JSON_custo;
	}
	
	function JSON_atributos() {
		global $wpdb;

		$atributos_modelo = explode(";", $this->texto_nave);
		$atributos_modelo_temp = [];
		foreach ($atributos_modelo as $chave => $valor) {
			$valor_explode = explode(":", $valor);
			$chave_nome_atributo = strtolower(str_replace(" ","_", trim($valor_explode[0])));
			//Hardcoded -- tirar o acento do pdf_projétil
			if ($chave_nome_atributo == "pdf_projétil") {
				$chave_nome_atributo = "pdf_projetil";
			}
			if (intval(trim($valor_explode[1])) != 0) {
				$atributos_modelo_temp[$chave_nome_atributo] = intval(trim($valor_explode[1]));
			}
		}
		
		return $atributos_modelo_temp;		
	}
}
?>