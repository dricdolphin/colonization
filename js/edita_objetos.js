var objeto_em_edicao = false; //Define se está no modo de edição ou não
var range_em_edicao = false; //Define se o "range" está em edição

/******************
function pega_ascendente(objeto,tag)
--------------------
Pega o ascendente do objeto com o tag selecionado
objeto -- objeto escolhido
tag -- tag do ascendente
******************/
function pega_ascendente(objeto, tag) {
	if (objeto.tagName == tag) { return objeto; }
	var parent_node = objeto.parentNode;
	//Retroage até achar a linha
	while(parent_node.tagName != tag) {
		parent_node = parent_node.parentNode;
	}
	
	return parent_node;
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
	var where_clause = ""
	var valor_atributo = "";
	
	divs = linha.getElementsByTagName('DIV');
	inputs = linha.getElementsByTagName('INPUT'); 
	
	for (var index = 0; index < divs.length; index++) {
		if (divs[index].getAttribute('data-valor-original') !== null) {
			atributo = divs[index].getAttribute('data-atributo');
			//HARDCODED -- Adiciona o link para gerenciar os objetos que são gerenciáveis
			if(divs[index].getAttribute('data-atributo') == "gerenciar") {
				divs[index].childNodes[0].style.visibility="visible";
				//divs[index].childNodes[0].addEventListener("click",function () {chama_funcao_validacao(objeto,"gerenciar_objeto")});
			}
			if (typeof dados[atributo] !== "undefined") {
				//Só atualiza o innerHTML de divs que não contenham objetos
				if (dados[atributo] !== null) {
					if (divs[index].hasChildNodes()) {
						if (divs[index].childNodes[0].tagName != "INPUT" && divs[index].childNodes[0].tagName != "TEXTAREA") {
							divs[index].setAttribute('data-valor-original',dados[atributo]);
							divs[index].innerHTML = dados[atributo];
						}
					}
				}
			}
		}	
	}
	
	for (var index = 0; index < inputs.length; index++) {
		//HARDCODED -- Atualiza o valor do where_value
		if (inputs[index].getAttribute('data-atributo') == "where_clause") {
			where_clause = inputs[index].value;
		}
		if (inputs[index].getAttribute('data-atributo') == "where_value" && inputs[index].getAttribute('data-inalteravel') != "true") {
			inputs[index].value = dados[where_clause];
		}		
		if (inputs[index].getAttribute('data-valor-original') !== null) {
			atributo = inputs[index].getAttribute('data-atributo');
			if (typeof dados[atributo] !== "undefined" ) {
				if (dados[atributo] !== null) {
					inputs[index].setAttribute('data-valor-original',dados[atributo]);
					inputs[index].setAttribute('value',dados[atributo]);
				}
			}
		}
	}
	
	return objeto;
}

/******************
function cancela_edicao
--------------------
Cancela a edição do objeto
******************/	
function cancela_edicao(evento, objeto) {
	objeto_em_edicao = false;
	
	var tabela_objetos = pega_ascendente(objeto,"TABLE");
	tabela_objetos.deleteRow(-1);
	
	evento.preventDefault();
	return false;
}

/******************
function edita_objeto(objeto)
--------------------
Edita um objeto
objeto -- objeto a ser editado
******************/	
function edita_objeto(evento, objeto) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
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
	var data_type = "";
	var data_checked = "";
	var data_ajax = "";
	
	
	//Pega cada uma das células e altera para o modo de edição, caso seja editável
	for (var index = 0; index < celulas.length; index++) {
		celula = celulas[index];
		divs = celula.getElementsByTagName('div'); //Os dados editáveis ficam sempre dentro de divs
		if (index == 0) {//A primeira célula é especial, pois tem dois divs -- um com dados e outro com os links para Salvar e Excluir, que no modo edição são alterados para Salvar e Cancelar
			for (let index_div = 0; index_div < divs.length; index_div++) {
				if (divs[index_div].getAttribute('data-atributo') == "gerenciar") {
					divs[index_div].innerHTML = "<a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return salva_objeto(event, this, true);'>Cancelar</a>";
				} else if (divs[index_div].getAttribute('data-atributo') == "processa_string") {
					divs[index_div].style.visibility = "visible";
				}
			}
		}
		
		
		for (var index_div = 0; index_div < divs.length; index_div++) {
			editavel = divs[index_div].getAttribute('data-editavel');
			data_ajax= "";
			data_id = "";
			if (editavel) {
				atributo = divs[index_div].getAttribute('data-atributo');
				valor_atributo = divs[index_div].innerHTML;
				data_estilo = divs[index_div].getAttribute('data-style');
				if (data_estilo !== "undefined" && data_estilo !== null) {
					data_estilo = " style='"+data_estilo+"'";
				}
				
				if (divs[index_div].getAttribute('data-ajax') == "false") {
					data_ajax = "data-ajax='false'";
				} else {
					data_ajax = "data-ajax='true'";
				}
				
				if (divs[index_div].getAttribute('data-id') !== null) {
					data_id = "id="+divs[index_div].getAttribute('data-id');
				}
				
				if (divs[index_div].getAttribute('data-type') !== null) {
					if (divs[index_div].getAttribute('data-type') == "checkbox") {
						inputs = divs[index_div].getElementsByTagName("INPUT");
						inputs[0].disabled=false;
					} else if (divs[index_div].getAttribute('data-type') == "select") {
						var lista = chama_funcao_validacao(divs[index_div].getAttribute('data-id-selecionado'),divs[index_div].getAttribute('data-funcao'));
						divs[index_div].innerHTML = lista;
					} else if (divs[index_div].getAttribute('data-type') == "textarea") {
						divs[index_div].innerHTML = "<textarea data-atributo='"+atributo+"' "+data_id+" "+data_ajax+" "+data_estilo+">"+valor_atributo+"</textarea>";
					}
				} else {
					divs[index_div].innerHTML = "<input type='text' data-atributo='"+atributo+"' "+data_id+" "+data_ajax+" value='"+valor_atributo+"'"+data_estilo+"></input>";
				}
			}
		}
	}
	
	evento.preventDefault();
	return false;
}

/******************
function chama_funcao_validacao(objeto,funcao)
--------------------
Função do tipo "helper" para chamar uma função de validação
funcao -- função a ser chamada
objeto -- objeto sendo editado
******************/	
function chama_funcao_validacao(objeto, funcao, argumentos="") {
	var fn = window[funcao];
	
	if (typeof fn === "function") {
		if (argumentos != "") {
			var retorno = fn(objeto, argumentos);
		} else {
			var retorno = fn(objeto);
		}
	} else {//Caso não encontre a função de validação, usa a validação genérica
		var retorno = valida_generico(objeto);
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
	var textarea_linha = linha.getElementsByTagName("TEXTAREA")
	var select_linha = linha.getElementsByTagName("SELECT");
	var checkbox_checked = "";

	var inputs_linha_temp = [];
	for (var index = 0; index < inputs_linha.length; index++) {
		inputs_linha_temp[index] = inputs_linha[index];
	}
	
	for (let index_textarea = 0; index_textarea < textarea_linha.length; index_textarea++) {
		index_temp = index + index_textarea;
		if (inputs_linha_temp[index_temp] !== undefined) {
			index_temp++;
		}
		inputs_linha_temp[index_temp] = textarea_linha[index_textarea];
	}	
	
	inputs_linha = inputs_linha_temp;
	
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
		} else if (inputs_linha[index].getAttribute('data-atributo') == "funcao_pos_processamento"){
			objeto_editado['funcao_pos_processamento_objeto'] = inputs_linha[index].value;
		} else if (inputs_linha[index].getAttribute('data-atributo') == "where_clause") {
			objeto_editado['where_clause'] = inputs_linha[index].value;
		} else if (inputs_linha[index].getAttribute('data-atributo') == "where_value") {
			objeto_editado['where_value'] = inputs_linha[index].value;
		} else {
			if (inputs_linha[index].getAttribute('data-ajax') == "true") {//Só salva um atributo que seja "passável" para o AJAX. Normalmente é proveniente de um <div> que seja editável
				if (inputs_linha[index].type == "checkbox") {
					if (inputs_linha[index].checked) {
						checkbox_checked=1;
					} else {
						checkbox_checked=0;
					}
					objeto_editado['dados_ajax'] = objeto_editado['dados_ajax']+"&"+inputs_linha[index].getAttribute('data-atributo')+"="+checkbox_checked;
				} else {
					objeto_editado['dados_ajax'] = objeto_editado['dados_ajax']+"&"+inputs_linha[index].getAttribute('data-atributo')+"="+inputs_linha[index].value;
				}
			}
		}
	}
	
	//Além de INPUT, existe a possibilidade dos dados serem passados via SELECT
	for (index = 0; index < select_linha.length; index++) {
		objeto_editado['dados_ajax'] = objeto_editado['dados_ajax']+"&"+select_linha[index].getAttribute('data-atributo')+"="+select_linha[index].options[select_linha[index].selectedIndex].value;
		objeto_editado[select_linha[index].getAttribute('data-atributo')] = select_linha[index];
	}

	return objeto_editado;
}


/******************
function salva_objeto(evento, objeto, cancela = false)
--------------------
Salva o Império sendo editado.
objeto -- objeto sendo editado
cancela = false -- Define se é para salvar ou apenas cancelar a edição
******************/	
function salva_objeto(evento, objeto, cancela = false) {
	var objeto_editado = pega_dados_objeto(objeto);//Pega os dados do objeto
	if (typeof objeto_editado['funcao_pos_processamento_objeto'] != "") {
		var pos_processamento = objeto_editado['funcao_pos_processamento_objeto'];
	}
	
	if (cancela) {
		var objeto_desabilitado = desabilita_edicao_objeto(objeto, cancela);
		
		var processa = true;
		if (typeof pos_processamento !== "undefined") {
			processa = chama_funcao_validacao(objeto_desabilitado, pos_processamento, true);
		}
		
		objeto_em_edicao = false;
		
		evento.preventDefault();
		return false;
	}

	if (objeto_editado['where_value'] == "") {//Se a o valor do WHERE estiver em branco, significa que estamos criando um objeto novo
		var where_clause = objeto_editado['where_clause'];
		objeto_editado['where_value'] = objeto_editado[where_clause].value;
	}
	
	var valida_dados = true;
	if (objeto_editado['funcao_valida_objeto'] != "") { //Valida os dados através de uma função específica, definida para cada objeto
		valida_dados = chama_funcao_validacao(objeto, objeto_editado['funcao_valida_objeto']);
	}

	if (!valida_dados) {
		evento.preventDefault();
		return false;
	}

	objeto_editado = pega_dados_objeto(objeto);//Pega os dados do objeto_editado pois eles podem ter sido modificados pelas funções de validação e pós-processamento
	//Cria o string que será passado para o AJAX
	objeto_editado['dados_ajax'] = "post_type=POST&action=salva_objeto&tabela="+objeto_editado['nome_tabela']+objeto_editado['dados_ajax']+"&where_clause="+objeto_editado['where_clause']+"&where_value="+objeto_editado['where_value'];

	//Envia a chamada de AJAX para salvar o objeto
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var resposta = JSON.parse(this.responseText);
			if (resposta.resposta_ajax == "SALVO!") {
				//Após salvar os dados, remove os "inputs" e transforma a linha em texto, deixando o Império passível de ser editado
				var objeto_desabilitado = desabilita_edicao_objeto(objeto);
				var objeto_atualizado = atualiza_objeto(objeto_desabilitado,resposta[0]); //O objeto salvo está no array resposta[0]
				if (typeof pos_processamento !== "undefined") {
					var processa = chama_funcao_validacao(objeto_desabilitado, pos_processamento);
				}
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
	evento.preventDefault();
	return false;
}

/******************
function excluir_objeto(objeto,funcao_confirmacao)
--------------------
Exclui um objeto
objeto -- objeto sendo editado
funcato_confirmacao -- mensagem de confirmação a ser exibida para o usuário
******************/	
function excluir_objeto(evento, objeto) {
	if (objeto_em_edicao) {
		alert('Não é possível deletar um objeto enquanto outro está em edição!');
		
		evento.preventDefault();
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
		
		evento.preventDefault();
		return false;
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
	var inputs = linha.getElementsByTagName("INPUT");
	var textarea_linha = linha.getElementsByTagName("TEXTAREA");
	var selects = linha.getElementsByTagName("SELECT");
	var div = "";
	var checkbox_checked = "";

	var inputs_linha_temp = [];
	for (var index = 0; index < inputs.length; index++) {
		inputs_linha_temp[index] = inputs[index];
	}
	
	for (let index_textarea = 0; index_textarea < textarea_linha.length; index_textarea++) {
		index_temp = index + index_textarea;
		if (inputs_linha_temp[index_temp] !== undefined) {
			index_temp++;
		}
		inputs_linha_temp[index_temp] = textarea_linha[index_textarea];
	}	
	
	inputs = inputs_linha_temp;

	//Pega cada um dos inputs e tira do modo de edição
	var tamanho_maximo = inputs.length-1;
	for (var index = tamanho_maximo; index >-1; index--) {
		if (inputs[index].type == 'text' || inputs[index].tagName =='TEXTAREA') {
			div = pega_ascendente(inputs[index],"DIV");
			if (cancela) {
				div.innerHTML = div.getAttribute('data-valor-original');
			} else {
				div.innerHTML = inputs[index].value;
			}
		} else if (inputs[index].type == 'checkbox') {
			div = pega_ascendente(inputs[index],"DIV");
				if (div.getAttribute('data-valor-original') == 0) {
					checkbox_checked = false;
				} else {
					checkbox_checked = true;
				}
			if (cancela) {
				inputs[index].checked = checkbox_checked;
				inputs[index].disabled = true;
			} else {
				inputs[index].disabled = true;
			}
		}
	}

	//Além de INPUT, existe a possibilidade dos dados serem passados via SELECT
	for (index = 0; index < selects.length; index++) {
		div = pega_ascendente(selects[index],"DIV");
		if (cancela) {
			div.innerHTML = div.getAttribute('data-valor-original');
		} else {
			div.setAttribute('data-id-selecionado',selects[index].value);
			div.setAttribute('data-valor-original',selects[index].options[selects[index].selectedIndex].innerHTML);
			if (div.getAttribute('data-editavel') == 'true') {
				div.innerHTML = selects[index].options[selects[index].selectedIndex].innerHTML;
			}
		}
	}
	
	//A primeira célula NORMALMENTE é especial, pois tem divs com dados como Id e outros, e links de gerenciamento, que no modo edição são alterados para Salvar e Cancelar
	var celula = linha.cells[0]
	var divs = celula.getElementsByTagName("DIV");
	for (let index=0; index < divs.length; index++) {
		if (divs[index].getAttribute('data-atributo') == "gerenciar") {
			divs[index].innerHTML = "<a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a>";
		} else if (divs[index].getAttribute('data-atributo') == "processa_string") {
			divs[index].style.visibility = "hidden";
		}
	}
	return linha;
}

/******************
function altera_acao(objeto) 
--------------------
Desabilita o modo de edição e atualiza os dados
objeto -- objeto sendo desabilitado
cancela = false -- define se pega os dados originais ou os novos
******************/	
function altera_acao(evento, objeto) {
	if (!range_em_edicao || range_em_edicao == objeto) {
		range_em_edicao = objeto;
		
		var linha = pega_ascendente(objeto,"TR");
		var inputs = linha.getElementsByTagName("INPUT");
		var selects = linha.getElementsByTagName("SELECT");
		var label = linha.getElementsByTagName("LABEL");
		var tds = linha.getElementsByTagName("TD");
		
		//***
		for(let index=0; index<tds.length; index++) {
			//data-atributo='gerenciar'			
			if (typeof(tds[index].childNodes[0].getAttribute) === "function") {
				if (tds[index].childNodes[0].getAttribute("data-atributo") == 'gerenciar') {
					if (tds[index].childNodes[0].style.visibility == "hidden") {	
						tds[index].childNodes[0].style.visibility = "visible";
					}
				}
			}
		}
		//***/
		
		label[0].innerText = objeto.value;
	} else {
		alert('Já existe uma ação em edição!');
		objeto.value = objeto.getAttribute('data-valor-original');
		
		evento.preventDefault();
		return false;
	}
}

/******************
function efetua_acao(evento, objeto) 
--------------------
Tenta atualizar os dados
******************/	
function efetua_acao (evento, objeto) {
	var objeto_editado = pega_dados_objeto(objeto);//Pega os dados do objeto
	var linha = pega_ascendente(objeto,"TR");
	var divs = linha.getElementsByTagName('DIV');
	var inputs = linha.getElementsByTagName('INPUT');
	var labels = linha.getElementsByTagName('LABEL');
	var dados = []; //Dados que serão enviados para a validação
	
	for (index=0;index<inputs.length;index++) {
		if (inputs[index].getAttribute('data-atributo') == "turno" || inputs[index].getAttribute('data-atributo') == "id_imperio" || inputs[index].getAttribute('data-atributo') == "id_instalacao" || inputs[index].getAttribute('data-atributo') == "id_planeta_instalacoes" || inputs[index].getAttribute('data-atributo') == "id_planeta") {
			dados[inputs[index].getAttribute('data-atributo')] = inputs[index].value;
		} else if (inputs[index].getAttribute('data-atributo') == "pop") {
			//No caso do atributo "pop", precisamos validar a DIFERENÇA entre o valor já salvo (data-valor-original) e o valor novo, para verificar se estamos ou não ultrapassando algum limite de consumo
			dados['pop_original'] = inputs[index].getAttribute('data-valor-original');
			dados['pop'] = inputs[index].value;
		}
	}	
	
	let valida = valida_acao(dados); //Valida os dados
	
	if (!valida) {
		salva_acao(evento, objeto,true); //Se não liberou, falhou a validação, então cancela a ação.	
	} else {
		salva_acao(evento, objeto); //Pode salvar!
		
	}
	
	evento.preventDefault();
	return false;
}

/******************
function salva_acao(objeto, cancela = false)
--------------------
Salva uma Ação sendo editada
objeto -- objeto sendo editado
cancela = false -- Define se é para salvar ou apenas cancelar a edição
******************/	
function salva_acao(evento, objeto, cancela = false) {
	var objeto_editado = pega_dados_objeto(objeto);//Pega os dados do objeto
	var linha = pega_ascendente(objeto,"TR");
	var divs = linha.getElementsByTagName('DIV');
	var inputs = linha.getElementsByTagName('INPUT');
	var labels = linha.getElementsByTagName('LABEL');
	var dados = []; //Dados que serão enviados para a validação
	//Pega a data atual
	var data_atual = new Date();
	var dia_atual = data_atual.getDate();
	var mes_atual = data_atual.getMonth();
	var ano_atual = data_atual.getFullYear();
	var hora_atual = data_atual.getHours();
	var minuto_atual = data_atual.getMinutes();
	
	if (cancela) {
		//***
		for (var index=0;index<divs.length;index++) {
			if (divs[index].getAttribute('data-atributo') == "gerenciar") {
				divs[index].style.visibility = "hidden";
			}
		}
		//***/
		
		for (index=0;index<inputs.length;index++) {
			if (inputs[index].getAttribute('data-atributo') == "pop") {
				var index_range_pop = index;
				inputs[index].value = inputs[index].getAttribute("data-valor-original");
			}
		}

		for (index=0;index<labels.length;index++) {
			if (labels[index].getAttribute('data-atributo') == "pop") {
				labels[index].innerText = inputs[index_range_pop].value;
			}
		}
		
		range_em_edicao = false;
		
		evento.preventDefault();
		return false;
	}

	if (objeto_editado['where_value'] == "") {//Se a o valor do WHERE estiver em branco, significa que estamos criando um objeto novo
		var where_clause = objeto_editado['where_clause'];
		objeto_editado['where_value'] = objeto_editado[where_clause].value;
	}

	for (index=0;index<inputs.length;index++) {
		if (inputs[index].getAttribute('data-atributo') == "turno" || inputs[index].getAttribute('data-atributo') == "id_imperio" || inputs[index].getAttribute('data-atributo') == "id_instalacao" || inputs[index].getAttribute('data-atributo') == "id_planeta_instalacoes" || inputs[index].getAttribute('data-atributo') == "id_planeta") {
			dados[inputs[index].getAttribute('data-atributo')] = inputs[index].value;
		} else if (inputs[index].getAttribute('data-atributo') == "pop") {
			//No caso do atributo "pop", precisamos validar a DIFERENÇA entre o valor já salvo (data-valor-original) e o valor novo, para verificar se estamos ou não ultrapassando algum limite de consumo
			dados['pop_original'] = inputs[index].getAttribute('data-valor-original');
			dados['pop'] = inputs[index].value;
		}
	}
	
	if (!valida_acao(dados)) {
		evento.preventDefault();
		return false;
	}

	//Cria o string que será passado para o AJAX
	objeto_editado['dados_ajax'] = "post_type=POST&action=salva_objeto&tabela="+objeto_editado['nome_tabela']+objeto_editado['dados_ajax']+"&where_clause="+objeto_editado['where_clause']+"&where_value="+objeto_editado['where_value'];	

	//Envia a chamada de AJAX para salvar o objeto
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var resposta = JSON.parse(this.responseText);
			if (resposta.resposta_ajax == "SALVO!") {
				//Após salvar os dados, remove os "inputs" e transforma a linha em texto, deixando o Império passível de ser editado
				//var objeto_desabilitado = desabilita_edicao_objeto(objeto);
				var linha = pega_ascendente(objeto,"TR");
				var objeto_atualizado = atualiza_objeto(linha,resposta[0]); //O objeto salvo está no array resposta[0]
				var divs = objeto_atualizado.getElementsByTagName("DIV");
				var inputs = objeto_atualizado.getElementsByTagName("INPUT");
				//***
				for (var index=0;index<divs.length;index++) {
					if (divs[index].getAttribute('data-atributo') == "gerenciar") {
						divs[index].style.visibility = "hidden";
					} 
				}
				//***/
				
				for (index=0;index<inputs.length;index++) {
					if(inputs[index].getAttribute('data-atributo') == "id_imperio") {
						var id_imperio = inputs[index].value;
					}
				}
				atualiza_produtos_acao(id_imperio);
				range_em_edicao = false;
			} else {
				alert(resposta.resposta_ajax);
			}
		}
	};
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(objeto_editado['dados_ajax']);

	range_em_edicao = true; //Trava o objeto em modo de edição até que o AJAX libere
	evento.preventDefault();
	return false;
}

/******************
function atualiza_produtos_acao(id_imperio)
--------------------
Pega os produtos da Ação
id_imperio -- id do Império
******************/	
function atualiza_produtos_acao(id_imperio) {
	dados_ajax= "post_type=POST&action=produtos_acao&id_imperio="+id_imperio;
	
	//Envia a chamada de AJAX para salvar o objeto
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var resposta = JSON.parse(this.responseText);
			if (resposta.resposta_ajax == "OK!") {
				id_colonias = "lista_colonias_imperio_"+id_imperio;
				id_produz = "recursos_produzidos_imperio_"+id_imperio;
				id_consome = "recursos_consumidos_imperio_"+id_imperio;
				div_colonias = document.getElementById(id_colonias);
				div_produz = document.getElementById(id_produz);
				div_consome = document.getElementById(id_consome);
				
				div_colonias.innerHTML = resposta.lista_colonias
				div_produz.innerHTML = resposta.recursos_produzidos;
				div_consome.innerHTML = resposta.recursos_consumidos;
			} else {
				alert(resposta.resposta_ajax);
			}
		}
	};
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);

}