<?php

class Objetivo
{
    private $id;
    private $usuarioId;
    private $tipo;
    private $descricao;
    private $meta;
    private $prazo;

    public function __construct($usuarioId = "", $tipo = "", $descricao = "", $meta = "", $prazo = "")
    {
        $this->usuarioId = $usuarioId;
        $this->tipo = $tipo;
        $this->descricao = $descricao;
        $this->meta = $meta;
        $this->prazo = $prazo;
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

    public function getTipo()
    {
        return $this->tipo;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    public function getMeta()
    {
        return $this->meta;
    }

    public function setMeta($meta)
    {
        $this->meta = $meta;
    }

    public function getPrazo()
    {
        return $this->prazo;
    }

    public function setPrazo($prazo)
    {
        $this->prazo = $prazo;
    }
}