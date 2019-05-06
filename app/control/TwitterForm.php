<?php

class TwitterForm extends TPage
{
    private $form;

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

        $titulo->setSize(300);
        $texto->setSize(100, 150);

        $titulo->placeholder = 'Informe um titulo';
        $titulo->setTip('Aqui é a descrição do ditulo');

        $titulo->addValidation('Titulo', new TRequiredValidator); // required field

        $texto->setSize('100%', 50);

        $this->form->addAction('Salvar', new TAction(array($this, 'onSave')), 'fa:save green');
        $this->form->addAction('Voltar', new TAction(array('TwitterList', 'onReload')), 'fa:table blue');


        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($this->form);

        parent::add($vbox);
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

            new TMessage('info', "Registro Salvo com Sucesso", new TAction(['TwitterList', 'onReload']));

        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            \Adianti\Database\TTransaction::rollback();

        }
    }

    function onEdit($param)
    {
        try {
            if (isset($param['id'])) {
                TTransaction::open('example');

                $object = new Twitter($param['id']);

                $this->form->setData($object);

                TTransaction::close();
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());

            TTransaction::rollback();
        }
    }
}