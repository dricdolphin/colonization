<?php
/**************************
TRANSFERE_RECURSO.PHP
----------------
Cria o objeto "transfere_recurso"
***************************/

//Classe "transfere_recurso"
//Permite a transferência de recursos
class transfere_recurso
{
	public $id;
	public $id_imperio_origem;
	public $nome_npc;
	public $id_imperio_destino;
	public $id_recurso;
	public $qtd;
	public $processado;
	public $turno;
	
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
		
		$resultados = $wpdb->get_results("SELECT id, id_imperio_origem, nome_npc, id_imperio_destino, id_recurso, qtd, processado, turno FROM colonization_imperio_transfere_recurso WHERE id={$id}");
		$resultado = $resultados[0];
		
		$this->id = $resultado->id;
		$this->id_imperio_origem = $resultado->id_imperio_origem;
		$this->nome_npc = $resultado->nome_npc;
		$this->id_imperio_destino = $resultado->id_imperio_destino;
		$this->id_recurso = $resultado->id_recurso;
		$this->qtd = $resultado->qtd;
		$this->processado = $resultado->processado;
		$this->turno = $resultado->turno;
	}
	
	function exibe_listas($id_imperio=0) {
		global $wpdb;
		
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		if ($id_imperio != 0) {
			$imperio = new imperio($id_imperio);
			$id_techs_envio = $wpdb->get_results("
			SELECT citr.id, citr.id_recurso, citr.qtd, citr.processado, citr.nome_npc, citr.id_imperio_origem, citr.id_imperio_destino, ci_origem.nome AS nome_imperio_origem, ci_destino.nome AS nome_imperio_destino
			FROM colonization_imperio_transfere_recurso AS citr
			LEFT JOIN colonization_imperio AS ci_origem
			ON ci_origem.id = citr.id_imperio_origem
			LEFT JOIN colonization_imperio AS ci_destino
			ON ci_destino.id = citr.id_imperio_destino			
			WHERE citr.id_imperio_origem = {$imperio->id}");

			$id_techs_recebidas = $wpdb->get_results("
			SELECT citr.id, citr.id_recurso, citr.qtd, citr.processado, citr.nome_npc, citr.id_imperio_origem, citr.id_imperio_destino, ci_origem.nome AS nome_imperio_origem, ci_destino.nome AS nome_imperio_destino
			FROM colonization_imperio_transfere_recurso AS citr
			LEFT JOIN colonization_imperio AS ci_origem
			ON ci_origem.id = citr.id_imperio_origem
			LEFT JOIN colonization_imperio AS ci_destino
			ON ci_destino.id = citr.id_imperio_destino
			WHERE citr.id_imperio_destino = {$imperio->id}
			AND citr.processado = true");
		} elseif ($roles == "administrator") {
			$id_techs_envio = $wpdb->get_results("			
			SELECT citr.id, citr.id_recurso, citr.qtd, citr.processado, citr.nome_npc, citr.id_imperio_origem, citr.id_imperio_destino, ci_origem.nome AS nome_imperio_origem, ci_destino.nome AS nome_imperio_destino
			FROM colonization_imperio_transfere_recurso AS citr
			LEFT JOIN colonization_imperio AS ci_origem
			ON ci_origem.id = citr.id_imperio_origem
			LEFT JOIN colonization_imperio AS ci_destino
			ON ci_destino.id = citr.id_imperio_destino");
			$id_techs_recebidas = [];
		}
		
		$recurso = [];
		$lista_techs_enviadas = "";
		foreach ($id_techs_envio as $id) {
			if ($id->processado == 1) {
				$processado = "<span style='color: #0E8836'>PROCESSADA!</span>";
			} else {
				$processado = "<span style='color: #E0BA44'>EM PROCESSAMENTO!</span>";
				if ($roles == "administrator") {
					$processado = "<a href='#'style='color: #E0BA44 !important;' onclick='return processa_recebimento_recurso(this, event,{$id->id});'>EM PROCESSAMENTO!</a>";
				}
			}
			if (empty($recurso[$id->id_recurso])) {
				$recurso[$id->id_recurso] = new recurso($id->id_recurso);
			}
			if ($id->id_imperio_origem == 0) {
				$id->nome_imperio_origem = $id->nome_npc;
			}
			
			$lista_techs_enviadas.= "<tr><td>{$recurso[$id->id_recurso]->nome}</td><td>{$id->qtd}</td><td>{$id->nome_imperio_origem}</td><td>{$id->nome_imperio_destino}</td><td>{$id->turno}</td><td>{$processado}</td></tr>";
		}

		$lista_techs_recebidas = "";
		foreach ($id_techs_recebidas as $id) {
			if ($id->processado == 1) {
				$processado = "<span style='color: #0E8836'>PROCESSADA!</span>";
			} else {
				$processado = "<span style='color: #E0BA44'>EM PROCESSAMENTO!</span>";
			}
			if (empty($recurso[$id->id_recurso])) {
				$recurso[$id->id_recurso] = new recurso($id->id_recurso);
			}
			if ($id->id_imperio_origem == 0) {
				$id->nome_imperio_origem = $id->nome_npc;
			}
			$lista_techs_recebidas.= "<tr><td>{$recurso[$id->id_recurso]->nome}</td><td>{$id->qtd}</td><td>{$id->nome_imperio_origem}</td><td>{$id->nome_imperio_destino}</td><td>{$id->turno}</td><td>{$processado}</td></tr>";
		}
		
		$listas['lista_techs_enviadas'] = $lista_techs_enviadas;
		$listas['lista_techs_recebidas'] = $lista_techs_recebidas;
		
		return $listas;
	
	}
	
	function exibe_autoriza() {
		global $wpdb;
		
		$imperio_origem = new imperio($this->id_imperio_origem, true);
		if ($this->id_imperio_origem == 0) {
			$imperio_origem->nome = $this->nome_npc;
		}
		
		$recurso = new recurso($this->id_recurso);
		$html = "<div>O {$imperio_origem->nome} lhe enviou {$this->qtd} '{$recurso->nome}'.<br> 
		<div><a href='#' style='font-weight: bold !important;' onclick='return processa_recebimento_recurso(this, event,{$this->id});'>OK, entendido!</a></div>";

		return $html;
	}
	
}