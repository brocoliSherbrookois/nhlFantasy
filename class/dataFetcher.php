<?php
require_once 'fantasyDatabaseManager.php';
class DataFetcher {
    private $apiUrl = 'https://api-web.nhle.com/v1/';
    private $ch;
    private $cacheDir = __DIR__ . '/cache/';
    private $cacheTTL = 3600; // Cache time-to-live in seconds (1 hour)
   

    private $teamCodes = [
        'ANA' => 'Anaheim Ducks',
        'BOS' => 'Boston Bruins',
        'BUF' => 'Buffalo Sabres',
        'CGY' => 'Calgary Flames',
        'CAR' => 'Carolina Hurricanes',
        'CHI' => 'Chicago Blackhawks',
        'COL' => 'Colorado Avalanche',
        'CBJ' => 'Columbus Blue Jackets',
        'DAL' => 'Dallas Stars',
        'DET' => 'Detroit Red Wings',
        'EDM' => 'Edmonton Oilers',
        'FLA' => 'Florida Panthers',
        'LAK' => 'Los Angeles Kings',
        'MIN' => 'Minnesota Wild',
        'MTL' => 'Montreal Canadiens',
        'NSH' => 'Nashville Predators',
        'NJD' => 'New Jersey Devils',
        'NYI' => 'New York Islanders',
        'NYR' => 'New York Rangers',
        'OTT' => 'Ottawa Senators',
        'PHI' => 'Philadelphia Flyers',
        'PIT' => 'Pittsburgh Penguins',
        'SJS' => 'San Jose Sharks',
        'SEA' => 'Seattle Kraken',
        'STL' => 'St. Louis Blues',
        'TBL' => 'Tampa Bay Lightning',
        'TOR' => 'Toronto Maple Leafs',
        'UTA' => 'Utah Hockey Club',
        'VAN' => 'Vancouver Canucks',
        'VGK' => 'Vegas Golden Knights',
        'WSH' => 'Washington Capitals',
        'WPG' => 'Winnipeg Jets'
    ];

    public function __construct() {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);

        // Ensure cache directory exists
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    private function getCacheFilePath($key) {
        return $this->cacheDir . md5($key) . '.cache';
    }

    private function getFromCache($key) {
        $filePath = $this->getCacheFilePath($key);
        if (file_exists($filePath) && (time() - filemtime($filePath)) < $this->cacheTTL) {
            return file_get_contents($filePath);
        }
        return false;
    }

    private function saveToCache($key, $data) {
        $filePath = $this->getCacheFilePath($key);
        file_put_contents($filePath, $data);
    }

    public function getTeamSchedule($teamCode, $season) {
        $url = $this->apiUrl . "club-schedule-season/$teamCode/$season";
        curl_setopt($this->ch, CURLOPT_URL, $url);
        $response = curl_exec($this->ch);

        if (curl_errno($this->ch)) {
            return 'Curl error: ' . curl_error($this->ch);
        } else {
            // Debugging: Print the raw JSON response
            echo '<pre>';
            echo "Raw JSON response:\n";
            echo htmlspecialchars($response);
            echo '</pre>';

            $data = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return 'JSON decode error: ' . json_last_error_msg();
            }
            return $data;
        }
    }

    public function getAllGameScheduleToday() {
        $url = $this->apiUrl . "schedule?date=" . date('Y-m-d');
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($this->ch);

        if (curl_errno($this->ch)) {
            return 'Curl error: ' . curl_error($this->ch);
        } else {
            $data = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return 'JSON decode error: ' . json_last_error_msg();
            }
            return $data;
        }
    }

     public function getTeamRoster($teamCode) {
        $cacheKey = "roster_$teamCode";
        $cachedData = $this->getFromCache($cacheKey);

        if ($cachedData !== false) {
            return json_decode($cachedData, true);
        }

        $url = $this->apiUrl . "roster/$teamCode/current";
        curl_setopt($this->ch, CURLOPT_URL, $url);
        $response = curl_exec($this->ch);

        if (curl_errno($this->ch)) {
            return 'Curl error: ' . curl_error($this->ch);
        } else {
            // Debugging: Print the raw JSON response
            echo '<pre>';
            echo "Raw JSON response:\n";
            echo htmlspecialchars($response);
            echo '</pre>';

            $data = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return 'JSON decode error: ' . json_last_error_msg();
            }
            $this->saveToCache($cacheKey, $response);
            return $data;
        }
    }

    public function getAllTeamsRoster() {
        $allRosters = [];
        foreach ($this->teamCodes as $teamCode => $teamName) {
            $data = $this->getTeamRoster($teamCode);
            if (is_array($data)) {
                $allRosters[$teamCode] = $data;
            } else {
                $allRosters[$teamCode] = "Error fetching roster: $data";
            }
        }
        return $allRosters;
    }

    public function getPlayerStats($idPlayer, $report = 'summary', $lang = 'en', $params = []) {
        $url = $this->apiUrl . "$lang/skater/$report";
        $queryParams = array_merge([
            'cayenneExp' => "playerId=$idPlayer"
        ], $params);

        $queryString = http_build_query($queryParams);
        $url .= '?' . $queryString;

        curl_setopt($this->ch, CURLOPT_URL, $url);
        $response = curl_exec($this->ch);

        if (curl_errno($this->ch)) {
            return 'Curl error: ' . curl_error($this->ch);
        } else {
            $data = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return 'JSON decode error: ' . json_last_error_msg();
            }
            return $data;
        }
    }

public function getDailyScores() {
    $url = "https://api-web.nhle.com/v1/score/now";
    curl_setopt($this->ch, CURLOPT_URL, $url);
    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($this->ch);

    if (curl_errno($this->ch)) {
        return 'Curl error: ' . curl_error($this->ch);
    } else {
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return 'JSON decode error: ' . json_last_error_msg();
        }
        return $data;
    }
}


    public function __destruct() {
        curl_close($this->ch);
    }
}
?>