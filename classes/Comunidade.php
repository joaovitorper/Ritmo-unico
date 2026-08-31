<?php

class Comunidade
{
    private $id;
    private $usuarioId;
    private $titulo;
    private $conteudo;
    private $data;

    public function __construct($usuarioId = "", $titulo = "", $conteudo = "", $data = "")
    {
        $this->usuarioId = $usuarioId;
        $this->titulo = $titulo;
        $this->conteudo = $conteudo;
        $this->data = $data;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUsuarioId()
    {
        return $this->usuarioId;
    }

    public function setUsuarioId($usuarioId)
    {
        $this->usuarioId = $usuarioId;
    }

    public function getTitulo()
    {
        return $this->titulo;
    }

    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    public function getConteudo()
    {
        return $this->conteudo;
    }

    public function setConteudo($conteudo)
    {
        $this->conteudo = $conteudo;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }
}