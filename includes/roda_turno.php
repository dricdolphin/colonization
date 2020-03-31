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
		//O sistema tem por peculiaridade executar "ações especiais", que dependem das Tecnologias e outros detalhes dos Impérios (A SER IMPLEMENTADO)
		//Uma das ações padrão é o consumo de Alimentos -- um por Pop de cada colônia
		
		if ($roles == "administrator") {//Somente pode rodar o turno se for um Administrador
			$turno = new turno(); //Pega o turno atual
			$proximo_turno = $turno->turno + 1;
			$proxima_semana = new DateTime($turno->data_turno);
			$proxima_semana->modify('+7 days');

			if ($turno->bloqueado) {
				$html = "<div>Não é possível rodar o turno. Ele se encontra BLOQUEADO!<br>";
				
				$data_atual = new DateTime("now");
				$diferenca_datas = $data_atual->diff($proxima_semana);
				
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
			
			$proxima_semana = $proxima_semana->format('Y-m-d H:i:s');
			
			$imperios = $wpdb->get_results("SELECT id FROM colonization_imperio");
			foreach ($imperios as $id_imperio) {
				$html .= "<br>";
				$imperio = new imperio($id_imperio->id);
				$imperio_recursos = new imperio_recursos($imperio->id);	

				//Vamos modificar os estoques!
				//Primeiro, CONSUME os Recursos dos Planetas
				$resultados = $wpdb->get_results(
				"SELECT cat.pop, cir.id_recurso, cpi.id_planeta, cr.nome, SUM(FLOOR((cir.qtd_por_nivel * cpi.nivel * cat.pop)/10)) AS producao
				FROM colonization_acoes_turno AS cat
				JOIN colonization_planeta_instalacoes AS cpi
				ON cpi.id = cat.id_planeta_instalacoes
				JOIN colonization_instalacao_recursos AS cir
				ON cir.id_instalacao = cat.id_instalacao
				JOIN colonization_recurso AS cr
				ON cir.id_recurso = cr.id
				WHERE cat.id_imperio={$imperio->id} AND cat.turno={$turno->turno} AND cir.consome=false AND cpi.turno_destroi IS NULL AND cr.extrativo=true
				GROUP BY cr.nome");
				
				$html .= "CONSUMINDO Recursos Planetários do Império {$imperio->id}:<br>";
				foreach ($resultados as $resultado) {
					$chave = array_search($resultado->id_recurso,$imperio_recursos->id_recurso);
					$qtd = $imperio_recursos->qtd[$chave] + $resultado->producao;

					$recursos_disponivel = $wpdb->get_var("SELECT disponivel FROM colonization_planeta_recursos WHERE id_planeta={$resultado->id_planeta} AND id_recurso={$resultado->id_recurso} AND turno={$turno->turno}");
					if ($recursos_disponivel > 0) {
						$recursos_disponivel = $recursos_disponivel - $resultado->producao;

					/***************************************************
					--- MODIFICAÇÕES ESPECIAIS NO BALANÇO DO TURNO ---
					***************************************************/
					//TODO -- Aqui entram os Especiais de cada Império
					//No caso, tenho apenas o "hard-coded" do Império 3
					if ($imperio->id == 3) {
						if ($wpdb->get_var("SELECT extrativo FROM colonization_recurso WHERE id={$resultado->id_recurso}") && $resultado->pop == 10) {
							$recursos_disponivel = $recursos_disponivel - 1;
						}
					}
						$html .= "INSERT INTO colonization_planeta_recursos SET id_planeta={$resultado->id_planeta}, id_recurso ={$resultado->id_recurso}, disponivel={$recursos_disponivel}, turno={$proximo_turno}<br>";
						$wpdb->query("INSERT INTO colonization_planeta_recursos SET id_planeta={$resultado->id_planeta}, id_recurso ={$resultado->id_recurso}, disponivel={$recursos_disponivel}, turno={$proximo_turno}");
					}
				}
				
				$html .= "<br>FAZENDO O BALANÇO dos Recursos do Império {$imperio->id}:<br>";
				//Depois, FAZ O BALANÇO dos recursos dos Estoques
				$resultados = $wpdb->get_results("
				SELECT pop, nome, id_recurso, (producao-consumo+estoque) AS balanco 
				FROM (
					SELECT tabela_produz.pop, cr.nome, cr.id as id_recurso, (CASE WHEN tabela_produz.producao IS NULL THEN 0 ELSE tabela_produz.producao END) AS producao, 
					(CASE WHEN tabela_consome.producao IS NULL THEN 0 ELSE tabela_consome.producao END) AS consumo, 
					cimr.qtd AS estoque 
					FROM colonization_recurso AS cr
					LEFT JOIN (
						SELECT cat.pop, cir.id_recurso, cat.turno, cat.id_imperio, SUM(FLOOR((cir.qtd_por_nivel * cpi.nivel * cat.pop)/10)) AS producao
						FROM 
						(SELECT turno, id_imperio, id_instalacao, id_planeta_instalacoes, id_planeta, pop
						FROM colonization_acoes_turno 
						WHERE id_imperio={$imperio->id} AND turno={$turno->turno}
						) AS cat
						JOIN colonization_planeta_instalacoes AS cpi
						ON cpi.id = cat.id_planeta_instalacoes
						JOIN colonization_instalacao_recursos AS cir
						ON cir.id_instalacao = cat.id_instalacao
						WHERE cir.consome=false AND cpi.turno_destroi IS NULL
						GROUP BY cir.id_recurso
					) AS tabela_produz
					ON tabela_produz.id_recurso = cr.id
					LEFT JOIN (
					SELECT cir.id_recurso, cat.turno, cat.id_imperio, SUM(FLOOR((cir.qtd_por_nivel * cpi.nivel * cat.pop)/10)) AS producao
						FROM 
						(SELECT turno, id_imperio, id_instalacao, id_planeta_instalacoes, id_planeta, pop
						FROM colonization_acoes_turno 
						WHERE id_imperio={$imperio->id} AND turno={$turno->turno}
						) AS cat
						JOIN colonization_planeta_instalacoes AS cpi
						ON cpi.id = cat.id_planeta_instalacoes
						JOIN colonization_instalacao_recursos AS cir
						ON cir.id_instalacao = cat.id_instalacao
						WHERE cir.consome=true AND cpi.turno_destroi IS NULL
						GROUP BY cir.id_recurso
					) AS tabela_consome
					ON tabela_consome.id_recurso = cr.id
					LEFT JOIN colonization_imperio_recursos AS cimr
					ON cimr.id_imperio = {$imperio->id}
					AND cimr.id_recurso = cr.id 
					AND cimr.turno = {$turno->turno}
					WHERE cr.local=false AND cr.acumulavel=true
				) AS tabela_balanco
				ORDER BY (producao-consumo) ASC
				");				
				
				
				//Faz o balanço dos resultados
				foreach ($resultados as $resultado) {
					$chave = array_search($resultado->id_recurso,$imperio_recursos->id_recurso);
					
					/***************************************************
					--- MODIFICAÇÕES ESPECIAIS NO BALANÇO DO TURNO ---
					***************************************************/
					//TODO -- Aqui entram os Especiais de cada Império
					//No caso, tenho apenas o "hard-coded" do Império 3
					if ($imperio->id == 3) {
						if ($wpdb->get_var("SELECT extrativo FROM colonization_recurso WHERE id={$resultado->id_recurso}") && $resultado->pop == 10) {
							$resultado->balanco = $resultado->balanco + 1;
						}
					}
					
					if ($resultado->id_recurso == 7) {//Se for ALIMENTO, precisamos alterar o valor do balanço, pois a população consome Alimentos
						$resultado->balanco = $resultado->balanco - $imperio->pop;
						$alimentos = $resultado->balanco;
					}
					
					$html .= "INSERT INTO colonization_imperio_recursos SET id_imperio={$imperio->id}, id_recurso ={$resultado->id_recurso}, qtd={$resultado->balanco}, turno={$proximo_turno}, disponivel={$imperio_recursos->disponivel[$chave]}<br>";
					$wpdb->query("INSERT INTO colonization_imperio_recursos SET id_imperio={$imperio->id}, id_recurso ={$resultado->id_recurso}, qtd={$resultado->balanco}, turno={$proximo_turno}, disponivel={$imperio_recursos->disponivel[$chave]}");
				}
				
				//Cria poluição
				$html .= "<br>POLUINDO as Colônias e Gerando nova MdO...<br>";
				
				$lista_id_colonias = $wpdb->get_results("SELECT id FROM colonization_imperio_colonias WHERE id_imperio={$imperio->id} AND turno={$turno->turno}");
				foreach ($lista_id_colonias as $id_colonia) {
					$colonia = new colonia($id_colonia->id);
					$planeta = new planeta($colonia->id_planeta);
					
					$poluicao_produz = $wpdb->get_var(" 
					SELECT SUM(FLOOR((cir.qtd_por_nivel * cpi.nivel * cat.pop)/10)) AS producao
					FROM 
					(SELECT turno, id_imperio, id_instalacao, id_planeta_instalacoes, id_planeta, pop
					FROM colonization_acoes_turno 
					WHERE id_imperio={$imperio->id} AND turno={$turno->turno} AND id_planeta={$colonia->id_planeta}
					) AS cat
					JOIN colonization_planeta_instalacoes AS cpi
					ON cpi.id = cat.id_planeta_instalacoes
					JOIN colonization_instalacao_recursos AS cir
					ON cir.id_instalacao = cat.id_instalacao
					WHERE cir.id_recurso=16 
					AND cir.consome = false
					AND  cpi.turno_destroi IS NULL
					GROUP BY cir.id_recurso");

					$poluicao_consome = $wpdb->get_var(" 
					SELECT SUM(FLOOR((cir.qtd_por_nivel * cpi.nivel * cat.pop)/10)) AS producao
					FROM 
					(SELECT turno, id_imperio, id_instalacao, id_planeta_instalacoes, id_planeta, pop
					FROM colonization_acoes_turno 
					WHERE id_imperio={$imperio->id} AND turno={$turno->turno} AND id_planeta={$colonia->id_planeta}
					) AS cat
					JOIN colonization_planeta_instalacoes AS cpi
					ON cpi.id = cat.id_planeta_instalacoes
					JOIN colonization_instalacao_recursos AS cir
					ON cir.id_instalacao = cat.id_instalacao
					WHERE cir.id_recurso=16 
					AND cir.consome = true
					AND  cpi.turno_destroi IS NULL
					GROUP BY cir.id_recurso");					
					
					if ($poluicao_produz == "") {
						$poluicao_produz = 0;
					}

					if ($poluicao_consome == "") {
						$poluicao_consome = 0;
					}					
					$poluicao = $colonia->poluicao + $poluicao_produz - $poluicao_consome;
					$poluicao = $poluicao-25; //Os planetas conseguem reduzir a poluição em 25 todos os turnos
					
					if ($poluicao<0) {
						$poluicao=0;
					}
					
					//Aumenta a população
					//O aumento da população funciona assim: se houver comida sobrando DEPOIS do consumo, ela cresce em 5 por turno se pop<30, depois cresce 10 por turno até atingir (Tamanho do Planeta*10)
					//No entanto, a poluição reduz o crescimento populacional
					if ($poluicao > 100 && $planeta->inospito == 1) {
						$nova_pop = $colonia->pop;
					} else {
						if ($alimentos > 0) {
								if ($colonia->pop <= $planeta->tamanho*10) {//Tem espaço para crescer
									if ($colonia->pop <=24) {
										$nova_pop = $colonia->pop + 5;
									} else {
										$nova_pop = $colonia->pop + 10;
									}
									if ($nova_pop > $planeta->tamanho*10) {
										$nova_pop = $planeta->tamanho*10;
									}
								}
						} else {
							//Caso os Alimentos sejam 0, a população CAI em 10%
							$nova_pop = round(0.9*$colonia->pop);
						}
					}
				
					$html.= "INSERT INTO colonization_imperio_colonias SET poluicao={$poluicao}, pop={$nova_pop}, turno={$proximo_turno}, id_planeta={$colonia->id_planeta}, id_imperio={$colonia->id_imperio}<br>";
					$wpdb->query("INSERT INTO colonization_imperio_colonias SET poluicao={$poluicao}, pop={$nova_pop}, turno={$proximo_turno}, id_imperio={$colonia->id_imperio}");
				}
			}
		
		//Ao terminar de rodar o Turno, muda o Turno para o próximo turno!
		$html.= "INSERT INTO colonization_turno_atual SET id={$proximo_turno}, data_turno='{$proxima_semana}'<br>";
		$wpdb->query("INSERT INTO colonization_turno_atual SET id={$proximo_turno}, data_turno='{$proxima_semana}'");
		
		$this->concluido = true;
		} else {
			$html = "É NECESSÁRIO TER PRIVILÉGIOS ADMINISTRATIVOS PARA RODAR O TURNO!";
		}
		
		return $html;
	}
}
?>