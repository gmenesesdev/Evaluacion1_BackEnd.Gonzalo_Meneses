<?php
include_once '../version1.php';

$existeId = false;
$valorId = 0;

if (count($_parametros) > 0) {
    foreach ($_parametros as $p) {
        if (strpos($p, 'id') !== false) {
            $existeId = true;
            $valorId = explode('=', $p)[1];
        }
    }
}

if ($_version == 'v1') {
    if ($_mantenedor == 'mantenedor') {
        switch ($_metodo) {
            case 'GET':
                if ($_header == $_token_get) {
                    include_once 'controller.php';
                    include_once '../conexion.php';
                    // Captura y verifica el ID recibido desde la URL
                    $id = $_GET['id'] ?? null;
                    if ($id !== null && is_numeric($id)) {
                        // Intentar obtener el registro con el ID proporcionado
                        $control = new Controlador();
                        $registro = $control->getRegistroPorID($id);
                        if ($registro !== null) {
                            http_response_code(200); // OK
                            echo json_encode(["data" => $registro]);
                        } else {
                            http_response_code(404); // No encontrado
                            echo json_encode(["Error" => "No se encontró el registro con el ID proporcionado"]);
                        }
                    } else {
                        // Si no se proporciona un ID, obtener todos los registros
                        // Realizar validación de autorización
                        if ($_header == $_token_get) {
                            include_once 'controller.php';
                            include_once '../conexion.php';

                            $control = new Controlador();
                            $lista = $control->getTodos();

                            if ($lista !== null) {
                                http_response_code(200); // OK
                                echo json_encode(["data" => $lista]);
                            } else {
                                http_response_code(500); // Error interno del servidor
                                echo json_encode(["Error" => "No se pudo obtener la lista de registros"]);
                            }
                        } else {
                            http_response_code(401);
                            echo json_encode(["Error" => "No tiene autorización GET"]);
                        }
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(["Error" => "No tiene autorización GET"]);
                }
                break;

            case 'POST':
                if ($_header == $_token_post) {
                    include_once 'controller.php';
                    include_once '../conexion.php';

                    // Captura y verifica datos recibidos
                    $data = json_decode(file_get_contents("php://input"), true);
                    if (isset($data['nombre']) && isset($data['activo'])) {
                        // Verificar si el nombre ya existe en la tabla
                        $con = new Conexion();
                        $nombre = mysqli_real_escape_string($con->getConnection(), $data['nombre']);
                        $sql_check_nombre = "SELECT COUNT(*) AS existe FROM mantenedor WHERE nombre = '$nombre'";
                        $resultado_check_nombre = mysqli_query($con->getConnection(), $sql_check_nombre);
                        $fila_check_nombre = mysqli_fetch_assoc($resultado_check_nombre);
                        $existe_nombre = $fila_check_nombre['existe'];

                        if ($existe_nombre > 0) {
                            http_response_code(400); // Solicitud incorrecta
                            echo json_encode(["Error" => "Nombre no se puede insertar"]);
                        } else {
                            // Insertar el elemento si el nombre no existe
                            $control = new Controlador();
                            $id_insertado = $control->insertar($data['nombre'], $data['activo']);
                            if ($id_insertado !== false) {
                                http_response_code(201); // Creado
                                echo json_encode(["mensaje" => "Elemento creado correctamente", "id_insertado" => $id_insertado]);
                            } else {
                                http_response_code(500); // Error interno del servidor
                                echo json_encode(["Error" => "No se pudo crear el elemento"]);
                            }
                        }
                    } else {
                        http_response_code(400); // Solicitud incorrecta
                        echo json_encode(["Error" => "Datos incompletos"]);
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(["Error" => "No tiene autorizacion POST"]);
                }
                break;
            case 'DELETE':
                if ($_header == $_token_delete) {
                    include_once 'controller.php';
                    include_once '../conexion.php';

                    // Captura y verifica el ID recibido
                    $id = $_GET['id'] ?? null;
                    if ($id !== null && is_numeric($id)) {
                        // Intentar eliminar el registro con el ID proporcionado
                        $control = new Controlador();
                        $eliminado = $control->eliminarID($id);
                        if ($eliminado) {
                            http_response_code(200); // OK
                            echo json_encode(["mensaje" => "Elemento eliminado correctamente"]);
                        } else {
                            http_response_code(404); // No encontrado
                            echo json_encode(["Error" => "No se puede eliminar el elemento"]);
                        }
                    } else {
                        http_response_code(400); // Solicitud incorrecta
                        echo json_encode(["Error" => "ID no válido"]);
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(["Error" => "No tiene autorizacion DELETE"]);
                }
                break;
            case 'PUT':
                if ($_header == $_token_put) {
                    include_once 'controller.php';
                    include_once '../conexion.php';

                    // Captura y verifica el ID recibido desde la URL
                    $id = $_GET['id'] ?? null;
                    if ($id !== null && is_numeric($id)) {
                        // Captura los datos del cuerpo de la solicitud
                        $data = json_decode(file_get_contents("php://input"), true);
                        $nombre = $data['nombre'] ?? null;

                        // Verifica si se recibió el nombre en el cuerpo de la solicitud
                        if ($nombre !== null) {
                            // Intentar actualizar el nombre del registro con el ID proporcionado
                            $control = new Controlador();
                            $actualizado = $control->updateNombrePorID($id, $nombre);
                            if ($actualizado) {
                                http_response_code(200); // OK
                                echo json_encode(["mensaje" => "Nombre actualizado correctamente"]);
                            } else {
                                http_response_code(404); // No encontrado
                                echo json_encode(["Error" => "No se pudo actualizar el nombre"]);
                            }
                        } else {
                            http_response_code(400); // Solicitud incorrecta
                            echo json_encode(["Error" => "Datos incompletos o inválidos en el cuerpo de la solicitud"]);
                        }
                    } else {
                        http_response_code(400); // Solicitud incorrecta
                        echo json_encode(["Error" => "ID no válido en la URL"]);
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(["Error" => "No tiene autorización PUT"]);
                }
                break;


            default:
                http_response_code(405);
                echo json_encode(["Error" => "No implementado"]);
                break;
        }
    }
}
