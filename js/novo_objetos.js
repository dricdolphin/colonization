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
	
	var id = linha_nova.insertCell(0);
	var dados_jogador = linha_nova.insertCell(1);
	var nome_imperio = linha_nova.insertCell(2);
	var prestigio = linha_nova.insertCell(3);
	var populacao = linha_nova.insertCell(4);
	var pontuacao = linha_nova.insertCell(5);
	var gerencia = linha_nova.insertCell(6);
	
	var lista_jogadores = lista_jogadores_html(); //Pega a lista de usuários do Fórum
	
	
	id.innerHTML = "<input type='hidden' data-atributo='id_jogador' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id_jogador'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_imperio'></input>"
	+"<input type='hidden' data-atributo='funcao_pos_processamento' value='mais_dados_imperio'></input>"
	+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value=''></input>"
	+"<div data-atributo='id' data-valor-original=''>#</div>"
	+"<div><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	dados_jogador.innerHTML = "<div data-atributo='nome_jogador' data-id-selecionado='0'>"+lista_jogadores+"</div>";
	nome_imperio.innerHTML = "<div data-atributo='nome' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nome' data-ajax='true'></input></div>";
	prestigio.innerHTML = "<div data-atributo='prestigio' data-editavel='true' data-valor-original='' data-style='width: 40px;'><input type='text' data-atributo='prestigio' data-ajax='true' style='width: 40px;'></input></div>";
	populacao.innerHTML = "<div data-atributo='pop' data-valor-original=''></div>";
	pontuacao.innerHTML = "<div data-atributo='pontuacao' data-valor-original=''></div>";
	gerencia.innerHTML = "<div data-atributo='gerenciar' data-valor-original=''><a href='#' onclick='return gerenciar_objeto(event, this);' style='visibility: hidden;'>Gerenciar Objeto</a></div>";

	window.scrollTo(0, document.body.scrollHeight);
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
	
	var nome_estrela = linha_nova.insertCell(0);
	var estrela_x = linha_nova.insertCell(1);
	var estrela_y = linha_nova.insertCell(2);
	var estrela_z = linha_nova.insertCell(3);
	var estrela_tipo = linha_nova.insertCell(4);
	var gerencia = linha_nova.insertCell(5);
	
	nome_estrela.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_estrela'></input>"
	+"<div data-atributo='nome' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nome' data-ajax='true'></input></div>"
	+"<div><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	estrela_x.innerHTML = "<div data-atributo='X' data-style='width: 100%;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='X' data-ajax='true' style='width: 100%;'></input></div>";
	estrela_y.innerHTML = "<div data-atributo='Y' data-style='width: 100%;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='Y' data-ajax='true' style='width: 100%;'></input></div>";
	estrela_z.innerHTML = "<div data-atributo='Z' data-style='width: 100%;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='Z' data-ajax='true' style='width: 100%;'></input></div>";
	estrela_tipo.innerHTML = "<div data-atributo='tipo' data-editavel='true' data-valor-original=''><input type='text' data-atributo='tipo' data-ajax='true'></input></div>";
	gerencia.innerHTML = "<div data-atributo='gerenciar' data-valor-original=''><a href='#' onclick='return gerenciar_objeto(event, this);' style='visibility: hidden;'>Gerenciar Objeto</a></div>";

	window.scrollTo(0, document.body.scrollHeight);
	evento.preventDefault();
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
	var id = linha_nova.insertCell(0);
	var nome = linha_nova.insertCell(1);
	var estrela = linha_nova.insertCell(2);
	var posicao = linha_nova.insertCell(3);
	var classe = linha_nova.insertCell(4);
	var subclasse = linha_nova.insertCell(5);
	var tamanho = linha_nova.insertCell(6);
	var inospito = linha_nova.insertCell(7);
	var gerencia = linha_nova.insertCell(8);
	
	if (id_estrela == 0) {
		var lista_estrelas = lista_estrelas_html();
	} else {
		var lista_estrelas = lista_estrelas_html(id_estrela);
	}
	
	id.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='id_estrela' value='"+id_estrela+"'></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>"
	+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir este planeta e todas suas ligações (recursos, instalações etc)?'></input>"
	+"<div data-atributo='id' data-valor-original='' value=''>#</div>"
	+"<div><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	nome.innerHTML = "<div data-atributo='nome' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nome' data-ajax='true'></input></div>";
	estrela.innerHTML = "<div data-atributo='nome_estrela' data-id-selecionado='"+id_estrela+"'>"+lista_estrelas+"</div>";
	posicao.innerHTML = "<div data-atributo='posicao' data-style='width: 30px;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='posicao' data-ajax='true' style='width: 30px;'></input></div>";
	classe.innerHTML = "<div data-atributo='classe' data-editavel='true' data-valor-original=''><input type='text' data-atributo='classe' data-ajax='true'></input></div>";
	subclasse.innerHTML = "<div data-atributo='subclasse' data-editavel='true' data-valor-original=''><input type='text' data-atributo='subclasse' data-ajax='true'></input></div>";
	tamanho.innerHTML = "<div data-atributo='tamanho' data-style='width: 30px;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='tamanho' data-ajax='true' style='width: 30px;'></input></div>";
	inospito.innerHTML = "<div data-atributo='inospito' data-type='checkbox' data-editavel='true' data-valor-original='1'><input type='checkbox' data-atributo='inospito' data-ajax='true' checked></input></div>";
	gerencia.innerHTML = "<div data-atributo='gerenciar' data-valor-original=''><a href='#' onclick='return gerenciar_objeto(event, this,\"page=colonization_admin_planetas\");' style='visibility: hidden;'>Gerenciar Objeto</a></div>";
	
	if (id_estrela != 0) {
		var selects = estrela.getElementsByTagName("select");
		estrela.innerHTML = "<input type='hidden' data-atributo='id_estrela' data-ajax='true' value='"+id_estrela+"'></input><div data-atributo='nome_estrela' data-valor-original='"+selects[0].options[selects[0].selectedIndex].innerHTML+"'>"+selects[0].options[selects[0].selectedIndex].innerHTML+"</div>";
	}
	
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
	
	var id = linha_nova.insertCell(0);
	var nome = linha_nova.insertCell(1);
	var descricao = linha_nova.insertCell(2);
	var acumulavel = linha_nova.insertCell(3);
	var extrativo = linha_nova.insertCell(4);
	var local = linha_nova.insertCell(5);
	
	id.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>"
	+"<div data-atributo='id' data-valor-original=''>#</div>"
	+"<div><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	nome.innerHTML = "<div data-atributo='nome' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nome' data-ajax='true'></input></div>";
	descricao.innerHTML = "<div data-atributo='descricao' data-editavel='true' data-valor-original=''><input type='text' data-atributo='descricao' data-ajax='true'></input></div>";
	acumulavel.innerHTML = "<div data-atributo='acumulavel' data-type='checkbox' data-editavel='true' data-valor-original='1'><input type='checkbox' data-atributo='acumulavel' data-ajax='true' checked></input></div>";
	extrativo.innerHTML = "<div data-atributo='extrativo' data-type='checkbox' data-editavel='true' data-valor-original='1'><input type='checkbox' data-atributo='extrativo' data-ajax='true' checked></input></div>";
	local.innerHTML = "<div data-atributo='local' data-type='checkbox' data-editavel='true' data-valor-original='1'><input type='checkbox' data-atributo='local' data-ajax='true' checked></input></div>";

	window.scrollTo(0, document.body.scrollHeight);
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
	
	var id = linha_nova.insertCell(0);
	var nome = linha_nova.insertCell(1);
	var descricao = linha_nova.insertCell(2);
	var nivel = linha_nova.insertCell(3);
	var custo = linha_nova.insertCell(4);
	var id_tech_parent = linha_nova.insertCell(5);
	var lista_requisitos = linha_nova.insertCell(6);
	var belica = linha_nova.insertCell(7);
	var publica = linha_nova.insertCell(8);
	var especiais = linha_nova.insertCell(9);
	var icone = linha_nova.insertCell(10);
	
	id.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>"
	+"<div data-atributo='id' data-valor-original=''>#</div>"
	+"<div><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
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

	window.scrollTo(0, document.body.scrollHeight);
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
	
	var nome_tech = linha_nova.insertCell(0);
	var custo_pago = linha_nova.insertCell(1);
	var turno = linha_nova.insertCell(2);

	nome_tech.innerHTML = "<td style='width: 300px;'>"
	+"<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='"+id_imperio+"' value='"+id_imperio+"'></input>"
	+"<input type='hidden' data-atributo='id_tech' data-ajax='true' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>"
	+"<div data-atributo='nome_tech' data-editavel='true' data-type='select' data-funcao='lista_techs_html' data-id-selecionado='' data-valor-original=''>"+lista_techs+"</div>"
	+"<div><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	custo_pago.innerHTML = "<div data-atributo='custo_pago' data-editavel='true' data-valor-original='0' data-style='width: 30px;'><input type='text' data-atributo='custo_pago' data-ajax='true' style='width: 30px;' value='0'></input></div>";
	turno.innerHTML = "<div data-atributo='turno' data-editavel='true' data-valor-original='' data-style='width: 30px;'><input type='text' data-atributo='turno' data-ajax='true' style='width: 30px;'></input></div>";

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
	
	var id = linha_nova.insertCell(0);
	var nome = linha_nova.insertCell(1);
	var descricao = linha_nova.insertCell(2);
	var slots = linha_nova.insertCell(3);
	var autonoma = linha_nova.insertCell(4);
	var desguarnecida = linha_nova.insertCell(5);
	var oculta = linha_nova.insertCell(6);
	var gerencia = linha_nova.insertCell(7);
	
	id.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>"
	+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir esta instalação e todas suas ligações (recursos produzidos, consumidos etc)?'></input>"
	+"<div data-atributo='id' data-valor-original=''>#</div>"
	+"<div><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	nome.innerHTML = "<div data-atributo='nome' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nome' data-ajax='true'></input></div>";
	descricao.innerHTML = "<div data-atributo='descricao' data-editavel='true' data-valor-original=''><input type='text' data-atributo='descricao' data-ajax='true'></input></div>";
	slots.innerHTML = "<div data-atributo='slots' data-editavel='true' data-valor-original='1' data-style='width: 30px;'><input type='text' data-atributo='slots' data-ajax='true' value='1' style='width: 30px;'></input></div>";
	autonoma.innerHTML = "<div data-atributo='autonoma' data-type='checkbox' data-editavel='true' data-valor-original='0'><input type='checkbox' data-atributo='autonoma' data-ajax='true'></input></div>";
	desguarnecida.innerHTML = "<div data-atributo='desguarnecida' data-type='checkbox' data-editavel='true' data-valor-original='0'><input type='checkbox' data-atributo='desguarnecida' data-ajax='true'></input></div>";
	oculta.innerHTML = "<div data-atributo='oculta' data-type='checkbox' data-editavel='true' data-valor-original='0'><input type='checkbox' data-atributo='oculta' data-ajax='true'></input></div>";
	gerencia.innerHTML = "<div data-atributo='gerenciar' data-valor-original=''><a href='#' onclick='return gerenciar_objeto(event, this);' style='visibility: hidden;'>Gerenciar Objeto</a></div>";

	window.scrollTo(0, document.body.scrollHeight);	
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
	var id = linha_nova.insertCell(0);
	var recurso = linha_nova.insertCell(1);
	var qtd_por_nivel = linha_nova.insertCell(2);
		
	var lista_recursos = lista_recursos_html();
	
	id.innerHTML = 	"<input type='hidden' data-atributo='id' value='' data-valor-original=''></input>"
	+"<input type='hidden' data-atributo='id_instalacao' data-ajax='true' value='"+id_instalacao+"' data-valor-original='"+id_instalacao+"'></input>"
	+"<input type='hidden' data-atributo='id_recurso' data-ajax='true' value='' data-valor-original=''></input>"
	+"<input type='hidden' data-atributo='consome' data-ajax='true' value='"+consome+"' data-valor-original='"+consome+"'></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_instalacao_recurso'></input>"
	+"<div data-atributo='id' data-valor-original=''>#</div>"
	+"<div><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	recurso.innerHTML = "<div data-atributo='nome_recurso' data-editavel='true' data-type='select' data-funcao='lista_recursos_html' data-id-selecionado = '0' data-valor-original=''>"+lista_recursos+"</div>";
	qtd_por_nivel.innerHTML = "<div data-atributo='qtd_por_nivel' data-style='width: 50px;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='qtd_por_nivel' data-ajax='true' style='width: 50px;'></input></div>";

	window.scrollTo(0, document.body.scrollHeight);		
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
			break;
		}
	}
	
	var linha_nova = tabela.insertRow(-1);
	var id = linha_nova.insertCell(0);
	var nome_planeta = linha_nova.insertCell(1);
	var pop = linha_nova.insertCell(2);
	var poluicao = linha_nova.insertCell(3);
	var turno = linha_nova.insertCell(4);		
	var gerencia = linha_nova.insertCell(5);
	
	var lista_planetas = lista_planetas_html();
	
	id.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='"+id_imperio+"' value='"+id_imperio+"'></input>"
	+"<input type='hidden' data-atributo='id_planeta' data-ajax='true' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_colonia'></input>"
	+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja remover esta colônia?'></input>"
	+"<div data-atributo='id' data-valor-original=''>#</div>"
	+"<div><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	nome_planeta.innerHTML = "<div data-atributo='nome_planeta' data-editavel='true' data-type='select' data-funcao='lista_planetas_html' data-id-selecionado='' data-valor-original=''>"+lista_planetas+"</div>";
	pop.innerHTML = "<div data-atributo='pop' data-editavel='true' data-style='width: 30px;'><input type='text' data-atributo='pop' data-ajax='true' style='width: 30px;'></input></div>";
	poluicao.innerHTML = "<div data-atributo='poluicao' data-editavel='true' data-style='width: 30px;'><input type='text' data-atributo='poluicao' data-ajax='true' style='width: 30px;'></input></div>";
	turno.innerHTML = "<div data-atributo='turno' data-editavel='true' data-style='width: 30px;'><input type='text' data-atributo='turno' data-ajax='true' style='width: 30px;'></input></div>";
	gerencia.innerHTML = "<div data-atributo='gerenciar' data-valor-original=''><a href='#' onclick='return gerenciar_objeto(event, this);' style='visibility: hidden;'>Gerenciar Objeto</a></div>";

	evento.preventDefault();
	return false;
}

/******************
function novo_planeta_recurso
--------------------
Insere um novo recurso numa colônia
--------
id_planeta -- id do Planeta que receberá o recurso
******************/
function novo_planeta_recurso(evento, id_planeta) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
		
	objeto_em_edicao = true; //Bloqueia a edição de outros objetos
	var tabela = document.getElementsByTagName('TABLE')[0];
	
	var linha_nova = tabela.insertRow(-1);
	var id = linha_nova.insertCell(0);
	var nome_recurso = linha_nova.insertCell(1);
	var disponivel = linha_nova.insertCell(2);
	var turno = linha_nova.insertCell(3);
	
	var lista_recursos = lista_recursos_html();
	
	id.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='id_planeta' data-ajax='true' data-valor-original='"+id_planeta+"' value='"+id_planeta+"'></input>"
	+"<input type='hidden' data-atributo='id_recurso' data-ajax='true' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_planeta_recurso'></input>"
	+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir este recurso?'></input>"
	+"<div data-atributo='id' data-valor-original=''>#</div>"
	+"<div><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	nome_recurso.innerHTML = "<div data-atributo='nome_recurso' data-editavel='true' data-type='select' data-funcao='lista_recursos_html' data-id-selecionado='' data-valor-original=''>"+lista_recursos+"</div>";
	disponivel.innerHTML = "<div data-atributo='disponivel' data-editavel='true' data-style='width: 50px;'><input type='text' data-atributo='disponivel' data-ajax='true' style='width: 50px;'></input></div>";
	turno.innerHTML = "<div data-atributo='turno' data-editavel='true' data-style='width: 50px;'><input type='text' data-atributo='turno' data-ajax='true' style='width: 50px;'></input></div>";

	window.scrollTo(0, document.body.scrollHeight);	
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
	var id = linha_nova.insertCell(0);
	var nome_instalacao = linha_nova.insertCell(1);
	var nivel = linha_nova.insertCell(2);
	var turno = linha_nova.insertCell(3);
	var turno_destroi = linha_nova.insertCell(4);		
	var gerencia = linha_nova.insertCell(5);
	
	var lista_instalacao = lista_instalacoes_html();
	
	id.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='id_planeta' data-ajax='true' data-valor-original='"+id_planeta+"' value='"+id_planeta+"'></input>"
	+"<input type='hidden' data-atributo='id_instalacao' data-ajax='true' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_colonia_instalacao'></input>"
	+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir esta instalação?'></input>"
	+"<div data-atributo='id' data-valor-original=''>#</div>"
	+"<div><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	nome_instalacao.innerHTML = "<div data-atributo='nome_instalacao' data-editavel='true' data-type='select' data-funcao='lista_instalacoes_html' data-id-selecionado='' data-valor-original=''>"+lista_instalacao+"</div>";
	nivel.innerHTML = "<div data-atributo='nivel' data-editavel='true' data-style='width: 30px;'><input type='text' data-atributo='nivel' data-ajax='true' style='width: 30px;'></input></div>";
	turno.innerHTML = "<div data-atributo='turno' data-editavel='true' data-style='width: 30px;'><input type='text' data-atributo='turno' data-ajax='true' style='width: 30px;'></input></div>";
	turno_destroi.innerHTML = "<div data-atributo='turno_destroi' data-valor-original=''>#</div>";
	gerencia.innerHTML = "<div data-atributo='gerenciar' data-valor-original=''><a href='#' onclick='return destruir_instalacao(event, this);' style='visibility: hidden;'>Destruir Instalação</a></div>";

	window.scrollTo(0, document.body.scrollHeight);
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
	var nome = linha_nova.insertCell(0);
	var tipo = linha_nova.insertCell(1);
	var qtd = linha_nova.insertCell(2);
	var X = linha_nova.insertCell(3);
	var Y = linha_nova.insertCell(4);
	var Z = linha_nova.insertCell(5);
	var tamanho = linha_nova.insertCell(6);
	var PDF_laser = linha_nova.insertCell(7);
	var PDF_torpedo = linha_nova.insertCell(8);
	var PDF_projetil = linha_nova.insertCell(9);
	var blindagem = linha_nova.insertCell(10);
	var escudos = linha_nova.insertCell(11);
	var velocidade = linha_nova.insertCell(12);
	var alcance = linha_nova.insertCell(13);
	var HP = linha_nova.insertCell(14);
	var turno = linha_nova.insertCell(15);
	var especiais = linha_nova.insertCell(16);
	var gerenciar = linha_nova.insertCell(17);
	
	nome.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='"+id_imperio+"' value='"+id_imperio+"'></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>"
	+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir esta Frota?'></input>"
	+"<div data-atributo='nome' data-editavel='true' data-valor-original='' data-style='width: 100px;'><input type='text' data-atributo='nome' data-ajax='true' style='width: 80px;'></input></div>"
	+"<div><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	tipo.innerHTML = "<td><div data-atributo='tipo' data-editavel='true' data-valor-original='' data-style='width: 100px;'><input type='text' data-atributo='tipo' data-ajax='true' style='width: 100px;'></input></div></td>";
	qtd.innerHTML = "<td><div data-atributo='qtd' data-editavel='true' data-valor-original='' data-style='width: 30px;'><input type='text' data-atributo='qtd' data-ajax='true' style='width: 30px;'></input></div></td>";
	X.innerHTML = "<td><div data-atributo='X' data-editavel='true' data-valor-original='' data-style='width: 30px;'><input type='text' data-atributo='X' data-ajax='true' style='width: 30px;'></input></div></td>";
	Y.innerHTML = "<td><div data-atributo='Y' data-editavel='true' data-valor-original='' data-style='width: 30px;'><input type='text' data-atributo='Y' data-ajax='true' style='width: 30px;'></input></div></td>";
	Z.innerHTML = "<td><div data-atributo='Z' data-editavel='true' data-valor-original='' data-style='width: 30px;'><input type='text' data-atributo='Z' data-ajax='true' style='width: 30px;'></input></div></td>";
	tamanho.innerHTML = "<td><div data-atributo='tamanho' data-editavel='true' data-valor-original='' data-style='width: 50px;'><input type='text' data-atributo='tamanho' data-ajax='true' style='width: 50px;'></input></div></td>";
	PDF_laser.innerHTML = "<td><div data-atributo='PDF_laser' data-editavel='true' data-valor-original='' data-style='width: 50px;'><input type='text' data-atributo='PDF_laser' data-ajax='true' style='width: 50px;'></input></div></td>";
	PDF_torpedo.innerHTML = "<td><div data-atributo='PDF_torpedo' data-editavel='true' data-valor-original='' data-style='width: 50px;'><input type='text' data-atributo='PDF_torpedo' data-ajax='true' style='width: 50px;'></input></div></td>";
	PDF_projetil.innerHTML = "<td><div data-atributo='PDF_projetil' data-editavel='true' data-valor-original='' data-style='width: 50px;'><input type='text' data-atributo='PDF_projetil' data-ajax='true' style='width: 50px;'></input></div></td>";
	blindagem.innerHTML = "<td><div data-atributo='blindagem' data-editavel='true' data-valor-original='' data-style='width: 50px;'><input type='text' data-atributo='blindagem' data-ajax='true' style='width: 50px;'></input></div></td>";
	escudos.innerHTML = "<td><div data-atributo='escudos' data-editavel='true' data-valor-original='' data-style='width: 50px;'><input type='text' data-atributo='escudos' data-ajax='true' style='width: 50px;'></input></div></td>";
	velocidade.innerHTML = "<td><div data-atributo='velocidade' data-editavel='true' data-valor-original='' data-style='width: 50px;'><input type='text' data-atributo='velocidade' data-ajax='true' style='width: 50px;'></input></div></td>";
	alcance.innerHTML = "<td><div data-atributo='alcance' data-editavel='true' data-valor-original='' data-style='width: 50px;'><input type='text' data-atributo='alcance' data-ajax='true' style='width: 50px;'></input></div></td>";
	HP.innerHTML = "<td><div data-atributo='HP' data-editavel='true' data-valor-original='' data-style='width: 50px;'><input type='text' data-atributo='HP' data-ajax='true' style='width: 50px;'></input></div></td>";
	turno.innerHTML = "<td><div data-atributo='turno' data-editavel='true' data-valor-original='' data-style='width: 50px;'><input type='text' data-atributo='turno' data-ajax='true' style='width: 50px;'></input></div></td>";
	especiais.innerHTML = "<td><div data-atributo='especiais' data-editavel='true' data-valor-original='' data-style='width: 120px;' data-branco='true'><input type='text' data-atributo='especiais' data-ajax='true' style='width: 120px;' data-branco='true'></input></div></td>";
	gerenciar.innerHTML = "<div data-atributo='gerenciar' data-valor-original=''><a href='#' onclick='return copiar_objeto(event, this,"+id_imperio+");' style='visibility: hidden;'>Criar cópia</a></div>";

	window.scrollTo(0, document.body.scrollHeight);
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
	var nome_imperio = linha_nova.insertCell(0);
	var lista_recursos = linha_nova.insertCell(1);
	var descricao = linha_nova.insertCell(2);
	var turno = linha_nova.insertCell(3);
	
	var lista_imperios = lista_imperios_html();
	
	nome_imperio.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_acao_admin'></input>"
	+"<input type='hidden' data-atributo='funcao_pos_processamento' value='altera_lista_recursos_qtd'></input>"
	+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Remover esta Ação não irá reverter seus efeitos. Deseja continuar?'></input>"
	+"<div data-atributo='nome_imperio' data-editavel='true' data-type='select' data-funcao='lista_imperios_html' data-id-selecionado='' data-valor-original=''>"+lista_imperios+"</div>"
	+"<div><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";

	lista_recursos.innerHTML = "<div data-atributo='lista_recursos_qtd' data-valor-original=''>&nbsp;</div>"
	+"<div style='visibility: hidden;'><div data-atributo='lista_recursos' data-editavel='true' data-style='width: 120px;' data-valor-original='' style='display: inline-block;'><input type='text' data-atributo='lista_recursos' data-ajax='true' style='width: 120px;'></input></div>"
	+ "&nbsp; || &nbsp; <div data-atributo='qtd' data-editavel='true' data-style='width: 120px;' data-valor-original='' style='display: inline-block;'><input type='text' data-atributo='qtd' data-ajax='true' style='width: 120px;'></input></div></div>";

	descricao.innerHTML = "<div data-atributo='descricao' data-editavel='true' data-valor-original='' data-style='width: 200px;'><input type='text' data-atributo='descricao' data-ajax='true' data-valor-original='' style='width: 200px;'></input></div>";
	turno.innerHTML = "<div data-atributo='turno' data-editavel='true' data-style='width: 30px;' data-valor-original=''><input type='text' data-atributo='turno' data-ajax='true' style='width: 30px;'></input></div>";
	
	window.scrollTo(0, document.body.scrollHeight);
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
			var resposta = JSON.parse(this.responseText);
			let div_resultados = document.getElementById('resultado_turno');
			div_resultados.innerHTML = resposta.html;
			if (resposta.turno_novo != "") {
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