<?php 	// Arquivo que define as variavés de linguagem.
/*
 * ##################################################################
 * #             ALL CREDITS FOR MODIFICATIONS ARE HERE             #
 * ##################################################################
 *
 * KEEP THE PATTERN
 *
 * Original Credits: João Ribeiro (https://github.com/jocafamaka) in 04 March 2018
 *
 */

//ERROS:
$var_cfg_error = ("não foi corretamente configurada, verifique o arquivo de configuração do NagMap Reborn e faça as correções necessárias! Valor definido: ");

$file_not_find_error = ("não foi encontrado! Por favor defina corretamente a variável no arquivo de configuração do NagMap Reborn!");

$in_definition_error = ("Começar um novo \"in_definition\" sem finalizar o anterior, não é legal!");

$one_column_error1 = ("Linha do arquivo de configuração (");

$one_column_error2 = (") que contém somente uma coluna, não está correta! Arquivo e linha: ");

$no_data_error = ("Não existem dados a serem exibidos, ou você não definiu as configurações corretamente ou esse é um bug do sistema.<br>Por favor entre em contato através do e-mail joao_carlos.r@hotmail.com para obter assistência.");

//Informações do debug:
$ignoredHosts = ("Esse host foi ignorado: ");

$positionHosts = ("Posição do host: ");

$message = ("Mensagem:");

$lineNum = ("Número da linha:");

$error = ("Erro");

$at = ("As:");

//Bolhas de informação:
$alias = ("Aliás");

$hostG = ("Grupo de hosts");

$addr = ("Endereço");

$other = ("Outros");

$hostP = ("Parentes");

$newVersion = ("Nova versão disponível");

$newVersionText = ("A versão do NagMap Reborn que você está usando atualmente não está atualizada!<br><br>Faça o download da nova versão para ter acesso as novidades e melhorias.<br><br>Encontre a nova versão no GitHub:<br><br>");

//Alertas ChangesBar:
$up = ("UP");

$down = ("DOWN");

$warning = ("EM ALERTA");

$unknown = ("DESCONHECIDO");

$critical = ("CRÍTICO");

$and = ("e");

$waiting = ("Aguardando");

$timePrefix = ('Há ');

$timeSuffix = ('');

//Debug page
$debugTitle = ("Info. de depuração");

$debugInfo = ("Essa página contém informações importantes que podem ajudar em caso de bug. Entre essas informações estão os hosts ignorados com o motivo, além de informações adicionais sobre cada um dos hosts presentes no aquivo de Status.");

$updating = ("Atualizando");

$mainPage = ("Página principal");

$project = ("Projeto no GitHub");

$btop = ("Voltar ao topo");

$starting = ("Iniciando, aguarde.");

$stopped = ("Parado");

$downData = ("Baixar dados");

$ignHosts = ("Hosts ignorados (estático)");

$statusFile = ("Informações do arquivo de status (dinâmico)");

$hostName = ("Nome do host");

$reasons = ("Motivo(s)");

$cs = ("Estado atual");

$lhs = ("Último pior estado");

$lsc = ("Última mudança de estado");

$lhsc = ("Última mudança de pior estado");

$ltup = ("Última vez up");

$ltd = ("Última vez down");

$ltun = ("Última vez inacessível");

$lto = ("Última vez ok");

$ltw = ("Última vez em alerta");

$ltunk = ("Última vez estado descohecido");

$ltc = ("Última vez estato crítico");

$isUp = ("está up");

$isDown = ("está down");

$inWar = ("está em alerta");

$incrit = ("está crítico");

$isunk = ("desconhecido");

$controlInfo = ("Para/Continua a atualização das informações");

$appStatus = ("Status atual da aplicação");

$noLatLng = ("Não possui LatLng no arquivo de definição");

$noHostN = ("Não possui HostName");

$noStatus = ("Não existe no arquivo de Status");

$help = ("Ajuda");

$close = ("Fechar");

$debugHelp = ('Essa página contem informações úteis na hora de solicitar suporte!<br><br>

As caractéristicas da páginas são essas:<br><br>

1 - Hosts que foram ignorados.<br>
     - Exibe todos os hosts ignorados.<br>
     - Informa o nome do host.<br>
     - O aliás do host.<br>
     - O motivos ou motivos daquele host ter sido ignorado.<br>
     - Os motivos podem ser bem úteis para definir se foi um erro de configuração ou bug da aplicação.<br><br>

2 - Informações importantes sobre cada host existente no arquivo de Status.<br>
     - A cor do Card indica qual o status do host em questão.<br>
     - Mostra informações sobre stats interno.<br>
     - Exibe os valores de tempo para varios parametros.<br>
     - Exibe o tempo em formato Epoch e o tempo em horas e minutos.<br><br>

3 - No rodapé da página existe o controlador de atualização das informações da página.<br>
     - É possivél parar a atualização a qualquer momento, útil para capturar acontecimentos rápidos.<br>
     - Também existe um botão de download que baixa um arquivo com as informações presentes na página no exato momento.<br>
     - O botão de download fica desabilitado durante as atualizações de informações da página.<br><br>

Sempre que for solicitar suporte acesse a página de debug faça o download do arquivo e envie em anexo a sua solicitação, esse procedimento, pode e irá tornar a resolução de problemas mais rapida.<br><br>

Você pode obter suporte me contatando através do e-mail: joao_carlos.r@hotmail.com');
?>