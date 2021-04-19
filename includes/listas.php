<?php
/**************************
LISTAS.PHP
----------------
Contém objetos de criação de listas
***************************/

//Classe "lista_estrelas"
//Mostra a lista de estrelas, com os IDs, nome e posição
class lista_estrelas
{
	public $html_lista;
	
	function __construct() {
		global $wpdb;
		$resultados = $wpdb->get_results(
		"SELECT id, nome, X, Y, Z
		FROM colonization_estrela 
		ORDER BY colonization_estrela.X, colonization_estrela.Y, colonization_estrela.Z");

		$lista_valores = "";
		$lista_options = "";
		$lista_x_estrela = "";
		$lista_y_estrela = "";
		$lista_z_estrela = "";
		$lista_nome_estrela = "";
		
		$index = 0;
		foreach ($resultados as $resultado) {
			$lista_options .= "			lista[{$index}]=\"<option value='{$resultado->id}'\"+selecionado[{$index}]+\">{$resultado->nome}  - {$resultado->X};{$resultado->Y};{$resultado->Z}</option>\";\n";
			$lista_valores .= "			lista_valores[{$index}]={$resultado->id};\n";
			$lista_x_estrela .= "			lista_x_estrela[{$resultado->id}]={$resultado->X};\n";
			$lista_y_estrela .= "			lista_y_estrela[{$resultado->id}]={$resultado->Y};\n";
			$lista_z_estrela .= "			lista_z_estrela[{$resultado->id}]={$resultado->Z};\n";
			$lista_nome_estrela .= "			lista_nome_estrela[{$resultado->id}]='{$resultado->nome}';\n";
			$index++;
		}


		$turno = new turno();
		$this->html_lista = 
"		
var lista_x_estrela=[];
var lista_y_estrela=[];
var lista_z_estrela=[];
var lista_nome_estrela=[];
var custos_instalacao=[];

var turno_atual={$turno->turno};

		/******************
		function lista_estrelas_html(id=0)
		--------------------
		Cria a lista de estrelas
		id -- qual ID está selecionado
		******************/
		function lista_estrelas_html(id=0) {
			
			var lista=[];
			var lista_valores=[];
			var selecionado=[];
{$lista_valores}
{$lista_x_estrela}
{$lista_y_estrela}
{$lista_z_estrela}
{$lista_nome_estrela}
			
			var html = \"			<select data-atributo='id_estrela' style='width: 100%'>\";
			for (var index = 0; index < lista_valores.length; index++) {
				if (lista_valores[index] == id) {
					selecionado[index] = 'selected';
				} else {
					selecionado[index] = '';
				}
			}

			var mapped_estrelas_destino = lista_x_estrela.map(function(el, i) {
				return { index: i, value: el, id_estrela: i, nome_estrela: lista_nome_estrela[i], posicao_estrela: ' ('+lista_x_estrela[i]+';'+lista_y_estrela[i]+';'+lista_z_estrela[i]+')' };
			});	
				
			mapped_estrelas_destino.sort(function(firstEl, secondEl) {
				if (firstEl.nome_estrela.toLowerCase() < secondEl.nome_estrela.toLowerCase()) {
				return -1;
				}
				if (firstEl.nome_estrela.toLowerCase() > secondEl.nome_estrela.toLowerCase()) {
				return 1;
				}
				// a must be equal to b
				return 0;
			});
				
				html_lista = '';
				mapped_estrelas_destino.forEach(function(valor_destino, chave_destino, mapa_destino) {
					let selecionado = '';
					if (valor_destino.id_estrela == id) {
						selecionado = 'selected';
					}
					html_lista = html_lista + '<option value=\"'+valor_destino.id_estrela+'\" '+selecionado+'>'+ valor_destino.nome_estrela +' '+ valor_destino.posicao_estrela +'</option>';
					
					//distancia[chave_destino] = true;
				});			

			//lista.sort();
			//for (index = 0; index < lista.length; index++) {
			//	html = html+lista[index];
			//}
			html = html + html_lista;
			html = html +\"			</select>\";


			return html;
		}";
	}
}

//Classe "lista_usuarios"
//Mostra a lista de usuários, com os IDs
class lista_usuarios 
{
	public $html_lista;
	
	function __construct() {
		$users = get_users(array( 'fields' => array( 'ID', 'display_name' ) )); //Pega todos os usuários
		$lista_usuarios = "";
		foreach ($users as $user) {
			$lista_usuarios .= "
+\"		<option value='{$user->ID}'>{$user->display_name}</option>\"\n";
		}

		$this->html_lista = 
"		/******************
		function lista_jogadores_html(id_jogador =0)
		--------------------
		Cria a lista de jogadores
		id_imperio = 0 -- define qual jogador está selecionado
		******************/
		function lista_jogadores_html(id_jogador = 0) {
			
			var html = \"			<select data-atributo='id_jogador' style='width: 100%'>\"
			$lista_usuarios
			+\"			</select>\";
				
			return html;
		}";
	}
}

//Classe "lista_imperios"
//Mostra a lista de Impérios, com os IDs
class lista_imperios
{
	public $html_lista;
	
	function __construct() {
		global $wpdb;
		$resultados = $wpdb->get_results("SELECT id, nome FROM colonization_imperio ORDER BY nome");

		$lista_options = "			lista[0]=\"<option value='0'\"+selecionado[0]+\">Impérios NPCs</option>\";\n";
		$lista_valores = "			lista_valores[0]=0;\n";		
		$index = 1;

		foreach ($resultados as $resultado) {
			$lista_options .= "			lista[{$index}]=\"<option value='{$resultado->id}'\"+selecionado[{$index}]+\">{$resultado->nome}</option>\";\n";
			$lista_valores .= "			lista_valores[{$index}]={$resultado->id};\n";
			$index++;
		}

		$this->html_lista = 
"		/******************
		function lista_imperios_html(id=0,args={id_remove:0, npcs:1})
		--------------------
		Cria a lista de Impérios
		id -- qual ID está selecionado
		id_remove -- qual ID será removido da lista
		npcs -- se é para mostrar Império NPC na lista (1 não mostra, 0 mostra)
		******************/
		function lista_imperios_html(id=0,args={id_remove:0, npcs:1}) {
			
			if (typeof(args) == 'string') {
				args = JSON.parse(args);
			
				if (typeof(args.id_remove) == 'undefined') {
					args.id_remove = 0;
				}
				if (typeof(args.npcs) == 'undefined') {
					args.npc = 1;
				}				
			}
			
			var lista=[];
			var lista_valores=[];
			var selecionado=[];
{$lista_valores}
			
			var html = \"			<select data-atributo='id_imperio' style='width: 100%'>\";
			for (let index = args.npcs; index < lista_valores.length; index++) {
				if (lista_valores[index] == id) {
					selecionado[index] = 'selected';
				} else {
					selecionado[index] = '';
				}
				if (lista_valores[index] == args.id_remove && args.npcs != 0) {
					lista[index] = '';
				}
			}
			
{$lista_options}
			for (let index = args.npcs; index < lista.length; index++) {
				html = html+lista[index];
			}
			html = html +\"			</select>\";


			return html;
		}";
	}
}

//Classe "lista_recursos"
//Mostra a lista de recursos, com os IDs
class lista_recursos
{
	public $html_lista;
	
	function __construct() {
		global $wpdb;
		$resultados = $wpdb->get_results("SELECT id, nome FROM colonization_recurso ORDER BY nome");

		$lista_valores = "";
		$lista_options = "";
		$lista_recursos = "";
		$index = 0;
		foreach ($resultados as $resultado) {
			$lista_options .= "			lista[{$index}]=\"<option value='{$resultado->id}'\"+selecionado[{$index}]+\">{$resultado->nome}</option>\";\n";
			$lista_valores .= "			lista_valores[{$index}]={$resultado->id};\n";
			$lista_recursos .= "			lista_recursos[{$resultado->id}]='{$resultado->nome}';\n";
			$index++;
		}

		$this->html_lista = "
		var lista_recursos = [];
		{$lista_recursos}
		
		/******************
		function lista_recursos_html(id=0)
		--------------------
		Cria a lista de recursos
		id -- qual ID está selecionado
		******************/
		function lista_recursos_html(id=0) {
			
			var lista=[];
			var lista_valores=[];
			var selecionado=[];
{$lista_valores}
			
			var html = \"			<select data-atributo='id_recurso' style='width: 100%'>\";
			for (var index = 0; index < lista_valores.length; index++) {
				if (lista_valores[index] == id) {
					selecionado[index] = 'selected';
				} else {
					selecionado[index] = '';
				}
			}
			
{$lista_options}
			for (index = 0; index < lista.length; index++) {
				html = html+lista[index];
			}
			html = html +\"			</select>\";


			return html;
		}";
	}
}

//Classe "lista_techs"
//Mostra a lista de Techs, com os IDs
class lista_techs
{
	public $html_lista;
	
	function __construct() {
		global $wpdb;
		
		$techs = new tech();
		
		$resultados = $techs->query_tech();
		
		/***
		$resultados = $wpdb->get_results("
		SELECT ct.id, ct.nome, ct.id_tech_parent
		FROM 
		(SELECT ct_1.id, 
		(CASE WHEN ct_1.id_tech_parent = 0 THEN ct_1.nome ELSE ct_2.nome END) AS nome,
		(CASE WHEN ct_1.id_tech_parent = 0 THEN ct_1.id ELSE ct_1.id_tech_parent END) AS id_tech_parent
		FROM colonization_tech AS ct_1
		LEFT JOIN colonization_tech AS ct_2
		ON ct_2.id = ct_1.id_tech_parent
		) AS ctord
		JOIN colonization_tech AS ct
		ON ct.id = ctord.id
		ORDER BY ct.belica, ctord.nome, ctord.id_tech_parent, ct.id
		");
		//***/
		
		$lista_valores = "";
		$lista_options = "";
		$index = 0;
		foreach ($resultados as $resultado) {
			$tech = new tech($resultado->id);
			
			$lista_options .= "			lista[{$index}]=\"<option value='{$tech->id}'\"+selecionado[{$index}]+\">{$tech->nome}</option>\";\n";
			$lista_valores .= "			lista_valores[{$index}]={$tech->id};\n";
			$index++;
		}

		$this->html_lista = 
"		/******************
		function lista_techs_html(id=0)
		--------------------
		Cria a lista de Techs
		id -- qual ID está selecionado
		******************/
		function lista_techs_html(id=0) {
			
			var lista=[];
			var lista_valores=[];
			var selecionado=[];
{$lista_valores}
			
			var html = \"			<select data-atributo='id_tech' style='width: 100%'>\";
			for (var index = 0; index < lista_valores.length; index++) {
				if (lista_valores[index] == id) {
					selecionado[index] = 'selected';
				} else {
					selecionado[index] = '';
				}
			}
			
{$lista_options}
			for (index = 0; index < lista.length; index++) {
				html = html+lista[index];
			}
			html = html +\"			</select>\";


			return html;
		}";
	}
}

//Classe "lista_planetas"
//Mostra a lista de planetas, com os IDs
class lista_planetas
{
	public $html_lista;
	
	function __construct() {
		global $wpdb;
		$resultados = $wpdb->get_results(
		"SELECT colonization_planeta.id, colonization_planeta.nome, colonization_planeta.posicao, colonization_estrela.X, colonization_estrela.Y, colonization_estrela.Z
		FROM colonization_planeta 
		LEFT JOIN colonization_estrela 
		ON colonization_estrela.id = colonization_planeta.id_estrela
		ORDER BY colonization_estrela.X, colonization_estrela.Y, colonization_estrela.Z, colonization_planeta.posicao");

		$lista_valores = "";
		$lista_options = "";
		$index = 0;
		foreach ($resultados as $resultado) {
			$lista_options .= "			lista[{$index}]=\"<option value='{$resultado->id}'\"+selecionado[{$index}]+\">{$resultado->nome} - {$resultado->X};{$resultado->Y};{$resultado->Z} / {$resultado->posicao}</option>\";\n";
			$lista_valores .= "			lista_valores[{$index}]={$resultado->id};\n";
			$index++;
		}

		$this->html_lista = 
"		/******************
		function lista_planetas_html(id=0)
		--------------------
		Cria a lista de planetas
		id -- qual ID está selecionado
		******************/
		function lista_planetas_html(id=0) {
			
			var lista=[];
			var lista_valores=[];
			var selecionado=[];
{$lista_valores}
			
			var html = \"			<select data-atributo='id_planeta' style='width: 100%'>\";
			for (var index = 0; index < lista_valores.length; index++) {
				if (lista_valores[index] == id) {
					selecionado[index] = 'selected';
				} else {
					selecionado[index] = '';
				}
			}
			
{$lista_options}
			for (index = 0; index < lista.length; index++) {
				html = html+lista[index];
			}
			html = html +\"			</select>\";


			return html;
		}";
	}
}

//Classe "lista_instalacoes"
//Mostra a lista de instalações, com os IDs
class lista_instalacoes
{
	public $html_lista;
	
	function __construct() {
		global $wpdb;
		$resultados = $wpdb->get_results(
		"SELECT id, nome
		FROM colonization_instalacao
		ORDER BY nome");

		$lista_valores = "";
		$lista_options = "";
		$index = 0;
		foreach ($resultados as $resultado) {
			$lista_options .= "			lista[{$index}]=\"<option value='{$resultado->id}'\"+selecionado[{$index}]+\">{$resultado->nome}</option>\";\n";
			$lista_valores .= "			lista_valores[{$index}]={$resultado->id};\n";
			$index++;
		}

		$this->html_lista = 
"		/******************
		function lista_instalacoes_html(id=0)
		--------------------
		Cria a lista de instalações
		id -- qual ID está selecionado
		******************/
		function lista_instalacoes_html(id=0) {
			
			var lista=[];
			var lista_valores=[];
			var selecionado=[];
{$lista_valores}
			
			var html = \"			<select data-atributo='id_instalacao' style='width: 100%'>\";
			for (var index = 0; index < lista_valores.length; index++) {
				if (lista_valores[index] == id) {
					selecionado[index] = 'selected';
				} else {
					selecionado[index] = '';
				}
			}
			
{$lista_options}
			for (index = 0; index < lista.length; index++) {
				html = html+lista[index];
			}
			html = html +\"			</select>\";


			return html;
		}";

		
		$resultados = $wpdb->get_results(
		"SELECT id, nome
		FROM colonization_instalacao
		WHERE publica=0
		ORDER BY nome");

		$lista_valores = "";
		$lista_options = "";
		$index = 0;
		foreach ($resultados as $resultado) {
			$lista_options .= "			lista[{$index}]=\"<option value='{$resultado->id}'\"+selecionado[{$index}]+\">{$resultado->nome}</option>\";\n";
			$lista_valores .= "			lista_valores[{$index}]={$resultado->id};\n";
			$index++;
		}		
		
		$this->html_lista .= "		
		/******************
		function lista_instalacoes_ocultas_html(id=0)
		--------------------
		Cria a lista de instalações
		id -- qual ID está selecionado
		******************/
		function lista_instalacoes_ocultas_html(id=0) {
			
			var lista=[];
			var lista_valores=[];
			var selecionado=[];
{$lista_valores}
			
			var html = \"			<select data-atributo='id_instalacao' style='width: 100%'>\";
			for (var index = 0; index < lista_valores.length; index++) {
				if (lista_valores[index] == id) {
					selecionado[index] = 'selected';
				} else {
					selecionado[index] = '';
				}
			}
			
{$lista_options}
			for (index = 0; index < lista.length; index++) {
				html = html+lista[index];
			}
			html = html +\"			</select>\";


			return html;
		}";		

		$resultados = $wpdb->get_results(
		"SELECT id, nome
		FROM colonization_tech
		WHERE publica=0
		ORDER BY nome");

		$lista_valores = "";
		$lista_options = "";
		$index = 0;
		foreach ($resultados as $resultado) {
			$lista_options .= "			lista[{$index}]=\"<option value='{$resultado->id}'\"+selecionado[{$index}]+\">{$resultado->nome}</option>\";\n";
			$lista_valores .= "			lista_valores[{$index}]={$resultado->id};\n";
			$index++;
		}		
		
		$this->html_lista .= "
		/******************
		function lista_techs_ocultas_html(id=0)
		--------------------
		Cria a lista de Techs
		id -- qual ID está selecionado
		******************/
		function lista_techs_ocultas_html(id=0) {
			
			var lista=[];
			var lista_valores=[];
			var selecionado=[];
{$lista_valores}
			
			var html = \"			<select data-atributo='id_tech' style='width: 100%'>\";
			for (var index = 0; index < lista_valores.length; index++) {
				if (lista_valores[index] == id) {
					selecionado[index] = 'selected';
				} else {
					selecionado[index] = '';
				}
			}
			
{$lista_options}
			for (index = 0; index < lista.length; index++) {
				html = html+lista[index];
			}
			html = html +\"			</select>\";


			return html;
		}";		
	}
}
?>