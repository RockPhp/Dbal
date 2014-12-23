<?php

interface Rock_DbAl_Iface_IStmt
{

    public function nextObject($upperCase = false);

    public function nextArray($upperCase = false);

    public function nextArrayInt();

    public function resetCursor();

    public function closeCursor();

    public function numCollumns();

    public function numRows();
}
