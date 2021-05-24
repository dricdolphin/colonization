<?php
/**************************
RODA_TURNO.PHP
----------------
Responsável por "rodar" os turnos, ou seja, por alterar os dados do jogo.

Para acessá-lo, é necessário ter acesso de administrador do Wordpress e do Fórum.

Antes de rodar o turno, verifica qual a data do último turno e não libera 
para rodar caso não tenha passado pelo menos UMA SEMANA do último Turno
***************************/

//Classe "roda_turno"
//Contém as rotinas para rodar o turno
class roda_turno {
	public $concluido = false;
	
	function __construct() {

	}
	
	/******************
	function executa_roda_turno()
	-----------
	Roda o Turno
	******************/	
	function executa_roda_turno() {
		global $wpdb;
		
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		$html = "";
		//Rodar o turno é simples: primeiro, CRIAMOS todos os recursos, depois CONSUMIMOS todos os recursos, e por fim AUMENTAMOS a população dos planetas onde isso for possível
		//O sistema tem por peculiaridade executar "ações especiais", que dependem das Tecnologias e outros detalhes dos Impérios (EM IMPLEMENTAÇÃO)
		
		if ($roles == "administrator") {//Somente pode rodar o turno se for um Administrador
			$turno = new turno(); //Pega o turno atual
			$proximo_turno = $turno->turno + 1;
			$timezone = new DateTimeZone('America/Sao_Paulo');
			$proxima_semana = new DateTime($turno->data_turno, $timezone);
			$proxima_semana->modify('+7 days');

			//**
			if ($turno->bloqueado) {
				$html = "<div>Não é possível rodar o turno. Ele se encontra BLOQUEADO!<br>";
				$data_atual = new DateTime("now", $timezone);
				$data_atual_string = $data_atual->format('Y-m-d H:i:s');
				$proxima_semana_string = $proxima_semana->format('Y-m-d H:i:s');
				
				$diferenca_datas = $data_atual->diff($proxima_semana);
				
				//$html .= "Now: {$data_atual_string} / Próximo: {$proxima_semana_string} || {$diferenca_datas->invert} | {$diferenca_datas->h}";
				
				if ($diferenca_datas->invert == 1) {
					$html .= "<a href='#' class='page-title-action colonization_admin_botao' onclick='return desbloquear_turno(event, this);'>DESBLOQUEAR TURNO</a></div>";
				} else {
					$diff = date_diff($data_atual, $proxima_semana);
					$proxima_semana = $proxima_semana->format('Y-m-d H:i:s');
					$html .= "O próximo Turno deverá ser liberado após {$proxima_semana}, mas você pode liberar antes se desejar.<br>
					<a href='#' class='page-title-action colonization_admin_botao' onclick='return desbloquear_turno(event, this);'>DESBLOQUEAR TURNO</a></div>";
				}
				
				return $html;
			}
			//**/
			
			$proxima_semana = $proxima_semana->format('Y-m-d H:i:s');
			
			$imperios = $wpdb->get_results("SELECT id FROM colonization_imperio");
			foreach ($imperios as $id_imperio) {
				$html .= "<br>";
				$imperio = new imperio($id_imperio->id);
				$acoes = new acoes($imperio->id);
				$imperio->acoes = $acoes;
				$imperio_recursos = new imperio_recursos($imperio->id);	
				
				//Vamos modificar os estoques!
				//Primeiro, CONSUME os Recursos dos Planetas
				$html .= "CONSUMINDO Recursos Planetários do {$imperio->nome}:<br>";
				foreach ($acoes->recursos_produzidos as $id_recurso => $qtd_produzido) {
					foreach ($acoes->recursos_extraidos_planeta[$id_recurso] as $id_planeta => $qtd_extraidos_planeta) {
						//$chave = array_search($resultado->id_recurso,$imperio_recursos->id_recurso);
						$qtd = $imperio_recursos->qtd[$id_recurso] + $qtd_extraidos_planeta;

						$recursos_disponivel = $wpdb->get_var("SELECT disponivel FROM colonization_planeta_recursos WHERE id_planeta={$id_planeta} AND id_recurso={$id_recurso} AND turno={$turno->turno}");
						if ($recursos_disponivel > 0) {
							$recursos_disponivel = $recursos_disponivel - $qtd_extraidos_planeta;
							
							if ($recursos_disponivel < 0) {
								$acoes->recursos_produzidos_planeta[$id_recurso][$id_planeta] = $acoes->recursos_extraidos_planeta[$id_recurso][$id_planeta] + $recursos_disponivel;
								$recursos_disponivel = 0;
								if ($acoes->recursos_extraidos_planeta[$id_recurso][$id_planeta] < 0) {
									$acoes->recursos_extraidos_planeta[$id_recurso][$id_planeta] = 0;
								}
							}

							$html .= "INSERT INTO colonization_planeta_recursos SET id_planeta={$id_planeta}, id_recurso={$id_recurso}, disponivel={$recursos_disponivel}, turno={$proximo_turno};<br>";
							$wpdb->query("INSERT INTO colonization_planeta_recursos SET id_planeta={$id_planeta}, id_recurso={$id_recurso}, disponivel={$recursos_disponivel}, turno={$proximo_turno}");
						}
					}
				}
				
				$html .= "<br>FAZENDO O BALANÇO dos Recursos do {$imperio->nome}:<br>";
				
				//Faz o balanço dos resultados
				$id_alimento = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome = 'Alimentos'");
				foreach ($imperio_recursos->id_recurso as $chave => $id_recurso) {
					$recurso = new recurso($id_recurso);
					if (empty($acoes->recursos_balanco[$id_recurso])) {
						$acoes->recursos_balanco[$id_recurso] = 0;
					}
					//$chave = array_search($resultado->id_recurso,$imperio_recursos->id_recurso);
					
					if ($id_recurso == $id_alimento) {
						$alimentos = $imperio_recursos->qtd[$chave] + $acoes->recursos_balanco[$id_recurso];
					}
					
					if ($recurso->acumulavel == 0) {
						$acoes->recursos_balanco[$id_recurso] = 0;
					}
					$html .= "INSERT INTO colonization_imperio_recursos SET id_imperio={$imperio->id}, id_recurso ={$id_recurso}, qtd={$imperio_recursos->qtd[$chave]}+{$acoes->recursos_balanco[$id_recurso]}, turno={$proximo_turno}, disponivel={$imperio_recursos->disponivel[$chave]}<br>";
					$wpdb->query("INSERT INTO colonization_imperio_recursos SET id_imperio={$imperio->id}, id_recurso ={$id_recurso}, qtd={$imperio_recursos->qtd[$chave]}+{$acoes->recursos_balanco[$id_recurso]}, turno={$proximo_turno}, disponivel={$imperio_recursos->disponivel[$chave]}");
				}
				
				//Cria poluição
				$html .= "<br>POLUINDO as Colônias e Gerando nova MdO...<br>";
				
				$colonia = [];
				$planeta = [];
				
				$lista_id_colonias = $wpdb->get_results("SELECT id FROM colonization_imperio_colonias WHERE id_imperio={$imperio->id} AND turno={$turno->turno}");
				$id_poluicao = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome = 'Poluição'");
				foreach ($lista_id_colonias as $id_colonia) {
					if (empty($colonia[$id_colonia->id])) {
						$colonia[$id_colonia->id] = new colonia($id_colonia->id);	
					}
					
					if (empty($planeta[$colonia[$id_colonia->id]->id_planeta])) {
						$planeta[$colonia[$id_colonia->id]->id_planeta] = new planeta($colonia[$id_colonia->id]->id_planeta);	
					}
	
					if (empty($acoes->recursos_produzidos_planeta[$id_poluicao][$planeta[$colonia[$id_colonia->id]->id_planeta]->id])) {
						$acoes->recursos_produzidos_planeta[$id_poluicao][$planeta[$colonia[$id_colonia->id]->id_planeta]->id] = 0;
					}
					
					if (empty($acoes->recursos_consumidos_planeta[$id_poluicao][$planeta[$colonia[$id_colonia->id]->id_planeta]->id])) {
						$acoes->recursos_consumidos_planeta[$id_poluicao][$planeta[$colonia[$id_colonia->id]->id_planeta]->id] = 0;
					}
					
					$poluicao = $colonia[$id_colonia->id]->poluicao + $acoes->recursos_balanco_planeta[$id_poluicao][$planeta[$colonia[$id_colonia->id]->id_planeta]->id];
					
					if ($poluicao<0) {
						$poluicao=0;
					}
					
					//Aumenta a população
					$nova_pop = $colonia[$id_colonia->id]->crescimento_colonia($imperio, $alimentos, $imperio->acoes->recursos_balanco[$id_alimento]);
					
					$html.= "INSERT INTO colonization_imperio_colonias SET poluicao={$poluicao}, pop={$colonia[$id_colonia->id]->pop}+{$nova_pop}, pop_robotica={$colonia[$id_colonia->id]->pop_robotica}, id_planeta={$colonia[$id_colonia->id]->id_planeta}, id_imperio={$colonia[$id_colonia->id]->id_imperio}, capital={$colonia[$id_colonia->id]->capital}, vassalo={$colonia[$id_colonia->id]->vassalo}, turno={$proximo_turno}<br>";
					$wpdb->query("INSERT INTO colonization_imperio_colonias SET poluicao={$poluicao}, pop={$colonia[$id_colonia->id]->pop}+{$nova_pop}, pop_robotica={$colonia[$id_colonia->id]->pop_robotica}, id_planeta={$colonia[$id_colonia->id]->id_planeta}, id_imperio={$colonia[$id_colonia->id]->id_imperio}, capital={$colonia[$id_colonia->id]->capital}, vassalo={$colonia[$id_colonia->id]->vassalo}, turno={$proximo_turno}");
				}

				//Registra a pesquisa das naves
				$html .= "<br>REGISTRANDO as Pesquisas das Naves...<br>";
				
				$frota = $wpdb->get_results("SELECT id FROM colonization_imperio_frota
				WHERE id_imperio = {$imperio->id} AND pesquisa=1 AND turno_destruido=0");
				
				foreach ($frota as $id) {
					$nave = new frota($id->id);
					$id_estrela = $wpdb->get_var("SELECT id FROM colonization_estrela WHERE X={$nave->X} AND Y={$nave->Y} AND Z={$nave->Z}");
					
					if (!empty($id_estrela)) {
						$pesquisa_anterior = $wpdb->get_var("SELECT id FROM colonization_imperio_historico_pesquisa  WHERE id_imperio={$imperio->id} AND id_estrela={$id_estrela}");
						if (empty($pesquisa_anterior)) {//O sistema ainda não foi pesquisado, pode adicionar o bônus de pesquisa!
							$html.= "INSERT INTO colonization_imperio_historico_pesquisa SET id_imperio={$imperio->id}, id_estrela={$id_estrela}, turno={$proximo_turno}<br>";
							$wpdb->query("INSERT INTO colonization_imperio_historico_pesquisa SET id_imperio={$imperio->id}, id_estrela={$id_estrela}, turno={$proximo_turno}");
						}

						//Verifica se a Estrela já foi visitada, e se não foi marca como visitada
						$estrela_visitada = $wpdb->get_var("SELECT id FROM colonization_estrelas_historico WHERE id_imperio={$nave->id_imperio} AND id_estrela={$id_estrela}");
						if (empty($estrela_visitada)) {
							$html.= "INSERT INTO colonization_estrelas_historico SET id_imperio={$nave->id_imperio}, id_estrela={$id_estrela}, turno={$proximo_turno}<br>";
							$wpdb->query("INSERT INTO colonization_estrelas_historico SET id_imperio={$nave->id_imperio}, id_estrela={$id_estrela}, turno={$proximo_turno}");
						} else {
							$html.= "UPDATE colonization_estrelas_historico SET turno={$proximo_turno} WHERE id={$estrela_visitada}<br>";
							$wpdb->query("UPDATE colonization_estrelas_historico SET turno={$proximo_turno} WHERE id={$estrela_visitada}");
						}						
					}
				}
			}
		
		//Ao terminar de rodar o Turno, muda o Turno para o próximo turno!
		$html .= "Atualizando Colônias dos NPCs e indo para o próximo Turno...<br>";
		$colonias_npcs = $wpdb->get_results("SELECT id FROM colonization_imperio_colonias WHERE id_imperio=0 AND turno={$turno->turno}");
		
		foreach ($colonias_npcs as $id_colonia) {
			if (empty($colonia[$id_colonia->id])) {
				$colonia[$id_colonia->id] = new colonia($id_colonia->id);
			}
			$html.= "INSERT INTO colonization_imperio_colonias SET id_imperio={$colonia[$id_colonia->id]->id_imperio}, nome_npc='{$colonia[$id_colonia->id]->nome_npc}', id_planeta={$colonia[$id_colonia->id]->id_planeta}, capital={$colonia[$id_colonia->id]->capital}, pop={$colonia[$id_colonia->id]->pop}, pop_robotica={$colonia[$id_colonia->id]->pop_robotica}, poluicao={$colonia[$id_colonia->id]->poluicao}, turno={$proximo_turno}<br>";	
			$wpdb->query("INSERT INTO colonization_imperio_colonias SET id_imperio={$colonia[$id_colonia->id]->id_imperio}, nome_npc='{$colonia[$id_colonia->id]->nome_npc}', id_planeta={$colonia[$id_colonia->id]->id_planeta}, capital={$colonia[$id_colonia->id]->capital}, pop={$colonia[$id_colonia->id]->pop}, pop_robotica={$colonia[$id_colonia->id]->pop_robotica}, poluicao={$colonia[$id_colonia->id]->poluicao}, turno={$proximo_turno}");
		}
		
		//$html.= "UPDATE wp_postmeta SET meta_value='{$proxima_semana}' WHERE meta_key='_wpcdt_timer_date' AND post_id='419'<br>";
		$html.= "UPDATE wp_forum_topics SET closed = 0 WHERE name LIKE 'Turno {$proximo_turno}%'<br>";
		$html.= "INSERT INTO colonization_turno_atual SET id={$proximo_turno}, data_turno='{$proxima_semana}', encerrado=0, bloqueado=1<br>";
		
		//$wpdb->query("UPDATE wp_postmeta SET meta_value='{$proxima_semana}' WHERE meta_key='_wpcdt_timer_date' AND post_id='419'");
		$wpdb->query("UPDATE wp_forum_topics SET closed = 0 WHERE name LIKE 'Turno {$proximo_turno}%'");
		$wpdb->query("INSERT INTO colonization_turno_atual SET id={$proximo_turno}, data_turno='{$proxima_semana}', encerrado=0, bloqueado=1");		
		
		$lista_id_planetas = $wpdb->get_results("SELECT id FROM colonization_planeta");
		$html.= "Populando os novos recursos de todos os planetas...<br>";
		foreach ($lista_id_planetas as $ids_planeta) {
			//Cria TODOS os planetas para popular os recursos para o Turno atual. Pode demorar um pouco...
			$planeta = new planeta($ids_planeta->id);
		}
		
		$this->concluido = true;
		} else {
			$html = "É NECESSÁRIO TER PRIVILÉGIOS ADMINISTRATIVOS PARA RODAR O TURNO!";
		}
		
		return $html;
	}


	function aumenta_pop_colonias() {
		global $wpdb;
		$html = "";
		
		$turno = new turno();
		$turno_anterior = $turno->turno - 1;
		$id_imperios = $wpdb->get_results("SELECT id FROM colonization_imperio");
		$id_alimento = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome = 'Alimentos'");
		$id_poluicao = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome = 'Poluição'");
		$colonia = [];
		
		foreach ($id_imperios as $id_imperio) {
			$imperio = new imperio($id_imperio->id, $turno_anterior );
			$acoes = new acoes($imperio->id, $turno_anterior);
			$imperio->acoes = $acoes;
			$imperio_recursos = new imperio_recursos($imperio->id, $turno_anterior);				
			
			$chave_alimento = array_search($id_alimento,$imperio_recursos->id_recurso);
			$ids_colonias = $wpdb->get_results("SELECT id FROM colonization_imperio_colonias WHERE id_imperio={$imperio->id} AND turno={$turno_anterior}");
			$alimentos = $imperio_recursos->qtd[$chave_alimento] + $acoes->recursos_balanco[$id_alimento];
			//$html .= "<br><br>qtd_alimentos: {$imperio_recursos->qtd[$chave_alimento]} + balanco_alimentos: {$acoes->recursos_balanco[$id_alimento]}<br>";
			foreach ($ids_colonias as $id_colonia) {
				$colonia[$id_colonia->id] = new colonia($id_colonia->id);
				$nova_pop = $colonia[$id_colonia->id]->crescimento_colonia($imperio, $alimentos, $imperio->acoes->recursos_balanco[$id_alimento]);
				$poluicao = $colonia[$id_colonia->id]->poluicao + $acoes->recursos_balanco_planeta[$id_poluicao][$colonia[$id_colonia->id]->id_planeta];
				if ($poluicao < 0) {
					$poluicao = 0;
				}
				$html .= "UPDATE colonization_imperio_colonias SET poluicao={$poluicao} WHERE id_planeta={$colonia[$id_colonia->id]->id_planeta} AND id_imperio={$colonia[$id_colonia->id]->id_imperio} AND turno={$turno->turno};<br>";
				//$wpdb->query("UPDATE colonization_imperio_colonias SET poluicao={$colonia[$id_colonia->id]->poluicao}, pop={$colonia[$id_colonia->id]->pop}+{$nova_pop}, pop_robotica={$colonia[$id_colonia->id]->pop_robotica}, id_planeta={$colonia[$id_colonia->id]->id_planeta}, id_imperio={$colonia[$id_colonia->id]->id_imperio}, capital={$colonia[$id_colonia->id]->capital}, vassalo={$colonia[$id_colonia->id]->vassalo} WHERE id_planeta={$colonia[$id_colonia->id]->id_planeta} AND id_imperio={$colonia[$id_colonia->id]->id_imperio} AND turno={$turno->turno}");	
			}
		}
	
		return $html;
	}
}
?>