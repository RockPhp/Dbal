<?php

class Rock_DbAl_BindDrv implements Rock_DbAl_Iface_IBind
{

    private $value;

    private $maxLenght;

    private $type;

    public function __construct($value = null, $maxLenght = null, $type = null)
    {
        $this->setValue($value);
        $this->setMaxLenght($maxLenght);
        $this->setType($type);
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function getMaxLenght()
    {
        return $this->maxLenght;
    }

    public function setMaxLenght($maxLenght)
    {
        $this->maxLenght = $maxLenght;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
}
