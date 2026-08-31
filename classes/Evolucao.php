<?php

class Evolucao
{
    private $id;
    private $usuarioId;
    private $distancia;
    private $tempo;
    private $ritmo;
    private $data;

    public function __construct($usuarioId = "", $distancia = "", $tempo = "", $ritmo = "", $data = "")
    {
        $this->usuarioId = $usuarioId;
        $this->distancia = $distancia;
        $this->tempo = $tempo;
        $this->ritmo = $ritmo;
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

    public function getDistancia()
    {
        return $this->distancia;
    }

    public function setDistancia($distancia)
    {
        $this->distancia = $distancia;
    }

    public function getTempo()
    {
        return $this->tempo;
    }

    public function setTempo($tempo)
    {
        $this->tempo = $tempo;
    }

    public function getRitmo()
    {
        return $this->ritmo;
    }

    public function setRitmo($ritmo)
    {
        $this->ritmo = $ritmo;
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