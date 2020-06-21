<?php
/**************************
MISSOES.PHP
----------------
Cria o objeto "missões"
***************************/

//Classe "missoes"
//Mosta as Missões individuais e coletivas e gerencia o sistema de Missões (aprovar, rejeitar, sucesso, fracasso)
class missoes
{
	public $id;
	public $descricao;
	public $texto_sucesso;
	public $texto_fracasso;
	public $lista_recurso;
	public $qtd;
	public $id_imperio;
	public $id_imperios_aceitaram;
	public $id_imperios_rejeitaram;
	public $turno;
	public $ativo;
	public $turno_validade;
	
	/***********************
	function __construct()
	----------------------
	Inicializa os dados
	***********************/
	function __construct($id=0) {
		global $wpdb;
		
		if ($id == 0) {
			$this->id = 0;
			return;
		}
		

		$resultados = $wpdb->get_results("SELECT id, descricao, texto_sucesso, texto_fracasso, lista_recurso, qtd, 
		id_imperio, id_imperios_aceitaram, id_imperios_rejeitaram, turno, ativo, turno_validade
		FROM colonization_missao WHERE id={$id}");
		$resultado = $resultados[0];
		
		$this->id = $resultado->id;
		$this->descricao = $resultado->descricao;
		$this->texto_sucesso = $resultado->texto_sucesso;
		$this->texto_fracasso = $resultado->texto_fracasso;
		$this->lista_recurso = $resultado->lista_recurso;
		$this->qtd = $resultado->qtd;
		$this->id_imperio = $resultado->id_imperio;
		$this->id_imperios_aceitaram = $resultado->id_imperios_aceitaram;
		$this->id_imperios_rejeitaram = $resultado->id_imperios_rejeitaram;
		$this->turno = $resultado->turno;
		$this->ativo = $resultado->ativo;
		$this->turno_validade = $resultado->turno_validade;
	}
	
	function lista_dados() {
		global $wpdb;
	
		$ativo_checked = "";
		if ($this->ativo == 1) {
			$ativo_checked = "checked";
		}	
		
		$html = "<td>
			<input type='hidden' data-atributo='where_clause' value='id'></input>
			<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
			<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>
			<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir essa Missão'></input>
			<div data-atributo='id' data-ajax='true'>{$this->id}</div>
			<div data-atributo='gerenciar'><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a></div>
			</td>
		<td><div data-atributo='descricao' data-type='textarea' data-editavel='true' data-valor-original='{$this->descricao}' data-style='width: 140px; height: 40px;' data-id='descricao'>{$this->descricao}</div></td>
		<td><div data-atributo='texto_sucesso' data-type='textarea' data-editavel='true' data-valor-original='{$this->texto_sucesso}' data-style='width: 140px; height: 40px;' data-id='texto_sucesso'>{$this->texto_sucesso}</div></td>
		<td><div data-atributo='texto_fracasso' data-type='textarea' data-editavel='true' data-valor-original='{$this->texto_fracasso}' data-style='width: 140px; height: 40px;' data-id='texto_fracasso' data-branco='true'>{$this->texto_fracasso}</div></td>
		<td><div data-atributo='id_imperio' data-editavel='true' data-valor-original='{$this->id_imperio}'>{$this->id_imperio}</div></td>
		<td><div data-atributo='id_imperios_aceitaram' data-editavel='true' data-valor-original='{$this->id_imperios_aceitaram}' data-style='width: 80px;' data-branco='true'>{$this->id_imperios_aceitaram}</div></td>
		<td><div data-atributo='id_imperios_rejeitaram' data-editavel='true' data-valor-original='{$this->id_imperios_rejeitaram}' data-style='width: 80px;' data-branco='true'>{$this->id_imperios_rejeitaram}</div></td>
		<td><div data-atributo='ativo' data-editavel='true' data-type='checkbox' data-valor-original='{$this->ativo}'><input type='checkbox' data-atributo='ativo' data-ajax='true' {$ativo_checked} disabled></input></div></td>
		<td><div data-atributo='turno' data-editavel='true' data-style='width: 30px;' data-valor-original='{$this->turno}'>{$this->turno}</div></td>
		<td><div data-atributo='turno_validade' data-editavel='true' data-style='width: 30px;' data-valor-original='{$this->turno_validade}'>{$this->turno_validade}</div></td>
		";
	
		return $html;
	}
	
	function exibe_missao($id_imperio=0) {
		global $wpdb;
		
		//Verifica se o player já aceitou ou se rejeitou essa Missão. Uma missão REJEITADA pode ser aceita posteriormente, mas uma missão ACEITA não pode mais ser editada
		$id_imperios_aceitaram = explode(";",$this->id_imperios_aceitaram);
		$id_imperios_rejeitaram = explode(";",$this->id_imperios_rejeitaram);
		
		$aceitou = array_search($id_imperio,$id_imperios_aceitaram);
		$rejeitou = array_search($id_imperio,$id_imperios_rejeitaram);
		
		if (!empty($this->texto_fracasso)) {
			$html_fracasso = "<div><b>FRACASSO:</b> {$this->texto_fracasso}</div>";
		}
		
		$html_aceitar = "";
		$html_rejeitar = "";
		if ($this->ativo == 1 && $id_imperio != 0) {
			if ($aceitou === false) {
				$html_aceitar = "<a href='#' onclick='return aceita_missao(this,event,{$id_imperio},{$this->id});' style='color: #009922 !important;'> Aceitar a Missão</a>";
				if ($rejeitou === false) {
					$html_rejeitar = "<a href='#' onclick='return aceita_missao(this,event,{$id_imperio},{$this->id},false);' style='color: #DD0022 !important;'>REJEITAR a Missão</a>";
				} elseif ($rejeitou !== false) {
					$html_aceitar = "<span style='color: #DD0022 !important;'><b>MISSÃO REJEITADA!</b></span> => <a href='#' onclick='return aceita_missao(this,event,{$id_imperio},{$this->id});' style='color: #009922 !important;'> Aceitar a Missão</a>";
				}
			} else {
				$html_aceitar = "<span style='color: #009922 !important;'><b>MISSÃO ACEITA!</b></span>";
			}
		}
		
		
		$html_imperio = "";
		if ($id_imperio == 0 && $this->id_imperio != 0) {
			$imperio = new imperio($this->id_imperio);
			$html_imperio = "<i>({$imperio->nome})</i>";
		}
		
		$html = "<div>
			<div><b>MISSÃO:</b> {$this->descricao} {$html_imperio}</div>
			<div><b>SUCESSO:</b> {$this->texto_sucesso}</div>
			{$html_fracasso}
			<div>Missão deve ser concluída até o Turno {$this->turno_validade}</div>
			<div data-atributo='gerenciar'>{$html_aceitar} &nbsp; {$html_rejeitar}</div>
		</div>
		";
	
		return $html;

	}		
	
}
?>