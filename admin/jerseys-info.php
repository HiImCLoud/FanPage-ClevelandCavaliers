<?php
include 'shared/config.php';
require 'shared/auth_check.php';

// INSERT
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_jersey'])) {
    $title = $_POST['title'];

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

        $stmt = $conn->prepare("INSERT INTO jerseys (title, image_path) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $relativePath);
        $stmt->execute();

        echo "<script>alert('Jersey added successfully!'); window.location.href = 'jerseys-info.php';</script>";
        exit;
    } else {
        echo "<script>alert('Image upload failed.'); window.location.href = 'jerseys-info.php';</script>";
        exit;
    }
}

// UPDATE
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_jersey'])) {
    $jerseyId = $_POST['jersey_id'];
    $title = $_POST['title'];

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

        $stmt = $conn->prepare("UPDATE jerseys SET title = ?, image_path = ? WHERE jersey_id = ?");
        $stmt->bind_param("ssi", $title, $relativePath, $jerseyId);
    } else {
        $stmt = $conn->prepare("UPDATE jerseys SET title = ? WHERE jersey_id = ?");
        $stmt->bind_param("si", $title, $jerseyId);
    }

    $stmt->execute();
    echo "<script>alert('Jersey updated successfully!'); window.location.href = 'jerseys-info.php';</script>";
    exit;
}

// DELETE
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_jersey'])) {
    $jerseyId = $_POST['jersey_id'];

    $img = $conn->prepare("SELECT image_path FROM jerseys WHERE jersey_id = ?");
    $img->bind_param("i", $jerseyId);
    $img->execute();
    $result = $img->get_result();

    if ($result->num_rows > 0) {
        $imgData = $result->fetch_assoc();
        $filePath = dirname(__DIR__) . '/' . $imgData['image_path'];
        if (file_exists($filePath)) unlink($filePath);
    }

    $stmt = $conn->prepare("DELETE FROM jerseys WHERE jersey_id = ?");
    $stmt->bind_param("i", $jerseyId);
    $stmt->execute();

    echo "<script>alert('Jersey deleted successfully!'); window.location.href = 'jerseys-info.php';</script>";
    exit;
}

// FETCH
$jerseys = $conn->query("SELECT * FROM jerseys ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cavs | Jerseys Info</title>
    <link rel="shortcut icon" href="img/shortcut-icon.png?v=<?= time(); ?>" type="image/x-icon">
    <?php include 'shared/css.php'; ?>
</head>

<body>
    <?php include 'shared/sidebar.php'; ?>

    <div class="content p-5">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <h1 class="m-0">System Jerseys Info</h1>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addJerseyModal">
                <i class="bi bi-plus-square"></i> Add Jersey
            </button>
        </div>

        <hr>

        <div class="table-responsive">
            <table id="jerseys-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Preview</th>
                        <th>Title</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $jerseys->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <a href="/cavs/<?= htmlspecialchars($row['image_path']) ?>" target="_blank">
                                    <img src="/cavs/<?= htmlspecialchars($row['image_path']) ?>" style="height: 80px; border: 1px solid #ccc;">
                                </a>
                            </td>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= date("Y-m-d H:i", strtotime($row['created_at'])) ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateJerseyModal<?= $row['jersey_id'] ?>">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteJerseyModal<?= $row['jersey_id'] ?>">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </td>
                        </tr>

                        <!-- UPDATE MODAL -->
                        <div class="modal fade" id="updateJerseyModal<?= $row['jersey_id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <form method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="update_jersey" value="1">
                                        <input type="hidden" name="jersey_id" value="<?= $row['jersey_id'] ?>">
                                        <div class="modal-header bg-warning text-white">
                                            <h5 class="modal-title text-dark">Edit Jersey</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Title</label>
                                                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($row['title']) ?>" required>
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

                        <!-- DELETE MODAL -->
                        <div class="modal fade" id="deleteJerseyModal<?= $row['jersey_id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST">
                                        <input type="hidden" name="delete_jersey" value="1">
                                        <input type="hidden" name="jersey_id" value="<?= $row['jersey_id'] ?>">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Delete Jersey</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to delete <strong><?= htmlspecialchars($row['title']) ?></strong>?
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                                            <button class="btn btn-danger" type="submit">Delete</button>
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

    <!-- ADD MODAL -->
    <div class="modal fade" id="addJerseyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="add_jersey" value="1">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title text-white">Add Jersey</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" placeholder="Enter jersey title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">Save Jersey</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'shared/js.php'; ?>
</body>

</html>