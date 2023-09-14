<?php
// Permitir solicitudes de cualquier origen
header("Access-Control-Allow-Origin: *");
// Permitir métodos GET, POST, PUT, DELETE
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
// Configurar el tipo de contenido como JSON
header("Content-Type: application/json");
// Simulación de una base de datos de tareas
$tasks = array(
    array("id" => 1, "task" => "Comprar Comestibles", "completed" => false),
    array("id" => 2, "task" => "Hacer ejercicio", "completed" => true),
    array("id" => 3, "task" => "Desayunos", "completed" => true),
    array("id" => 4, "task" => "Salir al Parque", "completed" => true),
    array("id" => 5, "task" => "Ordenar la Ropa", "completed" => true)
);
// Obtener el contenido de la solicitud
$data = json_decode(file_get_contents("php://input"), true);
// Manejar la solicitud según el método
$method = $_SERVER["REQUEST_METHOD"];
switch ($method) {
    case "GET":
        // Lógica para obtener la lista de tareas
        if (isset($data['action']) && $data['action'] == 'list') {
            http_response_code(200);
            echo json_encode($tasks);
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Acción no válida."));
        }
        break;
    case "POST":
        // Lógica para agregar una tarea
        if (isset($data['action']) && $data['action'] == 'add' && isset($data['task'])) {
            $newTask = array(
                "id" => count($tasks) + 1,
                "task" => $data["task"],
                "completed" => false
            );
            array_push($tasks, $newTask);
            http_response_code(201);
            echo json_encode(array("message" => "Tarea agregada correctamente."));
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Datos no válidos."));
        }
        break;
    case "PUT":
        // Lógica para marcar una tarea como completada
        if (isset($data['action']) && $data['action'] == 'complete' && isset($data['id'])) {
            $taskId = $data["id"];
            foreach ($tasks as &$task) {
                if ($task["id"] == $taskId) {
                    $task["completed"] = true;
                    break;
                }
            }
            http_response_code(200);
            echo json_encode(array("message" => "Tarea marcada como completada."));
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Datos no válidos."));
        }
        break;
    case "DELETE":
        // Lógica para eliminar una tarea
        if (isset($data['action']) && $data['action'] == 'delete' && isset($data['id'])) {
            $taskId = $data["id"];
            $tasks = array_filter($tasks, function ($task) use ($taskId) {
                return $task["id"] != $taskId;
            });
            http_response_code(200);
            echo json_encode(array("message" => "Tarea eliminada correctamente."));
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Datos no válidos."));
        }
        break;
    default:
        // Método no permitido
        http_response_code(405);
        echo json_encode(array("message" => "Método no permitido."));
        break;
}
?>
