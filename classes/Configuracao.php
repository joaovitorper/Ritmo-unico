<?php

class Configuracao
{
    private $id;
    private $usuarioId;
    private $notificacoes;
    private $tema;
    private $privacidade;

    public function __construct($usuarioId = "", $notificacoes = "", $tema = "", $privacidade = "")
    {
        $this->usuarioId = $usuarioId;
        $this->notificacoes = $notificacoes;
        $this->tema = $tema;
        $this->privacidade = $privacidade;
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

    public function getNotificacoes()
    {
        return $this->notificacoes;
    }

    public function setNotificacoes($notificacoes)
    {
        $this->notificacoes = $notificacoes;
    }

    public function getTema()
    {
        return $this->tema;
    }

    public function setTema($tema)
    {
        $this->tema = $tema;
    }

    public function getPrivacidade()
    {
        return $this->privacidade;
    }

    public function setPrivacidade($privacidade)
    {
        $this->privacidade = $privacidade;
    }
}