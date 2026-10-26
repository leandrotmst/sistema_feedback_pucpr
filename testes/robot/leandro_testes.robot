*** Settings ***
Documentation     Testes de gestores e analistas do AlignUp - Leandro.
Resource          leandro_resources.robot
Test Setup        Preparar o banco de dados
Test Teardown     Encerrar o teste

*** Test Cases ***
CT17 - Ver lista de gestores sem estar logado
    [Documentation]    Acessar a lista de gestores sem login não deve mostrar dados de gestores.
    [Tags]    leandro    CT17
    Abrir a página de listagem de gestores sem login
    Verificar se nenhum dado de gestor aparece na tela

CT19 - Cadastrar gestor com e-mail repetido
    [Documentation]    Cadastrar gestor com e-mail já existente deve ser recusado pelo sistema.
    [Tags]    leandro    CT19
    Fazer login como administrador
    Cadastrar gestor com e-mail repetido
    Verificar se o sistema recusou
    Verificar se o gestor repetido não foi cadastrado

CT20 - Alterar gestor sem trocar a senha
    [Documentation]    Alterar apenas o e-mail do gestor 1 deixando a nova senha em branco.
    [Tags]    leandro    CT20
    Fazer login como administrador
    Alterar gestor 1 sem preencher a nova senha
    Verificar se o sistema aceitou
    Verificar mensagem de registro alterado com sucesso
    Verificar se o e-mail do gestor 1 mudou e a senha continuou a mesma

CT21 - Cadastrar analista de dados
    [Documentation]    Gestor 2 cadastra um novo analista de dados válido.
    [Tags]    leandro    CT21
    Fazer login como gestor 2
    Cadastrar analista de dados válido
    Verificar se o sistema aceitou
    Verificar se o analista foi cadastrado e ligado ao gestor 2

CT22 - Cadastrar analista com e-mail repetido
    [Documentation]    Cadastrar analista com e-mail já existente deve ser recusado com mensagem de erro.
    [Tags]    leandro    CT22
    Fazer login como gestor 1
    Cadastrar analista com e-mail repetido
    Verificar se o sistema recusou
    Verificar mensagem de e-mail já cadastrado
    Verificar se o analista repetido não foi cadastrado