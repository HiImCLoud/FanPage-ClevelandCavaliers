<?php
include 'shared/config.php';
$home = $conn->query("SELECT * FROM home_info LIMIT 1")->fetch_assoc();
$jerseys = $conn->query("SELECT * FROM jerseys");
$players = $conn->query("SELECT * FROM players");
$arenas = $conn->query("SELECT * FROM arenas ORDER BY arena_id ASC");
$sources = $conn->query("SELECT * FROM sources");

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Cleveland Cavaliers</title>
    <link rel="icon" href="assets/brand.png" type="image/png">
    <script src="featherextension.js"></script>
</head>

<body>
    <?php include 'shared/navbar.php'; ?>
    <section id="home">
        <div class="home container">
            <h1>Cleveland Cavaliers</h1>
            <p>
                The Cleveland Cavaliers, often referred to as the Cavs, are an American professional basketball team
                based in Cleveland. The Cavaliers compete in the National Basketball Association as a member of the
                Central Division of the Eastern Conference.
            </p>
            <div class="view-more-btn">
                <a href="https://www.nba.com/cavaliers/" target="_blank">
                    <button>
                        <span>View More</span>
                    </button>
                </a>
            </div>
        </div>
    </section>

    <section id="jerseys">
        <div class="jerseys container">
            <div class="jerseys-top">
                <h1 class="section-title">Jers<span>e</span>ys</h1>
            </div>
            <div class="jerseys-bottom">
                <?php while ($j = $jerseys->fetch_assoc()): ?>
                    <div class="jerseys-item">
                        <div class="icon"><img src="<?= $j['image_path'] ?>" alt="<?= $j['title'] ?>"></div>
                        <div class="details">
                            <h2><?= htmlspecialchars($j['title']) ?></h2>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="see-more-btn">
                <a href="https://www.cavshistory.com/jerseys/" target="_blank">
                    <button><span>See More</span><i data-feather="arrow-right-circle"></i></button>
                </a>
            </div>
        </div>
    </section>
    <hr style="margin-right: 10%; margin-left: 10%; border: 1px solid #800000;">
    <section id="player">
        <div class="player container">
            <div class="player-header">
                <h1 class="section-title">Team<span>Players</span></h1>
            </div>
            <div class="all-player">
                <?php while ($p = $players->fetch_assoc()): ?>
                    <div class="player-item">
                        <div class="player-info">
                            <h1><?= htmlspecialchars($p['name']) ?></h1>
                            <h2><?= htmlspecialchars($p['position']) ?></h2>
                            <p><?= nl2br(htmlspecialchars($p['description'])) ?></p>
                        </div>
                        <div class="player-img">
                            <img src="<?= $p['image_path'] ?>" alt="player-<?= $p['player_id'] ?>">
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="see-more-btn">
                <a href="https://www.nba.com/cavaliers/roster" target="_blank">
                    <button><span>See More</span><i data-feather="arrow-right-circle"></i></button>
                </a>
            </div>
        </div>
    </section>
    <hr style="margin-right: 10%; margin-left: 10%; border: 1px solid #800000;">
    <section id="arenas">
        <div class="arenas container">
            <div class="arena-top">
                <h1 class="section-title">Home<span>Arenas</span></h1>
            </div>
            <div class="arenas-bottom">
                <?php while ($row = $arenas->fetch_assoc()): ?>
                    <div class="arenas-item">
                        <div class="icon">
                            <img src="<?= $row['image_path'] ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                        </div>
                        <div class="details">
                            <h2><?= htmlspecialchars($row['name']) ?></h2>
                            <p><?= htmlspecialchars($row['year_range']) ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    <hr style="margin-right: 10%; margin-left: 10%; border: 1px solid #800000;">
    <section id="source">
        <div class="source container">
            <div>
                <h1 class="section-title">Source <span>Info</span></h1><br>
            </div>
            <div class="source-items">
                <?php while ($s = $sources->fetch_assoc()): ?>
                    <a href="<?= $s['url'] ?>" target="_blank" class="source-item">
                        <div class="icon"><img src="<?= $s['image_path'] ?>" alt="<?= htmlspecialchars($s['name']) ?> Icon"></div>
                        <div class="source-info">
                            <h1><?= strtoupper($s['name']) ?></h1>
                            <p>Visit Page</p>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    <?php include 'shared/footer.php'; ?>
    <script>
        feather.replace();
    </script>
    <script src="./script.js"></script>
</body>

</html>