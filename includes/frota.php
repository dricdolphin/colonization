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
	public $nome_npc;
	public $nome;
	public $tipo;
	public $X;
	public $Y;
	public $Z;
	public $id_estrela;
	public $estrela;
	public $string_nave;
	public $custo;
	public $tamanho;
	public $velocidade;
	public $alcance;
	public $alcance_temp = 0;
	public $pdf_laser;
	public $pdf_projetil;
	public $pdf_torpedo;
	public $blindagem;
	public $escudos;
	public $pdf_bombardeamento;
	public $poder_invasao;
	public $pesquisa;
	public $camuflagem;
	public $nivel_estacao_orbital;
	public $especiais;
	public $HP;
	public $HP_max;
	public $qtd;
	public $turno;
	public $turno_destruido;
	public $id_estrela_destino;
	public $anti_dobra;
	public $visivel;
	public $partes_nave;
	public $poder_abordagem;
	public $bonus_abordagem = 0;
	public $bonus_invasao = 0;
	public $destinos_buracos_minhoca;
	
	function __construct($id=0) {
		global $wpdb;
		
		if ($id == 0) {
			return false;
		}
		
		$this->id = $id;
	
		$resultados = $wpdb->get_results("SELECT id, id_imperio, nome_npc, nome, tipo, qtd, X, Y, Z, string_nave, custo,
		tamanho, HP, velocidade, alcance, 
		pdf_laser, pdf_projetil, pdf_torpedo,
		blindagem, escudos, 
		pdf_bombardeamento, poder_invasao, pesquisa, nivel_estacao_orbital,
		camuflagem, especiais, turno, turno_destruido, id_estrela_destino, visivel, anti_dobra
		FROM colonization_imperio_frota 
		WHERE id={$this->id}");
		
		$resultado = $resultados[0];
		if (empty($resultado)) {
			$this->id = 0;
			return false;
		}
		
		$this->id_imperio = $resultado->id_imperio;
		$this->nome_npc = $resultado->nome_npc;
		$this->nome = $resultado->nome;
		$this->tipo = $resultado->tipo;
		$this->qtd = $resultado->qtd;
		$this->X = $resultado->X;
		$this->Y = $resultado->Y;
		$this->Z = $resultado->Z;
		
		$id_estrela = $wpdb->get_var("SELECT id FROM colonization_estrela WHERE X={$this->X} AND Y={$this->Y} AND Z={$this->Z}");
		$this->id_estrela = $id_estrela;
		//$this->estrela = new estrela($id_estrela);
		
		$this->string_nave = stripslashes($resultado->string_nave);
		$this->custo = stripslashes($resultado->custo);
		$this->tamanho = $resultado->tamanho;
		$this->velocidade = $resultado->velocidade;
		$this->alcance = $resultado->alcance;
		$this->pdf_laser = $resultado->pdf_laser;
		$this->pdf_projetil = $resultado->pdf_projetil;
		$this->pdf_torpedo = $resultado->pdf_torpedo;
		$this->blindagem = $resultado->blindagem;
		$this->escudos = $resultado->escudos;
		$this->pdf_bombardeamento = $resultado->pdf_bombardeamento;
		$this->poder_invasao = $resultado->poder_invasao;
		$this->camuflagem = $resultado->camuflagem;
		$this->pesquisa = $resultado->pesquisa;
		$this->nivel_estacao_orbital = $resultado->nivel_estacao_orbital;
		$this->especiais = $resultado->especiais;
		$this->HP = $resultado->HP;
		$this->HP_max = $this->tamanho*10;
		$this->turno = $resultado->turno;
		$this->turno_destruido = $resultado->turno_destruido;
		$this->id_estrela_destino = $resultado->id_estrela_destino;
		$this->anti_dobra = $resultado->anti_dobra;
		$this->visivel = $resultado->visivel;
		
		if ($this->HP_max > 0) {
			if (round((($this->HP)/($this->HP_max))*10,0) == 1) {
				if ($this->alcance_temp == 0) {
					$this->alcance_temp = $this->alcance;
				}
				$this->alcance = 0;
			}
		}
		
		if ($this->anti_dobra) {
			if ($this->alcance_temp == 0) {
				$this->alcance_temp = $this->alcance;
			}
			$this->alcance = 0;
		}
		
		
		$this->partes_nave = JSON_decode($this->string_nave);
		if (!empty($this->partes_nave)) {
			foreach ($this->partes_nave as $parte_nave => $valor) {
				$especiais_tech = $wpdb->get_var("SELECT ct.especiais FROM colonization_tech AS ct WHERE especiais LIKE '%id={$parte_nave}%'");
				$especiais = explode(";",$especiais_tech);
				
				//bonus_abordagem
				$bonus_abordagem = array_values(array_filter($especiais, function($value) {
					return strpos($value, 'bonus_abordagem') !== false;
				}));
				
				if (!empty($bonus_abordagem)) {
					$bonus_abordagem_valor = explode("=",$bonus_abordagem[0]);
					$this->bonus_abordagem = $this->bonus_abordagem	+ $bonus_abordagem_valor[1];
				}

				//bonus_invasao
				$bonus_invasao = array_values(array_filter($especiais, function($value) {
					return strpos($value, 'bonus_invasao') !== false;
				}));
				
				if (!empty($bonus_invasao)) {
					$bonus_invasao_valor = explode("=",$bonus_invasao[0]);
					$this->bonus_invasao = $this->bonus_invasao	+ $bonus_invasao_valor[1];
				}
			}
		}
	}
	
	/***********************
	function exibe_autoriza()
	----------------------
	Exibe a autorização para mover uma nave
	***********************/
	function exibe_autoriza() {
		global $asgarosforum, $wpdb;
	
		$imperio = new imperio($this->id_imperio);
		$estrela_destino = new estrela($this->id_estrela_destino);
		
		$html_qtd = "a nave";
		if ($this->qtd > 1) {
			$html_qtd = "{$this->qtd} naves";
		}
		
		$post_autoriza = $asgarosforum->spoilers->render_spoiler(false, "<p>A nave '{$this->nome}' fora para {$estrela_destino->nome} ({$estrela_destino->X};{$estrela_destino->Y};{$estrela_destino->Z})</p>
		<p>{$estrela_destino->descricao}</p>");
		
		$html = "<div>O Império '{$imperio->nome}' deseja enviar {$html_qtd} '{$this->nome}' para {$estrela_destino->nome} ({$estrela_destino->X};{$estrela_destino->Y};{$estrela_destino->Z})</div>
		{$post_autoriza}
		<div><a href='#' style='font-weight: bold !important;' onclick='return processa_viagem_nave(this, event,{$this->id});'>OK, autorizado!</a></div>";

		return $html;
	}


	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;
		
		if ($this->pesquisa == 1) {
			$pesquisa_checked = "checked";
		} else {
			$pesquisa_checked = "";
		}		
		
		if ($this->id_imperio != 0) {
			if ($this->alcance_temp != 0) {
				$this->alcance = $this->alcance_temp;
			}
			$html = "<td>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
				<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='{$this->id_imperio}' value='{$this->id_imperio}'></input>
				<input type='hidden' data-atributo='where_clause' value='id'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
				<input type='hidden' data-atributo='funcao_validacao' value='valida_nave'></input>
				<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir esta Frota?'></input>
				<div data-atributo='nome' data-editavel='true' data-valor-original='{$this->nome}' data-style='width: 100px;'>{$this->nome}</div>
				<div data-atributo='gerenciar'><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a></div>
				<div data-atributo='processa_string' data-valor-original='' style='visibility: hidden;'><a href='#' onclick='return processa_string_admin(event, this);'>Processa String</a></div>
				</td>
				<td><div data-atributo='tipo' data-editavel='true' data-valor-original='{$this->tipo}' data-style='width: 100px;' data-id='categoria'>{$this->tipo}</div>
				<div class='subtitulo'>Custo</div>
				<div data-atributo='custo' data-ajax='true' data-editavel='true' data-branco='true' data-valor-original='{$this->custo}' data-style='width: 100px;' data-id='custo'>{$this->custo}</div>
				</td>
				<td><div data-atributo='qtd' data-editavel='true' data-valor-original='{$this->qtd}' data-style='width: 30px;'>{$this->qtd}</div></td>
				<td><div data-atributo='X' data-editavel='true' data-valor-original='{$this->X}' data-style='width: 30px;'>{$this->X}</div></td>
				<td><div data-atributo='Y' data-editavel='true' data-valor-original='{$this->Y}' data-style='width: 30px;'>{$this->Y}</div></td>
				<td><div data-atributo='Z' data-editavel='true' data-valor-original='{$this->Z}' data-style='width: 30px;'>{$this->Z}</div></td>
				<td><div data-atributo='string_nave' data-type='textarea' data-editavel='true' data-valor-original='{$this->string_nave}' data-style='width: 80px; height: 200px;' data-id='string_nave' data-branco='true'>{$this->string_nave}</div></td>
				<td><div data-atributo='tamanho' data-editavel='true' data-valor-original='{$this->tamanho}' data-style='width: 50px;' data-id='tamanho' data-id='tamanho'>{$this->tamanho}</div></td>
				<td><div data-atributo='HP' data-editavel='true' data-valor-original='{$this->HP}' data-style='width: 50px;' data-id='hp'>{$this->HP}</div></td>
				<td><div data-atributo='velocidade' data-editavel='true' data-valor-original='{$this->velocidade}' data-style='width: 50px;' data-id='velocidade'>{$this->velocidade}</div></td>
				<td><div data-atributo='alcance' data-editavel='true' data-valor-original='{$this->alcance}' data-style='width: 50px;' data-id='alcance'>{$this->alcance}</div></td>
				<td><div data-atributo='pdf_laser' data-editavel='true' data-valor-original='{$this->pdf_laser}' data-style='width: 50px;' data-id='pdf_laser'>{$this->pdf_laser}</div></td>
				<td><div data-atributo='pdf_torpedo' data-editavel='true' data-valor-original='{$this->pdf_torpedo}' data-style='width: 50px;' data-id='pdf_torpedo'>{$this->pdf_torpedo}</div></td>
				<td><div data-atributo='pdf_projetil' data-editavel='true' data-valor-original='{$this->pdf_projetil}' data-style='width: 50px;' data-id='pdf_projetil'>{$this->pdf_projetil}</div></td>
				<td><div data-atributo='blindagem' data-editavel='true' data-valor-original='{$this->blindagem}' data-style='width: 50px;' data-id='blindagem'>{$this->blindagem}</div></td>
				<td><div data-atributo='escudos' data-editavel='true' data-valor-original='{$this->escudos}' data-style='width: 50px;' data-id='escudos'>{$this->escudos}</div></td>
				<td><div data-atributo='pdf_bombardeamento' data-editavel='true' data-valor-original='{$this->pdf_bombardeamento}' data-style='width: 50px;' data-id='pdf_bombardeamento'>{$this->pdf_bombardeamento}</div></td>
				<td><div data-atributo='poder_invasao' data-editavel='true' data-valor-original='{$this->poder_invasao}' data-style='width: 50px;' data-id='qtd_tropas'>{$this->poder_invasao}</div></td>
				<td><div data-atributo='pesquisa' data-type='checkbox' data-editavel='true' data-valor-original='{$this->pesquisa}' data-id='pesquisa'><input type='checkbox' data-atributo='pesquisa' data-ajax='true' {$pesquisa_checked} disabled></input></div></td>
				<td><div data-atributo='camuflagem' data-editavel='true' data-valor-original='{$this->camuflagem}' data-id='mk_camuflagem' data-style='width: 50px;'>{$this->camuflagem}</div></td>
				<td><div data-atributo='nivel_estacao_orbital' data-editavel='true' data-valor-original='{$this->nivel_estacao_orbital}' data-style='width: 50px;' data-id='nivel_estacao_orbital'>{$this->nivel_estacao_orbital}</div></td>			
				<td><div data-atributo='especiais' data-editavel='true' data-type='textarea' data-valor-original='{$this->especiais}' data-branco='true' data-style='width: 120px; height: 100px;' data-id='especiais'>{$this->especiais}</div></td>
				<td><div data-atributo='turno' data-editavel='true' data-valor-original='{$this->turno}' data-style='width: 50px;'>{$this->turno}</div></td>
				<td><div data-atributo='turno_destruido' data-editavel='true' data-valor-original='{$this->turno_destruido}' data-style='width: 50px;'>{$this->turno_destruido}</div></td>
				<td><div data-atributo='gerenciar'>
				<a href='#' onclick='return copiar_objeto(event, this, {$this->id_imperio});'>Criar cópia</a><br>
				<a href='#' onclick='return copiar_objeto(event, this, {$this->id_imperio},true);'>Upgrade</a>
				</div>
				</td>";
		
		} else {
			
			$html = "<td>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
				<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='{$this->id_imperio}' value='{$this->id_imperio}'></input>
				<input type='hidden' data-atributo='where_clause' value='id'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
				<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>
				<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir esta Frota?'></input>
				<div data-atributo='nome_npc' data-editavel='true' data-valor-original='{$this->nome_npc}' data-style='width: 180px;'>{$this->nome_npc}</div>
				<div data-atributo='gerenciar'><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a></div>
				</td>
				<td><div data-atributo='nome' data-editavel='true' data-valor-original='{$this->nome}' data-style='width: 120px;'>{$this->nome}</div></td>
				<td><div data-atributo='tipo' data-editavel='true' data-valor-original='{$this->tipo}' data-style='width: 120px;' data-id='categoria'>{$this->tipo}</div></td>
				<td><div data-atributo='qtd' data-editavel='true' data-valor-original='{$this->qtd}' data-style='width: 30px;'>{$this->qtd}</div></td>
				<td><div data-atributo='X' data-editavel='true' data-valor-original='{$this->X}' data-style='width: 30px;'>{$this->X}</div></td>
				<td><div data-atributo='Y' data-editavel='true' data-valor-original='{$this->Y}' data-style='width: 30px;'>{$this->Y}</div></td>
				<td><div data-atributo='Z' data-editavel='true' data-valor-original='{$this->Z}' data-style='width: 30px;'>{$this->Z}</div></td>
				<td><div data-atributo='turno' data-editavel='true' data-valor-original='{$this->turno}' data-style='width: 50px;'>{$this->turno}</div></td>
				<td><div data-atributo='turno_destruido' data-editavel='true' data-valor-original='{$this->turno_destruido}' data-style='width: 50px;'>{$this->turno_destruido}</div></td>
				<td><div data-atributo='gerenciar'><a href='#' onclick='return copiar_objeto(event, this, {$this->id_imperio});'>Criar cópia</a></div></td>";			
		}
		
		return $html;
	}

	/***********************
	function exibe_frota()
	----------------------
	Exibe uma Nave
	***********************/
	function exibe_frota() {
		global $wpdb;
		if (empty($this->estrela)) {
			$this->estrela = new estrela($this->id_estrela);
		}
		$turno = new turno();
		
		$html_armas = "";
		if ($this->pdf_laser >0) {
			$html_armas .= " pdf Laser: {$this->pdf_laser};";
		}

		if ($this->pdf_torpedo >0) {
			$html_armas .= " pdf Torpedo: {$this->pdf_torpedo};";
		}

		if ($this->pdf_projetil >0) {
			$html_armas .= " pdf Projétil: {$this->pdf_projetil};";
		}

		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
			$banido = get_user_meta($user->ID, 'asgarosforum_role', true);
			//if ($banido === "banned") {
			//	return;
			//} 
		}


		//$html .= "<td>&nbsp;</td>";
		
		
		$planetas_estrela = $wpdb->get_results("
		SELECT cp.id
		FROM colonization_planeta AS cp
		WHERE cp.id_estrela = {$this->estrela->id}
		");
		
		$alcance_local = 0;
		$tamanho_alcance_local = 0;
		
		$disabled = "disabled";
		$display = "style='display: none;'";
		$display_select = "display: none;";
		foreach ($planetas_estrela as $id) {
			$planeta_estrela = new planeta ($id->id);
			
			if ($planeta_estrela->alcance_local > $alcance_local) {
				$alcance_local = $planeta_estrela->alcance_local;
			}

			if ($planeta_estrela->tamanho_alcance_local > $tamanho_alcance_local) {
				$tamanho_alcance_local = $planeta_estrela->tamanho_alcance_local;
			}
		
			if ($planeta_estrela->buraco_de_minhoca == 1) {
				$this->destinos_buracos_minhoca = $this->estrela->destinos_buracos_minhoca;
			}
		}

		if ($this->alcance > 0 || $this->tamanho <= $tamanho_alcance_local)  {
			$imperio = new imperio($this->id_imperio);
			$this->alcance = $this->alcance+$imperio->bonus_alcance;
			
			$display_select = "";
			if (($this->id_estrela_destino == 0 && $turno->encerrado != 1 && $banido !== "banned") || $roles == "administrator") {
				$disabled = "";
				$display = "";
				//$html .= $this->exibe_estrelas_destino();
			}
		}			

		$html = "<td>
		<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
		<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='{$this->id_imperio}' value='{$this->id_imperio}'></input>
		<input type='hidden' data-atributo='where_clause' value='id'></input>
		<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>		
		<div data-atribut='nome_nave'><b>{$this->qtd} {$this->tipo} '<a href='#' onclick='return muda_nome_nave({$this->id}, event);'><span class='tooltip'>{$this->nome}<span class='tooltiptext'>Alterar o nome da Nave</span></span></a>'</b></div>
		<div data-atributo='atributos'>Tam: {$this->tamanho}; Vel: {$this->velocidade}; Alc: {$this->alcance}";

		$html .= $html_armas;
		
		$html .= " Blindagem: {$this->blindagem}; Escudos: {$this->escudos};";

		$html .= " HP: {$this->HP}/{$this->HP_max};";
		
		if ($this->poder_invasao > 0) {
			$poder_invasao_total = $this->poder_invasao;
			if (empty($imperio)) {
				$imperio = new imperio($this->id_imperio);
			}
			$poder_invasao_total = $this->poder_invasao * $imperio->bonus_invasao;

			$html .= " Poder de Invasão: {$poder_invasao_total};";	
		}
		
		if ($this->especiais != "") {
		$html .= " Especiais: {$this->especiais};";
		} 
		$html .= "</div>
		</td>
		<td>{$this->estrela->nome} ({$this->X};{$this->Y};{$this->Z})</td>		
		";


		$href_calcula_distancia = "";
		if ($roles == "administrator") {
			$href_calcula_distancia = " &nbsp; <a href='#' onclick='return calcula_distancia_reabastece(event, this, false, {$this->id});'>Custo e Trajeto</a>";
		}
		$html .= "<td>
		<div data-atributo='nome_estrela' data-editavel='true' data-type='select' data-id-selecionado='' data-valor-original=''>
		<select class='select_frota' data-atributo='id_estrela' data-alcance='{$this->alcance}' data-alcance-local='{$alcance_local}' style='width: 100%; {$display_select}' {$disabled}>
		</select>
		</div>
		<div data-atributo='gerenciar'><a href='#' onclick='return envia_nave(this,event,{$this->id})' {$display}>Despachar Nave</a>{$href_calcula_distancia}</div>
		</td>";			

		return $html;
	}		


	/***********************
	function exibe_estrelas_destino()
	----------------------
	Exibe as estrelas do destino
	***********************/	
	function exibe_estrelas_destino($disabled='') {
		global $wpdb;
		
		$turno = new turno();
		
		$display = '';
		if ($disabled == 'disabled') {
			$display = " style='display: none;'";
		}
		
		$ids_estrelas_imperio = [];
		$id_colonias = $wpdb->get_results("SELECT id FROM colonization_imperio_colonias WHERE id_imperio={$this->id_imperio} and turno={$turno->turno}");
		foreach ($id_colonias as $id) {
			$colonia = new colonia($id->id);
			if (empty($colonia->estrela)) {
				$colonia->estrela = new estrela($colonia->id_estrela);
			}
			$ids_estrelas_imperio[$colonia->estrela->id] = $colonia->estrela->id;
		}
		
		$id_esrelas_reabastece = $wpdb->get_results("SELECT id_estrela FROM colonization_imperio_abastecimento WHERE id_imperio={$this->id_imperio}");
		
		foreach ($id_esrelas_reabastece as $id) {
			$estrela = new estrela ($id->id_estrela);
			$ids_estrelas_imperio[$estrela->id] = $estrela->id;
		}
		
		
		$options = [];
		//Primeiro verifica se consegue chegar da estrela atual para qualquer estrela do Império ou ponto de Reabastecimento
		$novos_pontos_reabastece = false;
		foreach ($ids_estrelas_imperio as $chave => $id_destino) {

			$estrela_destino = new estrela($id_destino);
			
			//if ($this->estrela->id != $estrela_destino->id) {
				$distancia = $this->distancia_estrelas($this->estrela->id,$estrela_destino->id);
				//$alcance = $this->alcance;
				//if (!empty($ids_estrelas_imperio[$estrela_destino->id])) {
					$alcance = $this->alcance*2;
				//}
				
				$selected = "";
				if ($this->id_estrela_destino == $estrela_destino->id) {
					$selected = " selected";
				}
				if ($alcance >= $distancia) {//Verifica se consegue chegar até qualquer um desses pontos
					$options[$estrela_destino->id] = "<option value='{$estrela_destino->id}' {$selected}>{$estrela_destino->nome} ({$estrela_destino->X};{$estrela_destino->Y};{$estrela_destino->Z})</option>";
					$novos_pontos_reabastece = true;
				}
			//}
		}
		

		//Agora, com todos os pontos onde a nave pode chegar, verifica à partir deles _TODAS_ as estrelas da Galáxia
		$id_estrelas = $wpdb->get_results("SELECT id FROM colonization_estrela");
		$id_estrelas_temp =[];
		foreach ($id_estrelas as $id) {
			$id_estrelas_temp[$id->id] = $id->id;
		}
		$id_estrelas = $id_estrelas_temp;	
		
		do {
			$novos_pontos_reabastece = false;
			$options_temp = [];
			
			foreach ($options as $id_origem => $valor_origem) {
				//$estrela_origem = new estrela($id_origem);
				
				foreach ($id_estrelas as $chave => $id_destino) {
					//$estrela_destino = new estrela($id_destino);
					
					if ($id_origem != $id_destino && empty($options[$id_destino])) {
						$distancia = $this->distancia_estrelas($id_origem,$id_destino);
						$alcance = $this->alcance;
						if (!empty($ids_estrelas_imperio[$id_destino])) {
							//Estrelas do Império ou Pontos de Reabastecimento permitem irmos para irmos até o DOBRO da distância
							$alcance = $this->alcance*2;
						}
						$selected = "";
						if ($this->id_estrela_destino == $id_destino) {
							$selected = " selected";
						}
						if ($alcance >= $distancia) {
							$estrela_destino = new estrela($id_destino);
							$options_temp[$id_destino] = "<option value='{$estrela_destino->id}' {$selected}>{$estrela_destino->nome} ({$estrela_destino->X};{$estrela_destino->Y};{$estrela_destino->Z})</option>";
							if ($alcance == $this->alcance*2) {//É um novo ponto de reabastecimento!
								$novos_pontos_reabastece = true;
							}
						}
					}
				}
			}
			
			foreach ($options_temp as $chave => $valor) {
				if (empty($options[$chave])) {
					$options[$chave] = $valor;
				}
			}
		} while ($novos_pontos_reabastece === true);
		
		//Remove o ponto atual da lista de estrelas
		$options_temp = [];
		foreach ($options as $chave => $valor) {
			if ($chave != $this->estrela->id) {
				$options_temp[$chave] = $valor;
			}
		}
		$options = $options_temp;
		
		$html = "<td>
		<div data-atributo='nome_estrela' data-editavel='true' data-type='select' data-id-selecionado='' data-valor-original=''>
		<select data-atributo='id_estrela_destino' style='width: 100%' {$disabled}>";
		
		foreach ($options as $chave => $valor) {
			$html .= "
			{$valor}
			";
		}
		
		
		$html .= "</select>
		</div>
		<div data-atributo='gerenciar'><a href='#' onclick='return envia_nave(this,event,{$this->id})' {$display}>Despachar Nave</a></div>
		</td>";
		
		return $html; 
	}
	
	function distancia_estrelas ($id_estrela_origem, $id_estrela_destino) {
		$estrela_origem = new estrela($id_estrela_origem);
		$estrela_destino = new estrela($id_estrela_destino);
		
		$distancia = sqrt(($estrela_origem->X - $estrela_destino->X)**2 + ($estrela_origem->Y - $estrela_destino->Y)**2 + ($estrela_origem->Z - $estrela_destino->Z)**2);
		
		return $distancia;
	}
	
	/***********************
	function exibe_frota()
	----------------------
	Exibe uma Nave
	***********************/
	function html_nave($imperio) {
		$tipo = "";
		if ($this->tipo != "Estação Orbital") {
			$tipo = "<span style='font-weight: normal; font-size: 0.9em;'>{$this->tipo}</span>&nbsp;";
		}
		
		$html_HP = "";
		if ($this->HP < $this->HP_max) {
			$html_HP = "{$this->HP}/";
		}
		
		$html_nave = "{$tipo}<div class='fas fa-heart' style='display: inline-block; margin: 2px;'><span style='font-weight: normal; font-size: 0.9em;'>&nbsp;{$html_HP}{$this->HP_max}</span></div>";
		$html_nave .= "<div class='far fa-tachometer-alt' style='display: inline-block; margin: 2px;'><span style='font-weight: normal; font-size: 0.9em;'>&nbsp;{$this->velocidade}</span></div>";
		
		if ($this->blindagem >0) {
			$html_nave .= "<div class='fas fa-hard-hat' style='display: inline-block; margin: 2px;'><span style='font-weight: normal; font-size: 0.9em;'>&nbsp;{$this->blindagem}</span></div>";
		}
		
		if ($this->escudos >0) {
			$html_nave .= "<div class='fas fa-shield' style='display: inline-block; margin: 2px;'><span style='font-weight: normal; font-size: 0.9em;'>&nbsp;{$this->escudos}</span></div>";
		}

		if ($this->pdf_laser >0) {
			$html_nave .= "<div class='far fa-sword-laser' style='display: inline-block; margin: 2px;'><span style='font-weight: normal; font-size: 0.9em;'>&nbsp;{$this->pdf_laser}({$this->partes_nave->qtd_laser})</span></div>";
		}

		if ($this->pdf_torpedo >0) {
			$html_nave .= "<div class='far fa-bahai' style='display: inline-block; margin: 2px;'><span style='font-weight: normal; font-size: 0.9em;'>&nbsp;{$this->pdf_torpedo}({$this->partes_nave->qtd_torpedo})</span></div>";
		}
		
		if ($this->pdf_projetil >0) {
			$html_nave .= "<div class='far fa-asterisk' style='display: inline-block; margin: 2px;'><span style='font-weight: normal; font-size: 0.9em;'>&nbsp;{$this->pdf_projetil}({$this->partes_nave->qtd_projetil})</span></div>";
		}
		
		if ($this->poder_invasao >0) {
			$poder_invasao_total = $this->poder_invasao * ($imperio->bonus_invasao + $this->bonus_invasao);
			$html_nave .= "<div class='fas fa-users' style='display: inline-block; margin: 2px;'><span style='font-weight: normal; font-size: 0.9em;'>&nbsp;{$poder_invasao_total}</span></div>";
		}

		if ($this->pdf_bombardeamento >0) {
			$html_nave .= "<div class='fas fa-bomb' style='display: inline-block; margin: 2px;'><span style='font-weight: normal; font-size: 0.9em;'>&nbsp;{$this->pdf_bombardeamento}</span></div>";
		}

		$poder_abordagem = $this->tamanho + floor((($imperio->bonus_abordagem + $this->bonus_abordagem)*$this->tamanho)/100);
		$html_nave .= "<div class='fas fa-running' style='display: inline-block; margin: 2px;'><span style='font-weight: normal; font-size: 0.9em;'>&nbsp;{$poder_abordagem}</span></div>";
		
		$defesa_abordagem = $this->tamanho + floor((($imperio->defesa_abordagem)*$this->tamanho)/100);
		if ($this->nivel_estacao_orbital > 0) {
			$defesa_abordagem = $defesa_abordagem*10;
		}
		if ($this->poder_invasao > 0) {
			$defesa_abordagem = $defesa_abordagem*$this->poder_invasao;
		}		
		$html_nave .= "<div class='fas fa-user-shield' style='display: inline-block; margin: 2px;'><span style='font-weight: normal; font-size: 0.9em;'>&nbsp;{$defesa_abordagem}</span></div>";

		if ($this->especiais != "") {
			$html_nave .= "<div class='especiais'>{$this->especiais}</div>";
		}

		return $html_nave;
	}

}
?>