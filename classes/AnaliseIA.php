<?php

class AnaliseIA
{
    private $id;
    private $usuarioId;
    private $analise;
    private $recomendacao;
    private $data;

    public function __construct($usuarioId = "", $analise = "", $recomendacao = "", $data = "")
    {
        $this->usuarioId = $usuarioId;
        $this->analise = $analise;
        $this->recomendacao = $recomendacao;
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

    public function getAnalise()
    {
        return $this->analise;
    }

    public function setAnalise($analise)
    {
        $this->analise = $analise;
    }

    public function getRecomendacao()
    {
        return $this->recomendacao;
    }

    public function setRecomendacao($recomendacao)
    {
        $this->recomendacao = $recomendacao;
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