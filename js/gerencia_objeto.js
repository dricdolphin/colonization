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
		return;
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
	var linha=pega_ascendente(objeto,"TR");;
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
	
	var linha = pega_ascendente(objeto,"TR");;
	var celulas = linha.getElementsByTagName("TD");
	var inputs = [];
	
	var linha_nova = tabela.insertRow(-1);
	
	var celula = linha_nova.insertCell(0); //A primeira célula é diferente!
	var nome = celulas[0].getElementsByTagName("DIV");
	nome = nome[0].innerHTML;
	
	celula.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='"+id_imperio+"' value='"+id_imperio+"'></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>"
	+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir esta Frota?'></input>"
	+"<div data-atributo='nome' data-editavel='true' data-valor-original='' data-style='width: 100px;'>"+nome+"</div>"
	+"<div><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	
	for (var index = 1; index < celulas.length; index++) {
		celula = linha_nova.insertCell(index);
		celula.innerHTML = celulas[index].innerHTML;
		inputs = celula.getElementsByTagName("INPUT");
		
		/*
		for (var index_input = 0; index_input < inputs.length; index_input++) {
			if (inputs[index_input].getAttribute('data-atributo') == "id" || inputs[index_input].getAttribute('data-atributo') == "where_value") {
				inputs[index_input].value = "";
				inputs[index_input].setAttribute('data-valor-original',"");
			}
		}*/
	}

	
	var retorno = edita_objeto(evento, celula);
	
	celulas = linha_nova.getElementsByTagName("TD");
	celulas[0].innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='"+id_imperio+"' value='"+id_imperio+"'></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>"
	+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir esta Frota?'></input>"
	+"<div data-atributo='nome' data-editavel='true' data-valor-original='' data-style='width: 100px;'><input type='text' data-atributo='nome' data-ajax='true' style='width: 100px;' value='"+nome+"'></input></div>"
	+"<div><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	
	evento.preventDefault();
	return false;
}

/******************
function desbloquear_turno()
--------------------
Desbloqueia o Turno
******************/
function desbloquear_turno(evento, objeto) {
	var confirma = confirm("Tem certeza que deseja desbloquear o Turno?");
	
	if (confirma) {
		var dados_ajax = "post_type=POST&action=libera_turno";
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var resposta = JSON.parse(this.responseText);
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
		xhttp.open("POST", ajaxurl, false); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
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
function processa_recebimento_tech()
--------------------
Aceita ou rejeita o recebimento de uma Tech
******************/
function processa_recebimento_tech (objeto, evento, id_tech_transf, autoriza) {
	let div_notice = objeto;
	
	while (div_notice.getAttribute("class") != "notice") {
		div_notice = div_notice.parentNode;
	}

	let div_notice_panel = div_notice;
	while (div_notice_panel.getAttribute("class") != "notices-panel") {
		div_notice_panel = div_notice.parentNode;
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
			var resposta = JSON.parse(this.responseText);
			if (resposta.resposta_ajax == "SALVO!") {
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

	div_notice.remove();
	if (div_notice_panel.childElementCount == 0) {
		div_notice_panel.remove();
	}
		
	let envia_tech = document.getElementById("envia_tech");
	if (envia_tech !== undefined) {
		atualiza_lista_techs(envia_tech);
	}
	
	evento.preventDefault();
	return false;
}