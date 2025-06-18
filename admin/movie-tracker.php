<?php
include 'shared/config.php';
require 'shared/auth_check.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $genre = $_POST['genre'];
    $actors = $_POST['actors'];
    $year = $_POST['year'];
    $synopsis = $_POST['synopsis'];
    $link = $_POST['link'];

    if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
        $originalName = basename($_FILES['poster']['name']);
        $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
        $posterName = time() . '_' . $originalName;


        $targetPath = 'upload/' . $posterName;

        $counter = 1;
        while (file_exists($targetPath)) {
            $posterName = time() . '_' . $counter . '_' . $originalName;
            $targetPath = 'upload/' . $posterName;
            $counter++;
        }

        move_uploaded_file($_FILES['poster']['tmp_name'], $targetPath);
    } else {
        $posterName = null;
    }

    $stmt = $conn->prepare("INSERT INTO Movies (Title, Genre, Actors, Year, Synopsis, Link, Poster) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $title, $genre, $actors, $year, $synopsis, $link, $posterName);
    $stmt->execute();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cavs | Admin Panel</title>
    <link rel="shortcut icon" href="img/shortcut-icon.png?v=<?php echo time(); ?>" type="image/x-icon">
    <?php include 'shared/css.php'; ?>
</head>

<body>
    <?php include 'shared/sidebar.php'; ?>
    <div class="preloader"></div>
    <div class="content p-5">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <h1 class="text-start m-0">Movie Tracker</h1>
            <button class="btn btn-primary btn-md" data-bs-toggle="modal" data-bs-target="#addMovieModal">
                <i class="bi bi-plus-circle me-1"></i> Add Movie
            </button>
        </div>

        <?php if (isset($message)) { ?>
            <div class="alert alert-info" role="alert">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="dashboard.php" class="text-decoration-none">Home</a>
                </li>
                <li class="breadcrumb-item active text-tomato" aria-current="page">Movies</li>
            </ol>
        </nav>

        <hr>

        <div class="table-responsive">
            <table id="movie-table" class="display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Genre</th>
                        <th>Actors</th>
                        <th>Year</th>
                        <th>Synopsis</th>
                        <th>Link</th>
                        <th>Poster</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM Movies");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($movie = $result->fetch_assoc()) {
                        echo "<tr>
                        <td>{$movie['Title']}</td>
                        <td>{$movie['Genre']}</td>
                        <td>{$movie['Actors']}</td>
                        <td>{$movie['Year']}</td>
                        <td>{$movie['Synopsis']}</td>
                        <td><a href='{$movie['Link']}' target='_blank'>Watch</a></td>
                        <td>
                        <a href='upload/{$movie['Poster']}' target='_blank' class='btn btn-sm btn-info'>
                                View Image
                            </a>

                        </td>
                        <td>
                            <button 
                            class='btn btn-sm btn-warning editBtn' 
                            data-id='{$movie['Movie_ID']}' 
                            data-title='{$movie['Title']}' 
                            data-genre='{$movie['Genre']}' 
                            data-actors='{$movie['Actors']}' 
                            data-year='{$movie['Year']}' 
                            data-synopsis='{$movie['Synopsis']}' 
                            data-link='{$movie['Link']}'>
                            <i class='bi bi-pencil-square'></i>
                        </button>
                    <button class='btn btn-sm btn-danger' onclick='confirmDelete({$movie['Movie_ID']})'>
                            <i class='bi bi-trash'></i>
                        </button>


                        </td>
                    </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>


        <div class="modal fade" id="addMovieModal" tabindex="-1" aria-labelledby="addMovieModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="addMovieModalLabel">Add New Movie</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="movieTitle" class="form-label">Movie Title</label>
                                <input type="text" class="form-control" id="movieTitle" name="title" placeholder="Enter movie title" required>
                            </div>
                            <div class="mb-3">
                                <label for="movieGenre" class="form-label">Genre</label>
                                <input type="text" class="form-control" id="movieGenre" name="genre" placeholder="e.g., Action, Comedy" required>
                            </div>
                            <div class="mb-3">
                                <label for="movieActors" class="form-label">Cast</label>
                                <input type="text" class="form-control" id="movieActors" name="actors" placeholder="Main cast names" required>
                            </div>
                            <div class="mb-3">
                                <label for="movieYear" class="form-label">Year</label>
                                <input type="number" class="form-control" id="movieYear" name="year" placeholder="e.g., 2024" required>
                            </div>
                            <div class="mb-3">
                                <label for="movieSynopsis" class="form-label">Synopsis</label>
                                <textarea class="form-control" id="movieSynopsis" name="synopsis" rows="3" placeholder="Brief summary of the movie" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="movieLink" class="form-label">Movie Link</label>
                                <input type="url" class="form-control" id="movieLink" name="link" placeholder="YouTube or streaming link" required>
                            </div>
                            <div class="mb-3">
                                <label for="moviePoster" class="form-label">Poster</label>
                                <input type="file" class="form-control" id="moviePoster" name="poster" accept="image/*" required>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editMovieModal" tabindex="-1" aria-labelledby="editMovieModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <form action="update_movie.php" method="POST">
                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title text-dark" id="editMovieModalLabel">Edit Movie</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="movie_id" id="editMovieId">
                            <div class="mb-3">
                                <label for="editTitle" class="form-label">Title</label>
                                <input type="text" class="form-control" name="title" id="editTitle" required>
                            </div>
                            <div class="mb-3">
                                <label for="editGenre" class="form-label">Genre</label>
                                <input type="text" class="form-control" name="genre" id="editGenre" required>
                            </div>
                            <div class="mb-3">
                                <label for="editActors" class="form-label">Actors</label>
                                <input type="text" class="form-control" name="actors" id="editActors" required>
                            </div>
                            <div class="mb-3">
                                <label for="editYear" class="form-label">Year</label>
                                <input type="number" class="form-control" name="year" id="editYear" required>
                            </div>
                            <div class="mb-3">
                                <label for="editSynopsis" class="form-label">Synopsis</label>
                                <textarea class="form-control" name="synopsis" id="editSynopsis" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="editLink" class="form-label">Movie Link</label>
                                <input type="url" class="form-control" name="link" id="editLink" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <script>
            function confirmDelete(movieId) {
                if (confirm("Are you sure you want to delete this movie?")) {
                    window.location.href = "delete-movie.php?id=" + movieId;
                }
            }

            function confirmLogout() {
                if (confirm("Are you sure you want to log out?")) {
                    window.location.href = 'logout.php';
                }
            }
        </script>
        <?php include 'shared/js.php'; ?>
</body>

</html>