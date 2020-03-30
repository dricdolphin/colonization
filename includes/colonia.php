<?php
/**************************
COLONIA.PHP
----------------
Cria o objeto "colônia"
***************************/

//Classe "colonia"
//Contém os dados da colonia
class colonia 
{
	public $id;
	public $id_imperio;
	public $id_planeta;
	public $pop;
	public $poluicao;
	public $turno;
	public $planeta;
	public $estrela;
	
	function __construct($id, $turno=0) {
		global $wpdb;
		
		$this->id = $id;

		$resultados = $wpdb->get_results("SELECT id_imperio, id_planeta, pop, poluicao, turno FROM colonization_imperio_colonias WHERE id=".$this->id);
		$resultado = $resultados[0];				
		
		$this->id_imperio = $resultado->id_imperio;
		$this->id_planeta = $resultado->id_planeta;
		$this->pop = $resultado->pop;
		$this->poluicao = $resultado->poluicao;
		$this->turno = $resultado->turno;
		
		$this->planeta = new planeta($this->id_planeta);
		$this->estrela = new estrela($this->planeta->id_estrela);
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;

		//Exibe os dados do Império		
		$html = "		<td>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
				<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='{$this->id_imperio}' value='{$this->id_imperio}'></input>
				<input type='hidden' data-atributo='id_planeta' data-ajax='true' data-valor-original='{$this->id_planeta}' value='{$this->id_planeta}'></input>
				<input type='hidden' data-atributo='where_clause' value='id'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
				<input type='hidden' data-atributo='funcao_validacao' value='valida_colonia'></input>
				<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir este planeta e todas suas ligações (recursos, instalações etc)?'></input>
				<div data-atributo='id' data-ajax='true'>{$this->id}</div>
				<div><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a></div>
			</td>
			<td><div data-atributo='nome_planeta' data-editavel='true' data-type='select' data-funcao='lista_planetas_html' data-id-selecionado='{$this->id_planeta}' data-valor-original='{$this->planeta->nome} - {$this->estrela->X};{$this->estrela->Y};{$this->estrela->Z} / {$this->planeta->posicao}'>{$this->planeta->nome} - {$this->estrela->X};{$this->estrela->Y};{$this->estrela->Z} / {$this->planeta->posicao}</div></td>
			<td><div data-atributo='pop' data-editavel='true' data-valor-original='{$this->pop}' data-style='width: 60px;'>{$this->pop}</div></td>
			<td><div data-atributo='poluicao' data-editavel='true' data-valor-original='{$this->poluicao}' data-style='width: 30px;'>{$this->poluicao}</div></td>
			<td><div data-atributo='turno' data-editavel='true' data-valor-original='{$this->turno}' data-style='width: 30px;'>{$this->turno}</div></td>
			<td><div data-atributo='gerenciar'><a href='#' onclick='return gerenciar_objeto(event, this);'>Gerenciar Objeto</a></div></td>";
		
		return $html;
	}

	/***********************
	function exibe_recursos_colonia()
	----------------------
	Exibe os recursos da Colônia
	***********************/
	function exibe_recursos_colonia($turno=0) {
		global $wpdb;
		$turno = new turno($turno);
		
		$resultados = $wpdb->get_results("
		SELECT cr.nome, cpr.disponivel
		FROM colonization_planeta_recursos AS cpr
		JOIN colonization_imperio_colonias AS cic
		ON cic.id_imperio = {$this->id_imperio} 
		AND cic.id_planeta = cpr.id_planeta
		AND cic.turno=cpr.turno
		JOIN colonization_recurso AS cr
		ON cr.id = cpr.id_recurso
		JOIN colonization_imperio_recursos AS cir
		ON cir.id_recurso = cr.id
		AND cir.turno={$turno->turno}
		AND cir.id_imperio = {$this->id_imperio}
		WHERE cpr.id_planeta={$this->planeta->id}
		AND cpr.turno={$turno->turno}
		AND cir.disponivel = TRUE
		");
		
		$html = "";
		foreach ($resultados as $resultado) {
			$html .= "{$resultado->nome}: {$resultado->disponivel}, ";
		}
		
		if ($html != "") {
			$html = substr($html,0,-2);
		}
		
		return $html;
	}
}
?>