<?php
class Empleado {
    public $id;
    public $nombre;
    public $correo;
    
    public $salario;
    public $departamento_id;

    public function __construct($id, $nombre, $correo, $salario, $departamento_id) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->correo = $correo;
        $this->salario = $salario;
        $this->departamento_id = $departamento_id;
    }
}
?>