*** Settings ***
Library           RequestsLibrary
Library           DatabaseLibrary
Library           SeleniumLibrary
Library           Process

*** Variables ***
${BROWSER}                       chrome
${URL}                           http://localhost/sistema_feedback_pucpr
${MYSQL}                         /Applications/XAMPP/xamppfiles/bin/mysql
${SOCKET}                        /Applications/XAMPP/xamppfiles/var/mysql/mysql.sock
${MASSA}                         ${CURDIR}/../massa_de_dados.sql

${USUARIO_ADMIN}                 admin
${SENHA_ADMIN}                   admin123

${ID_GESTOR_1}                   1
${EMAIL_GESTOR_1}                gestor1@empresa.com
${EMAIL_GESTOR_1_NOVO}           gestor1.novo@empresa.com
${SENHA_GESTOR_1}                g1senha
${SENHA_REPETIDO_GESTOR}         abc

${ID_GESTOR_2}                   2
${EMAIL_GESTOR_2}                gestor2@empresa.com
${SENHA_GESTOR_2}                g2senha

${EMAIL_ANALISTA_1}              analista1@empresa.com
${SENHA_ANALISTA_REPETIDO}       x
${EMAIL_ANALISTA_NOVO}           analista3@empresa.com
${SENHA_ANALISTA_NOVO}           an3

${MENSAGEM_ALTERADO_SUCESSO}     Registro alterado com sucesso!
${MENSAGEM_EMAIL_CADASTRADO}     Este e-mail já está cadastrado no sistema

*** Keywords ***
Preparar o banco de dados
    @{args}=    Create List    --socket=${SOCKET}    -u    root    -e    source ${MASSA}
    ${resultado}=    Run Process    ${MYSQL}    @{args}    stderr=STDOUT
    Log    ${resultado.stdout}
    Should Be Equal As Integers    ${resultado.rc}    0    msg=Erro ao importar o banco: ${resultado.stdout}
    Connect To Database    pymysql    db_name=projeto    db_user=root    db_password=${EMPTY}    db_host=localhost    unix_socket=${SOCKET}
    
Encerrar o teste
    Run Keyword And Ignore Error    Close Browser
    Disconnect From Database
    Delete All Sessions

Fazer login como administrador
    Create Session    admin    ${URL}
    ${dados}=    Create Dictionary    usuario=${USUARIO_ADMIN}    senha=${SENHA_ADMIN}
    POST On Session    admin    /admins/php/admin_login.php    data=${dados}

Fazer login como gestor 1
    Create Session    gestor1    ${URL}
    ${dados}=    Create Dictionary    email=${EMAIL_GESTOR_1}    senha=${SENHA_GESTOR_1}
    POST On Session    gestor1    /php/gestor_login.php    data=${dados}

Fazer login como gestor 2
    Create Session    gestor2    ${URL}
    ${dados}=    Create Dictionary    email=${EMAIL_GESTOR_2}    senha=${SENHA_GESTOR_2}
    POST On Session    gestor2    /php/gestor_login.php    data=${dados}

Abrir a página de listagem de gestores sem login
    Open Browser    url=${URL}/admins/php/gestores/gestor_get.php    browser=${BROWSER}
    Maximize Browser Window

Verificar se nenhum dado de gestor aparece na tela
    Capture Page Screenshot
    Page Should Not Contain    ${EMAIL_GESTOR_1}
    Page Should Not Contain    ${SENHA_GESTOR_1}

Cadastrar gestor com e-mail repetido
    ${dados}=    Create Dictionary    email=${EMAIL_GESTOR_1}    senha=${SENHA_REPETIDO_GESTOR}
    ${RESPOSTA}=    POST On Session    admin    /admins/php/gestores/gestor_novo.php    data=${dados}
    Set Test Variable    ${RESPOSTA}

Alterar gestor 1 sem preencher a nova senha
    ${dados}=    Create Dictionary    email=${EMAIL_GESTOR_1_NOVO}
    ${RESPOSTA}=    POST On Session    admin    /admins/php/gestores/gestor_alterar.php    params=id=${ID_GESTOR_1}    data=${dados}
    Set Test Variable    ${RESPOSTA}

Cadastrar analista de dados válido
    ${dados}=    Create Dictionary    email=${EMAIL_ANALISTA_NOVO}    senha=${SENHA_ANALISTA_NOVO}
    ${RESPOSTA}=    POST On Session    gestor2    /php/analista_dados_novo.php    data=${dados}
    Set Test Variable    ${RESPOSTA}

Cadastrar analista com e-mail repetido
    ${dados}=    Create Dictionary    email=${EMAIL_ANALISTA_1}    senha=${SENHA_ANALISTA_REPETIDO}
    ${RESPOSTA}=    POST On Session    gestor1    /php/analista_dados_novo.php    data=${dados}
    Set Test Variable    ${RESPOSTA}

Verificar se o sistema aceitou
    Should Be Equal    ${RESPOSTA.json()}[status]    ok

Verificar se o sistema recusou
    Should Be Equal    ${RESPOSTA.json()}[status]    nok

Verificar mensagem de registro alterado com sucesso
    Should Be Equal    ${RESPOSTA.json()}[mensagem]    ${MENSAGEM_ALTERADO_SUCESSO}

Verificar mensagem de e-mail já cadastrado
    Should Be Equal    ${RESPOSTA.json()}[mensagem]    ${MENSAGEM_EMAIL_CADASTRADO}

Verificar se o gestor repetido não foi cadastrado
    Check Row Count    SELECT id FROM gestor WHERE email='${EMAIL_GESTOR_1}'    ==    1

Verificar se o e-mail do gestor 1 mudou e a senha continuou a mesma
    Check Row Count    SELECT id FROM gestor WHERE id=${ID_GESTOR_1} AND email='${EMAIL_GESTOR_1_NOVO}' AND senha='${SENHA_GESTOR_1}'    ==    1

Verificar se o analista foi cadastrado e ligado ao gestor 2
    Check Row Count    SELECT id FROM analista_dados WHERE email='${EMAIL_ANALISTA_NOVO}' AND senha='${SENHA_ANALISTA_NOVO}' AND gestor_id=${ID_GESTOR_2}    ==    1

Verificar se o analista repetido não foi cadastrado
    Check Row Count    SELECT id FROM analista_dados WHERE email='${EMAIL_ANALISTA_1}'    ==    1