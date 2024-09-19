-- Create the database
CREATE DATABASE IF NOT EXISTS FantasyData;

-- Use the database
USE FantasyData;

-- Create the users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create the teams table
CREATE TABLE IF NOT EXISTS teams (
    team_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    team_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Create the players table
CREATE TABLE IF NOT EXISTS players (
    player_id INT PRIMARY KEY,
    team_id INT,
    player_name VARCHAR(100) NOT NULL,
    headshot VARCHAR(255),
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    sweater_number INT,
    position_code VARCHAR(10),
    shoots_catches VARCHAR(1),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS player_stats (
    player_id INT,
    goals INT,
    assists INT,
    plus_minus INT,
    shots INT,
    points INT,
    PRIMARY KEY (player_id),
    FOREIGN KEY (player_id) REFERENCES players(player_id) ON DELETE CASCADE
);

CREATE TABLE teamColors (
    team_code VARCHAR(3) PRIMARY KEY,
    team_name VARCHAR(50) NOT NULL
);

CREATE TABLE teamColorDetails (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_code VARCHAR(3),
    color VARCHAR(7), -- Assuming colors are stored as hex codes
    FOREIGN KEY (team_code) REFERENCES teamColors(team_code)
);