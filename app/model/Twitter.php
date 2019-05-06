<?php


class Twitter extends \Adianti\Database\TRecord
{
    const TABLENAME = 'twitter';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'max'; // {max, serial}

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('titulo');
        parent::addAttribute('texto');
    }
}