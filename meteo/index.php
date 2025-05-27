<?php
$showCard = false;
$dailyData = [];
$country = '';
$city = '';


if (isset($_POST["getWeather"])) {
    $city = $_POST["City"];
    $apiKey = "38c672005bbaaefae6ee11ce6d9548f3";
    $url = "https://api.openweathermap.org/data/2.5/forecast?q=$city&units=metric&appid=$apiKey";

    $response = file_get_contents($url);
    if ($response !== false) {
        $data = json_decode($response, true);

        if (isset($data["list"])) {
            $country = $data["city"]["country"];
            $forecasts = $data["list"];
            $dailyData = [];

            foreach ($forecasts as $forecast) {
                $date = date("Y-m-d", strtotime($forecast["dt_txt"]));
                if (!isset($dailyData[$date]) && strpos($forecast["dt_txt"], "12:00:00") !== false) {
                    $dailyData[$date] = $forecast;
                }
            }


            $showCard = true;
        } else {
            $error = "❌ Could not find weather data for this city.";
        }
    } else {
        $error = "❌ Failed to connect to the API.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Weather Forecast</title>
    <style>
body {
    font-family: 'Segoe UI', sans-serif;
background-image: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1950&q=80');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    min-height: 100vh;
    padding-top: 40px;
    color: #000;
    backdrop-filter: brightness(0.9);
}


        input[type="text"], button {
            padding: 12px 16px;
            border-radius: 10px;
            border: none;
            font-size: 16px;
        }

        input[type="text"] {
            width: 220px;
        }

        button {
            background: linear-gradient(to right, #4facfe, #00f2fe);
            color: white;
            margin-left: 10px;
            cursor: pointer;
        }

        .cards-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 800px;
        }

        .main-card {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 15px;
            width: 100%;
            text-align: center;
            margin-bottom: 20px;
        }

        .mini-cards {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .mini-card {
            background: rgba(255, 255, 255, 0.5);
            padding: 10px;
            border-radius: 12px;
            text-align: center;
            width: 120px;
        }

        .error {
            background: #ffcccc;
            color: #b30000;
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
        }

        img {
            width: 70px;
        }

        .main-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(255, 255, 255, 0.5);
    padding: 20px;
    border-radius: 15px;
    width: 100%;
    text-align: left;
    margin-bottom: 20px;
    flex-wrap: wrap; /* Make it responsive */
}

.main-left, .main-right {
    flex: 1;
    min-width: 200px;
}

.main-left {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.main-right p {
    margin: 8px 0;
    font-size: 16px;
    text-align: center;
}

    </style>
</head>
<body>

<form method="post">
    <input type="text" name="City" placeholder="Enter a city (e.g., London)" required>
    <button type="submit" name="getWeather">Get Weather</button>
</form>

<?php if (isset($error)) : ?>
    <div class="error"><?= $error ?></div>
<?php endif; ?>

<?php if ($showCard): ?>
    <h2> <?= htmlspecialchars($city) ?> - <?= $country ?></h2>
    <div class="cards-container">

        <?php
        $days = array_values($dailyData);
        $first = $days[0];
        $others = array_slice($days, 1);

        $mainDate = date("l", strtotime($first["dt_txt"]));
        $mainTemp = round($first["main"]["temp"]);
        $mainDesc = ucfirst($first["weather"][0]["description"]);
        $mainIcon = $first["weather"][0]["icon"];
        $mainIconURL = "https://openweathermap.org/img/wn/{$mainIcon}@2x.png";
        ?>

<div class="main-card">
    <div class="main-left">
        <h3><?= $mainDate ?> - Today</h3>
        <img src="<?= $mainIconURL ?>" alt="">
        <p><?= $mainDesc ?></p>
        <h2><?= $mainTemp ?>°C</h2>
    </div>
    <div class="main-right">
        <p><strong>Feels like:</strong> <?= round($first["main"]["feels_like"]) ?>°C</p>
        <p><strong>Humidity:</strong> <?= $first["main"]["humidity"] ?>%</p>
        <p><strong>Wind:</strong> <?= round($first["wind"]["speed"]) ?> m/s</p>
        <p><strong>Pressure:</strong> <?= $first["main"]["pressure"] ?> hPa</p>
    </div>
</div>



        <div class="mini-cards">
            <?php foreach ($others as $day):
                $d = date("D", strtotime($day["dt_txt"]));
                $temp = round($day["main"]["temp"]);
                $desc = ucfirst($day["weather"][0]["description"]);
                $icon = $day["weather"][0]["icon"];
                $iconURL = "https://openweathermap.org/img/wn/{$icon}@2x.png";
            ?>
                <div class="mini-card">
                    <h4><?= $d ?></h4>
                    <img src="<?= $iconURL ?>" alt="">
                    <p><?= $desc ?></p>
                    <strong><?= $temp ?>°C</strong>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
<?php endif; ?>



</body>
</html>
