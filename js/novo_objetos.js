/******************
function novo_imperio
--------------------
Insere um novo Império na lista
******************/
function novo_imperio(evento) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
		
	objeto_em_edicao = true; //Bloqueia a edição de outros Impérios
	var tabela_imperios = document.getElementsByTagName('TABLE')[0];
	var linha_nova = tabela_imperios.insertRow(-1);
	
	var id = linha_nova.insertCell(-1);
	var dados_jogador = linha_nova.insertCell(-1);
	var nome_imperio = linha_nova.insertCell(-1);
	var prestigio = linha_nova.insertCell(-1);
	var populacao = linha_nova.insertCell(-1);
	var pontuacao = linha_nova.insertCell(-1);
	var gerencia = linha_nova.insertCell(-1);
	
	var lista_jogadores = lista_jogadores_html(); //Pega a lista de usuários do Fórum
	
	
	id.innerHTML = "<input type='hidden' data-atributo='id_jogador' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id_jogador'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_imperio'></input>"
	+"<input type='hidden' data-atributo='funcao_pos_processamento' value='mais_dados_imperio'></input>"
	+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value=''></input>"
	+"<div data-atributo='id' data-valor-original=''>#</div>"
	+"<div data-atributo='gerenciar'><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	dados_jogador.innerHTML = "<div data-atributo='nome_jogador' data-id-selecionado='0'>"+lista_jogadores+"</div>";
	nome_imperio.innerHTML = "<div data-atributo='nome' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nome' data-ajax='true'></input></div>";
	prestigio.innerHTML = "<div data-atributo='prestigio' data-editavel='true' data-valor-original='' data-style='width: 40px;'><input type='text' data-atributo='prestigio' data-ajax='true' style='width: 40px;'></input></div>";
	populacao.innerHTML = "<div data-atributo='pop' data-valor-original=''></div>";
	pontuacao.innerHTML = "<div data-atributo='pontuacao' data-valor-original=''></div>";
	gerencia.innerHTML = "<div data-atributo='gerenciar' data-valor-original=''><a href='#' onclick='return gerenciar_objeto(event, this);' style='visibility: hidden;'>Gerenciar Objeto</a></div>";

	evento.preventDefault();
	return false;
}

/******************
function nova_estrela
--------------------
Insere uma nova estrela na lista
******************/
function nova_estrela(evento) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
		
	objeto_em_edicao = true; //Bloqueia a edição de outros Impérios
	var tabela_estrelas = document.getElementsByTagName('TABLE')[0];
	var linha_nova = tabela_estrelas.insertRow(-1);
	
	var id = linha_nova.insertCell(-1);
	var nome = linha_nova.insertCell(-1);
	var descricao = linha_nova.insertCell(-1);
	var estrela_x = linha_nova.insertCell(-1);
	var estrela_y = linha_nova.insertCell(-1);
	var estrela_z = linha_nova.insertCell(-1);
	var estrela_tipo = linha_nova.insertCell(-1);
	var gerencia = linha_nova.insertCell(-1);
	
	id.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_estrela'></input>"
	+"<div data-atributo='id' data-valor-original=''>#</div>"
	+"<div data-atributo='gerenciar'><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	nome.innerHTML = "<div data-atributo='nome' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nome' data-ajax='true'></input></div>";
	descricao.innerHTML = "<div data-atributo='descricao' data-type='textarea' data-editavel='true' data-valor-original='' data-style='width: 190px; height: 50px;' data-id='descricao'>"
	+"<textarea data-atributo='descricao' data-ajax='true' style='width: 190px; height: 50px;' id='descricao'></textarea></div>";
	estrela_x.innerHTML = "<div data-atributo='X' data-style='width: 100%;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='X' data-ajax='true' style='width: 100%;'></input></div>";
	estrela_y.innerHTML = "<div data-atributo='Y' data-style='width: 100%;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='Y' data-ajax='true' style='width: 100%;'></input></div>";
	estrela_z.innerHTML = "<div data-atributo='Z' data-style='width: 100%;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='Z' data-ajax='true' style='width: 100%;'></input></div>";
	estrela_tipo.innerHTML = "<div data-atributo='tipo' data-editavel='true' data-valor-original=''><input type='text' data-atributo='tipo' data-ajax='true'></input></div>";
	gerencia.innerHTML = "<div data-atributo='gerenciar' data-valor-original=''><a href='#' onclick='return gerenciar_objeto(event, this);' style='visibility: hidden;'>Gerenciar Objeto</a></div>";

	evento.preventDefault();
	return false;
}

function novo_reabastecimento(evento) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault()
		return false;
	}
		
	objeto_em_edicao = true; //Bloqueia a edição de outros objetos

	var tabela = document.getElementsByTagName('TABLE')[0];
	var linha_nova = tabela.insertRow(-1);
	
	var id = linha_nova.insertCell(-1);
	var imperio = linha_nova.insertCell(-1);
	var estrela = linha_nova.insertCell(-1);

	var lista_imperios = lista_imperios_html();
	var lista_estrelas = lista_estrelas_html();

	id.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_reabastecimento'></input>"
	+"<div data-atributo='id' data-valor-original='' value=''>#</div>"
	+"<div data-atributo='gerenciar'><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";

	imperio.innerHTML = "<div data-atributo='nome_imperio' data-editavel='true' data-type='select' data-funcao='lista_imperios_html' data-id-selecionado=''>"+lista_imperios+"</div>";
	estrela.innerHTML = "<div data-atributo='nome_estrela' data-editavel='true' data-type='select' data-funcao='lista_estrelas_html' data-id-selecionado=''>"+lista_estrelas+"</div>";

	event.preventDefault();
	return false;
}


/******************
function novo_planeta
--------------------
Insere um novo Planeta na lista
id_estrela -- id da estrela (caso esteja adicionando planetas dentro de um sistema estelar específico)
******************/
function novo_planeta(evento, id_estrela = 0) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault()
		return false;
	}
		
	objeto_em_edicao = true; //Bloqueia a edição de outros objetos
	var tabela = document.getElementsByTagName('TABLE')[0];
	var linha_nova = tabela.insertRow(-1);
	var id = linha_nova.insertCell(-1);
	var nome = linha_nova.insertCell(-1);
	var estrela = linha_nova.insertCell(-1);
	var posicao = linha_nova.insertCell(-1);
	var classe = linha_nova.insertCell(-1);
	var subclasse = linha_nova.insertCell(-1);
	var tamanho = linha_nova.insertCell(-1);
	var inospito = linha_nova.insertCell(-1);
	var gerencia = linha_nova.insertCell(-1);
	var link_gerenciamento = "\"page=colonization_admin_planetas\"";
	
	if (id_estrela == 0) {
		var lista_estrelas = lista_estrelas_html();
	} else {
		var lista_estrelas = lista_estrelas_html(id_estrela);
		link_gerenciamento = "\"page=colonization_admin_planetas&id_estrela="+id_estrela+"\"";
	}
	
	id.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='id_estrela' value='"+id_estrela+"'></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>"
	+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir este planeta e todas suas ligações (recursos, instalações etc)?'></input>"
	+"<div data-atributo='id' data-valor-original='' value=''>#</div>"
	+"<div data-atributo='gerenciar'><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	nome.innerHTML = "<div data-atributo='nome' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nome' data-ajax='true'></input></div>";
	estrela.innerHTML = "<div data-atributo='nome_estrela' data-id-selecionado='"+id_estrela+"'>"+lista_estrelas+"</div>";
	posicao.innerHTML = "<div data-atributo='posicao' data-style='width: 30px;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='posicao' data-ajax='true' style='width: 30px;'></input></div>";
	classe.innerHTML = "<div data-atributo='classe' data-editavel='true' data-valor-original=''><input type='text' data-atributo='classe' data-ajax='true'></input></div>";
	subclasse.innerHTML = "<div data-atributo='subclasse' data-editavel='true' data-valor-original=''><input type='text' data-atributo='subclasse' data-ajax='true'></input></div>";
	tamanho.innerHTML = "<div data-atributo='tamanho' data-style='width: 30px;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='tamanho' data-ajax='true' style='width: 30px;'></input></div>";
	inospito.innerHTML = "<div data-atributo='inospito' data-type='checkbox' data-editavel='true' data-valor-original='1'><input type='checkbox' data-atributo='inospito' data-ajax='true' checked></input></div>";
	gerencia.innerHTML = "<div data-atributo='gerenciar' data-valor-original=''><a href='#' onclick='return gerenciar_objeto(event, this,"+link_gerenciamento+");' style='visibility: hidden;'>Gerenciar Objeto</a></div>";
	
	if (id_estrela != 0) {
		var selects = estrela.getElementsByTagName("select");
		estrela.innerHTML = "<input type='hidden' data-atributo='id_estrela' data-ajax='true' value='"+id_estrela+"'></input><div data-atributo='nome_estrela' data-valor-original='"+selects[0].options[selects[0].selectedIndex].innerHTML+"'>"+selects[0].options[selects[0].selectedIndex].innerHTML+"</div>";
	}
	
	evento.preventDefault();
	return false;
}


/******************
function nova_configuracao
--------------------
Insere uma nova configuração
******************/
function nova_configuracao(evento) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault()
		return false;
	}
		
	objeto_em_edicao = true; //Bloqueia a edição de outros objetos
	var tabela = document.getElementsByTagName('TABLE')[0];
	var linha_nova = tabela.insertRow(-1);
	var id = linha_nova.insertCell(-1);
	var descricao = linha_nova.insertCell(-1);
	var id_post = linha_nova.insertCell(-1);
	var page_id = linha_nova.insertCell(-1);

	id.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='deletavel' value='0'></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>"
	+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir esta configuração?'></input>"
	+"<div data-atributo='id' data-valor-original='' value=''>#</div>"
	+"<div data-atributo='gerenciar'><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	descricao.innerHTML = "<div data-atributo='descricao' data-editavel='true' data-valor-original=''><input type='text' data-atributo='descricao' data-ajax='true'></input></div>";
	id_post.innerHTML = "<div data-atributo='id_post' data-style='width: 30px;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='id_post' data-ajax='true' style='width: 30px;'></input></div>";
	page_id.innerHTML = "<div data-atributo='page_id' data-type='checkbox' data-editavel='true' data-valor-original='0'><input type='checkbox' data-atributo='page_id' data-ajax='true'></input></div>";
	
	evento.preventDefault();
	return false;
}

/******************
function nova_missao
--------------------
Insere uma nova Missão
******************/
function nova_missao(evento) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault()
		return false;
	}
		
	objeto_em_edicao = true; //Bloqueia a edição de outros objetos
	var tabela = document.getElementsByTagName('TABLE')[0];
	var linha_nova = tabela.insertRow(-1);
	var id = linha_nova.insertCell(-1);
	var descricao = linha_nova.insertCell(-1);
	var texto_sucesso = linha_nova.insertCell(-1);
	var texto_fracasso = linha_nova.insertCell(-1);
	var id_imperio = linha_nova.insertCell(-1);
	var id_imperios_aceitaram = linha_nova.insertCell(-1);
	var id_imperios_rejeitaram = linha_nova.insertCell(-1);
	var ativo = linha_nova.insertCell(-1);
	var turno = linha_nova.insertCell(-1);
	var turno_validade = linha_nova.insertCell(-1);
	var sucesso = linha_nova.insertCell(-1);

	id.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>"
	+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir essa Missão'></input>"
	+"<div data-atributo='id' data-valor-original='' value=''>#</div>"
	+"<div data-atributo='gerenciar'><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	descricao.innerHTML = "<div data-atributo='descricao' data-type='textarea' data-editavel='true' data-valor-original='' data-style='width: 140px; height: 40px;' data-id='descricao'>"
	+"<textarea data-atributo='descricao' data-ajax='true' style='width: 140px; height: 40px;' id='descricao'></textarea></div>";
	texto_sucesso.innerHTML = "<div data-atributo='texto_sucesso' data-type='textarea' data-editavel='true' data-valor-original='' data-style='width: 140px; height: 40px;' data-id='texto_sucesso'>"
	+"<textarea data-atributo='texto_sucesso' data-ajax='true' style='width: 140px; height: 40px;' id='texto_sucesso'></textarea></div>";	
	texto_fracasso.innerHTML = "<div data-atributo='texto_fracasso' data-type='textarea' data-editavel='true' data-valor-original='' data-style='width: 140px; height: 40px;' data-id='texto_fracasso'>"
	+"<textarea data-atributo='texto_fracasso' data-ajax='true' style='width: 140px; height: 40px;' id='texto_fracasso'></textarea></div>";	
	id_imperio.innerHTML = "<div data-atributo='id_imperio' data-style='width: 30px;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='id_imperio' data-ajax='true' style='width: 30px;'></input></div>";
	id_imperios_aceitaram.innerHTML = "<div data-atributo='id_imperios_aceitaram' data-style='width: 80px;' data-editavel='true' data-valor-original='' data-branco='true'><input type='text' data-atributo='id_imperios_aceitaram' data-ajax='true' style='width: 80px;'></input></div>";
	id_imperios_rejeitaram.innerHTML = "<div data-atributo='id_imperios_rejeitaram' data-style='width: 80px;' data-editavel='true' data-valor-original='' data-branco='true'><input type='text' data-atributo='id_imperios_rejeitaram' data-ajax='true' style='width: 80px;'></input></div>";
	ativo.innerHTML = "<div data-atributo='ativo' data-editavel='true' data-type='checkbox' data-editavel='true' data-valor-original='1'><input type='checkbox' data-atributo='ativo' data-ajax='true' checked></input></div>";
	turno.innerHTML = "<div data-atributo='turno' data-editavel='true' data-valor-original='' data-style='width: 30px;'><input type='text' data-atributo='turno' data-ajax='true' style='width: 30px;' value='"+turno_atual+"'></input></div>";
	turno_validade.innerHTML = "<div data-atributo='turno_validade' data-editavel='true' data-valor-original='' data-style='width: 30px;'><input type='text' data-atributo='turno_validade' data-ajax='true' style='width: 30px;' value='"+turno_atual+"'></input></div>";
	sucesso.innerHTML = "<div data-atributo='sucesso' data-editavel='true' data-type='checkbox' data-editavel='true' data-valor-original='0'><input type='checkbox' data-atributo='sucesso' data-ajax='true'></input></div>";
	
	window.scrollTo(0, document.body.scrollHeight);
	evento.preventDefault();
	return false;
}

/******************
function novo_recurso
--------------------
Insere um novo recurso
******************/
function novo_recurso(evento) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
		
	objeto_em_edicao = true; //Bloqueia a edição de outros Impérios
	var tabela = document.getElementsByTagName('TABLE');
	tabela = tabela[0];
	var linha_nova = tabela.insertRow(-1);
	
	var id = linha_nova.insertCell(-1);
	var nome = linha_nova.insertCell(-1);
	var descricao = linha_nova.insertCell(-1);
	var icone = linha_nova.insertCell(-1);
	var nivel = linha_nova.insertCell(-1);
	var acumulavel = linha_nova.insertCell(-1);
	var extrativo = linha_nova.insertCell(-1);
	var local = linha_nova.insertCell(-1);
	
	id.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>"
	+"<div data-atributo='id' data-valor-original=''>#</div>"
	+"<div data-atributo='gerenciar'><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	nome.innerHTML = "<div data-atributo='nome' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nome' data-ajax='true'></input></div>";
	descricao.innerHTML = "<div data-atributo='descricao' data-editavel='true' data-valor-original=''><input type='text' data-atributo='descricao' data-ajax='true'></input></div>";
	icone.innerHTML = "<div data-atributo='icone' data-editavel='true' data-branco='true' data-valor-original=''><input type='text' data-atributo='icone' data-ajax='true' data-branco='true'></input></div>";
	nivel.innerHTML = "<div data-atributo='nivel' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nivel' data-ajax='true' value='1'></input></div>";
	acumulavel.innerHTML = "<div data-atributo='acumulavel' data-type='checkbox' data-editavel='true' data-valor-original='1'><input type='checkbox' data-atributo='acumulavel' data-ajax='true' checked></input></div>";
	extrativo.innerHTML = "<div data-atributo='extrativo' data-type='checkbox' data-editavel='true' data-valor-original='1'><input type='checkbox' data-atributo='extrativo' data-ajax='true' checked></input></div>";
	local.innerHTML = "<div data-atributo='local' data-type='checkbox' data-editavel='true' data-valor-original='1'><input type='checkbox' data-atributo='local' data-ajax='true' checked></input></div>";

	evento.preventDefault();
	return false;
}

/******************
function nova_tech
--------------------
Insere um nova Tech
******************/
function nova_tech(evento) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
		
	objeto_em_edicao = true; //Bloqueia a edição de outros Impérios
	var tabela = document.getElementsByTagName('TABLE');
	tabela = tabela[0];
	var linha_nova = tabela.insertRow(-1);
	
	var id = linha_nova.insertCell(-1);
	var nome = linha_nova.insertCell(-1);
	var descricao = linha_nova.insertCell(-1);
	var nivel = linha_nova.insertCell(-1);
	var custo = linha_nova.insertCell(-1);
	var id_tech_parent = linha_nova.insertCell(-1);
	var lista_requisitos = linha_nova.insertCell(-1);
	var belica = linha_nova.insertCell(-1);
	var publica = linha_nova.insertCell(-1);
	var especiais = linha_nova.insertCell(-1);
	var icone = linha_nova.insertCell(-1);
	
	id.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>"
	+"<div data-atributo='id' data-valor-original=''>#</div>"
	+"<div data-atributo='gerenciar'><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	nome.innerHTML = "<div data-atributo='nome' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nome' data-ajax='true'></input></div>";
	descricao.innerHTML = "<div data-atributo='descricao' data-editavel='true' data-valor-original=''><input type='text' data-atributo='descricao' data-ajax='true'></input></div>";
	nivel.innerHTML = "<div data-atributo='nivel' data-editavel='true' data-valor-original='' data-style='width: 50px;'><input type='text' data-atributo='nivel' data-ajax='true' style='width: 50px;'></input></div>";
	custo.innerHTML = "<div data-atributo='custo' data-editavel='true' data-valor-original='' data-style='width: 50px;'><input type='text' data-atributo='custo' data-ajax='true' style='width: 50px;'></input></div>";
	id_tech_parent.innerHTML = "<div data-atributo='id_tech_parent' data-editavel='true' data-valor-original='' data-style='width: 30px;'><input type='text' data-atributo='id_tech_parent' data-ajax='true' style='width: 30px;'></input></div>";
	lista_requisitos.innerHTML = "<div data-atributo='lista_requisitos' data-editavel='true' data-branco='true' data-valor-original=''><input type='text' data-atributo='lista_requisitos' data-ajax='true' data-branco='true'></input></div>";
	belica.innerHTML = "<div data-atributo='belica' data-type='checkbox' data-editavel='true' data-valor-original='0'><input type='checkbox' data-atributo='belica' data-ajax='true'></input></div>";
	publica.innerHTML = "<div data-atributo='publica' data-type='checkbox' data-editavel='true' data-valor-original='1'><input type='checkbox' data-atributo='publica' data-ajax='true' checked></input></div>";
	especiais.innerHTML = "<div data-atributo='especiais' data-editavel='true' data-branco='true' data-valor-original=''><input type='text' data-atributo='especiais' data-ajax='true' data-branco='true'></input></div>";
	icone.innerHTML = "<div data-atributo='icone' data-editavel='true' data-branco='true' data-valor-original=''><input type='text' data-atributo='icone' data-ajax='true' data-branco='true'></input></div>";

	evento.preventDefault();
	return false;
}

/******************
function nova_tech_imperio
--------------------
Adiciona um nova Tech à um Império
******************/
function nova_tech_imperio(evento, id_imperio) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
		
	
	objeto_em_edicao = true; //Bloqueia a edição de outros objetos
	var tabela = document.getElementsByTagName('TABLE');
	tabela = tabela[1];
	var linha_nova = tabela.insertRow(-1);
	
	var lista_techs = lista_techs_html();
	
	var nome_tech = linha_nova.insertCell(-1);
	var custo_pago = linha_nova.insertCell(-1);
	var turno = linha_nova.insertCell(-1);
	var tech_inicial = linha_nova.insertCell(-1);

	nome_tech.innerHTML = "<td style='width: 300px;'>"
	+"<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='"+id_imperio+"' value='"+id_imperio+"'></input>"
	+"<input type='hidden' data-atributo='id_tech' data-ajax='true' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_tech_imperio'></input>"
	+"<div data-atributo='nome_tech' data-editavel='true' data-type='select' data-funcao='lista_techs_html' data-id-selecionado='' data-valor-original=''>"+lista_techs+"</div>"
	+"<div data-atributo='gerenciar'><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	custo_pago.innerHTML = "<div data-atributo='custo_pago' data-editavel='true' data-valor-original='0' data-style='width: 30px;'><input type='text' data-atributo='custo_pago' data-ajax='true' style='width: 30px;' value='0'></input></div>";
	turno.innerHTML = "<div data-atributo='turno' data-editavel='true' data-valor-original='' data-style='width: 30px;'><input type='text' data-atributo='turno' data-ajax='true' style='width: 30px;' value='"+turno_atual+"'></input></div>";
	tech_inicial.innerHTML = "<td><div data-atributo='tech_inicial' data-type='checkbox' data-editavel='true' data-valor-original='0'><input type='checkbox' data-atributo='tech_inicial' data-ajax='true' value='1'></input></div></td>";

	window.scrollTo(0, document.body.scrollHeight);
	evento.preventDefault();
	return false;
}

/******************
function nova_instalacao_imperio
--------------------
Adiciona uma nova Instalação não-pública ao Império
******************/
function nova_instalacao_imperio(evento, id_imperio) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
		
	
	objeto_em_edicao = true; //Bloqueia a edição de outros objetos
	var tabela = document.getElementsByTagName('TABLE');
	tabela = tabela[2];
	var linha_nova = tabela.insertRow(-1);
	
	var lista_instalacao = lista_instalacoes_ocultas_html();
	
	var nome_instalacao = linha_nova.insertCell(-1);

	nome_instalacao.innerHTML = "<td style='width: 400px;'>"
	+"<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='"+id_imperio+"' value='"+id_imperio+"'></input>"
	+"<input type='hidden' data-atributo='id_instalacao' data-ajax='true' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<div data-atributo='nome_instalacao' data-editavel='true' data-type='select' data-funcao='lista_instalacao' data-id-selecionado='' data-valor-original=''>"+lista_instalacao+"</div>"
	+"<div data-atributo='gerenciar'><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";

	window.scrollTo(0, document.body.scrollHeight);
	evento.preventDefault();
	return false;
}

/******************
function nova_instalacao
--------------------
Insere uma nova instalação
******************/
function nova_instalacao(evento) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
		
	objeto_em_edicao = true; //Bloqueia a edição de outros Impérios
	var tabela = document.getElementsByTagName('TABLE');
	tabela = tabela[0];
	var linha_nova = tabela.insertRow(-1);
	
	var lista_techs = lista_techs_html();
	
	var id = linha_nova.insertCell(-1);
	var nome = linha_nova.insertCell(-1);
	var descricao = linha_nova.insertCell(-1);
	var tech_requisito = linha_nova.insertCell(-1);
	var slots = linha_nova.insertCell(-1);
	var autonoma = linha_nova.insertCell(-1);
	var desguarnecida = linha_nova.insertCell(-1);
	var sempre_ativa = linha_nova.insertCell(-1);
	var oculta = linha_nova.insertCell(-1);
	var publica = linha_nova.insertCell(-1);
	var especiais = linha_nova.insertCell(-1);
	var icone = linha_nova.insertCell(-1);
	var custos = linha_nova.insertCell(-1);
	var gerencia = linha_nova.insertCell(-1);
	
	id.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>"
	+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir esta instalação e todas suas ligações (recursos produzidos, consumidos etc)?'></input>"
	+"<div data-atributo='id' data-valor-original=''>#</div>"
	+"<div data-atributo='gerenciar'><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	nome.innerHTML = "<div data-atributo='nome' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nome' data-ajax='true'></input></div>";
	descricao.innerHTML = "<div data-atributo='descricao' data-editavel='true' data-valor-original=''><input type='text' data-atributo='descricao' data-ajax='true'></input></div>";
	tech_requisito.innerHTML = "<div data-atributo='nome_tech' data-editavel='true' data-type='select' data-funcao='lista_techs_html' data-id-selecionado='' data-valor-original=''>"+lista_techs+"</div>";
	slots.innerHTML = "<div data-atributo='slots' data-editavel='true' data-valor-original='1' data-style='width: 30px;'><input type='text' data-atributo='slots' data-ajax='true' value='1' style='width: 30px;'></input></div>";
	autonoma.innerHTML = "<div data-atributo='autonoma' data-type='checkbox' data-editavel='true' data-valor-original='0'><input type='checkbox' data-atributo='autonoma' data-ajax='true'></input></div>";
	desguarnecida.innerHTML = "<div data-atributo='desguarnecida' data-type='checkbox' data-editavel='true' data-valor-original='0'><input type='checkbox' data-atributo='desguarnecida' data-ajax='true'></input></div>";
	sempre_ativa.innerHTML = "<div data-atributo='sempre_ativa' data-type='checkbox' data-editavel='true' data-valor-original='1'><input type='checkbox' data-atributo='sempre_ativa' data-ajax='true' checked></input></div>";
	oculta.innerHTML = "<div data-atributo='oculta' data-type='checkbox' data-editavel='true' data-valor-original='0'><input type='checkbox' data-atributo='oculta' data-ajax='true'></input></div>";
	publica.innerHTML = "<div data-atributo='publica' data-type='checkbox' data-editavel='true' data-valor-original='0'><input type='checkbox' data-atributo='publica' data-ajax='true' checked></input></div>";
	especiais.innerHTML = "<div data-atributo='especiais' data-editavel='true' data-branco='true' data-valor-original=''><input type='text' data-atributo='especiais' data-ajax='true' data-branco='true'></input></div>";
	icone.innerHTML = "<div data-atributo='icone' data-editavel='true' data-branco='true' data-valor-original=''><input type='text' data-atributo='icone' data-ajax='true' data-branco='true'></input></div>";
	custos.innerHTML = "<div data-atributo='custos' data-editavel='true' data-branco='true' data-valor-original=''><input type='text' data-atributo='custos' data-ajax='true' data-branco='true'></input></div>";
	gerencia.innerHTML = "<div data-atributo='gerenciar' data-valor-original=''><a href='#' onclick='return gerenciar_objeto(event, this);' style='visibility: hidden;'>Gerenciar Objeto</a></div>";

	evento.preventDefault();	
	return false;	
}

/******************
function novo_instalacao_recurso(consome = 1)
--------------------
Insere um novo recurso atrelado à instalação
consome = 1 -- O recurso é produzido (0) ou consumido (1)
******************/
function novo_instalacao_recurso(evento, consome = 1) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
		
	objeto_em_edicao = true; //Bloqueia a edição de outros objetos
	var tabela = document.getElementsByTagName('TABLE')[consome];
	var linha_nova = tabela.insertRow(-1);
	var id = linha_nova.insertCell(-1);
	var recurso = linha_nova.insertCell(-1);
	var qtd_por_nivel = linha_nova.insertCell(-1);
		
	var lista_recursos = lista_recursos_html();
	
	id.innerHTML = 	"<input type='hidden' data-atributo='id' value='' data-valor-original=''></input>"
	+"<input type='hidden' data-atributo='id_instalacao' data-ajax='true' value='"+id_instalacao+"' data-valor-original='"+id_instalacao+"'></input>"
	+"<input type='hidden' data-atributo='id_recurso' data-ajax='true' value='' data-valor-original=''></input>"
	+"<input type='hidden' data-atributo='consome' data-ajax='true' value='"+consome+"' data-valor-original='"+consome+"'></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_instalacao_recurso'></input>"
	+"<div data-atributo='id' data-valor-original=''>#</div>"
	+"<div data-atributo='gerenciar'><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	recurso.innerHTML = "<div data-atributo='nome_recurso' data-editavel='true' data-type='select' data-funcao='lista_recursos_html' data-id-selecionado = '0' data-valor-original=''>"+lista_recursos+"</div>";
	qtd_por_nivel.innerHTML = "<div data-atributo='qtd_por_nivel' data-style='width: 50px;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='qtd_por_nivel' data-ajax='true' style='width: 50px;'></input></div>";

	evento.preventDefault();
	return false;
}

/******************
function nova_colonia
--------------------
Insere uma nova Colônia
--------
id_imperio -- id do Império que receberá a colônia
******************/
function nova_colonia(evento, id_imperio) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
		
	objeto_em_edicao = true; //Bloqueia a edição de outros objetos
	var tabela = document.getElementsByTagName('TABLE');
	
	//Determina qual tabela (ou seja, qual Império) está sendo editado
	for (var index_tabelas = 0; index_tabelas < tabela.length; index_tabelas++) {
		if (tabela[index_tabelas].getAttribute('data-id-imperio') == id_imperio) {
			tabela = tabela[index_tabelas];
			nome_imperio = tabela.getAttribute('data-nome-imperio')
			break;
		}
	}
	
	var linha_nova = tabela.insertRow(-1);
	var id = linha_nova.insertCell(-1);
	if (id_imperio == 0) {
		var nome_npc = linha_nova.insertCell(-1);
		nome_npc.innerHTML = "<div data-atributo='nome_npc' data-editavel='true' data-branco='true' data-style='width: 120px;'><input type='text' data-atributo='nome_npc' data-ajax='true' style='width: 120px;'></input></div>";
	}
	
	var nome_planeta = linha_nova.insertCell(-1);
	var capital = linha_nova.insertCell(-1);
	var vassalo = linha_nova.insertCell(-1);
	var pop = linha_nova.insertCell(-1);
	var pop_robotica = linha_nova.insertCell(-1);
	var poluicao = linha_nova.insertCell(-1);
	var turno = linha_nova.insertCell(-1);		
	var gerencia = linha_nova.insertCell(-1);
	
	var lista_planetas = lista_planetas_html();
	var lista_imperios = "<select data-atributo='id_imperio' style='width: 100%'>\n"
	+"<option value='"+id_imperio+"' selected>"+nome_imperio+"</option>\n"
	+"</select>";
	
	id.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='id_planeta' data-ajax='true' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_colonia'></input>"
	+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja remover esta colônia?'></input>"
	+"<div data-atributo='id' data-valor-original=''>#</div>"
	+"<div data-atributo='nome_imperio' data-editavel='true' data-type='select' data-funcao='lista_imperios_html' data-id-selecionado='"+id_imperio+"' data-argumentos='\"id_remove\":\"0\", \"npcs\":\"0\"}' data-valor-original='"+nome_imperio+"'>"+lista_imperios+"</div>"
	+"<div data-atributo='gerenciar'><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	nome_planeta.innerHTML = "<div data-atributo='nome_planeta' data-editavel='true' data-type='select' data-funcao='lista_planetas_html' data-id-selecionado='' data-valor-original=''>"+lista_planetas+"</div>";
	capital.innerHTML = "<div data-atributo='capital' data-type='checkbox' data-editavel='true' data-valor-original=''><input type='checkbox' data-atributo='capital' data-ajax='true' value='1'></input></div>";
	vassalo.innerHTML = "<div data-atributo='vassalo' data-type='checkbox' data-editavel='true' data-valor-original=''><input type='checkbox' data-atributo='vassalo' data-ajax='true' value='1'></input></div>";
	pop.innerHTML = "<div data-atributo='pop' data-editavel='true' data-style='width: 30px;'><input type='text' data-atributo='pop' data-ajax='true' style='width: 30px;'></input></div>";
	pop_robotica.innerHTML = "<div data-atributo='pop_robotica' data-editavel='true' data-style='width: 30px;'><input type='text' data-atributo='pop_robotica' data-ajax='true' style='width: 30px;' value=0></input></div>";
	poluicao.innerHTML = "<div data-atributo='poluicao' data-editavel='true' data-style='width: 30px;'><input type='text' data-atributo='poluicao' data-ajax='true' style='width: 30px;'></input></div>";
	turno.innerHTML = "<div data-atributo='turno' data-editavel='true' data-style='width: 30px;'><input type='text' data-atributo='turno' data-ajax='true' style='width: 30px;' value='"+turno_atual+"'></input></div>";
	gerencia.innerHTML = "<div data-atributo='gerenciar' data-valor-original=''><a href='#' onclick='return gerenciar_objeto(event, this);' style='visibility: hidden;'>Gerenciar Objeto</a></div>";

	evento.preventDefault();
	return false;
}


/******************
function popular_recursos_planeta
--------------------
Adiciona todos os recursos extrativos num planeta
--------
id_planeta -- id do Planeta que receberá o recurso
objeto -- Usado para remover o link após clicar
diversos -- Permite adicionar mais de um recurso por vez
******************/
function popular_recursos_planeta(evento, objeto, id_planeta) {
	var dados_ajax = "post_type=POST&action=ids_recursos_extrativos";
	var retorno = false;

	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			try {
				var resposta = JSON.parse(this.responseText);
				resposta.forEach(element => {
					novo_planeta_recurso(evento, id_planeta, true, element);
					//console.log(element);
				});
			} catch (err) {
				console.log(resposta);
			}
		}
	};
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);
	
	objeto.parentNode.removeChild(objeto);
}

/******************
function novo_planeta_recurso
--------------------
Insere um novo recurso numa colônia
--------
id_planeta -- id do Planeta que receberá o recurso
diversos -- Permite adicionar mais de um recurso por vez
******************/
function novo_planeta_recurso(evento, id_planeta, diversos=false, id_recurso=0) {
	if (objeto_em_edicao && !diversos) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
		
	objeto_em_edicao = true; //Bloqueia a edição de outros objetos
	var tabela = document.getElementsByTagName('TABLE')[0];
	
	var linha_nova = tabela.insertRow(-1);
	var id = linha_nova.insertCell(-1);
	var nome_recurso = linha_nova.insertCell(-1);
	var disponivel = linha_nova.insertCell(-1);
	var turno = linha_nova.insertCell(-1);
	
	var lista_recursos = lista_recursos_html(id_recurso);
	
	id.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='id_planeta' data-ajax='true' data-valor-original='"+id_planeta+"' value='"+id_planeta+"'></input>"
	+"<input type='hidden' data-atributo='id_recurso' data-ajax='true' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_planeta_recurso'></input>"
	+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir este recurso?'></input>"
	+"<div data-atributo='id' data-valor-original=''>#</div>"
	+"<div data-atributo='gerenciar'><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	nome_recurso.innerHTML = "<div data-atributo='nome_recurso' data-editavel='true' data-type='select' data-funcao='lista_recursos_html' data-id-selecionado='' data-valor-original=''>"+lista_recursos+"</div>";
	disponivel.innerHTML = "<div data-atributo='disponivel' data-editavel='true' data-style='width: 50px;'><input type='text' data-atributo='disponivel' data-ajax='true' style='width: 50px;'></input></div>";
	turno.innerHTML = "<div data-atributo='turno' data-editavel='true' data-style='width: 50px;'><input type='text' data-atributo='turno' data-ajax='true' style='width: 50px;' value='"+turno_atual+"'></input></div>";

	evento.preventDefault();
	return false;
}

/******************
function nova_colonia_instalacao
--------------------
Insere uma nova Instalação no Planeta
--------
id_planeta -- id do Planeta que receberá a Instalação
******************/
function nova_colonia_instalacao(evento, id_planeta) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
	
	objeto_em_edicao = true; //Bloqueia a edição de outros objetos
	var tabela = document.getElementsByTagName('TABLE')[1];
	
	var linha_nova = tabela.insertRow(-1);
	var id = linha_nova.insertCell(-1);
	var nome_instalacao = linha_nova.insertCell(-1);
	var nivel = linha_nova.insertCell(-1);
	var turno = linha_nova.insertCell(-1);
	var instalacao_inicial = linha_nova.insertCell(-1);
	var turno_desmonta = linha_nova.insertCell(-1);
	var turno_destroi = linha_nova.insertCell(-1);
	var gerencia = linha_nova.insertCell(-1);
	
	var lista_instalacao = lista_instalacoes_html();
	
	id.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='id_planeta' data-ajax='true' data-valor-original='"+id_planeta+"' value='"+id_planeta+"'></input>"
	+"<input type='hidden' data-atributo='id_instalacao' data-ajax='true' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_colonia_instalacao'></input>"
	+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir esta instalação?'></input>"
	+"<div data-atributo='id' data-valor-original=''>#</div>"
	+"<div data-atributo='gerenciar'><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	nome_instalacao.innerHTML = "<div data-atributo='nome_instalacao' data-editavel='true' data-type='select' data-funcao='lista_instalacoes_html' data-id-selecionado='' data-valor-original=''>"+lista_instalacao+"</div>";
	nivel.innerHTML = "<div data-atributo='nivel' data-editavel='true' data-style='width: 30px;'><input type='text' data-atributo='nivel' data-ajax='true' style='width: 30px;'></input></div>";
	turno.innerHTML = "<div data-atributo='turno' data-editavel='true' data-style='width: 30px;'><input type='text' data-atributo='turno' data-ajax='true' style='width: 30px;' value='"+turno_atual+"'></input></div>";
	turno_destroi.innerHTML = "<div data-atributo='turno_destroi' data-valor-original=''>#</div>";
	turno_desmonta.innerHTML = "<div data-atributo='turno_desmonta' data-style='width: 50px;' data-editavel='true' data-valor-original='' data-branco='true'>#</div>";
	instalacao_inicial.innerHTML = "<td><div data-atributo='instalacao_inicial' data-type='checkbox' data-editavel='true' data-valor-original='0'><input type='checkbox' data-atributo='instalacao_inicial' data-ajax='true' value='1'></input></div></td>";
	gerencia.innerHTML = "<div data-atributo='gerenciar' data-valor-original=''><a href='#' onclick='return destruir_instalacao(event, this);' style='visibility: hidden;'>Destruir Instalação</a></div>"
	+"<div data-atributo='gerenciar'><a href='#' onclick='return desmonta_instalacao(event, this, "+turno_atual+");' style='visibility: hidden;'>Desmantelar</a></div>";

	evento.preventDefault();
	return false;
}

/******************
function nova_nave
--------------------
Insere uma nova Nave (Frota)
--------
id_imperio -- id do Império que receberá a nave
******************/
function nova_nave(evento, id_imperio) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
		
	objeto_em_edicao = true; //Bloqueia a edição de outros objetos
	var tabela = document.getElementsByTagName('TABLE');
	
	//Determina qual tabela (ou seja, qual Império) está sendo editado
	for (var index_tabelas = 0; index_tabelas < tabela.length; index_tabelas++) {
		if (tabela[index_tabelas].getAttribute('data-id-imperio') == id_imperio) {
			tabela = tabela[index_tabelas];
			break;
		}
	}
	
	var linha_nova = tabela.insertRow(-1);
	
	var nome = linha_nova.insertCell(-1);
	var categoria = linha_nova.insertCell(-1);
	var qtd = linha_nova.insertCell(-1);
	var X = linha_nova.insertCell(-1);
	var Y = linha_nova.insertCell(-1);
	var Z = linha_nova.insertCell(-1);

	categoria.innerHTML = "<div data-atributo='tipo' data-editavel='true' data-valor-original='' data-style='width: 100px;' data-id='categoria'><input type='text' data-atributo='tipo' data-ajax='true' style='width: 100px;' id='categoria'></input></div>";
	
	qtd.innerHTML = "<div data-atributo='qtd' data-editavel='true' data-valor-original='' data-style='width: 30px;'><input type='text' data-atributo='qtd' data-ajax='true' style='width: 30px;'></input></div>";
	X.innerHTML = "<div data-atributo='X' data-editavel='true' data-valor-original='' data-style='width: 30px;'><input type='text' data-atributo='X' data-ajax='true' style='width: 30px;'></input></div>";
	Y.innerHTML = "<div data-atributo='Y' data-editavel='true' data-valor-original='' data-style='width: 30px;'><input type='text' data-atributo='Y' data-ajax='true' style='width: 30px;'></input></div>";
	Z.innerHTML = "<div data-atributo='Z' data-editavel='true' data-valor-original='' data-style='width: 30px;'><input type='text' data-atributo='Z' data-ajax='true' style='width: 30px;'></input></div>";


	if (id_imperio != 0) {
	
		var string_nave = linha_nova.insertCell(-1);
		var tamanho = linha_nova.insertCell(-1);
		var HP = linha_nova.insertCell(-1);
		var velocidade = linha_nova.insertCell(-1);
		var alcance = linha_nova.insertCell(-1);
		var pdf_laser = linha_nova.insertCell(-1);
		var pdf_torpedo = linha_nova.insertCell(-1);
		var pdf_projetil = linha_nova.insertCell(-1);
		var blindagem = linha_nova.insertCell(-1);
		var escudos = linha_nova.insertCell(-1);
		var qtd_bombas = linha_nova.insertCell(-1);
		var qtd_tropas = linha_nova.insertCell(-1);
		var pesquisa = linha_nova.insertCell(-1);
		var camuflagem = linha_nova.insertCell(-1);
		var nivel_estacao_orbital = linha_nova.insertCell(-1);
		var especiais = linha_nova.insertCell(-1);
		
		categoria.innerHTML = categoria.innerHTML
		+ "\n<div class='subtitulo'>Custo</div>"
		+"\n<div data-atributo='custo' data-ajax='true' data-editavel='true' data-branco='true' data-valor-original='' data-style='width: 100px;' data-id='custo'><input type='text' data-atributo='custo' data-ajax='true' style='width: 100px;' id='custo'></input></div>";
		
		nome.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
		+"<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='"+id_imperio+"' value='"+id_imperio+"'></input>"
		+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
		+"<input type='hidden' data-atributo='where_value' value=''></input>"
		+"<input type='hidden' data-atributo='funcao_validacao' value='valida_nave'></input>"
		+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir esta Frota?'></input>"
		+"<div data-atributo='nome' data-editavel='true' data-valor-original='' data-style='width: 100px;'><input type='text' data-atributo='nome' data-ajax='true' style='width: 80px;'></input></div>"
		+"<div data-atributo='gerenciar'><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>"
		+"<div data-atributo='processa_string' data-valor-original=''><a href='#' onclick='return processa_string_admin(event, this);'>Processa String</a></div>";
	
		string_nave.innerHTML = "<div data-atributo='string_nave' data-editavel='true' data-type='textarea' data-valor-original='' data-style='width: 100px; height: 200px;' data-id='string_nave'><textarea data-atributo='string_nave' data-ajax='true' style='width: 80px; height: 200px;' id='string_nave'></textarea></div>";
		tamanho.innerHTML = "<div data-atributo='tamanho' data-editavel='true' data-valor-original='' data-style='width: 50px;' data-id='tamanho'><input type='text' data-atributo='tamanho' data-ajax='true' style='width: 50px;' id='tamanho'></input></div>";
		HP.innerHTML = "<div data-atributo='HP' data-editavel='true' data-valor-original='' data-style='width: 50px;' data-id='hp'><input type='text' data-atributo='HP' data-ajax='true' style='width: 50px;' id='hp'></input></div>";
		velocidade.innerHTML = "<div data-atributo='velocidade' data-editavel='true' data-valor-original='' data-style='width: 50px;' data-id='velocidade'><input type='text' data-atributo='velocidade' data-ajax='true' style='width: 50px;' id='velocidade'></input></div>";
		alcance.innerHTML = "<div data-atributo='alcance' data-editavel='true' data-valor-original='' data-style='width: 50px;' data-id='alcance'><input type='text' data-atributo='alcance' data-ajax='true' style='width: 50px;' id='alcance'></input></div>";
		pdf_laser.innerHTML = "<div data-atributo='pdf_laser' data-editavel='true' data-valor-original='' data-style='width: 50px;' data-id='pdf_laser'><input type='text' data-atributo='pdf_laser' data-ajax='true' style='width: 50px;' id='pdf_laser'></input></div>";
		pdf_torpedo.innerHTML = "<div data-atributo='pdf_torpedo' data-editavel='true' data-valor-original='' data-style='width: 50px;' data-id='pdf_torpedo'><input type='text' data-atributo='pdf_torpedo' data-ajax='true' style='width: 50px;' id='pdf_torpedo'></input></div>";
		pdf_projetil.innerHTML = "<div data-atributo='pdf_projetil' data-editavel='true' data-valor-original='' data-style='width: 50px;' data-id='pdf_projetil'><input type='text' data-atributo='pdf_projetil' data-ajax='true' style='width: 50px;' id='pdf_projetil'></input></div>";
		blindagem.innerHTML = "<div data-atributo='blindagem' data-editavel='true' data-valor-original='' data-style='width: 50px;' data-id='blindagem'><input type='text' data-atributo='blindagem' data-ajax='true' style='width: 50px;' id='blindagem'></input></div>";
		escudos.innerHTML = "<div data-atributo='escudos' data-editavel='true' data-valor-original='' data-style='width: 50px;' data-id='escudos'><input type='text' data-atributo='escudos' data-ajax='true' style='width: 50px;' id='escudos'></input></div>";
		qtd_bombas.innerHTML = "<div data-atributo='pdf_bombardeamento' data-editavel='true' data-valor-original='' data-style='width: 50px;' data-id='qtd_bombas'><input type='text' data-atributo='pdf_bombardeamento' data-ajax='true' style='width: 50px;' id='qtd_bombas'></input></div>";
		qtd_tropas.innerHTML = "<div data-atributo='poder_invasao' data-editavel='true' data-valor-original='' data-style='width: 50px;' data-id='qtd_tropas'><input type='text' data-atributo='poder_invasao' data-ajax='true' style='width: 50px;' id='qtd_tropas'></input></div>";
		pesquisa.innerHTML = "<div data-atributo='pesquisa' data-type='checkbox' data-editavel='true' data-valor-original='' data-id='pesquisa'><input type='checkbox' data-atributo='pesquisa' data-ajax='true' id='pesquisa'></input></div>";
		camuflagem.innerHTML = "<div data-atributo='camuflagem' data-editavel='true' data-valor-original='' data-style='width: 50px;'><input type='text' data-atributo='camuflagem' data-ajax='true' style='width: 50px;' value=0></input></div>";
		nivel_estacao_orbital.innerHTML = "<div data-atributo='nivel_estacao_orbital' data-editavel='true' data-valor-original='' data-style='width: 50px;' data-id='nivel_estacao_orbital'><input type='text' data-atributo='nivel_estacao_orbital' data-ajax='true' style='width: 50px;' id='nivel_estacao_orbital'></input></div>";
		especiais.innerHTML = "<div data-atributo='especiais' data-editavel='true' data-type='textarea' data-valor-original='' data-branco='true' data-style='width: 120px; height: 100px;' data-id='especiais'><textarea data-atributo='especiais' data-ajax='true' style='width: 120px; height: 100px;' data-branco='true' id='especiais'></textarea></div>";
	} else {
		var nome_npc = linha_nova.insertCell(0);
		
		nome_npc.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
		+"<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='"+id_imperio+"' value='"+id_imperio+"'></input>"
		+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
		+"<input type='hidden' data-atributo='where_value' value=''></input>"
		+"<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>"
		+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir esta Frota?'></input>"
		+"<div data-atributo='nome_npc' data-editavel='true' data-valor-original='' data-style='width: 180px;'><input type='text' data-atributo='nome_npc' data-ajax='true' style='width: 180px;'></input></div>"
		+"<div data-atributo='gerenciar'><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
		
		nome.innerHTML = "<div data-atributo='nome' data-editavel='true' data-valor-original='' data-style='width: 120px;'><input type='text' data-atributo='nome' data-ajax='true' style='width: 120px;'></input></div>";
	}

	var turno = linha_nova.insertCell(-1);
	var turno_destruido = linha_nova.insertCell(-1);
	var gerenciar = linha_nova.insertCell(-1);	
	
	turno.innerHTML = "<div data-atributo='turno' data-editavel='true' data-valor-original='' data-style='width: 50px;'><input type='text' data-atributo='turno' data-ajax='true' style='width: 50px;' value='"+turno_atual+"'></input></div>";
	turno_destruido.innerHTML = "<div data-atributo='turno_destruido' data-editavel='true' data-valor-original='' data-style='width: 50px;'><input type='text' data-atributo='turno_destruido' data-ajax='true' style='width: 50px;' value='0'></input></div>";
	gerenciar.innerHTML = "<div data-atributo='gerenciar' data-valor-original=''><a href='#' onclick='return copiar_objeto(event, this,"+id_imperio+");' style='visibility: hidden;'>Criar cópia</a></div>";


	evento.preventDefault();
	return false;	
}

/******************
function nova_acao_admin
--------------------
Insere uma nova Ação do Admin
******************/
function nova_acao_admin(evento) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
		
	objeto_em_edicao = true; //Bloqueia a edição de outros objetos
	var tabela = document.getElementsByTagName('TABLE')[0];
	
	var linha_nova = tabela.insertRow(-1);
	var nome_imperio = linha_nova.insertCell(-1);
	var lista_recursos = linha_nova.insertCell(-1);
	var descricao = linha_nova.insertCell(-1);
	var turno = linha_nova.insertCell(-1);
	
	var lista_imperios = lista_imperios_html();
	
	nome_imperio.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_acao_admin'></input>"
	+"<input type='hidden' data-atributo='funcao_pos_processamento' value='altera_lista_recursos_qtd'></input>"
	+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Remover esta Ação não irá reverter seus efeitos. Deseja continuar?'></input>"
	+"<div data-atributo='nome_imperio' data-editavel='true' data-type='select' data-funcao='lista_imperios_html' data-id-selecionado='' data-valor-original=''>"+lista_imperios+"</div>"
	+"<div data-atributo='gerenciar'><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";

	lista_recursos.innerHTML = "<div data-atributo='lista_recursos_qtd' data-valor-original=''>&nbsp;</div>"
	+"<div style='visibility: hidden;'><div data-atributo='lista_recursos' data-editavel='true' data-style='width: 120px;' data-valor-original='' style='display: inline-block;'><input type='text' data-atributo='lista_recursos' data-ajax='true' style='width: 120px;'></input></div>"
	+ "&nbsp; || &nbsp; <div data-atributo='qtd' data-editavel='true' data-style='width: 120px;' data-valor-original='' style='display: inline-block;'><input type='text' data-atributo='qtd' data-ajax='true' style='width: 120px;'></input></div></div>";

	descricao.innerHTML = "<div data-atributo='descricao' data-editavel='true' data-valor-original='' data-style='width: 200px;'><input type='text' data-atributo='descricao' data-ajax='true' data-valor-original='' style='width: 200px;'></input></div>";
	turno.innerHTML = "<div data-atributo='turno' data-editavel='true' data-style='width: 30px;' data-valor-original=''><input type='text' data-atributo='turno' data-ajax='true' style='width: 30px;' value='"+turno_atual+"'></input></div>";
	
	window.scrollTo(0, document.body.scrollHeight);
	evento.preventDefault();
	return false;
}


/******************
function nova_instalacao_jogador
--------------------
Cria uma nova Instalação que o Jogador pode adicionar
******************/
function nova_instalacao_jogador(evento, objeto, id_planeta, id_imperio) {
	if (range_em_edicao || range_em_edicao == objeto) {
		alert("Já existe um objeto em edição!");
		
		evento.preventDefault();
		return false;
	}
	
	range_em_edicao = objeto;
	let dados_ajax = "post_type=POST&action=lista_instalacoes_imperio&id_planeta="+id_planeta;
	
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
				
			if (resposta.resposta_ajax == "OK!") {
				custos_instalacao = resposta.custo_instalacao;
				//console.log(custos_instalacao);
				processa_nova_instalacao_jogador(evento, objeto, id_planeta, id_imperio, resposta.html);
			} else {
				alert(resposta.resposta_ajax);
			}
			if (resposta.debug !== undefined) {
				console.log(resposta.debug);
			}
		} else if (this.status == 500) {
			console.log(this.responseText);
			console.log(this.statusText);
		}
	};
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);	

	evento.preventDefault();
	return false;
}

/******************
function processa_nova_instalacao_jogador
--------------------
Cria uma nova Instalação que o Jogador pode adicionar
******************/
function processa_nova_instalacao_jogador(evento, objeto, id_planeta, id_imperio, lista_instalacao="") {
	let td_colonia = pega_ascendente(objeto,"TD");
	let tr_colonia = pega_ascendente(objeto, "TR");
	let tabela = pega_ascendente(objeto,"TABLE");
	let trs = tabela.getElementsByTagName("TR");
		
	//console.log(td_colonia.rowSpan);
	let index_linha = 0;
	for (index_linha=0; index_linha < trs.length; index_linha++) {
		//console.log(trs[index_linha].cells[0].rowSpan);
		if (trs[index_linha].cells[0] == td_colonia) {
			//console.log("A linha atual da colônia é a linha " + index_linha);
			break;
		}
	}
	
	td_colonia.rowSpan = td_colonia.rowSpan + 1;
	try {
		var nova_linha = tabela.insertRow(index_linha+td_colonia.rowSpan-1);
	} catch (err) {
		//Normalmente isso acontece pois uma adição foi cancelada. Nesse caso temos que reverter a situação do rowSpan
		td_colonia.rowSpan = td_colonia.rowSpan - 1;
		var nova_linha = tabela.insertRow(index_linha+td_colonia.rowSpan-1);
	}
	
	let celula_instalacao = nova_linha.insertCell(-1);
	let celula_acao = nova_linha.insertCell(-1);
	let celula_gerenciar = nova_linha.insertCell(-1);
	
	celula_instalacao.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='id_planeta' data-ajax='true' data-valor-original='"+id_planeta+"' value='"+id_planeta+"'></input>"
	+"<input type='hidden' data-atributo='id_imperio' data-valor-original='"+id_imperio+"' value='"+id_imperio+"'></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_colonia_instalacao'></input>"
	+"<input type='hidden' data-atributo='funcao_pos_processamento' value='atualiza_recursos_imperio'></input>"
	+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir esta instalação?'></input>"
	+"<input type='hidden' data-atributo='nivel' data-editavel='true' data-ajax='true' data-style='width: 30px;' value='1'></input>"
	+"<input type='hidden' data-atributo='turno' data-editavel='true' data-ajax='true' data-style='width: 30px;' value='"+turno_atual+"'></input>"
	+"<div data-atributo='nome_instalacao' data-editavel='true' data-type='select' data-funcao='lista_instalacoes_html' data-id-selecionado='' data-valor-original=''>"+lista_instalacao+"</div>"
	+"<div data-atributo='custo_instalacao' class='custo_instalacao'>&nbsp;</div>"
	+"<div data-atributo='gerenciar'><a href='#' onclick='return salva_objeto(event, this, false, true,\"colonization_planeta_instalacoes\");'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";

	celula_acao.innerHTML = "&nbsp";
	celula_gerenciar.innerHTML = "&nbsp";
	
	nova_linha.style.backgroundColor = tr_colonia.style.backgroundColor;
	nova_linha.scrollIntoView({behavior: "smooth", block: "center"});
	//objeto.style.visibility = "hidden";
	
	select_lista_instalacao = celula_instalacao.getElementsByTagName("SELECT")[0];
	select_lista_instalacao.focus();
	atualiza_custo_instalacao(evento,select_lista_instalacao);
	
	evento.preventDefault();
	return false;
	
}