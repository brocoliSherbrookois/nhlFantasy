function toggleMenu() {
    var menu = document.getElementById('menu');
    menu.classList.toggle('active');
}

document.addEventListener('DOMContentLoaded', function() {
    const currentDate = '2024-09-21'; // Example date
    document.getElementById('currentDate').textContent = currentDate;

    fetch(`fetchDailyScores.php?date=${currentDate}`)
        .then(response => response.json())
        .then(data => {
            const scoresDiv = document.getElementById('scores');
            data.games.forEach(game => {
                const scoreCard = document.createElement('div');
                scoreCard.className = 'score-card';

                const awayTeam = document.createElement('div');
                awayTeam.className = 'team';
                awayTeam.innerHTML = `<img src="${game.awayTeam.logo}" alt="${game.awayTeam.name.default} logo">
                                      <span class="team-name">${game.awayTeam.name.default}</span>`;

                const homeTeam = document.createElement('div');
                homeTeam.className = 'team';
                homeTeam.innerHTML = `<img src="${game.homeTeam.logo}" alt="${game.homeTeam.name.default} logo">
                                      <span class="team-name">${game.homeTeam.name.default}</span>`;

                const gameInfo = document.createElement('div');
                gameInfo.className = 'game-info';
                gameInfo.innerHTML = `<p>${game.gameDate}</p>
                                      <p>${game.venue.default}</p>`;

                scoreCard.appendChild(awayTeam);
                scoreCard.appendChild(gameInfo);
                scoreCard.appendChild(homeTeam);

                scoresDiv.appendChild(scoreCard);
            });
        })
        .catch(error => console.error('Error fetching daily scores:', error));
});