<?php
include 'shared/config.php';
require 'shared/auth_check.php';

// ADD SOURCE
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_source'])) {
    $name = $_POST['name'];
    $url = $_POST['url'];

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

        $stmt = $conn->prepare("INSERT INTO sources (name, url, image_path) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $url, $relativePath);
        $stmt->execute();

        echo "<script>alert('Source added successfully!'); window.location.href = 'sources-info.php';</script>";
        exit;
    } else {
        echo "<script>alert('Image upload failed.'); window.location.href = 'sources-info.php';</script>";
        exit;
    }
}

// UPDATE SOURCE
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_source'])) {
    $sourceId = $_POST['source_id'];
    $name = $_POST['name'];
    $url = $_POST['url'];

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

        $stmt = $conn->prepare("UPDATE sources SET name=?, url=?, image_path=? WHERE source_id=?");
        $stmt->bind_param("sssi", $name, $url, $relativePath, $sourceId);
    } else {
        $stmt = $conn->prepare("UPDATE sources SET name=?, url=? WHERE source_id=?");
        $stmt->bind_param("ssi", $name, $url, $sourceId);
    }

    $stmt->execute();
    echo "<script>alert('Source updated successfully!'); window.location.href = 'sources-info.php';</script>";
    exit;
}

// DELETE SOURCE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM sources WHERE source_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo "<script>alert('Source deleted successfully!'); window.location.href = 'sources-info.php';</script>";
    exit;
}

// FETCH SOURCES
$sources = $conn->query("SELECT * FROM sources ORDER BY source_id DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cavs | Sources Info</title>
    <link rel="shortcut icon" href="img/shortcut-icon.png?v=<?= time(); ?>" type="image/x-icon">
    <?php include 'shared/css.php'; ?>
</head>

<body>
    <?php include 'shared/sidebar.php'; ?>
    <div class="preloader"></div>

    <div class="content p-5">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <h1 class="m-0">Sources Info</h1>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSourceModal">
                <i class="bi bi-plus-square"></i> Add Source
            </button>
        </div>

        <hr>

        <div class="table-responsive">
            <table id="sources-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Preview</th>
                        <th>Name</th>
                        <th>URL</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $sources->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <a href="/cavs/<?= htmlspecialchars($row['image_path']) ?>" target="_blank">
                                    <img src="/cavs/<?= htmlspecialchars($row['image_path']) ?>" style="height: 80px; border: 1px solid #ccc;">
                                </a>
                            </td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><a href="<?= htmlspecialchars($row['url']) ?>" target="_blank"><?= htmlspecialchars($row['url']) ?></a></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateSourceModal<?= $row['source_id'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="?delete=<?= $row['source_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this source?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Update Modal -->
                        <div class="modal fade" id="updateSourceModal<?= $row['source_id'] ?>" tabindex="-1" aria-labelledby="updateSourceModalLabel<?= $row['source_id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <form method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="update_source" value="1">
                                        <input type="hidden" name="source_id" value="<?= $row['source_id'] ?>">
                                        <div class="modal-header bg-warning text-white">
                                            <h5 class="modal-title text-dark">Update Source</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Name</label>
                                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">URL</label>
                                                <input type="url" name="url" class="form-control" value="<?= htmlspecialchars($row['url']) ?>" required>
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

    <!-- Add Source Modal -->
    <div class="modal fade" id="addSourceModal" tabindex="-1" aria-labelledby="addSourceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="add_source" value="1">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title text-white">Add Source</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter source name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">URL</label>
                            <input type="url" name="url" class="form-control" placeholder="https://example.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">Save Source</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'shared/js.php'; ?>
</body>

</html>