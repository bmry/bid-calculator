<?php

namespace Progi\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use Progi\Application\UseCase\CalculateBidUseCase;
use Progi\Domain\Exception\InvalidPriceException;
use Progi\Domain\Exception\PolicyNotFoundException;

class CalculateBidFeeController extends AbstractController
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    #[Route('/api/bid/calculate', name: 'api_bid_calculate', methods: ['POST'])]
    public function __invoke(Request $request, CalculateBidUseCase $useCase): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        $priceValue = (float)($data['price'] ?? 0);
        $vehicleType = (string)($data['type'] ?? 'common');

        try {
            $dto = $useCase->execute($priceValue, $vehicleType);
            $this->logger->debug("Calculated bid for type=$vehicleType, price=$priceValue");
            return $this->json($dto->toArray());
        } catch (InvalidPriceException | PolicyNotFoundException $e) {
            $this->logger->error($e->getMessage());
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
