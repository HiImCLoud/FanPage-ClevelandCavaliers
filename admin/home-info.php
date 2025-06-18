<?php
include 'shared/config.php';
require 'shared/auth_check.php';

// Fetch home info
$home = $conn->query("SELECT * FROM home_info LIMIT 1")->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $button_text = $_POST['button_text'];
    $button_link = $_POST['button_link'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $originalName = basename($_FILES['image']['name']);

        $uploadDir = dirname(__DIR__) . '/assets/';
        $relativeDir = 'assets/';

        // Break into name and extension
        $pathInfo = pathinfo($originalName);
        $filename = $pathInfo['filename'];
        $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';

        $finalName = $filename . $extension;
        $counter = 1;

        // Rename if exists
        while (file_exists($uploadDir . $finalName)) {
            $finalName = $filename . "($counter)" . $extension;
            $counter++;
        }

        $targetPath = $uploadDir . $finalName;
        $relativePath = $relativeDir . $finalName;

        // Save the file
        move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);

        // Update with image
        $stmt = $conn->prepare("UPDATE home_info SET title=?, description=?, button_text=?, button_link=?, image=? WHERE id=?");
        $stmt->bind_param("sssssi", $title, $description, $button_text, $button_link, $relativePath, $home['id']);
    } else {
        // Update without image
        $stmt = $conn->prepare("UPDATE home_info SET title=?, description=?, button_text=?, button_link=? WHERE id=?");
        $stmt->bind_param("ssssi", $title, $description, $button_text, $button_link, $home['id']);
    }

    $stmt->execute();
    echo "<script>alert('Home info updated successfully.'); window.location.href='home-info.php';</script>";
    exit();
}

$imagePath = !empty($home['image']) ? $home['image'] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cavs | Home Info</title>
    <link rel="shortcut icon" href="img/shortcut-icon.png?v=<?= time(); ?>" type="image/x-icon">
    <?php include 'shared/css.php'; ?>
</head>

<body>
    <?php include 'shared/sidebar.php'; ?>
    <div class="preloader"></div>

    <div class="content p-5">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <h1 class="m-0">System Home Info</h1>
            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editHomeInfoModal">
                <i class="bi bi-pencil-square"></i> Edit Info
            </button>
        </div>

        <hr>

        <div class="table-responsive">
            <table id="home-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Title</th>
                        <td><?= htmlspecialchars($home['title']) ?></td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td><?= nl2br(htmlspecialchars($home['description'])) ?></td>
                    </tr>
                    <tr>
                        <th>Button Text</th>
                        <td><?= htmlspecialchars($home['button_text']) ?></td>
                    </tr>
                    <tr>
                        <th>Button Link</th>
                        <td><a href="<?= htmlspecialchars($home['button_link']) ?>" target="_blank"><?= htmlspecialchars($home['button_link']) ?></a></td>
                    </tr>
                    <tr>
                        <th>Image</th>
                        <td>
                            <?php if (!empty($imagePath)): ?>
                                <a href="/cavs/<?= htmlspecialchars($imagePath) ?>" target="_blank" class="btn btn-sm btn-info mb-2">View Full Image</a><br>
                                <img src="/cavs/<?= htmlspecialchars($imagePath) ?>" alt="Preview" style="max-height: 100px; border: 1px solid #ccc;">
                            <?php else: ?>
                                <span class="text-muted">No image</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editHomeInfoModal" tabindex="-1" aria-labelledby="editHomeInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title text-dark">Edit Home Info</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($home['title']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="5" required><?= htmlspecialchars($home['description']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Button Text</label>
                            <input type="text" name="button_text" class="form-control" value="<?= htmlspecialchars($home['button_text']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Button Link</label>
                            <input type="url" name="button_link" class="form-control" value="<?= htmlspecialchars($home['button_link']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Change Image (optional)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <?php if (!empty($imagePath)): ?>
                                <small class="form-text text-muted">Current image: <?= htmlspecialchars($imagePath) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success" type="submit">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'shared/js.php'; ?>
</body>

</html>