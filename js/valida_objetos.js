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
function valida_tech_imperio(objeto)
--------------------
Valida os dados da Tech sendo adicionada à um Império
objeto -- objeto sendo editado
******************/	
function valida_tech_imperio(objeto) {
	var linha = pega_ascendente(objeto,"TR");
	var celulas = linha.cells;
	var inputs_linha = linha.getElementsByTagName("INPUT");
	var select_linha = linha.getElementsByTagName("SELECT");
	var dados_ajax = "post_type=POST&action=valida_tech_imperio";
	var retorno = false;

	for (index = 0; index < inputs_linha.length; index++) {
		if (inputs_linha[index].getAttribute('data-atributo') == "id_imperio" || inputs_linha[index].getAttribute('data-atributo') == "id") {
			dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"="+inputs_linha[index].value;
		}
	}

	if (typeof select_linha[0] !== "undefined") {
		var id_tech = select_linha[0].value;
		dados_ajax = dados_ajax +"&id_tech="+id_tech;
	}

	if (!valida_generico(objeto)) {
		return;
	}

	//Chama um AJAX para verificar se já existe uma estrela nas coordenadas informadas
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var resposta = JSON.parse(this.responseText);
			if (resposta.resposta_ajax == "OK!") {
				retorno = true;
			} else {
				alert(resposta.resposta_ajax);
				retorno = false;
			}
		}
	};
	xhttp.open("POST", ajaxurl, false); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);

	return retorno;	
	
	
}

/******************
function valida_estrela(objeto)
--------------------
Valida os dados da Estrela
objeto -- objeto sendo editado
******************/	
function valida_estrela(objeto) {
	
	var linha = pega_ascendente(objeto,"TR");
	var celulas = linha.cells;
	var inputs_linha = linha.getElementsByTagName("INPUT");
	var select_linha = linha.getElementsByTagName("SELECT");
	var dados_ajax = "post_type=POST&action=valida_estrela";
	var retorno = false;
	
	//Verifica se o nome do Império está preenchido
	for (index = 0; index < inputs_linha.length; index++) {
		if (inputs_linha[index].getAttribute('data-atributo') == "X" || inputs_linha[index].getAttribute('data-atributo') == "Y" || inputs_linha[index].getAttribute('data-atributo') == "Z" || inputs_linha[index].getAttribute('data-atributo') == "id") {
			dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"="+inputs_linha[index].value;
		}
		if (inputs_linha[index].type == "text" && inputs_linha[index].value == "") {
			alert('Nenhum dado pode ser deixado em branco!');
			return false;
		}
	}

	//Chama um AJAX para verificar se já existe uma estrela nas coordenadas informadas
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var resposta = JSON.parse(this.responseText);
			if (resposta.resposta_ajax == "OK!") {
				retorno = true;
			} else {
				alert(resposta.resposta_ajax);
				retorno = false;
			}
		}
	};
	xhttp.open("POST", ajaxurl, false); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);

	return retorno;
}

/******************
function valida_colonia(objeto)
--------------------
Valida os dados da Colônia
objeto -- objeto sendo editado
******************/	
function valida_colonia(objeto) {
	
	var linha = pega_ascendente(objeto,"TR");
	var celulas = linha.cells;
	var inputs_linha = linha.getElementsByTagName("INPUT");
	var select_linha = linha.getElementsByTagName("SELECT");
	var dados_ajax = "post_type=POST&action=valida_colonia";
	var retorno = false;

	if (typeof select_linha[0] !== "undefined") {
		var id_planeta = select_linha[0].value;
		dados_ajax = dados_ajax +"&id_planeta="+id_planeta;
	}
	
	//Verifica se o nome do Império está preenchido
	for (index = 0; index < inputs_linha.length; index++) {
		if (inputs_linha[index].getAttribute('data-atributo') == "id" || inputs_linha[index].getAttribute('data-atributo') == "turno" ) {
			dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"="+inputs_linha[index].value;
		}
		if (inputs_linha[index].type == "text" && inputs_linha[index].value == "") {
			alert('Nenhum dado pode ser deixado em branco!');
			return false;
		}
	}

	//Chama um AJAX para verificar se já existe uma estrela nas coordenadas informadas
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var resposta = JSON.parse(this.responseText);
			if (resposta.resposta_ajax == "OK!") {
				retorno = true;
			} else {
				alert(resposta.resposta_ajax);
				retorno = false;
			}
		}
	};
	xhttp.open("POST", ajaxurl, false); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);

	return retorno;
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
	var textarea_linha = linha.getElementsByTagName("INPUT");
	var select_linha = linha.getElementsByTagName("SELECT");

	let inputs_linha_temp = "";
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
	
	//Verifica se o todos os dados estão preenchidos
	for (index = 0; index < inputs_linha.length; index++) {
		if (inputs_linha[index].type == "text" && inputs_linha[index].value == "") {
			if (inputs_linha[index].parentNode.getAttribute("data-branco") != "true") {//Caso o div tenha o atributo "data-branco", então pode deixar em branco
				alert('Nenhum dado pode ser deixado em branco!');
				return false;
			}
		}
	}

	return true; //Validou!
}

/******************
function valida_instalacao_recurso(objeto)
--------------------
Valida os dados da Instalação
objeto -- objeto sendo editado
******************/	
function valida_instalacao_recurso(objeto) {
	
	var linha = pega_ascendente(objeto,"TR");
	var celulas = linha.cells;
	var inputs_linha = linha.getElementsByTagName("INPUT");
	var select_linha = linha.getElementsByTagName("SELECT");
	var dados_ajax = "post_type=POST&action=valida_instalacao_recurso";
	var retorno = false;
	
	if (typeof select_linha[0] !== "undefined") {
		var id_recurso = select_linha[0].value;
		dados_ajax = dados_ajax +"&id_recurso="+id_recurso;
	}

	for (index = 0; index < inputs_linha.length; index++) {
		if (inputs_linha[index].getAttribute('data-atributo') == "id_instalacao" || inputs_linha[index].getAttribute('data-atributo') == "consome" || (inputs_linha[index].getAttribute('data-atributo') == "id_recurso" && typeof id_recurso === "undefined") || inputs_linha[index].getAttribute('data-atributo') == "id") {
			dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"="+inputs_linha[index].value;
		}
		if (inputs_linha[index].type == "text" && inputs_linha[index].value == "") {
			alert('Nenhum dado pode ser deixado em branco!');
			return false;
		}
	}

	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var resposta = JSON.parse(this.responseText);
			if (resposta.resposta_ajax == "OK!") {
				retorno = true;
			} else {
				alert(resposta.resposta_ajax);
				retorno = false;
			}
		}
	};
	xhttp.open("POST", ajaxurl, false); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);

	return retorno;
}

/******************
function valida_planeta_recurso(objeto)
--------------------
Valida os recursos da Colônia
objeto -- objeto sendo editado
******************/	
function valida_planeta_recurso(objeto) {
	
	var linha = pega_ascendente(objeto,"TR");
	var celulas = linha.cells;
	var inputs_linha = linha.getElementsByTagName("INPUT");
	var select_linha = linha.getElementsByTagName("SELECT");
	var dados_ajax = "post_type=POST&action=valida_planeta_recurso";
	var retorno = false;
	
	if (typeof select_linha[0] !== "undefined") {
		var id_recurso = select_linha[0].value;
		dados_ajax = dados_ajax +"&id_recurso="+id_recurso;
	}

	for (index = 0; index < inputs_linha.length; index++) {
		if (inputs_linha[index].getAttribute('data-atributo') == "id_planeta"  || (inputs_linha[index].getAttribute('data-atributo') == "id_recurso" && typeof id_recurso === "undefined") || inputs_linha[index].getAttribute('data-atributo') == "id" || inputs_linha[index].getAttribute('data-atributo') == "turno") {
			dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"="+inputs_linha[index].value;
		}
		if (inputs_linha[index].type == "text" && inputs_linha[index].value == "") {
			alert('Nenhum dado pode ser deixado em branco!');
			return false;
		}
	}

	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var resposta = JSON.parse(this.responseText);
			if (resposta.resposta_ajax == "OK!") {
				retorno = true;
			} else {
				alert(resposta.resposta_ajax);
				retorno = false;
			}
		}
	};
	xhttp.open("POST", ajaxurl, false); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);

	return retorno;
}

/******************
function valida_colonia_instalacao(objeto)
--------------------
Valida as Instalações da Colônia
objeto -- objeto sendo editado
******************/	
function valida_colonia_instalacao(objeto) {
	
	var linha = pega_ascendente(objeto,"TR");
	var celulas = linha.cells;
	var inputs_linha = linha.getElementsByTagName("INPUT");
	var select_linha = linha.getElementsByTagName("SELECT");
	var dados_ajax = "post_type=POST&action=valida_colonia_instalacao";
	var retorno = false;
	
	if (typeof select_linha[0] !== "undefined") {
		var id_instalacao = select_linha[0].value;
		dados_ajax = dados_ajax +"&id_instalacao="+id_instalacao;
	}

	for (index = 0; index < inputs_linha.length; index++) {
		if (inputs_linha[index].getAttribute('data-atributo') == "nivel" ||inputs_linha[index].getAttribute('data-atributo') == "id_planeta" || (inputs_linha[index].getAttribute('data-atributo') == "id_instalacao" && typeof id_instalacao === "undefined") || inputs_linha[index].getAttribute('data-atributo') == "id") {
			dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"="+inputs_linha[index].value;
		}
		if (inputs_linha[index].type == "text" && inputs_linha[index].value == "") {
			alert('Nenhum dado pode ser deixado em branco!');
			return false;
		}
	}

	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var resposta = JSON.parse(this.responseText);
			if (resposta.resposta_ajax == "OK!") {
				retorno = true;
			} else {
				alert(resposta.resposta_ajax);
				retorno = false;
			}
			if (resposta.debug !== undefined) {
				console.log(resposta.debug);
			}
		}
	};
	xhttp.open("POST", ajaxurl, false); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);

	return retorno;
}

/******************
function destruir_instalacao(evento, objeto)
--------------------
Função para chamar o AJAX de destruir instalação
objeto -- objeto sendo editado
******************/
function destruir_instalacao(evento, objeto) {
	var linha=pega_ascendente(objeto,"TR");;
	var inputs=linha.getElementsByTagName("INPUT");
	var dados_ajax = "post_type=POST&action=destruir_instalacao";

	
	for (var index = 0; index < inputs.length; index++) {
		if (inputs[index].getAttribute('data-atributo') == "id") {
			var id_objeto = inputs[index].value;
		}
	}
	
	dados_ajax = dados_ajax + "&id=" + id_objeto;
	
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4) {
			objeto_em_edicao = false;
		}
		if (this.readyState == 4 && this.status == 200) {
			var resposta = JSON.parse(this.responseText);
			if (resposta.resposta_ajax == "OK!") {
				if (resposta[0].turno_destroi != "") {
					objeto.text = "Reparar Instalação";
				} else {
					objeto.text = "Destruir Instalação";
					resposta[0].turno_destroi = "&nbsp;";
				}
				var objeto_desabilitado = desabilita_edicao_objeto(objeto);
				var objeto_atualizado = atualiza_objeto(objeto_desabilitado,resposta[0]); //O objeto salvo está no array resposta[0]
			} else {
				alert(resposta.resposta_ajax);
			}
		}
	};
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);
	objeto_em_edicao = true;
	
	evento.preventDefault();
	return false;
}

/******************
function remove_excluir(objeto) 
--------------------
Remove a opção de excluir um objeto
objeto -- objeto sendo editado
******************/	
function remove_excluir(objeto, cancela=false) {
	var linha = pega_ascendente(objeto,"TR");
	
	//A primeira célula é especial, pois tem dois divs -- um com dados e outro com os links para Salvar e Excluir, que no modo edição são alterados para Salvar e Cancelar
	var celula = linha.cells[0]
	var divs = celula.getElementsByTagName("DIV");
	divs[1].innerHTML = "<a href='#' onclick='return edita_objeto(event, this);'>Editar</a>";
}

/******************
function mais_dados_imperio(objeto) 
--------------------
Pega dados adicionais do Império
objeto -- objeto sendo editado
******************/	
function mais_dados_imperio(objeto, cancela=false) {
	var linha = pega_ascendente(objeto,"TR");
	var inputs = linha.getElementsByTagName("INPUT");
	var dados_ajax = "post_type=POST&action=dados_imperio";

	
	for (var index = 0; index < inputs.length; index++) {
		if (inputs[index].getAttribute('data-atributo') == "id") {
			var id_objeto = inputs[index].value;
		}
	}
	
	dados_ajax = dados_ajax + "&id=" + id_objeto;
	
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4) {
			objeto_em_edicao = false;
		}
		if (this.readyState == 4 && this.status == 200) {
			var resposta = JSON.parse(this.responseText);
			if (resposta.resposta_ajax == "OK!") {
				var objeto_desabilitado = desabilita_edicao_objeto(objeto);
				var objeto_atualizado = atualiza_objeto(objeto_desabilitado,resposta[0]); //O objeto salvo está no array resposta[0]
			} else {
				alert(resposta.resposta_ajax);
			}
		}
	};
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);
}

/******************
function valida_acao(dados)
--------------------
Pega os produtos da Ação
dados -- dados do objeto
******************/	
function valida_acao(dados) {
	var dados_ajax = "post_type=POST&action=valida_acao&turno="+dados['turno']+"&id_imperio="+dados['id_imperio']+"&id_instalacao="+dados['id_instalacao']+"&id_planeta_instalacoes="+dados['id_planeta_instalacoes']+"&id_planeta="+dados['id_planeta']+"&pop="+dados['pop']+"&pop_original="+dados['pop_original'];
	var retorno = true;
	
	//Envia a chamada de AJAX para salvar o objeto
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.status == 400) {
			
		}
		if (this.readyState == 4 && this.status == 200) {
			try {
				var resposta = JSON.parse(this.responseText);
			} 
			catch (err) {
				console.log(this.responseText);
				retorno = false;
				return false;
			}
			
			if (resposta.resposta_ajax == "OK!") {
				if (resposta.balanco_acao != "") {
					retorno = false;
					alert("Não é possível realizar esta ação! Estão faltando os seguintes recursos: "+resposta.balanco_acao);
				}
				
				if (resposta.debug !== undefined) {
					console.log(resposta.debug);
				}
			} else {
				alert(resposta.resposta_ajax);
				retorno = false;
			}
		}
	};
	xhttp.open("POST", ajaxurl, false); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);

	return retorno;
}

/******************
function valida_acao_admin(dados)
--------------------
Valida as ações do Admin e as executa
dados -- dados do objeto
******************/	
function valida_acao_admin(objeto) {
	//Reconstrói os inputs 'lista_recursos' e 'qtd' baseado nas alterações dos inputs visíveis
	altera_lista_recursos_qtd(objeto, false, true);
	
	if (!valida_generico(objeto)) {
		return false;
	}

	var objeto_editado = pega_dados_objeto(objeto);//Pega os dados do objeto
	
	var dados_ajax = "post_type=POST&action=valida_acao_admin&turno="+objeto_editado['turno'].value+"&id_imperio="+objeto_editado['id_imperio'].value
	+"&lista_recursos="+objeto_editado['lista_recursos'].value+"&qtd="+objeto_editado['qtd'].value+"&descricao="+objeto_editado['descricao'].value+"&id="+objeto_editado['id'].value
	+"&lista_recursos_original="+objeto_editado['lista_recursos'].parentNode.getAttribute('data-valor-original')+"&qtd_original="+objeto_editado['qtd'].parentNode.getAttribute('data-valor-original');
	
	var retorno = true;

	//Envia a chamada de AJAX para salvar o objeto
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.status == 400) {
			
		}
		if (this.readyState == 4 && this.status == 200) {
			var resposta = JSON.parse(this.responseText);
			if (resposta.resposta_ajax == "OK!") {
				var div_resposta = document.getElementById("div_resposta");
				div_resposta.innerHTML = resposta.html;
			} else {
				alert(resposta.resposta_ajax);
				var div_resposta = document.getElementById("div_resposta");
				div_resposta.innerHTML = resposta.html;
				retorno = false;
			}
		}
	};
	xhttp.open("POST", ajaxurl, false); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);
	
	return retorno;
}

/******************
function altera_lista_recursos_qtd(objeto) 
--------------------
Altera os valores da lista de recursos e suas qtds
objeto -- objeto sendo editado
******************/	
function altera_lista_recursos_qtd(objeto, cancela=false, valida=false) {
	var linha = pega_ascendente(objeto,"TR");
	var inputs = linha.getElementsByTagName("INPUT");
	var divs = linha.getElementsByTagName("DIV");
	var div_lista_recursos_qtd = "";
	var input_qtd = "";
	var input_lista_recursos = "";
	var qtds = [];
	var recursos = [];
	
	for (let index=0; index<divs.length; index++) { //Encontra o lista_recursos_qtd
		if (divs[index].getAttribute('data-atributo') == 'lista_recursos_qtd') {
			div_lista_recursos_qtd = divs[index];
		}
	}
	
	if (cancela) {
		if (div_lista_recursos_qtd.getAttribute('data-valor-original') != "") {
			div_lista_recursos_qtd.innerHTML = div_lista_recursos_qtd.getAttribute('data-valor-original');
			
			return;
		}
	} else if (valida) {
		//Atualiza o INPUT qtds
		var index_qtds = 0;
		for (let index=0; index<inputs.length; index++) {
			if (inputs[index].getAttribute('data-atributo') == 'qtd') {
				input_qtd = inputs[index];
			} else if (inputs[index].getAttribute('data-atributo') == 'qtd') {
				input_lista_recursos = inputs[index];
			} else if(inputs[index].type == 'text' && inputs[index].getAttribute('data-atributo') != 'lista_recursos' && inputs[index].getAttribute('data-atributo') != 'qtd' && inputs[index].getAttribute('data-atributo') != 'descricao' & inputs[index].getAttribute('data-atributo') != 'turno') {
				qtds[index_qtds] = inputs[index].value;
				recursos[index_qtds] = inputs[index].getAttribute('data-atributo');
				index_qtds++;
			}
		}
		input_qtd.value = qtds.join(";");
		input_lista_recursos.value = recursos.join(";");
	}
}



function valida_transfere_tech(objeto){
	var dados = pega_dados_objeto(objeto);//Pega os dados do objeto
	var dados_ajax = "post_type=POST&action=valida_transfere_tech&turno="+dados['turno'].value+"&id_imperio_origem="+dados['id_imperio_origem'].value+"&id_imperio_destino="+dados['id_imperio_destino'].value+"&id_tech="+dados['id_tech'].value;
	
	var retorno = true;
	
	//Envia a chamada de AJAX para salvar o objeto
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.status == 400) {
			
		}
		if (this.readyState == 4 && this.status == 200) {
			var resposta = JSON.parse(this.responseText);
			if (resposta.resposta_ajax != "OK!") {
				alert(resposta.resposta_ajax);
				retorno = false;
			}
			if (resposta.mensagem !== undefined) {
				alert(resposta.mensagem);	
			}
		}
	};
	xhttp.open("POST", ajaxurl, false); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);

	return retorno;
}