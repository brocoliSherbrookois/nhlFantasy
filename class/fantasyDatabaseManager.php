<?php

class FantasyDatabaseManager {
    private $pdo;

    public function __construct($host, $dbname, $username, $password) {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $this->pdo = new PDO($dsn, $username, $password, $options);
    }

    // User operations
    public function createUser($username, $email, $password) {
        $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->execute(['username' => $username, 'email' => $email, 'password' => password_hash($password, PASSWORD_DEFAULT)]);
        return $this->pdo->lastInsertId();
    }

    public function getUserById($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch();
    }

    // Team operations
    public function createTeam($userId, $teamName) {
        $stmt = $this->pdo->prepare("INSERT INTO teams (user_id, team_name) VALUES (:user_id, :team_name)");
        $stmt->execute(['user_id' => $userId, 'team_name' => $teamName]);
        return $this->pdo->lastInsertId();
    }

    public function getTeamById($teamId) {
        $stmt = $this->pdo->prepare("SELECT * FROM teams WHERE team_id = :team_id");
        $stmt->execute(['team_id' => $teamId]);
        return $stmt->fetch();
    }

    // Player operations
    public function addPlayerToTeam($teamId, $playerData) {
        $stmt = $this->pdo->prepare("INSERT INTO players (
            player_id, team_id, player_name, headshot, first_name, last_name, sweater_number, position_code, 
            shoots_catches
        ) VALUES (
            :player_id, :team_id, :player_name, :headshot, :first_name, :last_name, :sweater_number, :position_code, 
            :shoots_catches
        )");
        $stmt->execute([
            'player_id' => $playerData['id'],
            'team_id' => $teamId,
            'player_name' => $playerData['firstName']['default'] . ' ' . $playerData['lastName']['default'],
            'headshot' => $playerData['headshot'],
            'first_name' => $playerData['firstName']['default'],
            'last_name' => $playerData['lastName']['default'],
            'sweater_number' => $playerData['sweaterNumber'],
            'position_code' => $playerData['positionCode'],
            'shoots_catches' => $playerData['shootsCatches']
        ]);
        $this->updatePlayerStats($playerData);
        return $this->pdo->lastInsertId();
    }

    public function updatePlayerStats($playerData) {
        $stmt = $this->pdo->prepare("REPLACE INTO player_stats (
            player_id, goals, assists, plus_minus, shots, points
        ) VALUES (
            :player_id, :goals, :assists, :plus_minus, :shots, :points
        )");
        $stmt->execute([
            'player_id' => $playerData['id'],
            'goals' => $playerData['stats']['goals'] ?? 0,
            'assists' => $playerData['stats']['assists'] ?? 0,
            'plus_minus' => $playerData['stats']['plusMinus'] ?? 0,
            'shots' => $playerData['stats']['shots'] ?? 0,
            'points' => $playerData['stats']['points'] ?? 0
        ]);
    }

    public function updateOrInsertPlayer($playerData) {
        $stmt = $this->pdo->prepare("REPLACE INTO players (
            player_id, team_id, player_name, headshot, first_name, last_name, sweater_number, position_code, 
            shoots_catches
        ) VALUES (
            :player_id, :team_id, :player_name, :headshot, :first_name, :last_name, :sweater_number, :position_code, 
            :shoots_catches
        )");
        $stmt->execute([
            'player_id' => $playerData['id'],
            'team_id' => $playerData['team_id'], // Assuming team_id is part of the player data
            'player_name' => $playerData['firstName']['default'] . ' ' . $playerData['lastName']['default'],
            'headshot' => $playerData['headshot'],
            'first_name' => $playerData['firstName']['default'],
            'last_name' => $playerData['lastName']['default'],
            'sweater_number' => $playerData['sweaterNumber'],
            'position_code' => $playerData['positionCode'],
            'shoots_catches' => $playerData['shootsCatches']
        ]);
        $this->updatePlayerStats($playerData);
    }

    public function getPlayerById($playerId) {
        $stmt = $this->pdo->prepare("SELECT * FROM players WHERE player_id = :player_id");
        $stmt->execute(['player_id' => $playerId]);
        $player = $stmt->fetch();

        if ($player) {
            $stmt = $this->pdo->prepare("SELECT * FROM player_stats WHERE player_id = :player_id");
            $stmt->execute(['player_id' => $playerId]);
            $player['stats'] = $stmt->fetch();
        }

        return $player;
    }

    // Team color operations
    public function addTeamColor($teamCode, $teamName) {
        $stmt = $this->pdo->prepare("INSERT INTO teamColors (team_code, team_name) VALUES (:team_code, :team_name)");
        $stmt->execute(['team_code' => $teamCode, 'team_name' => $teamName]);
    }

    public function addTeamColorDetail($teamCode, $color) {
        $stmt = $this->pdo->prepare("INSERT INTO teamColorDetails (team_code, color) VALUES (:team_code, :color)");
        $stmt->execute(['team_code' => $teamCode, 'color' => $color]);
    }

    public function getTeamColors($teamCode) {
        $stmt = $this->pdo->prepare("SELECT color FROM teamColorDetails WHERE team_code = :team_code");
        $stmt->execute(['team_code' => $teamCode]);
        return $stmt->fetchAll();
    }
}
?>


?>