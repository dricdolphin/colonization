/******************
function novo_imperio
--------------------
Insere um novo Império na lista
******************/
function novo_imperio() {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		return false;
	}
		
		objeto_em_edicao = true; //Bloqueia a edição de outros Impérios
		var tabela_imperios = document.getElementsByTagName('TABLE')[0];
		var linha_nova = tabela_imperios.insertRow(-1);
		
		var id = linha_nova.insertCell(0);
		var dados_jogador = linha_nova.insertCell(1);
		var nome_imperio = linha_nova.insertCell(2);
		var populacao = linha_nova.insertCell(3);
		var pontuacao = linha_nova.insertCell(4);
		
		var lista_jogadores = lista_jogadores_html(); //Pega a lista de usuários do Fórum
		
		
		id.innerHTML = "<input type='hidden' data-atributo='id_jogador' data-valor-original='' value=''></input>"
		+"<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
		+"<input type='hidden' data-atributo='where_clause' value='id_jogador'></input>"
		+"<input type='hidden' data-atributo='where_value' value=''></input>"
		+"<input type='hidden' data-atributo='funcao_validacao' value='valida_imperio'></input>"
		+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value=''></input>"
		+"<div data-atributo='id' data-valor-original=''>#</div>"
		+"<div><a href='#' onclick='salva_objeto(this);'>Salvar</a> | <a href='#' onclick='cancela_edicao(this);'>Cancelar</a></div>";
		dados_jogador.innerHTML = "<div data-atributo='nome_jogador' data-id-selecionado='0'>"+lista_jogadores+"</div>";
		nome_imperio.innerHTML = "<div data-atributo='nome' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nome' data-ajax='true'></input></div>";
		populacao.innerHTML = "<div data-atributo='pop' data-valor-original=''></div>";
		pontuacao.innerHTML = "<div data-atributo='poluicao' data-valor-original=''></div>";
}

/******************
function nova_estrela
--------------------
Insere uma nova estrela na lista
******************/
function nova_estrela() {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
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
		
		nome_estrela.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
		+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
		+"<input type='hidden' data-atributo='where_value' value=''></input>"
		+"<input type='hidden' data-atributo='funcao_validacao' value='valida_estrela'></input>"
		+"<div data-atributo='nome' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nome' data-ajax='true'></input></div>"
		+"<div><a href='#' onclick='salva_objeto(this);'>Salvar</a> | <a href='#' onclick='cancela_edicao(this);'>Cancelar</a></div>";
		estrela_x.innerHTML = "<div data-atributo='X' data-style='width: 100%;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='X' data-ajax='true' style='width: 100%;'></input></div>";
		estrela_y.innerHTML = "<div data-atributo='Y' data-style='width: 100%;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='Y' data-ajax='true' style='width: 100%;'></input></div>";
		estrela_z.innerHTML = "<div data-atributo='Z' data-style='width: 100%;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='Z' data-ajax='true' style='width: 100%;'></input></div>";
		estrela_tipo.innerHTML = "<div data-atributo='tipo' data-editavel='true' data-valor-original=''><input type='text' data-atributo='tipo' data-ajax='true'></input></div>";
}

/******************
function novo_planeta
--------------------
Insere um novo Planeta na lista
******************/
function novo_planeta() {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		return false;
	}
		
		objeto_em_edicao = true; //Bloqueia a edição de outros objetos
		var tabela = document.getElementsByTagName('TABLE')[0];
		var linha_nova = tabela.insertRow(-1);
		var nome = linha_nova.insertCell(0);
		var estrela = linha_nova.insertCell(1);
		var posicao = linha_nova.insertCell(2);
		var classe = linha_nova.insertCell(3);
		var subclasse = linha_nova.insertCell(4);
		var tamanho = linha_nova.insertCell(5);
		var gerencia = linha_nova.insertCell(6);
		
		var lista_estrelas = lista_estrelas_html();
		
		nome.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
		+"<input type='hidden' data-atributo='id_estrela' value=''></input>"
		+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
		+"<input type='hidden' data-atributo='where_value' value=''></input>"
		+"<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>"
		+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir este planeta e todas suas ligações (recursos, instalações etc)?'></input>"
		+"<div data-atributo='nome' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nome' data-ajax='true'></input></div>"
		+"<div><a href='#' onclick='salva_objeto(this);'>Salvar</a> | <a href='#' onclick='cancela_edicao(this);'>Cancelar</a></div>";
		estrela.innerHTML = "<div data-atributo='nome_estrela' data-id-selecionado='0'>"+lista_estrelas+"</div>";
		posicao.innerHTML = "<div data-atributo='posicao' data-style='width: 30px;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='posicao' data-ajax='true' style='width: 30px;'></input></div>";
		classe.innerHTML = "<div data-atributo='classe' data-editavel='true' data-valor-original=''><input type='text' data-atributo='classe' data-ajax='true'></input></div>";
		subclasse.innerHTML = "<div data-atributo='subclasse' data-editavel='true' data-valor-original=''><input type='text' data-atributo='subclasse' data-ajax='true'></input></div>";
		tamanho.innerHTML = "<div data-atributo='tamanho' data-style='width: 30px;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='tamanho' data-ajax='true' style='width: 30px;'></input></div>";
		gerencia.innerHTML = "<div data-atributo='gerenciar' data-valor-original=''><a href='#' onclick='gerenciar_objeto(this);' style='visibility: hidden;'>Gerenciar Objeto</a></div>";
}


/******************
function novo_recurso
--------------------
Insere um novo recurso
******************/
function novo_recurso() {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		return false;
	}
		
		objeto_em_edicao = true; //Bloqueia a edição de outros Impérios
		var tabela = document.getElementsByTagName('TABLE');
		tabela = tabela[0];
		var linha_nova = tabela.insertRow(-1);
		
		var nome = linha_nova.insertCell(0);
		var descricao = linha_nova.insertCell(1);
		var acumulavel = linha_nova.insertCell(2);
		
		nome.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
		+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
		+"<input type='hidden' data-atributo='where_value' value=''></input>"
		+"<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>"
		+"<div data-atributo='nome' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nome' data-ajax='true'></input></div>"
		+"<div><a href='#' onclick='salva_objeto(this);'>Salvar</a> | <a href='#' onclick='cancela_edicao(this);'>Cancelar</a></div>";
		descricao.innerHTML = "<div data-atributo='descricao' data-editavel='true' data-valor-original=''><input type='text' data-atributo='descricao' data-ajax='true'></input></div>";
		acumulavel.innerHTML = "<div data-atributo='acumulavel' data-type='checkbox' data-editavel='true' data-valor-original='1'><input type='checkbox' data-atributo='acumulavel' data-ajax='true' checked></input></div>";
}

/******************
function nova_instalacao
--------------------
Insere uma nova instalação
******************/
function nova_instalacao() {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		return false;
	}
		
		objeto_em_edicao = true; //Bloqueia a edição de outros Impérios
		var tabela = document.getElementsByTagName('TABLE');
		tabela = tabela[0];
		var linha_nova = tabela.insertRow(-1);
		
		var nome = linha_nova.insertCell(0);
		var descricao = linha_nova.insertCell(1);
		var gerencia = linha_nova.insertCell(2);
		
		nome.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
		+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
		+"<input type='hidden' data-atributo='where_value' value=''></input>"
		+"<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>"
		+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir esta instalação e todas suas ligações (recursos produzidos, consumidos etc)?'></input>"
		+"<div data-atributo='nome' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nome' data-ajax='true'></input></div>"
		+"<div><a href='#' onclick='salva_objeto(this);'>Salvar</a> | <a href='#' onclick='cancela_edicao(this);'>Cancelar</a></div>";
		descricao.innerHTML = "<div data-atributo='descricao' data-editavel='true' data-valor-original=''><input type='text' data-atributo='descricao' data-ajax='true'></input></div>";
		gerencia.innerHTML = "<div data-atributo='gerenciar' data-valor-original=''><a href='#' onclick='gerenciar_objeto(this);' style='visibility: hidden;'>Gerenciar Objeto</a></div>";
}

/******************
function novo_instalacao_recurso(consome = 1)
--------------------
Insere um novo recurso atrelado à instalação
consome = 1 -- O recurso é produzido (0) ou consumido (1)
******************/
function novo_instalacao_recurso(consome = 1) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		return false;
	}
		
		objeto_em_edicao = true; //Bloqueia a edição de outros objetos
		var tabela = document.getElementsByTagName('TABLE')[consome];
		var linha_nova = tabela.insertRow(-1);
		var id = linha_nova.insertCell(0);
		var recurso = linha_nova.insertCell(1);
		var qtd_por_nivel = linha_nova.insertCell(2);

		
		var lista_recursos = lista_recursos_html();
		
		id.innerHTML = 	"<input type='hidden' data-atributo='id' value=''></input>"
		+"<input type='hidden' data-atributo='id_instalacao' data-ajax='true' value='"+id_instalacao+"'></input>"
		+"<input type='hidden' data-atributo='id_recurso' data-ajax='true' value=''></input>"
		+"<input type='hidden' data-atributo='consome' data-ajax='true' value='"+consome+"'></input>"
		+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
		+"<input type='hidden' data-atributo='where_value' value=''></input>"
		+"<input type='hidden' data-atributo='funcao_validacao' value='valida_instalacao_recurso'></input>"
		+"<div data-atributo='id' data-valor-original=''>#</div>"
		+"<div><a href='#' onclick='salva_objeto(this);'>Salvar</a> | <a href='#' onclick='cancela_edicao(this);'>Cancelar</a></div>";
		recurso.innerHTML = "<div data-atributo='nome_recurso' data-editavel='true' data-type='select' data-funcao='lista_recursos_html' data-id-selecionado = '0' data-valor-original=''>"+lista_recursos+"</div>";
		qtd_por_nivel.innerHTML = "<div data-atributo='qtd_por_nivel' data-style='width: 50px;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='qtd_por_nivel' data-ajax='true' style='width: 50px;'></input></div>";
}

/******************
function nova_colonia
--------------------
Insere uma nova Colônia
--------
id_imperio -- id do Império que receberá a colônia
******************/
function nova_colonia(id_imperio) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
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
		+"<div><a href='#' onclick='salva_objeto(this);'>Salvar</a> | <a href='#' onclick='cancela_edicao(this);'>Cancelar</a></div>";
		nome_planeta.innerHTML = "<div data-atributo='nome_planeta' data-editavel='true' data-type='select' data-funcao='lista_planetas_html' data-id-selecionado='' data-valor-original=''>"+lista_planetas+"</div>";
		pop.innerHTML = "<div data-atributo='pop' data-editavel='true' data-style='width: 30px;'><input type='text' data-atributo='pop' data-ajax='true' style='width: 30px;'></input></div>";
		poluicao.innerHTML = "<div data-atributo='poluicao' data-editavel='true' data-style='width: 30px;'><input type='text' data-atributo='poluicao' data-ajax='true' style='width: 30px;'></input></div>";
		turno.innerHTML = "<div data-atributo='turno' data-editavel='true' data-style='width: 30px;'><input type='text' data-atributo='turno' data-ajax='true' style='width: 30px;'></input></div>";
		gerencia.innerHTML = "<div data-atributo='gerenciar' data-valor-original=''><a href='#' onclick='gerenciar_objeto(this);' style='visibility: hidden;'>Gerenciar Objeto</a></div>";
}

/******************
function novo_colonia_recurso
--------------------
Insere um novo recurso numa colônia
--------
id_planeta -- id do Planeta que receberá o recurso
******************/
function novo_colonia_recurso(id_planeta) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		return false;
	}
		
		objeto_em_edicao = true; //Bloqueia a edição de outros objetos
		var tabela = document.getElementsByTagName('TABLE')[0];
		
		var linha_nova = tabela.insertRow(-1);
		var id = linha_nova.insertCell(0);
		var nome_recurso = linha_nova.insertCell(1);
		var disponivel = linha_nova.insertCell(2);
		
		var lista_recursos = lista_recursos_html();
		
		id.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
		+"<input type='hidden' data-atributo='id_planeta' data-ajax='true' data-valor-original='"+id_planeta+"' value='"+id_planeta+"'></input>"
		+"<input type='hidden' data-atributo='id_recurso' data-ajax='true' data-valor-original='' value=''></input>"
		+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
		+"<input type='hidden' data-atributo='where_value' value=''></input>"
		+"<input type='hidden' data-atributo='funcao_validacao' value='valida_colonia_recurso'></input>"
		+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir este recurso?'></input>"
		+"<div data-atributo='id' data-valor-original=''>#</div>"
		+"<div><a href='#' onclick='salva_objeto(this);'>Salvar</a> | <a href='#' onclick='cancela_edicao(this);'>Cancelar</a></div>";
		nome_recurso.innerHTML = "<div data-atributo='nome_recurso' data-editavel='true' data-type='select' data-funcao='lista_recursos_html' data-id-selecionado='' data-valor-original=''>"+lista_recursos+"</div>";
		disponivel.innerHTML = "<div data-atributo='disponivel' data-editavel='true' data-style='width: 50px;'><input type='text' data-atributo='disponivel' data-ajax='true' style='width: 50px;'></input></div>";
}

/******************
function nova_colonia_instalacao
--------------------
Insere uma nova Instalação no Planeta
--------
id_planeta -- id do Planeta que receberá a Instalação
******************/
function nova_colonia_instalacao(id_planeta) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
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
		+"<div><a href='#' onclick='salva_objeto(this);'>Salvar</a> | <a href='#' onclick='cancela_edicao(this);'>Cancelar</a></div>";
		nome_instalacao.innerHTML = "<div data-atributo='nome_instalacao' data-editavel='true' data-type='select' data-funcao='lista_instalacoes_html' data-id-selecionado='' data-valor-original=''>"+lista_instalacao+"</div>";
		nivel.innerHTML = "<div data-atributo='nivel' data-editavel='true' data-style='width: 30px;'><input type='text' data-atributo='nivel' data-ajax='true' style='width: 30px;'></input></div>";
		turno.innerHTML = "<div data-atributo='turno' data-editavel='true' data-style='width: 30px;'><input type='text' data-atributo='turno' data-ajax='true' style='width: 30px;'></input></div>";
		turno_destroi.innerHTML = "<div data-atributo='turno_destroi' data-valor-original=''>#</div>";
		gerencia.innerHTML = "<div data-atributo='gerenciar' data-valor-original=''><a href='#' onclick='destruir_instalacao(this);' style='visibility: hidden;'>Destruir Instalação</a></div>";

/**
			<td><div data-atributo='gerenciar'><a href='#' onclick='destruir_instalacao(this);'>Destruir Instalação</a></div></td>";



**/


}