<?php

namespace App\Command;

use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:city',
    description: 'Displays weather forecast for a specific city and country',
)]
class WeatherCityCommand extends Command
{
    public function __construct(
        private readonly WeatherUtil $weatherUtil,
    ) {
        parent::__construct();
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument('Country code')] string $countryCode,
        #[Argument('City name')] string $cityName,
    ): int {
        $measurements = $this->weatherUtil->getWeatherForCountryAndCity($countryCode, $cityName);

        if (empty($measurements)) {
            $io->error(sprintf('No weather measurements found for city "%s" in country "%s".', $cityName, $countryCode));

            return Command::FAILURE;
        }

        $io->writeln(sprintf('Location: %s - %s', $countryCode, $cityName));

        $io->table(
            ['Date', 'Temperature', 'Wind speed', 'Humidity', 'Pressure'],
            array_map(fn($m) => [
                $m->getDate()->format('Y-m-d'),
                $m->getTemperature(),
                $m->getWindSpeed(),
                $m->getHumidity(),
                $m->getPressure(),
            ], $measurements)
        );

        return Command::SUCCESS;
    }
}
