<?php require_once './class/DataFetcher.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="./js/function.js"></script>

</head>
<body>
    <header>
        <ul>
    <h1>>My Fantasy Hockey</h1>
            <li><a href="index.php">Log in</a></li>
            <li><a href="about.php">My Fantasy Profile</a></li>
        </ul>
        <nav>
            <div class="menu-icon" onclick="toggleMenu()">&#9776;</div>
            <ul id="menu">
                <img src="./ressources/nhl.png" alt="NHL logo">
                <li><a href="home.php">Home</a></li>
                <li><a href="Teams.php">My Teams</a></li>
                <li><a href="Stats.php">Statistics</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>

        <div class="container">
    <div id="scores">
        <?php 
        $data = new DataFetcher();
        
        $dailyScores = $data->getDailyScores();
     
        
        if (isset($dailyScores['games']) && count($dailyScores['games']) > 0): ?>
            <?php foreach ($dailyScores['games'] as $game): ?>
                <div class="score-card">
                    <div class="team">
                        <img src="<?php echo $game['awayTeam']['logo']; ?>" alt="<?php echo $game['awayTeam']['name']['default']; ?> logo">
                        <span class="team-name"><?php echo $game['awayTeam']['name']['default']; ?></span>
                    </div>
                    <div class="game-info">
                        <p><?php echo $game['gameDate']; ?></p>
                        <p><?php echo $game['venue']['default']; ?></p>
                    </div>
                    <div class="team">
                        <img src="<?php echo $game['homeTeam']['logo']; ?>" alt="<?php echo $game['homeTeam']['name']['default']; ?> logo">
                        <span class="team-name"><?php echo $game['homeTeam']['name']['default']; ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No games scheduled for today.</p>
        <?php endif; ?>
    </div>
</div>
    </header>
