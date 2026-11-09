<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Measurement;
use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class WeatherApiController extends AbstractController
{
    #[Route('/api/v1/weather', name: 'api_weather', methods: ['GET'])]
    public function index(
        #[MapQueryParameter] string $country,
        #[MapQueryParameter] string $city,
        WeatherUtil $weatherUtil,
        #[MapQueryParameter] string $format = 'json',
        #[MapQueryParameter('twig')] bool $twig = false,
    ): Response {
        $measurements = $weatherUtil->getWeatherForCountryAndCity($country, $city);

        if ($twig) {
            if ($format === 'csv') {
                return $this->render('weather_api/index.csv.twig', [
                    'city' => $city,
                    'country' => $country,
                    'measurements' => $measurements,
                ], new Response(headers: ['Content-Type' => 'text/csv']));
            }

            if ($format === 'json') {
                return $this->render('weather_api/index.json.twig', [
                    'city' => $city,
                    'country' => $country,
                    'measurements' => $measurements,
                ], new Response(headers: ['Content-Type' => 'application/json']));
            }

            return new Response('Format must be "json" or "csv"', 400);
        }

        if ($format === 'csv') {
            $csvData = [];
            $csvData[] = 'city,country,date,celsius,fahrenheit,wind_speed,humidity,pressure';

            foreach ($measurements as $measurement) {
                $csvData[] = implode(',', [
                    $city,
                    $country,
                    $measurement->getDate()->format('Y-m-d'),
                    $measurement->getCelsius(),
                    $measurement->getFahrenheit(),
                    $measurement->getWindSpeed(),
                    $measurement->getHumidity(),
                    $measurement->getPressure(),
                ]);
            }

            return new Response(implode("\n", $csvData), 200, [
                'Content-Type' => 'text/csv',
            ]);
        }

        if ($format === 'json') {
            return $this->json([
                'city' => $city,
                'country' => $country,
                'measurements' => array_map(fn(Measurement $m) => [
                    'date' => $m->getDate()->format('Y-m-d'),
                    'celsius' => $m->getCelsius(),
                    'fahrenheit' => $m->getFahrenheit(),
                    'wind_speed' => $m->getWindSpeed(),
                    'humidity' => $m->getHumidity(),
                    'pressure' => $m->getPressure(),
                ], $measurements),
            ]);
        }

        return new Response('Format must be "json" or "csv"', 400);
    }
}
