<?php
include 'shared/config.php';
require 'shared/auth_check.php';

// ADD ARENA
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_arena'])) {
    $name = $_POST['name'];
    $yearRange = $_POST['year_range'];

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

        $stmt = $conn->prepare("INSERT INTO arenas (name, year_range, image_path) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $yearRange, $relativePath);
        $stmt->execute();

        echo "<script>alert('Arena added successfully!'); window.location.href = 'arenas-info.php';</script>";
        exit;
    } else {
        echo "<script>alert('Image upload failed.'); window.location.href = 'arenas-info.php';</script>";
        exit;
    }
}

// UPDATE ARENA
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_arena'])) {
    $arenaId = $_POST['arena_id'];
    $name = $_POST['name'];
    $yearRange = $_POST['year_range'];

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

        $stmt = $conn->prepare("UPDATE arenas SET name=?, year_range=?, image_path=? WHERE arena_id=?");
        $stmt->bind_param("sssi", $name, $yearRange, $relativePath, $arenaId);
    } else {
        $stmt = $conn->prepare("UPDATE arenas SET name=?, year_range=? WHERE arena_id=?");
        $stmt->bind_param("ssi", $name, $yearRange, $arenaId);
    }

    $stmt->execute();
    echo "<script>alert('Arena updated successfully!'); window.location.href = 'arenas-info.php';</script>";
    exit;
}

// DELETE ARENA
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM arenas WHERE arena_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo "<script>alert('Arena deleted successfully!'); window.location.href = 'arenas-info.php';</script>";
    exit;
}

// FETCH ARENAS
$arenas = $conn->query("SELECT * FROM arenas ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cavs | Arenas Info</title>
    <link rel="shortcut icon" href="img/shortcut-icon.png?v=<?= time(); ?>" type="image/x-icon">
    <?php include 'shared/css.php'; ?>
</head>

<body>
    <?php include 'shared/sidebar.php'; ?>
    <div class="preloader"></div>

    <div class="content p-5">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <h1 class="m-0">System Arenas Info</h1>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addArenaModal">
                <i class="bi bi-plus-square"></i> Add Arena
            </button>
        </div>

        <hr>

        <div class="table-responsive">
            <table id="arenas-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Preview</th>
                        <th>Name</th>
                        <th>Year Range</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $arenas->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <a href="/cavs/<?= htmlspecialchars($row['image_path']) ?>" target="_blank">
                                    <img src="/cavs/<?= htmlspecialchars($row['image_path']) ?>" style="height: 80px; border: 1px solid #ccc;">
                                </a>
                            </td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['year_range']) ?></td>
                            <td><?= date("Y-m-d H:i", strtotime($row['created_at'])) ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateArenaModal<?= $row['arena_id'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="?delete=<?= $row['arena_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this arena?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Update Arena Modal -->
                        <div class="modal fade" id="updateArenaModal<?= $row['arena_id'] ?>" tabindex="-1" aria-labelledby="updateArenaModalLabel<?= $row['arena_id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <form method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="update_arena" value="1">
                                        <input type="hidden" name="arena_id" value="<?= $row['arena_id'] ?>">
                                        <div class="modal-header bg-warning text-white">
                                            <h5 class="modal-title text-dark">Update Arena</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Name</label>
                                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Year Range</label>
                                                <input type="text" name="year_range" class="form-control" value="<?= htmlspecialchars($row['year_range']) ?>" placeholder="e.g., 1999–2009">
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

    <!-- Add Arena Modal -->
    <div class="modal fade" id="addArenaModal" tabindex="-1" aria-labelledby="addArenaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="add_arena" value="1">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title text-white">Add Arena</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter arena name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Year Range</label>
                            <input type="text" name="year_range" class="form-control" placeholder="e.g., 2001–2012">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">Save Arena</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'shared/js.php'; ?>
</body>

</html>