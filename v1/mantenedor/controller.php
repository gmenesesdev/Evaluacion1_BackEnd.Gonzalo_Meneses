<?php

class Controlador
{
    private $lista;

    public function __construct()
    {
        $this->lista = [];
    }

    // Obtiene todos los datos
    public function getTodos()
    {
        $con = new Conexion();
        $sql = "SELECT id, nombre, activo FROM mantenedor;";
        $rs = mysqli_query($con->getConnection(), $sql);
        if ($rs) {
            while ($tupla = mysqli_fetch_assoc($rs)) {
                $tupla['activo'] = $tupla['activo'] == 1 ? true : false;
                array_push($this->lista, $tupla);
            }
            mysqli_free_result($rs);
        }
        $con->closeConnection();
        return $this->lista;
    }

    // Obtiene los registros según ID
    public function getRegistroPorID($id)
    {
        $con = new Conexion();
        $id = mysqli_real_escape_string($con->getConnection(), $id);

        // Consultar el registro con el ID proporcionado
        $sql = "SELECT id, nombre, activo FROM mantenedor WHERE id = $id";
        $resultado = mysqli_query($con->getConnection(), $sql);

        if ($resultado) {
            $registro = mysqli_fetch_assoc($resultado);
            mysqli_free_result($resultado);
            $con->closeConnection();
            return $registro;
        } else {
            $con->closeConnection();
            return null;
        }
    }

    // Inserta lso datos en la BDD
    public function insertar($nombre, $activo)
    {
        $con = new Conexion();
        $nombre = mysqli_real_escape_string($con->getConnection(), $nombre); // Evita inyección de SQL
        $activo = $activo ? 1 : 0; // Convierte a formato de base de datos

        // Obtener el ID más alto de la tabla
        // Se realiza esto ya que el dato ID de la tabla mantenedor no es incremental
        $sql_max_id = "SELECT MAX(id) AS max_id FROM mantenedor";
        $resultado_max_id = mysqli_query($con->getConnection(), $sql_max_id);
        $fila_max_id = mysqli_fetch_assoc($resultado_max_id);
        $nuevo_id = $fila_max_id['max_id'] + 1;

        // Insertar el nuevo registro con el nuevo ID
        $sql = "INSERT INTO mantenedor (id, nombre, activo) VALUES ($nuevo_id, '$nombre', $activo)";
        $resultado = mysqli_query($con->getConnection(), $sql);

        if ($resultado) {
            $con->closeConnection();
            return $nuevo_id;
        } else {
            $con->closeConnection();
            return false;
        }
    }

    // Eliminar dato por ID
    public function eliminarID($id)
    {
        $con = new Conexion();
        $id = mysqli_real_escape_string($con->getConnection(), $id);

        // Verificar si el ID existe en la tabla
        $sql_check_id = "SELECT COUNT(*) AS existe FROM mantenedor WHERE id = $id";
        $resultado_check_id = mysqli_query($con->getConnection(), $sql_check_id);
        $fila_check_id = mysqli_fetch_assoc($resultado_check_id);
        $existe_id = $fila_check_id['existe'];

        if ($existe_id > 0) {
            // Si el ID existe, eliminar el registro
            $sql = "DELETE FROM mantenedor WHERE id = $id";
            $resultado = mysqli_query($con->getConnection(), $sql);

            if ($resultado) {
                $con->closeConnection();
                return true;
            } else {
                $con->closeConnection();
                return false;
            }
        } else {
            // Si el ID no existe, devolver error
            $con->closeConnection();
            return false;
        }
    }

    // Update dato por ID
    public function updateNombrePorID($id, $nuevoNombre)
    {
        $con = new Conexion();
        $id = mysqli_real_escape_string($con->getConnection(), $id);
        $nuevoNombre = mysqli_real_escape_string($con->getConnection(), $nuevoNombre);

        // Verificar si el ID existe en la tabla
        $sql_check_id = "SELECT COUNT(*) AS existe FROM mantenedor WHERE id = $id";
        // Genera la query en la BDD
        $resultado_check_id = mysqli_query($con->getConnection(), $sql_check_id);

        $fila_check_id = mysqli_fetch_assoc($resultado_check_id);
        $existe_id = $fila_check_id['existe'];

        if ($existe_id > 0) {
            // Si el ID existe, actualizar el nombre del registro
            $sql_update_nombre = "UPDATE mantenedor SET nombre = '$nuevoNombre' WHERE id = $id";
            $resultado_update_nombre = mysqli_query($con->getConnection(), $sql_update_nombre);

            if ($resultado_update_nombre) {
                $con->closeConnection();
                return true;
            } else {
                $con->closeConnection();
                return false;
            }
        } else {
            // Si el ID no existe, devolver error
            $con->closeConnection();
            return false;
        }
    }
}
