/******************
function altera_lista_recursos_qtd(objeto) 
--------------------
Altera os valores da lista de recursos e suas qtds
objeto -- objeto sendo editado
******************/	
function altera_lista_recursos_qtd(objeto, cancela=false, valida=false) {
	let linha = pega_ascendente(objeto,"TR");
	let inputs = linha.getElementsByTagName("INPUT");
	let divs = linha.getElementsByTagName("DIV");
	let div_lista_recursos_qtd = "";
	let input_qtd = "";
	let input_lista_recursos = "";
	let qtds = [];
	let recursos = [];
	
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
		let index_qtds = 0;
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
function inclui_recurso(evento, id_recurso)
--------------------
Inclui um recurso na lista_recursos_qtd
******************/
function inclui_recurso(evento, id_recurso) {
	let inputs = document.getElementsByTagName("INPUT");
	let div_lista_recursos_qtd = "";
	let inputs_text = [];
	let div_recurso = "";
	let div_recurso_parent = "";
	let div_recurso_parent_html_original = "";
	let input_lista_recursos = "";
	let input_qtd = "";

	if (!objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}

	let index_input_text = 0;
	for (let index=0; index<inputs.length; index++) { //Pega todos os Inputs do tipo 'text'
		if (inputs[index].type == "text") {
			inputs_text[index_input_text] = inputs[index];
			index_input_text++;
		}
		if (inputs[index].getAttribute('data-atributo') == 'lista_recursos') {
			input_lista_recursos = inputs[index];
		} else if(inputs[index].getAttribute('data-atributo') == 'qtd') {
			input_qtd = inputs[index];
		}
	}
	
	let linha = pega_ascendente(input_lista_recursos,"TR");
	let divs = linha.getElementsByTagName("DIV");
	
	for (let index=0; index<divs.length; index++) {
		if (divs[index].getAttribute('data-atributo') == 'lista_recursos_qtd') {
			div_lista_recursos_qtd = divs[index];
		}
	}

	//Pega os Recursos que já existem. Se o novo recurso também existir, é para REMOVER. Pergunte antes de remover!
	let lista_lista_recursos = "";
	let lista_qtds = "";
	if(input_lista_recursos.value !== undefined) {
		lista_lista_recursos = input_lista_recursos.value.split(";");
		lista_qtds = input_qtd.value.split(";");
	}
	
	for (let index=0; index<lista_lista_recursos.length; index++) {
		if (lista_lista_recursos[index] == id_recurso) {
			//inputs_text[index].style.backgroundColor = "#DDDDDD";
			div_recurso = inputs_text[index].parentNode.parentNode;
			let resposta = true;
			if (inputs_text[index].value != 0 && inputs_text[index].value != "") {
				resposta = confirm("Deseja mesmo remover o recurso "+lista_recursos[id_recurso]+"?");
			}
			
			if (resposta) {//Remove o item
				lista_lista_recursos.splice(index,1);
				lista_qtds.splice(index,1);
				
				input_lista_recursos.value = lista_lista_recursos.join(';');
				input_qtd.value = lista_qtds.join(';');
				
				if (div_recurso.getAttribute('data-recurso-id') == id_recurso) {
					div_recurso_parent = div_recurso.parentNode;
					if (div_recurso_parent_html_original == "" && div_recurso_parent.getAttribute("data-valor-original") == "") {//Salva o HTML original, para o caso do usuário cancelar a edição
						let inputs_recursos = div_recurso_parent.getElementsByTagName("INPUT");
						div_recurso_parent_html_original = div_recurso_parent.innerHTML;
						
						for (let index_input=0; index_input<inputs_recursos.length; index_input++) {
							div_recurso_parent_html_original = div_recurso_parent_html_original.replace(inputs_recursos[index_input].outerHTML,inputs_recursos[index_input].value);
						}
						div_recurso_parent.setAttribute("data-valor-original", div_recurso_parent_html_original);
					}
					div_recurso_parent.removeChild(div_recurso);
					
					evento.preventDefault();
					return false;
				}
			}
		}
	}
		
	//Se o recurso não existe, então inclui na div_lista_recursos_qtd
	let div_parent = document.createElement('DIV');
	div_parent.setAttribute('data-recurso-id',id_recurso);
	div_parent.style.display = "inline-block";
	div_parent.innerHTML = "<div style='display: inline-block;' >"+lista_recursos[id_recurso]+"</div>: "
	+"<div style='display: inline-block; max-width: 40px;' data-editavel='true' data-ajax='false' data-style='max-width: 30px;' data-atributo='"+id_recurso+"' data-valor-original=''><input type='text' data-atributo='"+id_recurso+"' style='max-width: 30px;' value='0'></input></div> ;";
	div_lista_recursos_qtd.appendChild(div_parent);
	
	if (input_lista_recursos.value != "") {
		input_lista_recursos.value = input_lista_recursos.value+";"+id_recurso;
		input_qtd.value = input_qtd.value+";"+0;
	} else {
		input_lista_recursos.value = id_recurso;
		input_qtd.value = 0;
	}

	evento.preventDefault();
	return false;
}


/******************
function gerenciar_objeto(objeto)
--------------------
Abre a página de gerenciamento de informações acessórias de um objeto
objeto -- objeto sendo editado
redireciona -- Se é para redirecionar para outra página
******************/
function gerenciar_objeto(evento, objeto, redireciona = "") {
	//alert(typeof objeto);
	let linha=pega_ascendente(objeto,"TR");
	let inputs=linha.getElementsByTagName("INPUT");
	
	let id_objeto = "";
	for (let index = 0; index < inputs.length; index++) {
		if (inputs[index].getAttribute('data-atributo') == "id") {
			id_objeto = inputs[index].value;
		}
	}
	
	let vars = window.location.href.split("?");
	
	if (redireciona == "") {
		redireciona = vars[1];
	}
	
	let url_gerencia = vars[0]+"?"+redireciona+"&id="+id_objeto;
	url_gerencia = url_gerencia.replace("#","");
	window.location = url_gerencia;
	
	evento.preventDefault();
	return false;
}

/******************
function desbloquear_turno()
--------------------
Desbloqueia o Turno
******************/
function desbloquear_turno(evento, objeto) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
	
	objeto_em_edicao = true;
	
	let confirma = confirm("Tem certeza que deseja desbloquear o Turno?");
	if (confirma) {
		let dados_ajax = "post_type=POST&action=libera_turno";
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				let resposta = JSON.parse(this.responseText);
				
				objeto_em_edicao = false;
				
				if (resposta.resposta_ajax == "OK!") {
					retorno = true;
					objeto.parentNode.style.visibility="hidden";
					alert('Turno liberado! Favor clicar em Rodar Turno novamente!');
				} else {
					alert(resposta.resposta_ajax);
					retorno = false;
				}
			}
		};
		xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send(dados_ajax);
	}
}

/******************
function atualiza_lista_recursos()
--------------------
Atualiza a lista de Recursos enviados e recebidos
******************/
function atualiza_lista_recursos(objeto, argumentos="") {
	document.location.reload();
}


/******************
function atualiza_lista_techs()
--------------------
Atualiza a lista de Techs enviadas e recebidas
******************/
function atualiza_lista_techs(objeto, argumentos="") {
		let dados = pega_dados_objeto(objeto);//Pega os dados do objeto
		id_imperio=dados['id_imperio_origem'].value;
		id_tech=dados['id_tech'].value;

		let dados_ajax = "post_type=POST&action=dados_transfere_tech&id_imperio="+id_imperio;
		//console.log(dados_ajax);
		let xhttp_salvou = new XMLHttpRequest();
		xhttp_salvou.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				let resposta = JSON.parse(this.responseText);
				if (resposta.resposta_ajax == "OK!") {
					let div_techs_enviadas = document.getElementById("techs_enviadas");
					let div_techs_recebidas = document.getElementById("techs_recebidas");
					
					div_techs_enviadas.innerHTML = resposta.techs_enviadas
					div_techs_recebidas.innerHTML = resposta.techs_recebidas
					
					retorno = true;
				} else {
					alert(resposta.resposta_ajax);
					retorno = false;
				}
			}
		};
		xhttp_salvou.open("POST", ajaxurl, false); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
		xhttp_salvou.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp_salvou.send(dados_ajax);
}

/******************
function nave_visivel()
--------------------
Torna a nave visível
******************/
function nave_visivel (objeto, evento, id_nave) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
	
	objeto_em_edicao = true;
	
	let dados_ajax = "post_type=POST&action=nave_visivel&id="+id_nave;
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
			
			objeto_em_edicao = false;
			
			if (resposta.resposta_ajax == "SALVO!") {
				retorno = true;
			} else {
				alert(resposta.resposta_ajax);
				retorno = false;
			}
			
			if (resposta.debug != undefined) {
				console.log(resposta.debug);
			}
		}
	};
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);	
	
	objeto.style.display='none';

	evento.preventDefault();
	return false;
}

/******************
function envia_nave()
--------------------
Envia uma nave para algum lugar
******************/
function envia_nave (objeto, evento, id_nave) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
	
	
	let linha = pega_ascendente(objeto,"TR");
	let divs = linha.getElementsByTagName("DIV");
	let destinos_select = "";
	
	let id_estrela = 0;
	for (let index=0; index < divs.length; index++) {
		if (divs[index].getAttribute('data-atributo') == "nome_estrela") {
			for (let index_child=0; index_child<divs[index].childNodes.length; index_child++) {
				if (divs[index].childNodes[index_child].tagName == "SELECT") {
					id_estrela = divs[index].childNodes[index_child].value;
					destinos_select = divs[index].childNodes[index_child];
					break;
				}
			}
		}
	}

	let confirma = confirm('Tem certeza que deseja despachar a nave para ' + lista_nome_estrela[id_estrela] + ' ('+lista_x_estrela[id_estrela]+';'+lista_y_estrela[id_estrela]+';'+lista_z_estrela[id_estrela]+')?');
	
	if (!confirma) {
		evento.preventDefault();
		return false;
	}
	
	objeto_em_edicao = true;
	
	let dados_ajax = "post_type=POST&action=envia_nave&id="+id_nave+"&id_estrela="+id_estrela;
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
			
			
			objeto_em_edicao = false;
			if (resposta.debug != undefined) {
				console.log(resposta.debug);
			}
			
			if (resposta.resposta_ajax == "SALVO!") {
				retorno = true;
				alert('Nave despachada com sucesso!');
				destinos_select.disabled = true;
			} else {
				alert(resposta.resposta_ajax);
				destinos_select.disabled = false;
				objeto.style.display='inline';
				retorno = false;
			}
		} else if (this.status == 500) {
				console.log(this.responseText);
				console.log(this.statusText)
		}
	};
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);	
	
	objeto.style.display='none';

	evento.preventDefault();
	return false;
}

/******************
function upgrade_instalacao()
--------------------
Faz o upgrade de uma Instalação
******************/
function upgrade_instalacao(evento,objeto,nivel_maximo=0) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
	
	objeto_em_edicao = true;

	let linha = pega_ascendente(objeto,"TR");
	let celula = pega_ascendente(objeto,"TD");
	let inputs = linha.getElementsByTagName("INPUT");
	let labels = linha.getElementsByTagName("LABEL");
	let divs = linha.getElementsByTagName("DIV");

	let input_nivel = {};
	let dados = [];
	let input_pop = "";
	let checkbox_desativa_instalacao = "";
	for (let index=0; index<inputs.length; index++) {
		if (inputs[index].getAttribute("data-atributo") == "id_imperio" 
		|| inputs[index].getAttribute("data-atributo") == "id_planeta" 
		|| inputs[index].getAttribute("data-atributo") == "id_instalacao" 
		|| inputs[index].getAttribute("data-atributo") == "id_estrela" 
		|| inputs[index].getAttribute("data-atributo") == "id_planeta_instalacoes") {
			dados[inputs[index].getAttribute("data-atributo")] = inputs[index].value;
		} else if (inputs[index].getAttribute("data-atributo") == "nivel") {
			dados[inputs[index].getAttribute("data-atributo")] = inputs[index].value;
			input_nivel = inputs[index];
		} else if (inputs[index].getAttribute("data-atributo") == "pop" ) {
			input_pop = inputs[index];
		} else if (inputs[index].getAttribute("data-atributo") == "desativado") {
			checkbox_desativa_instalacao = inputs[index];
		}
	}
	
	let div_gerenciar = "";
	for (let index=0; index<divs.length; index++) {
		if(divs[index].getAttribute('data-atributo') == "gerenciar") {
			divs[index].style.visibility = "visible";
			div_gerenciar = divs[index];
		}
	}

	let label_pop = {};
	let label_mk = {};
	for (let index=0; index<labels.length; index++) {
		if (labels[index].getAttribute("data-atributo") == "pop") {
			label_pop = labels[index];
		} else if (labels[index].getAttribute("data-atributo") == "nivel") {
			label_mk = labels[index];
		}
	}
	
	let nivel_upgrade = dados['nivel']*1 + 1;
	
	let dados_ajax = "post_type=POST&action=valida_colonia_instalacao&upgrade_acao=true&id="+dados['id_planeta_instalacoes']+"&nivel="+nivel_upgrade+"&tabela=colonization_planeta_instalacoes"
	+"&where_clause=id&where_value="+dados['id_planeta_instalacoes']+"&id_planeta="+dados['id_planeta']+"&id_instalacao="+dados['id_instalacao']+"&id_imperio="+dados['id_imperio'];

	//***
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
			if (resposta.debug != undefined) {
				console.log(resposta.debug);
			}
			
			objeto_em_edicao = false;
			
			if (resposta.resposta_ajax == "SALVO!") {
				retorno = true;
				//Remove o objeto e ATUALIZA a Instalação
				switch(nivel_upgrade) {
					case 1:
						html_nivel = " Mk I";
						break;
					case 2:
						html_nivel = " Mk II";
						break;
					case 3:
						html_nivel = " Mk III";
						break;
					case 4:
						html_nivel = " Mk IV";
						break;
					case 5:
						html_nivel = " Mk V";
						break;
					case 6:
						html_nivel = " Mk VI";
						break;
					case 7:
						html_nivel = " Mk VII";
						break;
					case 8:
						html_nivel = " Mk VIII";
						break;
					case 9:
						html_nivel = " Mk IX";
						break;
					case 10:
						html_nivel = " Mk X";
						break;
					default:
						html_nivel = "";
					}
				
				label_mk.innerHTML = html_nivel;
				input_nivel.value = nivel_upgrade;
				let id_recursos_atuais = "recursos_atuais_imperio_"+dados['id_imperio'];
				let div_recursos_atuais = document.getElementById(id_recursos_atuais);
				div_recursos_atuais.innerHTML = resposta.recursos_atuais;
				
				let fator = ((nivel_upgrade-1)/(nivel_upgrade));
				
				div_gerenciar.style.visibility = "hidden";
				objeto_em_edicao = false;
				if (input_pop != "") {
					input_pop.value = Math.ceil(input_pop.value*fator);
					label_pop.innerHTML = input_pop.value;
					//input_pop.click();
					altera_acao(event, input_pop);
					valida_acao(event, input_pop, true);
				} else if (checkbox_desativa_instalacao != "") {
					if (checkbox_desativa_instalacao.value == 0) {
						//checkbox_desativa_instalacao.click();
						//altera_acao(event, checkbox_desativa_instalacao);
						//valida_acao(event, checkbox_desativa_instalacao, true);
						div_gerenciar.style.visibility = "visible";
						let retorno = new Promise((resolve, reject) => {
							let dados_ajax= "post_type=POST&action=produtos_acao&id_imperio="+dados['id_imperio']+"&id_planeta="+dados['id_planeta']+"&id_estrela="+dados['id_estrela']+"&id_planeta_instalacoes="+dados['id_planeta_instalacoes'];
							console.log(dados_ajax);
							objeto_em_edicao = true;
							range_em_edicao = true;
							resolve(processa_xhttp_resposta(dados_ajax));
						});
						
						retorno.then((successMessage) => {
							console.log(successMessage);
							atualiza_produtos_acao(dados['id_imperio'], dados['id_planeta'], dados['id_planeta'], dados['id_planeta_instalacoes'], successMessage);
							div_gerenciar.style.visibility = "hidden";
							objeto_em_edicao = false;
							range_em_edicao = false;							
						});
					}
				}

				if (nivel_upgrade >= nivel_maximo) {
					objeto.style.display='none';
				}

				//document.location.reload();
			} else {
				alert(resposta.resposta_ajax);
				div_gerenciar.style.visibility = "hidden";
				objeto_em_edicao = false;
				retorno = false;
			}
			
			objeto_em_edicao = false;
		}
	};
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);	
	//***/
	
	evento.preventDefault();
	return false;	
}

/******************
function alerta()
--------------------
Chama um alert
******************/
function alerta(texto) {
	alert(texto);
}

/******************
function aceita_missao()
--------------------
Aceita ou rejeita uma missão
******************/
function aceita_missao (objeto, evento, id_imperio, id_missao, aceita=true) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
	
	objeto_em_edicao = true;	
	
	let div_notice = objeto;
	
	while (div_notice.getAttribute("data-atributo") != "gerenciar") {
		div_notice = div_notice.parentNode;
	}
	
	let dados_ajax = "post_type=POST&action=aceita_missao&id="+id_missao+"&id_imperio="+id_imperio+"&aceita="+aceita;
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
			
			objeto_em_edicao = false;
			
			if (resposta.resposta_ajax == "SALVO!") {
				retorno = true;
				if (aceita === true) {
					div_notice.innerHTML = "<span style='color: #009922 !important;'><b>MISSÃO ACEITA!</b></span>";
				} else {
					div_notice.innerHTML = "<span style='color: #DD0022 !important;'><b>MISSÃO REJEITADA!</b></span> => <a href='#' onclick='return aceita_missao(this,event,"+id_imperio+","+id_missao+");' style='color: #009922 !important;'> Aceitar a Missão</a>";
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
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);	

	evento.preventDefault();
	return false;
}

/******************
function processa_viagem_nave()
--------------------
Processa a viagem de uma nave
******************/
function processa_viagem_nave (objeto, evento, id_nave) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
	
	objeto_em_edicao = true;	
	
	let div_notice = objeto;
	
	while (div_notice.getAttribute("class") != "notice") {
		div_notice = div_notice.parentNode;
	}

	let div_notice_panel = div_notice;
	while (div_notice_panel.getAttribute("class") != "notices-panel") {
		div_notice_panel = div_notice.parentNode;
	}

	let dados_ajax = "post_type=POST&action=processa_viagem_nave&id="+id_nave;
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			let resposta = "";
			try {
				resposta = JSON.parse(this.responseText);
			} 
			catch (err) {
				console.log(this.responseText);
				return false;
			}
			
			objeto_em_edicao = false;
			if (resposta.debug != undefined) {
				console.log(resposta.debug);
			}			
			if (resposta.resposta_ajax == "SALVO!") {
				div_notice.remove();
				if (div_notice_panel.childElementCount == 0) {
					div_notice_panel.remove();
				}
				if (resposta.alerta != undefined && resposta.alerta != "") {
					alert(resposta.alerta);
				}
			} else {
				alert(resposta.resposta_ajax);
			}
		}
	};
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);	

	evento.preventDefault();
	return false;
}

//processa_recebimento_recurso
/******************
function processa_recebimento_tech()
--------------------
Aceita ou rejeita o recebimento de uma Tech
******************/
function processa_recebimento_tech (objeto, evento, id_tech_transf, autoriza) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
	
	objeto_em_edicao = true;
	
	let div_notice = objeto;
	
	try {
		while (div_notice.getAttribute("class") != "notice") {
			div_notice = div_notice.parentNode;
		} 
	}
	catch (err) {
		div_notice.remove = function () {return;};
	}

	let div_notice_panel = div_notice;
	try {
		while (div_notice_panel.getAttribute("class") != "notices-panel") {
			div_notice_panel = div_notice.parentNode;
		} 
	}
	catch (err) {
		div_notice_panel.childElementCount = false;
	}

	if (autoriza) {
		autoriza = 1;
	} else {
		autoriza = 0;
	}
	
	//Salva também o registro da Transferência da Tech
	let dados_ajax = "post_type=POST&action=processa_recebimento_tech&tabela=colonization_imperio_transfere_techs&where_clause=id&where_value="+id_tech_transf+"&id="+id_tech_transf+"&processado=1&autorizado="+autoriza;
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
			
			objeto_em_edicao = false;
			if (resposta.resposta_ajax == "SALVO!") {
				retorno = true;
			} else {
				alert(resposta.resposta_ajax);
				retorno = false;
			}
			
			if (resposta.debug != undefined) {
				console.log(resposta.debug);
			}

			div_notice.remove();
			if (div_notice_panel.childElementCount == 0) {
				div_notice_panel.remove();
			}
					
			let envia_tech = document.getElementById("envia_tech");
			if (envia_tech !== undefined && envia_tech !== null) {
				atualiza_lista_techs(envia_tech);
			}		

		}
	};
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);	

	evento.preventDefault();
	return false;
}


/******************
function processa_recebimento_recurso()
--------------------
Processa o recebimento de um recurso
******************/
function processa_recebimento_recurso (objeto, evento, id_tech_transf) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
	
	objeto_em_edicao = true;
	
	let div_notice = objeto;
	
	try {
		while (div_notice.getAttribute("class") != "notice") {
			div_notice = div_notice.parentNode;
		} 
	}
	catch (err) {
		div_notice.remove = function () {return;};
	}

	let div_notice_panel = div_notice;
	try {
		while (div_notice_panel.getAttribute("class") != "notices-panel") {
			div_notice_panel = div_notice.parentNode;
		} 
	}
	catch (err) {
		div_notice_panel.childElementCount = false;
	}

	let dados_ajax = "post_type=POST&action=processa_recebimento_recurso&id="+id_tech_transf;
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
			
			objeto_em_edicao = false;
			if (resposta.resposta_ajax == "SALVO!") {
				retorno = true;
			} else {
				alert(resposta.resposta_ajax);
				retorno = false;
			}
			
			if (resposta.debug != undefined) {
				console.log(resposta.debug);
			}

			div_notice.remove();
			if (div_notice_panel.childElementCount == 0) {
				div_notice_panel.remove();
			}
			document.location.reload();
		}
	};
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);	

	evento.preventDefault();
	return false;
}

/******************
function encerra_turno
--------------------
Encerra o Turno
--------
******************/
function encerra_turno(evento, objeto) {
	let div_resultados = document.getElementById('resultado_turno');
	div_resultados.innerHTML = "Encerrando o Turno...";
	let dados_ajax = "post_type=POST&action=encerra_turno";
	
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			let resposta = "";
			try {
				resposta = JSON.parse(this.responseText);
			} 
			catch (err) {
				console.log(this.responseText);
				return false;
			}
			if (resposta.resposta_ajax != "OK!") {
				alert (resposta.resposta_ajax);
			}
			div_resultados.innerHTML = "Turno Encerrado!";
			objeto.parentNode.style.display = "none";
		}
	};

	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);

	evento.preventDefault();
	return false;
}

/******************
function roda_turno
--------------------
Roda o Turno
--------
******************/
function roda_turno(evento) {
	let div_resultados = document.getElementById('resultado_turno');
	div_resultados.innerHTML = "Processando o Turno, aguarde!";
	let dados_ajax = "post_type=POST&action=roda_turno";
	
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
			
			let div_resultados = document.getElementById('resultado_turno');
			div_resultados.innerHTML = resposta.html;
			if (resposta.turno_novo != '') {
				let div_turno = document.getElementById('div_turno');
				let div_dados_acoes_imperios = document.getElementById('dados_acoes_imperios');
				div_dados_acoes_imperios.innerHTML = resposta.dados_acoes_imperios;
				div_turno.innerHTML = resposta.turno_novo;
			}
		} else if (this.status == 500) {
				console.log(this.responseText);
				console.log(this.statusText)
		}
	};
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);
	
	evento.preventDefault();
	return false;
}


/******************
function mostra_div_transferencia()
--------------------
Mostra o Div onde ficam os planetas para onde pode enviar a Pop
******************/
function mostra_div_transferencia (evento, objeto) {
	let div_mestre = objeto.parentNode;
	
	for (let index=0; index < div_mestre.childNodes.length; index++) {
		if (div_mestre.childNodes[index].tagName == "DIV") {
			if (div_mestre.childNodes[index].getAttribute('data-atributo') == "lista_planetas") {
				if (div_mestre.childNodes[index].style.display == "block" ) {
					div_mestre.childNodes[index].style.maxHeight = "0px";
					div_mestre.childNodes[index].style.display = "none";
				} else {
					div_mestre.childNodes[index].style.display = "block";
					div_mestre.childNodes[index].style.maxHeight = "36px";
				}
			}
		}
	}
	
	evento.preventDefault()
	return false;
}


/******************
function atualiza_custo_instalacao()
--------------------
Atualiza a DIV com o custo das instalações
******************/
function atualiza_custo_instalacao(evento, objeto) {
	let index_selecionado = objeto.selectedIndex;
	let id_instalacao = objeto.options[index_selecionado].value;
	let td_instalacao = pega_ascendente(objeto,"TD");
	let divs_td_instalacao = td_instalacao.getElementsByTagName("DIV");
	let div = [];

	
	for (let index_divs=0; index_divs<divs_td_instalacao.length; index_divs++) {
		if (divs_td_instalacao[index_divs].getAttribute('data-atributo') == "custo_instalacao" || divs_td_instalacao[index_divs].getAttribute('data-atributo') == "descricao_instalacao"
			|| divs_td_instalacao[index_divs].getAttribute('data-atributo') == "tech_instalacao" || divs_td_instalacao[index_divs].getAttribute('data-atributo') == "produz_instalacao") {
			div[divs_td_instalacao[index_divs].getAttribute('data-atributo')] = divs_td_instalacao[index_divs];
		} 
	}
	
	div['custo_instalacao'].innerHTML = "Custo por nível: " + custos_instalacao[id_instalacao];
	div['descricao_instalacao'].innerHTML = "<b>Descrição:</b> " + descricao_instalacao[id_instalacao];
	div['produz_instalacao'].innerHTML = "<b>Produção/Consumo:</b> " + produz_instalacao[id_instalacao];
	div['tech_instalacao'].innerHTML = "<b>Tech Requisito:</b> " + tech_instalacao[id_instalacao];
}

/******************
function atualiza_custo_tech()
--------------------
Atualiza a DIV com o custo da Tech
******************/
function atualiza_custo_tech(evento, objeto) {
	let index_selecionado = objeto.selectedIndex;
	let id_tech = objeto.options[index_selecionado].value;
	let tr_tech = pega_ascendente(objeto,"TR");
	let divs_td_tech = tr_tech.getElementsByTagName("DIV");
	let inputs_td_tech = tr_tech.getElementsByTagName("INPUT");
	
	for (let index_divs=0; index_divs<divs_td_tech.length; index_divs++) {
		if (divs_td_tech[index_divs].getAttribute('data-atributo') == "custo_tech") {
			divs_td_tech[index_divs].innerHTML = custos_tech[id_tech];
		} else if (divs_td_tech[index_divs].getAttribute('data-atributo') == "descricao_tech") {
			divs_td_tech[index_divs].innerHTML = descricao_tech[id_tech];
		}
	}

	for (let index_inputs=0; index_inputs<inputs_td_tech.length; index_inputs++) {
		if (inputs_td_tech[index_inputs].getAttribute('data-atributo') == "id" || inputs_td_tech[index_inputs].getAttribute('data-atributo') == "where_value") {
			inputs_td_tech[index_inputs].value = objeto.options[index_selecionado].getAttribute('data-id-imperio-tech');
		} else if(inputs_td_tech[index_inputs].getAttribute('data-atributo') == "id_tech") {
			inputs_td_tech[index_inputs].value = objeto.options[index_selecionado].value;
			console.log(objeto.options[index_selecionado].value);
		}
	}
}

/******************
function atualiza_recursos_imperio(objeto) 
--------------------
Atualiza a tabela de recursos do Império
******************/	
function atualiza_recursos_imperio(objeto, argumento=false) {
	let tabela = pega_ascendente(objeto, "TABLE");
	let tr = pega_ascendente(objeto, "TR");
	let inputs = tr.getElementsByTagName("INPUT");
	
	dados = [];
	for (let index_inputs=0; index_inputs < inputs.length; index_inputs++) {
		if (inputs[index_inputs].getAttribute('data-atributo') != undefined) {
			dados[inputs[index_inputs].getAttribute('data-atributo')] = inputs[index_inputs].value;
		}
	}

	
	let dados_ajax = "post_type=POST&action=recursos_atuais_imperio&id_imperio="+dados['id_imperio'];
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			let resposta = "";
			try {
				resposta = JSON.parse(this.responseText);
			} 
			catch (err) {
				console.log(this.responseText);
				return false;
			}
			
			let id_recursos_atuais = "recursos_atuais_imperio_"+dados['id_imperio'];
			let div_recursos_atuais = document.getElementById(id_recursos_atuais);
			div_recursos_atuais.innerHTML = resposta.recursos_atuais;	
		}
	};

	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);
}

/******************
function destruir_instalacao(evento, objeto)
--------------------
Função para chamar o AJAX de destruir instalação
objeto -- objeto sendo editado
******************/
function destruir_instalacao(evento, objeto, jogador=false, reparar=false) {
	let linha=pega_ascendente(objeto,"TR");
	let inputs=linha.getElementsByTagName("INPUT");
	let dados_ajax = "post_type=POST&action=destruir_instalacao";
	let desmantelar = "";
	
	if(objeto_em_edicao) {
		evento.preventDefault();
		return false;		
	}
	
	id_objeto = 0;
	for (let index = 0; index < inputs.length; index++) {
		if (inputs[index].getAttribute('data-atributo') == "id") {
			id_objeto = inputs[index].value;
		}
		if (jogador) {//Se o input veio do jogador, então está tentando DESMANTELAR a Instalação
			if (inputs[index].getAttribute('data-atributo') == "id_planeta_instalacoes") {
				id_objeto = inputs[index].value;
				if (!reparar) {
					desmantelar = "&desmantelar=true";
				}
			}
		}
	}
	
	dados_ajax = dados_ajax + "&id=" + id_objeto + desmantelar;
	
	let retorno = new Promise((resolve, reject) =>	{
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4) {
				objeto_em_edicao = false;
			}
			if (this.readyState == 4 && this.status == 200) {
				let resposta = "";
				try {
					resposta = JSON.parse(this.responseText);
				} catch(err) {
					console.log(err);
					reject(false);
				}
				
				if (resposta.debug != undefined) {
					console.log(resposta.debug);
				}				
				if (resposta.resposta_ajax == "OK!") {
					if (!jogador) {
						if (resposta[0].turno_destroi != "") {
							objeto.text = "Reparar Instalação";
						} else {
							objeto.text = "Destruir Instalação";
							resposta[0].turno_destroi = "&nbsp;";
						}
						let objeto_desabilitado = desabilita_edicao_objeto(objeto);
						let objeto_atualizado = atualiza_objeto(objeto_desabilitado,resposta[0]); //O objeto salvo está no array resposta[0]
					}
					resolve(true);
				} else {
					alert(resposta.resposta_ajax);
					resolve(false);
				}
			} else if (this.status == 500) {
				console.log(this.responseText);
				console.log(this.statusText);
				reject(false);
			}
		};
		xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send(dados_ajax);
	});

	objeto_em_edicao = true;
	evento.preventDefault();
	return retorno;
}

/******************
function desmonta_instalacao(evento, objeto, turno, jogador=false)
--------------------
Função para chamar o AJAX de desmontar uma instalação
objeto -- objeto sendo editado
******************/
function desmonta_instalacao(evento, objeto, turno, jogador=false, destruido=false) {
	let linha=pega_ascendente(objeto,"TR");
	let tabela=pega_ascendente(linha,"TABLE");
	let inputs = linha.getElementsByTagName("INPUT");
	let divs = linha.getElementsByTagName("DIV");
	let desativado = true;
	let dados = [];
	let id_objeto = 0;

	if(objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;		
	}
	
	
	for (let index=0; index<inputs.length; index++) {
		if (inputs[index].getAttribute("data-atributo") == "id_imperio" 
		|| inputs[index].getAttribute("data-atributo") == "id_planeta" 
		|| inputs[index].getAttribute("data-atributo") == "id_instalacao" 
		|| inputs[index].getAttribute("data-atributo") == "id_planeta_instalacoes"
		|| inputs[index].getAttribute("data-atributo") == "turno_destroi") {
			dados[inputs[index].getAttribute("data-atributo")] = inputs[index].value;
		} else if (inputs[index].getAttribute("data-atributo") == "nivel") {
			dados[inputs[index].getAttribute("data-atributo")] = inputs[index].value;
			input_nivel = inputs[index];
		}  else if (inputs[index].getAttribute("data-atributo") == "id") {
			id_objeto = inputs[index].value;
		}
		
		if (jogador) {
			if (inputs[index].getAttribute('data-atributo') == "id_planeta_instalacoes") {
				id_objeto = inputs[index].value;
			}
		}
	}
	
	let div_gerenciar = "";
	for (let index=0; index<divs.length; index++) {
		if (divs[index].getAttribute("data-atributo") == "id_imperio" 
		|| divs[index].getAttribute("data-atributo") == "id_planeta" 
		|| divs[index].getAttribute("data-atributo") == "id_instalacao" 
		|| divs[index].getAttribute("data-atributo") == "id_planeta_instalacoes"
		|| divs[index].getAttribute("data-atributo") == "turno_destroi") {
			dados[divs[index].getAttribute("data-atributo")] = divs[index].innerHTML;
		} else if (divs[index].getAttribute("data-atributo") == "nivel") {
			dados[divs[index].getAttribute("data-atributo")] = divs[index].innerHTML;
			input_nivel = divs[index];
		} else if (divs[index].getAttribute("data-atributo") == "id") {
			id_objeto = divs[index].innerHTML;
		}
		
		if (jogador) {
			if (divs[index].getAttribute('data-atributo') == "id_planeta_instalacoes") {
				id_objeto = divs[index].innerHTML;
			} else if (divs[index].getAttribute('data-atributo') == "pop") {
				let inputs_div_pop = divs[index].getElementsByTagName("INPUT");
				for (let index_input = 0; index_input < inputs_div_pop.length; index_input++) {
					if (inputs_div_pop[index_input].type == "checkbox" && inputs_div_pop[index_input].value != "1") {
						desativado = false;	
					} else if (inputs_div_pop[index_input].type == "range" && inputs_div_pop[index_input].value != "0") {
						desativado = false;
					}
				}
			}
			
			if (divs[index].getAttribute('data-atributo') == "gerenciar") {
				div_gerenciar = divs[index];
				console.log(div_gerenciar.style.visibility);
				div_gerenciar.style.visibility = "visible";
				console.log(div_gerenciar.style.visibility);
			}
		}
	}	

	if (!desativado) {
		alert("Só é possível desmantelar uma Instalação que esteja desativada!");
		div_gerenciar.style.visibility = "hidden";
		evento.preventDefault();
		return false;		
	}

	let confirma=confirm("AVISO!\nEsta ação é irreversível. Deseja continuar?");

	if (!confirma) {
		div_gerenciar.style.visibility = "hidden";
		
		evento.preventDefault();
		return false;		
	}

	if (dados["turno_destroi"] == "&nbsp;") {
		destruido = true;
	}

	let dados_ajax = "post_type=POST&action=desmonta_instalacao&upgrade_acao=true&id="+id_objeto+"&nivel="+dados['nivel']+"&tabela=colonization_planeta_instalacoes"
	+"&where_clause=id&where_value="+id_objeto+"&id_planeta="+dados['id_planeta']+"&id_instalacao="+dados['id_instalacao']+"&turno_desmonta="+turno;
	
	let link_destruir = objeto;

	if (!jogador) {
		link_destruir = document.getElementById('destruir_'+id_objeto).getElementsByTagName("A")[0];
	}
	
	let valida_dados = new Promise((resolve, reject) =>	{
		if (!destruido) {
			resolve(destruir_instalacao(evento, link_destruir, jogador));
		} else {
			resolve(true);
		}
	});
	
	valida_dados.then((successMessage) => {
		objeto_em_edicao = false;
		objeto_em_salvamento = false;
		if (successMessage) {
			let xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4) {
					objeto_em_edicao = false;
				}
				let resposta = "";
				if (this.readyState == 4 && this.status == 200) {
					try {
						resposta = JSON.parse(this.responseText);
					} catch(err) {
						console.log(err)
					}
					if (resposta.debug != undefined) {
						console.log(resposta.debug);
					}
					if (resposta.resposta_ajax == "SALVO!") {
						console.log(resposta);
						if (!jogador) {
							if (resposta[0].turno_desmonta != "") {
								objeto.style.visibility = "hidden";
							} else {
								resposta[0].turno_desmonta = "&nbsp;";
							}
							let objeto_desabilitado = desabilita_edicao_objeto(objeto);
							let objeto_atualizado = atualiza_objeto(objeto_desabilitado,resposta[0]); //O objeto salvo está no array resposta[0]
						} else {
							linha_acima = linha.previousElementSibling;
							do {
								if (linha_acima.cells[0].rowSpan > 1) {
									if (linha_acima.cells[0].getAttribute('data-atributo') == "dados_colonia") {
										linha_acima.cells[0].rowSpan = linha_acima.cells[0].rowSpan - 1;
									}
									break;
								}
								linha_acima = linha_acima.previousElementSibling
							} while(linha_acima != null)						
							linha.remove();
							document.location.reload();
						}
					} else {
						destruir_instalacao(evento, link_destruir, jogador);
						alert(resposta.resposta_ajax);
					}
				} else if (this.status == 500) {
					console.log(this.responseText);
					console.log(this.statusText);			
				}
			};
			xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhttp.send(dados_ajax);
		} else {
			div_gerenciar.style.visibility = "hidden";
		}
	});
	
	objeto_em_edicao = true;
	evento.preventDefault();
	return false;
}

/******************
function repara_instalacao(evento, objeto, turno, jogador=false)
--------------------
Função para chamar o AJAX de desmontar uma instalação
objeto -- objeto sendo editado
******************/
function repara_instalacao(evento, objeto) {
	let linha=pega_ascendente(objeto,"TR");
	let tabela=pega_ascendente(linha,"TABLE");
	let inputs = linha.getElementsByTagName("INPUT");
	let divs = linha.getElementsByTagName("DIV");
	let dados = [];
	let id_objeto = 0;

	if(objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;		
	}

	let link_destruir = objeto;
	
	let valida_dados = new Promise((resolve, reject) =>	{
		let jogador = true;
		let reparar = true;
		resolve(destruir_instalacao(evento, link_destruir, jogador, reparar));
	});
	
	valida_dados.then((successMessage) => {
		objeto_em_edicao = false;
		objeto_em_salvamento = false;
		if (successMessage) {
			objeto.style.visibility = "hidden";
			document.location.reload();
		}
	});

	valida_dados.catch((reason) => {
	   console.log("Ocorreu um erro desconhecido!");
	});
	
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
	let linha = pega_ascendente(objeto,"TR");
	
	//A primeira célula é especial, pois tem dois divs -- um com dados e outro com os links para Salvar e Excluir, que no modo edição são alterados para Salvar e Cancelar
	let celula = linha.cells[0]
	let divs = celula.getElementsByTagName("DIV");
	divs[1].innerHTML = "<a href='#' onclick='return edita_objeto(event, this);'>Editar</a>";
}

/******************
function mais_dados_imperio(objeto) 
--------------------
Pega dados adicionais do Império
objeto -- objeto sendo editado
******************/	
function mais_dados_imperio(objeto, cancela=false) {
	let linha = pega_ascendente(objeto,"TR");
	let inputs = linha.getElementsByTagName("INPUT");
	let dados_ajax = "post_type=POST&action=dados_imperio";

	let id_objeto = "";
	for (let index = 0; index < inputs.length; index++) {
		if (inputs[index].getAttribute('data-atributo') == "id") {
			id_objeto = inputs[index].value;
		}
	}
	
	dados_ajax = dados_ajax + "&id=" + id_objeto;
	
	let xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4) {
			objeto_em_edicao = false;
		}
		if (this.readyState == 4 && this.status == 200) {
			let resposta = JSON.parse(this.responseText);
			if (resposta.resposta_ajax == "OK!") {
				let objeto_desabilitado = desabilita_edicao_objeto(objeto);
				let objeto_atualizado = atualiza_objeto(objeto_desabilitado,resposta[0]); //O objeto salvo está no array resposta[0]
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
function altera_imperio_colonia(objeto) 
--------------------
Altera a colônia para outro Império
objeto -- objeto sendo editado
******************/	
function altera_imperio_colonia(objeto, cancela=false) {
	let linha = pega_ascendente(objeto,"TR");
	let tabela = pega_ascendente(objeto,"TABLE");
	let inputs = linha.getElementsByTagName("INPUT");
	let selects = linha.getElementsByTagName("SELECT");
	let divs = linha.getElementsByTagName("DIV");

	for (let index=0; index<divs.length; index++) {
		if (divs[index].getAttribute('data-atributo') == "nome_imperio") {
			if (divs[index].getAttribute('data-id-selecionado') != tabela.getAttribute('data-id-imperio')) { //Mudou de Império!
				let id_novo_imperio = divs[index].getAttribute('data-id-selecionado');
				let id_antigo_imperio = tabela.getAttribute('data-id-imperio');
				let tabela_novo_imperio = document.getElementsByTagName('TABLE');
			
				//Determina qual tabela (ou seja, qual Império) está sendo editado
				for (let index_tabelas = 0; index_tabelas < tabela_novo_imperio.length; index_tabelas++) {
					if (tabela_novo_imperio[index_tabelas].getAttribute('data-id-imperio') == id_novo_imperio) {
						tabela_novo_imperio = tabela_novo_imperio[index_tabelas];
						break;
					}
				}
				
				//Primeiro verifica se está indo de um NPC para um PC. Nese caso tem que alterar a estrutura da linha
				if (id_novo_imperio == 0) {
					linha.insertCell(1);
				} else if (id_antigo_imperio == 0) {
					linha.deleteCell(1);
				}
				
				//Copia a linha para a tabela do novo Império
				let linha_nova = tabela_novo_imperio.insertRow(-1);
				linha_nova.innerHTML = linha.innerHTML;
				
				//E remove da tabela antiga
				linha.parentNode.removeChild(linha);
			}
		}
	}
}

/******************
function muda_nome_colonia(id_planeta, evento) 
--------------------
Muda o nome de uma Colônia (no caso, de um planeta)
id_planeta = id do planeta sendo editado
******************/	
function muda_nome_colonia(id_planeta, evento) {
	let confirma = confirm('Tem certeza que deseja mudar o nome desse Planeta?');
	let novo_nome = "";
	
	if (confirma) {
		novo_nome = prompt('Qual será o novo nome do Planeta?');
	} else {
		evento.preventDefault();
		return false;
	}
	
	if (novo_nome !== null && novo_nome != "") {
		//novo_nome = encodeURIComponent(novo_nome);
		let dados_ajax = "post_type=POST&action=muda_nome_colonia&id_planeta=" + id_planeta + "&novo_nome=" + encodeURIComponent(novo_nome);
		let resposta = processa_xhttp_basico(dados_ajax);
		resposta.then((successMessage) => {
			objeto_em_edicao = false;
			objeto_em_edicao = false;
			if (successMessage) {
				document.location.reload();
			}
		});		
	}

	evento.preventDefault();
	return false;	
}

/******************
function muda_nome_nave(objeto) 
--------------------
Muda o nome de uma Nave
id_nave = id da nave
******************/	
function muda_nome_nave(id_nave, evento) {
	let confirma = confirm('Tem certeza que deseja mudar o nome dessa Nave?');
	let novo_nome = "";
	
	if (confirma) {
		novo_nome = prompt('Qual será o novo nome do Nave?');
	} else {
		evento.preventDefault();
		return false;
	}
	
	if (novo_nome !== null && novo_nome != "") {
		//novo_nome = encodeURIComponent(novo_nome);
		let dados_ajax = "post_type=POST&action=muda_nome_nave&id=" + id_nave + "&novo_nome=" + encodeURIComponent(novo_nome);
		let resposta = processa_xhttp_basico(dados_ajax);
		resposta.then((successMessage) => {
			objeto_em_edicao = false;
			objeto_em_edicao = false;
			if (successMessage) {
				document.location.reload();
			}
		});		
	}

	evento.preventDefault();
	return false;	
}

/******************
function tirar_cerco(objeto, evento, id_estrela, coloca_cerco=false) 
--------------------
Tira uma Estrela do Cerco
******************/	
function tirar_cerco(objeto, evento, id_estrela, coloca_cerco = false) {
	let dados_ajax = "post_type=POST&action=tirar_cerco&id_estrela=" + id_estrela + "&coloca_cerco=" + coloca_cerco;
	let div_parent = pega_ascendente(objeto, "DIV");
	if (coloca_cerco) {
		if (div_parent.previousElementSibling != null || div_parent.firstChild.tagName == "A") {
			evento.stopImmediatePropagation();
			evento.stopPropagation();
			evento.preventDefault();
			return false;		
		}
	}
	
	let resposta = processa_xhttp_basico(dados_ajax);
	resposta.then((successMessage) => {
		objeto_em_edicao = false;
		objeto_em_edicao = false;
		if (successMessage) {
			if (coloca_cerco) {
				let icone_cerco = document.createElement("A");
				icone_cerco.href = "#";
				icone_cerco.innerHTML = "<div class='fas fa-bell-on tooltip' style='display: inline;'><span class='tooltiptext'>Sistema sob ataque!</span>&nbsp;</div>";
				icone_cerco.addEventListener("click", function () {tirar_cerco(this,event,id_estrela)});
				div_parent.insertBefore(icone_cerco, div_parent.firstChild);
			} else {
				objeto.remove();
			}
		}
	});		

	evento.stopImmediatePropagation();
	evento.stopPropagation();
	evento.preventDefault();
	return false;
}

/******************
function muda_nome_nave(objeto) 
--------------------
Muda o nome de uma Nave
id_nave = id da nave
******************/	
function coloniza_planeta (objeto, evento, id_planeta, id_imperio) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
	
	let confirma = confirm('Tem certeza que deseja Colonizar esse planeta?');
	let novo_nome = "";
	
	if (!confirma) {
		evento.preventDefault();
		return false;
	}

	objeto_em_edicao = true;	
	let dados_ajax = "post_type=POST&action=coloniza_planeta&id_planeta=" + id_planeta + "&id_imperio=" + id_imperio;
	let resposta = processa_xhttp_basico(dados_ajax);
	resposta.then((successMessage) => {
		objeto_em_edicao = false;
		objeto_em_edicao = false;
		if (successMessage) {
			document.location.reload();
			//alert("Salvou!");
		}
	});		

	evento.preventDefault();
	return false;	
}

/******************
function remove_aviso()
--------------------
Remove um aviso
******************/
function remove_aviso (objeto, evento, id) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
	
	objeto_em_edicao = true;
	
	let div_notice = objeto;
	
	try {
		while (div_notice.getAttribute("class") != "notice") {
			div_notice = div_notice.parentNode;
		} 
	}
	catch (err) {
		div_notice.remove = function () {return;};
	}

	let div_notice_panel = div_notice;
	try {
		while (div_notice_panel.getAttribute("class") != "notices-panel") {
			div_notice_panel = div_notice.parentNode;
		} 
	}
	catch (err) {
		div_notice_panel.childElementCount = false;
	}


	let dados_ajax = "post_type=POST&action=remove_aviso&id=" + id;
	let resposta = processa_xhttp_basico(dados_ajax);
	resposta.then((successMessage) => {
		objeto_em_edicao = false;
		objeto_em_edicao = false;
		if (successMessage) {
			objeto_em_edicao = false;
			div_notice.remove();
			if (div_notice_panel.childElementCount == 0) {
				div_notice_panel.remove();
			}				
		}
	});		

	evento.preventDefault();
	return false;
}

/******************
function ativa_anti_dobra(id=0)
--------------------
Ativa o campo anti_dobra em um sistema
******************/
function ativa_anti_dobra(objeto, evento, id_estrela, id_nave = 0) {
	let confirma = confirm("Esta ação afeta TODAS as naves do sistema, incluindo as suas.\n O efeito anti-dobra só será liberado no final do Turno. Deseja continuar?");
	
	if (!confirma) {
		evento.preventDefault();
		return false;
	}
	
	let retorno = new Promise((resolve, reject) => {
		let dados_ajax = "post_type=POST&action=ativa_anti_dobra&id_estrela="+id_estrela+"&id_nave="+id_nave;
		resolve(processa_xhttp_basico(dados_ajax));
	});
	
	retorno.then((successMessage) => {
		objeto_em_edicao = false;
		objeto_em_edicao = false;
		if (successMessage) {
			document.location.reload();
		}
	});

	evento.preventDefault();
	return false;
}

/******************
function criar_pop(evento, objeto, id_colonia, tipo_pop)
--------------------
Cria Pop em uma Colônia específica
******************/
function criar_pop(evento, objeto, id_colonia, tipo_pop) {
	let input_qtd = objeto.previousElementSibling;
	if (input_qtd.value > 0) {
		let consumo_texto = "";
		if (tipo_pop == "droids") {
			consumo_texto = input_qtd.value*10 + " Industrializáveis";
		} else if (tipo_pop == "pop") {
			consumo_texto = input_qtd.value*100 + " Alimentos";
		}
		let confirma = confirm("Esta ação irá consumir "+consumo_texto+". Deseja continuar?");
		
		if (!confirma) {
			evento.preventDefault();
			return false;			
		}
		
		let retorno = new Promise((resolve, reject) => {
			let dados_ajax = "post_type=POST&action=criar_pop&id_colonia="+id_colonia+"&tipo_pop="+tipo_pop+"&qtd="+input_qtd.value;
			resolve(processa_xhttp_basico(dados_ajax));
		});
		
		retorno.then((successMessage) => {
			objeto_em_edicao = false;
			objeto_em_edicao = false;
			if (successMessage) {
				document.location.reload();
			}
		});
		retorno.catch((error) => {
			console.error(error);
		});
	}

	evento.preventDefault();
	return false;
}

/******************
function ativa_anti_dobra(id=0)
--------------------
Desativa o Campo Anti-Dobra afetando uma nave
******************/
function desativa_anti_dobra(objeto, evento, id_nave) {
	let retorno = new Promise((resolve, reject) => {
		let dados_ajax = "post_type=POST&action=ativa_anti_dobra&id_nave="+id_nave;
		resolve(processa_xhttp_basico(dados_ajax));
	});
	
	retorno.then((successMessage) => {
		objeto_em_edicao = false;
		objeto_em_edicao = false;
		if (successMessage) {
			alert('Nave liberada!');
			objeto.remove();
		}
	});

	evento.preventDefault();
	return false;
}