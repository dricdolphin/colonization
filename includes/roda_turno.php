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
		$roles = $user->roles[0];
		
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
					$html .= "<a href='#' class='page-title-action colonization_admin_botao' onclick='return desbloquear_turno(event, this);'>DESBLOQUEAR TURNO</a></div>
					";
				} else {
					$diff = date_diff($data_atual, $proxima_semana);
					$proxima_semana = $proxima_semana->format('Y-m-d H:i:s');
					$html .= "O próximo Turno somente será liberado após {$proxima_semana}<br>";
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
				$imperio_recursos = new imperio_recursos($imperio->id);	
				
				//Vamos modificar os estoques!
				//Primeiro, CONSUME os Recursos dos Planetas
				$html .= "CONSUMINDO Recursos Planetários do {$imperio->nome}:<br>";
				foreach ($acoes->recursos_produzidos as $id_recurso => $qtd_produzido) {
					foreach ($acoes->recursos_produzidos_planeta[$id_recurso] as $id_planeta => $qtd_produzido_planeta) {
						//$chave = array_search($resultado->id_recurso,$imperio_recursos->id_recurso);
						$qtd = $imperio_recursos->qtd[$id_recurso] + $qtd_produzido_planeta;

						$recursos_disponivel = $wpdb->get_var("SELECT disponivel FROM colonization_planeta_recursos WHERE id_planeta={$id_planeta} AND id_recurso={$id_recurso} AND turno={$turno->turno}");
						if ($recursos_disponivel > 0) {
							$recursos_disponivel = $recursos_disponivel - $qtd_produzido_planeta;

							$html .= "INSERT INTO colonization_planeta_recursos SET id_planeta={$id_planeta}, id_recurso ={$id_recurso}, disponivel={$recursos_disponivel}, turno={$proximo_turno};<br>";
							$wpdb->query("INSERT INTO colonization_planeta_recursos SET id_planeta={$id_planeta}, id_recurso ={$id_recurso}, disponivel={$recursos_disponivel}, turno={$proximo_turno}");
						}
					}
				}
				
				$html .= "<br>FAZENDO O BALANÇO dos Recursos do {$imperio->nome}:<br>";
				
				//Faz o balanço dos resultados
				foreach ($imperio_recursos->id_recurso as $chave => $id_recurso) {
					$recurso = new recurso($id_recurso);
					if (empty($acoes->recursos_balanco[$id_recurso])) {
						$acoes->recursos_balanco[$id_recurso] = 0;
					}
					//$chave = array_search($resultado->id_recurso,$imperio_recursos->id_recurso);

					$id_alimento = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome = 'Alimentos'");
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
				
				$lista_id_colonias = $wpdb->get_results("SELECT id FROM colonization_imperio_colonias WHERE id_imperio={$imperio->id} AND turno={$turno->turno}");
				$id_poluicao = $wpdb->get_var("SELECT id FROM colonization_recurso WHERE nome = 'Poluição'");
				foreach ($lista_id_colonias as $id_colonia) {
					$colonia = new colonia($id_colonia->id);
					$planeta = new planeta($colonia->id_planeta);
	
					if (empty($acoes->recursos_produzidos_planeta[$id_poluicao][$planeta->id])) {
						$acoes->recursos_produzidos_planeta[$id_poluicao][$planeta->id] = 0;
					}
					
					if (empty($acoes->recursos_consumidos_planeta[$id_poluicao][$planeta->id])) {
						$acoes->recursos_consumidos_planeta[$id_poluicao][$planeta->id] = 0;
					}
					
					$poluicao = $colonia->poluicao +$acoes->recursos_produzidos_planeta[$id_poluicao][$planeta->id] -$acoes->recursos_consumidos_planeta[$id_poluicao][$planeta->id];
					$poluicao = $poluicao-25; //Os planetas conseguem reduzir a poluição em 25 todos os turnos
					
					if ($poluicao<0) {
						$poluicao=0;
					}
					
					//Aumenta a população
					//O aumento da população funciona assim: se houver comida sobrando DEPOIS do consumo, ela cresce em 5 por turno se pop<30, depois cresce 10 por turno até atingir (Tamanho do Planeta*10)
					//No entanto, a poluição reduz o crescimento populacional
					$nova_pop = $colonia->pop;
					if ($alimentos > $colonia->pop && $acoes->recursos_balanco[$id_alimento] > 0) {//Caso tenha alimentos suficientes E tenha balanço de alimentos positivo...
						if ($planeta->inospito == 0) {//Se for planeta habitável, a Pop pode crescer
							if ($poluicao <= 100) {//Se a poluição for maior que 100, a população não cresce
								$limite_pop_planeta = $planeta->tamanho*10; 
								//Caso o Império tenha uma Tech de Bônus Populacional...
								if ($imperio->max_pop >0) {
									$limite_pop_planeta	= $limite_pop_planeta*(1+($imperio->max_pop/100));
								}
								
								if ($colonia->pop <= $limite_pop_planeta) {//Tem espaço para crescer
									if ($colonia->pop <=24) {
										$nova_pop = $colonia->pop + 5*$imperio->crescimento_pop;
									} else {
										$nova_pop = $colonia->pop + 10*$imperio->crescimento_pop;
									}
									if ($nova_pop > $limite_pop_planeta) {
										$nova_pop = $limite_pop_planeta;
									}
								}
							}
						}
					} else {
						//Caso os Alimentos sejam menores que a Pop da colônia, a população CAI em 10%
						if ($alimentos < $colonia->pop) {
							$nova_pop = round(0.9*$colonia->pop);
						}
					}
				
					$html.= "INSERT INTO colonization_imperio_colonias SET poluicao={$poluicao}, pop={$nova_pop}, turno={$proximo_turno}, id_planeta={$colonia->id_planeta}, id_imperio={$colonia->id_imperio}, capital={$colonia->capital}<br>";
					$wpdb->query("INSERT INTO colonization_imperio_colonias SET poluicao={$poluicao}, pop={$nova_pop}, turno={$proximo_turno}, id_planeta={$colonia->id_planeta}, id_imperio={$colonia->id_imperio}, capital={$colonia->capital}");
				}

				//Registra a pesquisa das naves
				$html .= "<br>REGISTRANDO as Pesquisas das Naves...<br>";
				
				$frota = $wpdb->get_results("SELECT id FROM colonization_imperio_frota
				WHERE id_imperio = {$imperio->id} AND pesquisa=1");
				
				foreach ($frota as $id) {
					$nave = new frota($id->id);
					$id_estrela = $wpdb->get_var("SELECT id FROM colonization_estrela WHERE X={$nave->X} AND Y={$nave->Y} AND Z={$nave->Z}");
					
					if (!empty($id_estrela)) {
						$pesquisa_anterior = $wpdb->get_var("SELECT id FROM colonization_imperio_historico_pesquisa  WHERE id_imperio={$imperio->id} AND id_estrela={$id_estrela}");
						if (empty($pesquisa_anterior)) {//O sistema ainda não foi pesquisado, pode adicionar o bônus de pesquisa!
							$html.= "INSERT INTO colonization_imperio_historico_pesquisa SET id_imperio={$imperio->id}, id_estrela={$id_estrela}, turno={$proximo_turno}<br>";
							$wpdb->query("INSERT INTO colonization_imperio_historico_pesquisa SET id_imperio={$imperio->id}, id_estrela={$id_estrela}, turno={$proximo_turno}");
						}
					}
				}
			}
		
		//Ao terminar de rodar o Turno, muda o Turno para o próximo turno!
		$html .= "Atualizando Colônias dos NPCs e indo para o próximo Turno...<br>";
		$html.= "UPDATE colonization_imperio_colonias SET turno={$proximo_turno} WHERE id_imperio=0<br>";
		$html.= "UPDATE wp_postmeta SET meta_value='{$proxima_semana}' WHERE meta_key='_wpcdt_timer_date' AND post_id='419'<br>";
		$html.= "UPDATE wp_forum_topics SET closed = 0 WHERE name LIKE 'Turno {$proximo_turno}%'<br>";
		$html.= "INSERT INTO colonization_turno_atual SET id={$proximo_turno}, data_turno='{$proxima_semana}', encerrado=0, bloqueado=1<br>";
		
		$wpdb->query("UPDATE colonization_imperio_colonias SET turno={$proximo_turno} WHERE id_imperio=0");
		$wpdb->query("UPDATE wp_postmeta SET meta_value='{$proxima_semana}' WHERE meta_key='_wpcdt_timer_date' AND post_id='419'");
		$wpdb->query("UPDATE wp_forum_topics SET closed = 0 WHERE name LIKE 'Turno {$proximo_turno}%'");
		$wpdb->query("INSERT INTO colonization_turno_atual SET id={$proximo_turno}, data_turno='{$proxima_semana}', encerrado=0, bloqueado=1");		
		$this->concluido = true;
		} else {
			$html = "É NECESSÁRIO TER PRIVILÉGIOS ADMINISTRATIVOS PARA RODAR O TURNO!";
		}
		
		return $html;
	}
}
?>