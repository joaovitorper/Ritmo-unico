<?php

class Objetivo
{
    private $id;
    private $usuarioId;
    private $tipo;
    private $meta;
    private $progresso;
    private $dataInicio;
    private $dataFim;

    public function __construct($id, $usuarioId, $tipo, $meta, $progresso, $dataInicio, $dataFim)
    {
        $this->id = $id;
        $this->usuarioId = $usuarioId;
        $this->tipo = $tipo;
        $this->meta = $meta;
        $this->progresso = $progresso;
        $this->dataInicio = $dataInicio;
        $this->dataFim = $dataFim;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsuarioId()
    {
        return $this->usuarioId;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function getMeta()
    {
        return $this->meta;
    }

    public function getProgresso()
    {
        return $this->progresso;
    }

    public function getDataInicio()
    {
        return $this->dataInicio;
    }

    public function getDataFim()
    {
        return $this->dataFim;
    }
}