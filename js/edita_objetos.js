var objeto_em_edicao = false; //Define se está no modo de edição ou não
var range_em_edicao = false; //Define se o "range" está em edição
var objeto_em_salvamento = false; //Impede de salvar o mesmo objeto duas vezes

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
			
			if (typeof(dados[atributo]) !== "undefined") {
				//Só atualiza o innerHTML de divs que não contenham objetos
				if (dados[atributo] !== null) {
					if (divs[index].hasChildNodes()) {
						if (divs[index].childNodes[0].tagName != "INPUT" && divs[index].childNodes[0].tagName != "TEXTAREA" && divs[index].childNodes[0].tagName != "LABEL") {
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
			if (typeof(dados[atributo]) !== "undefined" ) {
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
	range_em_edicao = false;
	
	var tabela_objetos = pega_ascendente(objeto,"TABLE");
	var linha = pega_ascendente(objeto,"TR");
	//tabela_objetos.deleteRow(-1);
	linha.parentNode.removeChild(linha);
	
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
						if (data_id != "") {
							inputs[0].id = divs[index_div].getAttribute('data-id');
						}
					} else if (divs[index_div].getAttribute('data-type') == "select") {
						let argumentos = "";
						if (divs[index_div].getAttribute('data-argumentos') != null) {
							argumentos = divs[index_div].getAttribute('data-argumentos');
						}
						var lista = chama_funcao_validacao(divs[index_div].getAttribute('data-id-selecionado'),divs[index_div].getAttribute('data-funcao'),argumentos);
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
	
	if (typeof(fn) === "function") {
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
remove_gerenciar -- Define se deve remover a opção de voltar a editar o objeto
******************/	
function salva_objeto(evento, objeto, cancela=false, remove_gerenciar=false, nome_tabela='') {
	if (objeto_em_salvamento) {
		evento.preventDefault();
		return false;
	}
	
	objeto_em_salvamento = true;
	
	var objeto_editado = pega_dados_objeto(objeto);//Pega os dados do objeto
	if (typeof(objeto_editado['funcao_pos_processamento_objeto']) != "") {
		var pos_processamento = objeto_editado['funcao_pos_processamento_objeto'];
	}
	
	if (cancela) {
		var objeto_desabilitado = desabilita_edicao_objeto(objeto, cancela);
		
		var processa = true;
		if (typeof(pos_processamento) !== "undefined") {
			processa = chama_funcao_validacao(objeto_desabilitado, pos_processamento, true);
		}
		
		objeto_em_edicao = false;
		objeto_em_salvamento = false;
		
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
		
		objeto_em_salvamento = false;
		
		evento.preventDefault();
		return false;
	}

	objeto_editado = pega_dados_objeto(objeto);//Pega os dados do objeto_editado pois eles podem ter sido modificados pelas funções de validação e pós-processamento
	//Cria o string que será passado para o AJAX
	if (nome_tabela == '') {
		nome_tabela = objeto_editado['nome_tabela'];
	}
	
	objeto_editado['dados_ajax'] = "post_type=POST&action=salva_objeto&tabela="+nome_tabela+objeto_editado['dados_ajax']+"&where_clause="+objeto_editado['where_clause']+"&where_value="+objeto_editado['where_value'];

	//Envia a chamada de AJAX para salvar o objeto
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			try {
				var resposta = JSON.parse(this.responseText);
			} 
			catch (err) {
				console.log(this.responseText);
				retorno = false;
				return false;
			}
			
			objeto_em_salvamento = false;
			objeto_em_edicao = false; //Libera a edição de outros objetos
			range_em_edicao = false;
			
			if (resposta.resposta_ajax == "SALVO!") {
				//Após salvar os dados, remove os "inputs" e transforma a linha em texto, deixando o Império passível de ser editado
				var objeto_desabilitado = desabilita_edicao_objeto(objeto);
				var objeto_atualizado = atualiza_objeto(objeto_desabilitado,resposta[0]); //O objeto salvo está no array resposta[0]
				if (typeof(pos_processamento) !== "undefined") {
					var processa = chama_funcao_validacao(objeto_desabilitado, pos_processamento);
				}
			} else {
				alert(resposta.resposta_ajax);
			}

			if (resposta.debug !== undefined) {
				console.log(resposta.debug);
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
	
	var linha_objeto = pega_ascendente(objeto,"TR");
	var objeto_editado = pega_dados_objeto(objeto);//Pega os dados do objeto
	
	if (typeof(objeto_editado['mensagem_exclui_objeto']) === "undefined") {
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
				try {
					var resposta = JSON.parse(this.responseText);
				} 
				catch (err) {
					console.log(this.responseText);
					retorno = false;
					return false;
				}

				if (resposta.resposta_ajax == "DELETADO!") {
					linha_objeto.remove(); //Remove a linha
					objeto_em_edicao = false;
					range_em_edicao = false;
				} else {
					alert(resposta.resposta_ajax);
					objeto_em_edicao = false;
					range_em_edicao = false;
				}
				
				if (resposta.debug != undefined) {
					console.log(resposta.debug);
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
remove_gerenciar = false -- define se deve remover o div "gerenciar"
******************/	
function desabilita_edicao_objeto(objeto, cancela = false, remove_gerenciar=false) {

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
	
	let html_deletar = " | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a>"; //HTML com o link para deletar o objeto
	//Pega cada um dos inputs e tira do modo de edição
	var tamanho_maximo = inputs.length-1;
	for (var index = tamanho_maximo; index >-1; index--) {
		if (inputs[index].type == 'text' || inputs[index].tagName =='TEXTAREA') {
			div = pega_ascendente(inputs[index],"DIV");
			if (cancela) {
				div.innerHTML = div.getAttribute('data-valor-original');
			} else {
				if (div.getAttribute('data-desabilita') != 'false') {
				div.innerHTML = inputs[index].value;
				}
			}
		} else if (inputs[index].type == 'checkbox') {
			if (inputs[index].id != "") {
				inputs[index].id = "";
			}
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
		} else if (inputs[index].type == 'hidden') {
			if (inputs[index].getAttribute('data-atributo') == "deletavel") {
				if (inputs[index].value == 0) {
					html_deletar = "";
				}
			}
		}
	}

	//Além de INPUT, existe a possibilidade dos dados serem passados via SELECT
	var tamanho_maximo = selects.length-1;
	for (let index = tamanho_maximo; index > -1; index--) {
		div = pega_ascendente(selects[index],"DIV");
		if (cancela) {
			div.innerHTML = div.getAttribute('data-valor-original');
		} else {
			div.setAttribute('data-id-selecionado',selects[index].value);
			div.setAttribute('data-valor-original',selects[index].options[selects[index].selectedIndex].innerHTML);
			//if (div.getAttribute('data-editavel') == 'true') {
				div.innerHTML = selects[index].options[selects[index].selectedIndex].innerHTML;
			//}
		}
	}
	
	//A primeira célula NORMALMENTE é especial, pois tem divs com dados como Id e outros, e links de gerenciamento, que no modo edição são alterados para Salvar e Cancelar
	var celula = linha.cells[0]
	var divs = celula.getElementsByTagName("DIV");
	for (let index=0; index < divs.length; index++) {
		if (divs[index].getAttribute('data-atributo') == "gerenciar") {
			divs[index].innerHTML = "<a href='#' onclick='return edita_objeto(event, this);'>Editar</a>"+html_deletar;
			if (remove_gerenciar) {
				divs[index].style.visibility = "hidden";
			}
		} else if (divs[index].getAttribute('data-atributo') == "processa_string") {
			divs[index].style.visibility = "hidden";
		}
	}
	return linha;
}


/******************
function altera_pop_transfere(objeto) 
--------------------
Altera o valor do label
******************/	
function altera_pop_transfere(evento, objeto) {
	let div_parente = objeto.parentNode;
	let label = div_parente.getElementsByTagName("label");
	
	label[0].innerHTML = objeto.value;
}


/******************
function libera_npc(evento, objeto)
--------------------
Se estiver com o modo Select na opção "NPC", libera para colocar o nome do NPC
******************/	
function libera_npc(evento, objeto) {
	let nome_npc_div = document.getElementById('nome_npc');
	
	if (objeto.value == 0) {
		nome_npc_div.style.display = 'block';
	} else {
		nome_npc_div.style.display = 'none';
		nome_npc_div.childNodes[0].value='';
	}

	evento.preventDefault();
	return false;
}

/******************
function transfere_pop
--------------------
Transfere a Pop de uma colônia para outra
--------
******************/
function transfere_pop(evento,objeto,id_imperio,id_colonia_origem,id_planeta,id_estrela) {
	if (!range_em_edicao || range_em_edicao == objeto) {
		range_em_edicao = objeto;
		
		let div_parent = objeto.parentNode;
		
		let range_pop = div_parent.getElementsByTagName("input")[0];
		let select_planeta_destino = div_parent.getElementsByTagName("select")[0];
		
		//console.log(range_pop.value);
		//console.log(select_planeta_destino.options[select_planeta_destino.selectedIndex].value);
		
		dados_ajax = "post_type=POST&action=transfere_pop&id_imperio="+id_imperio+"&id_planeta="+id_planeta+"&id_estrela="+id_estrela+"&id_colonia_origem="+id_colonia_origem
		+"&id_colonia_destino="+select_planeta_destino.options[select_planeta_destino.selectedIndex].value+"&pop="+range_pop.value;

		//Envia a chamada de AJAX para salvar o objeto
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				try {
					var resposta = JSON.parse(this.responseText);
				} 
				catch (err) {
					console.log(this.responseText);
					retorno = false;
					return false;
				}
				
				if (resposta.resposta_ajax == "SALVO!") {
					atualiza_produtos_acao(id_imperio, id_planeta, id_estrela, 0, resposta);
					range_em_edicao = false;
				} else {
					alert(resposta.resposta_ajax);
					range_em_edicao = false;
				}
				if (resposta.debug !== undefined) {
					console.log(resposta.debug);
				}
			}
		};
		xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send(dados_ajax);
	} else {
		alert("Já existe uma Ação em edição!");
	}

	evento.preventDefault();
	return false;
}