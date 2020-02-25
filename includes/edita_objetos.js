var objeto_em_edicao = false; //Define se está no modo de edição ou não

/******************
function pega_ascendente(objeto,tag)
--------------------
Pega o ascendente do objeto com o tag selecionado
objeto -- objeto escolhido
tag -- tag do ascendente
******************/
function pega_ascendente(objeto, tag) {
	var parent_node = objeto.parentNode;
	//Retroage até achar a linha
	while(parent_node.tagName != tag) {
		parent_node = parent_node.parentNode;
	}
	
	return parent_node;
}

/******************
function valida_imperio(objeto)
--------------------
Valida os dados do Império
objeto -- objeto sendo editado
******************/	
function valida_imperio(objeto) {
	
	var linha = pega_ascendente(objeto,"TR");
	var tabela = pega_ascendente(objeto,"TABLE");
	var celulas = linha.cells;
	var inputs_tabela = tabela.getElementsByTagName("INPUT");
	var inputs_linha = linha.getElementsByTagName("INPUT");
	var select_linha = linha.getElementsByTagName("SELECT");
	
	//Verifica se o nome do Império está preenchido
	for (index = 0; index < inputs_linha.length; index++) {
		if (inputs_linha[index].getAttribute('data-atributo') == "nome" && inputs_linha[index].type == "text" && inputs_linha[index].value == "") {
			alert('O nome do Império não pode ser deixado em branco!');
			return false;
		}
	}

	if (typeof select_linha[0] !== "undefined") {
		var id_jogador = select_linha[0].value;
		//Verifica se o jogador já tem um Império cadastrado. Cada jogador pode ter apenas um Império.
		for (index = 0; index < inputs_tabela.length; index++) {
			if (inputs_tabela[index].getAttribute('data-atributo') == "id_jogador" && inputs_tabela[index].getAttribute('value') == id_jogador) {
				alert('O jogador selecionado já tem um Império cadastrado! Por favor, escolha outro jogador!');
				return false;
			}
		}
	}

	return true; //Validou!
}

/******************
function valida_estrela(objeto)
--------------------
Valida os dados da Estrela
objeto -- objeto sendo editado
******************/	
function valida_estrela(objeto) {
	
	var linha = pega_ascendente(objeto,"TR");
	//var tabela = pega_ascendente(objeto,"TABLE");
	var celulas = linha.cells;
	//var inputs_tabela = tabela.getElementsByTagName("INPUT");
	var inputs_linha = linha.getElementsByTagName("INPUT");
	var select_linha = linha.getElementsByTagName("SELECT");
	
	//Verifica se o nome do Império está preenchido
	for (index = 0; index < inputs_linha.length; index++) {
		if (inputs_linha[index].type == "text" && inputs_linha[index].value == "") {
			alert('Nenhum dado pode ser deixado em branco!');
			return false;
		}
	}

	return true; //Validou!
}

/******************
function valida_generico(objeto)
--------------------
Valida os dados de um objeto genérico
objeto -- objeto sendo editado
******************/	
function valida_generico(objeto) {
	
	var linha = pega_ascendente(objeto,"TR");
	//var tabela = pega_ascendente(objeto,"TABLE");
	var celulas = linha.cells;
	//var inputs_tabela = tabela.getElementsByTagName("INPUT");
	var inputs_linha = linha.getElementsByTagName("INPUT");
	var select_linha = linha.getElementsByTagName("SELECT");
	
	//Verifica se o nome do Império está preenchido
	for (index = 0; index < inputs_linha.length; index++) {
		if (inputs_linha[index].type == "text" && inputs_linha[index].value == "") {
			alert('Nenhum dado pode ser deixado em branco!');
			return false;
		}
	}

	return true; //Validou!
}


/******************
function novo_imperio
--------------------
Insere um novo Império na lista
******************/
function novo_imperio() {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		return false;
	}
		
		objeto_em_edicao = true; //Bloqueia a edição de outros Impérios
		var tabela_imperios = document.getElementsByTagName('TABLE');
		tabela_imperios = tabela_imperios[0];
		var linha_nova = tabela_imperios.insertRow(-1);
		var dados_jogador = linha_nova.insertCell(0);
		var nome_imperio = linha_nova.insertCell(1);
		var populacao = linha_nova.insertCell(2);
		var pontuacao = linha_nova.insertCell(3);
		
		var lista_jogadores = lista_jogadores_html(); //Pega a lista de usuários do Fórum
		
		dados_jogador.innerHTML = "<input type='hidden' data-atributo='id_jogador' data-valor-original='' value=''></input>"
		+"<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
		+"<input type='hidden' data-atributo='where_clause' value='id_jogador'></input>"
		+"<input type='hidden' data-atributo='where_value' value=''></input>"
		+"<input type='hidden' data-atributo='funcao_validacao' value='valida_imperio'></input>"
		+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value=''></input>"
		+"<div data-atributo='nome_jogador'>"
		+lista_jogadores+"</div>"
		+"<div><a href='#' onclick='salva_objeto(this);'>Salvar</a> | <a href='#' onclick='cancela_edicao(this);'>Cancelar</a></div>";
		nome_imperio.innerHTML = "<div data-atributo='nome' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nome' data-ajax='true'></input></div>";
		populacao.innerHTML = "<div></div>";
		pontuacao.innerHTML = "<div></div>";
}

/******************
function nova_estrela
--------------------
Insere uma nova estrela na lista
******************/
function nova_estrela() {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		return false;
	}
		
		objeto_em_edicao = true; //Bloqueia a edição de outros Impérios
		var tabela_estrelas = document.getElementsByTagName('TABLE');
		tabela_estrelas = tabela_estrelas[0];
		var linha_nova = tabela_estrelas.insertRow(-1);
		
		var nome_estrela = linha_nova.insertCell(0);
		var estrela_x = linha_nova.insertCell(1);
		var estrela_y = linha_nova.insertCell(2);
		var estrela_z = linha_nova.insertCell(3);
		var estrela_tipo = linha_nova.insertCell(4);
		
		nome_estrela.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
		+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
		+"<input type='hidden' data-atributo='where_value' value=''></input>"
		+"<input type='hidden' data-atributo='funcao_validacao' value='valida_estrela'></input>"
		+"<div data-atributo='nome' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nome' data-ajax='true'></input></div>"
		+"<div><a href='#' onclick='salva_objeto(this);'>Salvar</a> | <a href='#' onclick='cancela_edicao(this);'>Cancelar</a></div>";
		estrela_x.innerHTML = "<div data-atributo='X' data-style='width: 100%;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='X' data-ajax='true' style='width: 100%;'></input></div>";
		estrela_y.innerHTML = "<div data-atributo='Y' data-style='width: 100%;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='Y' data-ajax='true' style='width: 100%;'></input></div>";
		estrela_z.innerHTML = "<div data-atributo='Z' data-style='width: 100%;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='Z' data-ajax='true' style='width: 100%;'></input></div>";
		estrela_tipo.innerHTML = "<div data-atributo='tipo' data-editavel='true' data-valor-original=''><input type='text' data-atributo='tipo' data-ajax='true'></input></div>";
}

/******************
function novo_recurso
--------------------
Insere um novo recurso
******************/
function novo_recurso() {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		return false;
	}
		
		objeto_em_edicao = true; //Bloqueia a edição de outros Impérios
		var tabela = document.getElementsByTagName('TABLE');
		tabela = tabela[0];
		var linha_nova = tabela.insertRow(-1);
		
		var nome = linha_nova.insertCell(0);
		var descricao = linha_nova.insertCell(1);
		var acumulavel = linha_nova.insertCell(2);
		
		nome.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
		+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
		+"<input type='hidden' data-atributo='where_value' value=''></input>"
		+"<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>"
		+"<div data-atributo='nome' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nome' data-ajax='true'></input></div>"
		+"<div><a href='#' onclick='salva_objeto(this);'>Salvar</a> | <a href='#' onclick='cancela_edicao(this);'>Cancelar</a></div>";
		descricao.innerHTML = "<div data-atributo='descricao' data-editavel='true' data-valor-original=''><input type='text' data-atributo='descricao' data-ajax='true'></input></div>";
		acumulavel.innerHTML = "<div data-atributo='acumulavel' data-editavel='true' data-valor-original=''><input type='text' data-atributo='acumulavel' data-ajax='true'></input></div>";
}


/******************
function cancela_edicao
--------------------
Cancela a edição do objeto
******************/	
function cancela_edicao(objeto) {
	objeto_em_edicao = false;
	
	var tabela_objetos = pega_ascendente(objeto,"TABLE");
	tabela_objetos.deleteRow(-1);
}

/******************
function atualiza_objeto(objeto, dados)
--------------------
Atualiza os dados do objeto, em particular os dados "hidden" e também os data-valor-original
objeto -- objeto sendo atualizado
dados -- dados atualizados
******************/	
function atualiza_objeto(objeto, dados) {
	var linha = objeto;
	var divs = "";
	var inputs = "";
	var atributo = "";
	var valor_atributo = "";
	
	divs = linha.getElementsByTagName('DIV');
	inputs = linha.getElementsByTagName('INPUT'); 
	
	for (var index = 0; index < divs.length; index++) {
		if (divs[index].getAttribute('data-valor-original') !== null) {
			atributo = divs[index].getAttribute('data-atributo');
			if (typeof dados[atributo] !== "undefined" && dados[atributo] !== null) {
				divs[index].setAttribute('data-valor-original',dados[atributo]);
			}
		}
	}
	
	for (var index = 0; index < inputs.length; index++) {
		if (inputs[index].getAttribute('data-valor-original') !== null) {
			atributo = inputs[index].getAttribute('data-atributo');
			if (typeof dados[atributo] !== "undefined" && dados[atributo] !== null) {
				inputs[index].setAttribute('data-valor-original',dados[atributo]);
				inputs[index].setAttribute('value',dados[atributo]);
			}
		}
	}
}

/******************
function edita_objeto(objeto)
--------------------
Edita um objeto
objeto -- objeto a ser editado
******************/	
function edita_objeto(objeto) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		return false;
	}
	
	objeto_em_edicao = true;

	var linha = pega_ascendente(objeto,"TR");
	var celulas = linha.cells;
	var celula = "";
	var divs = "";
	var inputs = "";
	var atributo = "";
	var valor_atributo = "";
	var editavel = "";
	var data_estilo = "";
	
	//Pega cada uma das células e altera para o modo de edição, caso seja editável
	for (var index = 0; index < celulas.length; index++) {
		celula = celulas[index];
		divs = celula.getElementsByTagName('div'); //Os dados editáveis ficam sempre dentro de divs
		if (index == 0) {//A primeira célula é especial, pois tem dois divs -- um com dados e outro com os links para Salvar e Excluir, que no modo edição são alterados para Salvar e Cancelar
			divs[1].innerHTML = "<a href='#' onclick='salva_objeto(this);'>Salvar</a> | <a href='#' onclick='salva_objeto(this,true);'>Cancelar</a>";
		}
		
		for (var index_div = 0; index_div < divs.length; index_div++) {
			editavel = divs[index_div].getAttribute('data-editavel');
			if (editavel) {
				atributo = divs[index_div].getAttribute('data-atributo');
				valor_atributo = divs[index_div].innerHTML;
				data_estilo = divs[index_div].getAttribute('data-style');
				if (data_estilo !== "undefined" && data_estilo !== null) {
					data_estilo = " style='"+data_estilo+"'";
				}
				divs[index_div].innerHTML = "<input type='text' data-atributo='"+atributo+"' data-ajax='true' value='"+valor_atributo+"'"+data_estilo+"></input>";

			}
		}
	}
}

/******************
function chama_funcao_validacao(objeto,funcao)
--------------------
Função do tipo "helper" para chamar uma função de validação
funcao -- função a ser chamada
objeto -- objeto sendo editado
******************/	
function chama_funcao_validacao(objeto, funcao) {
	// find object
	var fn = window[funcao];

	// is object a function?
	if (typeof fn === "function") {
		var retorno = fn(objeto);
	}

	return retorno;
}

/******************
function pega_dados_objeto(objeto)
--------------------
Pega os dados do objeto
objeto -- objeto sendo editado
******************/	
function pega_dados_objeto(objeto) {
	var objeto_editado = [];
	var linha = pega_ascendente(objeto,"TR");
	var tabela = pega_ascendente(objeto,"TABLE");
	var celulas = linha.cells;
	var inputs_linha = linha.getElementsByTagName("INPUT");
	var select_linha = linha.getElementsByTagName("SELECT");
	
	
	var funcao_valida_objeto = "";
	objeto_editado['nome_tabela'] = tabela.getAttribute('data-tabela');
	objeto_editado['dados_ajax'] = "";
	
	//Pega cada um dos inputs
	for (var index = 0; index < inputs_linha.length; index++) {
		if(inputs_linha[index].getAttribute('data-atributo') != null) {
			objeto_editado[inputs_linha[index].getAttribute('data-atributo')] = inputs_linha[index]; //Salva os inputs como variáveis
		}
		
		if (inputs_linha[index].getAttribute('data-atributo') == "funcao_validacao") {
			objeto_editado['funcao_valida_objeto'] = inputs_linha[index].value;
		} else if (inputs_linha[index].getAttribute('data-atributo') == "where_clause") {
			objeto_editado['where_clause'] = inputs_linha[index].value;
		} else if (inputs_linha[index].getAttribute('data-atributo') == "where_value") {
			objeto_editado['where_value'] = inputs_linha[index].value;
		} else {
			if (inputs_linha[index].getAttribute('data-ajax')) {//Só salva um atributo que seja "passável" para o AJAX. Normalmente é proveniente de um <div> que seja editável
				objeto_editado['dados_ajax'] = objeto_editado['dados_ajax']+"&"+inputs_linha[index].getAttribute('data-atributo')+"="+inputs_linha[index].value;
			}
		}
	}
	
	//Além de INPUT, existe a possibilidade dos dados serem passados via SELECT
	for (index = 0; index < select_linha.length; index++) {
		objeto_editado['dados_ajax'] = objeto_editado['dados_ajax']+"&"+select_linha[index].getAttribute('data-atributo')+"="+select_linha[index].value;
		objeto_editado[select_linha[index].getAttribute('data-atributo')] = select_linha[index];
	}

	return objeto_editado;

}


/******************
function salva_objeto(objeto, cancela = false)
--------------------
Salva o Império sendo editado.
objeto -- objeto sendo editado
cancela = false -- Define se é para salvar ou apenas cancelar a edição
******************/	
function salva_objeto(objeto, cancela = false) {
	if (cancela) {
		var desabilita = desabilita_edicao_objeto(objeto, cancela);
		objeto_em_edicao = false;		
		return false;
	}

	var objeto_editado = pega_dados_objeto(objeto);//Pega os dados do objeto
	
	if (objeto_editado['where_value'] == "") {//Se a o valor do WHERE estiver em branco, significa que estamos criando um objeto novo
		var where_clause = objeto_editado['where_clause'];
		objeto_editado['where_value'] = objeto_editado[where_clause].value;
	}
	//Cria o string que será passado para o AJAX
	objeto_editado['dados_ajax'] = "post_type=POST&action=salva_objeto&tabela="+objeto_editado['nome_tabela']+objeto_editado['dados_ajax']+"&where_clause="+objeto_editado['where_clause']+"&where_value="+objeto_editado['where_value'];
	
	var valida_dados = true;
	if (objeto_editado['funcao_valida_objeto'] != "") { //Valida os dados através de uma função específica, definida para cada objeto
		valida_dados = chama_funcao_validacao(objeto, objeto_editado['funcao_valida_objeto']);
	}
	
	if (!valida_dados) {
		return false;
	}

	//Envia a chamada de AJAX para salvar o objeto
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var resposta = JSON.parse(this.responseText);
			if (resposta.resposta_ajax == "SALVO!") {
				//Após salvar os dados, remove os "inputs" e transforma a linha em texto, deixando o Império passível de ser editado
				var objeto_desabilitado = desabilita_edicao_objeto(objeto);
				var objeto_atualizado = atualiza_objeto(objeto_desabilitado,resposta[0]); //O objeto salvo está no array resposta[0]
				objeto_em_edicao = false; //Libera a edição de outros objetos
			} else {
				alert(resposta.resposta_ajax);
			}
		}
	};
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(objeto_editado['dados_ajax']);

	objeto_em_edicao = true; //Trava o objeto em modo de edição até que o AJAX libere
}

/******************
function excluir_objeto(objeto,funcao_confirmacao)
--------------------
Exclui um objeto
objeto -- objeto sendo editado
funcato_confirmacao -- mensagem de confirmação a ser exibida para o usuário
******************/	
function excluir_objeto(objeto) {
	if (objeto_em_edicao) {
		alert('Não é possível deletar um objeto enquanto outro está em edição!');
		return false;
	}
	
	var linha_imperio = pega_ascendente(objeto,"TR");
	var objeto_editado = pega_dados_objeto(objeto);//Pega os dados do objeto
	
	if (typeof objeto_editado['mensagem_exclui_objeto'] === "undefined") {
		var texto_confirmacao = "Tem certeza que deseja deletar esse objeto?";
	} else {
		var texto_confirmacao = objeto_editado['mensagem_exclui_objeto'].value;
	}
	
	var confirma = confirm(texto_confirmacao);
	
	objeto_editado['dados_ajax'] = "post_type=POST&action=deleta_objeto&tabela="+objeto_editado['nome_tabela']+"&where_clause="+objeto_editado['where_clause']+"&where_value="+objeto_editado['where_value'];
	//Se for mesmo deletar, remove a linha
	if (confirma) {
		//Envia a chamada de AJAX para remover o usuário
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var resposta = JSON.parse(this.responseText);
				if (resposta.resposta_ajax == "DELETADO!") {
					linha_imperio.remove(); //Remove a linha
					objeto_em_edicao = false;
				} else {
					alert(resposta.resposta_ajax);
					objeto_em_edicao = false;
				}
			}
		};
		xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send(objeto_editado['dados_ajax']);

		objeto_em_edicao = true;
	}
}

/******************
function desabilita_edicao_objeto(objeto, cancela) 
--------------------
Desabilita o modo de edição e atualiza os dados
objeto -- objeto sendo desabilitado
cancela = false -- define se pega os dados originais ou os novos
******************/	
function desabilita_edicao_objeto(objeto, cancela = false) {

	var linha = pega_ascendente(objeto,"TR");
	var inputs = linha.getElementsByTagName('INPUT');
	var selects = linha.getElementsByTagName('select');
	var div = "";
	
	//Pega cada um dos inputs e tira do modo de edição
	var tamanho_maximo = inputs.length-1;
	for (var index = tamanho_maximo; index >-1; index--) {
		if (inputs[index].type == 'text') {
			div = pega_ascendente(inputs[index],"DIV");
			if (cancela) {
				div.innerHTML = div.getAttribute('data-valor-original');
			} else {
				div.innerHTML = inputs[index].value;
			}
		}
	}

	//Além de INPUT, existe a possibilidade dos dados serem passados via SELECT
	for (index = 0; index < selects.length; index++) {
		div = pega_ascendente(selects[index],"DIV");
		div.innerHTML = selects[index].options[selects[index].selectedIndex].innerHTML;
	}
	
	//A primeira célula é especial, pois tem dois divs -- um com dados e outro com os links para Salvar e Excluir, que no modo edição são alterados para Salvar e Cancelar
	var celula = linha.cells[0]
	var divs = celula.getElementsByTagName("DIV");
	divs[1].innerHTML = "<a href='#' onclick='edita_objeto(this);'>Editar</a> | <a href='#' onclick='excluir_objeto(this);'>Excluir</a>";
	
	return linha;
}