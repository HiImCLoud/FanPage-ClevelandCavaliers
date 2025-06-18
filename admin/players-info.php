<?php
include 'shared/config.php';
require 'shared/auth_check.php';

// Add Player
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_player'])) {
    $name = $_POST['name'];
    $position = $_POST['position'] ?? null;
    $description = $_POST['description'] ?? null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $originalName = basename($_FILES['image']['name']);
        $uploadDir = dirname(__DIR__) . '/assets/';
        $relativeDir = 'assets/';

        $pathInfo = pathinfo($originalName);
        $filename = $pathInfo['filename'];
        $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
        $finalName = $filename . $extension;
        $counter = 1;

        while (file_exists($uploadDir . $finalName)) {
            $finalName = $filename . "($counter)" . $extension;
            $counter++;
        }

        $targetPath = $uploadDir . $finalName;
        $relativePath = $relativeDir . $finalName;

        move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);

        $stmt = $conn->prepare("INSERT INTO players (name, position, description, image_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $position, $description, $relativePath);
        $stmt->execute();

        echo "<script>alert('Player added successfully!'); window.location.href = 'players-info.php';</script>";
        exit;
    } else {
        echo "<script>alert('Image upload failed.'); window.location.href = 'players-info.php';</script>";
        exit;
    }
}

// Update Player
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_player'])) {
    $playerId = $_POST['player_id'];
    $name = $_POST['name'];
    $position = $_POST['position'] ?? null;
    $description = $_POST['description'] ?? null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $originalName = basename($_FILES['image']['name']);
        $uploadDir = dirname(__DIR__) . '/assets/';
        $relativeDir = 'assets/';

        $pathInfo = pathinfo($originalName);
        $filename = $pathInfo['filename'];
        $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
        $finalName = $filename . $extension;
        $counter = 1;

        while (file_exists($uploadDir . $finalName)) {
            $finalName = $filename . "($counter)" . $extension;
            $counter++;
        }

        $targetPath = $uploadDir . $finalName;
        $relativePath = $relativeDir . $finalName;

        move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);

        $stmt = $conn->prepare("UPDATE players SET name=?, position=?, description=?, image_path=? WHERE player_id=?");
        $stmt->bind_param("ssssi", $name, $position, $description, $relativePath, $playerId);
    } else {
        $stmt = $conn->prepare("UPDATE players SET name=?, position=?, description=? WHERE player_id=?");
        $stmt->bind_param("sssi", $name, $position, $description, $playerId);
    }

    $stmt->execute();
    echo "<script>alert('Player updated successfully!'); window.location.href = 'players-info.php';</script>";
    exit;
}

// Delete Player
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM players WHERE player_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo "<script>alert('Player deleted successfully!'); window.location.href = 'players-info.php';</script>";
    exit;
}

// Fetch players
$players = $conn->query("SELECT * FROM players ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cavs | Players Info</title>
    <link rel="shortcut icon" href="img/shortcut-icon.png?v=<?= time(); ?>" type="image/x-icon">
    <?php include 'shared/css.php'; ?>
</head>

<body>
    <?php include 'shared/sidebar.php'; ?>

    <div class="content p-5">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <h1 class="m-0">System Players Info</h1>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPlayerModal">
                <i class="bi bi-plus-square"></i> Add Player
            </button>
        </div>

        <hr>

        <div class="table-responsive">
            <table id="players-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Preview</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Description</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $players->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <a href="/cavs/<?= htmlspecialchars($row['image_path']) ?>" target="_blank">
                                    <img src="/cavs/<?= htmlspecialchars($row['image_path']) ?>" style="height: 80px; border: 1px solid #ccc;">
                                </a>
                            </td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['position']) ?></td>
                            <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
                            <td><?= date("Y-m-d H:i", strtotime($row['created_at'])) ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updatePlayerModal<?= $row['player_id'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="?delete=<?= $row['player_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this player?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Update Modal -->
                        <div class="modal fade" id="updatePlayerModal<?= $row['player_id'] ?>" tabindex="-1" aria-labelledby="updatePlayerModalLabel<?= $row['player_id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <form method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="update_player" value="1">
                                        <input type="hidden" name="player_id" value="<?= $row['player_id'] ?>">
                                        <div class="modal-header bg-warning text-white">
                                            <h5 class="modal-title text-dark">Update Player</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Name</label>
                                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Position</label>
                                                <input type="text" name="position" class="form-control" value="<?= htmlspecialchars($row['position']) ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($row['description']) ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Change Image (optional)</label>
                                                <input type="file" name="image" class="form-control" accept="image/*">
                                                <small class="form-text text-muted">Current: <?= htmlspecialchars($row['image_path']) ?></small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-primary" type="submit">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Player Modal -->
    <div class="modal fade" id="addPlayerModal" tabindex="-1" aria-labelledby="addPlayerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="add_player" value="1">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title text-white">Add Player</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter player name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Position</label>
                            <input type="text" name="position" class="form-control" placeholder="Optional: e.g., Forward">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Player bio or notes (optional)"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">Save Player</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'shared/js.php'; ?>
</body>

</html>