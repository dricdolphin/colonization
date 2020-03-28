<?php
/**************************
FROTA.PHP
----------------
Cria o objeto "FROTA"
***************************/

//Classe "frota"
//Contém os dados da frota
class frota 
{
	public $id;
	public $id_imperio;
	public $nome;
	public $tipo;
	public $X;
	public $Y;
	public $Z;
	public $tamanho;
	public $velocidade;
	public $alcance;
	public $PDF_laser;
	public $PDF_projetil;
	public $PDF_torpedo;
	public $blindagem;
	public $escudos;
	public $poder_invasao;
	public $especiais;
	public $HP;
	public $qtd;
	public $turno;
	
	function __construct($id=0) {
		global $wpdb;
		
		if ($id == 0) {
			return false;
		}
		
		$this->id = $id;
		
		$resultados = $wpdb->get_results("SELECT id, id_imperio, nome, tipo, X, Y, Z, tamanho, velocidade, alcance, PDF_laser, PDF_projetil, PDF_torpedo,
		blindagem, escudos, poder_invasao, HP, qtd, turno, especiais
		FROM colonization_imperio_frota WHERE id={$this->id}");
		
		$resultado = $resultados[0];
		$this->id_imperio = $resultado->id_imperio;
		$this->nome = $resultado->nome;
		$this->tipo = $resultado->tipo;
		$this->X = $resultado->X;
		$this->Y = $resultado->Y;
		$this->Z = $resultado->Z;
		$this->tamanho = $resultado->tamanho;
		$this->velocidade = $resultado->velocidade;
		$this->alcance = $resultado->alcance;
		$this->PDF_laser = $resultado->PDF_laser;
		$this->PDF_projetil = $resultado->PDF_projetil;
		$this->PDF_torpedo = $resultado->PDF_torpedo;
		$this->blindagem = $resultado->blindagem;
		$this->escudos = $resultado->escudos;
		$this->poder_invasao = $resultado->poder_invasao;
		$this->HP = $resultado->HP;
		$this->qtd = $resultado->qtd;
		$this->turno = $resultado->turno;
		$this->especiais = $resultado->especiais;
	}
	

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;
		
		$html = "<td>
			<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
			<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='{$this->id_imperio}' value='{$this->id_imperio}'></input>
			<input type='hidden' data-atributo='where_clause' value='id'></input>
			<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
			<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>
			<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir esta Frota?'></input>
			<div data-atributo='nome' data-editavel='true' data-valor-original='{$this->nome}' data-style='width: 100px;'>{$this->nome}</div>
			<div><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a></div>
			</td>
			<td><div data-atributo='tipo' data-editavel='true' data-valor-original='{$this->tipo}' data-style='width: 100px;'>{$this->tipo}</div></td>
			<td><div data-atributo='qtd' data-editavel='true' data-valor-original='{$this->qtd}' data-style='width: 30px;'>{$this->qtd}</div></td>
			<td><div data-atributo='X' data-editavel='true' data-valor-original='{$this->X}' data-style='width: 30px;'>{$this->X}</div></td>
			<td><div data-atributo='Y' data-editavel='true' data-valor-original='{$this->Y}' data-style='width: 30px;'>{$this->Y}</div></td>
			<td><div data-atributo='Z' data-editavel='true' data-valor-original='{$this->Z}' data-style='width: 30px;'>{$this->Z}</div></td>
			<td><div data-atributo='tamanho' data-editavel='true' data-valor-original='{$this->tamanho}' data-style='width: 50px;'>{$this->tamanho}</div></td>
			<td><div data-atributo='PDF_laser' data-editavel='true' data-valor-original='{$this->PDF_laser}' data-style='width: 50px;'>{$this->PDF_laser}</div></td>
			<td><div data-atributo='PDF_torpedo' data-editavel='true' data-valor-original='{$this->PDF_torpedo}' data-style='width: 50px;'>{$this->PDF_torpedo}</div></td>
			<td><div data-atributo='PDF_projetil' data-editavel='true' data-valor-original='{$this->PDF_projetil}' data-style='width: 50px;'>{$this->PDF_projetil}</div></td>
			<td><div data-atributo='blindagem' data-editavel='true' data-valor-original='{$this->blindagem}' data-style='width: 50px;'>{$this->blindagem}</div></td>
			<td><div data-atributo='escudos' data-editavel='true' data-valor-original='{$this->escudos}' data-style='width: 50px;'>{$this->escudos}</div></td>
			<td><div data-atributo='velocidade' data-editavel='true' data-valor-original='{$this->velocidade}' data-style='width: 50px;'>{$this->velocidade}</div></td>
			<td><div data-atributo='alcance' data-editavel='true' data-valor-original='{$this->alcance}' data-style='width: 50px;'>{$this->alcance}</div></td>
			<td><div data-atributo='HP' data-editavel='true' data-valor-original='{$this->HP}' data-style='width: 50px;'>{$this->HP}</div></td>
			<td><div data-atributo='turno' data-editavel='true' data-valor-original='{$this->turno}' data-style='width: 50px;'>{$this->turno}</div></td>
			<td><div data-atributo='especiais' data-editavel='true' data-valor-original='{$this->especiais}' data-style='width: 120px;' data-branco='true'>{$this->especiais}</div></td>
			<td><div data-atributo='gerenciar'><a href='#' onclick='return copiar_objeto(event, this, {$this->id_imperio});'>Criar cópia</a></div></td>";
		
		return $html;
	}

	/***********************
	function exibe_frota()
	----------------------
	Exibe uma Nave
	***********************/
	function exibe_frota() {
		global $wpdb;
		
		//1 Estação Orbital "Orbit One" (1;8;9) - Tamanho 100; Velocidade 1; Alcance 0; PdF Laser 10/Torpedo 10; Blindagem 10; HP 1000; Especiais: (1) - Produz até 50 Equipamentos de Naves por turno
		$html = "<b>{$this->qtd} {$this->tipo} \"{$this->nome}\" ({$this->X};{$this->Y};{$this->Z})</b> - Tamanho: {$this->tamanho}; Velocidade: {$this->velocidade}; Alcance: {$this->alcance};";
		
		$html_armas = "";
		if ($this->PDF_laser >0) {
			$html_armas .= " PdF Laser: {$this->PDF_laser}/";
		}

		if ($this->PDF_torpedo >0) {
			$html_armas .= " PdF Torpedo: {$this->PDF_laser}/";
		}

		if ($this->PDF_projetil >0) {
			$html_armas .= " PdF Projétil: {$this->PDF_laser}/";
		}
		
		if ($html_armas != "") {
			$html_armas = substr($html_armas,0,-1);
			$html_armas .= ";";
		}
		$html .= $html_armas;
		
		if ($this->blindagem >0) {
			$html .= " Blindagem: {$this->blindagem};";
		}

		if ($this->escudos >0) {
			$html .= " Escudos: {$this->escudos};";
		}		

		$html .= " HP: {$this->HP};";
		
		if ($this->especiais == "") {
			$html = substr($html,0,-1);
		} else {
			$html .= " Especiais: {$this->especiais}";
		}

		return $html;
	}		

}
?>