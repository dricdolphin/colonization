/******************
function altera_acao(objeto) 
--------------------
Desabilita o modo de edição e atualiza os dados
objeto -- objeto sendo desabilitado
cancela = false -- define se pega os dados originais ou os novos
******************/	
function altera_acao(evento, objeto) {
	let linha = pega_ascendente(objeto,"TR");
	let inputs = linha.getElementsByTagName("INPUT");
	let selects = linha.getElementsByTagName("SELECT");
	let labels = linha.getElementsByTagName("LABEL");
	let tds = linha.getElementsByTagName("TD");

	if (!range_em_edicao || range_em_edicao == objeto) {
		range_em_edicao = objeto;
		
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
		
		for (let index=0; index<labels.length; index++) {
			if (labels[index].getAttribute('data-atributo') == 'pop') {
				labels[index].innerText = objeto.value;
			}
		}
		
	} else {
		alert('Já existe uma ação em edição!');
		objeto.value = objeto.getAttribute('data-valor-original');
		
		evento.preventDefault();
		return false;
	}
}


/******************
function valida_acao(evento, objeto)
--------------------
Pega os produtos da Ação
******************/	
function valida_acao(evento, objeto, forcar_valida_acao = false) {
	let objeto_editado = pega_dados_objeto(objeto);//Pega os dados do objeto
	let linha = pega_ascendente(objeto,"TR");
	let divs = linha.getElementsByTagName('DIV');
	let inputs = linha.getElementsByTagName('INPUT');
	let labels = linha.getElementsByTagName('LABEL');
	let dados = []; //Dados que serão enviados para a validação
	
	if (evento.button !== 0 && evento.type !== "touchend" && !forcar_valida_acao) {
		evento.preventDefault();
		return false;
	}
	
	for (let index=0;index<inputs.length;index++) {
		if (inputs[index].getAttribute('data-atributo') == "turno" || inputs[index].getAttribute('data-atributo') == "id_imperio" || inputs[index].getAttribute('data-atributo') == "id_instalacao" || inputs[index].getAttribute('data-atributo') == "id_planeta_instalacoes" || inputs[index].getAttribute('data-atributo') == "id_planeta" || inputs[index].getAttribute('data-atributo') == "desativado") {
			dados[inputs[index].getAttribute('data-atributo')] = inputs[index].value;
		} else if (inputs[index].getAttribute('data-atributo') == "pop") {
			//No caso do atributo "pop", precisamos validar a DIFERENÇA entre o valor já salvo (data-valor-original) e o valor novo, para verificar se estamos ou não ultrapassando algum limite de consumo
			dados['pop_original'] = inputs[index].getAttribute('data-valor-original');
			dados['pop'] = inputs[index].value;
		}
	}
	
	if (objeto.type == "checkbox") {
		dados['pop'] = 0;
	}
	
	if (dados['pop_original'] == dados['pop']) {
		evento.preventDefault();
		return false;
	}

	let dados_ajax = "post_type=POST&action=valida_acao&turno="+dados['turno']+"&id_imperio="+dados['id_imperio']+"&id_instalacao="+dados['id_instalacao']+"&id_planeta_instalacoes="+dados['id_planeta_instalacoes']+"&id_planeta="+dados['id_planeta']+"&desativado="+dados['desativado']+"&pop="+dados['pop']+"&pop_original="+dados['pop_original'];
	let retorno = {};
	retorno.retorno = true;
	
	//Envia a chamada de AJAX para salvar o objeto
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			let resposta = "";
			try {
				resposta = JSON.parse(this.responseText);
				retorno.resposta = resposta;
			} 
			catch (err) {
				console.log(this.responseText);
				retorno = false;
				return false;
			}

			if (resposta.debug !== undefined) {
				console.log(resposta.debug);
			}			

			if (resposta.resposta_ajax == "OK!") {
				if (resposta.balanco_acao != "") {
					retorno = false;
					alert("Não é possível realizar esta ação! Estão faltando os seguintes recursos: "+resposta.balanco_acao);
				}
			} else {
				alert(resposta.resposta_ajax);
				retorno = false;
			}
		
			//Chama o call-back "efetua ação"
			efetua_acao(evento, objeto, retorno);
		} else if(this.status == 500) {
			console.log(this.responseText);
			console.log(this.statusText);
		}
	};
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);

	evento.preventDefault();
	return retorno;
}

/******************
function desativar_instalacao(evento, objeto)
--------------------
Função para chamar o AJAX de desativar uma instalação
objeto -- objeto sendo editado
******************/
function desativar_instalacao(evento, objeto, id_acao) {
	if (evento.button !== 0) {
		evento.preventDefault();
		return false;
	}
	
	if (range_em_edicao || range_em_edicao == objeto) {
		alert('Já existe uma ação em edição!');
		objeto.value = objeto.getAttribute('data-valor-original');
		
		evento.preventDefault();
		return false;
	}
	
	let linha=pega_ascendente(objeto,"TR");
	let inputs=linha.getElementsByTagName("INPUT");
	let divs=linha.getElementsByTagName("DIV");
	//var dados_ajax = "post_type=POST&action=desativar_instalacao";

	range_em_edicao = objeto;
	
	for (let index = 0; index < inputs.length; index++) {
		if (inputs[index].getAttribute('data-atributo') == "desativado") {
			if (inputs[index].value == 1) {
				inputs[index].value = 0;
			} else {
				inputs[index].value = 1;
			}
		}
	}

	for (let index = 0; index < divs.length; index++) {
		if(divs[index].getAttribute('data-atributo') == "gerenciar") {
			divs[index].style.visibility = "visible";
		}
	}

	//dados_ajax = dados_ajax + "&id=" + id_acao;
	
	/***
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
			} else {
				alert(resposta.resposta_ajax);
			}
		}
	};
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);
	//***/
	
	//objeto_em_edicao = true;
	valida_acao(evento, objeto);
	
	evento.preventDefault();
	return false;
}


/******************
function efetua_acao(evento, objeto) 
--------------------
Tenta atualizar os dados
******************/	
function efetua_acao (evento, objeto, valida) {
	let objeto_editado = pega_dados_objeto(objeto);//Pega os dados do objeto
	let linha = pega_ascendente(objeto,"TR");
	let divs = linha.getElementsByTagName('DIV');
	let inputs = linha.getElementsByTagName('INPUT');
	let labels = linha.getElementsByTagName('LABEL');
	let dados = []; //Dados que serão enviados para a validação
	
	for (let index=0;index<inputs.length;index++) {
		if (inputs[index].getAttribute('data-atributo') == "turno" || inputs[index].getAttribute('data-atributo') == "id_imperio" || inputs[index].getAttribute('data-atributo') == "id_instalacao" || inputs[index].getAttribute('data-atributo') == "id_planeta_instalacoes" || inputs[index].getAttribute('data-atributo') == "id_planeta") {
			dados[inputs[index].getAttribute('data-atributo')] = inputs[index].value;
		} else if (inputs[index].getAttribute('data-atributo') == "pop") {
			//No caso do atributo "pop", precisamos validar a DIFERENÇA entre o valor já salvo (data-valor-original) e o valor novo, para verificar se estamos ou não ultrapassando algum limite de consumo
			dados['pop_original'] = inputs[index].getAttribute('data-valor-original');
			dados['pop'] = inputs[index].value;
		} else if(inputs[index].getAttribute('data-atributo') == "desativado") {
			console.log("Desativado: "+inputs[index].value);
			dados[inputs[index].getAttribute('data-atributo')] = inputs[index].value;
			inputs[index].checked = !inputs[index].checked;
		}
	}
	
	//let valida = valida_acao(dados); //Valida os dados
	
	if (!valida.retorno) {
		salva_acao(evento, objeto, true); //Se não liberou, falhou a validação, então cancela a ação.	
	} else {
		salva_acao(evento, objeto, false, valida.resposta); //Pode salvar!
		
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
function salva_acao(evento, objeto, cancela = false, produtos_acao={}) {
	let objeto_editado = pega_dados_objeto(objeto);//Pega os dados do objeto
	let linha = pega_ascendente(objeto,"TR");
	let divs = linha.getElementsByTagName('DIV');
	let inputs = linha.getElementsByTagName('INPUT');
	let labels = linha.getElementsByTagName('LABEL');
	let dados = []; //Dados que serão enviados para a validação
	
	if (cancela) {
		//***
		for (let index=0;index<divs.length;index++) {
			if (divs[index].getAttribute('data-atributo') == "gerenciar") {
				divs[index].style.visibility = "hidden";
			}
		}
		//***/
		
		let index_range_pop = 0;
		for (let index=0;index<inputs.length;index++) {
			if (inputs[index].getAttribute('data-atributo') == "pop") {
				index_range_pop = index;
				inputs[index].value = inputs[index].getAttribute("data-valor-original");
			} else if (inputs[index].getAttribute('data-atributo') == "desativado") {
				inputs[index].value = inputs[index].getAttribute("data-valor-original");
				inputs[index].checked = !inputs[index].checked;
			}
		}

		for (let index=0; index<labels.length; index++) {
			if (labels[index].getAttribute('data-atributo') == "pop") {
				labels[index].innerText = inputs[index_range_pop].value;
			}
		}
		
		range_em_edicao = false;
		
		evento.preventDefault();
		return false;
	}

	let where_clause = "";
	if (objeto_editado['where_value'] == "") {//Se a o valor do WHERE estiver em branco, significa que estamos criando um objeto novo
		where_clause = objeto_editado['where_clause'];
		objeto_editado['where_value'] = objeto_editado[where_clause].value;
	}

	for (let index=0;index<inputs.length;index++) {
		if (inputs[index].getAttribute('data-atributo') == "turno" || inputs[index].getAttribute('data-atributo') == "id_imperio" || inputs[index].getAttribute('data-atributo') == "id_instalacao" || inputs[index].getAttribute('data-atributo') == "id_planeta_instalacoes" || inputs[index].getAttribute('data-atributo') == "id_planeta" || inputs[index].getAttribute('data-atributo') == "desativado") {
			dados[inputs[index].getAttribute('data-atributo')] = inputs[index].value;
		} else if (inputs[index].getAttribute('data-atributo') == "pop") {
			//No caso do atributo "pop", precisamos validar a DIFERENÇA entre o valor já salvo (data-valor-original) e o valor novo, para verificar se estamos ou não ultrapassando algum limite de consumo
			dados['pop_original'] = inputs[index].getAttribute('data-valor-original');
			dados['pop'] = inputs[index].value;
		}
	}
	
	//Cria o string que será passado para o AJAX
	//console.log("Dados Ajax: "+objeto_editado['dados_ajax']);
	objeto_editado['dados_ajax'] = "post_type=POST&action=salva_acao&tabela="+objeto_editado['nome_tabela']+objeto_editado['dados_ajax']+"&where_clause="+objeto_editado['where_clause']+"&where_value="+objeto_editado['where_value'];	

	//Envia a chamada de AJAX para salvar o objeto

	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			let resposta = "";
			try {
				resposta = JSON.parse(this.responseText);
			} 
			catch (err) {
				console.log(this.responseText);
				retorno = false;
				return false;
			}
			
			if (resposta.resposta_ajax == "SALVO!") {
				//Após salvar os dados, remove os "inputs" e transforma a linha em texto, deixando o Império passível de ser editado
				//var objeto_desabilitado = desabilita_edicao_objeto(objeto);
				let linha = pega_ascendente(objeto,"TR");
				let objeto_atualizado = atualiza_objeto(linha,resposta[0]); //O objeto salvo está no array resposta[0]
				let divs = objeto_atualizado.getElementsByTagName("DIV");
				let inputs = objeto_atualizado.getElementsByTagName("INPUT");
				//***
				for (let index=0;index<divs.length;index++) {
					if (divs[index].getAttribute('data-atributo') == "gerenciar") {
						divs[index].style.visibility = "hidden";
					} 
				}
				//***/
				
				let id_imperio = "";
				let id_planeta = "";
				let id_estrela = "";
				let id_planeta_instalacoes = "";
				for (let index=0;index<inputs.length;index++) {
					if(inputs[index].getAttribute('data-atributo') == "id_imperio") {
						id_imperio = inputs[index].value;
					} else if(inputs[index].getAttribute('data-atributo') == "id_planeta") {
						id_planeta = inputs[index].value;
					} else  if(inputs[index].getAttribute('data-atributo') == "id_estrela") {
						id_estrela = inputs[index].value;
					} else if (inputs[index].getAttribute('data-atributo') == "id_planeta_instalacoes") {
						id_planeta_instalacoes = inputs[index].value;
					}
				}
				
				atualiza_produtos_acao(id_imperio, id_planeta, id_estrela, id_planeta_instalacoes, produtos_acao);
				range_em_edicao = false;
			} else {
				alert(resposta.resposta_ajax);
			}
			if (resposta.debug !== undefined) {
				console.log(resposta.debug);
			}
		} else if(this.status == 500) {
			console.log(this.responseText);
			console.log(this.statusText);
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
function atualiza_produtos_acao(id_imperio,id_planeta,id_estrela,id_planeta_instalacoes,resposta)
--------------------
Pega os produtos da Ação
id_imperio -- id do Império
******************/	
function atualiza_produtos_acao(id_imperio,id_planeta,id_estrela,id_planeta_instalacoes,resposta) {
	let dados_ajax= "post_type=POST&action=produtos_acao&id_imperio="+id_imperio+"&id_planeta="+id_planeta+"&id_estrela="+id_estrela;
	
	//if (resposta.resposta_ajax == "SALVO!") {
		let id_colonias = "lista_colonias_imperio_"+id_imperio;
		let id_produz = "recursos_produzidos_imperio_"+id_imperio;
		let id_consome = "recursos_consumidos_imperio_"+id_imperio;
		let id_balanco = "recursos_balanco_imperio_"+id_imperio;
		let id_balanco_planeta = "balanco_planeta_"+id_planeta;
		let id_pop_mdo_planeta = "pop_mdo_planeta_"+id_planeta;
		let nome_mdo_sistema = "mdo_sistema_"+id_estrela;
		
		let div_colonias = document.getElementById(id_colonias);
		let div_produz = document.getElementById(id_produz);
		let div_consome = document.getElementById(id_consome);
		let div_balanco = document.getElementById(id_balanco);
		let div_balanco_planeta = document.getElementById(id_balanco_planeta);
		let div_pop_mdo_planeta = document.getElementById(id_pop_mdo_planeta);
		let div_mdo_sistema = document.getElementsByName(nome_mdo_sistema);
		let div_produz_consome = document.getElementById(id_planeta_instalacoes);
		
		div_colonias.innerHTML = resposta.lista_colonias;
		div_produz.innerHTML = resposta.recursos_produzidos;
		div_consome.innerHTML = resposta.recursos_consumidos;
		div_balanco.innerHTML = resposta.recursos_balanco;
		div_balanco_planeta.innerHTML = resposta.balanco_planeta;
		div_pop_mdo_planeta.innerHTML = resposta.pop_mdo_planeta;
		if (div_produz_consome !== null) {
			div_produz_consome.innerHTML = resposta.id_planeta_instalacoes_produz_consome;
		}
		
		for (let index=0; index < div_mdo_sistema.length; index++) {
			div_mdo_sistema[index].innerHTML = resposta.mdo_sistema;
		}
	//}
}