<?php

$archivo = 'visitas.json';

if (!file_exists($archivo)) {
    file_put_contents($archivo, json_encode([]));
}

$visitas = json_decode(file_get_contents($archivo), true);
$accion = $_GET['accion'] ?? '';


if ($accion === 'asignar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevaVisita = [
        "id" => uniqid(), 
        "paciente" => $_POST['paciente'],
        "fecha" => $_POST['fecha'],
        "motivo" => $_POST['motivo'],
        "estado" => "Pendiente"
    ];
    
    $visitas[] = $nuevaVisita;
    file_put_contents($archivo, json_encode($visitas, JSON_PRETTY_PRINT));
    header("Location: index.php");
    exit();
}


if ($accion === 'cancelar' && isset($_GET['id'])) {
    $idBusqueda = $_GET['id'];
    $visitas = array_filter($visitas, function($v) use ($idBusqueda) {
        return $v['id'] !== $idBusqueda;
    });
    file_put_contents($archivo, json_encode(array_values($visitas), JSON_PRETTY_PRINT));
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignación de Visitas Médicas</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #eef2f3; padding: 30px; display: flex; justify-content: center; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); width: 100%; max-width: 700px; }
        h2 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
        label { display: block; margin-top: 15px; font-weight: bold; color: #555; }
        input, select, textarea { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        button { width: 100%; background: #3498db; color: white; border: none; padding: 12px; margin-top: 20px; border-radius: 6px; cursor: pointer; font-size: 16px; }
        button:hover { background: #2980b9; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        th { background: #f8f9fa; padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        .status { padding: 4px 8px; border-radius: 4px; background: #ffeaa7; font-size: 12px; }
        .btn-cancel { color: #e74c3c; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="card">
    <h2>Asignar Nueva Visita</h2>
    <form action="index.php?accion=asignar" method="POST">
        <label>Nombre del Paciente:</label>
        <input type="text" name="paciente" placeholder="Ej. Juan Pérez" required>

        <label>Fecha y Hora:</label>
        <input type="datetime-local" name="fecha" required>

        <label>Motivo de la Visita:</label>
        <textarea name="motivo" rows="2" placeholder="Ej. Chequeo general" required></textarea>

        <button type="submit">Agendar Visita</button>
    </form>

    <h2>Agenda de Visitas</h2>
    <table>
        <thead>
            <tr>
                <th>Paciente</th>
                <th>Fecha/Hora</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($visitas)): ?>
                <tr><td colspan="4" style="text-align:center;">No hay visitas programadas.</td></tr>
            <?php else: ?>
                <?php foreach ($visitas as $v): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($v['paciente']) ?></strong><br>
                        <small style="color:gray"><?= htmlspecialchars($v['motivo']) ?></small>
                    </td>
                    <td><?= str_replace('T', ' ', $v['fecha']) ?></td>
                    <td><span class="status"><?= $v['estado'] ?></span></td>
                    <td>
                        <a href="index.php?accion=cancelar&id=<?= $v['id'] ?>" 
                           class="btn-cancel" onclick="return confirm('¿Cancelar esta visita?')">Cancelar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>