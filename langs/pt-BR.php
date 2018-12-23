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

$moduleError = ("Um módulo/extensão do PHP essencial para o funcionamento do NagMap Reborn não foi encontrado, por favor instale o módulo/extensão antes de continuar. Nome do módulo/extenssão: ");

$file_not_find_error = ("não foi encontrado! Por favor defina corretamente a variável no arquivo de configuração do NagMap Reborn!");

$in_definition_error = ("Começar um novo \"in_definition\" sem finalizar o anterior, não é legal!");

$no_data_error = ("Não existem dados a serem exibidos, ou você não definiu as configurações corretamente ou esse é um bug do sistema.<br>Por favor entre em contato através do e-mail joao_carlos.r@hotmail.com para obter assistência.");

$reported = (" reportado.");

$errorFound = ("Um erro foi automaticamente reportado.");

$reporterErrorPre =("Ocorreu um erro porém ele não pode ser reportado!");

$reporterError =("Essa versão do NagMap Reborn não recebe mais suporte para erros. Por favor utilize a <a href='https://github.com/jocafamaka/nagmapReborn/releases'>versão mais recente!</a>");

$reporterErrorOF =("Não foi possível reportar o erro, porque um ou mais arquivos principais do projeto foram modificados!");

$emptyUserPass = ("Usuário e/ou senha de autenticação não foram definidos, defina usuário e senha no arquivo de configuração.");

$updateError = ("Ocorreu um problema na atualização do status dos hosts, os status exibidos podem estar desatualizados, verifique o console para mais informações.");

$updateErrorServ = ("Esse tipo de erro está normalmente relacionado ao seguinte problema: O servidor está inacessível ou recussando conexões, verifique o servidor e sua conexão.");

$updateErrorStatus = ("Esse tipo de erro está normalmente relacionado ao seguinte problema: O arquivo de status esta inacessível ou não existe, verifique se o serviço de monitoramento está executando corretamente.");

$updateErrorChanges = ("Esse tipo de erro está normalmente relacionado ao seguinte problema: Aconteceu modificações nos hosts, adição, remoção ou edição de nomes, nesses caso atualize a página.");

$updateErrorSolved = ("Problema resolvido, os status exibidos estão em tempo real.");

//Informações do debug:
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

$newVersion = ("Atualização disponível");

$newVersionFooter = ("Irá fechar após 10 segundos.");

$newVersionText = ("<br>A versão do NagMap Reborn utilizada atualmente está desatualizada!<br><br>Obtenha a nova versão no GitHub:<br><br>");

$passAlertTitle = ("Autenticação padrão");

$passAlert = ("Você está atualmente utilizando a senha e usuário padrão, não fique desprotegido, modifique agora mesmo!");

$asFilter = ("Usar como filtro");

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

$filter = ("Filtrar");

$clear = ("Limpar");

//Debug page
$debugTitle = ("Info. de depuração");

$updating = ("Atualizando");

$mainPage = ("Página principal");

$project = ("Projeto no GitHub");

$btop = ("Voltar ao topo");

$starting = ("Iniciando, aguarde.");

$stopped = ("Parado");

$downData = ("Baixar dados");

$verifications = ("Verificações (estático)");

$ignHosts = ("Hosts ignorados (estático)");

$statusFile = ("Informações do arquivo de status (dinâmico)");

$hostName = ("Nome do host");

$reasons = ("Motivo(s)");

$tServ = ("O serviço ");

$tHost = ("O host ");

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

$isunk = ("tem um status desconhecido");

$controlInfo = ("Para/Continua a atualização das informações");

$appStatus = ("Status atual da aplicação");

$noLatLng = ("Não possui LatLng no arquivo de definição");

$noHostN = ("Não possui HostName");

$noStatus = ("Não existe no arquivo de Status");

$outFilterHg = ("Não está no HostGroup filtrado.");

$help = ("Ajuda");

$close = ("Fechar");

$primary = (" (Primário)");

$debugHelp = ('Essa página contem informações úteis na hora de solicitar suporte!<br><br>

As caractéristicas da páginas são essas:<br><br>

<strong>1 - Verificações.</strong><br>
     - Exibe informações sobre o controle de acesso.<br>
     - Exibe informações sobre reporte automático de erros.<br>
     - Informa dados e avisos importantes sobre os dados reportados.<br><br>

<strong>2 - Hosts que foram ignorados.</strong><br>
     - Exibe todos os hosts ignorados.<br>
     - Informa o nome do host.<br>
     - O aliás do host.<br>
     - O motivos ou motivos daquele host ter sido ignorado.<br>
     - Os motivos podem ser bem úteis para definir se foi um erro de configuração ou bug da aplicação.<br><br>

<strong>3 - Informações importantes sobre cada host existente no arquivo de Status.</strong><br>
     - A cor do Card indica qual o status do host ou serviço em questão.<br>
       - Verde: ok; Amarelo: alerta; Laranja: crítico; Cinza: desconhecido.<br>
     - Mostra informações sobre status interno.<br>
     - Exibe os valores de tempo para varios parametros.<br>
     - Exibe o tempo em formato Epoch e o tempo em horas e minutos.<br><br>

<strong>4 - No rodapé da página existe o controlador de atualização das informações da página.</strong><br>
     - É possivél parar a atualização a qualquer momento, útil para capturar acontecimentos rápidos.<br>
     - Também existe um botão de download que baixa um arquivo com as informações presentes na página no exato momento.<br>
     - O botão de download fica desabilitado durante as atualizações de informações da página.<br><br>

<strong>Sempre que for solicitar suporte</strong> acesse a página de debug faça o download do arquivo e envie em anexo a sua solicitação, esse procedimento, pode e irá tornar a resolução de problemas mais rapida.<br><br>

Você pode obter suporte me contatando através do e-mail: <strong>joao_carlos.r@hotmail.com</strong>');

// Autenticação

$authFail = ("Falha de autenticação! Tente novamente.");

$noAuthDanger = ("<strong>Perigo: </strong> Atualmente o controle de acesso está desabilitado!");

$defaultPassUser = ("<strong>Atenção: </strong> ".$passAlert);

$AuthOk = ("<strong>Tudo certo: </strong> O controle de acesso está habilitado e foi realizada alteração do usuário e senha padrão.");

$reportOffOF = ("<strong>Perigo: </strong> Um ou mais arquivos principais do projeto foram modificados, por esse motivo, não é possível reportar erros automáticamente.");

$reportOff = ("<strong>Atenção: </strong> O reporte automático de erros está desabilitado!");

$reportOk = ("<strong>Tudo certo: </strong> Todos os erros são reportados automáticamente!");

$reportDataRequestP1 = ('*Para solicitar todos os dados recolhidos dos reportes de erros, por favor envie um email para: joao_carlos.r@hotmail.com
<br>No assunto: Data of the report of errors.
<br>No corpo do e-mail informe o domínio (url) pelo qual o serviço é acessado e o seu');

$reportDataRequestP2 = ('token de report');

$reportDataRequestP3 = ('<br>Sua solicitação será atendida o mais breve possível, caso necessário por motivos de segurança, serão solicitadas outras formas de verificação do domínio.');

$yourRToken = ("Seu token de reporte");

$accessControl =("Controle de acesso");

$errorReporting = ("Reporte de erros");

$reportCountP1 = ("<strong>Dados: </strong>Você fez");

$reportCountP2 = ("reporte(s) válido(s) nos últimos 7 dias.");
?>