<?php


class TwitterList extends TPage
{
    private $form;
    private $datagrid;

    function __construct()
    {
        parent::__construct();

        ############### Formulário ###############
        $this->form = new \Adianti\Wrapper\BootstrapFormBuilder();
        $this->form->setFormTitle("Listagem Exemplo CRUD - Twitter");

        $titulo = new \Adianti\Widget\Form\TEntry('titulo');

        $this->form->addFields([new TLabel('Titulo')], [$titulo]);
        $titulo->setSize(250);

        $titulo->placeholder = 'Digite aqui para pesquisar';

        $this->form->addAction('Buscar', new TAction(array($this, 'onReload')), 'fa:search blue');
        $this->form->addAction('Twittar', new TAction(array('TwitterForm', 'onEdit')), 'fa:plus green');
        ############### Fim Formulário ###############

        ############### GRID ###############
        $this->datagrid = new \Adianti\Wrapper\BootstrapDatagridWrapper(new \Adianti\Widget\Wrapper\TQuickGrid());
        $this->datagrid->width = '100%';

        $this->datagrid->addQuickColumn('ID', 'id', 'center');
        $this->datagrid->addQuickColumn('Titulo', 'titulo', 'left', 800);

        $this->datagrid->enablePopover('Popover', 'Este é o <b> {texto} </b>');

        $action1 = new TDataGridAction(array('TwitterForm', 'onEdit'));
        $action2 = new TDataGridAction(array($this, 'onDelete'));

        $this->datagrid->addQuickAction('Editar', $action1, 'id', 'fa:edit blue');
        $this->datagrid->addQuickAction('Delete', $action2, 'id', 'fa:trash red');

        $action1->setUseButton(TRUE);
        $action1->setButtonClass('btn btn-default');
        $action1->setImage('fa:search blue');

        $action2->setUseButton(TRUE);
        $action2->setButtonClass('btn btn-default');
        $action2->setImage('fa:remove red');

        $this->datagrid->createModel();
        ############### Fim GRID ###############


        ############### Container ###############
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($this->form);
        $vbox->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));

        // add the box inside the page
        parent::add($vbox);
        ############### Fim Container ###############
    }


    function onReload()
    {
        //pega dados do formulário
        $data = $this->form->getData();

        //cria criterios para filtrar
        $criteria = new TCriteria();
        $criteria->setProperty('order', 'id desc');

        //verifica se o campo titulo do formulário é diferente de vazio
        if (!empty($data->titulo)) {
            $filter = new TFilter('titulo', 'like', "{$data->titulo}%");
            $criteria->add($filter);
        }

        TTransaction::open('example');
        $repository = new TRepository('Twitter');

        $objects = $repository->load($criteria);

        $this->datagrid->clear();

        foreach ($objects as $object) {

            $this->datagrid->addItem($object);
        }

        TTransaction::close();

    }

    public static function Delete($param)
    {
        try {
            $key = $param['key'];
            TTransaction::open('example');

            $object = new Twitter($key);
            $object->delete();

            TTransaction::close();

            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', 'Registro Removido com Sucesso!', $pos_action);

        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public static function onDelete($param)
    {

        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param);

        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }


    function show()
    {
        $this->onReload();
        parent::show();
    }


}