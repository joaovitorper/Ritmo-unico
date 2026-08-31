
<?php

class Treino
{
    private $id;
    private $usuarioId;
    private $distancia;
    private $tempo;
    private $ritmo;
    private $data;

    public function __construct($id, $usuarioId, $distancia, $tempo, $ritmo, $data)
    {
        $this->id = $id;
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

    public function getUsuarioId()
    {
        return $this->usuarioId;
    }

    public function getDistancia()
    {
        return $this->distancia;
    }

    public function getTempo()
    {
        return $this->tempo;
    }

    public function getRitmo()
    {
        return $this->ritmo;
    }

    public function getData()
    {
        return $this->data;
    }
}