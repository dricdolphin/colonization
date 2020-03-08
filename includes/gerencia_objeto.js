/******************
function gerenciar_objeto(objeto)
--------------------
Abre a página de gerenciamento de informações acessórias de um objeto
objeto -- objeto sendo editado
redireciona -- Se é para redirecionar para outra página
******************/
function gerenciar_objeto(objeto, redireciona = "") {
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
	console.log(url_gerencia);
	window.location = url_gerencia;
}