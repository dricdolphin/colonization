var reabastece_em_edicao = false;

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
	var textarea_linha = linha.getElementsByTagName("TEXTAREA");
	var select_linha = linha.getElementsByTagName("SELECT");

	let inputs_linha_temp = [];
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
	for (let index = 0; index < inputs_linha.length; index++) {
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
	
	if (!valida_generico(objeto)) {
		return;
	}

	if (typeof(select_linha[0]) !== "undefined") {
		var id_jogador = select_linha[0].value;
		//Verifica se o jogador já tem um Império cadastrado. Cada jogador pode ter apenas um Império.
		for (let index = 0; index < inputs_tabela.length; index++) {
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
	var custo_pago = "";

	for (let index = 0; index < inputs_linha.length; index++) {
		if (inputs_linha[index].getAttribute('data-atributo') == "id_imperio" || inputs_linha[index].getAttribute('data-atributo') == "id") {
			dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"="+inputs_linha[index].value;
		}
		
		if (inputs_linha[index].getAttribute('data-atributo') == "custo_pago") {
			custo_pago = inputs_linha[index];
			dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"="+inputs_linha[index].value;
		}
		
		if (inputs_linha[index].type == 'checkbox') {
			if (inputs_linha[index].checked) {
				dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"=1";
			} else {
				dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"=0";
			}			
		}			
	}

	if (typeof(select_linha[0]) !== "undefined") {
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
				if (resposta.confirma != "") {
					let confirma = confirm(resposta.confirma);
					custo_pago.value = resposta.custo_pago;
					if (confirma) {
						if (resposta.custo_pago == 0) {
							custo_pago.value = 0;
						}
					} else {
						custo_pago.value = resposta.custo_pago;
					}
					
					retorno = confirma;
				} else {
					retorno = true;
					custo_pago.value = resposta.custo_pago;
				}
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
function salva_reabastece(objeto, id_imperio, id_estrela)
--------------------
Salva o Reabastecimento
******************/	
function salva_reabastece(evento, objeto, id_imperio, id_estrela, tabela='colonization_imperio_abastecimento') {
	if (reabastece_em_edicao) {
		//alert("Aguarde o processamento de outro item antes de prosseguir!");
		objeto.checked = !objeto.checked;
		return false;
	}
	//console.log(objeto.checked);
	reabastece_em_edicao = true;
	
	var dados_ajax = "post_type=POST&action=valida_reabastecimento&id_imperio="+id_imperio+"&id_estrela="+id_estrela+"&tabela="+tabela;
	var retorno = false;

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
			
			if (!retorno) {
				if (objeto.checked) {
					objeto.checked = false;
				} else {
					objeto.checked = true;
				}
			}
		reabastece_em_edicao = false;
		}
	};
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);

	evento.preventDefault();
	return false;
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

	if (!valida_generico(objeto)) {
		return;
	}
	
	//Verifica se o nome do Império está preenchido
	for (let index = 0; index < inputs_linha.length; index++) {
		if (inputs_linha[index].getAttribute('data-atributo') == "X" || inputs_linha[index].getAttribute('data-atributo') == "Y" || inputs_linha[index].getAttribute('data-atributo') == "Z" || inputs_linha[index].getAttribute('data-atributo') == "id") {
			dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"="+inputs_linha[index].value;
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

	for (let index = 0; index < select_linha.length; index++) {
		if (typeof(select_linha[0]) !== "undefined") {
			dados_ajax = dados_ajax + "&" + select_linha[index].getAttribute('data-atributo') + "="+ select_linha[index].value;
		}
	}

	if (!valida_generico(objeto)) {
		return;
	}
	
	//Verifica se o nome do Império está preenchido
	for (let index = 0; index < inputs_linha.length; index++) {
		if (inputs_linha[index].getAttribute('data-atributo') == "id" || inputs_linha[index].getAttribute('data-atributo') == "turno" || inputs_linha[index].getAttribute('data-atributo') == "nome_npc" || inputs_linha[index].getAttribute('data-atributo') == "pop") {
			dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"="+inputs_linha[index].value;
		}
		if (inputs_linha[index].type == 'checkbox') {
			if (inputs_linha[index].checked) {
				dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"=1";
			} else {
				dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"=0";
			}			
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
	
	if (!valida_generico(objeto)) {
		return false;
	}	
	
	if (typeof(select_linha[0]) !== "undefined") {
		var id_recurso = select_linha[0].value;
		dados_ajax = dados_ajax +"&id_recurso="+id_recurso;
	}

	for (let index = 0; index < inputs_linha.length; index++) {
		if (inputs_linha[index].getAttribute('data-atributo') == "id_instalacao" || inputs_linha[index].getAttribute('data-atributo') == "consome" || (inputs_linha[index].getAttribute('data-atributo') == "id_recurso" && typeof(id_recurso) === "undefined") || inputs_linha[index].getAttribute('data-atributo') == "id") {
			dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"="+inputs_linha[index].value;
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
	
	if (!valida_generico(objeto)) {
		return false;
	}

	if (typeof(select_linha[0]) !== "undefined") {
		var id_recurso = select_linha[0].value;
		dados_ajax = dados_ajax +"&id_recurso="+id_recurso;
	}

	for (let index = 0; index < inputs_linha.length; index++) {
		if (inputs_linha[index].getAttribute('data-atributo') == "id_planeta"  || (inputs_linha[index].getAttribute('data-atributo') == "id_recurso" && typeof(id_recurso) === "undefined") || inputs_linha[index].getAttribute('data-atributo') == "id" || inputs_linha[index].getAttribute('data-atributo') == "turno") {
			dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"="+inputs_linha[index].value;
		}
		
		
	}

	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var resposta = JSON.parse(this.responseText);
			if (resposta.resposta_ajax == "OK!") {
				altera_recursos_planeta(objeto);
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
	
	if (!valida_generico(objeto)) {
		return false;
	}

	if (typeof(select_linha[0]) !== "undefined") {
		var id_instalacao = select_linha[0].value;
		dados_ajax = dados_ajax +"&id_instalacao="+id_instalacao;
	}

	for (let index = 0; index < inputs_linha.length; index++) {
		if (inputs_linha[index].getAttribute('data-atributo') == "nivel" ||inputs_linha[index].getAttribute('data-atributo') == "id_planeta" 
		|| (inputs_linha[index].getAttribute('data-atributo') == "id_instalacao" && typeof(id_instalacao) === "undefined") 
		|| inputs_linha[index].getAttribute('data-atributo') == "id"
		|| inputs_linha[index].getAttribute('data-atributo') == "instalacao_inicial"
		) {
			dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"="+inputs_linha[index].value;
		}
	}

	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var resposta = JSON.parse(this.responseText);
			if (resposta.resposta_ajax == "OK!") {
				if (resposta.confirma != "") {
					let confirma = confirm(resposta.confirma);
					retorno = confirma;
				} else {
					retorno = true;
				}
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
function altera_recursos_planeta(objeto) 
--------------------
Atualiza os recursos do planeta nos Turnos anteriores, caso necessário
******************/	
function altera_recursos_planeta(objeto) {
	var dados = pega_dados_objeto(objeto);//Pega os dados do objeto

	var linha = pega_ascendente(objeto,"TR");
	var divs = linha.getElementsByTagName("DIV");
	
	for (let index=0; index<divs.length; index++) {
		if (divs[index].getAttribute("data-atributo") == "nome_recurso") {
			if (divs[index].getAttribute("data-id-selecionado") != divs[index].childNodes[1].value) {
				console.log("MUDOU! De "+divs[index].getAttribute("data-id-selecionado")+" para "+divs[index].childNodes[1].value);
				var dados_ajax = "post_type=POST&action=altera_recursos_planeta&id_planeta="+dados['id_planeta'].value+"&id_recurso_original="+divs[index].getAttribute("data-id-selecionado")+"&id_recurso="+divs[index].childNodes[1].value;				
			}
		}
	}
	
	var retorno = true;
	
	//Envia a chamada de AJAX para salvar o objeto
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.status == 400) {
			
		}
		if (this.readyState == 4 && this.status == 200) {
			var resposta = JSON.parse(this.responseText);
			if (resposta.resposta_ajax != "OK!") {
				retorno = false;
			}
			if (resposta.mensagem !== undefined) {
				alert(resposta.mensagem);
			}
		}
	};
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
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


/******************
function valida_transfere_tech(objeto) 
--------------------
Valida uma transferência de tecnologia
******************/	
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