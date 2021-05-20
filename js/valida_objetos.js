var reabastece_em_edicao = false;

/******************
function processa_xhttp_basico(dados_ajax)
--------------------
Processa um AJAX genérico
******************/	
function processa_xhttp_basico(dados_ajax)	{
	
	return new Promise((resolve, reject) => {
		//Envia a chamada de AJAX para salvar o objeto
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				let resposta = "";
				try {
					resposta = JSON.parse(this.responseText);
				} catch (err) {
					console.log(this.responseText);
					console.log(err);
					reject('JSON_PARSE');
					return false;
				}

				if (resposta.debug !== undefined) {
					console.log(resposta.debug);
				}

				if (resposta.mensagem !== undefined) {
					alert(resposta.mensagem);	
				}

				if (resposta.resposta_ajax != "OK!") {
					alert(resposta.resposta_ajax);
					resolve(false);
				} else {
					resolve(true);
				}
			} else if (this.status == 500) {
				console.log(this.responseText);
				console.log(this.statusText);
			}
		};
		xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send(dados_ajax);
	});
}

/******************
function processa_xhttp_resposta(dados_ajax)
--------------------
Processa um AJAX genérico e devolve os dados no RESOLVE
******************/	
function processa_xhttp_resposta(dados_ajax)	{
	
	return new Promise((resolve, reject) => {
		//Envia a chamada de AJAX para salvar o objeto
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				let resposta = "";
				try {
					resposta = JSON.parse(this.responseText);
				} catch (err) {
					console.log(this.responseText);
					console.log(err);
					reject('JSON_PARSE');
					return false;
				}

				if (resposta.debug !== undefined) {
					console.log(resposta.debug);
				}

				if (resposta.mensagem !== undefined) {
					alert(resposta.mensagem);	
				}
				resolve(resposta);
			} else if (this.status == 500) {
				console.log(this.responseText);
				console.log(this.statusText);
			}
		};
		xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send(dados_ajax);
	});
}

/******************
function valida_generico(objeto)
--------------------
Valida os dados de um objeto genérico
objeto -- objeto sendo editado
******************/	
function valida_generico(objeto) {
	
	let linha = pega_ascendente(objeto,"TR");
	let celulas = linha.cells;
	let inputs_linha = linha.getElementsByTagName("INPUT");
	let textarea_linha = linha.getElementsByTagName("TEXTAREA");
	let select_linha = linha.getElementsByTagName("SELECT");

	let inputs_linha_temp = [];
	let index = 0;
	for (index = 0; index < inputs_linha.length; index++) {
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
	
	let linha = pega_ascendente(objeto,"TR");
	let tabela = pega_ascendente(objeto,"TABLE");
	let celulas = linha.cells;
	let inputs_tabela = tabela.getElementsByTagName("INPUT");
	let inputs_linha = linha.getElementsByTagName("INPUT");
	let select_linha = linha.getElementsByTagName("SELECT");
	
	if (!valida_generico(objeto)) {
		return false;
	}

	let id_jogador = "";
	if (typeof(select_linha[0]) !== "undefined") {
		id_jogador = select_linha[0].value;
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
function valida_tech_imperio(objeto, somente_valida=false, tech_permitida=false) {
	let linha = pega_ascendente(objeto,"TR");
	let celulas = linha.cells;
	let inputs_linha = linha.getElementsByTagName("INPUT");
	let select_linha = linha.getElementsByTagName("SELECT");
	let dados_ajax = "post_type=POST&action=valida_tech_imperio&somente_valida=" + somente_valida ;
	if (tech_permitida) {
		dados_ajax = dados_ajax + "&tech_permitida=true";
	}
	let retorno = false;
	let custo_pago = "";

	for (let index = 0; index < inputs_linha.length; index++) {
		if (inputs_linha[index].getAttribute('data-atributo') == "id_imperio" 
		|| inputs_linha[index].getAttribute('data-atributo') == "id"
		) {
			if (inputs_linha[index].type == "checkbox" && !inputs_linha[index].checked) {
				dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"=0";
			} else {
				dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"="+inputs_linha[index].value;
			}
		}
		
		if (inputs_linha[index].getAttribute('data-atributo') == "custo_pago") {
			custo_pago = inputs_linha[index];
			dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"="+inputs_linha[index].value;
		}
	}

	if (typeof(select_linha[0]) !== "undefined") {
		let id_tech = select_linha[0].value;
		dados_ajax = dados_ajax +"&id_tech="+id_tech;
	}

	if (!valida_generico(objeto)) {
		return false;
	}

	retorno = new Promise((resolve, reject) =>	{
		//Chama um AJAX para verificar se já existe uma estrela nas coordenadas informadas
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				let resposta = "";
				try {
					resposta = JSON.parse(this.responseText);
				} catch (err) {
					console.log(this.responseText);
					console.log(err);
					reject('JSON_PARSE');
					return false;
				}
				
				if (resposta.debug !== undefined) {
					console.log(resposta.debug);
				}
				
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
						resolve(confirma);
					} else {
						custo_pago.value = resposta.custo_pago;
						resolve(true);
					}
				} else {
					alert(resposta.resposta_ajax);
					resolve(false);
				}
			}
		};
		xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send(dados_ajax);
	});

	return retorno;	
}

/******************
function valida_tech_permitida_imperio(objeto)
--------------------
Valida os dados da Tech Permitida sendo adicionada à um Império
objeto -- objeto sendo editado
******************/	
function valida_tech_permitida_imperio(objeto) {
	let somente_valida = true;
	let tech_permitida = true;
	return valida_tech_imperio(objeto, somente_valida, tech_permitida);
}

/******************
function valida_estrela(objeto)
--------------------
Valida os dados da Estrela
objeto -- objeto sendo editado
******************/	
function valida_estrela(objeto) {
	
	let linha = pega_ascendente(objeto,"TR");
	let celulas = linha.cells;
	let inputs_linha = linha.getElementsByTagName("INPUT");
	let select_linha = linha.getElementsByTagName("SELECT");
	let dados_ajax = "post_type=POST&action=valida_estrela";

	if (!valida_generico(objeto)) {
		return false;
	}
	
	//Verifica se o nome do Império está preenchido
	for (let index = 0; index < inputs_linha.length; index++) {
		if (inputs_linha[index].getAttribute('data-atributo') == "X" 
		|| inputs_linha[index].getAttribute('data-atributo') == "Y" 
		|| inputs_linha[index].getAttribute('data-atributo') == "Z" 
		|| inputs_linha[index].getAttribute('data-atributo') == "id") {
			dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"="+inputs_linha[index].value;
		}
	}

	return processa_xhttp_basico(dados_ajax);
}

/******************
function valida_colonia(objeto)
--------------------
Valida os dados da Colônia
objeto -- objeto sendo editado
******************/	
function valida_colonia(objeto) {
	
	let linha = pega_ascendente(objeto,"TR");
	let celulas = linha.cells;
	let inputs_linha = linha.getElementsByTagName("INPUT");
	let select_linha = linha.getElementsByTagName("SELECT");
	let dados_ajax = "post_type=POST&action=valida_colonia";

	for (let index = 0; index < select_linha.length; index++) {
		if (typeof(select_linha[0]) !== "undefined") {
			dados_ajax = dados_ajax + "&" + select_linha[index].getAttribute('data-atributo') + "="+ select_linha[index].value;
		}
	}

	if (!valida_generico(objeto)) {
		return false;
	}
	
	//Verifica se o nome do Império está preenchido
	for (let index = 0; index < inputs_linha.length; index++) {
		if (inputs_linha[index].getAttribute('data-atributo') == "id" 
		|| inputs_linha[index].getAttribute('data-atributo') == "turno" 
		|| inputs_linha[index].getAttribute('data-atributo') == "nome_npc" 
		|| inputs_linha[index].getAttribute('data-atributo') == "pop") {
			if (inputs_linha[index].type == "checkbox" && !inputs_linha[index].checked) {
				dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"=0";
			} else {
				dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"="+inputs_linha[index].value;
			}
		}
	}

	return processa_xhttp_basico(dados_ajax);
}

/******************
function valida_instalacao_recurso(objeto)
--------------------
Valida os dados da Instalação
objeto -- objeto sendo editado
******************/	
function valida_instalacao_recurso(objeto) {
	
	let linha = pega_ascendente(objeto,"TR");
	let celulas = linha.cells;
	let inputs_linha = linha.getElementsByTagName("INPUT");
	let select_linha = linha.getElementsByTagName("SELECT");
	let dados_ajax = "post_type=POST&action=valida_instalacao_recurso";
	
	if (!valida_generico(objeto)) {
		return false;
	}	
	
	let id_recurso = "";
	if (typeof(select_linha[0]) !== "undefined") {
		id_recurso = select_linha[0].value;
		dados_ajax = dados_ajax +"&id_recurso="+id_recurso;
	}

	for (let index = 0; index < inputs_linha.length; index++) {
		if (inputs_linha[index].getAttribute('data-atributo') == "id_instalacao" 
		|| inputs_linha[index].getAttribute('data-atributo') == "consome" 
		|| (inputs_linha[index].getAttribute('data-atributo') == "id_recurso" && id_recurso == "")
		|| inputs_linha[index].getAttribute('data-atributo') == "id") {
			dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"="+inputs_linha[index].value;
		}
	}
	
	return processa_xhttp_basico(dados_ajax);
}

/******************
function valida_planeta_recurso(objeto)
--------------------
Valida os recursos da Colônia
objeto -- objeto sendo editado
******************/	
function valida_planeta_recurso(objeto) {
	
	let linha = pega_ascendente(objeto,"TR");
	let celulas = linha.cells;
	let inputs_linha = linha.getElementsByTagName("INPUT");
	let select_linha = linha.getElementsByTagName("SELECT");
	let dados_ajax = "post_type=POST&action=valida_planeta_recurso";
	
	if (!valida_generico(objeto)) {
		return false;
	}

	let id_recurso = "";
	if (typeof(select_linha[0]) !== "undefined") {
		id_recurso = select_linha[0].value;
		dados_ajax = dados_ajax +"&id_recurso="+id_recurso;
	}

	for (let index = 0; index < inputs_linha.length; index++) {
		if (inputs_linha[index].getAttribute('data-atributo') == "id_planeta"  
		|| (inputs_linha[index].getAttribute('data-atributo') == "id_recurso" && typeof(id_recurso) === "undefined") 
		|| inputs_linha[index].getAttribute('data-atributo') == "id" 
		|| inputs_linha[index].getAttribute('data-atributo') == "turno") {
			dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"="+inputs_linha[index].value;
		}
	}

	let retorno = new Promise((resolve, reject) =>	{
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				let resposta = "";
				try {
					resposta = JSON.parse(this.responseText);
				} catch (err) {
					console.log(this.responseText);
					console.log(err);
					reject('JSON_PARSE');
					return false;
				}
				
				if (resposta.resposta_ajax == "OK!") {
					resolve(altera_recursos_planeta(objeto));
				} else {
					alert(resposta.resposta_ajax);
					resolve(false);
				}
			}
		};
		xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send(dados_ajax);
	});

	return retorno;
}

/******************
function valida_colonia_instalacao(objeto)
--------------------
Valida as Instalações da Colônia
objeto -- objeto sendo editado
******************/	
function valida_colonia_instalacao(objeto) {
	
	let linha = pega_ascendente(objeto,"TR");
	let celulas = linha.cells;
	let inputs_linha = linha.getElementsByTagName("INPUT");
	let select_linha = linha.getElementsByTagName("SELECT");
	let dados_ajax = "post_type=POST&action=valida_colonia_instalacao";
	
	if (!valida_generico(objeto)) {
		return false;
	}

	let id_instalacao = "";
	if (typeof(select_linha[0]) !== "undefined") {
		id_instalacao = select_linha[0].value;
		dados_ajax = dados_ajax +"&id_instalacao="+id_instalacao;
	}

	for (let index = 0; index < inputs_linha.length; index++) {
		if (inputs_linha[index].getAttribute('data-atributo') == "nivel" ||inputs_linha[index].getAttribute('data-atributo') == "id_planeta" 
		|| (inputs_linha[index].getAttribute('data-atributo') == "id_instalacao" && typeof(id_instalacao) === "undefined") 
		|| inputs_linha[index].getAttribute('data-atributo') == "id"
		|| inputs_linha[index].getAttribute('data-atributo') == "instalacao_inicial"
		|| inputs_linha[index].getAttribute('data-atributo') == "turno_desmonta"
		) {
			if (inputs_linha[index].type == "checkbox" && !inputs_linha[index].checked) {
				dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"=0";
			} else {
				dados_ajax = dados_ajax +"&"+inputs_linha[index].getAttribute('data-atributo')+"="+inputs_linha[index].value;
			}
		}
	}

	let retorno = new Promise((resolve, reject) =>	{
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				let resposta = "";
				try {
					resposta = JSON.parse(this.responseText);
				} catch (err) {
					console.log(this.responseText);
					console.log(err);
					reject('JSON_PARSE');
					return false;
				}
				
				if (resposta.debug !== undefined) {
					console.log(resposta.debug);
				}
				if (resposta.resposta_ajax == "OK!") {
					if (resposta.confirma != "") {
						let confirma = confirm(resposta.confirma);
						resolve(confirma);
					} else {
						resolve(true);
					}
				} else {
					alert(resposta.resposta_ajax);
					resolve(false);
				}
			}
		}
		xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send(dados_ajax);
	});


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

	let objeto_editado = pega_dados_objeto(objeto);//Pega os dados do objeto
	
	let dados_ajax = "post_type=POST&action=valida_acao_admin&turno="+objeto_editado['turno'].value+"&id_imperio="+objeto_editado['id_imperio'].value
	+"&lista_recursos="+objeto_editado['lista_recursos'].value+"&qtd="+objeto_editado['qtd'].value+"&descricao="+objeto_editado['descricao'].value+"&id="+objeto_editado['id'].value
	+"&lista_recursos_original="+objeto_editado['lista_recursos'].parentNode.getAttribute('data-valor-original')+"&qtd_original="+objeto_editado['qtd'].parentNode.getAttribute('data-valor-original');
	
	let retorno = new Promise((resolve, reject) =>	{
		//Envia a chamada de AJAX para salvar o objeto
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				let resposta = "";
				try {
					resposta = JSON.parse(this.responseText);
				} catch (err) {
					console.log(this.responseText);
					console.log(err);
					reject('JSON_PARSE');
					return false;
				}
				
				if (resposta.resposta_ajax == "OK!") {
					let div_resposta = document.getElementById("div_resposta");
					div_resposta.innerHTML = resposta.html;
					resolve(true);
				} else {
					alert(resposta.resposta_ajax);
					let div_resposta = document.getElementById("div_resposta");
					div_resposta.innerHTML = resposta.html;
					resolve(false);
				}
			}
		};
		xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send(dados_ajax);
	});
	
	return retorno;
}

/******************
function altera_recursos_planeta(objeto) 
--------------------
Atualiza os recursos do planeta nos Turnos anteriores, caso necessário
******************/	
function altera_recursos_planeta(objeto) {
	let dados = pega_dados_objeto(objeto);//Pega os dados do objeto

	let linha = pega_ascendente(objeto,"TR");
	let divs = linha.getElementsByTagName("DIV");
	
	let dados_ajax = "";
	for (let index=0; index<divs.length; index++) {
		if (divs[index].getAttribute("data-atributo") == "nome_recurso") {
			if (divs[index].getAttribute("data-id-selecionado") != divs[index].childNodes[1].value) {
				console.log("MUDOU! De "+divs[index].getAttribute("data-id-selecionado")+" para "+divs[index].childNodes[1].value);
				dados_ajax = "post_type=POST&action=altera_recursos_planeta&id_planeta="+dados['id_planeta'].value+"&id_recurso_original="+divs[index].getAttribute("data-id-selecionado")+"&id_recurso="+divs[index].childNodes[1].value;				
			}
		}
	}
	
	return processa_xhttp_basico(dados_ajax);	
}

/******************
function valida_transfere_tech(objeto) 
--------------------
Valida uma transferência de tecnologia
******************/	
function valida_transfere_tech(objeto){
	let dados = pega_dados_objeto(objeto);//Pega os dados do objeto
	let dados_ajax = "post_type=POST&action=valida_transfere_tech&turno="+dados['turno'].value+"&id_imperio_origem="+dados['id_imperio_origem'].value+"&id_imperio_destino="+dados['id_imperio_destino'].value+"&id_tech="+dados['id_tech'].value;
	
	return processa_xhttp_basico (dados_ajax);
}

/******************
function valida_transfere_recurso(objeto) 
--------------------
Valida uma transferência de recursos
******************/	
function valida_transfere_recurso(objeto){
	let dados = pega_dados_objeto(objeto);//Pega os dados do objeto
	let dados_ajax = "post_type=POST&action=valida_transfere_recurso&turno="+dados['turno'].value+"&id_imperio_origem="+dados['id_imperio_origem'].value
	+"&id_imperio_destino="+dados['id_imperio_destino'].value+"&id_recurso="+dados['id_recurso'].value+"&qtd="+dados['qtd'].value;
	
	if (dados['qtd'].value <= 0 || isNaN(dados['qtd'].value)) {
		alert('A quantidade a ser transferida é inválida!');
		return false;
	}
	
	return processa_xhttp_basico (dados_ajax);
}


/******************
function valida_nave(objeto) 
--------------------
Valida uma nave (inicialmente somente os custos)
******************/	
function valida_nave(objeto){
	let dados = pega_dados_objeto(objeto);//Pega os dados do objeto
	let dados_ajax = "post_type=POST&action=valida_nave&custo="+dados['custo'].value+"&string_nave="+dados['string_nave'].value
	+"&id_imperio="+dados['id_imperio'].value+"&tamanho="+dados['tamanho'].value+"&nivel_estacao_orbital="+dados['nivel_estacao_orbital'].value
	+"&id="+dados['id'].value;

	if (!valida_generico(objeto)) {
		return false;
	}
	
	//TODO -- valida se a nave pode ser construída pelo jogador
	
	return processa_xhttp_basico(dados_ajax);
}