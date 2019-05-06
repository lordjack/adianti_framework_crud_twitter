<?php

class TwitterFormList extends TPage
{
    private $form;
    private $datagrid;

    function __construct()
    {
        parent::__construct();

        $this->form = new \Adianti\Wrapper\BootstrapFormBuilder();
        $this->form->setFormTitle("Exemplo CRUD - Twitter");

        $id = new \Adianti\Widget\Form\THidden('id');
        $titulo = new \Adianti\Widget\Form\TEntry('titulo');
        $texto = new TText('texto');

        $this->form->addFields([$id]);
        $this->form->addFields([new TLabel('Titulo')], [$titulo]);
        $this->form->addFields([new TLabel('Texto')], [$texto]);

        $titulo->placeholder = 'Informe um titulo';
        $titulo->setTip('Aqui é a descrição do ditulo');

        $titulo->addValidation('Titulo', new TRequiredValidator); // required field

        $texto->setSize('100%', 50);

        $this->form->addAction('Enviar', new TAction(array($this, 'onSave')), 'fa:check-circle-o green');

        $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->datagrid->width = '100%';

        $this->datagrid->addQuickColumn('ID', 'id', 'center');
        $this->datagrid->addQuickColumn('Titulo', 'titulo', 'left');

        $this->datagrid->enablePopover('Popover', 'Este é o <b> {texto} </b>');

        $action1 = new TDataGridAction(array($this, 'onView'));
        $action2 = new TDataGridAction(array($this, 'onDelete'));

        $this->datagrid->addQuickAction('View', $action1, 'texto', 'ico_find.png');
        $this->datagrid->addQuickAction('Delete', $action2, 'id', 'ico_delete.png');

        $action1->setUseButton(TRUE);
        $action1->setButtonClass('btn btn-default');
        $action1->setImage('fa:search blue');

        $action2->setUseButton(TRUE);
        $action2->setButtonClass('btn btn-default');
        $action2->setImage('fa:remove red');

        $this->datagrid->createModel();

        $panelForm = new TPanelGroup();
        $panelForm->add($this->form);

        $panelGrid = new TPanelGroup('Listagem');
        $panelGrid->add($this->datagrid)->style = 'overflow-x:auto';
        $panelGrid->addFooter('footer');

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($panelForm);
        $vbox->add($panelGrid);

        parent::add($vbox);
    }

    function onReload()
    {

        TTransaction::open('example');
        $repository = new TRepository('Twitter');
        $criteria = new TCriteria();
        $criteria->setProperty('order', 'id');

        $objects = $repository->load($criteria);

        $this->datagrid->clear();

        foreach ($objects as $object) {

            $this->datagrid->addItem($object);
        }

        TTransaction::close();

    }

    function onDelete($param)
    {
        $key = $param['key'];
        new TMessage('error', "The register $key may not be deleted");
    }

    function onView($param)
    {
        $texto = $param['texto'];
        new TMessage('info', "O texto do twitter é <b>: $texto </b>");
    }

    public function onSave($param)
    {
        try {

            \Adianti\Database\TTransaction::open('example');
            $object = $this->form->getData('Twitter');

            $this->form->validate();

            $object->store();

            $this->form->setData($object);

            \Adianti\Database\TTransaction::close();

            new TMessage('info', "Registro Salvo com Sucesso");

        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            \Adianti\Database\TTransaction::rollback();

        }
    }

    function show()
    {
        $this->onReload();
        parent::show();
    }

}