<?php
namespace App\Controller;

use App\Service\Integration\FootballApiClient;
use App\Service\Integration\SncfClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class SearchController extends AbstractController
{
    #[Route('/api/search/teams', name:'app_search_teams', methods:['GET'])]
    public function teams(Request $request, FootballApiClient $football): JsonResponse
    {
        $q = trim((string) $request->query->get('q',''));
        $rows = array_map(static function(array $row): array {
            $team = $row['team'] ?? [];
            return [
                'id' => $team['id'] ?? null,
                'name' => $team['name'] ?? null,
                'country' => $team['country'] ?? null,
                'logo' => $team['logo'] ?? null,
            ];
        }, $football->searchTeams($q));

        return $this->json(array_values(array_filter($rows, static fn(array $r): bool => $r['id'] !== null)));
    }

    #[Route('/api/search/stations', name:'app_search_stations', methods:['GET'])]
    public function stations(Request $request, SncfClient $sncf): JsonResponse
    {
        $q = trim((string) $request->query->get('q',''));
        $rows = array_map(static fn(array $place): array => [
            'id' => $place['id'] ?? null,
            'name' => $place['name'] ?? null,
            'type' => $place['embedded_type'] ?? null,
        ], $sncf->searchPlaces($q));

        return $this->json(array_values(array_filter($rows, static fn(array $r): bool => $r['id'] !== null)));
    }
}
