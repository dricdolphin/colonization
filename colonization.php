<?php
/**
 * Plugin Name: Colonization
 * Plugin URI: https://github.com/dricdolphin/colonization
 * Description: Plugin de WordPress com o sistema de jogo de Colonization.
 * Version: 0.1
 * Author: dricdolphin
 * Author URI: https://dricdolphin.com
 */

//Inclui os arquivos necessários para o sistema "Colonization"
include_once('./includes/geral.php');
include_once('./includes/imperio.php');
include_once('./includes/planeta.php');
include_once('./includes/instalacao.php');

//Ganchos de instalação e desinstalação do plugin "Colonization"
register_activation_hook( __FILE__, 'colonization_install' );

register_deactivation_hook( __FILE__, 'colonization_deactivate' );

/******************
function colonization_install()
-----------
Instala o plugin e cria os objetos necessários para rodar o sistema "Colonization"
******************/
function colonization_install() {
//TODO - Sistema de instalação
//Cria o banco de dados

//Adiciona os "shortcodes" que serão utilizados para exibir os dados do Império
add_shortcode("colonization_exibe_imperio","colonization_exibe_imperio"); //Exibe os dados do Império

}

/******************
function colonization_deactivate()
-----------
Desinstala o plugin.
******************/
function colonization_deactivate() {
//TODO - Rotinas de desativação
}
 
?>