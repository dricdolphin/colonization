/******************
function valida_acao(evento, objeto)
--------------------
Pega os produtos da Ação
******************/	
function valida_acao(evento, objeto) {
	var objeto_editado = pega_dados_objeto(objeto);//Pega os dados do objeto
	var linha = pega_ascendente(objeto,"TR");
	var divs = linha.getElementsByTagName('DIV');
	var inputs = linha.getElementsByTagName('INPUT');
	var labels = linha.getElementsByTagName('LABEL');
	var dados = []; //Dados que serão enviados para a validação
	var desativar_objeto = false;
	
	if (evento.button === 3) {
		evento.preventDefault();
		return false;
	}
	
	for (let index=0;index<inputs.length;index++) {
		if (inputs[index].getAttribute('data-atributo') == "desativado") {	
			desativar_objeto = true;
		}
		
		if (inputs[index].getAttribute('data-atributo') == "turno" || inputs[index].getAttribute('data-atributo') == "id_imperio" || inputs[index].getAttribute('data-atributo') == "id_instalacao" || inputs[index].getAttribute('data-atributo') == "id_planeta_instalacoes" || inputs[index].getAttribute('data-atributo') == "id_planeta" || inputs[index].getAttribute('data-atributo') == "desativado") {
			dados[inputs[index].getAttribute('data-atributo')] = inputs[index].value;
		} else if (inputs[index].getAttribute('data-atributo') == "pop") {
			//No caso do atributo "pop", precisamos validar a DIFERENÇA entre o valor já salvo (data-valor-original) e o valor novo, para verificar se estamos ou não ultrapassando algum limite de consumo
			dados['pop_original'] = inputs[index].getAttribute('data-valor-original');
			dados['pop'] = inputs[index].value;
		}
	}

	var dados_ajax = "post_type=POST&action=valida_acao&turno="+dados['turno']+"&id_imperio="+dados['id_imperio']+"&id_instalacao="+dados['id_instalacao']+"&id_planeta_instalacoes="+dados['id_planeta_instalacoes']+"&id_planeta="+dados['id_planeta']+"&desativado="+dados['desativado']+"&pop="+dados['pop']+"&pop_original="+dados['pop_original'];
	var retorno = {};
	retorno.retorno = true;
	
	//Envia a chamada de AJAX para salvar o objeto
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.status == 400) {
			
		}
		if (this.readyState == 4 && this.status == 200) {
			try {
				var resposta = JSON.parse(this.responseText);
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
		}
	};
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);

	evento.preventDefault();
	return retorno;
}



/******************
function efetua_acao(evento, objeto) 
--------------------
Tenta atualizar os dados
******************/	
function efetua_acao (evento, objeto, valida) {
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
	var objeto_editado = pega_dados_objeto(objeto);//Pega os dados do objeto
	var linha = pega_ascendente(objeto,"TR");
	var divs = linha.getElementsByTagName('DIV');
	var inputs = linha.getElementsByTagName('INPUT');
	var labels = linha.getElementsByTagName('LABEL');
	var dados = []; //Dados que serão enviados para a validação
	
	if (cancela) {
		//***
		for (let index=0;index<divs.length;index++) {
			if (divs[index].getAttribute('data-atributo') == "gerenciar") {
				divs[index].style.visibility = "hidden";
			}
		}
		//***/
		
		for (let index=0;index<inputs.length;index++) {
			if (inputs[index].getAttribute('data-atributo') == "pop") {
				var index_range_pop = index;
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

	if (objeto_editado['where_value'] == "") {//Se a o valor do WHERE estiver em branco, significa que estamos criando um objeto novo
		var where_clause = objeto_editado['where_clause'];
		objeto_editado['where_value'] = objeto_editado[where_clause].value;
	}

	for (index=0;index<inputs.length;index++) {
		if (inputs[index].getAttribute('data-atributo') == "turno" || inputs[index].getAttribute('data-atributo') == "id_imperio" || inputs[index].getAttribute('data-atributo') == "id_instalacao" || inputs[index].getAttribute('data-atributo') == "id_planeta_instalacoes" || inputs[index].getAttribute('data-atributo') == "id_planeta" || inputs[index].getAttribute('data-atributo') == "desativado") {
			dados[inputs[index].getAttribute('data-atributo')] = inputs[index].value;
		} else if (inputs[index].getAttribute('data-atributo') == "pop") {
			//No caso do atributo "pop", precisamos validar a DIFERENÇA entre o valor já salvo (data-valor-original) e o valor novo, para verificar se estamos ou não ultrapassando algum limite de consumo
			dados['pop_original'] = inputs[index].getAttribute('data-valor-original');
			dados['pop'] = inputs[index].value;
		}
	}
	
	//Cria o string que será passado para o AJAX
	console.log("Dados Ajax: "+objeto_editado['dados_ajax']);
	objeto_editado['dados_ajax'] = "post_type=POST&action=salva_acao&tabela="+objeto_editado['nome_tabela']+objeto_editado['dados_ajax']+"&where_clause="+objeto_editado['where_clause']+"&where_value="+objeto_editado['where_value'];	

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
					} else if(inputs[index].getAttribute('data-atributo') == "id_planeta") {
						var id_planeta = inputs[index].value;
					} else  if(inputs[index].getAttribute('data-atributo') == "id_estrela") {
						var id_estrela = inputs[index].value;
					} else if (inputs[index].getAttribute('data-atributo') == "id_planeta_instalacoes") {
						var id_planeta_instalacoes = inputs[index].value;
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
		}
	};
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(objeto_editado['dados_ajax']);

	range_em_edicao = true; //Trava o objeto em modo de edição até que o AJAX libere
	evento.preventDefault();
	return false;
}