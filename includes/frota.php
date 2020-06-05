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
	public $estrela;
	public $string_nave;
	public $tamanho;
	public $velocidade;
	public $alcance;
	public $PDF_laser;
	public $PDF_projetil;
	public $PDF_torpedo;
	public $blindagem;
	public $escudos;
	public $PDF_bombardeamento;
	public $poder_invasao;
	public $pesquisa;
	public $camuflagem;
	public $nivel_estacao_orbital;
	public $especiais;
	public $HP;
	public $HP_max;
	public $qtd;
	public $turno;
	public $id_estrela_destino;
	public $visivel;
	
	function __construct($id=0) {
		global $wpdb;
		
		if ($id == 0) {
			return false;
		}
		
		$this->id = $id;
	
		$resultados = $wpdb->get_results("SELECT id, id_imperio, nome, tipo, qtd, X, Y, Z, string_nave, 
		tamanho, HP, velocidade, alcance, 
		PDF_laser, PDF_projetil, PDF_torpedo,
		blindagem, escudos, 
		PDF_bombardeamento, poder_invasao, pesquisa, nivel_estacao_orbital,
		camuflagem, especiais, turno, id_estrela_destino, visivel
		FROM colonization_imperio_frota 
		WHERE id={$this->id}");
		
		$resultado = $resultados[0];
		$this->id_imperio = $resultado->id_imperio;
		$this->nome = $resultado->nome;
		$this->tipo = $resultado->tipo;
		$this->qtd = $resultado->qtd;
		$this->X = $resultado->X;
		$this->Y = $resultado->Y;
		$this->Z = $resultado->Z;
		
		$id_estrela = $wpdb->get_var("SELECT id FROM colonization_estrela WHERE X={$this->X} AND Y={$this->Y} AND Z={$this->Z}");
		$this->estrela = new estrela($id_estrela);
		
		$this->string_nave = $resultado->string_nave;
		$this->tamanho = $resultado->tamanho;
		$this->velocidade = $resultado->velocidade;
		$this->alcance = $resultado->alcance;
		$this->PDF_laser = $resultado->PDF_laser;
		$this->PDF_projetil = $resultado->PDF_projetil;
		$this->PDF_torpedo = $resultado->PDF_torpedo;
		$this->blindagem = $resultado->blindagem;
		$this->escudos = $resultado->escudos;
		$this->PDF_bombardeamento = $resultado->PDF_bombardeamento;
		$this->poder_invasao = $resultado->poder_invasao;
		$this->camuflagem = $resultado->camuflagem;
		$this->pesquisa = $resultado->pesquisa;
		$this->nivel_estacao_orbital = $resultado->nivel_estacao_orbital;
		$this->especiais = $resultado->especiais;
		$this->HP = $resultado->HP;
		$this->HP_max = $this->tamanho*10;
		$this->turno = $resultado->turno;
		$this->id_estrela_destino = $resultado->id_estrela_destino;
		$this->visivel = $resultado->visivel;
	}
	
	/***********************
	function exibe_autoriza()
	----------------------
	Exibe a autorização para mover uma nave
	***********************/
	function exibe_autoriza() {
		global $wpdb;
	
		$imperio = new imperio($this->id_imperio);
		$estrela = new estrela($this->id_estrela_destino);
		
		$html = "<div>O {$imperio->nome} deseja enviar a nave '{$this->nome}' para {$estrela->nome} ({$estrela->X};{$estrela->Y};{$estrela->Z})</div>
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
		
		$html = "<td>
			<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
			<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='{$this->id_imperio}' value='{$this->id_imperio}'></input>
			<input type='hidden' data-atributo='where_clause' value='id'></input>
			<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
			<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>
			<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir esta Frota?'></input>
			<div data-atributo='nome' data-editavel='true' data-valor-original='{$this->nome}' data-style='width: 100px;'>{$this->nome}</div>
			<div data-atributo='gerenciar'><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a></div>
			<div data-atributo='processa_string' data-valor-original='' style='visibility: hidden;'><a href='#' onclick='return processa_string_admin(event, this);'>Processa String</a></div>
			</td>
			<td><div data-atributo='tipo' data-editavel='true' data-valor-original='{$this->tipo}' data-style='width: 100px;' data-id='categoria'>{$this->tipo}</div></td>
			<td><div data-atributo='qtd' data-editavel='true' data-valor-original='{$this->qtd}' data-style='width: 30px;'>{$this->qtd}</div></td>
			<td><div data-atributo='X' data-editavel='true' data-valor-original='{$this->X}' data-style='width: 30px;'>{$this->X}</div></td>
			<td><div data-atributo='Y' data-editavel='true' data-valor-original='{$this->Y}' data-style='width: 30px;'>{$this->Y}</div></td>
			<td><div data-atributo='Z' data-editavel='true' data-valor-original='{$this->Z}' data-style='width: 30px;'>{$this->Z}</div></td>
			<td><div data-atributo='string_nave' data-type='textarea' data-editavel='true' data-valor-original='{$this->string_nave}' data-style='width: 80px; height: 200px;' data-id='string_nave' data-branco='true'>{$this->string_nave}</div></td>
			<td><div data-atributo='tamanho' data-editavel='true' data-valor-original='{$this->tamanho}' data-style='width: 50px;' data-id='tamanho' data-id='tamanho'>{$this->tamanho}</div></td>
			<td><div data-atributo='HP' data-editavel='true' data-valor-original='{$this->HP}' data-style='width: 50px;' data-id='HP'>{$this->HP}</div></td>
			<td><div data-atributo='velocidade' data-editavel='true' data-valor-original='{$this->velocidade}' data-style='width: 50px;' data-id='velocidade'>{$this->velocidade}</div></td>
			<td><div data-atributo='alcance' data-editavel='true' data-valor-original='{$this->alcance}' data-style='width: 50px;' data-id='alcance'>{$this->alcance}</div></td>
			<td><div data-atributo='PDF_laser' data-editavel='true' data-valor-original='{$this->PDF_laser}' data-style='width: 50px;' data-id='PDF_laser'>{$this->PDF_laser}</div></td>
			<td><div data-atributo='PDF_torpedo' data-editavel='true' data-valor-original='{$this->PDF_torpedo}' data-style='width: 50px;' data-id='PDF_torpedo'>{$this->PDF_torpedo}</div></td>
			<td><div data-atributo='PDF_projetil' data-editavel='true' data-valor-original='{$this->PDF_projetil}' data-style='width: 50px;' data-id='PDF_projetil'>{$this->PDF_projetil}</div></td>
			<td><div data-atributo='blindagem' data-editavel='true' data-valor-original='{$this->blindagem}' data-style='width: 50px;' data-id='blindagem'>{$this->blindagem}</div></td>
			<td><div data-atributo='escudos' data-editavel='true' data-valor-original='{$this->escudos}' data-style='width: 50px;' data-id='escudos'>{$this->escudos}</div></td>
			<td><div data-atributo='PDF_bombardeamento' data-editavel='true' data-valor-original='{$this->PDF_bombardeamento}' data-style='width: 50px;' data-id='qtd_bombas'>{$this->PDF_bombardeamento}</div></td>
			<td><div data-atributo='poder_invasao' data-editavel='true' data-valor-original='{$this->poder_invasao}' data-style='width: 50px;' data-id='qtd_tropas'>{$this->poder_invasao}</div></td>
			<td><div data-atributo='pesquisa' data-type='checkbox' data-editavel='true' data-valor-original='{$this->pesquisa}' data-id='pesquisa'><input type='checkbox' data-atributo='pesquisa' data-ajax='true' {$pesquisa_checked} disabled></input></div></td>
			<td><div data-atributo='camuflagem' data-editavel='true' data-valor-original='{$this->camuflagem}' data-style='width: 50px;'>{$this->camuflagem}</div></td>
			<td><div data-atributo='nivel_estacao_orbital' data-editavel='true' data-valor-original='{$this->nivel_estacao_orbital}' data-style='width: 50px;' data-id='nivel_estacao_orbital'>{$this->nivel_estacao_orbital}</div></td>			
			<td><div data-atributo='especiais' data-editavel='true' data-type='textarea' data-valor-original='{$this->especiais}' data-branco='true' data-style='width: 120px; height: 100px;'>{$this->especiais}</div></td>
			<td><div data-atributo='turno' data-editavel='true' data-valor-original='{$this->turno}' data-style='width: 50px;'>{$this->turno}</div></td>
			<td><div data-atributo='gerenciar'><a href='#' onclick='return copiar_objeto(event, this, {$this->id_imperio});'>Criar cópia</a></div>
			</td>";
		
		return $html;
	}

	/***********************
	function exibe_frota()
	----------------------
	Exibe uma Nave
	***********************/
	function exibe_frota() {
		global $wpdb;
		
		$turno = new turno();
		
		//1 Estação Orbital "Orbit One" (1;8;9) - Tamanho 100; Velocidade 1; Alcance 0; PdF Laser 10/Torpedo 10; Blindagem 10; HP 1000; Especiais: (1) - Produz até 50 Equipamentos de Naves por turno
		$html = "<td>
		<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
		<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='{$this->id_imperio}' value='{$this->id_imperio}'></input>
		<input type='hidden' data-atributo='where_clause' value='id'></input>
		<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>		
		<b>{$this->qtd} {$this->tipo} \"{$this->nome}\"</b>
		</td>
		<td>{$this->estrela->nome} ({$this->X};{$this->Y};{$this->Z})</td>
		<td>Tam: {$this->tamanho}; Vel: {$this->velocidade}; Alc: {$this->alcance}";
		
		$html_armas = "";
		if ($this->PDF_laser >0) {
			$html_armas .= " PdF Laser: {$this->PDF_laser};";
		}

		if ($this->PDF_torpedo >0) {
			$html_armas .= " PdF Torpedo: {$this->PDF_torpedo};";
		}

		if ($this->PDF_projetil >0) {
			$html_armas .= " PdF Projétil: {$this->PDF_projetil};";
		}

		$html .= $html_armas;
		
		$html .= " Blindagem: {$this->blindagem}; Escudos: {$this->escudos};";

		$html .= " HP: {$this->HP}/{$this->HP_max};";
		

		if ($this->especiais != "") {
		$html .= " Especiais: {$this->especiais};";
		} 
		$html .= "</td>";
		
		if ($this->alcance == 0) {
			$html .= "<td>&nbsp;</td>";
		} else {
			//TODO - Mostrar quais estrelas a Nave pode ir
			
			$disabled = "disabled";
			$display = "style='display: none;'";
			if ($this->id_estrela_destino == 0 && $turno->encerrado != 1) {
				$disabled = "";
				$display = "";
				//$html .= $this->exibe_estrelas_destino();
			}
			
			//***
			$html .= "<td>
			<div data-atributo='nome_estrela' data-editavel='true' data-type='select' data-id-selecionado='' data-valor-original=''>
			<select data-atributo='id_estrela' data-alcance='{$this->alcance}' style='width: 100%' {$disabled}>";
				
			$html .= "</select>
			</div>
			<div data-atributo='gerenciar'><a href='#' onclick='return envia_nave(this,event,{$this->id})' {$display}>Despachar Nave</a></div>
			</td>";
			//***/
		}

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
		$start_time = hrtime(true);
		$id_estrelas = $wpdb->get_results("SELECT id FROM colonization_estrela");
		$id_estrelas_temp =[];
		foreach ($id_estrelas as $id) {
			$id_estrelas_temp[$id->id] = $id->id;
		}
		$id_estrelas = $id_estrelas_temp;	
		$end_time = hrtime(true);
		$diferenca = round(($end_time - $start_time)/1000000,0);
		echo "Populando as Estrelas: {$diferenca}<br>";
		
		$start_time = hrtime(true);
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
		$end_time = hrtime(true);
		$diferenca = round(($end_time - $start_time)/1000000,0);
		echo "Buscando os Caminhos: {$diferenca}<br>";
		
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

}
?>