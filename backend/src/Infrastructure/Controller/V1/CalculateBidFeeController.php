<?php
declare(strict_types=1);

namespace Progi\Infrastructure\Controller\V1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use Progi\Application\UseCase\CalculateBidUseCase;
use Progi\Domain\Exception\InvalidPriceException;
use Progi\Domain\Exception\PolicyNotFoundException;

#[Route('/bid/calculate', name: 'api_bid_calculate', methods: ['POST'])]
class CalculateBidFeeController extends AbstractController
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    /**
     * Handles a POST request to calculate bid fees.
     *
     * @param Request $request
     * @param CalculateBidUseCase $useCase
     * @return JsonResponse
     */
    public function __invoke(Request $request, CalculateBidUseCase $useCase): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $priceValue = (float)($data['price'] ?? 0);
        $vehicleType = (string)($data['type'] ?? 'common');

        try {
            $dto = $useCase->execute($priceValue, $vehicleType);
            $this->logger->debug("Calculated fees for vehicleType=$vehicleType, price=$priceValue");
            return $this->json($dto->toArray());
        } catch (InvalidPriceException | PolicyNotFoundException $e) {
            $this->logger->error($e->getMessage());
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
