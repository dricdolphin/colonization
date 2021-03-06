/******************
function inclui_recurso(evento, id_recurso)
--------------------
Inclui um recurso na lista_recursos_qtd
******************/
function inclui_recurso(evento, id_recurso) {
	var inputs = document.getElementsByTagName("INPUT");
	var div_lista_recursos_qtd = "";
	var inputs_text = [];
	var div_recurso = "";
	var div_recurso_parent = "";
	var div_recurso_parent_html_original = "";
	var input_lista_recursos = "";
	var input_qtd = "";

	if (!objeto_em_edicao) {
		
		evento.preventDefault();
		return false;
	}

	var index_input_text = 0;
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
	if(input_lista_recursos.value !== undefined) {
		var lista_lista_recursos = input_lista_recursos.value.split(";");
		var lista_qtds = input_qtd.value.split(";");
	}
	
	for (let index=0; index<lista_lista_recursos.length; index++) {
		if (lista_lista_recursos[index] == id_recurso) {
			//inputs_text[index].style.backgroundColor = "#DDDDDD";
			div_recurso = inputs_text[index].parentNode.parentNode;
			if (inputs_text[index].value != 0 && inputs_text[index].value != "") {
				var resposta = confirm("Deseja mesmo remover o recurso "+lista_recursos[id_recurso]+"?");
			} else {
				var resposta = true;
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
	var linha=pega_ascendente(objeto,"TR");
	var inputs=linha.getElementsByTagName("INPUT");
	
	for (var index = 0; index < inputs.length; index++) {
		if (inputs[index].getAttribute('data-atributo') == "id") {
			var id_objeto = inputs[index].value;
		}
	}
	
	var vars = window.location.href.split("?");
	
	if (redireciona == "") {
		redireciona = vars[1];
	}
	
	var url_gerencia = vars[0]+"?"+redireciona+"&id="+id_objeto;
	url_gerencia = url_gerencia.replace("#","");
	window.location = url_gerencia;
	
	evento.preventDefault();
	return false;
}

/******************
function copia_objeto(objeto)
--------------------
Copia um objeto na última linha
objeto -- objeto sendo editado
******************/
function copiar_objeto(evento, objeto, id_imperio) {
	var tabela = document.getElementsByTagName('TABLE');
	
	for (var index_tabelas = 0; index_tabelas < tabela.length; index_tabelas++) {
		if (tabela[index_tabelas].getAttribute('data-id-imperio') == id_imperio) {
			tabela = tabela[index_tabelas];
			break;
		}
	}
	
	var linha = pega_ascendente(objeto,"TR");
	var celulas = linha.getElementsByTagName("TD");
	var inputs = [];
	
	var linha_nova = tabela.insertRow(-1);
	
	for (let index = 0; index < celulas.length; index++) {
		celula = linha_nova.insertCell(index);
		celula.innerHTML = celulas[index].innerHTML;
	}

	var retorno = edita_objeto(evento, celula);

	celulas = linha_nova.getElementsByTagName("TD");

	inputs = celulas[0].getElementsByTagName("INPUT");
	ahrefs = celulas[0].getElementsByTagName("A");
	

	for (let index_input = 0; index_input < inputs.length; index_input++) {
		if (inputs[index_input].getAttribute('data-atributo') == "id" || inputs[index_input].getAttribute('data-atributo') == "where_value") {
			inputs[index_input].value = "";
			inputs[index_input].setAttribute('data-valor-original',"");
		}
	}
	for (let index_anchors = 0; index_anchors < ahrefs.length; index_anchors++) {
		if (ahrefs[index_anchors].text == "Cancelar" ) {
			ahrefs[index_anchors].setAttribute('onclick','return cancela_edicao(event, this);');
		}
	}		
	
	evento.preventDefault();
	return false;
}

/******************
function desbloquear_turno()
--------------------
Desbloqueia o Turno
******************/
function desbloquear_turno(evento, objeto) {
	if (objeto_em_salvamento) {
		
		evento.preventDefault();
		return false;
	}
	
	objeto_em_salvamento = true;
	
	var confirma = confirm("Tem certeza que deseja desbloquear o Turno?");
	
	if (confirma) {
		var dados_ajax = "post_type=POST&action=libera_turno";
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var resposta = JSON.parse(this.responseText);
				
				objeto_em_salvamento = false;
				
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
function atualiza_lista_techs()
--------------------
Atualiza a lista de Techs enviadas e recebidas
******************/
function atualiza_lista_techs(objeto) {
		var dados = pega_dados_objeto(objeto);//Pega os dados do objeto
		id_imperio=dados['id_imperio_origem'].value;
		id_tech=dados['id_tech'].value;

		var dados_ajax = "post_type=POST&action=dados_transfere_tech&id_imperio="+id_imperio;
		console.log(dados_ajax);
		var xhttp_salvou = new XMLHttpRequest();
		xhttp_salvou.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var resposta = JSON.parse(this.responseText);
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
	if (objeto_em_salvamento) {
		
		evento.preventDefault();
		return false;
	}
	
	objeto_em_salvamento = true;
	
	var dados_ajax = "post_type=POST&action=nave_visivel&id="+id_nave;
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
	if (objeto_em_salvamento) {
		
		evento.preventDefault();
		return false;
	}
	
	objeto_em_salvamento = true;
	
	let linha = pega_ascendente(objeto,"TR");
	let divs = linha.getElementsByTagName("DIV");
	
	for (let index=0; index < divs.length; index++) {
		if (divs[index].getAttribute('data-atributo') == "nome_estrela") {
			for (let index_child=0; index_child<divs[index].childNodes.length; index_child++) {
				if (divs[index].childNodes[index_child].tagName == "SELECT") {
					var id_estrela = divs[index].childNodes[index_child].value;
					divs[index].childNodes[index_child].disabled = true;
				}
			}
		}
	}
	
	var dados_ajax = "post_type=POST&action=envia_nave&id="+id_nave+"&id_estrela="+id_estrela;
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
function upgrade_instalacao()
--------------------
Faz o upgrade de uma Instalação
******************/
function upgrade_instalacao(evento,objeto,nivel_maximo=0) {
	if (objeto_em_salvamento) {
		
		alert('Já existe um objeto em edição!');
		evento.preventDefault();
		return false;
	}
	
	objeto_em_salvamento = true;

	let linha = pega_ascendente(objeto,"TR");
	let inputs = linha.getElementsByTagName("INPUT");
	let labels = linha.getElementsByTagName("LABEL");

	let label_mk = objeto.parentNode;

	let input_nivel = {};
	label_mk = label_mk.getElementsByTagName("LABEL")[0];
	
	var dados = [];
	
	
	for (let index=0; index<inputs.length; index++) {
		if (inputs[index].getAttribute("data-atributo") == "id_imperio" || inputs[index].getAttribute("data-atributo") == "id_planeta" || inputs[index].getAttribute("data-atributo") == "id_instalacao" || inputs[index].getAttribute("data-atributo") == "id_planeta_instalacoes") {
			dados[inputs[index].getAttribute("data-atributo")] = inputs[index].value;
		} else if (inputs[index].getAttribute("data-atributo") == "nivel") {
			dados[inputs[index].getAttribute("data-atributo")] = inputs[index].value;
			input_nivel = inputs[index];
		} else if (inputs[index].getAttribute("data-atributo") == "pop" ) {
			var input_pop = inputs[index];
		}
	}

	for (let index=0; index<labels.length; index++) {
		if (labels[index].getAttribute("data-atributo") == "pop") {
			var label_pop = labels[index];
		}
	}
	
	var nivel_upgrade = dados['nivel']*1 + 1;
	
	var dados_ajax = "post_type=POST&action=valida_colonia_instalacao&upgrade_acao=true&id="+dados['id_planeta_instalacoes']+"&nivel="+nivel_upgrade+"&tabela=colonization_planeta_instalacoes"
	+"&where_clause=id&where_value="+dados['id_planeta_instalacoes']+"&id_planeta="+dados['id_planeta']+"&id_instalacao="+dados['id_instalacao']+"&id_imperio="+dados['id_imperio'];

	//***
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
			
			if (resposta.resposta_ajax == "SALVO!") {
				retorno = true;
				//Remove o objeto e ATUALIZA a Instalação
				switch(nivel_upgrade) {
					case 1:
						html_nivel = "Mk I";
						break;
					case 2:
						html_nivel = "Mk II";
						break;
					case 3:
						html_nivel = "Mk III";
						break;
					case 4:
						html_nivel = "Mk IV";
						break;
					case 5:
						html_nivel = "Mk V";
						break;
					case 6:
						html_nivel = "Mk VI";
						break;
					case 7:
						html_nivel = "Mk VII";
						break;
					case 8:
						html_nivel = "Mk VIII";
						break;
					case 9:
						html_nivel = "Mk IX";
						break;
					case 10:
						html_nivel = "Mk X";
						break;
					default:
						html_nivel = "";
					}
				
				label_mk.innerHTML = html_nivel;
				input_nivel.value = nivel_upgrade;
				let id_recursos_atuais = "recursos_atuais_imperio_"+dados['id_imperio'];
				let div_recursos_atuais = document.getElementById(id_recursos_atuais);
				div_recursos_atuais.innerHTML = resposta.recursos_atuais;
				
				let fator =  ((nivel_upgrade-1)/(nivel_upgrade));
				
				if (input_pop != undefined) {
					input_pop.value = Math.floor(input_pop.value*fator);
					label_pop.innerHTML = input_pop.value;
				}

				if (nivel_upgrade >= nivel_maximo) {
					objeto.style.display='none';
				}
				
				objeto_em_salvamento = false;
			} else {
				alert(resposta.resposta_ajax);
				retorno = false;
			}
			
			objeto_em_salvamento = false;
			
			if (resposta.debug != undefined) {
				console.log(resposta.debug);
			}
		
		}
	};
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);	
	//***/
	
	evento.preventDefault();
	return false;	
}

function alerta(texto) {
	alert(texto);
}

/******************
function aceita_missao()
--------------------
Aceita ou rejeita uma missão
******************/
function aceita_missao (objeto, evento, id_imperio, id_missao, aceita=true) {
	if (objeto_em_salvamento) {
		
		evento.preventDefault();
		return false;
	}
	
	objeto_em_salvamento = true;	
	
	let div_notice = objeto;
	
	while (div_notice.getAttribute("data-atributo") != "gerenciar") {
		div_notice = div_notice.parentNode;
	}
	
	var dados_ajax = "post_type=POST&action=aceita_missao&id="+id_missao+"&id_imperio="+id_imperio+"&aceita="+aceita;
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
	if (objeto_em_salvamento) {
		
		evento.preventDefault();
		return false;
	}
	
	objeto_em_salvamento = true;	
	
	let div_notice = objeto;
	
	while (div_notice.getAttribute("class") != "notice") {
		div_notice = div_notice.parentNode;
	}

	let div_notice_panel = div_notice;
	while (div_notice_panel.getAttribute("class") != "notices-panel") {
		div_notice_panel = div_notice.parentNode;
	}

	var dados_ajax = "post_type=POST&action=processa_viagem_nave&id="+id_nave;
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			try {
				var resposta = JSON.parse(this.responseText);
			} 
			catch (err) {
				console.log(this.responseText);
				return false;
			}
			
			objeto_em_salvamento = false;
			
			if (resposta.resposta_ajax == "SALVO!") {
				div_notice.remove();
				if (div_notice_panel.childElementCount == 0) {
					div_notice_panel.remove();
				}
					
				let envia_tech = document.getElementById("envia_tech");
				if (envia_tech !== undefined && envia_tech !== null) {
					atualiza_lista_techs(envia_tech);
				}
			} else {
				alert(resposta.resposta_ajax);
			}
			
			if (resposta.debug != undefined) {
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
function processa_recebimento_tech()
--------------------
Aceita ou rejeita o recebimento de uma Tech
******************/
function processa_recebimento_tech (objeto, evento, id_tech_transf, autoriza) {
	if (objeto_em_salvamento) {
		
		evento.preventDefault();
		return false;
	}
	
	objeto_em_salvamento = true;
	
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
	var dados_ajax = "post_type=POST&action=processa_recebimento_tech&tabela=colonization_imperio_transfere_techs&where_clause=id&where_value="+id_tech_transf+"&id="+id_tech_transf+"&processado=1&autorizado="+autoriza;
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
function encerra_turno
--------------------
Encerra o Turno
--------
******************/
function encerra_turno(evento, objeto) {
	var div_resultados = document.getElementById('resultado_turno');
	div_resultados.innerHTML = "Encerrando o Turno...";
	var dados_ajax = "post_type=POST&action=encerra_turno";
	
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			try {
				var resposta = JSON.parse(this.responseText);
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
	var div_resultados = document.getElementById('resultado_turno');
	div_resultados.innerHTML = "Processando o Turno, aguarde!";
	var dados_ajax = "post_type=POST&action=roda_turno";
	
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
			
			let div_resultados = document.getElementById('resultado_turno');
			div_resultados.innerHTML = resposta.html;
			if (resposta.turno_novo != '') {
				let div_turno = document.getElementById('div_turno');
				let div_dados_acoes_imperios = document.getElementById('dados_acoes_imperios');
				div_dados_acoes_imperios.innerHTML = resposta.dados_acoes_imperios;
				div_turno.innerHTML = resposta.turno_novo;
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