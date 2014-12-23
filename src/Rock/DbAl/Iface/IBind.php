<?php

interface Rock_DbAl_Iface_IBind
{

    public function __construct($value = null, $maxLenght = null, $type = null);

    public function getValue();

    public function setValue($value);

    public function getMaxLenght();

    public function setMaxLenght($maxLenght);

    public function getType();

    public function setType($type);
}
