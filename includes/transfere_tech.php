<?php
/**************************
TRANSFERE_TECH.PHP
----------------
Cria o objeto "transfere_tech"
***************************/

//Classe "transfere_tech"
//Permite a transferência de tecnologias
class transfere_tech
{
	public $id;
	public $id_imperio_origem;
	public $id_imperio_destino;
	public $id_tech;
	public $autorizado;
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
		
		$resultados = $wpdb->get_results("SELECT id, id_imperio_origem, id_imperio_destino, id_tech, autorizado, processado, turno FROM colonization_imperio_transfere_techs WHERE id={$id}");
		$resultado = $resultados[0];
		
		$this->id = $resultado->id;
		$this->id_imperio_origem = $resultado->id_imperio_origem;
		$this->id_imperio_destino = $resultado->id_imperio_destino;
		$this->id_tech = $resultado->id_tech;
		$this->autorizado = $resultado->autorizado;
		$this->processado = $resultado->processado;
		$this->turno = $resultado->turno;
	}
	
	function exibe_listas($id_imperio=0) {
		global $wpdb;
		
		$user = wp_get_current_user();
		$roles = $user->roles[0];		
		

		if ($id_imperio != 0) {
			$imperio = new imperio($id_imperio);
			$id_techs_envio = $wpdb->get_results("SELECT id FROM colonization_imperio_transfere_techs WHERE id_imperio_origem = {$imperio->id}");
			$id_techs_recebidas = $wpdb->get_results("
			SELECT id 
			FROM colonization_imperio_transfere_techs 
			WHERE id_imperio_destino = {$imperio->id}
			AND processado = true
			AND autorizado = true");
		} elseif ($roles == "administrator") {
			$id_techs_envio = $wpdb->get_results("SELECT id FROM colonization_imperio_transfere_techs");
			$id_techs_recebidas = [];
		}
		
		$lista_techs_enviadas = "";
		foreach ($id_techs_envio as $id) {
			$transfere_tech = new transfere_tech($id->id);
			if ($transfere_tech->processado == 1) {
				$processado = "<span style='color: #0E8836'>PROCESSADA!</span>";
			} else {
				$processado = "<span style='color: #E0BA44'>EM PROCESSAMENTO!</span>";
			}
			$tech = new tech($transfere_tech->id_tech);
			$imperio_destino = new imperio($transfere_tech->id_imperio_destino, true);
			$imperio_origem = new imperio($transfere_tech->id_imperio_origem, true);
			
			$lista_techs_enviadas.= "<tr><td>{$tech->nome}</td><td>{$imperio_origem->nome}</td><td>{$imperio_destino->nome}</td><td>{$transfere_tech->turno}</td><td>{$processado}</td></tr>";
		}

		$lista_techs_recebidas = "";
		foreach ($id_techs_recebidas as $id) {
			$transfere_tech = new transfere_tech($id->id);
			if ($transfere_tech->processado == 1) {
				$processado = "<span style='color: #0E8836'>PROCESSADA!</span>";
			} else {
				$processado = "<span style='color: #E0BA44'>EM PROCESSAMENTO!</span>";
			}
			$tech = new tech($transfere_tech->id_tech);
			$imperio_destino = new imperio($transfere_tech->id_imperio_destino, true);
			$imperio_origem = new imperio($transfere_tech->id_imperio_origem, true);
			$lista_techs_recebidas .= "<tr><td>{$tech->nome}</td><td>{$imperio_origem->nome}</td><td>{$imperio_destino->nome}</td><td>{$transfere_tech->turno}</td><td>{$processado}</td></tr>";
		}
		
		$listas['lista_techs_enviadas'] = $lista_techs_enviadas;
		$listas['lista_techs_recebidas'] = $lista_techs_recebidas;
		
		return $listas;
	
	}
	
	function exibe_autoriza() {
		global $wpdb;
		$imperio_origem = new imperio($this->id_imperio_origem, true);
		$tech = new tech($this->id_tech);
		$bonus = ceil(0.3*$tech->custo);
		$ressarce = ceil(0.1*$tech->custo);
		
		//Faz a validação da Tech
		//Verifica se o Império já tem essa Tech
		$html = "";
		$id_tech_imperio = $wpdb->get_var("SELECT id FROM colonization_imperio_techs WHERE id_imperio={$this->id_imperio_destino} AND id_tech={$this->id_tech}");
		
		if (!empty($id_tech_imperio)) {
			$imperio_tech = new imperio_techs($id_tech_imperio);
			
			$bonus_parcial = $bonus - $imperio_tech->custo_pago;
			if ($bonus_parcial < $ressarce) {
				$bonus_parcial = $ressarce;
			}
			
			$html = "<div>O {$imperio_origem->nome} lhe enviou a Tech '{$tech->nome}' porém você já possui essa tech.<br>
			Como compensação, você irá receber {$bonus_parcial} Pesquisa(s).</div>
			<div><a href='#' style='font-weight: bold !important;' onclick='return processa_recebimento_tech(this, event,{$this->id},null);'>OK, entendido!</a></div>";
		
			return $html;
		}
		
		//Verifica se o Império tem os pré-requisitos da Tech
		$tech = new tech($this->id_tech);
		if (!empty($tech->id_tech_parent)) {
			$id_tech_parent = str_replace(";",",",$tech->id_tech_parent);
			$tech_parent = $wpdb->get_var("SELECT COUNT(id) FROM colonization_imperio_techs WHERE id_imperio={$this->id_imperio_destino} AND id_tech IN ({$id_tech_parent})");
			if ($tech_parent == 0) {
				$id_tech_parent = explode(",",$id_tech_parent);
				$id_tech_parent = $id_tech_parent[0];
				$tech = new tech($id_tech_parent);
				
				$html = "<div>O {$imperio_origem->nome} lhe enviou a Tech '{$tech->nome}' porém você ão tem os pré-requisitos necessários! É necessário ter a Tech '{$tech->nome}'<br>
				Como compensação, você irá receber {$ressarce} Pesquisa(s).</div>
				<div><a href='#' style='font-weight: bold !important;' onclick='return processa_recebimento_tech(this, event,{$this->id},null);'>OK, entendido!</a></div>";
				
				return $html;
			}
			
			if (!empty($tech->lista_requisitos)) {
				foreach ($tech->id_tech_requisito as $chave => $id_requisito) {
					$tech_requisito = $wpdb->get_var("SELECT COUNT(id) FROM colonization_imperio_techs WHERE id_imperio={$this->id_imperio_destino} AND id_tech={$id_requisito}");
					if ($tech_requisito == 0) {
						if (empty($html)) {
							$html = "<div>O {$imperio_origem->nome} lhe enviou a Tech '{$tech->nome}' porém você não tem os pré-requisitos necessários! É necessário ter a(s) Tech(s):<br>";
						}
						$tech = new tech($id_requisito);
						$html .= $tech->nome."; ";
					}
				}
				if (!empty($html)) {
					$html .= "<br>
					Como compensação, você irá receber {$ressarce} Pesquisa(s).</div>
					<div><a href='#' style='font-weight: bold !important; color: #887F41 !important;' onclick='return processa_recebimento_tech(this, event,{$this->id},null);'>OK, entendido!</a></div>";
				}
			}
		}
		
		if (empty($html)) {
			$html = "<div>O {$imperio_origem->nome} lhe enviou a Tech '{$tech->nome}'. Deseja aceitá-la?<br>
			Caso rejeite, você irá receber {$ressarce} Pesquisa(s) como compensação.</div>
			<div><a href='#'style='color: #628049 !important;' onclick='return processa_recebimento_tech(this, event,{$this->id},true);'>Aceitar</a> &nbsp;&nbsp;&nbsp; 
			<a href='#' style='color: #D23535 !important;' onclick='return processa_recebimento_tech(this, event,{$this->id});',false>Rejeitar!</a>
			</div>";
		} 
		
		return $html;
	}
	
}